<?php

namespace Database\Seeders;

use App\Models\ActivationPt;
use App\Models\MlmPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class MlmSettingsSeeder extends Seeder
{
    /**
     * Inicializa los parámetros de activación MLM y el primer periodo mensual.
     */
    public function run(): void
    {
        ActivationPt::firstOrCreate(
            ['id' => 1],
            [
                'min_pts_first' => 1.80,
                'min_pts_monthly' => 1.80,
                'first_activation_grace_period_enabled' => true,
                'grace_period_months' => 1,
            ]
        );

        $now = CarbonImmutable::now();
        MlmPeriod::firstOrCreate(
            ['code' => $now->format('Y-m')],
            [
                'name' => ucfirst($now->locale('es')->isoFormat('MMMM YYYY')),
                'starts_at' => $now->startOfMonth()->toDateString(),
                'ends_at' => $now->endOfMonth()->toDateString(),
                'min_activation_pts' => 1.80,
                'status' => MlmPeriod::STATUS_ACTIVE,
            ]
        );
    }
}
