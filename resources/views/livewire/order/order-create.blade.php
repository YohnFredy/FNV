<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Breadcrumbs -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>{{ __('Inicio') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('products.cart') }}" wire:navigate>{{ __('Carrito de Compras') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Finalizar Compra') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Encabezado de la Página -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-zinc-200/80 dark:border-zinc-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-ink dark:text-zinc-50">
                {{ __('Finalizar Compra y Facturación') }}
            </h1>
            <p class="text-xs sm:text-sm text-ink/65 dark:text-zinc-400 mt-1">
                {{ __('Verifica y completa tus datos para procesar tu orden de forma segura y confiable.') }}
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold w-fit">
            <flux:icon.lock-closed class="size-4" />
            <span>{{ __('Transacción 100% Protegida') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Columna de Formularios (8 cols en desktop) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- 1. Datos de Facturación -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                    <h2 class="text-xl font-extrabold text-ink dark:text-zinc-100 flex items-center gap-3">
                        <span class="p-2.5 rounded-2xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200 shrink-0">
                            <flux:icon.document-text class="size-6 text-primary" />
                        </span>
                        <span>{{ __('Datos de Facturación Legal') }}</span>
                    </h2>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-zinc-100 text-ink/60 dark:bg-zinc-800 dark:text-zinc-400">
                        {{ __('Paso 1 de 2') }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl border border-primary/20 bg-primary/5 flex items-start gap-3.5">
                    <flux:icon.information-circle class="size-5 text-primary shrink-0 mt-0.5" />
                    <p class="text-xs sm:text-sm text-ink/80 dark:text-zinc-300 leading-relaxed">
                        {{ __('Tus datos han sido preseleccionados automáticamente con base en tu perfil registrado. Puedes actualizarlos si requieres facturación a otro nombre.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <x-input wire:model.blur="name" label="Nombre completo / Razón social:" type="text"
                            for="name" required autofocus autocomplete="name"
                            placeholder="Nombre a quien se le factura" />
                    </div>

                    <div>
                        <x-select-l wire:model.blur="document_type" label="Tipo de documento:" for="document_type">
                            @foreach ($documentTypes as $doc)
                                <option value="{{ $doc->id }}" @selected($document_type == $doc->id)>{{ $doc->name }}</option>
                            @endforeach
                        </x-select-l>
                    </div>

                    <div>
                        <x-input wire:model.blur="document" label="Número de documento:" type="text" for="document"
                            required placeholder="Número de documento fiscal" />
                    </div>

                    <div>
                        <x-input wire:model.blur="email" label="Correo electrónico de facturación:" type="email" for="email"
                            required autocomplete="email" placeholder="correo@ejemplo.com" />
                    </div>

                    <div>
                        <x-input wire:model.blur="phone" label="Teléfono de contacto:" type="tel" for="phone"
                            required placeholder="Ej: +57 300 123 4567" />
                    </div>

                    <!-- País de Facturación -->
                    <div>
                        <x-select wire:model.live="selectedCountry" label="País:" for="selectedCountry"
                            placeholder="Seleccione un país..." required>
                            @foreach ($countries as $c)
                                <option value="{{ $c->id }}" @selected($selectedCountry == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Departamento de Facturación -->
                    @if ($selectedCountry)
                        <div>
                            <x-select wire:model.live="selectedDepartment" label="{{ $division1 }}:"
                                for="selectedDepartment" placeholder="Seleccione {{ mb_strtolower($division1) }}..."
                                required>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" @selected($selectedDepartment == $dept->id)>{{ $dept->name }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <!-- Ciudad de Facturación -->
                        @if ($selectedDepartment)
                            @if (count($cities) > 0)
                                <div>
                                    <x-select wire:model.live="selectedCity" label="{{ $division2 }}:"
                                        for="selectedCity" placeholder="Seleccione {{ mb_strtolower($division2) }}..."
                                        required>
                                        @foreach ($cities as $ct)
                                            <option value="{{ $ct->id }}" @selected($selectedCity == $ct->id)>{{ $ct->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>

                                @if (count($parishes) > 0)
                                    <div>
                                        <x-select wire:model.live="selectedParish" label="{{ $division3 }}:"
                                            for="selectedParish" placeholder="Seleccione {{ mb_strtolower($division3) }}...">
                                            @foreach ($parishes as $p)
                                                <option value="{{ $p->id }}" @selected($selectedParish == $p->id)>{{ $p->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                @endif
                            @else
                                <div>
                                    <x-input wire:model="city" id="city" label="{{ $division2 }}:"
                                        type="text" for="city" autofocus autocomplete="city"
                                        placeholder="{{ $division2 }}" />
                                </div>
                            @endif
                        @endif
                    @endif

                    <div class="sm:col-span-2">
                        <x-input wire:model.blur="address" label="Dirección fiscal o residencia:" type="text" for="address" required
                            placeholder="Calle, carrera, número, barrio, conjunto" />
                    </div>
                </div>
            </div>

            <!-- 2. Destinatario Diferente (Opcional) -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-8" x-data="{ expanded: @entangle('shippingDifferent') }">
                <label class="flex items-start gap-4 cursor-pointer select-none">
                    <input wire:model.live="shippingDifferent" type="checkbox"
                        class="size-5 rounded-lg border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary mt-0.5 cursor-pointer accent-primary shrink-0">
                    <div class="flex-1">
                        <span class="text-base font-bold text-ink dark:text-zinc-100">
                            {{ __('¿El paquete lo recibe otra persona?') }}
                        </span>
                        <p class="text-xs sm:text-sm text-ink/65 dark:text-zinc-400 mt-0.5 leading-relaxed">
                            {{ __('Activa esta casilla solo si quien recibe físicamente la encomienda es diferente a la persona o empresa titular de la factura.') }}
                        </p>
                    </div>
                </label>

                <div x-show="expanded" x-collapse x-transition class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <x-input wire:model.blur="shipping_name" label="Nombre completo de quien recibe:" type="text"
                                for="shipping_name" required autofocus autocomplete="shipping_name"
                                placeholder="Nombre y apellidos" />
                        </div>

                        <div>
                            <x-select-l wire:model.blur="shipping_document_type" label="Tipo de documento:"
                                for="shipping_document_type">
                                @foreach ($shipping_documentTypes as $doc)
                                    <option value="{{ $doc->id }}" @selected($shipping_document_type == $doc->id)>{{ $doc->name }}</option>
                                @endforeach
                            </x-select-l>
                        </div>

                        <div>
                            <x-input wire:model.blur="shipping_document" label="Número de documento de quien recibe:"
                                type="text" for="shipping_document" required
                                placeholder="Número de documento" />
                        </div>

                        <div>
                            <x-input wire:model.blur="shipping_phone" label="Teléfono de contacto para la entrega:"
                                type="tel" for="shipping_phone" required
                                placeholder="Ej: +57 300 123 4567" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Dirección de Entrega y Envío -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                    <h2 class="text-xl font-extrabold text-ink dark:text-zinc-100 flex items-center gap-3">
                        <span class="p-2.5 rounded-2xl bg-secondary/15 text-secondary dark:bg-zinc-800 dark:text-zinc-200 shrink-0">
                            <flux:icon.map-pin class="size-6 text-secondary" />
                        </span>
                        <span>{{ __('Destino y Dirección de Envío') }}</span>
                    </h2>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-zinc-100 text-ink/60 dark:bg-zinc-800 dark:text-zinc-400">
                        {{ __('Paso 2 de 2') }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl border border-secondary/25 bg-secondary/5 flex items-start gap-3.5">
                    <flux:icon.truck class="size-5 text-secondary shrink-0 mt-0.5" />
                    <p class="text-xs sm:text-sm text-ink/80 dark:text-zinc-300 leading-relaxed">
                        {{ __('Enviamos a nivel nacional. La transportadora utilizará estos datos para realizar la entrega. El valor del flete es cancelado contra entrega.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <!-- País de Envío -->
                    <div>
                        <x-select wire:model.live="shipping_selectedCountry" label="País de Entrega:"
                            for="shipping_selectedCountry" placeholder="Seleccione un país..." required>
                            @foreach ($shipping_countries as $sc)
                                <option value="{{ $sc->id }}" @selected($shipping_selectedCountry == $sc->id)>{{ $sc->name }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Departamento de Envío -->
                    @if ($shipping_selectedCountry)
                        <div>
                            <x-select wire:model.live="shipping_selectedDepartment"
                                label="{{ $shipping_division1 }}:" for="shipping_selectedDepartment"
                                placeholder="Seleccione {{ mb_strtolower($shipping_division1) }}..." required>
                                @foreach ($shipping_departments as $sdept)
                                    <option value="{{ $sdept->id }}" @selected($shipping_selectedDepartment == $sdept->id)>{{ $sdept->name }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <!-- Ciudad de Envío -->
                        @if ($shipping_selectedDepartment)
                            @if (count($shipping_cities) > 0)
                                <div>
                                    <x-select wire:model.live="shipping_selectedCity"
                                        label="{{ $shipping_division2 }}:" for="shipping_selectedCity"
                                        placeholder="Seleccione {{ mb_strtolower($shipping_division2) }}..." required>
                                        @foreach ($shipping_cities as $sct)
                                            <option value="{{ $sct->id }}" @selected($shipping_selectedCity == $sct->id)>{{ $sct->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>

                                @if (count($shipping_parishes) > 0)
                                    <div>
                                        <x-select wire:model.live="shipping_selectedParish"
                                            label="{{ $shipping_division3 }}:" for="shipping_selectedParish"
                                            placeholder="Seleccione {{ mb_strtolower($shipping_division3) }}...">
                                            @foreach ($shipping_parishes as $sp)
                                                <option value="{{ $sp->id }}" @selected($shipping_selectedParish == $sp->id)>{{ $sp->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                @endif
                            @else
                                <div>
                                    <x-input wire:model.blur="shipping_city" id="shipping_city"
                                        label="{{ $shipping_division2 }}:" type="text" for="shipping_city"
                                        required autocomplete="shipping_city"
                                        placeholder="Nombre de la {{ $shipping_division2 }}" />
                                </div>
                            @endif
                        @endif
                    @endif

                    <div class="sm:col-span-2">
                        <x-input wire:model.blur="shipping_address" label="Dirección exacta de entrega:" type="text"
                            for="shipping_address" required placeholder="Calle, carrera, número, conjunto, torre, apartamento" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input wire:model.blur="shipping_additional_address" label="Indicaciones adicionales de entrega (opcional):"
                            type="text" for="shipping_additional_address"
                            placeholder="Ej: Dejar en portería, casa esquinera color blanco, llamar antes de llegar" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Resumen de Orden y Pago (4 cols en desktop) -->
        <div class="lg:col-span-4">
            <div class="sticky top-6 space-y-6">
                <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:border-zinc-800 overflow-hidden">
                    <!-- Cabecera Resumen -->
                    <div class="p-6 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/40">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-extrabold text-ink dark:text-zinc-100 flex items-center gap-2.5">
                                <flux:icon.receipt-percent class="size-5 text-primary" />
                                {{ __('Resumen de la Orden') }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                                {{ $totals['quantity'] }} {{ $totals['quantity'] == 1 ? __('artículo') : __('artículos') }}
                            </span>
                        </div>
                    </div>

                    <!-- Lista de Productos en la Orden -->
                    @if (count($productItems) > 0)
                    <div class="p-6 border-b border-zinc-200/80 dark:border-zinc-800 space-y-3 max-h-56 overflow-y-auto scrollbar-thin">
                        @foreach ($productItems as $item)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-ink dark:text-zinc-200 truncate">{{ $item['name'] }}</p>
                                <p class="text-ink/60 dark:text-zinc-400">
                                    {{ $item['quantity'] }} &times; ${{ formatear_precio($item['price']) }}
                                    @if ($item['discount_percent'] > 0)
                                    <span class="text-premium font-semibold">(-{{ $item['discount_percent'] }}%)</span>
                                    @endif
                                </p>
                            </div>
                            <span class="font-extrabold text-ink dark:text-zinc-100">
                                ${{ formatear_precio(($item['price'] * $item['quantity']) - (($item['price'] * $item['quantity'] * $item['discount_percent']) / 100)) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Desglose de Totales -->
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                            <span class="font-medium">{{ __('Subtotal') }}</span>
                            <span class="font-bold text-ink dark:text-zinc-100">${{ formatear_precio($totals['subtotal']) }}</span>
                        </div>

                        @if ($totals['descuento'] > 0)
                            <div class="flex justify-between items-center text-premium font-bold bg-premium/10 p-2.5 rounded-xl border border-premium/20">
                                <span class="flex items-center gap-1.5">
                                    <flux:icon.tag class="size-4" />
                                    {{ __('Descuento Aplicado') }}
                                </span>
                                <span>-${{ formatear_precio($totals['descuento']) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                            <span class="font-medium">{{ __('Total Bruto') }}</span>
                            <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($totals['total_bruto_factura']) }}</span>
                        </div>

                        @if ($totals['iva'] > 0)
                            <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                                <span class="font-medium">{{ __('IVA') }}</span>
                                <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($totals['iva']) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-ink/70 dark:text-zinc-400">
                            <span class="font-medium flex items-center gap-1">
                                <flux:icon.truck class="size-3.5 text-secondary" />
                                {{ __('Envío') }}
                            </span>
                            @if ($shipping_cost > 0)
                                <span class="font-semibold text-ink dark:text-zinc-100">${{ formatear_precio($shipping_cost) }}</span>
                            @else
                                <span class="text-xs font-semibold text-secondary bg-secondary/10 px-2 py-0.5 rounded-md">
                                    {{ __('Contra entrega') }}
                                </span>
                            @endif
                        </div>

                        @if (isset($totals['total_pts']) && $totals['total_pts'] > 0)
                            <div class="py-2.5 px-3.5 rounded-2xl bg-premium/15 border border-premium/30 flex items-center justify-between shadow-xs">
                                <span class="text-xs font-bold text-premium flex items-center gap-1.5">
                                    <flux:icon.star class="size-4 fill-current" />
                                    {{ __('Puntos Calificables:') }}
                                </span>
                                <span class="text-sm font-black text-premium">+{{ number_format($totals['total_pts'], 2) }} PTS</span>
                            </div>
                        @endif

                        <!-- Total Final a Pagar -->
                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-baseline">
                            <div>
                                <span class="text-sm font-bold text-ink dark:text-zinc-100 block">{{ __('Total a Pagar') }}</span>
                                <span class="text-[11px] text-ink/50 dark:text-zinc-500 font-medium">{{ __('Impuestos incluidos') }}</span>
                            </div>
                            <span class="text-2xl sm:text-3xl font-black text-primary dark:text-zinc-50 tracking-tight">
                                ${{ formatear_precio($totals['total_factura'] + $shipping_cost) }}
                            </span>
                        </div>
                    </div>

                    <!-- Términos, Checkbox y Botón de Pago -->
                    <div class="p-6 bg-zinc-50/90 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800 space-y-4">
                        <!-- Tarjeta Destacada del Checkbox de Términos y Condiciones -->
                        <div class="p-4 rounded-2xl border-2 {{ $errors->has('terms') ? 'border-danger/70 bg-danger/5 ring-2 ring-danger/20' : 'border-primary/30 bg-primary/5 hover:border-primary/60 dark:border-primary/40 dark:bg-primary/10' }} transition-all shadow-xs">
                            <div class="flex items-start">
                                <flux:checkbox wire:model="terms" required id="terms" class="mt-0.5 !accent-primary cursor-pointer text-primary" />
                                <div class="ml-2.5 flex-1">
                                    @livewire('purchase-policy-and-conditions')
                                </div>
                            </div>
                        </div>

                        @error('terms')
                            <p class="text-xs text-danger font-bold flex items-center gap-1.5">
                                <flux:icon.exclamation-circle class="size-4" />
                                {{ $message }}
                            </p>
                        @enderror

                        @if (session()->has('error'))
                            <p class="text-xs text-danger font-bold p-3 rounded-xl bg-danger/10 border border-danger/20">
                                {{ session('error') }}
                            </p>
                        @endif

                        <!-- Botón Principal Pagar Ahora -->
                        <button
                            type="button"
                            wire:click="create_order"
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-2xl bg-primary hover:bg-secondary active:scale-[0.99] text-white font-extrabold text-base tracking-wide shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed group">
                            <span wire:loading.remove wire:target="create_order" class="inline-flex items-center gap-2.5">
                                <flux:icon.lock-closed class="size-5 text-white/90 group-hover:scale-110 transition-transform" />
                                <span>{{ __('Pagar Ahora') }}</span>
                                <flux:icon.arrow-right class="size-4 text-white/80 group-hover:translate-x-1 transition-transform" />
                            </span>

                            <span wire:loading wire:target="create_order" class="inline-flex items-center gap-2.5">
                                <svg class="animate-spin size-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('Procesando pago seguro...') }}</span>
                            </span>
                        </button>

                        <div class="flex items-center justify-center gap-4 text-[11px] text-ink/60 dark:text-zinc-400 pt-1">
                            <span class="flex items-center gap-1">
                                <flux:icon.shield-check class="size-3.5 text-primary" />
                                {{ __('Pago Seguro') }}
                            </span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <flux:icon.truck class="size-3.5 text-secondary" />
                                {{ __('Envío Garantizado') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
