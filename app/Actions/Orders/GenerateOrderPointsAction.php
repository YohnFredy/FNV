<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\User;
use App\Services\PointDistributionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Acción para generar y propagar los puntos MLM de una orden de compra.
 * Distribuye los puntos en el árbol binario (pierna correspondiente) y en el unilevel (volumen grupal),
 * además de acreditar los puntos personales al comprador y registrar en el libro contable inmutable.
 *
 * Garantiza idempotencia estricta para evitar doble acreditación de puntos.
 */
class GenerateOrderPointsAction
{
    public function __construct(
        protected PointDistributionService $pointDistributionService
    ) {}

    /**
     * Ejecuta la generación y distribución ascendente de puntos.
     *
     * @return array{
     *     order_id: int,
     *     public_order_number: string,
     *     buyer_id: int,
     *     points_distributed: float,
     *     binary_ancestors_count: int,
     *     unilevel_ancestors_count: int,
     *     total_ledger_entries: int,
     *     is_activated: bool
     * }
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function execute(Order $order): array
    {
        $points = (float) $order->total_pts;

        if ($points <= 0) {
            throw new InvalidArgumentException("La orden #{$order->public_order_number} no contiene puntos acumulables (total_pts es 0).");
        }

        // Bloqueo atómico por orden para prevenir concurrencia y doble procesamiento
        $lock = Cache::lock("order_points_{$order->id}", 10);

        return $lock->block(5, function () use ($order, $points): array {
            // Verificar idempotencia: Comprobar si ya se registraron transacciones para esta orden
            $alreadyDistributed = PointTransaction::where('source_type', 'order')
                ->where('order_id', $order->id)
                ->exists();

            if ($alreadyDistributed || $order->status === Order::STATUS_PTS_GENERATED) {
                throw new RuntimeException("Los puntos de la orden #{$order->public_order_number} ya fueron generados y distribuidos previamente.");
            }

            $buyer = $order->user;
            if (! $buyer instanceof User) {
                throw new RuntimeException("La orden #{$order->public_order_number} no tiene un comprador válido asociado.");
            }

            return DB::transaction(function () use ($order, $points, $buyer): array {
                // 1. Distribuir puntos a través del servicio de red
                $distributionSummary = $this->pointDistributionService->distributePoints(
                    buyer: $buyer,
                    points: $points,
                    orderId: $order->id,
                    description: "Compra #{$order->public_order_number}",
                    sourceType: 'order'
                );

                // 2. Actualizar estado de la orden a STATUS_PTS_GENERATED
                $order->update([
                    'status' => Order::STATUS_PTS_GENERATED,
                ]);

                // 3. Estado de activación mensual del comprador
                $isActivated = $distributionSummary['is_active'];

                return [
                    'order_id' => $order->id,
                    'public_order_number' => $order->public_order_number,
                    'buyer_id' => $buyer->id,
                    'points_distributed' => $distributionSummary['points_distributed'],
                    'binary_ancestors_count' => $distributionSummary['binary_ancestors_count'],
                    'unilevel_ancestors_count' => $distributionSummary['unilevel_ancestors_count'],
                    'total_ledger_entries' => $distributionSummary['total_ledger_entries'],
                    'is_activated' => $isActivated,
                ];
            });
        });
    }
}
