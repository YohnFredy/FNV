<?php

namespace App\Services;

use App\Actions\GraduateAffiliateAction;
use App\Models\BinaryPath;
use App\Models\BinarySummary;
use App\Models\PointTransaction;
use App\Models\UnilevelPath;
use App\Models\UnilevelSummary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Servicio de Distribución y Propagación de Puntos MLM.
 * Cuando un afiliado realiza compras, este servicio genera puntos personales
 * y propaga el volumen hacia toda la línea ascendente del árbol binario (según la pierna correspondiente)
 * y del árbol escalonado (volumen grupal), registrando cada movimiento en el libro contable inmutable.
 *
 * Si el usuario se encuentra en la Sala de Espera (Holding Tank), los puntos binarios se propagan
 * a través de la posición de su patrocinador por la pierna asignada.
 * Apenas sus compras acumuladas alcancen la meta mínima (ej. 1.80 pts), se dispara automáticamente
 * su graduación y ocupación física definitiva en el árbol binario y unilevel.
 */
class PointDistributionService
{
    public function __construct(
        protected MlmPeriodService $periodService,
        protected GraduateAffiliateAction $graduateAffiliateAction,
        protected BinaryPlacementService $binaryPlacementService,
    ) {}

    /**
     * Procesa y distribuye los puntos originados por una compra.
     *
     * @param  User  $buyer  Usuario que efectúa la compra.
     * @param  float  $points  Cantidad de puntos a otorgar (mayor a 0).
     * @param  int|null  $orderId  ID de la orden de tienda virtual (opcional).
     * @param  string  $description  Detalle o concepto de la compra.
     * @param  string  $sourceType  Origen de los puntos: 'order' (tienda virtual) o 'invoice' (comercios aliados).
     * @param  int|null  $invoiceId  ID de la factura de comercio aliado (opcional).
     * @return array{buyer_id: int, points_distributed: float, binary_ancestors_count: int, unilevel_ancestors_count: int, total_ledger_entries: int, is_active: bool, graduated: bool} Resumen de la distribución efectuada.
     *
     * @throws InvalidArgumentException Si la cantidad de puntos es menor o igual a cero.
     */
    public function distributePoints(
        User $buyer,
        float $points,
        ?int $orderId = null,
        string $description = 'Compra de productos',
        string $sourceType = 'order',
        ?int $invoiceId = null
    ): array {
        if ($points <= 0) {
            throw new InvalidArgumentException('La cantidad de puntos a distribuir debe ser superior a cero.');
        }

        $activePeriod = $this->periodService->getActivePeriod();

        return DB::transaction(function () use ($buyer, $points, $orderId, $description, $sourceType, $invoiceId, $activePeriod) {
            $now = now();
            $ledgerRecords = [];

            $actualOrderId = $sourceType === 'order' ? $orderId : null;
            $actualInvoiceId = $sourceType === 'invoice' ? ($invoiceId ?? $orderId) : $invoiceId;

            // =========================================================================
            // 1. PUNTOS PERSONALES DEL COMPRADOR (Personal Points)
            // =========================================================================
            $ledgerRecords[] = [
                'period_id' => $activePeriod->id,
                'user_id' => $buyer->id,
                'from_user_id' => $buyer->id,
                'source_type' => $sourceType,
                'order_id' => $actualOrderId,
                'invoice_id' => $actualInvoiceId,
                'tree_type' => 'personal',
                'leg' => null,
                'points' => $points,
                'description' => "Puntos personales: {$description}",
                'created_at' => $now,
            ];

            // Acumular en el resumen escalonado del comprador (crear si no existiera)
            UnilevelSummary::firstOrCreate(
                ['user_id' => $buyer->id],
                [
                    'direct_sponsors_count' => 0,
                    'total_network_members' => 0,
                    'personal_points' => 0,
                    'group_points' => 0,
                ]
            );
            UnilevelSummary::where('user_id', $buyer->id)->increment('personal_points', $points);

            $leftBinaryAncestorIds = [];
            $rightBinaryAncestorIds = [];
            $unilevelAncestorIds = [];

            // =========================================================================
            // 2. PROPAGACIÓN ASCENDENTE EN EL ÁRBOL BINARIO Y UNILEVEL
            // =========================================================================
            $isPlacedInTree = $buyer->isPlacedInTree();

            if ($isPlacedInTree) {
                // CASO A: El afiliado ya tiene posición física en los árboles
                $binaryAncestors = BinaryPath::where('descendant_id', $buyer->id)
                    ->where('ancestor_id', '!=', $buyer->id)
                    ->get(['ancestor_id', 'leg']);

                foreach ($binaryAncestors as $ancestorPath) {
                    $ledgerRecords[] = [
                        'period_id' => $activePeriod->id,
                        'user_id' => $ancestorPath->ancestor_id,
                        'from_user_id' => $buyer->id,
                        'source_type' => $sourceType,
                        'order_id' => $actualOrderId,
                        'invoice_id' => $actualInvoiceId,
                        'tree_type' => 'binary',
                        'leg' => $ancestorPath->leg,
                        'points' => $points,
                        'description' => "Volumen binario pierna [{$ancestorPath->leg}] por compra de [{$buyer->username}]",
                        'created_at' => $now,
                    ];

                    if ($ancestorPath->leg === 'L') {
                        $leftBinaryAncestorIds[] = $ancestorPath->ancestor_id;
                    } elseif ($ancestorPath->leg === 'R') {
                        $rightBinaryAncestorIds[] = $ancestorPath->ancestor_id;
                    }
                }

                // Ancestros escalonados
                $unilevelAncestorIds = UnilevelPath::where('descendant_id', $buyer->id)
                    ->where('ancestor_id', '!=', $buyer->id)
                    ->pluck('ancestor_id')
                    ->all();

                foreach ($unilevelAncestorIds as $ancestorId) {
                    $ledgerRecords[] = [
                        'period_id' => $activePeriod->id,
                        'user_id' => $ancestorId,
                        'from_user_id' => $buyer->id,
                        'source_type' => $sourceType,
                        'order_id' => $actualOrderId,
                        'invoice_id' => $actualInvoiceId,
                        'tree_type' => 'unilevel',
                        'leg' => null,
                        'points' => $points,
                        'description' => "Puntos grupales escalonados por compra de [{$buyer->username}]",
                        'created_at' => $now,
                    ];
                }
            } else {
                // CASO B: El afiliado está en la Sala de Espera (Holding Tank)
                // Se acreditan como puntos clientes sala de espera al patrocinador directo por la pierna configurada
                // y se propagan como puntos de red a toda la línea ascendente del patrocinador.
                // NO se generan puntos a los nodos de derrame inferiores hasta que califique y ocupe posición física.
                $sponsor = $buyer->sponsor ?? ($buyer->sponsor_id ? User::find($buyer->sponsor_id) : null);

                if ($sponsor) {
                    $leg = $buyer->preferred_leg ? strtoupper($buyer->preferred_leg) : 'L';
                    if (! in_array($leg, ['L', 'R'], true)) {
                        $leg = 'L';
                    }

                    // 1. Registro y suma binaria para el Patrocinador directo por la pierna seleccionada
                    $ledgerRecords[] = [
                        'period_id' => $activePeriod->id,
                        'user_id' => $sponsor->id,
                        'from_user_id' => $buyer->id,
                        'source_type' => $sourceType,
                        'order_id' => $actualOrderId,
                        'invoice_id' => $actualInvoiceId,
                        'tree_type' => 'binary',
                        'leg' => $leg,
                        'points' => $points,
                        'description' => "Volumen binario pierna [{$leg}] por compra de [{$buyer->username}] en sala de espera",
                        'created_at' => $now,
                    ];

                    if ($leg === 'L') {
                        $leftBinaryAncestorIds[] = $sponsor->id;
                    } else {
                        $rightBinaryAncestorIds[] = $sponsor->id;
                    }

                    // 2. Ancestros binarios superiores del patrocinador
                    $sponsorBinaryAncestors = BinaryPath::where('descendant_id', $sponsor->id)
                        ->where('ancestor_id', '!=', $sponsor->id)
                        ->get(['ancestor_id', 'leg']);

                    foreach ($sponsorBinaryAncestors as $ancestorPath) {
                        $ledgerRecords[] = [
                            'period_id' => $activePeriod->id,
                            'user_id' => $ancestorPath->ancestor_id,
                            'from_user_id' => $buyer->id,
                            'source_type' => $sourceType,
                            'order_id' => $actualOrderId,
                            'invoice_id' => $actualInvoiceId,
                            'tree_type' => 'binary',
                            'leg' => $ancestorPath->leg,
                            'points' => $points,
                            'description' => "Volumen binario pierna [{$ancestorPath->leg}] por pre-afiliado [{$buyer->username}] vía [{$sponsor->username}]",
                            'created_at' => $now,
                        ];

                        if ($ancestorPath->leg === 'L') {
                            $leftBinaryAncestorIds[] = $ancestorPath->ancestor_id;
                        } elseif ($ancestorPath->leg === 'R') {
                            $rightBinaryAncestorIds[] = $ancestorPath->ancestor_id;
                        }
                    }

                    // 3. Puntos grupales escalonados para el patrocinador
                    $unilevelAncestorIds[] = $sponsor->id;
                    $ledgerRecords[] = [
                        'period_id' => $activePeriod->id,
                        'user_id' => $sponsor->id,
                        'from_user_id' => $buyer->id,
                        'source_type' => $sourceType,
                        'order_id' => $actualOrderId,
                        'invoice_id' => $actualInvoiceId,
                        'tree_type' => 'unilevel',
                        'leg' => null,
                        'points' => $points,
                        'description' => "Puntos grupales escalonados por pre-afiliado [{$buyer->username}] en sala de espera",
                        'created_at' => $now,
                    ];

                    // 4. Ancestros unilevel superiores del patrocinador
                    $sponsorUnilevelAncestors = UnilevelPath::where('descendant_id', $sponsor->id)
                        ->where('ancestor_id', '!=', $sponsor->id)
                        ->pluck('ancestor_id')
                        ->all();

                    foreach ($sponsorUnilevelAncestors as $ancestorId) {
                        $unilevelAncestorIds[] = $ancestorId;
                        $ledgerRecords[] = [
                            'period_id' => $activePeriod->id,
                            'user_id' => $ancestorId,
                            'from_user_id' => $buyer->id,
                            'source_type' => $sourceType,
                            'order_id' => $actualOrderId,
                            'invoice_id' => $actualInvoiceId,
                            'tree_type' => 'unilevel',
                            'leg' => null,
                            'points' => $points,
                            'description' => "Puntos grupales escalonados por pre-afiliado [{$buyer->username}] vía [{$sponsor->username}]",
                            'created_at' => $now,
                        ];
                    }
                }
            }

            // Actualizaciones atómicas en bloque de contadores binarios
            if (! empty($leftBinaryAncestorIds)) {
                BinarySummary::whereIn('user_id', array_unique($leftBinaryAncestorIds))->increment('total_left_points', $points);
            }
            if (! empty($rightBinaryAncestorIds)) {
                BinarySummary::whereIn('user_id', array_unique($rightBinaryAncestorIds))->increment('total_right_points', $points);
            }

            // Actualización atómica en bloque de volumen grupal unilevel
            if (! empty($unilevelAncestorIds)) {
                UnilevelSummary::whereIn('user_id', array_unique($unilevelAncestorIds))->increment('group_points', $points);
            }

            // =========================================================================
            // 3. INSERCIÓN EN EL LIBRO MAYOR INMUTABLE (Point Ledger)
            // =========================================================================
            PointTransaction::insert($ledgerRecords);

            // =========================================================================
            // 4. EVALUACIÓN DE GRADUACIÓN DE SALA DE ESPERA (Meta >= 1.80 Pts)
            // =========================================================================
            $buyer->load('unilevelSummary');
            $currentPersonalPoints = (float) ($buyer->unilevelSummary->personal_points ?? 0);
            $minRequired = (float) ($activePeriod->min_activation_pts ?? 1.80);

            $graduated = false;
            if ($buyer->isInWaitingRoom() && $currentPersonalPoints >= $minRequired) {
                $this->graduateAffiliateAction->execute($buyer);
                $graduated = true;
            }

            // =========================================================================
            // 5. EVALUACIÓN DE ACTIVACIÓN MENSUAL (Periodo / Vigencia)
            // =========================================================================
            $isActive = $this->periodService->evaluateUserMonthlyActivation($buyer, $activePeriod);

            return [
                'buyer_id' => $buyer->id,
                'points_distributed' => $points,
                'binary_ancestors_count' => count(array_unique(array_merge($leftBinaryAncestorIds, $rightBinaryAncestorIds))),
                'unilevel_ancestors_count' => count(array_unique($unilevelAncestorIds)),
                'total_ledger_entries' => count($ledgerRecords),
                'is_active' => $isActive,
                'graduated' => $graduated,
            ];
        }, 5);
    }
}
