<?php

namespace App\Services;

use App\Models\ActivationPt;
use App\Models\BinarySummary;
use App\Models\MlmPeriod;
use App\Models\MlmPeriodUserBalance;
use App\Models\UnilevelSummary;
use App\Models\User;
use App\Models\UserActivation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Servicio de Liquidación y Cierre de Ciclo Mensual MLM.
 * Ejecuta el cálculo de comisiones binarias y escalonadas, liquida únicamente
 * a usuarios activos (>= 1.80 pts, mes de gracia o admin), registra las instantáneas
 * inmutables en mlm_period_user_balances, efectúa el Flush Total (reseteo a 0-0)
 * e inicializa el nuevo periodo contable.
 */
class MlmSettlementService
{
    public function __construct(
        protected MlmPeriodService $periodService
    ) {}

    /**
     * Simula la liquidación del periodo sin realizar cambios en la base de datos (Pre-liquidación).
     *
     * @return array{
     *     period_id: int,
     *     period_name: string,
     *     total_users: int,
     *     active_users: int,
     *     inactive_users: int,
     *     total_binary_points_matched: float,
     *     estimated_binary_commissions: float,
     *     estimated_strategic_partner_commissions: float,
     *     estimated_rank_commissions: float,
     *     estimated_other_commissions: float,
     *     estimated_total_payout: float,
     *     total_personal_points: float
     * }
     */
    public function previewSettlement(?MlmPeriod $period = null): array
    {
        $period ??= $this->periodService->getActivePeriod();

        $activeCount = 0;
        $inactiveCount = 0;
        $totalMatched = 0.0;
        $totalPersonal = 0.0;
        $totalStrategic = 0.0;
        $totalRank = 0.0;
        $totalOther = 0.0;

        $startDate = $period->starts_at?->startOfDay() ?? now()->startOfMonth();
        $endDate = $period->ends_at?->endOfDay() ?? now()->endOfMonth();

        $users = User::with([
            'activation',
            'binarySummary',
            'unilevelSummary',
            'invoices' => fn ($q) => $q->where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate]),
        ])->get();

        foreach ($users as $user) {
            $personal = (float) ($user->unilevelSummary?->personal_points ?? 0);
            $totalPersonal += $personal;

            $isActive = $user->activation?->isValidActive() ?? false;

            if ($isActive) {
                $activeCount++;
                $left = (float) ($user->binarySummary?->total_left_points ?? 0);
                $right = (float) ($user->binarySummary?->total_right_points ?? 0);
                $matched = min($left, $right);
                $totalMatched += $matched;

                // Comisiones por compras en Comercios / Socios Estratégicos
                $strategicComm = (float) $user->invoices->sum('commission_total');
                $totalStrategic += $strategicComm;
            } else {
                $inactiveCount++;
            }
        }

        // Estimación estándar de binario (10% sobre puntos de pierna menor)
        $estimatedBinary = $totalMatched * 0.10;
        $estimatedTotal = $estimatedBinary + $totalStrategic + $totalRank + $totalOther;

        return [
            'period_id' => $period->id,
            'period_name' => $period->name,
            'total_users' => $users->count(),
            'active_users' => $activeCount,
            'inactive_users' => $inactiveCount,
            'total_binary_points_matched' => $totalMatched,
            'estimated_binary_commissions' => $estimatedBinary,
            'estimated_strategic_partner_commissions' => $totalStrategic,
            'estimated_rank_commissions' => $totalRank,
            'estimated_other_commissions' => $totalOther,
            'estimated_total_payout' => $estimatedTotal,
            'total_personal_points' => $totalPersonal,
        ];
    }

    /**
     * Ejecuta el cierre definitivo de mes, liquida comisiones, guarda snapshots y aplica Flush Total.
     *
     * @return array{
     *     period_id: int,
     *     period_name: string,
     *     processed_users: int,
     *     active_users: int,
     *     total_payout: float,
     *     new_period_code: string
     * }
     */
    public function executeSettlement(?MlmPeriod $period = null): array
    {
        $period ??= $this->periodService->getActivePeriod();

        if ($period->status === MlmPeriod::STATUS_SETTLED) {
            throw new RuntimeException("El periodo {$period->name} ya fue liquidado y cerrado previamente.");
        }

        return DB::transaction(function () use ($period) {
            $now = CarbonImmutable::now();
            $startDate = $period->starts_at?->startOfDay() ?? $now->startOfMonth();
            $endDate = $period->ends_at?->endOfDay() ?? $now->endOfMonth();

            // 1. Congelar el periodo actual
            $period->update(['status' => MlmPeriod::STATUS_CLOSING]);

            $processedCount = 0;
            $activeCount = 0;

            $totalBinaryPayout = 0.0;
            $totalUnilevelPayout = 0.0;
            $totalStrategicPayout = 0.0;
            $totalRankPayout = 0.0;
            $totalOtherPayout = 0.0;

            // 2. Procesar usuarios por lotes para alta concurrencia
            User::with([
                'activation',
                'binarySummary',
                'unilevelSummary',
                'invoices' => fn ($q) => $q->where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate]),
            ])->chunkById(500, function ($users) use (
                $period,
                $now,
                &$processedCount,
                &$activeCount,
                &$totalBinaryPayout,
                &$totalUnilevelPayout,
                &$totalStrategicPayout,
                &$totalRankPayout,
                &$totalOtherPayout
            ) {
                $balancesToInsert = [];

                foreach ($users as $user) {
                    $processedCount++;

                    $personal = (float) ($user->unilevelSummary?->personal_points ?? 0);
                    $group = (float) ($user->unilevelSummary?->group_points ?? 0);
                    $left = (float) ($user->binarySummary?->total_left_points ?? 0);
                    $right = (float) ($user->binarySummary?->total_right_points ?? 0);

                    $activation = $user->activation;
                    $isActive = $activation?->isValidActive() ?? false;
                    $activationType = $isActive ? ($activation->activation_type ?? 'points') : 'none';

                    $matched = 0.0;
                    $binaryComm = 0.0;
                    $unilevelComm = 0.0;
                    $strategicComm = 0.0;
                    $rankComm = 0.0;
                    $otherComm = 0.0;

                    if ($isActive) {
                        $activeCount++;
                        $matched = min($left, $right);
                        $binaryComm = $matched * 0.10; // 10% estándar sobre pierna menor

                        // Comisiones por compras asociadas a Comercios / Socios Estratégicos
                        $strategicComm = (float) $user->invoices->sum('commission_total');
                    }

                    $totalUserComm = $binaryComm + $unilevelComm + $strategicComm + $rankComm + $otherComm;

                    $totalBinaryPayout += $binaryComm;
                    $totalUnilevelPayout += $unilevelComm;
                    $totalStrategicPayout += $strategicComm;
                    $totalRankPayout += $rankComm;
                    $totalOtherPayout += $otherComm;

                    $rankLevel = (int) ($activation?->current_rank ?? 0);
                    $rankName = match ($rankLevel) {
                        0 => 'Afiliado',
                        1 => 'Bronce',
                        2 => 'Plata',
                        3 => 'Oro',
                        4 => 'Zafiro',
                        5 => 'Esmeralda',
                        6 => 'Rubí',
                        7 => 'Diamante',
                        8 => 'Diamante Azul',
                        9 => 'Diamante Negro',
                        10 => 'Diamante Corona',
                        default => "Rango {$rankLevel}",
                    };

                    $balancesToInsert[] = [
                        'period_id' => $period->id,
                        'user_id' => $user->id,
                        'personal_points' => $personal,
                        'binary_left_points' => $left,
                        'binary_right_points' => $right,
                        'binary_points_matched' => $matched,
                        'unilevel_group_points' => $group,
                        'is_active' => $isActive,
                        'activation_type' => $activationType,
                        'rank_achieved' => $rankName,
                        'rank_level' => $rankLevel,
                        'commission_binary' => $binaryComm,
                        'commission_unilevel' => $unilevelComm,
                        'commission_strategic_partner' => $strategicComm,
                        'commission_rank' => $rankComm,
                        'commission_other' => $otherComm,
                        'total_commission' => $totalUserComm,
                        'commission_notes' => null,
                        'settled_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Inserción en lote de instantáneas (Snapshots)
                MlmPeriodUserBalance::upsert(
                    $balancesToInsert,
                    ['user_id', 'period_id'],
                    [
                        'personal_points',
                        'binary_left_points',
                        'binary_right_points',
                        'binary_points_matched',
                        'unilevel_group_points',
                        'is_active',
                        'activation_type',
                        'rank_achieved',
                        'rank_level',
                        'commission_binary',
                        'commission_unilevel',
                        'commission_strategic_partner',
                        'commission_rank',
                        'commission_other',
                        'total_commission',
                        'commission_notes',
                        'settled_at',
                        'updated_at',
                    ]
                );
            });

            // 3. FLUSH TOTAL: Poner contadores de puntos en 0 en el árbol binario y unilevel
            BinarySummary::query()->update([
                'total_left_points' => 0,
                'total_right_points' => 0,
            ]);

            UnilevelSummary::query()->update([
                'personal_points' => 0,
                'group_points' => 0,
            ]);

            // 4. Actualizar estados de activación para el nuevo ciclo:
            // Aquellos cuya fecha de expiración ya caducó pasan a inactivos.
            // Aquellos que tienen mes de gracia o activación admin vigente continúan activos.
            UserActivation::where('expires_at', '<=', $now)
                ->update([
                    'is_active' => false,
                    'deactivated_at' => $now,
                ]);

            $totalPayout = $totalBinaryPayout + $totalUnilevelPayout + $totalStrategicPayout + $totalRankPayout + $totalOtherPayout;

            // 5. Marcar periodo como liquidado y cerrado con sus consolidados contables
            $period->update([
                'total_commission_binary' => $totalBinaryPayout,
                'total_commission_unilevel' => $totalUnilevelPayout,
                'total_commission_strategic_partner' => $totalStrategicPayout,
                'total_commission_rank' => $totalRankPayout,
                'total_commission_other' => $totalOtherPayout,
                'total_payout' => $totalPayout,
                'status' => MlmPeriod::STATUS_SETTLED,
                'settled_at' => $now,
            ]);

            // 6. Abrir automáticamente el periodo del nuevo mes
            $nextMonth = $now->addMonthNoOverflow();
            $minPts = (float) (ActivationPt::value('min_pts_monthly') ?? 1.80);

            $newPeriod = MlmPeriod::firstOrCreate(
                ['code' => $nextMonth->format('Y-m')],
                [
                    'name' => ucfirst($nextMonth->locale('es')->isoFormat('MMMM YYYY')),
                    'starts_at' => $nextMonth->startOfMonth()->toDateString(),
                    'ends_at' => $nextMonth->endOfMonth()->toDateString(),
                    'min_activation_pts' => $minPts,
                    'status' => MlmPeriod::STATUS_ACTIVE,
                ]
            );

            Cache::forget('mlm_active_period_id');
            Cache::forget('mlm_active_period');

            return [
                'period_id' => $period->id,
                'period_name' => $period->name,
                'processed_users' => $processedCount,
                'active_users' => $activeCount,
                'total_payout' => $totalPayout,
                'new_period_code' => $newPeriod->code,
            ];
        });
    }
}
