<x-layouts::app.header>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Header Reference Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-7 shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink dark:text-zinc-100 flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-primary/10 text-primary">
                        <flux:icon.credit-card class="size-7" />
                    </span>
                    Confirmación y Pago de la Orden
                </h1>
                <p class="text-xs sm:text-sm text-ink/65 dark:text-zinc-400 mt-1">Revisa el resumen antes de proceder con el pago seguro.</p>
            </div>
            <div class="flex items-center gap-2.5 bg-primary/10 border border-primary/20 px-4 py-2.5 rounded-xl shadow-xs">
                <span class="text-xs uppercase font-bold text-ink/70 dark:text-zinc-400">Referencia:</span>
                <span class="text-base font-mono font-black text-primary tracking-wide">{{ $order->public_order_number }}</span>
            </div>
        </div>

        <!-- Información Importante sobre el Envío -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-secondary/30 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none flex items-start gap-4 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary via-secondary to-primary"></div>
            <div class="p-3 rounded-xl bg-secondary/15 text-secondary dark:bg-zinc-800 dark:text-zinc-200 shrink-0">
                <flux:icon.truck class="size-6" />
            </div>
            <div class="space-y-1 text-sm">
                <h3 class="font-extrabold text-base text-danger dark:text-zinc-100 flex items-center gap-2">
                    {{ __('Información importante sobre el envío') }}
                </h3>
                <p class="text-ink/80 dark:text-zinc-300 leading-relaxed font-medium">
                    El costo del envío se pagará <strong>contra entrega</strong> directamente al repartidor.
                </p>
                <p class="text-xs sm:text-sm text-ink/70 dark:text-zinc-400">
                    Solo necesitas pagar ahora el valor del producto a través de nuestra pasarela segura o por transferencia bancaria.
                </p>
                <p class="text-xs text-ink/60 dark:text-zinc-400 pt-1">
                    Para cualquier consulta, escríbenos al WhatsApp <a href="mailto:info@fornuvi.com" class="font-semibold text-primary hover:text-secondary underline">3145207814</a>
                </p>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Facturación -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-7 shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none space-y-4">
                <div class="flex items-center gap-3 pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="p-2.5 bg-primary/10 rounded-xl text-primary">
                        <flux:icon.document-text class="size-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-ink dark:text-zinc-100">Datos de Facturación</h2>
                        <p class="text-xs text-ink/60 dark:text-zinc-400">Comprador registrado</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-sm text-ink/80 dark:text-zinc-300">
                    <p><strong class="text-ink dark:text-zinc-100 font-bold">Nombre / Razón Social:</strong> {{ $order->billingData->name ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100 font-bold">{{ $order->billingData->documentType?->name ?? 'Documento' }}:</strong> {{ $order->billingData->document ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100 font-bold">Email:</strong> {{ $order->billingData->email ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100 font-bold">Teléfono:</strong> {{ $order->billingData->phone ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100 font-bold">Dirección:</strong>
                        {{ $order->billingData->address ?? 'N/A' }},
                        {{ $order->billingData->city?->name ?? $order->billingData->addCity }},
                        {{ $order->billingData->department?->name }},
                        {{ $order->billingData->country?->name }}
                    </p>
                </div>
            </div>

            <!-- Envío -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-7 shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none space-y-4">
                <div class="flex items-center gap-3 pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="p-2.5 bg-premium/15 rounded-xl text-premium">
                        <flux:icon.truck class="size-6" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-ink dark:text-zinc-100">Detalles de Entrega</h2>
                        <p class="text-xs text-ink/60 dark:text-zinc-400">
                            {{ $order->shipping_type == 1 ? 'Recogida en tienda' : 'Entrega a domicilio' }}
                        </p>
                    </div>
                </div>

                @if ($order->shipping_type == 1)
                    <div class="space-y-2 text-sm text-ink/80 dark:text-zinc-300">
                        <p class="font-bold text-ink dark:text-zinc-100">Punto de Entrega Principal:</p>
                        <p>Sede Principal Fornuvi / Multinivel</p>
                    </div>
                @else
                    <div class="space-y-2.5 text-sm text-ink/80 dark:text-zinc-300">
                        <p><strong class="text-ink dark:text-zinc-100 font-bold">Destinatario:</strong> {{ $order->shipping_name ?? $order->billingData?->name ?? 'Mismo comprador' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100 font-bold">Documento:</strong> {{ $order->shipping_document ?? $order->billingData?->document ?? 'N/A' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100 font-bold">Teléfono:</strong> {{ $order->shipping_phone ?? $order->billingData?->phone ?? 'N/A' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100 font-bold">Dirección:</strong>
                            {{ $order->shipping_address ?? $order->billingData?->address }},
                            {{ $order->shipping_additional_address }}
                            {{ $order->shippingCity?->name ?? $order->shipping_addCity }},
                            {{ $order->shippingDepartment?->name }},
                            {{ $order->shippingCountry?->name }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none overflow-hidden">
            <div class="p-6 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100 flex items-center gap-2">
                    <flux:icon.cube class="size-5 text-primary" />
                    Productos del Pedido
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-ink/80 dark:text-zinc-300">
                    <thead class="text-xs uppercase bg-zinc-50/80 dark:bg-zinc-800/60 text-ink/80 dark:text-zinc-200 border-b border-zinc-200/80 dark:border-zinc-700">
                        <tr>
                            <th class="px-6 py-3.5">Producto</th>
                            <th class="px-4 py-3.5 text-center">Cant</th>
                            <th class="px-4 py-3.5 text-right">Precio Unit.</th>
                            <th class="px-4 py-3.5 text-center">Pts</th>
                            <th class="px-4 py-3.5 text-right">Descuento</th>
                            <th class="px-4 py-3.5 text-right">IVA</th>
                            <th class="px-4 py-3.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/80 dark:divide-zinc-800">
                        @foreach ($order->items as $item)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shrink-0 flex items-center justify-center shadow-inner">
                                        @if ($item->product?->latestImage)
                                            <img src="{{ asset('storage/' . $item->product->latestImage->path) }}"
                                                alt="{{ $item->name }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <flux:icon.photo class="size-5 text-zinc-400" />
                                        @endif
                                    </div>
                                    <span class="font-bold text-ink dark:text-zinc-100">{{ $item->name }}</span>
                                </td>
                                <td class="px-4 py-4 text-center font-extrabold text-ink dark:text-zinc-100">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-right font-medium">${{ formatear_precio($item->unit_price) }}</td>
                                <td class="px-4 py-4 text-center text-premium font-bold">⭐ {{ formatear_precio($item->pts) }}</td>
                                <td class="px-4 py-4 text-right text-premium font-bold">-${{ formatear_precio($item->discount) }}</td>
                                <td class="px-4 py-4 text-right font-medium">${{ formatear_precio($item->tax_amount) }}</td>
                                <td class="px-4 py-4 text-right font-black text-ink dark:text-zinc-100">
                                    ${{ formatear_precio($item->unit_sales_price) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Bar -->
            <div class="p-6 bg-zinc-50/80 dark:bg-zinc-800/40 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row justify-end items-end gap-4">
                <div class="w-full sm:w-88 space-y-2.5 text-sm">
                    <div class="flex justify-between text-ink/70 dark:text-zinc-400">
                        <span class="font-medium">Subtotal:</span>
                        <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($order->subtotal) }}</span>
                    </div>
                    @if ($order->discount > 0)
                        <div class="flex justify-between text-premium font-bold bg-premium/5 p-2 rounded-lg border border-premium/15">
                            <span>Descuento:</span>
                            <span>-${{ formatear_precio($order->discount) }}</span>
                        </div>
                    @endif
                    @if ($order->tax_amount > 0)
                        <div class="flex justify-between text-ink/70 dark:text-zinc-400">
                            <span class="font-medium">IVA:</span>
                            <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($order->tax_amount) }}</span>
                        </div>
                    @endif
                    @if ($order->shipping_cost > 0)
                        <div class="flex justify-between text-ink/70 dark:text-zinc-400">
                            <span class="font-medium">Envío:</span>
                            <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($order->shipping_cost) }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-baseline">
                        <span class="text-base font-bold text-ink dark:text-zinc-100">Total a Pagar:</span>
                        <span class="text-2xl sm:text-3xl font-black text-primary tracking-tight">${{ formatear_precio($order->total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- 1. Opción de Transferencia Bancaria (Primero) -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-8 shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="p-2 rounded-xl bg-primary/10 text-primary">
                            <flux:icon.building-library class="size-6" />
                        </span>
                        <div>
                            <h3 class="text-lg font-extrabold text-ink dark:text-zinc-100">
                                Pago por Transferencia Bancaria
                            </h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary">
                                <flux:icon.check-circle class="size-3.5" />
                                La orden ha sido generada exitosamente.
                            </span>
                        </div>
                    </div>

                    <div class="text-xs sm:text-sm text-ink/80 dark:text-zinc-300 space-y-2 leading-relaxed">
                        <p class="font-semibold text-ink dark:text-zinc-100">
                            Opciones de pago disponibles:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-ink/70 dark:text-zinc-400 pl-1">
                            <li>Pasarela de pagos en línea (ver opción continua).</li>
                            <li>Transferencia bancaria directa.</li>
                        </ul>
                        <p class="pt-1">
                            Si prefieres realizar una transferencia bancaria, puedes hacerlo a la siguiente cuenta:
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700 text-xs sm:text-sm space-y-2 font-medium">
                        <p><strong class="text-ink dark:text-zinc-300">Entidad:</strong> Bancolombia</p>
                        <p><strong class="text-ink dark:text-zinc-300">Tipo de cuenta:</strong> Ahorros</p>
                        <p><strong class="text-ink dark:text-zinc-300">Número de cuenta:</strong> <span class="font-mono font-black text-primary text-base">808-000157-69</span></p>
                        <p><strong class="text-ink dark:text-zinc-300">Titular:</strong> FORNUVI S.A.S.</p>
                        <p><strong class="text-ink dark:text-zinc-300">NIT:</strong> 901953881</p>
                    </div>

                    <p class="text-xs text-ink/75 dark:text-zinc-400 leading-relaxed pt-1">
                        Una vez realices el pago, por favor envía el comprobante o pantallazo al siguiente número de WhatsApp:
                        <a href="https://wa.me/573145207814?text=Hola,%20adjunto%20comprobante%20de%20pago%20de%20la%20orden%20{{ $order->public_order_number }}" target="_blank" class="font-extrabold text-primary hover:text-secondary inline-flex items-center gap-1 underline">
                            <i class="fab fa-whatsapp text-sm text-green-600"></i> (+57) 314 520 7814
                        </a>. Estaremos atentos para validar y procesar tu pedido.
                    </p>
                </div>

                <div class="pt-3">
                    <a href="https://wa.me/573145207814?text=Hola,%20adjunto%20comprobante%20de%20pago%20de%20la%20orden%20{{ $order->public_order_number }}" target="_blank" class="w-full">
                        <flux:button variant="outline" class="w-full justify-center !border-primary/40 !text-primary hover:!bg-primary/10 font-bold py-2.5 rounded-xl">
                            <i class="fab fa-whatsapp mr-2 text-base text-green-600"></i> Notificar Pago por WhatsApp
                        </flux:button>
                    </a>
                </div>
            </div>

            <!-- 2. Pasarela Bold (Segundo) -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-8 shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 dark:shadow-none flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="p-2 rounded-xl bg-secondary/15 text-secondary">
                            <flux:icon.lock-closed class="size-6" />
                        </span>
                        <div>
                            <h3 class="text-lg font-extrabold text-ink dark:text-zinc-100">
                                Pago en Línea con Bold
                            </h3>
                            <p class="text-xs text-ink/60 dark:text-zinc-400">Pasarela de pagos en línea</p>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-ink/70 dark:text-zinc-400 leading-relaxed">
                        Paga de manera 100% segura usando <strong>Tarjetas de Crédito, Débito, PSE, Nequi y Daviplata</strong> a través de la pasarela oficial de Bold.
                    </p>

                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700 space-y-2 text-xs text-ink/70 dark:text-zinc-400">
                        <div class="flex items-center gap-2 text-ink dark:text-zinc-200 font-semibold">
                            <flux:icon.shield-check class="size-4 text-primary" />
                            <span>Transacción cifrada y protegida</span>
                        </div>
                        <p>La aprobación del pedido es instantánea al pagar con Bold.</p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <x-button-dynamic id="custom-button-payment" class="w-full justify-center text-base py-3.5 font-bold !bg-primary hover:!bg-secondary text-white! border-none shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-200 rounded-xl cursor-pointer">
                        🔒 Pagar con Bold (${{ formatear_precio($order->total) }})
                    </x-button-dynamic>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const initBoldCheckout = () => {
                if (document.querySelector('script[src="https://checkout.bold.co/library/boldPaymentButton.js"]')) {
                    return;
                }

                const js = document.createElement('script');
                js.src = 'https://checkout.bold.co/library/boldPaymentButton.js';
                js.onload = () => {
                    window.dispatchEvent(new Event('boldCheckoutLoaded'));
                };
                document.head.appendChild(js);
            };

            initBoldCheckout();

            window.addEventListener('boldCheckoutLoaded', function() {
                try {
                    const checkout = new BoldCheckout({
                        orderId: "{{ $boldCheckoutConfig['orderId'] }}",
                        currency: "{{ $boldCheckoutConfig['currency'] }}",
                        amount: "{{ $boldCheckoutConfig['amount'] }}",
                        apiKey: "{{ $boldCheckoutConfig['apiKey'] }}",
                        integritySignature: "{{ $boldCheckoutConfig['integritySignature'] }}",
                        description: "{{ $boldCheckoutConfig['description'] }}",
                        tax: "{{ $boldCheckoutConfig['tax'] }}",
                        redirectionUrl: "{{ $boldCheckoutConfig['redirectionUrl'] }}",
                        expirationDate: "{{ $boldCheckoutConfig['expiration-date'] }}",
                    });

                    const customButton = document.getElementById('custom-button-payment');
                    if (customButton) {
                        customButton.addEventListener('click', function() {
                            checkout.open();
                        });
                    }
                } catch (e) {
                    console.error("Error al inicializar Bold Checkout", e);
                }
            });
        });
    </script>
</x-layouts::app.header>
