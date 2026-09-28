<?php

namespace App\Actions;

use App\Enums\MlmStatus;
use App\Models\BinaryPath;
use App\Models\BinarySummary;
use App\Models\PointTransaction;
use App\Models\User;
use App\Services\BinaryPlacementService;
use App\Services\MlmPeriodService;
use App\Services\UnilevelPlacementService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Acción para graduar a un afiliado desde la Sala de Espera (Holding Tank)
 * y asignarle su posición definitiva e inmutable en el Árbol Binario y Árbol Escalonado (Unilevel).
 */
class GraduateAffiliateAction
{
    public function __construct(
        protected BinaryPlacementService $binaryPlacementService,
        protected UnilevelPlacementService $unilevelPlacementService,
        protected MlmPeriodService $periodService,
    ) {}

    /**
     * Ejecuta la graduación y colocación física en ambos árboles.
     *
     * @param  User  $user  Usuario en sala de espera a graduar.
     * @return User El usuario graduado con estado activo y posición en los árboles.
     *
     * @throws InvalidArgumentException Si el usuario no está en sala de espera o no tiene patrocinador.
     */
    public function execute(User $user): User
    {
        if (! $user->isInWaitingRoom()) {
            return $user; // Ya se encuentra graduado o colocado
        }

        if (! $user->sponsor_id) {
            return $user; // Usuario sin patrocinador (ej. cliente final minorista) no entra a la red binaria
        }

        $sponsor = $user->sponsor ?? User::find($user->sponsor_id);
        if (! $sponsor) {
            throw new RuntimeException("No se encontró el registro del patrocinador [ID: {$user->sponsor_id}] para el usuario [{$user->username}].");
        }

        $leg = $user->preferred_leg ? strtoupper($user->preferred_leg) : 'L';
        if (! in_array($leg, ['L', 'R'], true)) {
            $leg = 'L';
        }

        return DB::transaction(function () use ($user, $sponsor, $leg) {
            $activePeriod = $this->periodService->getActivePeriod();

            // 1. Colocación en el Árbol Escalonado (Unilevel)
            $this->unilevelPlacementService->placeAffiliate($user, $sponsor);

            // 2. Colocación en el Árbol Binario (en la pierna designada por derrame exterior)
            $this->binaryPlacementService->placeAffiliate($user, $sponsor, $leg);

            // 3. Propagación de puntos acumulados a los nodos intermedios de derrame
            // El patrocinador directo y su línea ascendente YA recibieron los puntos durante las compras en sala de espera.
            // Por regla estricta de negocio ("que los que ya recibieron un puntaje no se les repita"),
            // únicamente se le acreditan estos puntos a los nodos intermediarios entre el patrocinador y la nueva posición.
            $user->load('unilevelSummary');
            $accumulatedPoints = (float) ($user->unilevelSummary->personal_points ?? 0);

            if ($accumulatedPoints > 0) {
                // Ancestros del patrocinador (incluido el patrocinador) que ya tienen los puntos acreditados
                $sponsorAndUplines = BinaryPath::where('descendant_id', $sponsor->id)
                    ->pluck('ancestor_id')
                    ->all();

                // Todos los ancestros binarios de la nueva posición física del usuario
                $userAncestors = BinaryPath::where('descendant_id', $user->id)
                    ->where('ancestor_id', '!=', $user->id)
                    ->get(['ancestor_id', 'leg']);

                // Filtrar exclusivamente los intermediarios (descendientes del sponsor ubicados por encima del nuevo nodo)
                $intermediaries = $userAncestors->whereNotIn('ancestor_id', $sponsorAndUplines);

                if ($intermediaries->isNotEmpty()) {
                    $now = now();
                    $ledgerRecords = [];
                    $leftIds = [];
                    $rightIds = [];

                    foreach ($intermediaries as $intermediary) {
                        $ledgerRecords[] = [
                            'period_id' => $activePeriod->id,
                            'user_id' => $intermediary->ancestor_id,
                            'from_user_id' => $user->id,
                            'source_type' => 'order',
                            'order_id' => null,
                            'invoice_id' => null,
                            'tree_type' => 'binary',
                            'leg' => $intermediary->leg,
                            'points' => $accumulatedPoints,
                            'description' => "Volumen binario pierna [{$intermediary->leg}] por activación y colocación de [{$user->username}] vía [{$sponsor->username}]",
                            'created_at' => $now,
                        ];

                        if ($intermediary->leg === 'L') {
                            $leftIds[] = $intermediary->ancestor_id;
                        } elseif ($intermediary->leg === 'R') {
                            $rightIds[] = $intermediary->ancestor_id;
                        }
                    }

                    PointTransaction::insert($ledgerRecords);

                    if (! empty($leftIds)) {
                        BinarySummary::whereIn('user_id', array_unique($leftIds))->increment('total_left_points', $accumulatedPoints);
                    }
                    if (! empty($rightIds)) {
                        BinarySummary::whereIn('user_id', array_unique($rightIds))->increment('total_right_points', $accumulatedPoints);
                    }
                }
            }

            // 4. Actualizar estado y fecha de colocación
            $user->update([
                'mlm_status' => MlmStatus::ACTIVE_AFFILIATE,
                'placed_at' => now(),
            ]);

            // 5. Evaluar activación mensual del periodo
            $this->periodService->evaluateUserMonthlyActivation($user, $activePeriod);

            return $user->refresh();
        }, 5);
    }
}
