<x-layouts::app.header>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-zinc-100">Mis Pedidos</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Supervisa y consulta el historial de tus compras y órdenes.</p>
            </div>
            <a href="{{ route('products.index') }}" wire:navigate>
                <flux:button variant="primary" icon="shopping-bag" class="shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none !bg-primary text-white hover:!bg-secondary transition-all">
                    Nueva Compra
                </flux:button>
            </a>
        </div>

        @php
            $totalOrders = $pendientes + $recibido + $enviado + $entregado + $anulado;
            $currentStatus = request('status');
        @endphp

        <!-- Status Filter Cards -->
        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Todos -->
            <a href="{{ route('orders.index') }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ !request()->filled('status') ? 'ring-2 ring-primary border-primary bg-primary/5 dark:bg-zinc-800 dark:ring-secondary dark:border-secondary' : 'border-zinc-200 dark:border-zinc-800 hover:border-primary/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Todos</span>
                    <div class="w-8 h-8 rounded-full bg-ink/10 text-ink dark:bg-zinc-800 dark:text-zinc-300 flex items-center justify-center">
                        <flux:icon.bars-3 class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $totalOrders }}</div>
            </a>

            <!-- Pendiente -->
            <a href="{{ route('orders.index', ['status' => 1]) }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ $currentStatus === '1' ? 'ring-2 ring-primary border-primary bg-primary/5 dark:bg-zinc-800 dark:ring-secondary dark:border-secondary' : 'border-zinc-200 dark:border-zinc-800 hover:border-primary/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Pendiente</span>
                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary dark:bg-primary/20 dark:text-secondary flex items-center justify-center">
                        <flux:icon.clock class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $pendientes }}</div>
            </a>

            <!-- Recibido / Pagado -->
            <a href="{{ route('orders.index', ['status' => 2]) }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ $currentStatus === '2' ? 'ring-2 ring-secondary border-secondary bg-secondary/5 dark:bg-zinc-800 dark:ring-secondary dark:border-secondary' : 'border-zinc-200 dark:border-zinc-800 hover:border-secondary/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Recibido</span>
                    <div class="w-8 h-8 rounded-full bg-secondary/10 text-secondary dark:bg-secondary/20 flex items-center justify-center">
                        <flux:icon.credit-card class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $recibido }}</div>
            </a>

            <!-- Enviados -->
            <a href="{{ route('orders.index', ['status' => 4]) }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ $currentStatus === '4' ? 'ring-2 ring-premium border-premium bg-premium/5 dark:bg-zinc-800 dark:ring-premium dark:border-premium' : 'border-zinc-200 dark:border-zinc-800 hover:border-premium/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Enviados</span>
                    <div class="w-8 h-8 rounded-full bg-premium/10 text-premium dark:bg-premium/20 flex items-center justify-center">
                        <flux:icon.truck class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $enviado }}</div>
            </a>

            <!-- Entregado -->
            <a href="{{ route('orders.index', ['status' => 5]) }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ $currentStatus === '5' ? 'ring-2 ring-ink border-ink bg-ink/5 dark:bg-zinc-800 dark:ring-zinc-400 dark:border-zinc-400' : 'border-zinc-200 dark:border-zinc-800 hover:border-ink/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Entregado</span>
                    <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 flex items-center justify-center">
                        <flux:icon.check-circle class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $entregado }}</div>
            </a>

            <!-- Anulado -->
            <a href="{{ route('orders.index', ['status' => 6]) }}" wire:navigate
                class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border transition-all duration-200 flex flex-col justify-between shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5 {{ $currentStatus === '6' ? 'ring-2 ring-danger border-danger bg-danger/5 dark:bg-zinc-800 dark:ring-danger dark:border-danger' : 'border-zinc-200 dark:border-zinc-800 hover:border-danger/50' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Anulado</span>
                    <div class="w-8 h-8 rounded-full bg-danger/10 text-danger dark:bg-danger/20 flex items-center justify-center">
                        <flux:icon.x-circle class="size-4" />
                    </div>
                </div>
                <div class="mt-4 text-2xl font-bold text-ink dark:text-zinc-100">{{ $anulado }}</div>
            </a>
        </section>

        <!-- Orders List -->
        <section class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-ink dark:text-zinc-100">Historial de Órdenes</h2>
                    @if (request()->filled('status'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200 border border-primary/20 dark:border-zinc-700">
                            Filtro activo
                            <a href="{{ route('orders.index') }}" class="hover:text-danger ml-0.5" title="Limpiar filtro">✕</a>
                        </span>
                    @endif
                </div>
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                    {{ $orders->total() }} {{ $orders->total() === 1 ? 'pedido' : 'pedidos' }} en total
                </span>
            </div>

            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" wire:navigate
                        class="group p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border
                                @switch($order->status)
                                    @case(1) bg-primary/10 text-primary border-primary/20 dark:bg-primary/20 dark:border-primary/30 @break
                                    @case(2) @case(3) bg-secondary/10 text-secondary border-secondary/20 dark:bg-secondary/20 dark:border-secondary/30 @break
                                    @case(4) bg-premium/10 text-premium border-premium/20 dark:bg-premium/20 dark:border-premium/30 @break
                                    @case(5) bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200 dark:border-zinc-700 @break
                                    @case(6) bg-danger/10 text-danger border-danger/20 dark:bg-danger/20 dark:border-danger/30 @break
                                    @default bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-200 dark:border-zinc-700
                                @endswitch
                            ">
                                @switch($order->status)
                                    @case(1) <flux:icon.clock class="size-5" /> @break
                                    @case(2) @case(3) <flux:icon.credit-card class="size-5" /> @break
                                    @case(4) <flux:icon.truck class="size-5" /> @break
                                    @case(5) <flux:icon.check-circle class="size-5" /> @break
                                    @case(6) <flux:icon.x-circle class="size-5" /> @break
                                    @default <flux:icon.shopping-bag class="size-5" />
                                @endswitch
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-ink dark:text-zinc-100 text-base group-hover:text-primary dark:group-hover:text-zinc-50 transition-colors">Orden #{{ $order->public_order_number }}</span>
                                    <span class="text-xs text-zinc-400">• {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $order->items_count ?? $order->items->count() }} productos • Total Puntos: <span class="font-semibold text-premium">{{ formatear_precio($order->total_pts) }} pts</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold border
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

                            <div class="text-right">
                                <span class="text-lg font-black text-primary dark:text-secondary">${{ formatear_precio($order->total) }}</span>
                            </div>

                            <flux:icon.chevron-right class="size-5 text-zinc-400 group-hover:text-primary dark:group-hover:text-zinc-100 transition-colors shrink-0" />
                        </div>
                    </a>
                @empty
                    <div class="py-16 text-center">
                        <flux:icon.inbox class="size-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-3" />
                        <h3 class="text-base font-bold text-ink dark:text-zinc-200">No se encontraron pedidos</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Aún no has registrado compras con este filtro.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if ($orders->hasPages())
                <div class="p-4 sm:p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                    {{ $orders->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts::app.header>
