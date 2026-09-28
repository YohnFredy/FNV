<div class="space-y-6">
    <!-- Migas de Pan -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ __('Gestión de Pedidos') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Alertas de Sesión -->
    @if (session('success'))
        <div class="p-4 rounded-xl bg-primary/10 border border-primary/30 text-primary dark:text-zinc-100 flex items-center justify-between shadow-md shadow-ink/70 dark:shadow-none">
            <div class="flex items-center gap-3">
                <flux:icon.check-circle class="size-5 text-primary dark:text-zinc-200 shrink-0" />
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-danger/10 border border-danger/30 text-danger dark:text-zinc-100 flex items-center justify-between shadow-md shadow-ink/70 dark:shadow-none">
            <div class="flex items-center gap-3">
                <flux:icon.exclamation-triangle class="size-5 text-danger dark:text-zinc-300 shrink-0" />
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Encabezado Principal -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">{{ __('Control de Pedidos y Puntos') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Monitorea, aprueba pedidos y propaga puntos ascendentes en la red binaria y escalonada.') }}</p>
        </div>
    </div>

    <!-- Tarjetas de Métricas de Estados -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Pendientes -->
        <button
            wire:click="$set('statusFilter', '1')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === '1'
                    ? 'border-primary bg-primary/5 dark:bg-zinc-800/80 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-primary/20'
                    : 'border-zinc-200 bg-white hover:border-primary/40 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Pendientes</span>
                <span class="size-2 rounded-full bg-primary animate-pulse"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-ink dark:text-zinc-100 font-mono">
                {{ $this->counts['pending'] }}
            </div>
        </button>

        <!-- Aprobados -->
        <button
            wire:click="$set('statusFilter', '2')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === '2'
                    ? 'border-secondary bg-secondary/5 dark:bg-zinc-800/80 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-secondary/20'
                    : 'border-zinc-200 bg-white hover:border-secondary/40 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Aprobados</span>
                <flux:icon.check class="size-3.5 text-secondary dark:text-zinc-300" />
            </div>
            <div class="mt-2 text-2xl font-black text-secondary dark:text-zinc-100 font-mono">
                {{ $this->counts['approved'] }}
            </div>
        </button>

        <!-- Puntos Generados -->
        <button
            wire:click="$set('statusFilter', '3')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === '3'
                    ? 'border-premium bg-premium/5 dark:bg-zinc-800/80 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-premium/20'
                    : 'border-zinc-200 bg-white hover:border-premium/40 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Pts Subidos</span>
                <flux:icon.sparkles class="size-3.5 text-premium dark:text-zinc-300" />
            </div>
            <div class="mt-2 text-2xl font-black text-premium dark:text-zinc-100 font-mono">
                {{ $this->counts['pts_generated'] }}
            </div>
        </button>

        <!-- Enviados -->
        <button
            wire:click="$set('statusFilter', '4')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === '4'
                    ? 'border-primary bg-primary/5 dark:bg-zinc-800/80 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-primary/20'
                    : 'border-zinc-200 bg-white hover:border-primary/40 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Enviados</span>
                <flux:icon.truck class="size-3.5 text-primary dark:text-zinc-300" />
            </div>
            <div class="mt-2 text-2xl font-black text-ink dark:text-zinc-100 font-mono">
                {{ $this->counts['sent'] }}
            </div>
        </button>

        <!-- Entregados -->
        <button
            wire:click="$set('statusFilter', '5')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === '5'
                    ? 'border-zinc-700 bg-zinc-100 dark:bg-zinc-800 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-zinc-500/20'
                    : 'border-zinc-200 bg-white hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Entregados</span>
                <flux:icon.check-circle class="size-3.5 text-zinc-700 dark:text-zinc-300" />
            </div>
            <div class="mt-2 text-2xl font-black text-ink dark:text-zinc-100 font-mono">
                {{ $this->counts['delivered'] }}
            </div>
        </button>

        <!-- Total / Rechazados -->
        <button
            wire:click="$set('statusFilter', 'all')"
            class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between
                {{ $statusFilter === 'all'
                    ? 'border-zinc-900 bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 shadow-md shadow-ink/70 dark:shadow-none ring-2 ring-zinc-400/20'
                    : 'border-zinc-200 bg-white hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' }}"
        >
            <div class="flex items-center justify-between w-full">
                <span class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Todos</span>
                <flux:icon.shopping-cart class="size-3.5 text-zinc-600 dark:text-zinc-400" />
            </div>
            <div class="mt-2 text-2xl font-black text-ink dark:text-zinc-100 font-mono">
                {{ $this->counts['all'] }}
            </div>
        </button>
    </div>

    <!-- Contenedor Principal con Filtros y Tabla -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none overflow-hidden">
        <!-- Barra de Búsqueda y Filtros Rápidos -->
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Buscador -->
                <div class="flex-1 max-w-lg">
                    <x-search
                        wire:model="search"
                        wire:keydown.enter="searchEnter"
                        placeholder="Buscar por # pedido, cliente, email, cédula..."
                    />
                </div>

                <!-- Filtros secundarios -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filtro Tipo de Envío -->
                    <select
                        wire:model.live="shippingFilter"
                        class="px-3 py-2 text-sm rounded-xl border border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200 focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="all">Todos los Despachos</option>
                        <option value="1">Recogida en Tienda</option>
                        <option value="2">Envío a Domicilio</option>
                    </select>

                    <!-- Filtro por Estado Dropdown -->
                    <select
                        wire:model.live="statusFilter"
                        class="px-3 py-2 text-sm rounded-xl border border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200 focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="all">Todos los Estados ({{ $this->counts['all'] }})</option>
                        <option value="1">Pendientes de Pago ({{ $this->counts['pending'] }})</option>
                        <option value="2">Pagados / Aprobados ({{ $this->counts['approved'] }})</option>
                        <option value="3">Puntos Generados ({{ $this->counts['pts_generated'] }})</option>
                        <option value="4">Enviados ({{ $this->counts['sent'] }})</option>
                        <option value="5">Entregados ({{ $this->counts['delivered'] }})</option>
                        <option value="rejected">Rechazados / Cancelados ({{ $this->counts['rejected'] }})</option>
                    </select>

                    @if ($search || $statusFilter !== 'all' || $shippingFilter !== 'all' || $dateFrom || $dateTo)
                        <flux:button wire:click="clearSearch" size="sm" variant="ghost" icon="x-mark" class="text-zinc-500 hover:text-danger">
                            Limpiar Filtros
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de Pedidos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                        <th class="px-6 py-3.5">Pedido</th>
                        <th class="px-6 py-3.5">Cliente</th>
                        <th class="px-6 py-3.5">Fecha</th>
                        <th class="px-6 py-3.5">Despacho</th>
                        <th class="px-6 py-3.5 text-right">Total</th>
                        <th class="px-6 py-3.5 text-center">Puntos</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                            <!-- Pedido -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" wire:navigate class="group flex flex-col">
                                    <span class="font-mono font-bold text-ink dark:text-zinc-100 group-hover:text-primary transition-colors">
                                        #{{ $order->public_order_number }}
                                    </span>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'producto' : 'productos' }}
                                    </span>
                                </a>
                            </td>

                            <!-- Cliente -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="size-9 rounded-full bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-300 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ substr($order->user?->name ?? 'C', 0, 1) }}{{ substr($order->user?->last_name ?? '', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-ink dark:text-zinc-100 truncate max-w-[180px]">
                                            {{ $order->user?->name }} {{ $order->user?->last_name }}
                                        </p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">
                                            {{ '@' . ($order->user?->username ?? 'sin_usuario') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Fecha -->
                            <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-400 text-xs">
                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                <span class="text-[11px] text-zinc-400 dark:text-zinc-500">{{ $order->created_at->format('h:i A') }}</span>
                            </td>

                            <!-- Despacho -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if ($order->shipping_type === 1)
                                    <span class="inline-flex items-center gap-1 text-zinc-700 dark:text-zinc-300 font-medium">
                                        <flux:icon.building-storefront class="size-3.5 text-zinc-400" />
                                        Tienda
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-zinc-700 dark:text-zinc-300 font-medium">
                                        <flux:icon.truck class="size-3.5 text-secondary dark:text-zinc-400" />
                                        {{ $order->shippingCity?->name ?? 'Domicilio' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-ink dark:text-zinc-100">
                                ${{ formatear_precio($order->total) }}
                            </td>

                            <!-- Puntos -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold font-mono bg-premium/10 text-premium border border-premium/20 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                    {{ formatear_precio($order->total_pts) }} pts
                                </span>
                            </td>

                            <!-- Estado -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @switch($order->status)
                                    @case(1)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
                                            Pendiente
                                        </span>
                                        @break
                                    @case(2)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-secondary/10 text-secondary border border-secondary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                            <flux:icon.check class="size-3 text-secondary dark:text-zinc-300" />
                                            Aprobado
                                        </span>
                                        @break
                                    @case(3)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-premium/10 text-premium border border-premium/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                            <flux:icon.sparkles class="size-3 text-premium dark:text-zinc-300" />
                                            Pts Subidos
                                        </span>
                                        @break
                                    @case(4)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                            <flux:icon.truck class="size-3 text-primary dark:text-zinc-300" />
                                            Enviado
                                        </span>
                                        @break
                                    @case(5)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-800 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                            <flux:icon.check-circle class="size-3 text-zinc-700 dark:text-zinc-300" />
                                            Entregado
                                        </span>
                                        @break
                                    @case(6)
                                    @case(7)
                                    @case(8)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-danger/10 text-danger border border-danger/25 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                            <flux:icon.x-circle class="size-3 text-danger" />
                                            Rechazado
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                            Estado {{ $order->status }}
                                        </span>
                                @endswitch
                            </td>

                            <!-- Acciones Rápidas -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Botón Generar Puntos (Si está aprobado y aún no ha subido puntos) -->
                                    @if ($order->status === 2 && $order->total_pts > 0)
                                        @can(\App\Enums\PermissionName::ORDERS_POINTS->value)
                                            <flux:button
                                                wire:click="generatePoints({{ $order->id }})"
                                                wire:confirm="¿Deseas generar y subir los {{ formatear_precio($order->total_pts) }} puntos de esta orden a la red ascendente (binario y unilevel)?"
                                                size="sm"
                                                variant="primary"
                                                icon="sparkles"
                                                class="!bg-premium hover:!bg-premium/80 text-white shadow-md shadow-ink/70 dark:shadow-none"
                                                title="Subir Puntos a la Red"
                                            >
                                                Puntos
                                            </flux:button>
                                        @endcan
                                    @endif

                                    <!-- Botón Aprobar Pago (Si está pendiente) -->
                                    @if ($order->status === 1)
                                        @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                                            <flux:button
                                                wire:click="updateOrderStatus({{ $order->id }}, 2)"
                                                wire:confirm="¿Confirmas que el pago de la orden #{{ $order->public_order_number }} fue verificado y aprobado?"
                                                size="sm"
                                                variant="filled"
                                                icon="check"
                                                class="!bg-secondary hover:!bg-primary text-white shadow-md shadow-ink/70 dark:shadow-none"
                                                title="Aprobar Pago"
                                            >
                                                Aprobar
                                            </flux:button>
                                        @endcan
                                    @endif

                                    <!-- Botón Marcar Entregado (Si está enviado o con puntos subidos) -->
                                    @if (in_array($order->status, [3, 4]))
                                        @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                                            <flux:button
                                                wire:click="updateOrderStatus({{ $order->id }}, 5)"
                                                wire:confirm="¿Marcar el pedido #{{ $order->public_order_number }} como Entregado al cliente?"
                                                size="sm"
                                                variant="ghost"
                                                icon="check-circle"
                                                class="text-zinc-600 hover:text-ink dark:text-zinc-400 dark:hover:text-zinc-100"
                                                title="Marcar Entregado"
                                            />
                                        @endcan
                                    @endif

                                    <!-- Enlace a Detalle Completo -->
                                    <flux:button
                                        :href="route('admin.orders.show', $order)"
                                        size="sm"
                                        variant="ghost"
                                        icon="eye"
                                        class="text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-100"
                                        wire:navigate
                                        title="Ver Detalle Completo"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon.shopping-cart class="size-8 text-zinc-300 dark:text-zinc-600" />
                                    <p class="font-medium">{{ __('No se encontraron pedidos con los filtros aplicados.') }}</p>
                                    @if ($search || $statusFilter !== 'all' || $shippingFilter !== 'all')
                                        <flux:button wire:click="clearSearch" size="sm" variant="ghost" class="text-primary mt-1">
                                            Restablecer filtros
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if ($orders->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
