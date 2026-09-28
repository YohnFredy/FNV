<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-6 gap-8">
        <!-- Products Column -->
        <div class="col-span-1 lg:col-span-4">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800 mb-4">
                <h1 class="text-2xl font-bold text-ink dark:text-zinc-100">
                    Carrito de Compras
                </h1>
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    {{ $totals['quantity'] }} {{ $totals['quantity'] == 1 ? 'producto' : 'productos' }}
                </span>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 overflow-hidden divide-y divide-zinc-200/80 dark:divide-zinc-800">
                @if (count($products) > 0)
                    @foreach ($products as $product)
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all duration-200 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                            <!-- Image & Details -->
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-20 h-20 shrink-0 bg-zinc-100/90 dark:bg-zinc-800 rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shadow-inner">
                                    @if (!empty($product['path']))
                                        <img class="w-full h-full object-contain p-1.5 hover:scale-105 transition-transform"
                                            src="{{ asset('storage/' . $product['path']) }}" alt="{{ $product['name'] }}">
                                    @else
                                        <flux:icon.photo class="size-8 text-zinc-400" />
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 space-y-1">
                                    <h3 class="font-bold text-ink dark:text-zinc-100 hover:text-primary transition-colors text-base">
                                        {{ $product['name'] }}
                                    </h3>
                                    <p class="text-xs text-ink/65 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                        {!! Str::limit(strip_tags($product['description']), 90) !!}
                                    </p>
                                    <div class="mt-2 flex items-center gap-3">
                                        <span class="text-base sm:text-lg font-black text-primary tracking-tight">
                                            ${{ format_price_with_tax($product['price'], $product['tax_percent']) }}
                                        </span>
                                        @if ($product['pts_base'] > 0)
                                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-premium/15 text-premium border border-premium/25 shadow-xs">
                                                ⭐ {{ $product['pts_base'] }} pts
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Controls & Delete -->
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                <div class="flex items-center border border-zinc-300 dark:border-zinc-700 rounded-xl overflow-hidden bg-white dark:bg-zinc-800 shadow-sm">
                                    <button wire:click="decrement({{ $product['index'] }})"
                                        type="button"
                                        class="px-3.5 py-1.5 text-ink/70 dark:text-zinc-300 hover:bg-primary/10 hover:text-primary dark:hover:bg-zinc-700 transition-colors font-extrabold cursor-pointer">
                                        -
                                    </button>
                                    <input type="text"
                                        class="w-12 text-center text-sm font-extrabold bg-transparent border-0 focus:ring-0 text-ink dark:text-zinc-100 p-0"
                                        wire:model.live="products.{{ $product['index'] }}.quantity"
                                        value="{{ $product['quantity'] }}">
                                    <button wire:click="increment({{ $product['index'] }})"
                                        type="button"
                                        class="px-3.5 py-1.5 text-ink/70 dark:text-zinc-300 hover:bg-primary/10 hover:text-primary dark:hover:bg-zinc-700 transition-colors font-extrabold cursor-pointer">
                                        +
                                    </button>
                                </div>

                                <button wire:click="removeFromCart({{ $product['index'] }})"
                                    type="button"
                                    class="p-2.5 text-danger hover:bg-danger/10 rounded-xl transition-all cursor-pointer hover:scale-105"
                                    title="Eliminar del carrito">
                                    <flux:icon.trash class="size-5" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-16 text-center space-y-3">
                        <div class="size-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto">
                            <flux:icon.shopping-cart class="size-8" />
                        </div>
                        <h3 class="text-lg font-bold text-ink dark:text-zinc-200">Tu carrito está vacío</h3>
                        <p class="text-sm text-ink/60 dark:text-zinc-400 max-w-sm mx-auto">
                            Explora nuestro catálogo y descubre una amplia variedad de productos para ti.
                        </p>
                        <div class="pt-4">
                            <a href="{{ route('products.index') }}">
                                <flux:button variant="primary" class="!bg-primary hover:!bg-secondary text-white! font-bold shadow-md shadow-ink/70">
                                    Ver Productos
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Column -->
        <div class="col-span-1 lg:col-span-2">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 overflow-hidden sticky top-24">
                <!-- Header -->
                <div class="p-6 border-b border-zinc-200/80 dark:border-zinc-800">
                    <h2 class="text-lg font-extrabold text-ink dark:text-zinc-100 flex items-center gap-2">
                        <flux:icon.receipt-percent class="size-5 text-primary" />
                        Resumen de la Orden
                    </h2>
                    <p class="text-xs text-ink/60 dark:text-zinc-400 mt-0.5">Precios e impuestos calculados</p>
                </div>

                <!-- Details -->
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                        <span class="font-medium">Subtotal</span>
                        <span class="font-semibold text-ink dark:text-zinc-100">
                            ${{ formatear_precio($totals['subtotal']) }}
                        </span>
                    </div>

                    @if ($totals['descuento'] > 0)
                        <div class="flex justify-between items-center text-premium font-bold bg-premium/5 p-2 rounded-lg border border-premium/15">
                            <span>Descuento aplicado</span>
                            <span>
                                -${{ formatear_precio($totals['descuento']) }}
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                        <span class="font-medium">Total Bruto</span>
                        <span class="font-semibold text-ink dark:text-zinc-100">
                            ${{ formatear_precio($totals['total_bruto_factura']) }}
                        </span>
                    </div>

                    @if ($totals['iva'] > 0)
                        <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                            <span class="font-medium">IVA</span>
                            <span class="font-semibold text-ink dark:text-zinc-100">
                                ${{ formatear_precio($totals['iva']) }}
                            </span>
                        </div>
                    @endif

                    @if (isset($totals['total_pts']) && $totals['total_pts'] > 0)
                        <div class="py-2.5 px-3.5 rounded-xl bg-premium/15 border border-premium/30 flex items-center justify-between shadow-xs">
                            <span class="text-xs font-bold text-premium">Puntos acumulados</span>
                            <span class="text-sm font-extrabold text-premium">⭐ {{ formatear_precio($totals['total_pts']) }} pts</span>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-baseline">
                        <span class="text-base font-bold text-ink dark:text-zinc-100">Total a Pagar</span>
                        <span class="text-2xl sm:text-3xl font-black text-primary tracking-tight">
                            ${{ formatear_precio($totals['total_factura']) }}
                        </span>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="p-6 bg-zinc-50/80 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                    @if ($totals['quantity'] < 1)
                        <flux:button variant="primary" disabled class="w-full justify-center font-bold">
                            Carrito Vacío
                        </flux:button>
                    @else
                        <a href="{{ route('orders.create') }}" class="block">
                            <flux:button variant="primary" class="w-full justify-center text-base py-3 font-bold !bg-primary hover:!bg-secondary text-white! border-none shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-200 rounded-xl">
                                Continuar con la Compra
                                <flux:icon.arrow-right class="size-4 ml-2" />
                            </flux:button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
