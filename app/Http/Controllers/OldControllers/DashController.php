<?php

namespace App\Http\Controllers\OldControllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\History;
use App\Models\Scan;

class DashController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            $now = now();
            $totalUsersNow = User::where('status', 1)->count();
    
            // Fecha de corte: último día del mes pasado
            $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

            $totalUsersLastMonth = User::where('status', 1)
                ->where('created_at', '<=', $endOfLastMonth)
                ->count();
    
            // Calcular porcentaje de crecimiento
            if ($totalUsersLastMonth > 0) {
                $growthPercentage = (float) number_format((($totalUsersNow - $totalUsersLastMonth) / $totalUsersLastMonth) * 100, 2, '.', '');
            } else {
                $growthPercentage = $totalUsersNow > 0 ? 100.0 : 0.0;
            }

            $totalPointsNow = History::where('type_history', 2)->sum('points');

            $totalPointsLastMonth = History::where('type_history', 2)
                ->where('created_at', '<=', $endOfLastMonth)
                ->sum('points');

            // Calcular porcentaje de crecimiento
            if ($totalPointsLastMonth > 0) {
                $growthPercentagePoints = (float) number_format((($totalPointsNow - $totalPointsLastMonth) / $totalPointsLastMonth) * 100, 2, '.', '');
            } else {
                $growthPercentagePoints = $totalPointsNow > 0 ? 100.0 : 0.0;
            }

            $totalScansNow = Scan::count();
            $totalScansLastMonth = Scan::where('created_at', '<=', $endOfLastMonth)->count();

            // Calcular porcentaje de crecimiento
            if ($totalScansLastMonth > 0) {
                $growthPercentageScans = (float) number_format((($totalScansNow - $totalScansLastMonth) / $totalScansLastMonth) * 100, 2, '.', '');
            } else {
                $growthPercentageScans = $totalScansNow > 0 ? 100.0 : 0.0;
            }

            $totalRewardsNow = History::where('type_history', 1)->count();
            $totalRewardsLastMonth = History::where('type_history', 1)
                ->where('created_at', '<=', $endOfLastMonth)
                ->count();

            // Calcular porcentaje de crecimiento
            if ($totalRewardsLastMonth > 0) {
                $growthPercentageRewards = (float) number_format((($totalRewardsNow - $totalRewardsLastMonth) / $totalRewardsLastMonth) * 100, 2, '.', '');
            } else {
                $growthPercentageRewards = $totalRewardsNow > 0 ? 100.0 : 0.0;
            }    
    
            $data = [
                'users' => [
                    'total' => $totalUsersNow,
                    'lastMonthTotal' => $totalUsersLastMonth,
                    'growthPercentage' => $growthPercentage,
                ],
                'totalPoints' => [
                    'total' => $totalPointsNow,
                    'lastMonthTotal' => $totalPointsLastMonth,
                    'growthPercentage' => $growthPercentagePoints,
                ],
                'totalScans' => [
                    'total' => $totalScansNow,
                    'lastMonthTotal' => $totalScansLastMonth,
                    'growthPercentage' => $growthPercentageScans,
                ],
                'totalRewards' => [
                    'total' => $totalRewardsNow,
                    'lastMonthTotal' => $totalRewardsLastMonth,
                    'growthPercentage' => $growthPercentageRewards,
                ],
            ];
    
            return $this->apiResponse(true, 'Dashboard obtenido exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el dashboard.', null, $e->getMessage(), 500);
        }
    }
}
