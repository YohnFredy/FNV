<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('user_id', $user->id);

        if ($request->filled('status')) {
            $status = (int) $request->status;

            if ($status === 2) {
                $query->whereIn('status', [2, 3]);
            } elseif ($status === 6) {
                $query->where('status', '>=', 6);
            } else {
                $query->where('status', $status);
            }
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $pendientes = Order::where('status', 1)->where('user_id', $user->id)->count();
        $recibido = Order::whereIn('status', [2, 3])->where('user_id', $user->id)->count();
        $enviado = Order::where('status', 4)->where('user_id', $user->id)->count();
        $entregado = Order::where('status', 5)->where('user_id', $user->id)->count();
        $anulado = Order::where('status', '>=', 6)->where('user_id', $user->id)->count();

        return view('orders.index', compact('orders', 'pendientes', 'recibido', 'enviado', 'entregado', 'anulado'));
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['billingData.documentType', 'billingData.city', 'billingData.department', 'billingData.country', 'shippingCountry', 'shippingDepartment', 'shippingCity', 'items.product.latestImage']);

        return view('orders.show', compact('order'));
    }

    public function boldCheckout(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['billingData.documentType', 'billingData.city', 'billingData.department', 'billingData.country', 'shippingCountry', 'shippingDepartment', 'shippingCity', 'items.product.latestImage']);

        $apiKey = config('services.bold.api_key');
        $secretKey = config('services.bold.secret_key');
        $currency = 'COP';
        $amount = (int) round($order->total);
        $orderId = $order->public_order_number;

        $boldHashString = "{$orderId}{$amount}{$currency}{$secretKey}";
        $boldIntegritySignature = hash('sha256', $boldHashString);

        $expirationTimestamp = now()->addHour()->timestamp * 1_000_000_000;

        $boldCheckoutConfig = [
            'orderId' => $orderId,
            'currency' => $currency,
            'amount' => $amount,
            'apiKey' => $apiKey,
            'integritySignature' => $boldIntegritySignature,
            'description' => 'Pago de pedido #'.$orderId,
            'tax' => (int) round($order->tax_amount),
            'redirectionUrl' => config('services.bold.redirect_url', route('bold.response')),
            'expiration-date' => $expirationTimestamp,
        ];

        return view('orders.checkout-bold', compact('order', 'boldCheckoutConfig'));
    }

    protected function authorizeOrder(Order $order): void
    {
        if ($order->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403, 'No tienes autorización para ver esta orden.');
        }
    }
}
