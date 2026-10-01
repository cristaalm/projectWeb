<?php

namespace App\Services;

use App\Enums\ContainerStatus;
use App\Enums\ScanStatus;
use App\Exceptions\ScanException;
use App\Models\MaterialTypes;
use App\Models\PointEarning;
use App\Models\Scan;
use App\Repositories\ContainerRepository;
use App\Repositories\MaterialTypeRepository;
use App\Repositories\PointRepository;
use App\Repositories\ScanRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ScanService
{
    public function __construct(
        private readonly ScanRepository $scans,
        private readonly ContainerRepository $containers,
        private readonly UserRepository $users,
        private readonly MaterialTypeRepository $materials,
        private readonly PointRepository $points,
        private readonly BadgeService $badges,
        private readonly StreakService $streaks,
    ) {}

    /**
     * Registra un escaneo reportado por un contenedor, que ya clasificó el
     * material con su propio modelo. Un material que no otorga puntos queda
     * guardado como FAILED (rastro para auditoría), sin acreditar nada.
     *
     * @return array{scan: Scan, total_points: int, points_month: int, streak: array, duplicate: bool}
     */
    public function register(array $data, ?UploadedFile $image): array
    {
        // Un reintento del contenedor con el mismo event_id nunca vuelve a acreditar.
        if ($existing = $this->scans->findByEventId($data['event_id'])) {
            return $this->result($existing, true);
        }

        $container = $this->containers->findBySerialNumber($data['container_serial_number']);

        if (! $container) {
            throw new ScanException('Contenedor no encontrado.', 404);
        }

        if ($container->status !== ContainerStatus::ACTIVE) {
            throw new ScanException('El contenedor no está activo.', 422);
        }

        $user = $this->users->findByCodeIdentity($data['code_identity'], withTrashed: true);

        if (! $user) {
            throw new ScanException('Código de identidad no válido.', 404);
        }

        if ($user->trashed()) {
            throw new ScanException('La cuenta del usuario está desactivada.', 422);
        }

        $material = $this->materials->findBySlug($data['material']);
        $rejection = $this->rejectionReason($material);

        try {
            $scan = DB::transaction(function () use ($data, $container, $user, $material, $rejection) {
                // Serializa escaneos simultáneos del mismo usuario: el avance
                // de insignias y de la racha lee y luego escribe.
                $this->users->lockForUpdate($user->id);

                $scan = $this->scans->create([
                    'event_id' => $data['event_id'],
                    'user_id' => $user->id,
                    'container_id' => $container->id,
                    'material_type_id' => $material->id,
                    'points_awarded' => $rejection ? 0 : $material->points,
                    'scan_status' => $rejection ? ScanStatus::FAILED : ScanStatus::SUCCESS,
                    'description' => $rejection,
                    'scanned_at' => now(),
                ]);

                if (! $rejection) {
                    PointEarning::create([
                        'user_id' => $user->id,
                        'scan_id' => $scan->id,
                        'points' => $material->points,
                    ]);

                    $this->badges->registerRecycle($user);
                    $this->streaks->registerRecycle($user);
                }

                return $scan;
            });
        } catch (QueryException $e) {
            // Dos reintentos idénticos en carrera: el índice único de event_id
            // deja pasar solo uno, el otro responde como duplicado.
            if ($e->getCode() === '23505' && ($existing = $this->scans->findByEventId($data['event_id']))) {
                return $this->result($existing, true);
            }

            throw $e;
        }

        if ($image) {
            $this->attachImage($scan, $image);
        }

        return $this->result($scan, false);
    }

    private function rejectionReason(MaterialTypes $material): ?string
    {
        if (! $material->is_active) {
            return 'Material no aceptado actualmente.';
        }

        if ($material->points <= 0) {
            return 'Material no reciclable dentro del programa.';
        }

        return null;
    }

    /**
     * La imagen se guarda después de confirmar la transacción: es opcional,
     * así que un fallo de almacenamiento no debe tumbar un reciclaje ya
     * acreditado, y un duplicado nunca llega a escribir en disco.
     */
    private function attachImage(Scan $scan, UploadedFile $image): void
    {
        try {
            $path = $image->storeAs(
                "scans/container_{$scan->container_id}/user_{$scan->user_id}",
                $scan->event_id.'.'.$image->extension(),
                'public'
            );

            if ($path) {
                $scan->update(['image' => $path]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function result(Scan $scan, bool $duplicate): array
    {
        return [
            'scan' => $scan->loadMissing('materialType'),
            'total_points' => $this->users->pointsBalance($scan->user_id),
            'points_month' => $this->points->earnedInMonth($scan->user_id, now()),
            'streak' => $this->streaks->snapshot($scan->user_id),
            'duplicate' => $duplicate,
        ];
    }
}
