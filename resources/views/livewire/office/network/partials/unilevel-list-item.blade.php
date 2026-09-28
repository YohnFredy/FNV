@php
    $depth = $node['depth'] ?? 0;
    $hasChildren = ! empty($node['children']);
    $isExpanded = isset($expandedNodes[$node['id']]);
    $directCount = $node['summary']['direct_sponsors_count'] ?? count($node['children'] ?? []);
@endphp

<div class="flex flex-col border-l-2 border-primary/30 dark:border-zinc-800 ml-1.5 sm:ml-4 pl-1.5 sm:pl-3 my-1.5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 rounded-2xl border border-zinc-200/90 bg-white p-2.5 sm:p-3 shadow-sm hover:shadow-md hover:border-primary/40 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700 transition-all">
        
        <!-- Identificación de Usuario y Avatar -->
        <div class="flex items-center gap-2.5 min-w-0">
            @if ($hasChildren)
                <button 
                    type="button" 
                    wire:click="toggleNode({{ $node['id'] }})"
                    class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-zinc-100 hover:bg-primary/10 text-zinc-600 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 cursor-pointer active:scale-95 transition-transform">
                    @if ($isExpanded)
                        <flux:icon.chevron-down class="size-4 text-primary dark:text-zinc-300" />
                    @else
                        <flux:icon.chevron-right class="size-4" />
                    @endif
                </button>
            @else
                <div class="size-8 flex shrink-0 items-center justify-center text-zinc-300 dark:text-zinc-700">
                    <flux:icon.minus class="size-3" />
                </div>
            @endif

            <div class="flex size-8.5 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-secondary font-black text-xs text-white shadow-xs">
                {{ $node['initials'] ?? 'MLM' }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-xs sm:text-sm text-zinc-900 dark:text-white truncate">
                        {{ $node['name'] }}
                    </span>
                    <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 text-[9px] sm:text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 shrink-0">
                        {{ __('Gen. :g', ['g' => $depth]) }}
                    </span>
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono truncate">
                    {{ '@' . $node['username'] }}
                </div>
            </div>
        </div>

        <!-- Métricas y Botones de Acción -->
        <div class="flex items-center justify-between sm:justify-end gap-2 pl-10 sm:pl-0">
            <div class="flex items-center gap-1.5 text-[11px]">
                <span class="rounded-lg bg-zinc-100 px-2 py-1 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 text-[10px] sm:text-xs">
                    <strong>{{ $directCount }}</strong> {{ __('dir.') }}
                </span>
                <span class="rounded-lg bg-primary/10 px-2 py-1 text-primary dark:bg-zinc-800 dark:text-zinc-200 text-[10px] sm:text-xs font-mono font-bold">
                    {{ number_format($node['summary']['personal_points'] ?? 0, 0) }} {{ __('PP') }}
                </span>
                <span class="rounded-lg bg-zinc-100 px-2 py-1 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 text-[10px] sm:text-xs">
                    <strong>{{ number_format($node['summary']['total_network_members']) }}</strong> {{ __('en red') }}
                </span>
                <span class="rounded-lg bg-secondary/10 px-2 py-1 text-secondary dark:bg-zinc-800 dark:text-zinc-200 text-[10px] sm:text-xs font-mono font-bold" title="{{ __('Puntos grupales totales') }}">
                    {{ number_format($node['summary']['group_points'] ?? 0, 2) }} {{ __('PG') }}
                </span>
                @if (($node['summary']['group_waiting_points'] ?? 0) > 0)
                <span class="rounded-lg bg-premium/10 border border-premium/30 px-1.5 py-0.5 text-premium dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 text-[10px] font-semibold cursor-help"
                    title="{{ __('Total: :total pts (:placed red activa + :waiting sala de espera)', ['total' => number_format($node['summary']['group_points'] ?? 0, 2), 'placed' => number_format($node['summary']['group_placed_points'] ?? 0, 2), 'waiting' => number_format($node['summary']['group_waiting_points'] ?? 0, 2)]) }}">
                    +{{ number_format($node['summary']['group_waiting_points'], 2) }} {{ __('esp.') }}
                </span>
                @endif
            </div>

            <div class="flex items-center gap-1.5">
                <button 
                    type="button" 
                    wire:click="focusNode({{ $node['id'] }})"
                    title="{{ __('Enfocar árbol de este afiliado') }}"
                    class="rounded-xl bg-zinc-100 hover:bg-primary/10 hover:text-primary dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:hover:text-zinc-100 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 cursor-pointer min-h-[34px] flex items-center justify-center">
                    {{ __('Enfocar') }}
                </button>

                <button 
                    type="button" 
                    wire:click="openUserDetails({{ $node['id'] }})"
                    title="{{ __('Ver ficha técnica') }}"
                    class="flex size-8.5 sm:size-8 items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 cursor-pointer">
                    <flux:icon.information-circle class="size-4" />
                </button>
            </div>
        </div>

    </div>

    <!-- Hijos Directos (Si está expandido) -->
    @if ($hasChildren && $isExpanded)
        <div class="flex flex-col">
            @foreach ($node['children'] as $child)
                @include('livewire.office.network.partials.unilevel-list-item', [
                    'node' => $child,
                    'expandedNodes' => $expandedNodes,
                ])
            @endforeach
        </div>
    @endif
</div>
