<?php

namespace App\Services;

use App\Enums\ScanStatus;
use App\Exceptions\ScanException;
use App\Models\Container;
use App\Models\MaterialTypes;
use App\Models\PointEarning;
use App\Models\Scan;
use App\Models\User;
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
        private readonly UserRepository $users,
        private readonly MaterialTypeRepository $materials,
        private readonly PointRepository $points,
        private readonly BadgeService $badges,
        private readonly StreakService $streaks,
    ) {}

    /**
     * Datos del usuario que se identificó en el contenedor (QR/NFC), para
     * que el equipo pueda saludarlo y mostrarle su avance antes de reciclar.
     */
    public function identify(string $codeIdentity): array
    {
        $user = $this->resolveUser($codeIdentity);
        [$badge, $nextBadge] = $this->badges->monthlySummary($user->id);

        return [
            'user' => [
                'id' => $user->id,
                'code_identity' => $user->code_identity,
                'name' => $user->name,
                'last_name' => $user->last_name,
            ],
            'total_points' => $this->users->pointsBalance($user->id),
            'points_month' => $this->points->earnedInMonth($user->id, now()),
            'valid_scans' => $this->scans->countValidByUser($user->id),
            'streak' => $this->streaks->snapshot($user->id),
            'badge' => $badge,
            'next_badge' => $nextBadge,
        ];
    }

    /**
     * Registra un escaneo reportado por un contenedor, que ya clasificó el
     * material con su propio modelo. El contenedor es siempre el dueño del
     * token con el que se autenticó la petición. Un material que no otorga
     * puntos queda guardado como FAILED (rastro para auditoría), sin
     * acreditar nada.
     *
     * @return array{scan: Scan, total_points: int, points_month: int, streak: array, duplicate: bool}
     */
    public function register(Container $container, array $data, ?UploadedFile $image): array
    {
        // Un reintento del contenedor con el mismo event_id nunca vuelve a acreditar.
        if ($existing = $this->scans->findByEventId($data['event_id'])) {
            return $this->duplicate($existing, $container);
        }

        $user = $this->resolveUser($data['code_identity']);

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
                return $this->duplicate($existing, $container);
            }

            throw $e;
        }

        if ($image) {
            $this->attachImage($scan, $image);
        }

        return $this->result($scan, false);
    }

    private function resolveUser(string $codeIdentity): User
    {
        $user = $this->users->findByCodeIdentity($codeIdentity, withTrashed: true);

        if (! $user) {
            throw new ScanException('Código de identidad no válido.', 404);
        }

        if ($user->trashed()) {
            throw new ScanException('La cuenta del usuario está desactivada.', 422);
        }

        return $user;
    }

    /**
     * Un event_id ya usado solo se responde como duplicado al mismo
     * contenedor que lo registró: así un contenedor no puede leer el
     * resultado (ni el saldo del usuario) de un escaneo ajeno.
     */
    private function duplicate(Scan $existing, Container $container): array
    {
        if ($existing->container_id !== $container->id) {
            throw new ScanException('Este identificador de evento ya fue usado por otro contenedor.', 409);
        }

        return $this->result($existing, true);
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
