<div class="space-y-6 max-w-6xl mx-auto py-4">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">
                Liquidaciones y Cierre Mensual MLM
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Monitorea el ciclo contable actual, realiza pre-liquidaciones, gestiona activaciones manuales y ejecuta el cierre con Flush Total.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openManualActivationModal"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-ink dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4 text-premium" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Activar Usuario Manual
            </button>

            <a href="{{ route('admin.mlm.settings') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Reglas y Metas
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-primary/10 border border-primary/30 text-ink dark:text-zinc-100 flex items-center gap-3">
            <svg class="w-5 h-5 text-primary dark:text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('status') }}</span>
        </div>
    @endif

    <!-- Tarjeta del Periodo Activo en Curso -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ciclo Activo
                    </span>
                    <span class="text-xs font-mono text-zinc-400">Código: {{ $this->activePeriod->code }}</span>
                </div>
                <h2 class="text-2xl font-black text-ink dark:text-zinc-100">
                    {{ $this->activePeriod->name }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Desde: <strong class="text-ink dark:text-zinc-200">{{ $this->activePeriod->starts_at?->format('d/m/Y') }}</strong>
                    hasta: <strong class="text-ink dark:text-zinc-200">{{ $this->activePeriod->ends_at?->format('d/m/Y') }}</strong>
                    • Meta Calificación: <strong class="text-primary dark:text-secondary font-bold">{{ $this->activePeriod->min_activation_pts }} Pts</strong>
                </p>
            </div>

            <!-- Acciones del Ciclo -->
            <div class="flex flex-wrap items-center gap-3">
                <button wire:click="runPreview"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs text-ink dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 active:scale-[0.98] transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-primary dark:text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Simular Pre-Liquidación
                </button>

                <button wire:click="$set('showConfirmSettlementModal', true)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-xs text-white bg-danger hover:bg-danger/90 active:scale-[0.98] shadow-md shadow-ink/70 dark:shadow-none transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Cerrar Mes y Flush Total
                </button>
            </div>
        </div>

        <div class="mt-6 border-t border-zinc-100 dark:border-zinc-800/80 pt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-zinc-500 dark:text-zinc-400">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                <span>Plan Binario: <strong>Flush Total (0 - 0)</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-premium"></span>
                <span>Calificación: <strong>1.80 Pts Acumulativos</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                <span>Auditoría: <strong>Inmutable por Periodo</strong></span>
            </div>
        </div>
    </div>

    <!-- Historial de Periodos Liquidados -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-md shadow-ink/70 dark:shadow-none">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-ink dark:text-zinc-100">
                Historial de Meses Cerrados y Liquidados
            </h2>
            <span class="text-xs text-zinc-400">Últimos 12 periodos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 uppercase tracking-wider font-semibold border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3.5">Periodo</th>
                        <th class="px-6 py-3.5">Rango de Fechas</th>
                        <th class="px-6 py-3.5">Meta Pts</th>
                        <th class="px-6 py-3.5">Afiliados</th>
                        <th class="px-6 py-3.5">Total Liquidado</th>
                        <th class="px-6 py-3.5">Fecha Cierre</th>
                        <th class="px-6 py-3.5">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                    @forelse ($this->settledPeriods as $period)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-ink dark:text-zinc-100">
                                {{ $period->name }}
                                <span class="block text-[11px] text-zinc-400 font-normal">{{ $period->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $period->starts_at?->format('d/m/Y') }} - {{ $period->ends_at?->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-medium">
                                {{ $period->min_activation_pts }} Pts
                            </td>
                            <td class="px-6 py-4 font-semibold text-ink dark:text-zinc-200">
                                {{ number_format($period->user_balances_count) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-ink dark:text-zinc-100 block">
                                    ${{ number_format((float) $period->total_payout, 2) }}
                                </span>
                                <span class="text-[10px] text-zinc-400">
                                    Bin: ${{ number_format((float) $period->total_commission_binary, 0) }} • Soc: ${{ number_format((float) $period->total_commission_strategic_partner, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">
                                {{ $period->settled_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                    Cerrado e Inmutable
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-400">
                                Aún no se han ejecutado cierres de mes. El ciclo actual está en curso.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: Simulación / Pre-Liquidación -->
    @if ($showPreviewModal && $previewData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-base font-bold text-ink dark:text-zinc-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Pre-Liquidación: {{ $previewData['period_name'] }}
                    </h3>
                    <button wire:click="$set('showPreviewModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                        <span class="text-zinc-500 dark:text-zinc-400 block mb-0.5">Afiliados Totales</span>
                        <strong class="text-base text-ink dark:text-zinc-100">{{ number_format($previewData['total_users']) }}</strong>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-emerald-700 dark:text-emerald-400 block mb-0.5">Afiliados Calificados (Activos)</span>
                        <strong class="text-base text-emerald-600 dark:text-emerald-300">{{ number_format($previewData['active_users']) }}</strong>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
                        <span class="text-zinc-500 dark:text-zinc-400 block mb-0.5">Afiliados Inactivos</span>
                        <strong class="text-base text-zinc-600 dark:text-zinc-300">{{ number_format($previewData['inactive_users']) }}</strong>
                    </div>

                    <div class="p-3 rounded-xl bg-primary/10 border border-primary/20">
                        <span class="text-primary dark:text-secondary block mb-0.5">Puntos Binario a Cobro</span>
                        <strong class="text-base text-primary dark:text-secondary">{{ number_format($previewData['total_binary_points_matched'], 2) }} Pts</strong>
                    </div>
                </div>

                <!-- Desglose de Comisiones por Concepto -->
                <div class="p-4 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Puntos Personales Movidos:</span>
                        <strong class="text-ink dark:text-zinc-100">{{ number_format($previewData['total_personal_points'], 2) }} Pts</strong>
                    </div>
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-300">1. Comisión Red Binaria:</span>
                            <strong class="text-ink dark:text-zinc-100 font-mono">${{ number_format($previewData['estimated_binary_commissions'], 2) }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-300">2. Comisión Socios Estratégicos (Comercios):</span>
                            <strong class="text-ink dark:text-zinc-100 font-mono">${{ number_format($previewData['estimated_strategic_partner_commissions'] ?? 0, 2) }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-300">3. Comisión por Rango:</span>
                            <strong class="text-ink dark:text-zinc-100 font-mono">${{ number_format($previewData['estimated_rank_commissions'] ?? 0, 2) }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-300">4. Otros Bonos / Ajustes:</span>
                            <strong class="text-ink dark:text-zinc-100 font-mono">${{ number_format($previewData['estimated_other_commissions'] ?? 0, 2) }}</strong>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2 border-t border-zinc-200 dark:border-zinc-700 text-sm">
                        <span class="font-bold text-ink dark:text-zinc-100">Total Liquidación Estimada:</span>
                        <strong class="text-primary dark:text-secondary font-black">${{ number_format($previewData['estimated_total_payout'] ?? $previewData['estimated_binary_commissions'], 2) }}</strong>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showPreviewModal', false)"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                        Cerrar Simulación
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Confirmación de Cierre y Flush Total -->
    @if ($showConfirmSettlementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 border border-danger/30 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="w-12 h-12 rounded-2xl bg-danger/10 text-danger flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <div class="text-center space-y-1.5">
                    <h3 class="text-lg font-black text-ink dark:text-zinc-100">
                        ¿Ejecutar Cierre y Flush Total?
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Esta acción es <strong>definitiva e irreversible</strong>. Se liquidarán las comisiones del periodo <strong class="text-ink dark:text-zinc-200">{{ $this->activePeriod->name }}</strong>, se guardará el snapshot histórico y <strong>los puntos de todos los usuarios quedarán en 0</strong> para abrir el nuevo mes.
                    </p>
                </div>

                <div class="p-3 bg-danger/5 rounded-xl border border-danger/20 text-[11px] text-danger space-y-1">
                    <p>• Los usuarios activos cobrarán sus comisiones correspondientes.</p>
                    <p>• El binario se reseteará a 0 en ambas piernas (Flush Total).</p>
                    <p>• Los usuarios sin mes de gracia arrancarán inactivos el nuevo ciclo.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button wire:click="$set('showConfirmSettlementModal', false)"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                        Cancelar
                    </button>

                    <button wire:click="executeSettlement"
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-danger hover:bg-danger/90 active:scale-[0.98] shadow-md shadow-ink/70 dark:shadow-none transition-all cursor-pointer">
                        <span wire:loading.remove wire:target="executeSettlement">Sí, Liquidar y Resetear a Cero</span>
                        <span wire:loading wire:target="executeSettlement">Procesando Cierre...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Activación Manual por Administrador -->
    @if ($showManualActivationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-base font-bold text-ink dark:text-zinc-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-premium" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Activar Afiliado Manualmente
                    </h3>
                    <button wire:click="$set('showManualActivationModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Buscador de usuario -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">
                        Buscar Afiliado (Usuario, Nombre o Email)
                    </label>
                    <input type="text"
                           wire:model.live.debounce.300ms="userSearch"
                           placeholder="Escribe el nombre o @usuario..."
                           class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-ink dark:text-zinc-100 px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none">

                    @if ($this->searchResults->isNotEmpty())
                        <div class="mt-2 border border-zinc-200 dark:border-zinc-700 rounded-xl divide-y divide-zinc-200 dark:divide-zinc-700 max-h-48 overflow-y-auto">
                            @foreach ($this->searchResults as $u)
                                <div wire:click="selectUser({{ $u->id }})"
                                     class="p-2.5 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer transition-colors {{ $selectedUserId === $u->id ? 'bg-primary/10 dark:bg-secondary/10' : '' }}">
                                    <div>
                                        <p class="font-bold text-xs text-ink dark:text-zinc-100">
                                            {{ $u->name }} <span class="font-normal text-zinc-400">(@ {{ $u->username }})</span>
                                        </p>
                                        <p class="text-[11px] text-zinc-400">
                                            Pts Personales: {{ $u->unilevelSummary?->personal_points ?? '0' }} • Estado: 
                                            <span class="{{ $u->activation?->is_active ? 'text-emerald-500' : 'text-zinc-400' }}">
                                                {{ $u->activation?->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </p>
                                    </div>
                                    @if ($selectedUserId === $u->id)
                                        <span class="text-primary dark:text-secondary text-xs font-bold">✓ Seleccionado</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($selectedUserId)
                    <!-- Selector de Vigencia -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">
                            Vigencia de la Activación
                        </label>
                        <select wire:model="manualExpiration"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-ink dark:text-zinc-100 px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none">
                            <option value="end_of_month">Hasta el fin del mes actual (Cierre del ciclo)</option>
                            <option value="end_of_next_month">Mes actual + Todo el siguiente mes (Con mes de gracia)</option>
                        </select>
                    </div>

                    <!-- Motivo para auditoría -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">
                            Motivo / Nota Administrativa (Auditoría)
                        </label>
                        <textarea wire:model="manualAdminNotes"
                                  rows="2"
                                  placeholder="Ej: Aprobación por comprobante bancario externo, cortesía de bienvenida..."
                                  class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-ink dark:text-zinc-100 px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <button wire:click="$set('showManualActivationModal', false)"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                        Cancelar
                    </button>

                    <button wire:click="applyManualActivation"
                            @if (! $selectedUserId) disabled @endif
                            class="px-5 py-2 rounded-xl font-bold text-xs text-white bg-primary hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-ink/70 dark:shadow-none transition-all cursor-pointer">
                        Confirmar Activación
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
