@php
$isUser = ($node['type'] ?? '') === 'user_node';
$isEmpty = ($node['type'] ?? '') === 'empty_slot';
$depth = $node['depth'] ?? 0;
$position = $node['position'] ?? null;
@endphp

<div class="flex flex-col items-center select-none">
    @if ($isUser)
    <!-- ============================================================= -->
    <!-- TARJETA DE NODO AFILIADO REGISTRADO (PRO EXECUTIVE DESIGN) -->
    <!-- ============================================================= -->
    <div class="group relative flex w-52 sm:w-56 flex-col rounded-2xl border transition-all duration-200 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-0.5
            {{ $depth === 0 
                ? 'border-primary bg-gradient-to-b from-primary/10 via-white to-white dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-950 dark:border-zinc-700 ring-2 ring-primary/30 dark:ring-zinc-700/50' 
                : ($position === 'L' 
                    ? 'border-primary/40 bg-white dark:bg-zinc-900 dark:border-zinc-800 hover:border-primary dark:hover:border-zinc-700' 
                    : 'border-secondary/40 bg-white dark:bg-zinc-900 dark:border-zinc-800 hover:border-secondary dark:hover:border-zinc-700') }}">

        <!-- Cuerpo de la Tarjeta: Avatar y Datos de Usuario -->
        <div class="p-3 flex flex-col gap-2.5">
            <div class="flex items-center gap-2.5">
                <!-- Avatar con Iniciales y Luz de Estado Activo -->
                <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-xs text-white
                        {{ $depth === 0 
                            ? 'bg-gradient-to-br from-primary to-ink dark:from-zinc-800 dark:to-zinc-700 dark:border dark:border-zinc-600' 
                            : ($position === 'L' 
                                ? 'bg-gradient-to-br from-primary to-secondary dark:from-zinc-800 dark:to-zinc-700 dark:border dark:border-zinc-600' 
                                : 'bg-gradient-to-br from-secondary to-primary dark:from-zinc-800 dark:to-zinc-700 dark:border dark:border-zinc-600') }}">
                    {{ $node['initials'] ?? 'MLM' }}
                    <span class="absolute -bottom-0.5 -right-0.5 flex size-2.5">
                        <span class="relative inline-flex size-2.5 rounded-full bg-secondary border-2 border-white dark:border-zinc-900 shadow-2xs"></span>
                    </span>
                </div>

                <!-- Nombre y Nombre de Usuario -->
                <div class="min-w-0 flex-1">
                    <h4 class="truncate font-bold text-primary dark:text-zinc-50 leading-tight" title="{{ $node['name'] }}">
                        {{ $node['name'] }}
                    </h4>
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400 font-mono font-medium">
                        {{ $node['username'] }}
                    </p>
                </div>
            </div>

            <!-- Métricas Binarias: Puntos Personales (PP) + Piernas Izq/Der -->
            <div class="flex flex-col gap-2">
                <!-- Fila 1: Puntos Personales (PP) -->
                <div class="flex items-center justify-between rounded-xl bg-zinc-100 dark:bg-zinc-800/80 px-3 py-1.5 border border-zinc-300 dark:border-zinc-700/80">
                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                        <flux:icon.bolt class="size-3.5 text-danger dark:text-zinc-300 shrink-0" />
                        <span>{{ __('Puntos (PP):') }}</span>
                    </span>
                    <span class="font-mono font-black text-primary dark:text-zinc-100">
                        {{ number_format($node['summary']['personal_points'] ?? 0, 0) }} <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">pts</span>
                    </span>
                </div>

                <!-- Fila 2: Pierna Izquierda vs Pierna Derecha -->
                <div class="grid grid-cols-2 gap-1 rounded-xl bg-white dark:bg-zinc-950/80 p-2 border border-zinc-300 dark:border-zinc-800">
                    <!-- Pierna Izquierda -->
                    <div class="flex flex-col items-center text-center">
                        <span class="text-xs font-bold text-primary dark:text-zinc-300 flex items-center gap-1">
                            <flux:icon.arrow-left class="size-3 text-primary dark:text-zinc-300" />
                            <span>{{ __('Pierna Izq') }}</span>
                        </span>
                        <div class="mt-1 text-base font-black font-mono text-primary dark:text-zinc-100 leading-tight">
                            {{ number_format($node['summary']['left_points'] ?? 0, 2) }} <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">pts</span>
                        </div>
                        <div class="mt-0.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ number_format($node['summary']['left_members'] ?? 0) }} <span class="text-xs font-normal text-zinc-700 dark:text-zinc-400">soc.</span>
                        </div>
                        @if (($node['summary']['left_waiting_points'] ?? 0) > 0)
                        <div class="mt-1 inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-premium/10 border border-premium/30 dark:bg-zinc-800 dark:border-zinc-700 text-[10px] font-semibold text-premium dark:text-zinc-300 cursor-help"
                            title="{{ __('Total: :total pts (:placed pts red activa + :waiting pts sala de espera)', ['total' => number_format($node['summary']['left_points'] ?? 0, 2), 'placed' => number_format($node['summary']['left_placed_points'] ?? 0, 2), 'waiting' => number_format($node['summary']['left_waiting_points'] ?? 0, 2)]) }}">
                            <flux:icon.clock class="size-2.5 text-premium dark:text-zinc-400 shrink-0" />
                            <span>+{{ number_format($node['summary']['left_waiting_points'], 2) }} esp.</span>
                        </div>
                        @endif
                    </div>

                    <!-- Pierna Derecha -->
                    <div class="flex flex-col items-center text-center border-l border-zinc-300 dark:border-zinc-800">
                        <span class="text-xs font-bold text-secondary dark:text-zinc-300 flex items-center gap-1">
                            <span>{{ __('Pierna Der') }}</span>
                            <flux:icon.arrow-right class="size-3 text-secondary dark:text-zinc-300" />
                        </span>
                        <div class="mt-1 text-base font-black font-mono text-secondary dark:text-zinc-100 leading-tight">
                            {{ number_format($node['summary']['right_points'] ?? 0, 2) }} <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-400">pts</span>
                        </div>
                        <div class="mt-0.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ number_format($node['summary']['right_members'] ?? 0) }} <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">soc.</span>
                        </div>
                        @if (($node['summary']['right_waiting_points'] ?? 0) > 0)
                        <div class="mt-1 inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-premium/10 border border-premium/30 dark:bg-zinc-800 dark:border-zinc-700 text-[10px] font-semibold text-premium dark:text-zinc-300 cursor-help"
                            title="{{ __('Total: :total pts (:placed pts red activa + :waiting pts sala de espera)', ['total' => number_format($node['summary']['right_points'] ?? 0, 2), 'placed' => number_format($node['summary']['right_placed_points'] ?? 0, 2), 'waiting' => number_format($node['summary']['right_waiting_points'] ?? 0, 2)]) }}">
                            <flux:icon.clock class="size-2.5 text-premium dark:text-zinc-400 shrink-0" />
                            <span>+{{ number_format($node['summary']['right_waiting_points'], 2) }} esp.</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Botones de Acción de la Tarjeta -->
            <div class="flex items-center gap-1.5 border-t border-zinc-100 dark:border-zinc-800 pt-1 {{ (($node['has_more_left'] ?? false) || ($node['has_more_right'] ?? false)) ? 'pb-1.5' : '' }}">
                <!-- Botón Perforar / Enfocar este Nodo -->
                <button
                    type="button"
                    wire:click="focusNode({{ $node['id'] }})"
                    title="{{ __('Enfocar y ver la red descendente de este afiliado') }}"
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

        <!-- Indicador si hay más ramas por debajo -->
        @if (($node['has_more_left'] ?? false) || ($node['has_more_right'] ?? false))
        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap">
            <button
                type="button"
                wire:click="focusNode({{ $node['id'] }})"
                title="{{ __('Ver ramas descendentes de este afiliado') }}"
                class="inline-flex items-center gap-1 rounded-full bg-premium hover:bg-premium/90 text-white dark:bg-zinc-800 dark:border dark:border-zinc-700 dark:text-zinc-100 px-3 py-0.5 text-xs font-bold shadow-md shadow-ink/40 hover:shadow-lg cursor-pointer transition-all active:scale-95 leading-tight">
                <flux:icon.chevron-down class="size-3 shrink-0" />
                <span>{{ __('Ver Ramas') }}</span>
            </button>
        </div>
        @endif
    </div>

    @elseif ($isEmpty)
    <!-- ============================================================= -->
    <!-- RANURA VACÍA DISPONIBLE PARA COLOCACIÓN -->
    <!-- ============================================================= -->
    @php
    $legSlug = ($node['position'] ?? 'L') === 'R' ? 'right' : 'left';
    $regUrl = url('/register/' . urlencode($node['parent_username'] ?? '') . '/' . $legSlug);
    @endphp
    <div class="group flex w-52 sm:w-56 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-400 bg-white/80 p-3.5 text-center transition-all hover:border-primary hover:bg-primary/5 dark:border-zinc-700 dark:bg-zinc-900/60 dark:hover:border-zinc-600 dark:hover:bg-zinc-800/40 shadow-sm hover:shadow-md backdrop-blur-xs">
        <div class="flex size-9 items-center justify-center rounded-xl bg-zinc-200 text-zinc-800 group-hover:bg-primary/10 group-hover:text-primary dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:text-zinc-200 transition-colors">
            <flux:icon.plus class="size-4" />
        </div>

        <h5 class="mt-2 text-xs font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
            {{ __('Posición :pos Disponible', ['pos' => ($node['position'] ?? 'L') === 'R' ? 'Derecha (R)' : 'Izquierda (L)']) }}
        </h5>

        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 font-mono font-medium">
            {{ __('Bajo :user', ['user' => '@' . ($node['parent_username'] ?? '')]) }}
        </p>
    </div>
    @endif

    <!-- ============================================================= -->
    <!-- CONECTORES Y SUB-RAMAS (HIJO IZQUIERDO Y HIJO DERECHO) -->
    <!-- ============================================================= -->
    @if ($isUser && (($node['left_child'] ?? null) !== null || ($node['right_child'] ?? null) !== null))
    <div class="flex flex-col items-center w-full">
        <!-- Tallo vertical que desciende del nodo padre -->
        <div class="h-6 w-0.5 bg-primary dark:bg-zinc-700"></div>

        <!-- Contenedor flex de ambas piernas contiguas -->
        <div class="flex items-start justify-center">

            <!-- Sub-rama Izquierda -->
            @if (($node['left_child'] ?? null) !== null)
            <div class="relative flex flex-col items-center px-1.5 sm:px-3 pt-6">
                <!-- Conector horizontal superior: Desde el centro (50%) hacia la derecha (100%) -->
                <div class="absolute top-0 left-1/2 right-0 h-0.5 bg-primary dark:bg-zinc-700"></div>
                <!-- Conector vertical hacia la tarjeta izquierda -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 h-6 w-0.5 bg-primary dark:bg-zinc-700"></div>
                @include('livewire.office.network.partials.binary-node', ['node' => $node['left_child']])
            </div>
            @endif

            <!-- Sub-rama Derecha -->
            @if (($node['right_child'] ?? null) !== null)
            <div class="relative flex flex-col items-center px-1.5 sm:px-3 pt-6">
                <!-- Conector horizontal superior: Desde la izquierda (0%) hacia el centro (50%) -->
                <div class="absolute top-0 left-0 right-1/2 h-0.5 bg-secondary dark:bg-zinc-700"></div>
                <!-- Conector vertical hacia la tarjeta derecha -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 h-6 w-0.5 bg-secondary dark:bg-zinc-700"></div>
                @include('livewire.office.network.partials.binary-node', ['node' => $node['right_child']])
            </div>
            @endif

        </div>
    </div>
    @endif
</div>
