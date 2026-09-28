<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Aviso de envíos solo Colombia -->
    <div x-data="{ show: true }" x-show="show" x-transition.opacity
        class="p-4 rounded-2xl bg-white border border-primary/20 dark:bg-zinc-900 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none flex items-start gap-4 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary via-secondary to-primary"></div>
        <div class="p-2.5 rounded-xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200 mt-0.5">
            <flux:icon.truck class="size-5" />
        </div>
        <div class="flex-1 pr-8">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-ink dark:text-zinc-100">{{ __('Información de Envíos') }}</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-secondary/15 text-secondary dark:bg-zinc-800 dark:text-zinc-300">
                    Nacional
                </span>
            </div>
            <p class="text-xs text-ink/70 dark:text-zinc-400 mt-0.5 leading-relaxed">
                {{ __('Los envíos de productos físicos están disponibles a nivel nacional para toda Colombia. Puntos MLM acreditados al instante.') }}
            </p>
        </div>
        <button @click="show = false" type="button"
            class="text-zinc-400 hover:text-danger dark:hover:text-zinc-200 transition-colors absolute top-4 right-4 cursor-pointer p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <flux:icon.x-mark class="size-4" />
        </button>
    </div>

    <!-- Contenedor principal con filtros laterales y productos -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar con filtros (desktop) -->
        <div class="hidden lg:block lg:w-72 flex-shrink-0">
            <div class="sticky top-6 bg-white rounded-2xl shadow-md shadow-ink/70 border border-zinc-200/90 dark:bg-zinc-900 dark:border-zinc-800 dark:shadow-none flex flex-col max-h-[calc(100vh-3rem)] overflow-hidden">
                <!-- Cabecera de filtros con acción rápida de limpiar si hay filtros activos -->
                <div class="px-5 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/70 dark:bg-zinc-900/90 shrink-0">
                    <div class="flex items-center gap-2">
                        <flux:icon.adjustments-horizontal class="size-4 text-primary dark:text-zinc-300" />
                        <span class="text-xs font-bold uppercase tracking-wider text-ink dark:text-zinc-200">{{ __('Filtros') }}</span>
                    </div>
                    @if ($filter)
                    <button wire:click="clearFilters" type="button"
                        class="text-[11px] font-semibold text-danger hover:text-danger/80 cursor-pointer transition-colors flex items-center gap-1">
                        <flux:icon.x-mark class="size-3" />
                        <span>{{ __('Limpiar') }}</span>
                    </button>
                    @endif
                </div>

                <!-- Contenedor scrolleable independiente -->
                <div class="p-5 divide-y divide-zinc-200/80 dark:divide-zinc-800 space-y-5 overflow-y-auto overscroll-contain custom-scrollbar flex-1">
                    <!-- Navegación de categorías -->
                    <div class="pt-0">
                        <livewire:product.category-listing />
                    </div>

                    <!-- Filtro de marcas -->
                    <div class="pt-5">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <flux:icon.building-storefront class="size-4 text-primary dark:text-zinc-300" />
                            <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300">{{ __('Marcas') }}</span>
                        </div>
                        <flux:radio.group wire:model.live="selectedBrand">
                            <flux:radio value="" :label="__('Todas las Marcas')" />
                            @foreach ($brands as $brand)
                            <flux:radio value="{{ $brand->id }}" label="{{ $brand->name }}" />
                            @endforeach
                        </flux:radio.group>
                    </div>

                    <!-- Filtro de precio -->
                    <div class="pt-5 space-y-2.5">
                        <div class="flex items-center gap-1.5">
                            <flux:icon.banknotes class="size-4 text-primary dark:text-zinc-300" />
                            <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300">{{ __('Rango de Precio') }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <flux:input wire:model.live.debounce.500ms="priceMin" type="number" size="sm"
                                placeholder="Mínimo" min="0" />
                            <flux:input wire:model.live.debounce.500ms="priceMax" type="number" size="sm"
                                placeholder="Máximo" min="0" />
                        </div>
                    </div>

                    <!-- Filtro de disponibilidad -->
                    <div class="pt-5">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <flux:icon.check-circle class="size-4 text-primary dark:text-zinc-300" />
                            <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300">{{ __('Disponibilidad') }}</span>
                        </div>
                        <flux:radio.group wire:model.live="inStock">
                            <flux:radio value="all" :label="__('Todos')" />
                            <flux:radio value="1" :label="__('En stock')" />
                            <flux:radio value="0" :label="__('Agotados')" />
                        </flux:radio.group>
                    </div>

                    <!-- Filtro de tipo de producto -->
                    <div class="pt-5">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <flux:icon.cube class="size-4 text-primary dark:text-zinc-300" />
                            <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300">{{ __('Tipo de Producto') }}</span>
                        </div>
                        <flux:radio.group wire:model.live="isPhysical">
                            <flux:radio value="all" :label="__('Todos')" />
                            <flux:radio value="1" :label="__('Físicos')" />
                            <flux:radio value="0" :label="__('Digitales')" />
                        </flux:radio.group>
                    </div>

                    @if ($filter)
                    <div class="pt-5">
                        <flux:button wire:click="clearFilters" size="sm" icon="x-mark" class="w-full !bg-danger/10 hover:!bg-danger text-danger! hover:text-white! border border-danger/20 font-semibold transition-all">
                            {{ __('Limpiar Filtros') }}
                        </flux:button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sección de productos -->
        <div class="flex-1 space-y-6">
            <!-- Barra superior: Búsqueda y Ordenamiento -->
            <div class="bg-white rounded-2xl border border-zinc-200/90 shadow-md shadow-ink/70 dark:bg-zinc-900 dark:border-zinc-800 dark:shadow-none p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Dropdown móvil de categorías -->
                <div x-data="{ open: $wire.entangle('showDropdown') }" class="lg:hidden relative">
                    <flux:button x-on:click="open = !open" icon="adjustments-horizontal" variant="outline" class="w-full !border-primary/30 !text-primary dark:!text-zinc-200">
                        {{ __('Filtrar Categorías') }}
                    </flux:button>
                    <div x-show="open" x-on:click.outside="open = false" x-cloak
                        class="absolute mt-2 left-0 w-full p-4 z-50 bg-white border border-zinc-200 rounded-2xl shadow-xl shadow-ink/70 dark:bg-zinc-900 dark:border-zinc-800">
                        <livewire:product.category-listing />
                    </div>
                </div>

                <!-- Buscador -->
                <div class="w-full sm:w-88 relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="w-full rounded-xl border border-zinc-300 bg-zinc-50/80 px-3.5 py-2.5 pl-10 text-sm text-ink placeholder-ink/40 transition-all duration-200 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:focus:border-zinc-500"
                        placeholder="Buscar por nombre, descripción o marca..." />
                    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-primary/70 dark:text-zinc-400">
                        <flux:icon.magnifying-glass class="size-4.5" />
                    </div>
                </div>

                <!-- Ordenamiento -->
                <div class="flex items-center gap-2.5">
                    <span class="text-xs font-semibold text-ink/70 dark:text-zinc-400 whitespace-nowrap">{{ __('Ordenar por:') }}</span>
                    <select
                        wire:model.live="sortBy"
                        class="rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-ink dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer transition-colors">
                        <option value="">{{ __('Más Puntos MLM') }}</option>
                        <option value="price_asc">{{ __('Menor Precio') }}</option>
                        <option value="price_desc">{{ __('Mayor Precio') }}</option>
                        <option value="name_asc">{{ __('Nombre (A-Z)') }}</option>
                        <option value="newest">{{ __('Más Nuevos') }}</option>
                    </select>
                </div>
            </div>

            <!-- Grilla de productos -->
            @if ($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($products as $product)
                <div
                    wire:key="prod-card-{{ $product->id }}"
                    class="group rounded-2xl border border-zinc-200/90 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 dark:shadow-none hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden">
                    <!-- Contenedor Imagen -->
                    <a href="{{ route('products.show', $product) }}" class="relative aspect-square overflow-hidden bg-zinc-100/80 dark:bg-zinc-950 block">
                        @if ($product->latestImage)
                        <img
                            src="{{ asset('storage/' . $product->latestImage->path) }}"
                            alt="{{ $product->name }}"
                            class="size-full object-cover object-center group-hover:scale-108 transition-transform duration-500 ease-out" />
                        @else
                        <div class="size-full flex flex-col items-center justify-center text-zinc-300 dark:text-zinc-700 bg-zinc-50 dark:bg-zinc-950">
                            <flux:icon.photo class="size-16 stroke-1" />
                            <span class="text-[11px] font-medium mt-2 text-zinc-400">Sin imagen</span>
                        </div>
                        @endif

                        <!-- Gradiente sutil inferior sobre la foto para contraste -->
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

                        <!-- Badge Puntos MLM (Color Premium con brillo) -->
                        @if ($product->pts_base > 0)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-premium text-white shadow-md shadow-ink/60 ring-1 ring-white/30 backdrop-blur-xs">
                            <flux:icon.star class="size-3.5 fill-current text-white" />
                            <span>{{ number_format($product->pts_base, 2) }} <span class="text-[10px] font-semibold opacity-90 uppercase">pts</span></span>
                        </span>
                        @endif

                        @if (!$product->isAvailable())
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[11px] font-bold bg-danger text-white shadow-md shadow-danger/40 ring-1 ring-white/30">
                            {{ __('Agotado') }}
                        </span>
                        @endif
                    </a>

                    <!-- Contenido Tarjeta -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <!-- Metadatos: Categoría y Marca -->
                            <div class="flex items-center justify-between text-xs">
                                <span class="inline-flex items-center font-semibold text-secondary dark:text-zinc-400 tracking-wide uppercase text-[11px]">
                                    {{ $product->category?->name ?? 'Catálogo' }}
                                </span>
                                @if ($product->brand)
                                <span class="px-2 py-0.5 rounded-md font-medium text-[11px] bg-zinc-100 text-ink/80 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $product->brand->name }}
                                </span>
                                @endif
                            </div>

                            <!-- Título del Producto -->
                            <h3 class="font-bold text-base text-ink dark:text-zinc-100 group-hover:text-primary transition-colors duration-200 line-clamp-1">
                                <a href="{{ route('products.show', $product) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Descripción -->
                            <p class="text-xs text-ink/65 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                {{ strip_tags($product->description) }}
                            </p>
                        </div>

                        <!-- Bloque Inferior: Precio y Acción -->
                        <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-semibold text-ink/50 dark:text-zinc-400 block tracking-wider">{{ __('Precio Público') }}</span>
                                <span class="text-xl font-extrabold text-primary dark:text-zinc-50 tracking-tight">
                                    ${{ formatear_precio($product->final_price) }}
                                </span>
                            </div>

                            <flux:button :href="route('products.show', $product)" size="sm" icon="eye" class="!bg-primary hover:!bg-secondary text-white! font-semibold px-3.5 py-2 rounded-xl shadow-sm shadow-ink/40 transition-all duration-200">
                                {{ __('Ver Detalle') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pt-6">
                {{ $products->links() }}
            </div>
            @else
            <div class="p-12 text-center rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                <flux:icon.shopping-bag class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto" />
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">{{ __('No se encontraron productos') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                    {{ __('Intenta ajustar los filtros de búsqueda, categorías o rangos de precios.') }}
                </p>
                @if ($filter)
                <flux:button wire:click="clearFilters" size="sm" variant="outline">
                    {{ __('Ver todos los productos') }}
                </flux:button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>