<div
    class="flex h-full w-full flex-1 flex-col gap-8 relative"
    x-data="{
        copiedLeftMessage: false,
        copiedRightMessage: false,
        copiedLeftUrl: false,
        copiedRightUrl: false,
        toastVisible: false,
        toastMessage: '',
        toastTimeout: null,
        copyToClipboard(text, type) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Error al copiar al portapapeles', err);
                }
                textArea.remove();
            }

            if (type === 'leftMessage') {
                this.copiedLeftMessage = true;
                setTimeout(() => this.copiedLeftMessage = false, 2500);
                this.showToast('{{ __('¡Mensaje con enlace copiado al portapapeles! Ya puedes pegarlo en WhatsApp o cualquier red.') }}');
            } else if (type === 'rightMessage') {
                this.copiedRightMessage = true;
                setTimeout(() => this.copiedRightMessage = false, 2500);
                this.showToast('{{ __('¡Mensaje con enlace copiado al portapapeles! Ya puedes pegarlo en WhatsApp o cualquier red.') }}');
            } else if (type === 'leftUrl') {
                this.copiedLeftUrl = true;
                setTimeout(() => this.copiedLeftUrl = false, 2500);
                this.showToast('{{ __('¡Enlace copiado al portapapeles!') }}');
            } else if (type === 'rightUrl') {
                this.copiedRightUrl = true;
                setTimeout(() => this.copiedRightUrl = false, 2500);
                this.showToast('{{ __('¡Enlace copiado al portapapeles!') }}');
            }
        },
        showToast(message) {
            this.toastMessage = message;
            this.toastVisible = true;
            if (this.toastTimeout) clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => {
                this.toastVisible = false;
            }, 4000);
        }
    }">

    {{-- Notificación Flotante Toast para Confirmación de Copiado --}}
    <div
        x-show="toastVisible"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-cloak
        class="fixed bottom-6 right-6 z-50 max-w-md w-full sm:w-auto flex items-center gap-3 rounded-2xl bg-ink text-white p-4 shadow-xl shadow-ink/80 border border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:border-zinc-700 dark:shadow-zinc-950/60">
        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 dark:bg-emerald-950/50">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <div class="flex-1 text-xs sm:text-sm font-medium leading-snug">
            <span x-text="toastMessage"></span>
        </div>
        <button
            type="button"
            @click="toastVisible = false"
            class="text-zinc-400 hover:text-white dark:hover:text-zinc-200 transition-colors p-1"
            aria-label="{{ __('Cerrar') }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Header de Bienvenida y Usuario --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
        <div class="flex items-center gap-4">
            <div class="flex size-13 shrink-0 items-center justify-center rounded-2xl bg-primary text-white font-bold text-lg dark:bg-zinc-800 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700">
                {{ strtoupper(substr($this->user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($this->user->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">
                        {{ __('¡Bienvenido, :name!', ['name' => $this->user->name]) }}
                    </h1>
                    @if ($this->isInWaitingRoom)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 dark:bg-zinc-800 dark:text-zinc-200 border border-amber-500/30 dark:border-zinc-700">
                            <span class="size-2 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ __('En Sala de Espera (Pre-afiliado)') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                            {{ __('Afiliado Fornuvi') }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ __('Tu identificador único en la red:') }}
                    <span class="font-semibold text-primary dark:text-zinc-200 tracking-wide">{{ $this->user->username }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if ($this->isInWaitingRoom)
                <flux:button :href="route('home')" icon="shopping-bag" variant="primary" class="font-medium bg-primary hover:bg-secondary text-white border-0 shadow-md shadow-ink/70 dark:shadow-none" wire:navigate>
                    {{ __('Comprar y Calificar') }}
                </flux:button>
            @else
                <flux:button :href="route('network.binary')" icon="cpu-chip" variant="outline" class="border-zinc-300 hover:border-zinc-400 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800 font-medium" wire:navigate>
                    {{ __('Árbol Binario') }}
                </flux:button>
                <flux:button :href="route('network.unilevel')" icon="user-group" variant="outline" class="border-zinc-300 hover:border-zinc-400 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800 font-medium" wire:navigate>
                    {{ __('Árbol Unilevel') }}
                </flux:button>
            @endif
        </div>
    </div>

    @if ($this->isInWaitingRoom)
    {{-- Banner Especial: Sala de Espera (Holding Tank) --}}
    <div class="rounded-2xl border-2 border-primary/30 bg-primary/5 p-6 shadow-md shadow-ink/70 dark:border-zinc-700 dark:bg-zinc-900/80 dark:shadow-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-100 border border-primary/20 dark:border-zinc-700">
                        <flux:icon.clock class="size-3.5" />
                        {{ __('Estado: Sala de Espera (Holding Tank)') }}
                    </span>
                    @if ($this->user->sponsor)
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Patrocinador:') }} <strong class="text-ink dark:text-zinc-200">{{ $this->user->sponsor->name }} (@ {{ $this->user->sponsor->username }})</strong>
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-black text-ink dark:text-zinc-100">
                    {{ __('¡Activa tu posición definitiva en la red binaria!') }}
                </h2>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 max-w-2xl leading-relaxed">
                    {{ __('Te encuentras registrado en la sala de espera. Para obtener tu posición inmutable en el sistema binario y unilevel y desbloquear tus enlaces de patrocinio, debes acumular al menos :min puntos en compras personales en este mes. Tus compras actuales ya suman puntos hacia tu activación definitiva.', ['min' => number_format($this->minRequiredPoints, 2)]) }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                <div class="w-full sm:w-64 bg-white dark:bg-zinc-800/80 p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-700 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Progreso de Entrada:') }}</span>
                        <span class="font-bold text-ink dark:text-zinc-100 font-mono">{{ number_format($this->personalPoints, 2) }} / {{ number_format($this->minRequiredPoints, 2) }} Pts</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-2.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-primary transition-all duration-500" style="width: {{ $this->qualificationProgress }}%;"></div>
                    </div>
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 text-right">
                        @if ($this->pointsNeeded > 0)
                            {{ __('Faltan :pts Pts para ingresar', ['pts' => number_format($this->pointsNeeded, 2)]) }}
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('¡Meta de activación alcanzada!') }}</span>
                        @endif
                    </div>
                </div>
                <flux:button :href="route('home')" icon="shopping-bag" variant="primary" class="w-full sm:w-auto font-bold text-sm bg-primary hover:bg-secondary text-white border-0 shadow-md shadow-ink/70 dark:shadow-none whitespace-nowrap" wire:navigate>
                    {{ __('Comprar Productos') }}
                </flux:button>
            </div>
        </div>
    </div>
    @endif

    {{-- Widget de Estado de Calificación Mensual y Mes de Gracia --}}
    <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <!-- Estado de Calificación -->
            <div class="space-y-2">
                <div class="flex items-center gap-2.5">
                    @if ($this->isQualified)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Activo para Comisionar') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">
                            <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                            {{ __('Inactivo en este Ciclo') }}
                        </span>
                    @endif

                    <span class="text-xs text-zinc-400">
                        {{ __('Periodo:') }} <strong class="text-ink dark:text-zinc-200">{{ $this->activePeriod?->name }}</strong>
                    </span>
                </div>

                <div>
                    <h2 class="text-lg font-black text-ink dark:text-zinc-100">
                        @if ($this->isQualified)
                            {{ __('¡Tu cuenta está calificada!') }}
                        @else
                            {{ __('Calificación Mensual Requerida') }}
                        @endif
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        @if ($this->userActivation?->activation_type === 'grace_period' && $this->isQualified)
                            <span class="text-premium font-semibold">🎁 {{ __('Beneficio de Mes de Gracia Activo:') }}</span>
                            {{ __('Mantienes tu estatus activo de bienvenida hasta el :date.', ['date' => $this->userActivation?->expires_at?->format('d/m/Y')]) }}
                        @elseif ($this->userActivation?->activation_type === 'admin' && $this->isQualified)
                            <span class="text-primary dark:text-secondary font-semibold">🛡️ {{ __('Activación por Administración:') }}</span>
                            {{ __('Vigente hasta el :date.', ['date' => $this->userActivation?->expires_at?->format('d/m/Y')]) }}
                        @elseif ($this->isQualified)
                            {{ __('Has acumulado el puntaje mínimo requerido (:min Pts) y cobrarás comisiones en este cierre.', ['min' => $this->minRequiredPoints]) }}
                        @else
                            {{ __('Acumula un mínimo de :min Pts personales en compras en el mes para cobrar comisiones en el binario y escalonado.', ['min' => $this->minRequiredPoints]) }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Barra de Progreso y Puntos -->
            <div class="md:w-72 lg:w-80 shrink-0 space-y-2 bg-zinc-50 dark:bg-zinc-800/40 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-zinc-600 dark:text-zinc-400">{{ __('Puntos Personales:') }}</span>
                    <span class="font-black font-mono text-ink dark:text-zinc-100">
                        {{ number_format($this->personalPoints, 2) }} / {{ number_format($this->minRequiredPoints, 2) }} Pts
                    </span>
                </div>

                <!-- Barra de Progreso Visual -->
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-2.5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $this->isQualified ? 'bg-emerald-500' : 'bg-primary dark:bg-secondary' }}"
                         style="width: {{ $this->qualificationProgress }}%;"></div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-0.5">
                    @if ($this->isQualified)
                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                            ✓ {{ __('100% Calificado') }}
                        </span>
                    @else
                        <span>
                            {{ __('Faltan :pts Pts', ['pts' => number_format($this->pointsNeeded, 2)]) }}
                        </span>
                    @endif
                    <span>
                        {{ __('Corte: :date', ['date' => $this->activePeriod?->ends_at?->format('d/m')]) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de Enlaces de Registro y Patrocinio --}}
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100 flex items-center gap-2">
                    <flux:icon.share class="size-5 text-zinc-600 dark:text-zinc-400" />
                    {{ __('Enlaces de Registro y Patrocinio') }}
                </h2>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                    {{ __('Registra nuevos afiliados directamente o copia el mensaje con tu enlace para compartirlo en WhatsApp.') }}
                </p>
            </div>
        </div>

        @if (! $this->canSponsor)
            <!-- Bloqueado para afiliados en Sala de Espera -->
            <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 text-center shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500 mb-3">
                    <flux:icon.lock-closed class="size-7" />
                </div>
                <h3 class="text-base font-bold text-ink dark:text-zinc-100">
                    {{ __('Enlaces de Patrocinio Bloqueados Temporalmente') }}
                </h3>
                <p class="mt-1 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-lg mx-auto">
                    {{ __('Tu cuenta se encuentra en la Sala de Espera. Para evitar inconsistencias en la red, podrás invitar y patrocinar a nuevos afiliados una vez alcances el mínimo de :min puntos en compras acumuladas este mes y ocupes tu posición oficial en el árbol binario.', ['min' => number_format($this->minRequiredPoints, 2)]) }}
                </p>
                <div class="mt-4">
                    <flux:button :href="route('home')" icon="shopping-bag" variant="primary" class="font-bold text-xs bg-primary hover:bg-secondary text-white border-0 shadow-md shadow-ink/70 dark:shadow-none" wire:navigate>
                        {{ __('Ver Catálogo y Acumular Puntos') }}
                    </flux:button>
                </div>
            </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tarjeta Pierna Izquierda -->
            <div class="flex flex-col justify-between rounded-2xl border border-zinc-200/90 bg-white p-5 sm:p-6 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none relative group">
                <div>
                    <!-- Cabecera de la Tarjeta -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-zinc-100 text-ink dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                            <flux:icon.arrow-left class="size-3.5 text-zinc-600 dark:text-zinc-400" />
                            {{ __('Pierna Izquierda (Equipo A)') }}
                        </span>
                        <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                            {{ __('Binario') }}
                        </span>
                    </div>

                    <!-- Vista Previa del Mensaje para WhatsApp -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                <flux:icon.chat-bubble-left-ellipsis class="size-3.5 text-zinc-500 dark:text-zinc-400" />
                                {{ __('Mensaje listo para compartir:') }}
                            </span>
                            <span class="text-[11px] text-zinc-400 dark:text-zinc-500">
                                {{ __('Formato WhatsApp') }}
                            </span>
                        </div>
                        <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-3.5 border border-zinc-200/80 dark:border-zinc-800/90 text-xs sm:text-sm text-ink dark:text-zinc-200 leading-relaxed font-sans select-all relative">
                            <p class="font-medium text-ink dark:text-zinc-100">{{ __('Dale clic al enlace para registrarse 👇') }}</p>
                            <p class="text-primary dark:text-zinc-300 font-mono text-xs break-all mt-1 underline decoration-primary/40 dark:decoration-zinc-600 underline-offset-2">{{ $this->leftLink }}</p>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1.5 flex items-center gap-1">
                            <flux:icon.information-circle class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                            {{ __('Al presionar "Copiar mensaje", este texto exacto quedará en tu portapapeles.') }}
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                    <!-- Fila 1: Acciones Principales (Registrar Directo y Copiar Mensaje) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Botón Registrar Directo -->
                        <a
                            href="{{ $this->leftLink }}"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-primary hover:bg-secondary dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 dark:border dark:border-zinc-700 transition-all cursor-pointer shadow-md shadow-ink/70 dark:shadow-none min-h-[44px] group/btn"
                            title="{{ __('Abrir formulario de registro directo en una nueva pestaña') }}">
                            <flux:icon.user-plus class="size-4 group-hover/btn:scale-110 transition-transform" />
                            <span>{{ __('Registrar directo') }}</span>
                            <svg class="size-3.5 text-white/70 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>

                        <!-- Botón Principal: Copiar Mensaje Formateado -->
                        <button
                            type="button"
                            @click="copyToClipboard(@js($this->leftShareText), 'leftMessage')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-secondary hover:bg-primary dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 dark:border dark:border-zinc-700 transition-all cursor-pointer shadow-md shadow-ink/70 dark:shadow-none min-h-[44px]"
                            :class="copiedLeftMessage ? '!bg-emerald-600 hover:!bg-emerald-700 dark:!bg-emerald-800' : ''">
                            <template x-if="!copiedLeftMessage">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.clipboard-document class="size-4" />
                                    {{ __('Copiar enlace') }}
                                </span>
                            </template>
                            <template x-if="copiedLeftMessage">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.clipboard-document-check class="size-4" />
                                    {{ __('¡Copiado!') }}
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Fila 2: Accesos Directos (Abrir WhatsApp y Solo URL) -->
                    <div class="flex items-center gap-2">
                        <!-- Botón Directo: Abrir en WhatsApp -->
                        <a
                            href="{{ $this->leftWhatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium text-zinc-700 bg-white hover:bg-zinc-50 border border-zinc-200 hover:border-zinc-300 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700 transition-colors min-h-[38px]"
                            title="{{ __('Abrir directamente en WhatsApp con el mensaje cargado') }}">
                            <svg class="size-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24">
                                <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.767.461 3.49 1.336 5.01L2 22l5.127-1.344a10.02 10.02 0 0 0 4.904 1.282h.004c5.535 0 10.03-4.495 10.03-10.031A10.025 10.025 0 0 0 12.031 2zm0 18.358h-.003a8.347 8.347 0 0 1-4.254-1.164l-.305-.181-3.162.83.844-3.082-.198-.316a8.312 8.312 0 0 1-1.272-4.414c0-4.606 3.748-8.354 8.356-8.354 2.233 0 4.332.869 5.91 2.449a8.318 8.318 0 0 1 2.446 5.912c0 4.607-3.748 8.354-8.362 8.354zm4.582-6.257c-.251-.126-1.488-.735-1.719-.818-.231-.084-.399-.126-.566.126-.167.251-.649.818-.796.986-.147.167-.293.188-.544.063-.251-.126-1.06-.391-2.02-1.246-.747-.666-1.252-1.489-1.398-1.74-.146-.251-.016-.387.11-.512.113-.112.251-.293.376-.439.126-.147.167-.251.251-.419.084-.167.042-.314-.021-.439-.063-.126-.566-1.362-.775-1.865-.204-.49-.41-.423-.566-.431h-.481c-.167 0-.439.063-.669.314-.23.251-.879.86-.879 2.097 0 1.237.9 2.43 1.026 2.597.126.167 1.77 2.704 4.288 3.791.6.259 1.068.414 1.433.53.603.192 1.152.165 1.586.1.484-.072 1.488-.608 1.698-1.194.209-.586.209-1.089.146-1.194-.063-.105-.23-.167-.481-.293z" />
                            </svg>
                            <span>{{ __('Compartir en WhatsApp') }}</span>
                        </a>

                        <!-- Botón Secundario: Copiar Solo Enlace -->
                        <button
                            type="button"
                            @click="copyToClipboard(@js($this->leftLink), 'leftUrl')"
                            class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl text-xs font-medium border border-zinc-200 dark:border-zinc-700 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 transition-colors cursor-pointer min-h-[38px]"
                            title="{{ __('Copiar únicamente la URL sin el mensaje de texto') }}">
                            <span x-text="copiedLeftUrl ? '✓ Copiado' : 'Solo URL'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Pierna Derecha -->
            <div class="flex flex-col justify-between rounded-2xl border border-zinc-200/90 bg-white p-5 sm:p-6 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none relative group">
                <div>
                    <!-- Cabecera de la Tarjeta -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-zinc-100 text-ink dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                            <flux:icon.arrow-right class="size-3.5 text-zinc-600 dark:text-zinc-400" />
                            {{ __('Pierna Derecha (Equipo B)') }}
                        </span>
                        <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                            {{ __('Binario') }}
                        </span>
                    </div>

                    <!-- Vista Previa del Mensaje para WhatsApp -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                <flux:icon.chat-bubble-left-ellipsis class="size-3.5 text-zinc-500 dark:text-zinc-400" />
                                {{ __('Mensaje listo para compartir:') }}
                            </span>
                            <span class="text-[11px] text-zinc-400 dark:text-zinc-500">
                                {{ __('Formato WhatsApp') }}
                            </span>
                        </div>
                        <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-3.5 border border-zinc-200/80 dark:border-zinc-800/90 text-xs sm:text-sm text-ink dark:text-zinc-200 leading-relaxed font-sans select-all relative">
                            <p class="font-medium text-ink dark:text-zinc-100">{{ __('Dale clic al enlace para registrarse 👇') }}</p>
                            <p class="text-primary dark:text-zinc-300 font-mono text-xs break-all mt-1 underline decoration-primary/40 dark:decoration-zinc-600 underline-offset-2">{{ $this->rightLink }}</p>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1.5 flex items-center gap-1">
                            <flux:icon.information-circle class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                            {{ __('Al presionar "Copiar mensaje", este texto exacto quedará en tu portapapeles.') }}
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                    <!-- Fila 1: Acciones Principales (Registrar Directo y Copiar Mensaje) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Botón Registrar Directo -->
                        <a
                            href="{{ $this->rightLink }}"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-primary hover:bg-secondary dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 dark:border dark:border-zinc-700 transition-all cursor-pointer shadow-md shadow-ink/70 dark:shadow-none min-h-[44px] group/btn"
                            title="{{ __('Abrir formulario de registro directo en una nueva pestaña') }}">
                            <flux:icon.user-plus class="size-4 group-hover/btn:scale-110 transition-transform" />
                            <span>{{ __('Registrar directo') }}</span>
                            <svg class="size-3.5 text-white/70 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>

                        <!-- Botón Principal: Copiar Mensaje Formateado -->
                        <button
                            type="button"
                            @click="copyToClipboard(@js($this->rightShareText), 'rightMessage')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-white bg-secondary hover:bg-primary dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 dark:border dark:border-zinc-700 transition-all cursor-pointer shadow-md shadow-ink/70 dark:shadow-none min-h-[44px]"
                            :class="copiedRightMessage ? '!bg-emerald-600 hover:!bg-emerald-700 dark:!bg-emerald-800' : ''">
                            <template x-if="!copiedRightMessage">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.clipboard-document class="size-4" />
                                    {{ __('Copiar enlace') }}
                                </span>
                            </template>
                            <template x-if="copiedRightMessage">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.clipboard-document-check class="size-4" />
                                    {{ __('¡Copiado!') }}
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Fila 2: Accesos Directos (Abrir WhatsApp y Solo URL) -->
                    <div class="flex items-center gap-2">
                        <!-- Botón Directo: Abrir en WhatsApp -->
                        <a
                            href="{{ $this->rightWhatsappUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium text-zinc-700 bg-white hover:bg-zinc-50 border border-zinc-200 hover:border-zinc-300 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700 transition-colors min-h-[38px]"
                            title="{{ __('Abrir directamente en WhatsApp con el mensaje cargado') }}">
                            <svg class="size-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24">
                                <path d="M12.031 2C6.495 2 2 6.495 2 12.031c0 1.767.461 3.49 1.336 5.01L2 22l5.127-1.344a10.02 10.02 0 0 0 4.904 1.282h.004c5.535 0 10.03-4.495 10.03-10.031A10.025 10.025 0 0 0 12.031 2zm0 18.358h-.003a8.347 8.347 0 0 1-4.254-1.164l-.305-.181-3.162.83.844-3.082-.198-.316a8.312 8.312 0 0 1-1.272-4.414c0-4.606 3.748-8.354 8.356-8.354 2.233 0 4.332.869 5.91 2.449a8.318 8.318 0 0 1 2.446 5.912c0 4.607-3.748 8.354-8.362 8.354zm4.582-6.257c-.251-.126-1.488-.735-1.719-.818-.231-.084-.399-.126-.566.126-.167.251-.649.818-.796.986-.147.167-.293.188-.544.063-.251-.126-1.06-.391-2.02-1.246-.747-.666-1.252-1.489-1.398-1.74-.146-.251-.016-.387.11-.512.113-.112.251-.293.376-.439.126-.147.167-.251.251-.419.084-.167.042-.314-.021-.439-.063-.126-.566-1.362-.775-1.865-.204-.49-.41-.423-.566-.431h-.481c-.167 0-.439.063-.669.314-.23.251-.879.86-.879 2.097 0 1.237.9 2.43 1.026 2.597.126.167 1.77 2.704 4.288 3.791.6.259 1.068.414 1.433.53.603.192 1.152.165 1.586.1.484-.072 1.488-.608 1.698-1.194.209-.586.209-1.089.146-1.194-.063-.105-.23-.167-.481-.293z" />
                            </svg>
                            <span>{{ __('Compartir en WhatsApp') }}</span>
                        </a>

                        <!-- Botón Secundario: Copiar Solo Enlace -->
                        <button
                            type="button"
                            @click="copyToClipboard(@js($this->rightLink), 'rightUrl')"
                            class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl text-xs font-medium border border-zinc-200 dark:border-zinc-700 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 transition-colors cursor-pointer min-h-[38px]"
                            title="{{ __('Copiar únicamente la URL sin el mensaje de texto') }}">
                            <span x-text="copiedRightUrl ? '✓ Copiado' : 'Solo URL'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if ($this->waitingRoomMembers->isNotEmpty())
    {{-- Sección: Mi Sala de Espera (Holding Tank) --}}
    <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100 flex items-center gap-2">
                    <flux:icon.user-group class="size-5 text-primary dark:text-zinc-300" />
                    {{ __('Mi Sala de Espera (Holding Tank)') }}
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200">
                        {{ $this->waitingRoomMembers->count() }}
                    </span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                    {{ __('Nuevos afiliados registrados con tu enlace que están acumulando sus :min puntos para ingresar a tu red binaria y unilevel.', ['min' => number_format($this->minRequiredPoints, 2)]) }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400 uppercase text-[11px] font-semibold tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">{{ __('Usuario / Nombre') }}</th>
                        <th class="px-4 py-3">{{ __('Pierna Asignada') }}</th>
                        <th class="px-4 py-3">{{ __('Fecha de Registro') }}</th>
                        <th class="px-4 py-3">{{ __('Puntos este Mes') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Estado') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($this->waitingRoomMembers as $member)
                        @php
                            $memberPts = (float) ($member->unilevelSummary?->personal_points ?? 0);
                            $memberProgress = min(100, (int) round(($memberPts / max(0.01, $this->minRequiredPoints)) * 100));
                            $missingPts = max(0.0, round($this->minRequiredPoints - $memberPts, 2));
                        @endphp
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-ink dark:text-zinc-100">{{ $member->name }} {{ $member->last_name }}</div>
                                <div class="text-xs text-primary dark:text-zinc-400 font-mono">{{ $member->username }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($member->preferred_leg === 'R')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-secondary/10 text-secondary dark:bg-zinc-800 dark:text-zinc-200">
                                        <flux:icon.arrow-right class="size-3" />
                                        {{ __('Derecha') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200">
                                        <flux:icon.arrow-left class="size-3" />
                                        {{ __('Izquierda') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                {{ $member->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-ink dark:text-zinc-100">
                                    {{ number_format($memberPts, 2) }} / {{ number_format($this->minRequiredPoints, 2) }} Pts
                                </div>
                                <div class="w-28 bg-zinc-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden mt-1">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $memberProgress }}%;"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                                    {{ __('Faltan :pts Pts', ['pts' => number_format($missingPts, 2)]) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tarjetas de Métricas: Sistema Binario --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2.5">
            <div class="rounded-lg bg-zinc-100 dark:bg-zinc-800 p-1.5 text-zinc-600 dark:text-zinc-400">
                <flux:icon.cpu-chip class="size-5" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100">
                    {{ __('Sistema Binario (Estructura de 2 Piernas)') }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Balance y volumen de puntos acumulados por equipo') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Afiliados Izquierda -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Afiliados Izquierda') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-primary dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.user-group class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format($this->binarySummary->total_left_members) }}
                    </span>
                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('miembros') }}</span>
                </div>
            </div>

            <!-- Puntos Izquierda -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Puntos Pierna Izquierda') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-danger dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.chart-bar class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format((float)$this->binarySummary->total_left_points, 2) }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ __('PTS') }}</span>
                </div>
                @if (($this->binaryWaitingPoints['L'] ?? 0) > 0)
                <div class="mt-2 flex items-center justify-between text-xs pt-2 border-t border-zinc-100 dark:border-zinc-800"
                    title="{{ __('Total: :total pts (:placed red activa + :waiting sala de espera)', ['total' => number_format((float)$this->binarySummary->total_left_points, 2), 'placed' => number_format(max(0.0, (float)$this->binarySummary->total_left_points - $this->binaryWaitingPoints['L']), 2), 'waiting' => number_format($this->binaryWaitingPoints['L'], 2)]) }}">
                    <span class="text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Red activa: :pts', ['pts' => number_format(max(0.0, (float)$this->binarySummary->total_left_points - $this->binaryWaitingPoints['L']), 2)]) }}</span>
                    <span class="text-premium font-semibold flex items-center gap-1">
                        <flux:icon.clock class="size-3 text-premium dark:text-zinc-400" />
                        +{{ number_format($this->binaryWaitingPoints['L'], 2) }} {{ __('esp.') }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Afiliados Derecha -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Afiliados Derecha') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-secondary dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.user-group class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format($this->binarySummary->total_right_members) }}
                    </span>
                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('miembros') }}</span>
                </div>
            </div>

            <!-- Puntos Derecha -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Puntos Pierna Derecha') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-premium dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.chart-bar class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format((float)$this->binarySummary->total_right_points, 2) }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ __('PTS') }}</span>
                </div>
                @if (($this->binaryWaitingPoints['R'] ?? 0) > 0)
                <div class="mt-2 flex items-center justify-between text-xs pt-2 border-t border-zinc-100 dark:border-zinc-800"
                    title="{{ __('Total: :total pts (:placed red activa + :waiting sala de espera)', ['total' => number_format((float)$this->binarySummary->total_right_points, 2), 'placed' => number_format(max(0.0, (float)$this->binarySummary->total_right_points - $this->binaryWaitingPoints['R']), 2), 'waiting' => number_format($this->binaryWaitingPoints['R'], 2)]) }}">
                    <span class="text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Red activa: :pts', ['pts' => number_format(max(0.0, (float)$this->binarySummary->total_right_points - $this->binaryWaitingPoints['R']), 2)]) }}</span>
                    <span class="text-premium font-semibold flex items-center gap-1">
                        <flux:icon.clock class="size-3 text-premium dark:text-zinc-400" />
                        +{{ number_format($this->binaryWaitingPoints['R'], 2) }} {{ __('esp.') }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tarjetas de Métricas: Sistema Escalonado (Unilevel) --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2.5">
            <div class="rounded-lg bg-zinc-100 dark:bg-zinc-800 p-1.5 text-zinc-600 dark:text-zinc-400">
                <flux:icon.user-group class="size-5" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-ink dark:text-zinc-100">
                    {{ __('Sistema Escalonado (Unilevel / Patrocinio Directo)') }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Patrocinados directos y volumen acumulado de la red') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Directos -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Patrocinados Directos') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-primary dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.users class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format($this->unilevelSummary->direct_sponsors_count) }}
                    </span>
                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('nivel 1') }}</span>
                </div>
            </div>

            <!-- Total Red -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Total Red Escalonada') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-danger dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.globe-alt class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format($this->unilevelSummary->total_network_members) }}
                    </span>
                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('toda la red') }}</span>
                </div>
            </div>

            <!-- Puntos Personales -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Puntos Personales (PV)') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-secondary dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.shopping-bag class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format((float)$this->unilevelSummary->personal_points, 2) }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ __('PTS') }}</span>
                </div>
            </div>

            <!-- Puntos Grupales -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">{{ __('Puntos Grupales (GV)') }}</span>
                    <div class="rounded-xl bg-zinc-100 p-2 text-premium dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.banknotes class="size-5" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50">
                        {{ number_format((float)$this->unilevelSummary->group_points, 2) }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ __('PTS') }}</span>
                </div>
                @if ($this->unilevelWaitingPoints > 0)
                <div class="mt-2 flex items-center justify-between text-xs pt-2 border-t border-zinc-100 dark:border-zinc-800"
                    title="{{ __('Total grupo: :total pts (:placed red activa + :waiting sala de espera)', ['total' => number_format((float)$this->unilevelSummary->group_points, 2), 'placed' => number_format(max(0.0, (float)$this->unilevelSummary->group_points - $this->unilevelWaitingPoints), 2), 'waiting' => number_format($this->unilevelWaitingPoints, 2)]) }}">
                    <span class="text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Red activa: :pts', ['pts' => number_format(max(0.0, (float)$this->unilevelSummary->group_points - $this->unilevelWaitingPoints), 2)]) }}</span>
                    <span class="text-premium font-semibold flex items-center gap-1">
                        <flux:icon.clock class="size-3 text-premium dark:text-zinc-400" />
                        +{{ number_format($this->unilevelWaitingPoints, 2) }} {{ __('esp.') }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>