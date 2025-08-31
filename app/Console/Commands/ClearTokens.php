<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class ClearTokens extends Command
{
    protected $signature = 'tokens:clear 
                            {--expired : Solo eliminar tokens expirados} 
                            {--user= : ID del usuario para eliminar solo sus tokens}';

    protected $description = 'Eliminar tokens de acceso de Sanctum (todos, expirados o por usuario)';

    public function handle()
    {
        $expiredOnly = $this->option('expired');
        $userId = $this->option('user');

        $query = PersonalAccessToken::query();

        if ($expiredOnly) {
            $query->where('expires_at', '<', Carbon::now());
        }

        if ($userId) {
            $query->where('tokenable_id', $userId)
                ->where('tokenable_type', config('auth.providers.users.model', \App\Models\User::class));
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No se encontraron tokens que coincidan con los filtros.');
            return;
        }

        if (!$this->confirm("¿Estás seguro de que deseas eliminar $count tokens?")) {
            $this->warn('Cancelado.');
            return;
        }

        $query->delete();

        $this->info("Se eliminaron $count token(s).");
    }
}
