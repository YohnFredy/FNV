@php
$depth = $node['depth'] ?? 0;
$hasChildren = ! empty($node['children']);
$childrenCount = count($node['children'] ?? []);
$totalDirects = $node['summary']['direct_sponsors_count'] ?? $childrenCount;
@endphp

<div class="flex flex-col items-center select-none">
    <!-- ============================================================= -->
    <!-- TARJETA DE NODO UNILEVEL (PRO EXECUTIVE DESIGN) -->
    <!-- ============================================================= -->
    <div class="group relative flex w-52 sm:w-56 flex-col rounded-2xl border transition-all duration-200 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5
        {{ $depth === 0 
            ? 'border-primary bg-gradient-to-b from-primary/10 via-white to-white dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-950 dark:border-zinc-700 ring-2 ring-primary/30 dark:ring-zinc-700/50' 
            : 'border-zinc-200/90 bg-white dark:bg-zinc-900 dark:border-zinc-800 hover:border-primary/60 dark:hover:border-zinc-700' }}">

        <!-- Cuerpo de la Tarjeta: Avatar y Datos de Usuario -->
        <div class="p-3 flex flex-col gap-2.5">
            <div class="flex items-center gap-2.5">
                <!-- Avatar con Iniciales y Luz de Estado Activo -->
                <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-xs text-white
                    {{ $depth === 0 
                        ? 'bg-gradient-to-br from-primary to-secondary dark:from-zinc-800 dark:to-zinc-700 dark:border dark:border-zinc-600' 
                        : 'bg-gradient-to-br from-primary to-secondary dark:from-zinc-800 dark:to-zinc-700 dark:border dark:border-zinc-600' }}">
                    {{ $node['initials'] ?? 'MLM' }}
                    <span class="absolute -bottom-0.5 -right-0.5 flex size-2.5">
                        <span class="relative inline-flex size-2.5 rounded-full bg-primary border-2 border-white dark:border-zinc-900 shadow-2xs"></span>
                    </span>
                </div>

                <!-- Nombre y Nombre de Usuario -->
                <div class="min-w-0 flex-1">
                    <h4 class="truncate font-bold text-ink dark:text-zinc-50 leading-tight" title="{{ $node['name'] }}">
                        {{ $node['name'] }}
                    </h4>
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400 font-mono font-medium">
                        {{ $node['username'] }}
                    </p>
                </div>
            </div>

            <!-- Métricas Unilevel: Puntos Personales (PP) + Red Total & Puntos Grupales -->
            <div class="flex flex-col gap-2">
                <!-- Fila 1: Puntos Personales (PP) -->
                <div class="flex items-center justify-between rounded-xl bg-zinc-100 dark:bg-zinc-800/80 px-3 py-1.5 border border-zinc-300 dark:border-zinc-700/80">
                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                        <flux:icon.bolt class="size-4 text-danger dark:text-zinc-300 shrink-0" />
                        <span>{{ __('Puntos (PP):') }}</span>
                    </span>
                    <span class="font-mono font-black text-primary dark:text-zinc-100">
                        {{ number_format($node['summary']['personal_points'] ?? 0, 2) }} <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">pts</span>
                    </span>
                </div>

                <!-- Fila 2: Red Total & Puntos Grupales -->
                <div class="flex flex-col gap-1.5 rounded-xl bg-white dark:bg-zinc-950/80 p-2 border border-zinc-300 dark:border-zinc-800">
                    <!-- Línea 1: Red Total y Directos -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                            <flux:icon.users class="size-3.5 text-primary dark:text-zinc-300 shrink-0" />
                            <span>{{ __('Red Total:') }}</span>
                        </span>
                        <div class="flex items-center gap-1 font-mono">
                            <span class="font-black text-primary dark:text-zinc-100">
                                {{ number_format($node['summary']['total_network_members'] ?? 0) }} <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">soc.</span>
                            </span>
                        </div>
                    </div>

                    <!-- Línea 2: Puntos Grupales -->
                    <div class="flex items-center justify-between pt-1.5 border-t border-zinc-200/80 dark:border-zinc-800">
                        <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                            <flux:icon.chart-bar class="size-3.5 text-secondary dark:text-zinc-300 shrink-0" />
                            <span>{{ __('Pts Grupo:') }}</span>
                        </span>
                        <div class="flex items-center gap-1 font-mono">
                            <span class="font-black text-secondary dark:text-zinc-100">
                                {{ number_format($node['summary']['group_points'] ?? 0, 2) }} <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">pts</span>
                            </span>
                        </div>
                    </div>
                    @if (($node['summary']['group_waiting_points'] ?? 0) > 0)
                    <div class="flex items-center justify-between pt-1 text-[10px] text-premium dark:text-zinc-300 font-semibold cursor-help border-t border-dashed border-zinc-200/60 dark:border-zinc-800"
                        title="{{ __('Total grupo: :total pts (:placed pts red activa + :waiting pts sala de espera)', ['total' => number_format($node['summary']['group_points'] ?? 0, 2), 'placed' => number_format($node['summary']['group_placed_points'] ?? 0, 2), 'waiting' => number_format($node['summary']['group_waiting_points'] ?? 0, 2)]) }}">
                        <span class="flex items-center gap-1">
                            <flux:icon.clock class="size-2.5 text-premium dark:text-zinc-400 shrink-0" />
                            <span>{{ __('En espera:') }}</span>
                        </span>
                        <span class="font-mono">+{{ number_format($node['summary']['group_waiting_points'], 2) }} pts</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Botones de Acción de la Tarjeta -->
            <div class="flex items-center gap-1.5 border-t border-zinc-100 dark:border-zinc-800 pt-1 {{ ($node['has_more_children'] ?? false) ? 'pb-1.5' : '' }}">
                <!-- Botón Perforar / Enfocar este Nodo -->
                <button
                    type="button"
                    wire:click="focusNode({{ $node['id'] }})"
                    title="{{ __('Enfocar red unilevel de este afiliado') }}"
                    class="flex-1 min-h-[32px] flex items-center justify-center gap-1.5 rounded-xl border border-zinc-200 bg-primary hover:border-secondary hover:bg-secondary hover:text-white dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:hover:text-zinc-50 py-1.5 px-2.5 text-xs font-bold text-white transition-all cursor-pointer active:scale-95">
                    <flux:icon.magnifying-glass-plus class="size-3.5" />
                    <span>{{ __('Enfocar') }}</span>
                </button>

                <!-- Botón Ficha Técnica / Ver Detalles -->
                <button
                    type="button"
                    wire:click="openUserDetails({{ $node['id'] }})"
                    title="{{ __('Ver ficha técnica completa del afiliado') }}"
                    class="size-9 shrink-0 flex items-center justify-center rounded-xl border border-zinc-300 bg-zinc-200 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-premium dark:text-zinc-300 transition-all cursor-pointer active:scale-95">
                    <flux:icon.information-circle class="size-5" />
                </button>
            </div>
        </div>

        <!-- Indicador si hay ramas más profundas -->
        @if ($node['has_more_children'] ?? false)
        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap">
            <button
                type="button"
                wire:click="focusNode({{ $node['id'] }})"
                title="{{ __('Ver afiliados frontales de este usuario') }}"
                class="inline-flex items-center gap-1 rounded-full bg-premium hover:bg-premium/90 text-white dark:bg-zinc-800 dark:border dark:border-zinc-700 dark:text-zinc-100 px-3 py-0.5 text-xs font-bold shadow-md shadow-ink/40 hover:shadow-lg cursor-pointer transition-all active:scale-95 leading-tight">
                <flux:icon.chevron-down class="size-3 shrink-0" />
                <span>{{ __('Ver Frontales') }}</span>
            </button>
        </div>
        @endif
    </div>

    <!-- ============================================================= -->
    <!-- CONECTORES Y FRONTALIDAD DE HIJOS DIRECTOS -->
    <!-- ============================================================= -->
    @if ($hasChildren)
    <div class="flex flex-col items-center w-full">
        <!-- Línea vertical que desciende del sponsor -->
        <div class="h-6 w-0.5 bg-primary dark:bg-zinc-700"></div>

        <!-- Contenedor horizontal con todos los hijos frontales contiguos -->
        <div class="flex items-start justify-center">
            @foreach ($node['children'] as $index => $child)
            <div class="relative flex flex-col items-center px-1.5 sm:px-3 pt-6">
                @if ($childrenCount > 1)
                @if ($index === 0)
                <!-- Primer hijo: línea horizontal de la mitad hacia la derecha -->
                <div class="absolute top-0 left-1/2 right-0 h-0.5 bg-primary dark:bg-zinc-700"></div>
                @elseif ($index === $childrenCount - 1)
                <!-- Último hijo: línea horizontal de la izquierda hacia la mitad -->
                <div class="absolute top-0 left-0 right-1/2 h-0.5 bg-primary dark:bg-zinc-700"></div>
                @else
                <!-- Hijos intermedios: línea horizontal completa de extremo a extremo -->
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-primary dark:bg-zinc-700"></div>
                @endif
                @endif

                <!-- Conector vertical individual hacia cada hijo -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 h-6 w-0.5 bg-primary dark:bg-zinc-700"></div>
                @include('livewire.office.network.partials.unilevel-node', ['node' => $child])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
