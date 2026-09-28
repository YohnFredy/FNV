<div class="w-full min-h-screen pb-16 bg-zinc-50 dark:bg-zinc-950 text-ink dark:text-zinc-100" x-data="{ filtersOpen: false }">
    <!-- CARRUSEL DE IMÁGENES A PANTALLA COMPLETA (FULL-BLEED) -->
    <div wire:ignore class="w-full relative bg-ink dark:bg-zinc-900 overflow-hidden shadow-md shadow-ink/70 dark:shadow-none">
        @if ($banners->count() > 0)
        <div x-data="{
                active: 0,
                total: {{ $banners->count() }},
                timer: null,
                touchStartX: 0,
                touchEndX: 0,
                startAutoplay() {
                    if (this.total > 1) {
                        this.timer = setInterval(() => { this.next() }, 9000);
                    }
                },
                stopAutoplay() {
                    if (this.timer) clearInterval(this.timer);
                },
                next() {
                    this.active = (this.active + 1) % this.total;
                },
                prev() {
                    this.active = (this.active - 1 + this.total) % this.total;
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    if (this.touchStartX - this.touchEndX > 40) this.next();
                    if (this.touchEndX - this.touchStartX > 40) this.prev();
                }
            }" x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()"
            @touchstart="handleTouchStart($event)" @touchend="handleTouchEnd($event)"
            class="relative w-full h-[120px] sm:h-[219px] md:h-[263px] lg:h-[350px] xl:h-[456px] select-none">

            <!-- Slides -->
            @foreach ($banners as $index => $banner)
            <div x-show="active === {{ $index }}" @if(!$loop->first) x-cloak @endif
                x-transition:enter="transition ease-out duration-700 transform-gpu"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-500 transform-gpu"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute inset-0 w-full h-full flex items-center justify-center">

                <!-- Skeleton loader de fondo mientras carga la imagen -->
                <div class="absolute inset-0 bg-zinc-800 animate-pulse flex items-center justify-center -z-10">
                    <svg class="w-8 h-8 text-zinc-600 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                @if ($banner->link_url)
                @php
                $isInternalLink = str_starts_with($banner->link_url, '/') || str_starts_with($banner->link_url, url('/'));
                @endphp
                <a href="{{ $banner->link_url }}" @if($isInternalLink) wire:navigate @endif class="block w-full h-full group relative">
                    <img src="{{ $banner->image_url }}"
                        alt="{{ $banner->title ?? 'Banner' }}"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        @if($loop->first) fetchpriority="high" @endif
                        class="w-full h-full object-fill group-hover:scale-101 transition-transform duration-700">
                    @if ($banner->title)
                    <div class="hidden sm:block absolute bottom-0 inset-x-0 bg-gradient-to-t from-ink/90 via-ink/50 to-transparent dark:from-zinc-950/90 dark:via-zinc-950/50 p-6 text-white pt-12">
                        <h3 class="text-xl sm:text-2xl font-bold max-w-4xl mx-auto drop-shadow-md">
                            {{ $banner->title }}
                        </h3>
                    </div>
                    @endif
                </a>
                @else
                <img src="{{ $banner->image_url }}"
                    alt="{{ $banner->title ?? 'Banner' }}"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    @if($loop->first) fetchpriority="high" @endif
                    class="w-full h-full object-fill">
                @endif
            </div>
            @endforeach

            <!-- Flechas de navegación (si hay más de 1 banner) -->
            @if ($banners->count() > 1)
            <button @click="prev()" type="button" aria-label="Anterior"
                class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 sm:w-12 sm:h-12 bg-white/30 hover:bg-white/70 text-white hover:text-ink rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 cursor-pointer">
                <svg class="w-4 sm:w-6 h-4 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button @click="next()" type="button" aria-label="Siguiente"
                class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 sm:w-12 sm:h-12 bg-white/30 hover:bg-white/70 text-white hover:text-ink rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 cursor-pointer">
                <svg class="w-4 sm:w-6 h-4 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Puntos indicativos bottom -->
            <div class="absolute bottom-2.5 inset-x-0 z-10 flex items-center justify-center gap-2">
                @foreach ($banners as $index => $banner)
                <button @click="active = {{ $index }}" type="button"
                    aria-label="Ir a slide {{ $index + 1 }}"
                    :class="active === {{ $index }} ? 'w-8 bg-white/80 shadow-md' : 'w-2.5 bg-white/50 hover:bg-white'"
                    class="h-2.5 rounded-full transition-all duration-300 cursor-pointer"></button>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <!-- Banner alternativo cuando no hay imágenes cargadas -->
        <div class="w-full h-[200px] sm:h-[280px] bg-gradient-to-r from-primary via-secondary to-primary dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900 flex flex-col items-center justify-center text-center px-4 text-white relative">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white/15 dark:bg-zinc-800/80 backdrop-blur-md rounded-2xl mb-3 shadow-inner">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11.02">
                    </path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-4xl font-bold tracking-tight mb-2">Directorio de Empresas Aliadas</h1>
            <p class="text-sm sm:text-base text-white/90 max-w-2xl font-light">
                Descubre comercios y empresas de toda la región. Filtra por categoría, ubicación y encuentra exactamente lo que buscas.
            </p>
        </div>
        @endif
    </div>

    <!-- CONTENEDOR DE BÚSQUEDA Y FILTROS SOBREPUESTO AL CARRUSEL -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 relative z-10 -mt-6 sm:-mt-8 mb-8">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 p-4 sm:p-6 transition-all duration-300">

            <!-- Cabecera Móvil (Solo visible en pantallas <640px) -->
            <div class="flex items-center justify-between mb-3 sm:hidden">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center shadow-md shadow-ink/50 dark:shadow-none shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-primary dark:text-zinc-100 tracking-tight">Buscar</span>
                </div>

                <!-- Botón Filtros (Móvil <640px) -->
                <button @click="filtersOpen = !filtersOpen" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-secondary hover:bg-primary active:scale-95 rounded-xl shadow-md shadow-ink/60 dark:shadow-none dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 transition-all shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    <span>Filtros</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="filtersOpen && 'rotate-180'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Fila Principal: Buscador y Botón de Filtros Desktop -->
            <div class="sm:flex sm:items-center sm:gap-4">
                <!-- Buscador -->
                <div class="relative w-full sm:flex-1">
                    <input type="text" id="search" wire:model.live.debounce.300ms="search"
                        @keydown.enter.prevent="$el.blur()"
                        class="block w-full pl-10 pr-4 py-2.5 sm:py-3 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-ink dark:text-zinc-100 text-sm sm:text-base rounded-xl focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:focus:border-zinc-500 transition-all font-medium placeholder:text-zinc-400 dark:placeholder:text-zinc-500 shadow-sm"
                        placeholder="¿Qué estás buscando?">

                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-primary dark:text-zinc-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Botón de Filtros (Escritorio ≥640px) -->
                <button @click="filtersOpen = !filtersOpen" type="button"
                    class="hidden sm:inline-flex items-center gap-2 px-6 py-3 text-base font-semibold text-white bg-secondary hover:bg-primary active:scale-95 rounded-xl shadow-md shadow-ink/60 dark:shadow-none dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 transition-all shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    <span>Filtros</span>
                    <svg class="w-6 h-6 transition-transform duration-200" :class="filtersOpen && 'rotate-180'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Sugerencia de corrección ortográfica / Did you mean -->
            @if ($suggestedCorrection && mb_strtolower(trim($suggestedCorrection)) !== mb_strtolower(trim($search)))
            <div class="mt-2.5 flex items-center gap-1.5 bg-primary/10 border border-primary/20 dark:bg-zinc-800/60 dark:border-zinc-700 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-ink dark:text-zinc-200">
                <span class="text-primary dark:text-zinc-300 font-medium flex items-center gap-1 shrink-0">
                    <svg class="w-4 h-4 text-primary dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mostrando resultados para:
                </span>
                <button wire:click="applySuggestedCorrection" type="button" class="font-bold text-primary hover:text-secondary dark:text-zinc-100 dark:hover:text-zinc-300 hover:underline cursor-pointer">
                    "{{ $suggestedCorrection }}"
                </button>
            </div>
            @endif

            <!-- Panel de Filtros Desplegable -->
            <div x-show="filtersOpen" x-cloak x-collapse class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <!-- Categoría -->
                    <x-select wire:model.live="selectedCategory" label="Categoría:"
                        placeholder="Todas las categorías..." :options="$categories" />

                    <!-- Subcategoría -->
                    @if (count($subcategories) > 0)
                    <x-select wire:model.live="selectedSubcategory" label="Subcategoría:"
                        placeholder="Todas las subcategorías..." :options="$subcategories" />
                    @endif

                    <!-- Tipo de tienda -->
                    <x-select wire:model.live="selectedStoreType" label="Tipo de tienda:"
                        placeholder="Todos los tipos..." :options="$storeTypes" />

                    <!-- País -->
                    <x-select wire:model.live="selectedCountry" label="País:"
                        placeholder="Todos los países..." :options="$countries" />

                    <!-- Departamento -->
                    @if (count($departments) > 0)
                    <x-select wire:model.live="selectedDepartment" label="Departamento:"
                        placeholder="Todos los departamentos..." :options="$departments" />
                    @endif

                    <!-- Ciudad -->
                    @if (count($cities) > 0)
                    <x-select wire:model.live="selectedCity" label="Ciudad:"
                        placeholder="Todas las ciudades..." :options="$cities" />
                    @endif

                    <!-- Limpiar Filtros -->
                    @if ($search || $selectedCategory || $selectedSubcategory || $selectedStoreType || $selectedDepartment || $selectedCity || ($selectedCountry && $selectedCountry != 1))
                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end mt-2">
                        <button wire:click="clearFilters" type="button"
                            class="inline-flex items-center gap-2 py-2.5 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 active:scale-95 text-ink dark:text-zinc-200 font-semibold text-sm rounded-xl transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4 text-danger animate-spin" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>{{ __('Limpiar Filtros') }}</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL DE COMERCIOS -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">

        <!-- Vista de Escritorio/Tablet — Grid de tarjetas -->
        <div class="hidden md:block">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse ($businessData as $data)
                <a href="{{ route('companies.show', $data) }}" wire:navigate
                    class="group relative bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none hover:shadow-xl hover:shadow-ink/80 dark:hover:border-zinc-700 hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                    <!-- Acento lateral con gradiente -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary via-secondary to-primary dark:from-zinc-700 dark:via-zinc-600 dark:to-zinc-700 rounded-l-2xl group-hover:from-secondary group-hover:via-primary group-hover:to-secondary transition-all"></div>

                    <div class="pl-5 pr-5 pt-5 pb-4 flex flex-col flex-1">
                        <!-- Cabecera: Logo + Nombre + Ciudad -->
                        <div class="flex items-start gap-4 mb-3">
                            <!-- Logo -->
                            <div class="shrink-0 w-16 h-16 rounded-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-md shadow-ink/50 dark:shadow-none flex items-center justify-center overflow-hidden">
                                @if ($data->business->latestLogo)
                                <img src="{{ asset('storage/' . $data->business->latestLogo->path) }}"
                                    alt="{{ $data->business->name }}"
                                    class="max-w-full max-h-full object-contain">
                                @else
                                <svg class="w-7 h-7 text-primary/60 dark:text-zinc-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11.02">
                                    </path>
                                </svg>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-ink dark:text-zinc-100 group-hover:text-primary dark:group-hover:text-zinc-300 transition-colors truncate">
                                    {{ $data->business->name ?? 'N/A' }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-secondary dark:text-zinc-300 bg-secondary/10 dark:bg-zinc-800 border border-secondary/20 dark:border-zinc-700 px-2.5 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $data->cityRelation->name ?? ($data->city ?? 'Sin ubicación') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Flecha hover -->
                            <div class="shrink-0 w-10 h-10 rounded-full bg-primary/10 dark:bg-zinc-800 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-1 group-hover:translate-x-0">
                                <svg class="w-4 h-4 text-primary dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Descripción -->
                        @if ($data->description)
                        <div class="text-sm text-ink/70 dark:text-zinc-400 leading-relaxed line-clamp-3 prose prose-sm dark:prose-invert max-w-none mt-auto">
                            {!! $data->description !!}
                        </div>
                        @else
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 italic mt-auto">Sin descripción disponible</p>
                        @endif
                    </div>

                    <!-- Pie de tarjeta -->
                    <div class="px-5 py-2.5 bg-zinc-50 dark:bg-zinc-950/60 border-t border-zinc-200 dark:border-zinc-800">
                        <span class="inline-flex items-center text-sm gap-1.5 font-semibold text-primary group-hover:text-secondary dark:text-zinc-300 dark:group-hover:text-zinc-100 transition-colors">
                            <i class="far fa-eye"></i>
                            Ver detalle del comercio
                            <svg class="w-3 h-3 translate-x-0 group-hover:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                    </div>
                </a>
                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-primary/10 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-5 shadow-inner">
                            <svg class="w-10 h-10 text-primary/60 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-ink dark:text-zinc-100 mb-2">No se encontraron comercios</h3>
                        <p class="text-ink/70 dark:text-zinc-400 max-w-sm">Intenta cambiar los filtros o limpiarlos para ver más resultados.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Vista Móvil — Tarjetas refinadas -->
        <div class="md:hidden">
            <div class="space-y-3.5">
                @forelse ($businessData as $data)
                <a href="{{ route('companies.show', $data) }}" wire:navigate
                    class="group block relative bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none active:shadow-lg active:scale-[0.99] transition-all duration-200 overflow-hidden">
                    <!-- Acento lateral con gradiente -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary via-secondary to-primary dark:from-zinc-700 dark:via-zinc-600 dark:to-zinc-700 rounded-l-2xl"></div>

                    <!-- Cabecera -->
                    <div class="flex items-center gap-3 pl-5 pr-4 pt-4 pb-3">
                        <!-- Logo -->
                        <div class="shrink-0 w-14 h-14 rounded-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-md shadow-ink/50 dark:shadow-none flex items-center justify-center overflow-hidden">
                            @if ($data->business->latestLogo)
                            <img src="{{ asset('storage/' . $data->business->latestLogo->path) }}"
                                alt="{{ $data->business->name }}"
                                class="max-w-full max-h-full object-contain">
                            @else
                            <svg class="w-6 h-6 text-primary/60 dark:text-zinc-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11.02">
                                </path>
                            </svg>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-ink dark:text-zinc-100 text-[15px] leading-tight truncate">
                                {{ $data->business->name ?? 'N/A' }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-secondary dark:text-zinc-300 bg-secondary/10 dark:bg-zinc-800 border border-secondary/20 dark:border-zinc-700 px-2.5 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $data->cityRelation->name ?? ($data->city ?? 'Sin ubicación') }}
                                </span>
                            </div>
                        </div>

                        <!-- Flecha -->
                        <div class="shrink-0 text-zinc-400 dark:text-zinc-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Descripción -->
                    @if ($data->description)
                    <div class="pl-5 pr-4 pb-3">
                        <div class="text-[13px] text-ink/70 dark:text-zinc-400 leading-relaxed line-clamp-3 prose prose-sm dark:prose-invert max-w-none">
                            {!! $data->description !!}
                        </div>
                    </div>
                    @endif

                    <!-- Pie -->
                    <div class="px-5 py-2.5 bg-zinc-50 dark:bg-zinc-950/60 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary dark:text-zinc-300">
                            <i class="far fa-eye"></i>
                            Ver comercio
                        </span>
                        <svg class="w-3.5 h-3.5 text-primary dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-primary/10 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-primary/60 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-ink dark:text-zinc-100 mb-2">No se encontraron comercios</h3>
                    <p class="text-ink/70 dark:text-zinc-400 text-sm px-4">Intenta cambiar los filtros o limpiarlos para ver más resultados.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Paginación -->
        @if ($businessData->hasPages())
        <div class="shadow-md shadow-ink/70 dark:shadow-none bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 mt-6">
            {{ $businessData->links() }}
        </div>
        @endif

        <!-- Footer estadísticas -->
        <div class="mt-8 md:mt-12 text-center">
            <div class="inline-flex items-center px-6 py-3 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none">
                <svg class="w-5 h-5 text-primary dark:text-zinc-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span class="text-ink dark:text-zinc-200 font-medium">
                    @if ($businessData->total() > 0)
                    {{ $businessData->total() }} comercios encontrados
                    @else
                    Directorio de comercios disponible
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Back to top button -->
    <button id="backToTop"
        class="fixed cursor-pointer bottom-6 right-6 bg-primary hover:bg-secondary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-ink/70 dark:shadow-none dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 transition duration-300 z-50"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Script para scroll to top -->
    <script>
        window.addEventListener('scroll', function() {
            var backToTopButton = document.getElementById('backToTop');
            if (backToTopButton) {
                if (window.pageYOffset > 300) {
                    backToTopButton.style.display = 'flex';
                } else {
                    backToTopButton.style.display = 'none';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            var backToTopButton = document.getElementById('backToTop');
            if (backToTopButton) {
                backToTopButton.style.display = 'none';
            }
        });
    </script>
</div>