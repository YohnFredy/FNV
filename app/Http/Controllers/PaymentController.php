<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function boldResponse(Request $request)
    {
        $orderId = $request->input('bold-order-id');
        $status = $request->input('bold-tx-status');

        switch ($status) {
            case 'approved':
                $message = '¡Gracias por tu compra! Tu transacción ha sido aprobada con éxito.';
                break;

            case 'processing':
                $message = 'Tu transacción está en proceso. Te notificaremos en cuanto se confirme el pago.';
                break;

            case 'pending':
                $message = 'Tu transacción se encuentra pendiente de confirmación por la entidad bancaria.';
                break;

            case 'rejected':
                $message = 'Lo sentimos, tu transacción ha sido rechazada. Por favor, intenta con otro medio de pago.';
                break;

            case 'failed':
                $message = 'Ocurrió un error al procesar la transacción. Por favor, intenta nuevamente.';
                break;

            default:
                $message = 'Estado de la transacción recibido. Estamos verificando los detalles.';
                break;
        }

        $order = null;
        if ($orderId) {
            $order = Order::where('public_order_number', $orderId)->first();
        }

        return view('payments.response-bold', compact('orderId', 'status', 'message', 'order'));
    }
}
