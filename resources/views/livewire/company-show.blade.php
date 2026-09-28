@php
$companyShareUrl = route('companies.show', $businessData);
$companyShareMessage = '¡Mira este comercio aliado en Fornuvi! 🛍️ *' . $businessData->business->name . "*\n\n" . $companyShareUrl;
$whatsappShareUrl = 'https://api.whatsapp.com/send?text=' . urlencode($companyShareMessage);
$facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($companyShareUrl);
$twitterShareUrl = 'https://twitter.com/intent/tweet?text=' . urlencode('Conoce a ' . $businessData->business->name . ' en Fornuvi') . '&url=' . urlencode($companyShareUrl);
$telegramShareUrl = 'https://t.me/share/url?url=' . urlencode($companyShareUrl) . '&text=' . urlencode('¡Mira este comercio aliado en Fornuvi! ' . $businessData->business->name);
@endphp

<div class="w-full min-h-screen pb-16 bg-zinc-50 dark:bg-zinc-950 text-ink dark:text-zinc-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
            <flux:breadcrumbs.item href="{{ route('companies.index') }}">Aliados</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $businessData->business->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <!-- Header con información básica -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-zinc-50 via-white to-zinc-50 dark:from-zinc-900 dark:via-zinc-950 dark:to-zinc-900 overflow-hidden">
                <div class="relative p-5 sm:p-8">
                    <div class="relative">
                        <!-- Layout vertical en móviles, horizontal en pantallas grandes -->
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 sm:gap-4">

                            <!-- Información textual -->
                            <div class="flex-1">
                                <h1 class="text-2xl sm:text-4xl font-bold text-ink dark:text-zinc-100 mb-2">
                                    {{ $businessData->business->name }}
                                </h1>

                                @if ($businessData->business->nit)
                                <p class="text-base sm:text-lg text-ink/80 dark:text-zinc-300 mb-2">
                                    <strong>NIT:</strong> {{ $businessData->business->nit }}
                                </p>
                                @endif

                                @if ($businessData->description)
                                <div x-data="{
                                    expanded: false,
                                    canExpand: false,
                                    checkOverflow() {
                                        this.$nextTick(() => {
                                            if (this.$refs.content) {
                                                this.canExpand = this.$refs.content.scrollHeight > (this.$refs.content.clientHeight + 4);
                                            }
                                        });
                                    }
                                }" x-init="checkOverflow()" @resize.window.debounce.150ms="checkOverflow()">
                                    <div x-ref="content"
                                        class="text-justify [&_*]:!text-justify prose prose-sm dark:prose-invert max-w-none text-ink/80 dark:text-zinc-300 line-clamp-5"
                                        :class="{ 'line-clamp-5': !expanded, 'line-clamp-none': expanded }">
                                        {!! $businessData->description !!}
                                    </div>

                                    <div x-show="canExpand" x-cloak>
                                        <button @click="expanded = !expanded" type="button"
                                            class="mt-2 text-sm font-semibold text-secondary hover:text-primary dark:text-zinc-300 dark:hover:text-zinc-100 transition-colors cursor-pointer inline-flex items-center gap-1 group">
                                            <span x-show="!expanded" class="inline-flex items-center gap-1">
                                                <span>Leer más...</span>
                                                <svg class="w-4 h-4 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                            <span x-show="expanded" x-cloak class="inline-flex items-center gap-1">
                                                <span>Ver menos</span>
                                                <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                @endif

                                <!-- Botones de Compartir y Copiar Enlace en Encabezado -->
                                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center gap-2 sm:gap-3"
                                    x-data="{
                                        copied: false,
                                        copyUrl() {
                                            const url = '{{ $companyShareUrl }}';
                                            if (navigator.clipboard && window.isSecureContext) {
                                                navigator.clipboard.writeText(url).then(() => {
                                                    this.copied = true;
                                                    setTimeout(() => this.copied = false, 2500);
                                                });
                                            } else {
                                                const el = document.createElement('textarea');
                                                el.value = url;
                                                el.style.position = 'fixed';
                                                el.style.opacity = '0';
                                                document.body.appendChild(el);
                                                el.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(el);
                                                this.copied = true;
                                                setTimeout(() => this.copied = false, 2500);
                                            }
                                        }
                                    }">
                                    <span class="text-xs font-semibold text-ink/70 dark:text-zinc-400 flex items-center gap-1.5 mr-1">
                                        <i class="fas fa-share-alt text-primary dark:text-zinc-300 text-sm"></i>
                                        Compartir:
                                    </span>

                                    <!-- Botón WhatsApp -->
                                    <a href="{{ $whatsappShareUrl }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#25D366] hover:bg-[#20ba59] text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-ink/40 dark:shadow-none hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer no-underline">
                                        <i class="fab fa-whatsapp text-base"></i>
                                        <span>Compartir por WhatsApp</span>
                                    </a>

                                    <!-- Botón Copiar Enlace -->
                                    <button @click="copyUrl()" type="button"
                                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-ink dark:text-zinc-200 text-xs sm:text-sm font-medium rounded-xl border border-zinc-300 dark:border-zinc-700 shadow-sm transition-all duration-200 cursor-pointer">
                                        <span x-show="!copied" class="inline-flex items-center gap-1.5 text-ink dark:text-zinc-200">
                                            <i class="fas fa-link text-secondary dark:text-zinc-400"></i>
                                            <span>Copiar enlace</span>
                                        </span>
                                        <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5 text-primary dark:text-zinc-100 font-semibold">
                                            <i class="fas fa-check text-primary dark:text-zinc-200"></i>
                                            <span>¡Enlace copiado!</span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Logo y tipo de tienda -->
                            <div class="flex flex-col items-center sm:items-center sm:ml-6 text-center">
                                {{-- Logo de la tienda --}}
                                @if ($businessData->business->latestLogo)
                                <div class="w-20 h-20 rounded-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-md shadow-ink/50 dark:shadow-none flex items-center justify-center overflow-hidden mb-4">
                                    <img src="{{ asset('storage/' . $businessData->business->latestLogo->path) }}"
                                        alt="{{ $businessData->business->name }}" class="max-w-full max-h-full object-contain">
                                </div>
                                @endif

                                {{-- Badge con ícono y tipos de tienda centrados --}}
                                @if ($businessData->storeTypes->isNotEmpty())
                                <div class="flex items-center px-4 py-2 rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/80 text-left">
                                    {{-- Ícono alineado al centro verticalmente --}}
                                    <i class="fas fa-store text-xl text-primary dark:text-zinc-300 mr-3"></i>

                                    {{-- Tipos de tienda uno debajo del otro, centrados verticalmente --}}
                                    <div class="flex flex-col justify-center">
                                        @foreach ($businessData->storeTypes as $type)
                                        <span wire:key="store-type-{{ $type->id }}" class="capitalize font-medium text-xs text-ink dark:text-zinc-200 text-center">
                                            {{ __($type->name) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galería de imágenes de la empresa -->
        @php
        $images = $businessData->business->images
        ->map(function ($image) use ($businessData) {
        return [
        'url' => asset('storage/' . $image->path),
        'alt' => $businessData->business->name,
        'is_primary' => false,
        ];
        })
        ->toArray();
        $displayImages = array_slice($images, 0, 10);
        @endphp

        @if (count($displayImages) > 0)
        <div x-data="{
                open: false,
                activeImage: null,
                activeAlt: '',
                scale: 1,
                panning: false,
                pointX: 0,
                pointY: 0,
                startX: 0,
                startY: 0,
                
                openLightbox(url, alt) {
                    this.activeImage = url;
                    this.activeAlt = alt;
                    this.open = true;
                    this.resetZoom();
                    document.body.style.overflow = 'hidden'; 
                },
                closeLightbox() {
                    this.open = false;
                    this.activeImage = null;
                    this.resetZoom();
                    document.body.style.overflow = 'auto'; 
                },
                resetZoom() {
                    this.scale = 1;
                    this.panning = false;
                    this.pointX = 0;
                    this.pointY = 0;
                },
                handleWheel(e) {
                    const delta = e.deltaY > 0 ? -0.2 : 0.2;
                    let newScale = this.scale + delta;
                    this.scale = Math.min(Math.max(1, newScale), 5);
                    
                    if (this.scale === 1) {
                        this.pointX = 0;
                        this.pointY = 0;
                    }
                },
                startDrag(e) {
                    if (this.scale <= 1) return;
                    e.preventDefault();
                    this.panning = true;
                    this.startX = e.clientX - this.pointX;
                    this.startY = e.clientY - this.pointY;
                },
                drag(e) {
                    if (!this.panning) return;
                    e.preventDefault();
                    this.pointX = e.clientX - this.startX;
                    this.pointY = e.clientY - this.startY;
                },
                endDrag() {
                    this.panning = false;
                }
            }"
            @keydown.escape.window="closeLightbox()"
            @mouseup.window="endDrag()">

            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 overflow-hidden mb-8">
                <div class="p-5 sm:p-10">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 min-w-12 bg-gradient-to-br from-primary to-secondary dark:from-zinc-800 dark:to-zinc-700 text-white rounded-xl flex items-center justify-center mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                            <i class="fas fa-images text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-ink dark:text-zinc-100">
                            <strong class="text-primary dark:text-zinc-300">Galería de:</strong> {{ $businessData->business->name }}
                        </h2>
                    </div>

                    <!-- Masonry Layout -->
                    <div class="columns-1 md:columns-2 lg:columns-3 gap-4 space-y-4">
                        @foreach ($displayImages as $index => $img)
                        <div wire:key="gallery-image-{{ $index }}" class="break-inside-avoid relative group overflow-hidden rounded-xl cursor-zoom-in border border-zinc-200 dark:border-zinc-800 shadow-md shadow-ink/70 dark:shadow-none hover:shadow-xl hover:shadow-ink/80 dark:hover:border-zinc-700 transition-all duration-300"
                            @click="openLightbox('{{ $img['url'] }}', '{{ $img['alt'] }}')">
                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}"
                                class="w-full h-auto object-contain bg-zinc-100 dark:bg-zinc-950 transition-transform duration-500 group-hover:scale-105"
                                loading="lazy" />

                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                <i class="fas fa-expand-arrows-alt text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 drop-shadow-md"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Lightbox (Modal) -->
            <div x-show="open" x-transition.opacity.duration.300ms
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 backdrop-blur-md p-4"
                style="display: none;">

                <!-- Botón Cerrar -->
                <button @click="closeLightbox()"
                    class="absolute top-4 right-4 text-white/70 hover:text-white z-50 p-2 transition-colors bg-black/40 rounded-full cursor-pointer">
                    <i class="fas fa-times text-2xl sm:text-3xl"></i>
                </button>

                <!-- Botones de Control de Zoom -->
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex items-center gap-4 bg-zinc-900/80 border border-zinc-700 px-4 py-2 rounded-full text-zinc-200 z-50">
                    <button @click="scale = Math.max(1, scale - 0.5); if(scale===1){pointX=0;pointY=0;}" class="hover:text-primary transition cursor-pointer"><i class="fas fa-minus"></i></button>
                    <span class="text-sm font-mono w-12 text-center" x-text="Math.round(scale * 100) + '%'"></span>
                    <button @click="scale = Math.min(5, scale + 0.5)" class="hover:text-primary transition cursor-pointer"><i class="fas fa-plus"></i></button>
                </div>

                <!-- Contenedor Imagen -->
                <div class="w-full h-full flex items-center justify-center overflow-hidden cursor-move"
                    @click.self="closeLightbox()"
                    @wheel.prevent="handleWheel">

                    <img :src="activeImage" :alt="activeAlt"
                        class="max-w-full max-h-[90vh] object-contain rounded transition-transform duration-75 ease-linear shadow-2xl"
                        :class="{ 'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning }"
                        @mousedown="startDrag"
                        @mousemove="drag"
                        :style="`transform: translate(${pointX}px, ${pointY}px) scale(${scale});`" />
                </div>
            </div>
        </div>
        @endif

        <!-- Catálogo virtual -->
        @if (!empty($businessData->custom_links))
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 overflow-hidden mb-8">
            <div class="p-5 sm:p-8">
                <!-- Encabezado -->
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 min-w-12 bg-gradient-to-br from-secondary to-primary dark:from-zinc-800 dark:to-zinc-700 text-white rounded-xl flex items-center justify-center mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                        <i class="fas fa-cart-arrow-down text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-ink dark:text-zinc-100">
                            <strong class="text-primary dark:text-zinc-300">Catálogo:</strong>
                            {{ $businessData->business->name }}
                        </h2>
                        <p class="text-sm text-danger mt-1.5 hidden sm:block">
                            ⚠️ Si el catálogo es de WhatsApp, es posible que requiera abrirse en celular. En ese caso, usa el botón de WhatsApp directo.
                        </p>
                    </div>
                </div>

                <!-- Instrucciones claras -->
                <div class="mb-4 p-3 bg-premium/10 border-l-4 border-premium dark:bg-zinc-800/80 dark:border-zinc-700 rounded-r-lg">
                    <p class="text-sm text-premium dark:text-zinc-200 font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        @if (count($businessData->custom_links) == 1)
                        Haz clic en el catálogo de abajo para acceder:
                        @else
                        Haz clic en cualquiera de los catálogos de abajo para acceder:
                        @endif
                    </p>
                </div>

                <!-- Enlaces a catálogos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($businessData->custom_links as $index => $link)
                    <a wire:key="custom-link-{{ $index }}" href="{{ $link['url'] }}" target="_blank" rel="noopener"
                        class="group relative flex items-center p-4 bg-gradient-to-r from-primary/10 to-secondary/10 hover:from-primary/15 hover:to-secondary/15 dark:from-zinc-800 dark:to-zinc-800/60 dark:hover:from-zinc-800 dark:hover:to-zinc-700 rounded-xl shadow-md shadow-ink/40 dark:shadow-none transition-all duration-300 border-2 border-primary/20 hover:border-primary/40 dark:border-zinc-700 dark:hover:border-zinc-600 cursor-pointer transform hover:-translate-y-1">

                        <!-- Indicador visual -->
                        <div class="absolute top-2 right-2 opacity-50 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-external-link-alt text-primary dark:text-zinc-400 text-xs"></i>
                        </div>

                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary to-secondary dark:from-zinc-700 dark:to-zinc-800 text-white flex items-center justify-center rounded-full mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                            <i class="fas fa-book-open text-lg"></i>
                        </div>

                        <div class="flex-grow">
                            <div class="flex items-center mb-1">
                                <p class="text-base font-bold text-ink dark:text-zinc-100 group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors">
                                    {{ $link['title'] }}
                                </p>
                                <i class="fas fa-chevron-right ml-2 text-primary dark:text-zinc-400 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-1"></i>
                            </div>

                            <p class="text-xs text-primary dark:text-zinc-400 font-medium mt-1 opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                Clic para abrir catálogo →
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Mensaje adicional de ayuda -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-ink/60 dark:text-zinc-400">
                        <i class="fas fa-mouse-pointer mr-1"></i>
                        @if (count($businessData->custom_links) == 1)
                        El catálogo se abrirá en una nueva pestaña
                        @else
                        Los catálogos se abrirán en una nueva pestaña
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Información de contacto y Ubicación -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Contacto -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 min-w-12 bg-primary dark:bg-zinc-800 text-white rounded-xl flex items-center justify-center mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                            <i class="fas fa-phone text-white text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-ink dark:text-zinc-100">Información de Contacto</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Teléfono -->
                        @if ($businessData->phone)
                        <div class="flex sm:flex-row flex-col sm:items-center items-start p-4 bg-primary/10 dark:bg-zinc-800/80 rounded-xl hover:bg-primary/15 dark:hover:bg-zinc-800 transition-colors">
                            <div class="w-10 h-10 min-w-10 bg-secondary dark:bg-zinc-700 text-white rounded-lg flex items-center justify-center sm:mr-4 mb-2 sm:mb-0">
                                <i class="fas fa-phone text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">Teléfono</p>
                                <a href="tel:{{ $businessData->phone }}"
                                    class="text-ink dark:text-zinc-100 font-semibold hover:text-primary dark:hover:text-zinc-200 transition-colors">
                                    {{ $businessData->phone }}
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- WhatsApp -->
                        @if ($businessData->whatsapp)
                        <div class="flex sm:flex-row flex-col sm:items-center items-start p-4 bg-primary/10 dark:bg-zinc-800/80 rounded-xl hover:bg-primary/15 dark:hover:bg-zinc-800 transition-colors">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $businessData->whatsapp) }}?text={{ urlencode('Hola, estoy interesado en sus productos y vengo referido por Fornuvi S.A.S.') }}"
                                target="_blank" rel="noopener"
                                class="flex sm:flex-row flex-col sm:items-center items-start w-full group no-underline">
                                <div class="w-10 h-10 min-w-10 bg-[#25D366] rounded-lg flex items-center justify-center sm:mr-4 mb-2 sm:mb-0 shadow-sm">
                                    <i class="fab fa-whatsapp text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">
                                        WhatsApp directo
                                    </p>
                                    <span class="text-ink dark:text-zinc-100 font-semibold group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors">
                                        {{ $businessData->whatsapp }}
                                    </span>
                                </div>
                            </a>
                        </div>
                        @endif

                        <!-- Email -->
                        @if ($businessData->business_email)
                        <div class="flex sm:flex-row flex-col sm:items-center items-start p-4 bg-primary/10 dark:bg-zinc-800/80 rounded-xl hover:bg-primary/15 dark:hover:bg-zinc-800 transition-colors">
                            <div class="w-10 h-10 min-w-10 bg-secondary dark:bg-zinc-700 text-white rounded-lg flex items-center justify-center sm:mr-4 mb-2 sm:mb-0">
                                <i class="fas fa-envelope text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">Email</p>
                                <a href="mailto:{{ $businessData->business_email }}"
                                    class="text-ink dark:text-zinc-100 font-semibold hover:text-primary dark:hover:text-zinc-200 transition-colors break-words">
                                    {{ $businessData->business_email }}
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Website -->
                        @if ($businessData->website_url)
                        <a href="{{ $businessData->website_url }}" target="_blank" rel="noopener noreferrer"
                            class="group flex sm:flex-row flex-col sm:items-center items-start p-4 bg-primary/10 dark:bg-zinc-800/80 rounded-xl hover:bg-primary/15 dark:hover:bg-zinc-800 transition-colors">
                            <div class="w-10 h-10 min-w-10 bg-secondary dark:bg-zinc-700 text-white rounded-lg flex items-center justify-center sm:mr-4 mb-2 sm:mb-0">
                                <i class="fas fa-globe text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">Sitio Web</p>
                                <span class="text-ink dark:text-zinc-100 font-semibold group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors break-words">
                                    Visitar sitio web
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </span>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 min-w-10 sm:w-12 sm:h-12 bg-premium dark:bg-zinc-800 text-white rounded-xl flex items-center justify-center mr-3 sm:mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                            <i class="fas fa-map-marker-alt text-white text-base sm:text-lg"></i>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-bold text-ink dark:text-zinc-100">Ubicación</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800/60 rounded-xl">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-premium dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-white text-sm"></i>
                                </div>

                                <div class="flex items-center">
                                    <div>
                                        @if ($businessData->city || $businessData->department || $businessData->country)
                                        <p class="text-ink dark:text-zinc-200 font-semibold">
                                            {{ $businessData->cityRelation ? $businessData->cityRelation->name : $businessData->city }}
                                            {{ $businessData->department ? ', ' . $businessData->department->name : '' }}
                                            {{ $businessData->country ? ' - ' . $businessData->country->name : '' }}
                                        </p>

                                        @if (!empty($businessData->address))
                                        <p class="text-sm text-ink/70 dark:text-zinc-400 mt-0.5">
                                            {{ $businessData->address }}
                                        </p>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($businessData->latitude != 0 && $businessData->longitude != 0)
                            <!-- Botones de navegación -->
                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium mb-3 flex items-center">
                                    <i class="fas fa-route mr-2 text-premium dark:text-zinc-300"></i>
                                    ¿Cómo llegar?
                                </p>

                                @php
                                $hasCoordinates =
                                !empty($businessData->latitude) &&
                                !empty($businessData->longitude) &&
                                $businessData->latitude != 0 &&
                                $businessData->longitude != 0;

                                $addressString = $businessData->address;
                                $addressString .= $businessData->city ? ', ' . $businessData->city : '';
                                $addressString .= $businessData->department ? ', ' . $businessData->department : '';
                                $addressString .= $businessData->country ? ', ' . $businessData->country : '';

                                if ($hasCoordinates) {
                                $dest = $businessData->latitude . ',' . $businessData->longitude;
                                $googleMapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $dest;
                                $wazeUrl = 'https://waze.com/ul?ll=' . $dest . '&navigate=yes';
                                } else {
                                $encodedAddr = urlencode($addressString);
                                $googleMapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $encodedAddr;
                                $wazeUrl = 'https://waze.com/ul?q=' . $encodedAddr . '&navigate=yes';
                                }
                                @endphp

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <!-- Google Maps -->
                                    <a href="{{ $googleMapsUrl }}" target="_blank" rel="noopener"
                                        class="flex items-center px-4 py-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-secondary dark:hover:border-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-900 shadow-md shadow-ink/40 dark:shadow-none transition group">
                                        <div class="flex items-center w-full">
                                            <div class="w-10 h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-xs rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/aa/Google_Maps_icon_%282020%29.svg"
                                                    alt="Google Maps" class="w-6 h-6">
                                            </div>
                                            <div class="text-left flex-1">
                                                <p class="font-bold text-ink dark:text-zinc-100 text-sm">Google Maps</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors">
                                                    Ir ahora
                                                </p>
                                            </div>
                                            <i class="fas fa-arrow-right text-zinc-300 dark:text-zinc-600 group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors"></i>
                                        </div>
                                    </a>

                                    <!-- Waze -->
                                    <a href="{{ $wazeUrl }}" target="_blank" rel="noopener"
                                        class="flex items-center px-4 py-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-primary dark:hover:border-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-900 shadow-md shadow-ink/40 dark:shadow-none transition group">
                                        <div class="flex items-center w-full">
                                            <div class="w-10 h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-xs rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                                <img src="https://play-lh.googleusercontent.com/r7XL36PVNtnidqy6ikRiW1AHEIsjhePrZ8W5M4cNTQy5ViF3-lIDY47hpvxc84kJ7lw"
                                                    alt="Waze" class="w-6 h-6">
                                            </div>
                                            <div class="text-left flex-1">
                                                <p class="font-bold text-ink dark:text-zinc-100 text-sm">Waze</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors">
                                                    Navegar
                                                </p>
                                            </div>
                                            <i class="fas fa-arrow-right text-zinc-300 dark:text-zinc-600 group-hover:text-primary dark:group-hover:text-zinc-200 transition-colors"></i>
                                        </div>
                                    </a>
                                </div>

                                <!-- Información método de navegación -->
                                <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center">
                                            <i class="fas fa-location-arrow mr-2 text-primary dark:text-zinc-300"></i>
                                            Se abrirá la ruta desde tu ubicación actual
                                        </p>

                                        <div class="flex items-center">
                                            @if ($hasCoordinates)
                                            <div class="flex items-center px-2 py-1 bg-zinc-200 dark:bg-zinc-800 text-ink dark:text-zinc-200 rounded text-[10px] font-bold uppercase tracking-wide">
                                                <i class="fas fa-satellite-dish mr-1 text-primary dark:text-zinc-300"></i> GPS Exacto
                                            </div>
                                            @else
                                            <div class="flex items-center px-2 py-1 bg-zinc-200 dark:bg-zinc-800 text-ink dark:text-zinc-200 rounded text-[10px] font-bold uppercase tracking-wide">
                                                <i class="fas fa-map-signs mr-1 text-primary dark:text-zinc-300"></i> Dirección
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($hasCoordinates)
                                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-2 font-mono">
                                        Lat: {{ number_format($businessData->latitude, 5) }} | Lon: {{ number_format($businessData->longitude, 5) }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar con redes sociales -->
            <div class="space-y-8">
                @php
                $socialNetworks = collect([
                'facebook_url' => [
                'icon' => 'fab fa-facebook-f',
                'name' => 'Facebook',
                'color' => 'bg-[#1877F2] hover:bg-[#166FE5]',
                ],
                'instagram_url' => [
                'icon' => 'fab fa-instagram',
                'name' => 'Instagram',
                'color' => 'bg-[#E1306C] hover:bg-[#C72E65]',
                ],
                'linkedin_url' => [
                'icon' => 'fab fa-linkedin-in',
                'name' => 'LinkedIn',
                'color' => 'bg-[#0077B5] hover:bg-[#00669C]',
                ],
                'youtube_url' => [
                'icon' => 'fab fa-youtube',
                'name' => 'YouTube',
                'color' => 'bg-[#FF0000] hover:bg-[#E60000]',
                ],
                'tiktok_url' => [
                'icon' => 'fab fa-tiktok',
                'name' => 'TikTok',
                'color' => 'bg-[#010101] hover:bg-[#121212]',
                ],
                'x_url' => [
                'icon' => 'fab fa-x-twitter',
                'name' => 'X (Twitter)',
                'color' => 'bg-black hover:bg-neutral-900',
                ],
                ])->filter(function ($data, $field) use ($businessData) {
                return !empty($businessData->$field);
                });
                @endphp

                @if ($socialNetworks->count() > 0)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-primary dark:from-zinc-800 dark:to-zinc-700 text-white rounded-xl flex items-center justify-center mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                            <i class="fas fa-share-alt text-white text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-ink dark:text-zinc-100">Redes Sociales</h2>
                    </div>

                    <div class="space-y-3">
                        @foreach ($socialNetworks as $field => $social)
                        <a href="{{ $businessData->$field }}" target="_blank" rel="noopener"
                            class="flex items-center p-3.5 rounded-xl {{ $social['color'] }} text-white transition-all duration-300 transform hover:scale-[1.02] shadow-md shadow-ink/40 dark:shadow-none">
                            <i class="{{ $social['icon'] }} text-xl mr-4 w-6 text-center"></i>
                            <span class="font-semibold">{{ $social['name'] }}</span>
                            <i class="fas fa-external-link-alt ml-auto text-sm opacity-75"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if ($businessData->promo_video_url || $businessData->additional_videos)
        <div class="mt-8 md:mt-16 space-y-4">
            <div class="flex items-center justify-center">
                <div class="w-12 h-12 min-w-12 bg-danger text-white rounded-xl flex items-center justify-center mr-4 shadow-md shadow-ink/40 dark:shadow-none">
                    <i class="fas fa-play text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-ink dark:text-zinc-100">Videos Promocionales</h2>
            </div>

            {{-- Video principal --}}
            @if ($businessData->promo_video_url)
            <div class="flex justify-center px-4 md:px-0">
                {!! $businessData->promo_video_url !!}
            </div>
            @endif

            {{-- Videos adicionales --}}
            @if ($businessData->additional_videos)
            @foreach ($businessData->additional_videos as $video)
            <div class="flex justify-center px-4 md:px-0">
                {!! $video !!}
            </div>
            @endforeach
            @endif
        </div>
        @endif

        <!-- Beneficios de compras -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md shadow-ink/70 dark:shadow-none border border-zinc-200/90 dark:border-zinc-800 mt-8 max-w-4xl mx-auto overflow-hidden">
            <div class="bg-gradient-to-r from-zinc-50 via-white to-zinc-50 dark:from-zinc-900 dark:via-zinc-950 dark:to-zinc-900 p-6 sm:p-10">
                <h2 class="text-xl font-bold text-ink dark:text-zinc-100 mb-4">Beneficios de tus compras como afiliado</h2>

                <p class="text-ink/80 dark:text-zinc-300 leading-relaxed">
                    Por cada compra que usted realice como afiliado de Fornuvi a la marca
                    <span class="font-semibold text-primary dark:text-zinc-100">{{ $businessData->business->name }}</span>, Fornuvi recibe una
                    comisión que varía entre <span class="font-semibold text-ink dark:text-zinc-100">{{ $businessData->business->minimum_percentage }}%</span>
                    (mínimo) y <span class="font-semibold text-ink dark:text-zinc-100">{{ $businessData->business->maximum_percentage }}%</span>
                    (máximo), dependiendo de las condiciones comerciales.
                </p>

                <p class="text-ink/80 dark:text-zinc-300 leading-relaxed mt-4">
                    Cada comisión de <span class="font-semibold text-ink dark:text-zinc-100">ingreso bruto de $38.000</span>, equivale a
                    <span class="font-semibold text-primary dark:text-zinc-100">1.80 puntos</span>. Ingreso bruto corresponde al valor total
                    <span class="italic">antes de aplicar cualquier descuento, retención o gasto legal obligatorio</span>.
                </p>
            </div>
        </div>

        <!-- Botón de regreso -->
        <div class="mt-8 flex justify-center">
            <a href="{{ route('companies.index') }}" wire:navigate
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-primary to-secondary dark:from-zinc-800 dark:to-zinc-700 dark:hover:from-zinc-700 dark:hover:to-zinc-600 text-white font-semibold rounded-xl shadow-md shadow-ink/60 dark:shadow-none hover:shadow-xl hover:shadow-ink/80 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                <i class="fas fa-arrow-left mr-3"></i>
                Volver a la lista
            </a>
        </div>
    </div>

    <!-- Back to top button -->
    <button id="backToTop"
        class="fixed cursor-pointer bottom-6 right-6 bg-primary hover:bg-secondary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-ink/70 dark:shadow-none dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 transition duration-300 z-50"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script async src="//www.instagram.com/embed.js"></script>

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