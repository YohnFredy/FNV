<div x-data="{ showTerms: false, showContract: false }" class="flex flex-col gap-6">

    @if ($isMaster)
    <!-- Alerta para el primer registro (Nodo Maestro) -->
    <div class="rounded-xl border border-primary/30 bg-primary/5 p-4 text-ink dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 shadow-2xs">
        <div class="flex items-center gap-2 font-bold text-primary dark:text-zinc-100">
            <flux:icon.sparkles class="size-5" />
            <span>{{ __('👑 Registro Maestro del Sistema (Nodo Raíz)') }}</span>
        </div>
        <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
            {{ __('Este es el primer registro de la plataforma. Esta cuenta será la cabeza y raíz del Árbol Binario y del Árbol Escalonado. No requiere patrocinador.') }}
        </p>
    </div>
    @else
    <!-- Información de afiliación con patrocinador -->
    <div class="rounded-xl border border-primary/20 bg-primary/5 p-4 text-ink dark:border-zinc-800 dark:bg-zinc-900/60 dark:text-zinc-300 shadow-2xs">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 font-semibold text-primary dark:text-zinc-100">
                <flux:icon.user-group class="size-5" />
                <span>{{ __('Afiliación de Nuevo Miembro') }}</span>
            </div>
            <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary dark:bg-zinc-800 dark:text-zinc-200">
                🔒 {{ __('Datos de Red Bloqueados') }}
            </span>
        </div>
        <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
            {{ __('Tu patrocinador y pierna de colocación han sido fijados por el enlace de invitación y no pueden ser alterados.') }}
        </p>

        @auth
        <div class="mt-3 flex items-start gap-2 rounded-lg border border-primary/20 bg-white/80 p-2.5 text-xs text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950/70 dark:text-zinc-300">
            <flux:icon.information-circle class="size-4 shrink-0 text-primary dark:text-zinc-400 mt-0.5" />
            <div>
                <span class="font-bold text-ink dark:text-zinc-100">{{ __('Sesión activa:') }} {{ auth()->user()->username }}</span>.
                {{ __('Estás registrando a un nuevo afiliado directamente. Tu cuenta actual no se verá afectada ni se cerrará.') }}
            </div>
        </div>
        @endauth
    </div>
    @endif

    @if (!$isMaster && $sponsorError)
    <!-- Alerta de Patrocinador Inhabilitado -->
    <div class="rounded-xl border border-danger/30 bg-danger/5 p-4 text-danger dark:border-danger/40 dark:bg-danger/10 dark:text-zinc-200 shadow-2xs">
        <div class="flex items-center gap-2 font-bold text-danger">
            <flux:icon.exclamation-triangle class="size-5 shrink-0" />
            <span>{{ __('Enlace de Afiliación No Disponible') }}</span>
        </div>
        <p class="mt-1 text-xs text-ink/80 dark:text-zinc-300">
            {{ $sponsorError }}
        </p>
    </div>
    @endif

    <form wire:submit="register" class="flex flex-col gap-6">

        @if (!$isMaster)
        <!-- ========================================================= -->
        <!-- SECCIÓN 1: PATROCINADOR Y COLOCACIÓN BINARIA (BLOQUEADOS) -->
        <!-- ========================================================= -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <flux:icon.share class="size-4 text-primary dark:text-zinc-300" />
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                        {{ __('Patrocinador y Colocación Binaria') }}
                    </h3>
                </div>
                <span class="flex items-center gap-1 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    <flux:icon.lock-closed class="size-3.5" />
                    {{ __('No editable') }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Patrocinador (Readonly / Bloqueado) -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex items-center justify-between">
                        <span>{{ __('Patrocinador (Sponsor)') }}</span>
                        <span class="text-[11px] text-zinc-400 font-normal">🔒 {{ __('Fijo') }}</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            value="{{ $sponsor_username }}"
                            readonly
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-100/80 px-3.5 py-2.5 text-sm font-semibold text-zinc-700 dark:border-zinc-800 dark:bg-zinc-800/80 dark:text-zinc-300 cursor-not-allowed select-none focus:outline-none" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                            <flux:icon.lock-closed class="size-4" />
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Usuario del patrocinador que te invitó a la red.') }}
                    </p>
                </div>

                <!-- Selector de Pierna Binaria (Bloqueado) -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex items-center justify-between">
                        <span>{{ __('Pierna Binaria Asignada') }}</span>
                        <span class="text-[11px] text-zinc-400 font-normal">🔒 {{ __('Fija') }}</span>
                    </label>

                    @if ($binary_leg === 'L')
                    <div class="flex items-center justify-center gap-2 rounded-xl border-2 border-primary bg-primary/5 p-3 text-xs font-bold text-primary dark:border-zinc-500 dark:bg-zinc-800 dark:text-zinc-100 shadow-2xs select-none">
                        <flux:icon.arrow-left class="size-4" />
                        <span>{{ __('Pierna Izquierda (Left)') }}</span>
                        <flux:icon.lock-closed class="size-3.5 ml-1 opacity-70" />
                    </div>
                    @else
                    <div class="flex items-center justify-center gap-2 rounded-xl border-2 border-secondary bg-secondary/5 p-3 text-xs font-bold text-secondary dark:border-zinc-500 dark:bg-zinc-800 dark:text-zinc-100 shadow-2xs select-none">
                        <flux:icon.lock-closed class="size-3.5 mr-1 opacity-70" />
                        <span>{{ __('Pierna Derecha (Right)') }}</span>
                        <flux:icon.arrow-right class="size-4" />
                    </div>
                    @endif

                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Posición asignada directamente por el enlace de registro.') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- ========================================================= -->
        <!-- SECCIÓN 2: DATOS PERSONALES Y DOCUMENTO DE IDENTIDAD -->
        <!-- ========================================================= -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <flux:icon.user class="size-4 text-primary dark:text-zinc-300" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                    {{ __('Datos Personales y Documento de Identidad') }}
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="{{ __('Nombres') }}" id="name" wire:model="name" required autocomplete="given-name" placeholder="Ej: Carlos Alberto" />
                <x-input label="{{ __('Apellidos') }}" id="last_name" wire:model="last_name" autocomplete="family-name" placeholder="Ej: Gómez Rodríguez" />

                <x-select label="{{ __('Tipo de Documento') }}" id="document_type_id" wire:model="document_type_id" placeholder="{{ __('Seleccione tipo...') }}">
                    @foreach ($this->documentTypes as $docType)
                    <option value="{{ $docType->id }}">
                        {{ $docType->name }} ({{ $docType->code }})
                    </option>
                    @endforeach
                </x-select>

                <x-input label="{{ __('Número de Documento / Cédula') }}" id="dni" wire:model="dni" placeholder="Ej: 1020304050" />

                <x-select label="{{ __('Género / Sexo') }}" id="sex" wire:model="sex" placeholder="{{ __('Seleccione género...') }}">
                    <option value="male">{{ __('Masculino') }}</option>
                    <option value="female">{{ __('Femenino') }}</option>
                    <option value="other">{{ __('Otro') }}</option>
                </x-select>

                <x-input type="date" label="{{ __('Fecha de Nacimiento') }}" id="birthdate" wire:model="birthdate" />
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- SECCIÓN 3: CONTACTO Y UBICACIÓN RESIDENCIAL (CASCADA) -->
        <!-- ========================================================= -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <flux:icon.map-pin class="size-4 text-primary dark:text-zinc-300" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                    {{ __('Información de Contacto y Ubicación Geográfica') }}
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input type="tel" label="{{ __('Teléfono / Celular / WhatsApp') }}" id="phone" wire:model="phone" placeholder="Ej: +57 300 123 4567" />

                <x-select label="{{ __('País') }}" id="country_id" wire:model.live="country_id" placeholder="{{ __('Seleccione país...') }}">
                    @foreach ($this->countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </x-select>

                <x-select :label="$this->divisionTerm1" id="department_id" wire:model.live="department_id" loading-target="country_id" placeholder="{{ __('Seleccione ') . mb_strtolower($this->divisionTerm1) }}...">
                    @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </x-select>

                <x-select :label="$this->divisionTerm2" id="city_id" wire:model.live="city_id" :disabled="!$department_id" loading-target="department_id" placeholder="{{ $department_id ? __('Seleccione ') . mb_strtolower($this->divisionTerm2) . '...' : __('Primero seleccione ') . mb_strtolower($this->divisionTerm1) }}">
                    @foreach ($this->cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </x-select>


                @if ($this->hasParishes)
                <x-select :label="$this->divisionTerm3" id="parish_id" wire:model="parish_id" placeholder="{{ __('Seleccione ') . mb_strtolower($this->divisionTerm3) }}...">
                    @foreach ($this->parishes as $parish)
                    <option value="{{ $parish->id }}">{{ $parish->name }}</option>
                    @endforeach
                </x-select>
                @endif

                <x-input label="{{ __('Dirección de Residencia') }}" id="address" wire:model="address" autocomplete="street-address" placeholder="Ej: Calle 100 # 15-20, Apto 402" class="sm:col-span-2" />
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- SECCIÓN 4: CREDENCIALES CON INTERACCIÓN INMEDIATA -->
        <!-- ========================================================= -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <flux:icon.lock-closed class="size-4 text-primary dark:text-zinc-300" />
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                    {{ __('Credenciales de Acceso al Sistema') }}
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="{{ __('Nombre de Usuario (Username)') }}" id="username" wire:model.live.debounce.300ms="username" required autocomplete="username" placeholder="Ejemplo: carlos_diamante" loading-target="username" :status="$usernameStatus" hint="{{ __('Tu identificador único para ingresar y tu enlace de invitación.') }}" />

                <x-input type="email" label="{{ __('Correo Electrónico') }}" id="email" wire:model.live.debounce.400ms="email" required autocomplete="email" placeholder="correo@ejemplo.com" loading-target="email" :status="$emailStatus" />

                <x-input type="password" label="{{ __('Contraseña') }}" id="password" wire:model.live.debounce.250ms="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" viewable :status="$passwordStatus" />

                <x-input type="password" label="{{ __('Confirmar Contraseña') }}" id="password_confirmation" wire:model.live.debounce.250ms="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contraseña exactamente" viewable :status="$passwordMatchStatus" />
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- SECCIÓN 5: CASILLA DE TÉRMINOS Y CONDICIONES Y CONTRATO -->
        <!-- ========================================================= -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900/50 shadow-2xs">
            <x-checkbox id="terms_accepted" wire:model.live="terms_accepted">
                {{ __('He leído y acepto los') }}
                <a href="#" @click.prevent="showTerms = true" class="font-bold text-primary underline hover:text-secondary dark:text-zinc-100 dark:hover:text-white">
                    {{ __('Términos y Condiciones') }}
                </a>
                {{ __('y el') }}
                <a href="#" @click.prevent="showContract = true" class="font-bold text-primary underline hover:text-secondary dark:text-zinc-100 dark:hover:text-white">
                    {{ __('Contrato de Afiliación como Distribuidor Independiente de Fornuvi S.A.S,') }}
                </a>
                {{ __('los cuales he leído previamente y entiendo en su totalidad.') }}
            </x-checkbox>
        </div>


        <!-- ========================================================= -->
        <!-- BOTÓN DE ENVÍO -->
        <!-- ========================================================= -->
        <div class="mt-1">
            <button
                type="submit"
                wire:loading.attr="disabled"
                @disabled(!$terms_accepted)
                data-test="register-user-button"
                class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-secondary text-white font-bold py-3.5 px-4 text-sm shadow-md transition-all active:scale-98 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100">
                <span wire:loading.remove wire:target="register" class="flex items-center gap-2">
                    <flux:icon.user-plus class="size-4" />
                    <span>{{ $isMaster ? __('Registrar Nodo Maestro') : __('Completar Afiliación y Registro') }}</span>
                </span>
                <span wire:loading wire:target="register" class="flex items-center gap-2">
                    <svg class="animate-spin size-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>{{ __('Procesando afiliación en la red...') }}</span>
                </span>
            </button>
            @if (!$terms_accepted)
            <p class="text-center text-xs text-zinc-500 dark:text-zinc-400 mt-2">
                {{ __('Debes marcar la casilla de aceptación de términos para habilitar el registro.') }}
            </p>
            @endif
        </div>
    </form>

    <!-- ========================================================= -->
    <!-- MODAL 1: TÉRMINOS Y CONDICIONES GENERALES -->
    <!-- ========================================================= -->
    <div
        x-show="showTerms"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs transition-opacity"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div
            @click.away="showTerms = false"
            class="relative w-full max-w-3xl max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 overflow-hidden">
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 px-6 py-4 bg-zinc-50 dark:bg-zinc-950">
                <div class="flex items-center gap-2">
                    <flux:icon.document-text class="size-5 text-primary dark:text-zinc-300" />
                    <h3 class="text-base font-bold text-ink dark:text-zinc-50">
                        {{ __('Términos y Condiciones Generales de Fornuvi S.A.S.') }}
                    </h3>
                </div>
                <button
                    type="button"
                    @click="showTerms = false"
                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-200 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">
                    <flux:icon.x-mark class="size-5" />
                </button>
            </div>

            <!-- Contenido Scrollable Modular -->
            <div class="flex-1 overflow-y-auto p-6 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300 font-sans">
                @include('pages.legal.partials.terms-content')
            </div>

            <!-- Footer Modal -->
            <div class="border-t border-zinc-200 dark:border-zinc-800 px-6 py-3 bg-zinc-50 dark:bg-zinc-950 flex justify-end">
                <button
                    type="button"
                    @click="showTerms = false"
                    class="rounded-xl bg-primary hover:bg-secondary text-white dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-bold px-5 py-2 text-xs transition-colors cursor-pointer">
                    {{ __('Entendido y Cerrar') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 2: CONTRATO DE VINCULACIÓN COMERCIAL -->
    <!-- ========================================================= -->
    <div
        x-show="showContract"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs transition-opacity"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div
            @click.away="showContract = false"
            class="relative w-full max-w-3xl max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 overflow-hidden">
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 px-6 py-4 bg-zinc-50 dark:bg-zinc-950">
                <div class="flex items-center gap-2">
                    <flux:icon.briefcase class="size-5 text-primary dark:text-zinc-300" />
                    <h3 class="text-base font-bold text-ink dark:text-zinc-50">
                        {{ __('Contrato de Vinculación Comercial') }}
                    </h3>
                </div>
                <button
                    type="button"
                    @click="showContract = false"
                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-200 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition-colors cursor-pointer">
                    <flux:icon.x-mark class="size-5" />
                </button>
            </div>

            <!-- Contenido Scrollable Modular -->
            <div class="flex-1 overflow-y-auto p-6 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300 font-sans">
                @include('pages.legal.partials.contract-content')
            </div>

            <!-- Footer Modal -->
            <div class="border-t border-zinc-200 dark:border-zinc-800 px-6 py-3 bg-zinc-50 dark:bg-zinc-950 flex justify-end">
                <button
                    type="button"
                    @click="showContract = false"
                    class="rounded-xl bg-primary hover:bg-secondary text-white dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-bold px-5 py-2 text-xs transition-colors cursor-pointer">
                    {{ __('Entendido y Cerrar') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 3: BIENVENIDA Y REGISTRO EXITOSO -->
    <!-- ========================================================= -->
    @if ($showSuccessModal)
    <div
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs transition-opacity animate-in fade-in duration-300">
        <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 overflow-hidden">
            
            <!-- Encabezado con Icono de Éxito -->
            <div class="px-6 pt-8 pb-4 text-center bg-gradient-to-b from-primary/10 to-transparent dark:from-zinc-800/40 dark:to-transparent">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-primary/25 dark:bg-zinc-100 dark:text-zinc-950 dark:shadow-none">
                    <flux:icon.check class="size-8 stroke-[2.5]" />
                </div>
                <h3 class="text-2xl font-black text-ink dark:text-zinc-50">
                    {{ __('¡Bienvenido a FORNUVI!') }}
                </h3>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                    {{ __('Tu afiliación ha sido procesada y registrada exitosamente en la organización.') }}
                </p>
            </div>

            <!-- Resumen de Credenciales y Posicionamiento -->
            <div class="px-6 py-3">
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/60 flex flex-col gap-2.5 text-xs">
                    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-2 dark:border-zinc-800/80">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Nombre Afiliado:') }}</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $registeredName }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-2 dark:border-zinc-800/80">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Usuario:') }}</span>
                        <span class="font-mono font-bold text-primary dark:text-zinc-200">{{ '@'.$registeredUsername }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-2 dark:border-zinc-800/80">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Correo Electrónico:') }}</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $registeredEmail }}</span>
                    </div>

                    @if ($registeredSponsor)
                    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-2 dark:border-zinc-800/80">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Patrocinador:') }}</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $registeredSponsor }}</span>
                    </div>
                    @endif

                    @if ($registeredLeg)
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Pierna Asignada:') }}</span>
                        <span class="inline-flex items-center rounded-md bg-zinc-200 px-2 py-0.5 font-bold text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ $registeredLeg }}
                        </span>
                    </div>
                    @endif
                </div>

                @if ($wasAuthenticatedBefore)
                <p class="mt-3 text-center text-xs text-zinc-500 dark:text-zinc-400">
                    ℹ️ {{ __('Has registrado este afiliado desde tu sesión. Al presionar el botón regresarás a tu panel.') }}
                </p>
                @else
                <p class="mt-3 text-center text-xs text-zinc-500 dark:text-zinc-400">
                    ✨ {{ __('Tu cuenta ya se encuentra activa. Puedes acceder inmediatamente a tu oficina virtual.') }}
                </p>
                @endif
            </div>

            <!-- Botón de Acción -->
            <div class="p-6 pt-2">
                <button
                    type="button"
                    wire:click="continueAfterRegister"
                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-secondary text-white dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-bold py-3 text-sm shadow-md transition-all cursor-pointer">
                    <span>{{ $wasAuthenticatedBefore ? __('Volver a mi Panel (Dashboard)') : __('Entrar a mi Oficina Virtual') }}</span>
                    <flux:icon.arrow-right class="size-4" />
                </button>
            </div>
        </div>
    </div>
    @endif

</div>