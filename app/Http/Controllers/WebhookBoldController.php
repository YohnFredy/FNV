<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookBoldController extends Controller
{
    public function handleBoldWebhook(Request $request)
    {
        $signature = $request->header('x-bold-signature');
        $strMessage = $request->getContent();
        $encoded = base64_encode($strMessage);

        $secretKey = config('services.bold.secret_key');
        $hashed = hash_hmac('sha256', $encoded, $secretKey);

        if (! hash_equals($hashed, (string) $signature)) {
            Log::warning('Bold webhook: firma inválida.', ['signature' => $signature]);

            return response('Invalid signature', 400);
        }

        try {
            $payload = json_decode($strMessage, true);

            if (! $payload) {
                Log::warning('Bold webhook: payload inválido.');

                return response('Invalid payload', 400);
            }

            DB::transaction(function () use ($payload) {
                $reference = $payload['data']['metadata']['reference'] ?? null;

                if ($reference) {
                    PaymentWebhook::create([
                        'payment_gateway' => 'bold',
                        'reference' => $reference,
                        'payload' => $payload,
                    ]);

                    $this->updateOrderStatus($payload);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error al procesar webhook de Bold: '.$e->getMessage());
        }

        return response('OK', 200);
    }

    protected function updateOrderStatus(array $payload): void
    {
        $reference = $payload['data']['metadata']['reference'] ?? null;
        if (! $reference) {
            return;
        }

        $order = Order::where('public_order_number', $reference)->first();

        $status = match ($payload['type'] ?? '') {
            'SALE_APPROVED' => Order::STATUS_SALE_APPROVED,
            'SALE_REJECTED' => Order::STATUS_SALE_REJECTED,
            'VOID_APPROVED' => Order::STATUS_VOIDED,
            'VOID_REJECTED' => Order::STATUS_VOID_REJECTED,
            default => Order::STATUS_SALE_PENDING,
        };

        if ($order) {
            $order->update([
                'status' => $status,
                'payment_method' => 'bold',
            ]);
        } else {
            Log::warning('Orden no encontrada para referencia en webhook Bold: '.$reference);
        }
    }
}
