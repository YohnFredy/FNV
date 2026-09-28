<x-layouts::app.header>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Progress Steps Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 sm:p-8 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Paso 1: Recibido -->
                <div class="flex flex-col items-center text-center flex-1">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                        {{ $order->status >= 2 && $order->status < 6 ? 'bg-primary shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                        <flux:icon.check class="size-6 stroke-2" />
                    </div>
                    <span class="text-sm font-semibold mt-2 text-ink dark:text-zinc-200">Pago Recibido</span>
                </div>

                <div class="w-full sm:w-auto flex-1 h-1 {{ $order->status >= 4 && $order->status < 6 ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

                <!-- Paso 2: Enviado -->
                <div class="flex flex-col items-center text-center flex-1">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                        {{ $order->status >= 4 && $order->status < 6 ? 'bg-primary shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                        <flux:icon.truck class="size-6" />
                    </div>
                    <span class="text-sm font-semibold mt-2 text-ink dark:text-zinc-200">Enviado</span>
                </div>

                <div class="w-full sm:w-auto flex-1 h-1 {{ $order->status >= 5 && $order->status < 6 ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

                <!-- Paso 3: Entregado -->
                <div class="flex flex-col items-center text-center flex-1">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                        {{ $order->status >= 5 && $order->status < 6 ? 'bg-primary shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                        <flux:icon.check-circle class="size-6" />
                    </div>
                    <span class="text-sm font-semibold mt-2 text-ink dark:text-zinc-200">Entregado</span>
                </div>
            </div>

            @if ($order->status >= 6)
                <div class="mt-6 p-3.5 rounded-xl bg-danger/10 border border-danger/25 text-danger flex items-center justify-center gap-2.5 text-sm font-semibold">
                    <flux:icon.x-circle class="size-5 shrink-0" />
                    <span>Esta orden fue cancelada o rechazada.</span>
                </div>
            @endif
        </div>

        <!-- Reference & Status Bar -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="text-xs uppercase font-bold text-zinc-400">Detalles de Orden</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border
                        @switch($order->status)
                            @case(1) bg-primary/10 text-primary border-primary/25 dark:bg-primary/20 dark:border-primary/30 @break
                            @case(2) @case(3) bg-secondary/10 text-secondary border-secondary/25 dark:bg-secondary/20 dark:border-secondary/30 @break
                            @case(4) bg-premium/10 text-premium border-premium/25 dark:bg-premium/20 dark:border-premium/30 @break
                            @case(5) bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border-zinc-300 dark:border-zinc-700 @break
                            @case(6) bg-danger/10 text-danger border-danger/25 dark:bg-danger/20 dark:border-danger/30 @break
                            @default bg-zinc-100 text-zinc-600 border-zinc-300 dark:border-zinc-700
                        @endswitch
                    ">
                        @switch($order->status)
                            @case(1) Pendiente de Pago @break
                            @case(2) Pago Aprobado @break
                            @case(3) Puntos Generados @break
                            @case(4) En Camino / Enviado @break
                            @case(5) Entregado @break
                            @case(6) Cancelado / Rechazado @break
                            @default Estado {{ $order->status }}
                        @endswitch
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-ink dark:text-zinc-100 font-mono">#{{ $order->public_order_number }}</h1>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                @if ($order->status === 1)
                    <a href="{{ route('bold.checkout', $order) }}">
                        <flux:button variant="primary" icon="credit-card" class="shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none !bg-primary text-white hover:!bg-secondary transition-all">
                            Continuar al Pago
                        </flux:button>
                    </a>
                @endif
                <a href="{{ route('orders.index') }}" wire:navigate>
                    <flux:button variant="ghost" icon="arrow-left" class="text-ink dark:text-zinc-300 hover:text-primary dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Volver a Mis Pedidos
                    </flux:button>
                </a>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Facturación -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
                <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <flux:icon.document-text class="size-5" />
                    </div>
                    <h2 class="font-bold text-ink dark:text-zinc-100">Datos de Facturación</h2>
                </div>
                <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <p><strong class="text-ink dark:text-zinc-100">Nombre / Razón Social:</strong> {{ $order->billingData->name ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">{{ $order->billingData->documentType?->name ?? 'Documento' }}:</strong> {{ $order->billingData->document ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Email:</strong> {{ $order->billingData->email ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Teléfono:</strong> {{ $order->billingData->phone ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Dirección:</strong>
                        {{ $order->billingData->address ?? 'N/A' }},
                        {{ $order->billingData->city?->name ?? $order->billingData->addCity }},
                        {{ $order->billingData->department?->name }},
                        {{ $order->billingData->country?->name }}
                    </p>
                </div>
            </div>

            <!-- Envío -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
                <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center">
                        <flux:icon.map-pin class="size-5" />
                    </div>
                    <h2 class="font-bold text-ink dark:text-zinc-100">Dirección de Entrega</h2>
                </div>
                @if ($order->shipping_type == 1)
                    <div class="text-sm text-zinc-600 dark:text-zinc-300">
                        <p class="font-semibold text-ink dark:text-zinc-100">Recogida en tienda principal</p>
                    </div>
                @else
                    <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                        <p><strong class="text-ink dark:text-zinc-100">Destinatario:</strong> {{ $order->shipping_name ?? $order->billingData?->name ?? 'Mismo comprador' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100">Documento:</strong> {{ $order->shipping_document ?? $order->billingData?->document ?? 'N/A' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100">Teléfono:</strong> {{ $order->shipping_phone ?? $order->billingData?->phone ?? 'N/A' }}</p>
                        <p><strong class="text-ink dark:text-zinc-100">Dirección:</strong>
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

        <!-- Products Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between flex-wrap gap-2">
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100">Productos ({{ $order->items->count() }})</h2>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-premium/10 text-premium border border-premium/25 dark:bg-premium/20">
                    Total Puntos: {{ formatear_precio($order->total_pts) }} pts
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-300">
                    <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-800/70 text-ink dark:text-zinc-200 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-6 py-3 font-bold">Producto</th>
                            <th class="px-4 py-3 text-center font-bold">Cant</th>
                            <th class="px-4 py-3 text-right font-bold">Precio Unit.</th>
                            <th class="px-4 py-3 text-center font-bold">Pts</th>
                            <th class="px-4 py-3 text-right font-bold">Descuento</th>
                            <th class="px-4 py-3 text-right font-bold">IVA</th>
                            <th class="px-4 py-3 text-right font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($order->items as $item)
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shrink-0 flex items-center justify-center">
                                        @if ($item->product?->latestImage)
                                            <img src="{{ asset('storage/' . $item->product->latestImage->path) }}"
                                                alt="{{ $item->name }}" class="w-full h-full object-contain p-0.5">
                                        @else
                                            <flux:icon.photo class="size-5 text-zinc-400" />
                                        @endif
                                    </div>
                                    <span class="font-semibold text-ink dark:text-zinc-100">{{ $item->name }}</span>
                                </td>
                                <td class="px-4 py-4 text-center font-medium text-ink dark:text-zinc-200">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-right font-mono">${{ formatear_precio($item->unit_price) }}</td>
                                <td class="px-4 py-4 text-center text-premium font-semibold font-mono">{{ formatear_precio($item->pts) }}</td>
                                <td class="px-4 py-4 text-right text-premium font-semibold font-mono">-${{ formatear_precio($item->discount) }}</td>
                                <td class="px-4 py-4 text-right font-mono">${{ formatear_precio($item->tax_amount) }}</td>
                                <td class="px-4 py-4 text-right font-bold text-ink dark:text-zinc-100 font-mono">
                                    ${{ formatear_precio($item->unit_sales_price) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Totals -->
            <div class="p-6 bg-zinc-50/80 dark:bg-zinc-800/40 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                <div class="w-full sm:w-80 space-y-2 text-sm">
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>Subtotal:</span>
                        <span class="font-medium text-ink dark:text-zinc-100 font-mono">${{ formatear_precio($order->subtotal) }}</span>
                    </div>
                    @if ($order->discount > 0)
                        <div class="flex justify-between text-premium">
                            <span>Descuento:</span>
                            <span class="font-semibold font-mono">-${{ formatear_precio($order->discount) }}</span>
                        </div>
                    @endif
                    @if ($order->tax_amount > 0)
                        <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                            <span>IVA:</span>
                            <span class="font-medium text-ink dark:text-zinc-100 font-mono">${{ formatear_precio($order->tax_amount) }}</span>
                        </div>
                    @endif
                    @if ($order->shipping_cost > 0)
                        <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                            <span>Envío:</span>
                            <span class="font-medium text-ink dark:text-zinc-100 font-mono">${{ formatear_precio($order->shipping_cost) }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-baseline">
                        <span class="text-base font-bold text-ink dark:text-zinc-100">Total:</span>
                        <span class="text-2xl font-black text-primary dark:text-secondary font-mono">${{ formatear_precio($order->total) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.header>
