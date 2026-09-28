<div>
    <flux:modal.trigger name="financiera">
        <flux:button variant="primary" icon="calculator" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm">
            {{ __('Calculadora Financiera de Puntos') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="financiera" class="max-w-4xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100">
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <flux:heading size="lg" class="text-ink dark:text-zinc-50 font-bold flex items-center gap-2">
                    <flux:icon.calculator class="size-5 text-primary dark:text-zinc-300" />
                    {{ __('Calculadora Financiera de Puntos MLM') }}
                </flux:heading>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3">
                    {{ __('1. Parámetros Base de Venta') }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="col-span-1">
                        <x-input wire:model.blur="precioPublico" type="number" step="any" label="P. Público:" required />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="valorProducto" type="number" step="any" label="Costo Base:" required />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="descuentoPrecioPublicoPorcentaje" type="number" step="any" label="Dto. (%)" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="ivaPorcentaje" type="number" step="any" label="IVA (%)" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="empresaPorcentaje" type="number" step="any" label="Empresa %" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="bonoInicioPorcentaje" type="number" step="any" label="Bono Inicio %" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3">
                    {{ __('2. Pasarela de Pagos & Valor Punto') }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="col-span-1">
                        <x-input wire:model.blur="tarifaPasarelaPorcentaje" type="number" step="any" label="Pasarela %" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="tarifaFijaPasarela" type="number" step="any" label="Tarifa Fija" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="reteIcaPorcentaje" type="number" step="any" label="ReteICA %" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="reteRentaPorcentaje" type="number" step="any" label="ReteRenta %" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="valorDolar" type="number" step="any" label="COP x Pts ($)" />
                    </div>
                    <div class="col-span-1">
                        <x-input wire:model.blur="binarioPorcentaje" type="number" step="any" label="Binario %" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950/50">
                    <p class="text-xs font-bold uppercase text-primary dark:text-zinc-400 mb-2">{{ __('Sin Bono Inicio') }}</p>
                    <ul class="text-xs space-y-1 text-zinc-700 dark:text-zinc-300">
                        <li><strong>Pts Base:</strong> {{ number_format($ptsBase, 2) }}</li>
                        <li><strong>Ganancia Empresa:</strong> ${{ number_format($gananciaEmpresa, 0) }}</li>
                        <li><strong>Saldo a Repartir:</strong> ${{ number_format($saldoSinBono, 0) }}</li>
                    </ul>
                </div>

                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950/50">
                    <p class="text-xs font-bold uppercase text-secondary dark:text-zinc-400 mb-2">{{ __('Con Bono Inicio') }}</p>
                    <ul class="text-xs space-y-1 text-zinc-700 dark:text-zinc-300">
                        <li><strong>Pts Bono:</strong> {{ number_format($ptsConBono, 2) }}</li>
                        <li><strong>Ganancia Empresa:</strong> ${{ number_format($gananciaEmpresa, 0) }}</li>
                        <li><strong>Saldo a Repartir:</strong> ${{ number_format($saldoConBono, 0) }}</li>
                    </ul>
                </div>

                <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950/50">
                    <p class="text-xs font-bold uppercase text-premium dark:text-zinc-400 mb-2">{{ __('Distribución / Dto') }}</p>
                    <ul class="text-xs space-y-1 text-zinc-700 dark:text-zinc-300">
                        <li><strong>Pts Distribución:</strong> {{ number_format($ptsDistribucion, 2) }}</li>
                        <li><strong>Bolsa Global:</strong> {{ number_format($ptsBolsaGlobal, 2) }} pts</li>
                        <li><strong>Generacional:</strong> {{ number_format($ptsGeneracional, 2) }} pts</li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button x-on:click="$dispatch('modal-close', 'financiera')" variant="ghost">
                    {{ __('Cerrar') }}
                </flux:button>
                <flux:button wire:click="agregar" x-on:click="$dispatch('modal-close', 'financiera')" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm">
                    {{ __('Aplicar Valores al Formulario') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
