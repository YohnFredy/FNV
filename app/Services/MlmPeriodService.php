<?php

namespace App\Services;

use App\Models\ActivationPt;
use App\Models\MlmPeriod;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio de Gestión de Periodos y Calificación de Afiliados MLM.
 * Centraliza la obtención del ciclo activo, la evaluación de activación mensual (>= 1.80 pts),
 * la política de mes de gracia para nuevos afiliados y la activación administrativa.
 */
class MlmPeriodService
{
    /**
     * Obtiene el periodo actualmente activo o lo inicializa automáticamente si no existe.
     */
    public function getActivePeriod(): MlmPeriod
    {
        $periodId = Cache::remember('mlm_active_period_id', 60, function () {
            $period = MlmPeriod::where('status', MlmPeriod::STATUS_ACTIVE)->first();

            if (! $period) {
                $now = CarbonImmutable::now();
                $minPts = (float) (ActivationPt::value('min_pts_monthly') ?? 1.80);

                $period = MlmPeriod::firstOrCreate(
                    ['code' => $now->format('Y-m')],
                    [
                        'name' => ucfirst($now->locale('es')->isoFormat('MMMM YYYY')),
                        'starts_at' => $now->startOfMonth()->toDateString(),
                        'ends_at' => $now->endOfMonth()->toDateString(),
                        'min_activation_pts' => $minPts,
                        'status' => MlmPeriod::STATUS_ACTIVE,
                    ]
                );
            }

            return $period->id;
        });

        $period = MlmPeriod::find($periodId);

        if (! $period || $period->status !== MlmPeriod::STATUS_ACTIVE) {
            Cache::forget('mlm_active_period_id');

            return MlmPeriod::where('status', MlmPeriod::STATUS_ACTIVE)->firstOrFail();
        }

        return $period;
    }

    /**
     * Evalúa y actualiza el estado de activación mensual del afiliado de forma acumulativa.
     *
     * @param  User  $user  Usuario a evaluar.
     * @param  MlmPeriod|null  $period  Periodo a evaluar (por defecto el activo).
     * @return bool True si el usuario quedó activo en el periodo.
     */
    public function evaluateUserMonthlyActivation(User $user, ?MlmPeriod $period = null): bool
    {
        $period ??= $this->getActivePeriod();
        $activation = $user->activation()->firstOrCreate(['user_id' => $user->id]);

        // 1. Si ya cuenta con activación manual por administrador o mes de gracia aún vigente
        if ($activation->is_active && $activation->isValidActive() && in_array($activation->activation_type, ['admin', 'grace_period'], true)) {
            return true;
        }

        // 2. Consultar puntos personales acumulados en el mes
        $personalPoints = (float) ($user->unilevelSummary?->personal_points ?? 0);
        $minRequired = (float) ($period->min_activation_pts ?? 1.80);

        if ($personalPoints >= $minRequired) {
            $now = CarbonImmutable::now();
            $isFirstTime = is_null($activation->first_activated_at);

            $config = ActivationPt::first();
            $graceEnabled = (bool) ($config?->first_activation_grace_period_enabled ?? true);

            // Si es su primera activación en la historia y la empresa tiene activo el mes de gracia
            if ($isFirstTime && $graceEnabled && ! $activation->has_used_grace_period) {
                // Activo para el mes actual y el mes siguiente completo
                $expiresAt = $now->addMonthNoOverflow()->endOfMonth();
                $activation->activateUntil($expiresAt, 'grace_period');
            } else {
                // Activación mensual regular hasta el fin del mes en curso
                $expiresAt = $now->endOfMonth();
                $activation->activateUntil($expiresAt, 'points');
            }

            return true;
        }

        return false;
    }

    /**
     * Activa manualmente a un usuario por orden de un administrador.
     */
    public function manuallyActivateByAdmin(
        User $user,
        User $admin,
        ?\DateTimeInterface $expiresAt = null,
        ?string $notes = null
    ): void {
        $activation = $user->activation()->firstOrCreate(['user_id' => $user->id]);
        $expiresAt ??= CarbonImmutable::now()->endOfMonth();

        $activation->activateUntil(
            expiresAt: $expiresAt,
            type: 'admin',
            adminId: $admin->id,
            adminNotes: $notes
        );
    }

    /**
     * Revoca manualmente la activación de un usuario.
     */
    public function deactivateUser(User $user): void
    {
        $activation = $user->activation;
        if ($activation) {
            $activation->deactivate();
        }
    }
}
