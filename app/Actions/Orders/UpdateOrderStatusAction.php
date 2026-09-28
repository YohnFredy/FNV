<?php

namespace App\Actions\Orders;

use App\Models\Order;
use InvalidArgumentException;

/**
 * Acción para actualizar el estado de una orden de compra en el flujo de despacho y control administrativo.
 */
class UpdateOrderStatusAction
{
    /**
     * Estados permitidos en el sistema.
     */
    public const VALID_STATUSES = [
        Order::STATUS_SALE_PENDING,
        Order::STATUS_SALE_APPROVED,
        Order::STATUS_PTS_GENERATED,
        Order::STATUS_SENT,
        Order::STATUS_DELIVERED,
        Order::STATUS_SALE_REJECTED,
        Order::STATUS_VOIDED,
        Order::STATUS_VOID_REJECTED,
    ];

    /**
     * Actualiza el estado de la orden.
     *
     * @throws InvalidArgumentException
     */
    public function execute(Order $order, int $newStatus): Order
    {
        if (! in_array($newStatus, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("El estado [{$newStatus}] no es válido para una orden de compra.");
        }

        $order->update([
            'status' => $newStatus,
        ]);

        return $order->fresh();
    }
}
