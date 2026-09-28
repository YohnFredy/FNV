<div class="space-y-6">
    <!-- Migas de Pan -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('admin.orders.index') }}" wire:navigate>
            {{ __('Gestión de Pedidos') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            #{{ $order->public_order_number }}
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

    <!-- Barra de Progreso del Pedido -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Paso 1: Pago Aprobado -->
            <div class="flex flex-col items-center text-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                    {{ $order->status >= 2 && $order->status < 6 ? 'bg-secondary shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                    <flux:icon.check class="size-6 stroke-2" />
                </div>
                <span class="text-xs font-semibold mt-2 text-ink dark:text-zinc-200">1. Pago Aprobado</span>
            </div>

            <div class="w-full sm:w-auto flex-1 h-1 {{ $order->status >= 3 && $order->status < 6 ? 'bg-premium' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

            <!-- Paso 2: Puntos Subidos -->
            <div class="flex flex-col items-center text-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                    {{ $order->status >= 3 && $order->status < 6 ? 'bg-premium shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                    <flux:icon.sparkles class="size-6" />
                </div>
                <span class="text-xs font-semibold mt-2 text-ink dark:text-zinc-200">2. Puntos en Red</span>
            </div>

            <div class="w-full sm:w-auto flex-1 h-1 {{ $order->status >= 4 && $order->status < 6 ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

            <!-- Paso 3: Enviado -->
            <div class="flex flex-col items-center text-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                    {{ $order->status >= 4 && $order->status < 6 ? 'bg-primary shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                    <flux:icon.truck class="size-6" />
                </div>
                <span class="text-xs font-semibold mt-2 text-ink dark:text-zinc-200">3. Enviado</span>
            </div>

            <div class="w-full sm:w-auto flex-1 h-1 {{ $order->status >= 5 && $order->status < 6 ? 'bg-zinc-800 dark:bg-zinc-400' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>

            <!-- Paso 4: Entregado -->
            <div class="flex flex-col items-center text-center flex-1">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white transition-colors
                    {{ $order->status >= 5 && $order->status < 6 ? 'bg-zinc-800 dark:bg-zinc-200 dark:text-zinc-900 shadow-md shadow-ink/70 dark:shadow-none' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }}">
                    <flux:icon.check-circle class="size-6" />
                </div>
                <span class="text-xs font-semibold mt-2 text-ink dark:text-zinc-200">4. Entregado</span>
            </div>
        </div>

        @if ($order->status >= 6)
            <div class="mt-6 p-4 rounded-xl bg-danger/10 border border-danger/25 text-danger flex items-center justify-center gap-2.5 text-sm font-semibold">
                <flux:icon.x-circle class="size-5 shrink-0" />
                <span>Esta orden fue cancelada, rechazada o anulada (Estado actual: {{ self::getStatusLabel($order->status) }}).</span>
            </div>
        @endif
    </div>

    <!-- Encabezado de la Orden y Barra de Acciones del Administrador -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs uppercase font-bold text-zinc-400">Orden de Compra</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold border
                    @switch($order->status)
                        @case(1) bg-primary/10 text-primary border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 @break
                        @case(2) bg-secondary/10 text-secondary border-secondary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 @break
                        @case(3) bg-premium/10 text-premium border-premium/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 @break
                        @case(4) bg-primary/10 text-primary border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 @break
                        @case(5) bg-zinc-100 text-zinc-800 border-zinc-300 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 @break
                        @case(6) @case(7) @case(8) bg-danger/10 text-danger border-danger/25 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700 @break
                        @default bg-zinc-100 text-zinc-600 border-zinc-300 dark:border-zinc-700
                    @endswitch
                ">
                    {{ self::getStatusLabel($order->status) }}
                </span>
                <span class="text-xs text-zinc-400">
                    Creado el {{ $order->created_at->format('d/m/Y h:i A') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink dark:text-zinc-100 font-mono">
                #{{ $order->public_order_number }}
            </h1>
        </div>

        <!-- Botones de Acción de Flujo -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Si está pendiente: Botón Aprobar Pago -->
            @if ($order->status === 1)
                @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                    <flux:button
                        wire:click="updateStatus(2)"
                        wire:confirm="¿Confirmas que el pago de la orden #{{ $order->public_order_number }} fue verificado y aprobado?"
                        variant="filled"
                        icon="check"
                        class="!bg-secondary hover:!bg-primary text-white shadow-md shadow-ink/70 dark:shadow-none"
                    >
                        Aprobar Pago
                    </flux:button>
                @endcan

                @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                    <flux:button
                        wire:click="updateStatus(6)"
                        wire:confirm="¿Deseas rechazar este pedido?"
                        variant="ghost"
                        icon="x-circle"
                        class="text-danger hover:bg-danger/10"
                    >
                        Rechazar
                    </flux:button>
                @endcan
            @endif

            <!-- Si está aprobado (status 2) o aún no ha generado puntos y total_pts > 0: Botón Generar Puntos MLM -->
            @if (in_array($order->status, [2, 1]) && $order->total_pts > 0 && $this->pointTransactions->isEmpty())
                @can(\App\Enums\PermissionName::ORDERS_POINTS->value)
                    <flux:button
                        wire:click="generatePoints"
                        wire:confirm="¿Confirmas que deseas generar y propagar los {{ formatear_precio($order->total_pts) }} puntos a la red ascendente (binario y unilevel) de {{ $order->user?->name }}?"
                        variant="primary"
                        icon="sparkles"
                        class="!bg-premium hover:!bg-premium/80 text-white shadow-md shadow-ink/70 dark:shadow-none font-bold"
                    >
                        Generar Puntos MLM ({{ formatear_precio($order->total_pts) }} pts)
                    </flux:button>
                @endcan
            @endif

            <!-- Si ya generó puntos o está aprobado: Botón Enviar -->
            @if (in_array($order->status, [2, 3]))
                @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                    <flux:button
                        wire:click="updateStatus(4)"
                        wire:confirm="¿Confirmas que el pedido fue despachado y se encuentra en camino?"
                        variant="filled"
                        icon="truck"
                        class="!bg-primary hover:!bg-secondary text-white shadow-md shadow-ink/70 dark:shadow-none"
                    >
                        Marcar como Enviado
                    </flux:button>
                @endcan
            @endif

            <!-- Si está enviado: Botón Entregado -->
            @if ($order->status === 4)
                @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                    <flux:button
                        wire:click="updateStatus(5)"
                        wire:confirm="¿Confirmas que el pedido fue entregado exitosamente al destinatario?"
                        variant="filled"
                        icon="check-circle"
                        class="!bg-zinc-800 dark:!bg-zinc-200 text-white dark:text-zinc-900 shadow-md shadow-ink/70 dark:shadow-none"
                    >
                        Marcar como Entregado
                    </flux:button>
                @endcan
            @endif

            <!-- Control Administrativo de Cambio Manual de Estado -->
            @can(\App\Enums\PermissionName::ORDERS_EDIT->value)
                <div class="flex items-center gap-1.5 pl-3 border-l border-zinc-200 dark:border-zinc-800">
                    <select
                        wire:model="selectedStatus"
                        class="px-2.5 py-1.5 text-xs rounded-xl border border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200 focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="1">1 - Pendiente de Pago</option>
                        <option value="2">2 - Pago Aprobado</option>
                        <option value="3">3 - Puntos Generados</option>
                        <option value="4">4 - Enviado / En Camino</option>
                        <option value="5">5 - Entregado</option>
                        <option value="6">6 - Pago Rechazado</option>
                        <option value="7">7 - Anulada</option>
                    </select>
                    @if ($selectedStatus !== $order->status)
                        <flux:button
                            wire:click="applyManualStatus"
                            wire:confirm="¿Confirmas el cambio forzado de estado a este pedido?"
                            size="sm"
                            variant="ghost"
                            class="text-primary hover:bg-primary/10"
                        >
                            Aplicar
                        </flux:button>
                    @endif
                </div>
            @endcan

            <flux:button :href="route('admin.orders.index')" size="sm" variant="ghost" icon="arrow-left" wire:navigate class="text-zinc-600 hover:text-ink dark:text-zinc-400 dark:hover:text-zinc-100">
                Volver
            </flux:button>
        </div>
    </div>

    <!-- Panel de Auditoría de Puntos MLM (Si ya se generaron transacciones) -->
    @if ($this->pointTransactions->isNotEmpty())
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-premium/30 dark:border-zinc-800 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-premium/10 text-premium flex items-center justify-center">
                        <flux:icon.sparkles class="size-5" />
                    </div>
                    <div>
                        <h2 class="font-bold text-ink dark:text-zinc-100">Trazabilidad de Puntos en Red MLM</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Puntos registrados en el libro contable inmutable de la red.</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-premium/15 text-premium border border-premium/30 font-mono">
                    {{ formatear_precio($order->total_pts) }} Puntos Generados
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 uppercase">
                            <th class="py-2.5 px-3">Tipo de Beneficio</th>
                            <th class="py-2.5 px-3">Usuario Beneficiado</th>
                            <th class="py-2.5 px-3 text-center">Pierna Binaria</th>
                            <th class="py-2.5 px-3 text-right">Puntos</th>
                            <th class="py-2.5 px-3">Concepto</th>
                            <th class="py-2.5 px-3 text-right">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @foreach ($this->pointTransactions as $tx)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="py-2.5 px-3 font-semibold">
                                    @switch($tx->tree_type)
                                        @case('personal')
                                            <span class="text-primary dark:text-zinc-200">Puntos Personales (PV)</span>
                                            @break
                                        @case('binary')
                                            <span class="text-secondary dark:text-zinc-300">Volumen Binario (GV)</span>
                                            @break
                                        @case('unilevel')
                                            <span class="text-premium dark:text-zinc-300">Volumen Unilevel (GV)</span>
                                            @break
                                        @default
                                            <span class="text-zinc-600">{{ $tx->tree_type }}</span>
                                    @endswitch
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-medium text-ink dark:text-zinc-200">{{ $tx->user?->name }} {{ $tx->user?->last_name }}</span>
                                    <span class="block text-[11px] text-zinc-400 font-mono">{{ '@' . $tx->user?->username }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if ($tx->leg)
                                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] {{ $tx->leg === 'L' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary' }}">
                                            Pierna {{ $tx->leg === 'L' ? 'Izquierda (L)' : 'Derecha (R)' }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-ink dark:text-zinc-100">
                                    +{{ formatear_precio($tx->points) }} pts
                                </td>
                                <td class="py-2.5 px-3 text-zinc-600 dark:text-zinc-400 truncate max-w-xs">
                                    {{ $tx->description }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-zinc-400 font-mono">
                                    {{ $tx->created_at->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tarjetas de Información: Cliente, Facturación y Envío -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Ficha del Comprador y Red -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <flux:icon.user class="size-5" />
                </div>
                <h2 class="font-bold text-ink dark:text-zinc-100">Datos del Comprador</h2>
            </div>
            <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                <p><strong class="text-ink dark:text-zinc-100">Nombre:</strong> {{ $order->user?->name }} {{ $order->user?->last_name }}</p>
                <p><strong class="text-ink dark:text-zinc-100">Usuario:</strong> <span class="font-mono">{{ '@' . $order->user?->username }}</span></p>
                <p><strong class="text-ink dark:text-zinc-100">Email:</strong> {{ $order->user?->email }}</p>
                <p><strong class="text-ink dark:text-zinc-100">Cédula/DNI:</strong> {{ $order->user?->dni ?? 'N/A' }}</p>
                @if ($order->user?->binaryNode)
                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 text-xs">
                        <span class="font-semibold text-ink dark:text-zinc-200">Ubicación Binaria:</span>
                        <span class="block text-zinc-500">Pierna: {{ $order->user->binaryNode->leg === 'L' ? 'Izquierda (L)' : 'Derecha (R)' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Facturación -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center">
                    <flux:icon.document-text class="size-5" />
                </div>
                <h2 class="font-bold text-ink dark:text-zinc-100">Datos de Facturación</h2>
            </div>
            <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                <p><strong class="text-ink dark:text-zinc-100">Razón Social:</strong> {{ $order->billingData?->name ?? 'N/A' }}</p>
                <p><strong class="text-ink dark:text-zinc-100">{{ $order->billingData?->documentType?->name ?? 'Documento' }}:</strong> {{ $order->billingData?->document ?? 'N/A' }}</p>
                <p><strong class="text-ink dark:text-zinc-100">Email:</strong> {{ $order->billingData?->email ?? 'N/A' }}</p>
                <p><strong class="text-ink dark:text-zinc-100">Teléfono:</strong> {{ $order->billingData?->phone ?? 'N/A' }}</p>
                <p><strong class="text-ink dark:text-zinc-100">Dirección:</strong>
                    {{ $order->billingData?->address ?? 'N/A' }}
                    @if ($order->billingData?->city || $order->billingData?->addCity)
                        , {{ $order->billingData?->city?->name ?? $order->billingData?->addCity }}
                    @endif
                    @if ($order->billingData?->department)
                        , {{ $order->billingData?->department?->name }}
                    @endif
                    @if ($order->billingData?->country)
                        , {{ $order->billingData?->country?->name }}
                    @endif
                </p>
            </div>
        </div>

        <!-- 3. Envío y Entrega -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="w-8 h-8 rounded-lg bg-premium/10 text-premium flex items-center justify-center">
                    <flux:icon.map-pin class="size-5" />
                </div>
                <h2 class="font-bold text-ink dark:text-zinc-100">Despacho y Entrega</h2>
            </div>
            @if ($order->shipping_type == 1)
                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm">
                    <div class="flex items-center gap-2 text-ink dark:text-zinc-100 font-semibold">
                        <flux:icon.building-storefront class="size-4 text-primary" />
                        <span>Recogida en Tienda Principal</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">El cliente reclamará sus productos directamente en la sede.</p>
                </div>
            @else
                <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <p><strong class="text-ink dark:text-zinc-100">Destinatario:</strong> {{ $order->shipping_name ?? $order->billingData?->name ?? 'Mismo comprador' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Documento:</strong> {{ $order->shipping_document ?? $order->billingData?->document ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Teléfono:</strong> {{ $order->shipping_phone ?? $order->billingData?->phone ?? 'N/A' }}</p>
                    <p><strong class="text-ink dark:text-zinc-100">Dirección:</strong>
                        {{ $order->shipping_address ?? $order->billingData?->address }}
                        @if ($order->shipping_additional_address)
                            ({{ $order->shipping_additional_address }})
                        @endif
                        , {{ $order->shippingCity?->name ?? $order->shipping_addCity }},
                        {{ $order->shippingDepartment?->name }},
                        {{ $order->shippingCountry?->name }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tabla de Productos del Pedido -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-lg font-bold text-ink dark:text-zinc-100">Productos del Pedido ({{ $order->items->count() }})</h2>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-premium/10 text-premium border border-premium/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                Puntos Totales: {{ formatear_precio($order->total_pts) }} pts
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-300">
                <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-950 text-ink dark:text-zinc-200 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3.5 font-bold">Producto</th>
                        <th class="px-4 py-3.5 text-center font-bold">Cant</th>
                        <th class="px-4 py-3.5 text-right font-bold">Precio Unit.</th>
                        <th class="px-4 py-3.5 text-center font-bold">Pts Unit.</th>
                        <th class="px-4 py-3.5 text-right font-bold">Descuento</th>
                        <th class="px-4 py-3.5 text-right font-bold">IVA</th>
                        <th class="px-4 py-3.5 text-right font-bold">Total</th>
                        <th class="px-4 py-3.5 text-center font-bold">Pts Totales</th>
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
                                <div>
                                    <span class="font-semibold text-ink dark:text-zinc-100">{{ $item->name }}</span>
                                    <span class="block text-xs text-zinc-400 font-mono">ID: {{ $item->product_id }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center font-medium text-ink dark:text-zinc-200">{{ $item->quantity }}</td>
                            <td class="px-4 py-4 text-right font-mono">${{ formatear_precio($item->unit_price) }}</td>
                            <td class="px-4 py-4 text-center text-premium font-semibold font-mono">{{ formatear_precio($item->pts) }}</td>
                            <td class="px-4 py-4 text-right text-premium font-semibold font-mono">-${{ formatear_precio($item->discount) }}</td>
                            <td class="px-4 py-4 text-right font-mono">${{ formatear_precio($item->tax_amount) }}</td>
                            <td class="px-4 py-4 text-right font-bold text-ink dark:text-zinc-100 font-mono">
                                ${{ formatear_precio($item->unit_sales_price) }}
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-premium font-mono">
                                {{ formatear_precio($item->total_pts) }} pts
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Desglose Financiero -->
        <div class="p-6 bg-zinc-50/80 dark:bg-zinc-800/40 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
            <div class="w-full sm:w-80 space-y-2.5 text-sm">
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Subtotal:</span>
                    <span class="font-medium text-ink dark:text-zinc-100 font-mono">${{ formatear_precio($order->subtotal) }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-premium">
                        <span>Descuento aplicado:</span>
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
                        <span>Costo de Envío:</span>
                        <span class="font-medium text-ink dark:text-zinc-100 font-mono">${{ formatear_precio($order->shipping_cost) }}</span>
                    </div>
                @endif
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-baseline">
                    <span class="text-base font-bold text-ink dark:text-zinc-100">Total Liquidado:</span>
                    <span class="text-2xl font-black text-primary dark:text-secondary font-mono">${{ formatear_precio($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Pasarela / Webhook de Pago (si existe) -->
    @if ($order->webhook)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200 dark:border-zinc-800 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <flux:icon.credit-card class="size-5" />
                </div>
                <div>
                    <h2 class="font-bold text-ink dark:text-zinc-100">Registro de Pasarela de Pagos ({{ strtoupper($order->webhook->payment_gateway) }})</h2>
                    <p class="text-xs text-zinc-500">Referencia: {{ $order->webhook->reference }}</p>
                </div>
            </div>
            <div class="text-xs text-zinc-600 dark:text-zinc-400 space-y-1 font-mono bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl overflow-x-auto">
                <p>Fecha Webhook: {{ $order->webhook->created_at->format('d/m/Y H:i:s') }}</p>
                <p>Estado Pasarela: {{ $order->webhook->payload['type'] ?? 'N/A' }}</p>
                <p>Método de Pago: {{ $order->payment_method ?? 'Bold' }}</p>
            </div>
        </div>
    @endif
</div>
