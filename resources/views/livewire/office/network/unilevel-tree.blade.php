<div class="flex flex-col gap-4 w-full p-1 sm:p-1.5"
    x-data="{
         zoom: 0.85,
         panX: 0,
         panY: 0,
         isDragging: false,
         startX: 0,
         startY: 0,
         isFullscreen: false,
         initialDistance: 0,
         initialZoom: 0.85,

         init() {
             this.$nextTick(() => this.resetView());
             this.$watch('$wire.depth', () => this.$nextTick(() => this.resetView()));
             this.$watch('$wire.targetUserId', () => this.$nextTick(() => this.resetView()));
             window.addEventListener('resize', () => {
                 if (window.innerWidth < 640 && this.zoom > 0.8) {
                     this.fitToView();
                 }
             });
             document.addEventListener('fullscreenchange', () => {
                 this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                 setTimeout(() => this.fitToView(), 150);
             });
         },

         resetView() {
             const depth = Number(this.$wire.depth) || 3;
             const isMobile = window.innerWidth < 640;
             if (isMobile) {
                 const mobileZooms = { 2: 0.90, 3: 0.78, 4: 0.60, 5: 0.45 };
                 this.zoom = mobileZooms[depth] || 0.78;
                 this.panY = 12;
             } else {
                 const depthZooms = { 2: 1.0, 3: 0.95, 4: 0.82, 5: 0.65 };
                 this.zoom = depthZooms[depth] || 0.95;
                 this.panY = 0;
             }
             this.panX = 0;
         },

         fitToView() {
            this.$nextTick(() => {
                const vp = this.$refs.viewport;
                const tr = this.$refs.treeRoot;
                if (!vp || !tr) return;

                const isMobile = window.innerWidth < 640;
                const vpW = vp.clientWidth - (isMobile ? 24 : 48);
                const vpH = vp.clientHeight - (isMobile ? 36 : 64);
                const trW = tr.scrollWidth;
                const trH = tr.scrollHeight;

                if (trW <= 0 || trH <= 0) return;

                const scaleX = vpW / trW;
                const scaleY = vpH / trH;
                
                let bestZoom = Math.min(scaleX, scaleY);
                bestZoom = Math.max(0.30, Math.min(bestZoom, 1.25));

                this.zoom = +bestZoom.toFixed(2);
                this.panX = 0;
                this.panY = isMobile ? 18 : 0;
            });
        },

         zoomIn() {
             this.setZoom(+(this.zoom + 0.10).toFixed(2));
         },

         zoomOut() {
             this.setZoom(+(this.zoom - 0.10).toFixed(2));
         },

         setZoom(newZoom) {
             this.zoom = +Math.max(0.30, Math.min(newZoom, 2.0)).toFixed(2);
         },

         reset100() {
             this.zoom = 1.0;
             this.panX = 0;
             this.panY = 0;
         },

         toggleFullscreen() {
            this.isFullscreen = !this.isFullscreen;
            setTimeout(() => {
                this.fitToView();
            }, 150);
        },

         startDrag(e) {
             if (e.target.closest('button') || e.target.closest('a') || e.target.closest('input')) return;
             this.isDragging = true;
             this.startX = e.clientX - this.panX;
             this.startY = e.clientY - this.panY;
         },

         onDrag(e) {
             if (!this.isDragging) return;
             this.panX = Math.round(e.clientX - this.startX);
             this.panY = Math.round(e.clientY - this.startY);
         },

         endDrag() {
             this.isDragging = false;
         },

         onTouchStart(e) {
             if (e.target.closest('button') || e.target.closest('a') || e.target.closest('input')) return;
             if (e.touches.length === 1) {
                 this.isDragging = true;
                 this.startX = e.touches[0].clientX - this.panX;
                 this.startY = e.touches[0].clientY - this.panY;
             } else if (e.touches.length === 2) {
                 this.isDragging = false;
                 const dx = e.touches[0].clientX - e.touches[1].clientX;
                 const dy = e.touches[0].clientY - e.touches[1].clientY;
                 this.initialDistance = Math.hypot(dx, dy);
                 this.initialZoom = this.zoom;
             }
         },

         onTouchMove(e) {
             if (e.touches.length === 1 && this.isDragging) {
                 if (e.cancelable) e.preventDefault();
                 this.panX = Math.round(e.touches[0].clientX - this.startX);
                 this.panY = Math.round(e.touches[0].clientY - this.startY);
             } else if (e.touches.length === 2 && this.initialDistance > 0) {
                 if (e.cancelable) e.preventDefault();
                 const dx = e.touches[0].clientX - e.touches[1].clientX;
                 const dy = e.touches[0].clientY - e.touches[1].clientY;
                 const currentDistance = Math.hypot(dx, dy);
                 const scaleChange = currentDistance / this.initialDistance;
                 this.setZoom(+(this.initialZoom * scaleChange).toFixed(2));
             }
         },

         onTouchEnd(e) {
             if (e.touches.length === 0) {
                 this.isDragging = false;
                 this.initialDistance = 0;
             } else if (e.touches.length === 1) {
                 this.isDragging = true;
                 this.startX = e.touches[0].clientX - this.panX;
                 this.startY = e.touches[0].clientY - this.panY;
             }
         },

         onWheel(e) {
             e.preventDefault();
             const delta = e.deltaY < 0 ? 0.08 : -0.08;
             this.setZoom(+(this.zoom + delta).toFixed(2));
         }
     }"
    @keydown.escape.window="if (isFullscreen) toggleFullscreen()">

    <!-- ================================================================= -->
    <!-- MENSAJE DE ESTADO / ALERTA DE SEGURIDAD O NAVEGACIÓN -->
    <!-- ================================================================= -->
    @if ($statusMessage)
    <div class="flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs sm:text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300 shadow-xs">
        <div class="flex items-center gap-2">
            <flux:icon.exclamation-triangle class="size-4 shrink-0 text-amber-600" />
            <span>{{ $statusMessage }}</span>
        </div>
        <button type="button" wire:click="$set('statusMessage', null)" class="text-amber-600 hover:text-amber-800 cursor-pointer p-1">
            <flux:icon.x-mark class="size-4" />
        </button>
    </div>
    @endif

    <!-- ================================================================= -->
    <!-- BARRA PRINCIPAL SUPERIOR (BREADCRUMBS, HERRAMIENTAS Y MODO DUAL) -->
    <!-- ================================================================= -->
    <div class="flex flex-col gap-3 rounded-2xl sm:rounded-3xl border border-zinc-200/90 bg-white/95 p-3.5 sm:p-4 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900/95 dark:shadow-none backdrop-blur-md">

        <!-- Fila 1: Título, Migas de Pan (Swipeable) y Buscador -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2.5 sm:gap-3">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.user-group class="size-4 sm:size-5" />
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg lg:text-xl font-bold tracking-tight text-zinc-900 dark:text-white leading-snug">
                            {{ __('Árbol Genealógico Unilevel') }}
                        </h1>
                    </div>
                </div>

                <!-- Migas de Pan (Swipeable horizontalmente en celular sin romper líneas) con Botón Subir Nivel -->
                <div class="mt-1 flex items-center gap-1.5 overflow-x-auto no-scrollbar whitespace-nowrap py-0.5 text-xs text-zinc-500 dark:text-zinc-400 font-medium touch-pan-x">
                    @if ($targetUserId !== auth()->id())
                    <button
                        type="button"
                        wire:click="goUpOneLevel"
                        title="{{ __('Subir un nivel genealógico') }}"
                        class="flex items-center gap-1 rounded-md bg-primary/10 hover:bg-primary/20 text-primary dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 px-2 py-0.5 text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                        <flux:icon.arrow-up class="size-3 text-primary dark:text-zinc-300" />
                        <span>{{ __('Subir Nivel') }}</span>
                    </button>
                    @endif

                    <span class="text-zinc-400 shrink-0 text-[11px] sm:text-xs">{{ __('Línea:') }}</span>
                    @foreach ($this->breadcrumbs as $index => $crumb)
                    @if ($index > 0)
                    <flux:icon.chevron-right class="size-3 text-zinc-400 shrink-0" />
                    @endif

                    @if ($crumb['id'] === $targetUserId)
                    <span class="font-bold text-primary dark:text-zinc-200 bg-primary/10 dark:bg-zinc-800 px-2 py-0.5 rounded-md shrink-0 text-[11px] sm:text-xs">
                        {{ '@' . $crumb['username'] }}
                    </span>
                    @else
                    <button
                        type="button"
                        wire:click="focusNode({{ $crumb['id'] }})"
                        class="hover:text-primary hover:underline dark:hover:text-zinc-200 cursor-pointer shrink-0 text-[11px] sm:text-xs">
                        {{ '@' . $crumb['username'] }}
                    </button>
                    @endif
                    @endforeach
                </div>
            </div>

            <!-- Buscador en tiempo real de patrocinados -->
            <div class="relative w-full lg:w-80" x-data="{ openResults: true }" @click.outside="openResults = false">
                <div class="relative">
                    <flux:icon.magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchQuery"
                        @focus="openResults = true"
                        placeholder="{{ __('Buscar en mi red unilevel...') }}"
                        class="w-full rounded-xl border border-zinc-200 bg-zinc-50/80 pl-9 pr-8 py-2 text-xs sm:text-sm text-zinc-800 placeholder-zinc-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:focus:bg-zinc-900" />
                    @if ($searchQuery)
                    <button
                        type="button"
                        wire:click="$set('searchQuery', '')"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer p-1">
                        <flux:icon.x-mark class="size-3.5" />
                    </button>
                    @endif
                </div>

                <!-- Desplegable de Resultados de Búsqueda -->
                @if (count($this->searchResults) > 0)
                <div
                    x-show="openResults"
                    class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-zinc-200 bg-white p-1.5 shadow-md shadow-ink/70 dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-none">
                    <div class="px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-zinc-400">
                        {{ __('Patrocinados en tu red (:c)', ['c' => count($this->searchResults)]) }}
                    </div>
                    <div class="max-h-56 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @foreach ($this->searchResults as $result)
                        <button
                            type="button"
                            wire:click="focusNode({{ $result['id'] }})"
                            @click="openResults = false"
                            class="w-full flex items-center justify-between rounded-lg px-2.5 py-2.5 text-left text-xs hover:bg-primary/10 dark:hover:bg-zinc-800 cursor-pointer transition-colors">
                            <div class="min-w-0 pr-2">
                                <div class="font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $result['name'] }}</div>
                                <div class="text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">{{ '@' . $result['username'] }}</div>
                            </div>
                            <span class="text-[10px] rounded-full bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-zinc-600 dark:text-zinc-400 font-medium shrink-0">
                                {{ __('Gen. +:d', ['d' => $result['depth']]) }}
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @elseif (mb_strlen($searchQuery) >= 2)
                <div
                    x-show="openResults"
                    class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-zinc-200 bg-white p-3 text-center text-xs text-zinc-500 shadow-xl dark:border-zinc-700 dark:bg-zinc-900">
                    {{ __('No se encontraron afiliados en tu red de patrocinio.') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Fila 2: Acciones Rápidas, Modo Dual y Selector de Profundidad -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 pt-2.5 border-t border-zinc-100 dark:border-zinc-800">
            <!-- Botones de Navegación Rápida -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 touch-pan-x">
                <button
                    type="button"
                    wire:click="resetToMyTree"
                    class="flex items-center justify-center gap-1.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-2 sm:py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer shrink-0 min-h-[38px] sm:min-h-0">
                    <flux:icon.home class="size-3.5 text-primary dark:text-zinc-300" />
                    <span>{{ __('Mi Raíz') }}</span>
                </button>

                @php
                $canGoUp = ($targetUserId !== auth()->id());
                @endphp
                <button
                    type="button"
                    wire:click="goUpOneLevel"
                    @disabled(! $canGoUp)
                    class="flex items-center justify-center gap-1.5 rounded-xl border px-3 py-2 sm:py-1.5 text-xs font-semibold transition-colors shrink-0 min-h-[38px] sm:min-h-0
                        {{ $canGoUp 
                            ? 'border-zinc-200 bg-zinc-50 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 cursor-pointer' 
                            : 'border-zinc-200/50 bg-zinc-50/50 text-zinc-400 dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-600 cursor-not-allowed opacity-60' }}">
                    <flux:icon.arrow-up class="size-3.5" />
                    <span>{{ __('Subir') }}</span>
                </button>

                <!-- Botón Ajustar al Lienzo (en modo gráfico) -->
                @if ($viewMode === 'graph')
                <button
                    type="button"
                    @click="fitToView()"
                    title="{{ __('Ajustar el árbol para que quepa exactamente en la pantalla') }}"
                    class="flex items-center justify-center gap-1.5 rounded-xl border border-primary/30 bg-primary/10 hover:bg-primary/15 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 px-3 py-2 sm:py-1.5 text-xs font-semibold text-primary dark:text-zinc-200 transition-colors cursor-pointer shrink-0 min-h-[38px] sm:min-h-0">
                    <flux:icon.arrows-pointing-in class="size-3.5 text-primary dark:text-zinc-300" />
                    <span>{{ __('Ajustar') }}</span>
                </button>
                @endif
            </div>

            <!-- Alternancia de Modo de Vista y Selector de Generaciones -->
            <div class="flex items-center justify-between sm:justify-end gap-2 pt-1 sm:pt-0">
                <!-- Toggle Modo: Gráfico vs Lista -->
                <div class="flex items-center rounded-xl bg-zinc-100 p-1 dark:bg-zinc-800 shrink-0">
                    <button
                        type="button"
                        wire:click="setViewMode('graph')"
                        class="flex items-center gap-1 rounded-lg px-2.5 py-1.5 sm:px-3 sm:py-1 text-xs font-bold transition-all cursor-pointer min-h-[34px] sm:min-h-0
                            {{ $viewMode === 'graph' 
                                ? 'bg-primary text-white shadow-xs dark:bg-zinc-700 dark:text-white' 
                                : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                        <flux:icon.squares-2x2 class="size-3.5" />
                        <span class="hidden sm:inline">{{ __('Vista Gráfica') }}</span>
                        <span class="sm:hidden">{{ __('Gráfico') }}</span>
                    </button>

                    <button
                        type="button"
                        wire:click="setViewMode('list')"
                        class="flex items-center gap-1 rounded-lg px-2.5 py-1.5 sm:px-3 sm:py-1 text-xs font-bold transition-all cursor-pointer min-h-[34px] sm:min-h-0
                            {{ $viewMode === 'list' 
                                ? 'bg-primary text-white shadow-xs dark:bg-zinc-700 dark:text-white' 
                                : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                        <flux:icon.list-bullet class="size-3.5" />
                        <span class="hidden sm:inline">{{ __('Vista de Lista') }}</span>
                        <span class="sm:hidden">{{ __('Lista') }}</span>
                    </button>
                </div>

                <!-- Selector de Generaciones de Profundidad -->
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 hidden sm:inline">
                        {{ __('Generaciones:') }}
                    </span>
                    <div class="flex items-center rounded-xl bg-zinc-100 p-1 dark:bg-zinc-800">
                        @foreach ([2, 3, 4, 5] as $lvl)
                        <button
                            type="button"
                            wire:click="setDepth({{ $lvl }})"
                            class="rounded-lg px-2 sm:px-2.5 py-1.5 sm:py-1 text-xs font-bold transition-all cursor-pointer min-h-[34px] sm:min-h-0 min-w-[28px] sm:min-w-0
                                    {{ $depth === $lvl 
                                        ? 'bg-primary text-white shadow-xs dark:bg-zinc-700 dark:text-white' 
                                        : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                            <span class="sm:hidden">{{ $lvl }}N</span>
                            <span class="hidden sm:inline">{{ __(':n Niveles', ['n' => $lvl]) }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ================================================================= -->
    <!-- AVISO DE PUNTOS EN SALA DE ESPERA (SI EXISTEN EN EL NODO ENFOCADO) -->
    <!-- ================================================================= -->
    @if (($this->tree['summary']['group_waiting_points'] ?? 0) > 0)
    <div class="flex items-center justify-between rounded-2xl border border-premium/30 bg-premium/10 px-4 py-2.5 text-xs text-premium dark:border-zinc-700 dark:bg-zinc-900/90 dark:text-zinc-300 shadow-sm">
        <div class="flex items-center gap-2.5">
            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-premium/20 dark:bg-zinc-800 text-premium dark:text-zinc-300">
                <flux:icon.clock class="size-4" />
            </div>
            <div>
                <span class="font-bold">{{ __('Volumen Grupal de Sala de Espera:') }}</span>
                <span>{{ __('Los puntos grupales (PG) incluyen +:pts pts generados por pre-afiliados pendientes de calificación en sala de espera (:placed pts de red activa colocada).', [
                    'pts' => number_format($this->tree['summary']['group_waiting_points'], 2),
                    'placed' => number_format($this->tree['summary']['group_placed_points'], 2),
                ]) }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- ================================================================= -->
    <!-- ÁREA DE CONTENIDO: MODO GRÁFICO (CANVAS) O MODO LISTA JERÁRQUICA -->
    <!-- ================================================================= -->
    @if ($viewMode === 'graph')
    <!-- MODO 1: LIENZO INTERACTIVO CON PANEO Y ZOOM -->
    <div
        x-ref="viewport"
        class="touch-none relative w-full rounded-2xl sm:rounded-3xl border border-zinc-300/90 bg-white/70 shadow-md shadow-ink/70 dark:shadow-none dark:border-zinc-800 dark:bg-zinc-950 overflow-hidden select-none flex justify-center items-start pt-7 sm:pt-6 pb-8 transition-[transform] duration-200"
        :class="isFullscreen ? '!fixed !inset-0 !z-[9999] !w-screen !h-screen !m-0 !max-w-none !rounded-none !border-none p-3 sm:p-6 bg-zinc-50 dark:bg-zinc-950 shadow-none' : 'h-[65vh] sm:h-[calc(100vh-14rem)] min-h-[420px]'"
        @mousedown="startDrag($event)"
        @mousemove="onDrag($event)"
        @mouseup="endDrag()"
        @mouseleave="endDrag()"
        @touchstart="onTouchStart($event)"
        @touchmove="onTouchMove($event)"
        @touchend="onTouchEnd($event)"
        @touchcancel="onTouchEnd($event)"
        @wheel.passive="onWheel($event)"
        @dblclick="fitToView()"
        :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
        style="background-image: radial-gradient(circle at 1px 1px, rgba(7, 97, 176, 0.12) 1.4px, transparent 0); background-size: 24px 24px;">

        <!-- Indicador sutil de navegación -->
        <div class="hidden sm:flex absolute top-4 left-4 z-20 items-center gap-1.5 rounded-xl bg-white/90 backdrop-blur-md px-3 py-1.5 text-xs text-zinc-600 shadow-md shadow-ink/70 dark:shadow-none dark:bg-zinc-900/90 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800">
            <flux:icon.arrows-pointing-out class="size-3.5 text-primary dark:text-zinc-400 shrink-0" />
            <span class="font-medium">{{ __('Arrastra • Rueda para zoom • Doble clic para ajustar') }}</span>
        </div>

        <!-- Overlay de Carga Livewire con Aceleración GPU -->
        <div wire:loading.flex class="absolute inset-0 z-30 items-center justify-center bg-zinc-900/10 dark:bg-black/40 backdrop-blur-[2px] transition-all">
            <div class="flex items-center gap-2.5 rounded-2xl bg-white/95 dark:bg-zinc-900/95 px-4 py-2.5 shadow-2xl border border-zinc-200/80 dark:border-zinc-800 text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                <svg class="size-4 animate-spin text-primary dark:text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ __('Actualizando red unilevel...') }}</span>
            </div>
        </div>

        <!-- Capa Transformada: Anclada arriba al centro con paneo y zoom suave -->
        <div
            class="flex flex-col items-center transition-transform duration-75 ease-out will-change-transform pointer-events-auto"
            :style="`transform: translate3d(${panX}px, ${panY}px, 0) scale(${zoom}); transform-origin: top center;`">

            <div x-ref="treeRoot" class="inline-flex flex-col items-center px-4">
                @if ($this->tree)
                @include('livewire.office.network.partials.unilevel-node', ['node' => $this->tree])
                @else
                <div class="flex flex-col items-center justify-center p-8 sm:p-12 text-center">
                    <flux:icon.user-minus class="size-10 sm:size-12 text-zinc-400 mb-3" />
                    <h3 class="text-sm sm:text-base font-bold text-zinc-800 dark:text-zinc-200">
                        {{ __('No se encontró el nodo unilevel') }}
                    </h3>
                    <flux:button wire:click="resetToMyTree" variant="primary" class="mt-4">
                        {{ __('Volver a Mi Árbol') }}
                    </flux:button>
                </div>
                @endif
            </div>
        </div>

        <!-- Controles Flotantes de Zoom e Interacción -->
        <div class="absolute bottom-3 right-3 sm:bottom-5 sm:right-5 z-20 flex items-center gap-1 sm:gap-1.5 rounded-2xl bg-white/95 backdrop-blur-md p-1 sm:p-1.5 shadow-md shadow-ink/70 border border-zinc-200/90 dark:bg-zinc-900/95 dark:shadow-none dark:border-zinc-800">
            <!-- Botón Subir un Nivel -->
            @if ($targetUserId !== auth()->id())
            <button
                type="button"
                wire:click="goUpOneLevel"
                title="{{ __('Subir un nivel genealógico') }}"
                class="flex items-center gap-1 rounded-xl bg-zinc-100 hover:bg-primary/10 text-zinc-700 hover:text-primary dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:hover:text-zinc-100 px-2 sm:px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer min-h-[36px] sm:min-h-0">
                <flux:icon.arrow-up class="size-3.5" />
                <span class="hidden sm:inline">{{ __('Subir') }}</span>
            </button>
            @endif

            <!-- Botón Ajustar Automático -->
            <button
                type="button"
                @click="fitToView()"
                title="{{ __('Ajustar el árbol a la pantalla') }}"
                class="flex items-center gap-1 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer min-h-[36px] sm:min-h-0">
                <flux:icon.arrows-pointing-in class="size-3.5 text-primary dark:text-zinc-300" />
                <span class="hidden sm:inline">{{ __('Ajustar') }}</span>
            </button>

            <!-- Zoom Out -->
            <button
                type="button"
                @click="zoomOut()"
                title="{{ __('Alejar (-) ') }}"
                class="flex size-9 sm:size-8 items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer">
                <flux:icon.minus class="size-4" />
            </button>

            <!-- Nivel de Zoom Actual -->
            <button
                type="button"
                @click="reset100()"
                title="{{ __('Restablecer al 100% de tamaño') }}"
                class="px-1.5 sm:px-2 py-1 text-xs font-mono font-bold text-zinc-700 hover:text-primary dark:text-zinc-300 dark:hover:text-zinc-100 cursor-pointer min-h-[36px] sm:min-h-0 flex items-center justify-center">
                <span x-text="Math.round(zoom * 100) + '%'">100%</span>
            </button>

            <!-- Zoom In -->
            <button
                type="button"
                @click="zoomIn()"
                title="{{ __('Acercar (+) ') }}"
                class="flex size-9 sm:size-8 items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer">
                <flux:icon.plus class="size-4" />
            </button>

            <!-- Botón Pantalla Completa -->
            <button
                type="button"
                @click="toggleFullscreen()"
                :title="isFullscreen ? '{{ __('Salir de pantalla completa') }}' : '{{ __('Pantalla completa') }}'"
                class="flex size-9 sm:size-8 items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer">
                <flux:icon.arrows-pointing-out x-show="!isFullscreen" class="size-4" />
                <flux:icon.x-mark x-show="isFullscreen" class="size-4 text-primary dark:text-zinc-300" />
            </button>
        </div>
    </div>

    @else
    <!-- MODO 2: VISTA DE LISTA JERÁRQUICA -->
    <div class="rounded-2xl sm:rounded-3xl border border-zinc-200/80 bg-white p-3.5 sm:p-5 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
        <div class="mb-3 sm:mb-4 flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-100 pb-2.5 sm:pb-3 dark:border-zinc-800 gap-1">
            <div class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                {{ __('Estructura Jerárquica de Patrocinio (Hasta :n Niveles)', ['n' => $depth]) }}
            </div>
            <div class="text-[11px] text-zinc-400">
                {{ __('Toca las flechas para expandir o contraer ramas') }}
            </div>
        </div>

        @if ($this->tree)
        <div class="flex flex-col">
            @include('livewire.office.network.partials.unilevel-list-item', [
            'node' => $this->tree,
            'expandedNodes' => $this->expandedNodes,
            ])
        </div>
        @endif
    </div>
    @endif

    <!-- ================================================================= -->
    <!-- MODAL / BOTTOM-SHEET: FICHA TÉCNICA DETALLADA DEL AFILIADO -->
    <!-- ================================================================= -->
    @if ($this->selectedUserDetails)
    @php
    $details = $this->selectedUserDetails;
    @endphp
    <div class="fixed inset-0 !z-[999999] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-xs p-0 sm:p-4 overflow-y-auto cursor-default"
        wire:click.self="closeUserDetails">
        <div class="relative w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl border-t sm:border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 flex flex-col max-h-[90vh] sm:max-h-[85vh] animate-in slide-in-from-bottom duration-200">

            <!-- Barra de arrastre visual en móvil -->
            <div class="flex justify-center pt-2.5 pb-1 sm:hidden">
                <div class="h-1.5 w-12 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
            </div>

            <!-- Encabezado del Modal / Bottom-Sheet -->
            <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-3.5 dark:border-zinc-800 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-2xl bg-primary font-bold text-base sm:text-lg text-white shadow-xs">
                        {{ $details['initials'] }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white truncate">
                            {{ $details['name'] }}
                        </h3>
                        <p class="text-xs text-zinc-500 font-mono truncate">
                            {{ '@' . $details['username'] }} • {{ $details['email'] }}
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="closeUserDetails"
                    class="size-9 flex shrink-0 items-center justify-center rounded-xl text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 cursor-pointer">
                    <flux:icon.x-mark class="size-5" />
                </button>
            </div>

            <!-- Contenido con Scroll Suave -->
            <div class="overflow-y-auto px-5 py-4 flex-1 space-y-4">
                <!-- Resumen Unilevel -->
                <div class="rounded-2xl bg-zinc-50 p-3.5 sm:p-4 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300 flex items-center gap-1.5">
                        <flux:icon.user-group class="size-4" />
                        {{ __('Métricas en Red Escalonada (Unilevel)') }}
                    </span>

                    <div class="mt-3 grid grid-cols-2 gap-2.5 sm:gap-3 text-xs">
                        <div class="rounded-xl bg-white p-3 shadow-xs dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800">
                            <span class="text-zinc-400 text-[11px]">{{ __('Patrocinados Directos:') }}</span>
                            <div class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white mt-0.5">
                                {{ $details['unilevel']['direct_sponsors_count'] }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-white p-3 shadow-xs dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800">
                            <span class="text-zinc-400 text-[11px]">{{ __('Red Total Descendente:') }}</span>
                            <div class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white mt-0.5">
                                {{ $details['unilevel']['total_network_members'] }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-white p-3 shadow-xs dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800">
                            <span class="text-zinc-400 text-[11px]">{{ __('Puntos Personales (PP):') }}</span>
                            <div class="text-sm sm:text-base font-bold text-zinc-900 dark:text-white mt-0.5 font-mono">
                                {{ number_format($details['unilevel']['personal_points'], 2) }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-white p-3 shadow-xs dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800">
                            <span class="text-zinc-400 text-[11px]">{{ __('Puntos Grupales (PG):') }}</span>
                            <div class="text-sm sm:text-base font-bold text-secondary dark:text-zinc-200 mt-0.5 font-mono">
                                {{ number_format($details['unilevel']['group_points'], 2) }}
                            </div>
                            @if (($details['unilevel']['group_waiting_points'] ?? 0) > 0)
                            <div class="mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800 text-[10px] space-y-0.5">
                                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Red activa:') }}</span>
                                    <span class="font-mono font-medium">{{ number_format($details['unilevel']['group_placed_points'], 2) }} pts</span>
                                </div>
                                <div class="flex items-center justify-between text-premium dark:text-zinc-300 font-semibold">
                                    <span class="flex items-center gap-1">
                                        <flux:icon.clock class="size-3 text-premium dark:text-zinc-400" />
                                        {{ __('Sala de espera:') }}
                                    </span>
                                    <span class="font-mono">+{{ number_format($details['unilevel']['group_waiting_points'], 2) }} pts</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-200/40 dark:border-zinc-700/40">
                        <span>{{ __('Patrocinador directo:') }}</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">
                            {{ $details['unilevel']['sponsor_username'] ? '@' . $details['unilevel']['sponsor_username'] : 'Ninguno (Master)' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pie del Modal / Bottom-Sheet -->
            <div class="border-t border-zinc-100 dark:border-zinc-800 p-4 sm:px-5 bg-zinc-50/90 dark:bg-zinc-900/90 backdrop-blur-sm flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2 shrink-0">
                <button
                    type="button"
                    wire:click="closeUserDetails"
                    class="rounded-xl border border-zinc-200 px-4 py-2.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 cursor-pointer text-center min-h-[44px]">
                    {{ __('Cerrar') }}
                </button>

                <button
                    type="button"
                    wire:click="focusNode({{ $details['id'] }}); closeUserDetails();"
                    class="flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-semibold text-white hover:bg-primary/90 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 cursor-pointer shadow-xs min-h-[44px]">
                    <flux:icon.magnifying-glass-plus class="size-4" />
                    <span>{{ __('Enfocar Árbol Unilevel') }}</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>