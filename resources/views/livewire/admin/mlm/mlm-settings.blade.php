<div class="space-y-6 max-w-4xl mx-auto py-4">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">
                Reglas de Calificación y Mes de Gracia MLM
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Configura los requisitos de puntos personales mínimos mensuales para calificar a comisiones y la política para nuevos afiliados.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.mlm.settlements') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-primary dark:text-secondary bg-primary/10 dark:bg-secondary/10 hover:bg-primary/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Ir a Liquidaciones y Cierres
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

    <form wire:submit="save" class="space-y-6">
        <!-- Tarjeta de Puntos Mínimos Mensuales -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary dark:text-secondary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-ink dark:text-zinc-100">
                        Puntos Personales Mínimos Mensuales
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Volumen personal acumulativo requerido por mes para que un afiliado pase a estado <strong class="text-ink dark:text-zinc-200">ACTIVO</strong> y tenga derecho a comisionar en el binario y escalonado.
                    </p>
                </div>
            </div>

            <div class="pt-2 max-w-xs">
                <label for="min_pts_monthly" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                    Meta de Puntos (Pts)
                </label>
                <div class="relative">
                    <input type="number"
                           step="0.01"
                           min="0.01"
                           id="min_pts_monthly"
                           wire:model="min_pts_monthly"
                           class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-ink dark:text-zinc-100 px-3.5 py-2.5 text-base font-semibold focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-secondary focus:border-transparent transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs font-bold text-zinc-400">
                        PTS
                    </div>
                </div>
                @error('min_pts_monthly')
                    <p class="text-xs text-danger mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5">
                    Valor por defecto del plan: <strong>1.80 Pts</strong>. Si un afiliado hace 0.80 y luego 1.20, suma 2.00 pts y se activa automáticamente.
                </p>
            </div>
        </div>

        <!-- Tarjeta de Mes de Gracia para Nuevos -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-md shadow-ink/70 dark:shadow-none space-y-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-premium/10 text-premium flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-ink dark:text-zinc-100">
                            Mes de Gracia para Nuevos Afiliados
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Permite que los usuarios que se activan por primera vez disfruten de su estatus activo durante el mes en curso más el siguiente mes calendario completo.
                        </p>
                    </div>
                </div>

                <!-- Switch Interactivo -->
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox"
                           wire:model.live="first_activation_grace_period_enabled"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-zinc-300 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-secondary"></div>
                </label>
            </div>

            @if ($first_activation_grace_period_enabled)
                <div class="border-t border-zinc-100 dark:border-zinc-800/80 pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs bg-zinc-50 dark:bg-zinc-800/40 p-4 rounded-xl">
                    <div>
                        <p class="font-semibold text-ink dark:text-zinc-200">
                            🟢 Beneficio de Bienvenida Activado
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Si un afiliado nuevo califica en Septiembre, mantendrá su estatus ACTIVO durante todo Octubre automáticamente, incluso tras el Flush Total.
                        </p>
                    </div>
                </div>
            @else
                <div class="border-t border-zinc-100 dark:border-zinc-800/80 pt-4 text-xs bg-danger/5 border-l-4 border-danger p-4 rounded-r-xl text-zinc-600 dark:text-zinc-300">
                    <strong class="text-danger">⚪ Beneficio Desactivado:</strong> Todos los afiliados (nuevos y antiguos) deberán volver a calificar con {{ $min_pts_monthly }} Pts cada mes para cobrar comisiones.
                </div>
            @endif
        </div>

        <!-- Botón de Guardar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm text-white bg-primary hover:bg-secondary active:scale-[0.98] shadow-md shadow-ink/70 dark:shadow-none transition-all cursor-pointer">
                <span wire:loading.remove wire:target="save">Guardar Cambios</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
