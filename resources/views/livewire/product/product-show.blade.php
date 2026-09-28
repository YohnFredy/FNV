<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Breadcrumbs -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('products.index') }}" wire:navigate>
            {{ __('Productos') }}
        </flux:breadcrumbs.item>
        @if ($product->category)
        <flux:breadcrumbs.item href="{{ route('products.index') . '?category=' . $product->category->id }}" wire:navigate>
            {{ $product->category->name }}
        </flux:breadcrumbs.item>
        @endif
        <flux:breadcrumbs.item>
            {{ $product->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Ficha Principal del Producto -->
    <div class="rounded-3xl border border-zinc-200/90 bg-white p-6 sm:p-10 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- Columna Izquierda: Galería de Imágenes Proporcional -->
            <div class="lg:col-span-6 space-y-4 lg:sticky lg:top-6">
                <!-- Contenedor Principal de Imagen con Proporción Perfecta y Fondo Limpio -->
                <div class="relative aspect-square max-h-[500px] w-full rounded-2xl border border-zinc-200/80 bg-white dark:border-zinc-800 dark:bg-zinc-950 p-6 sm:p-10 flex items-center justify-center overflow-hidden group shadow-inner">
                    @if ($product->images->isNotEmpty() && isset($product->images[$currentImageIndex]))
                    <img
                        src="{{ asset('storage/' . $product->images[$currentImageIndex]->path) }}"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out" />
                    @elseif ($product->latestImage)
                    <img
                        src="{{ asset('storage/' . $product->latestImage->path) }}"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out" />
                    @else
                    <div class="text-zinc-300 dark:text-zinc-700 flex flex-col items-center gap-2">
                        <flux:icon.photo class="size-20 stroke-1" />
                        <span class="text-xs text-zinc-400 font-medium">{{ __('Sin imagen disponible') }}</span>
                    </div>
                    @endif

                    <!-- Badge de Puntos Acumulables -->
                    @if ($product->pts_base > 0)
                    <span class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-premium text-white shadow-md shadow-ink/60 ring-1 ring-white/30 backdrop-blur-xs">
                        <flux:icon.star class="size-3.5 fill-current text-white" />
                        <span>{{ number_format($product->pts_base, 2) }} <span class="text-[10px] font-semibold uppercase">pts acumulables</span></span>
                    </span>
                    @endif

                    <!-- Badge de Estado de Stock -->
                    @if (!$product->isAvailable())
                    <span class="absolute top-4 right-4 px-3.5 py-1 rounded-full text-xs font-bold bg-danger text-white shadow-md shadow-ink/60 ring-1 ring-white/30">
                        {{ __('Agotado') }}
                    </span>
                    @elseif ($product->is_physical)
                    <span class="absolute top-4 right-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/90 dark:bg-zinc-900/90 text-primary border border-primary/20 backdrop-blur-md shadow-xs">
                        <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
                        <span>{{ __('En Inventario') }}</span>
                    </span>
                    @endif
                </div>

                <!-- Miniaturas de la Galería -->
                @if ($product->images->count() > 1)
                <div class="flex items-center gap-3 overflow-x-auto pb-2 pt-1 scrollbar-none">
                    @foreach ($product->images as $idx => $img)
                    <button
                        type="button"
                        wire:click="changeImage({{ $idx }})"
                        class="size-20 rounded-2xl border-2 p-1.5 bg-white dark:bg-zinc-900 overflow-hidden shrink-0 transition-all cursor-pointer flex items-center justify-center {{ $currentImageIndex === $idx ? 'border-primary ring-2 ring-primary/30 shadow-md shadow-ink/40 scale-105' : 'border-zinc-200 dark:border-zinc-800 opacity-60 hover:opacity-100 hover:border-secondary' }}">
                        <img src="{{ asset('storage/' . $img->path) }}" class="max-h-full max-w-full object-contain" alt="{{ $product->name }}" />
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Columna Derecha: Información y Compra -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Categoría, Marca y Título -->
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2.5">
                        @if ($product->category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-secondary/10 text-secondary border border-secondary/20">
                            {{ $product->category->name }}
                        </span>
                        @endif
                        @if ($product->brand)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-ink/70 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                            <flux:icon.building-storefront class="size-3.5" />
                            {{ $product->brand->name }}
                        </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink dark:text-zinc-50 leading-tight">
                        {{ $product->name }}
                    </h1>
                </div>

                <!-- Bloque Financiero: Precio, Impuestos y Puntos -->
                <div class="p-4 rounded-2xl bg-zinc-50/90 border border-zinc-200/90 dark:bg-zinc-950/60 dark:border-zinc-800 space-y-2 shadow-inner">
                    <div class="flex flex-wrap items-baseline justify-between gap-3">
                        <div>
                            <span class="text-[11px] uppercase font-bold tracking-wider text-ink/50 dark:text-zinc-400 block mb-1">
                                {{ __('Precio al Público') }}
                            </span>
                            <div class="flex items-baseline gap-2.5">
                                <span class="text-3xl font-black text-primary dark:text-zinc-50 tracking-tight">
                                    ${{ formatear_precio($product->final_price) }}
                                </span>
                               
                            </div>
                        </div>

                        @if ($product->pts_base > 0)
                        <div class="text-right">
                            <span class="text-[11px] uppercase font-bold tracking-wider text-premium block mb-1">
                                {{ __('Puntos de Red') }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-premium/15 text-premium text-xs font-bold border border-premium/30">
                                <flux:icon.star class="size-3.5 fill-current" />
                                +{{ number_format($product->pts_base, 2) }} PTS
                            </span>
                        </div>
                        @endif
                    </div>

                    @if ($product->maximum_discount > 0)
                    <div class="flex items-center gap-2 text-xs font-bold text-premium bg-premium/10 px-3.5 py-2 rounded-xl border border-premium/20">
                        <flux:icon.tag class="size-4 shrink-0" />
                        <span>{{ __('Hasta :pct% de descuento exclusivo para Distribuidores Activos.', ['pct' => $product->maximum_discount]) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Disponibilidad y Stock -->
              {{--   <div class="flex items-center gap-2">
                    @if ($product->is_physical)
                        @if ($product->isAvailable())
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold">
                                <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                                @if (($product->stock ?? 0) > 0)
                                    <span>{{ __('Disponible en inventario (:qty unidades)', ['qty' => $product->stock]) }}</span>
                                @else
                                    <span>{{ __('Disponible bajo pedido con entrega prioritaria') }}</span>
                                @endif
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-danger/10 text-danger text-xs font-bold">
                                <span class="size-2 rounded-full bg-danger"></span>
                                <span>{{ __('Agotado temporalmente') }}</span>
                            </div>
                        @endif
                    @else
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-secondary/15 text-secondary text-xs font-bold">
                            <flux:icon.bolt class="size-4" />
                            <span>{{ __('Producto Digital - Activación Inmediata') }}</span>
                        </div>
                    @endif
                </div> --}}

                <!-- Descripción Detallada -->
                @if ($product->description)
                <div class="space-y-2">
                   {{--  <span class="text-xs font-bold uppercase tracking-wider text-ink/60 dark:text-zinc-400">
                        {{ __('Descripción del Producto') }}
                    </span> --}}
                    <div class="prose prose-sm sm:prose-base dark:prose-invert text-ink dark:text-zinc-300 leading-relaxed max-w-none capitalize text-sm">
                        {!! $product->description !!}
                    </div>
                </div>
                @endif

                <!-- Selector de Cantidad y Botón de Compra -->
                <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 space-y-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <!-- Control de Cantidad -->
                        <div class="inline-flex items-center justify-between rounded-2xl border border-zinc-300 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 p-1.5 shadow-sm sm:w-auto">
                            <button
                                type="button"
                                wire:click="decrementQuantity"
                                class="size-10 rounded-xl flex items-center justify-center text-ink/70 hover:text-primary hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-700 font-extrabold transition cursor-pointer"
                                aria-label="{{ __('Disminuir cantidad') }}">
                                -
                            </button>
                            <span class="w-12 text-center text-base font-extrabold text-ink dark:text-zinc-100">
                                {{ $quantity }}
                            </span>
                            <button
                                type="button"
                                wire:click="incrementQuantity"
                                class="size-10 rounded-xl flex items-center justify-center text-ink/70 hover:text-primary hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-700 font-extrabold transition cursor-pointer"
                                aria-label="{{ __('Aumentar cantidad') }}">
                                +
                            </button>
                        </div>

                        <!-- Botón Añadir al Carrito -->
                        <flux:button
                            wire:click="addToCart"
                            variant="primary"
                            icon="shopping-cart"
                            class="flex-1 !py-4 !text-base font-bold !bg-primary hover:!bg-secondary text-white! border-none shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-200 cursor-pointer rounded-2xl">
                            {{ __('Añadir al Carrito') }}
                        </flux:button>
                    </div>

                    <!-- Micro Beneficios de Confianza -->
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-center">
                        <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-zinc-50/60 dark:bg-zinc-950/40">
                            <flux:icon.shield-check class="size-4 text-primary" />
                            <span class="text-[11px] font-semibold text-ink/70 dark:text-zinc-400 leading-tight">{{ __('Garantía Original') }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-zinc-50/60 dark:bg-zinc-950/40">
                            <flux:icon.truck class="size-4 text-secondary" />
                            <span class="text-[11px] font-semibold text-ink/70 dark:text-zinc-400 leading-tight">{{ __('Envío a Nivel Nacional') }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-zinc-50/60 dark:bg-zinc-950/40">
                            <flux:icon.sparkles class="size-4 text-premium" />
                            <span class="text-[11px] font-semibold text-ink/70 dark:text-zinc-400 leading-tight">{{ __('Puntos Calificables') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Especificaciones e Información de Uso -->
    @if ($product->specifications || $product->information)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
        @if ($product->specifications)
        <div class="rounded-3xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none space-y-4">
            <h2 class="text-lg font-bold text-ink dark:text-zinc-50 border-b border-zinc-200/80 dark:border-zinc-800 pb-3 flex items-center gap-3">
                <span class="p-2 rounded-xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-300">
                    <flux:icon.document-text class="size-5" />
                </span>
                {{ __('Especificaciones') }}
            </h2>
            <div class="prose prose-sm dark:prose-invert text-ink/80 dark:text-zinc-300 leading-relaxed">
                {!! $product->specifications !!}
            </div>
        </div>
        @endif

        @if ($product->information)
        <div class="rounded-3xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none space-y-4">
            <h2 class="text-lg font-bold text-ink dark:text-zinc-50 border-b border-zinc-200/80 dark:border-zinc-800 pb-3 flex items-center gap-3">
                <span class="p-2 rounded-xl bg-secondary/15 text-secondary dark:bg-zinc-800 dark:text-zinc-300">
                    <flux:icon.information-circle class="size-5" />
                </span>
                {{ __('Información de Uso') }}
            </h2>
            <div class="prose prose-sm dark:prose-invert text-ink/80 dark:text-zinc-300 leading-relaxed">
                {!! $product->information !!}
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Modal de Carrito de Compra Premium y Elegante -->
    <flux:modal wire:model.live="modalCart" class="w-full max-w-lg sm:max-w-xl !p-6 sm:!p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 shadow-2xl shadow-ink/70 dark:shadow-none">
        <div class="space-y-6">
            <!-- Header con Ícono Elegante y Mensaje -->
            <div class="flex items-start gap-4">
                <div class="size-12 rounded-2xl bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-200 flex items-center justify-center shrink-0 ring-4 ring-primary/5">
                    <flux:icon.check-circle class="size-7 text-primary dark:text-zinc-200" />
                </div>
                <div class="flex-1 pr-4">
                    <h3 class="text-xl font-extrabold text-ink dark:text-zinc-50 tracking-tight">
                        {{ __('¡Producto agregado al carrito!') }}
                    </h3>
                    <p class="text-xs text-ink/60 dark:text-zinc-400 mt-1">
                        {{ __('El producto fue añadido exitosamente a tu orden de compra.') }}
                    </p>
                </div>
            </div>

            <!-- Ficha del Producto Agregado -->
            <div class="p-4 sm:p-5 rounded-2xl bg-zinc-50/90 border border-zinc-200/90 dark:bg-zinc-950/70 dark:border-zinc-800 flex items-center gap-4 sm:gap-5 shadow-inner">
                <!-- Miniatura Proporcional -->
                <div class="size-20 sm:size-24 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden flex items-center justify-center p-2 shrink-0 shadow-xs">
                    @if ($product->images->isNotEmpty() && isset($product->images[$currentImageIndex]))
                    <img src="{{ asset('storage/' . $product->images[$currentImageIndex]->path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain filter drop-shadow-xs" />
                    @elseif ($product->latestImage)
                    <img src="{{ asset('storage/' . $product->latestImage->path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain filter drop-shadow-xs" />
                    @else
                    <flux:icon.photo class="size-8 text-zinc-300 dark:text-zinc-700" />
                    @endif
                </div>

                <!-- Detalles y Precios -->
                <div class="flex-1 min-w-0 space-y-1.5">
                    @if ($product->category)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/10 px-2 py-0.5 rounded-md inline-block">
                        {{ $product->category->name }}
                    </span>
                    @endif
                    <h4 class="text-sm sm:text-base font-bold text-ink dark:text-zinc-100 truncate">
                        {{ $product->name }}
                    </h4>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-ink/60 dark:text-zinc-400">
                            {{ $quantity }} {{ $quantity > 1 ? __('unidades') : __('unidad') }} &times; ${{ formatear_precio($product->final_price) }}
                        </span>
                        <span class="text-sm font-extrabold text-primary dark:text-zinc-100">
                            ${{ formatear_precio($product->final_price * $quantity) }}
                        </span>
                    </div>
                    @if ($product->pts_base > 0)
                    <div class="inline-flex items-center gap-1 text-[11px] font-bold text-premium bg-premium/10 border border-premium/20 px-2.5 py-0.5 rounded-lg">
                        <flux:icon.star class="size-3 fill-current" />
                        <span>+{{ number_format($product->pts_base * $quantity, 2) }} pts ganados</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resumen del Carrito en Sesión -->
            <div class="p-3.5 rounded-2xl bg-zinc-100/70 dark:bg-zinc-800/50 flex items-center justify-between text-xs border border-zinc-200/50 dark:border-zinc-700/50">
                <span class="font-medium text-ink/70 dark:text-zinc-400 flex items-center gap-1.5">
                    <flux:icon.shopping-bag class="size-4 text-primary" />
                    <span>{{ __('Artículos en carrito:') }} <strong class="text-ink dark:text-zinc-200">{{ $cartTotalUnits }}</strong></span>
                </span>
                <span class="font-bold text-ink dark:text-zinc-100">
                    {{ __('Subtotal:') }} <strong class="text-primary font-black text-sm">${{ formatear_precio($cartSubtotal) }}</strong>
                </span>
            </div>

            <!-- Acciones Principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="outline" class="w-full !py-3.5 font-bold border-zinc-300 dark:border-zinc-700 text-ink dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer rounded-2xl">
                        {{ __('Seguir Comprando') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button :href="route('products.cart')" icon="shopping-bag" class="w-full !py-3.5 font-bold !bg-primary hover:!bg-secondary text-white! border-none shadow-md shadow-ink/70 hover:shadow-xl hover:shadow-ink/80 transition-all duration-200 cursor-pointer rounded-2xl" wire:navigate>
                    {{ __('Ir al Carrito') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>