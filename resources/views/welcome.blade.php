<x-layouts::app.header :title="__('Inicio - Ecosistema de Economía Colaborativa')">
    <div x-data="{
        backToTop: false,
        activeFaq: null,
        init() {
            window.addEventListener('scroll', () => {
                this.backToTop = window.pageYOffset > 350;
            });
        }
    }" class="relative bg-zinc-50 dark:bg-zinc-950 text-ink dark:text-zinc-100 selection:bg-primary selection:text-white transition-colors duration-300">

        {{-- ========================================================================= --}}
        {{-- 1. HERO SECTION                                                          --}}
        {{-- ========================================================================= --}}
        <section class="relative w-full min-h-[90vh] lg:min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Background Image & Gradient Overlays -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=85"
                     alt="Personas felices comprando"
                     class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out" />

                <!-- Light mode gradient (Ink / Primary tones) -->
                <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/85 to-ink/40 md:bg-gradient-to-r md:from-ink md:via-ink/80 md:to-ink/30 dark:hidden"></div>

                <!-- Dark mode gradient (Strict Zinc scale) -->
                <div class="absolute inset-0 hidden dark:block bg-gradient-to-t from-zinc-950 via-zinc-950/90 to-zinc-900/60 md:bg-gradient-to-r md:from-zinc-950 md:via-zinc-950/85 md:to-zinc-950/40"></div>
            </div>

            <!-- Glowing ambient lights -->
            <div class="absolute top-1/4 left-10 w-96 h-96 bg-primary/20 dark:bg-zinc-800/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-secondary/15 dark:bg-zinc-700/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Hero Content -->
            <div class="relative z-10 w-full px-4 sm:px-8 lg:px-16 py-20 text-center md:text-left text-white max-w-7xl mx-auto">
                <div class="max-w-3xl">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 mb-6 px-4 py-1.5 rounded-full bg-primary/90 dark:bg-zinc-800/90 backdrop-blur-md text-xs sm:text-sm font-bold tracking-wide shadow-xl ring-1 ring-white/15 dark:ring-zinc-700">
                        <span class="inline-flex items-center justify-center size-5 rounded-full bg-danger text-white text-xs">
                            <flux:icon.bolt class="size-3.5" />
                        </span>
                        <span class="text-white tracking-wider uppercase">Economía Colaborativa</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="font-extrabold tracking-tight leading-[1.12] mb-6 text-3xl sm:text-5xl lg:text-6xl text-white">
                        Tus gastos cotidianos,
                        <span class="block mt-2 bg-gradient-to-r from-white via-zinc-100 to-secondary dark:from-zinc-100 dark:via-zinc-200 dark:to-zinc-400 bg-clip-text text-transparent">
                            transformados en ingresos.
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="mb-10 text-base sm:text-lg lg:text-xl text-zinc-200 dark:text-zinc-300 leading-relaxed max-w-2xl mx-auto md:mx-0">
                        Únete a la comunidad inteligente donde comprar lo que ya necesitas fortalece tu economía.
                        <span class="block mt-2 font-semibold text-white">Conecta, consume y gana.</span>
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                        @auth
                            <flux:button :href="route('office.dashboard')" variant="primary" icon="briefcase" class="!bg-primary hover:!bg-secondary text-white shadow-lg shadow-primary/30 border-none font-semibold px-6 py-3" wire:navigate>
                                {{ __('Ingresar a mi Oficina Virtual') }}
                            </flux:button>
                        @else
                            <flux:button :href="route('register')" variant="primary" icon="sparkles" class="!bg-primary hover:!bg-secondary text-white shadow-lg shadow-primary/30 border-none font-semibold px-6 py-3" wire:navigate>
                                {{ __('Comenzar Ahora') }}
                            </flux:button>
                            <flux:button :href="route('login')" variant="outline" icon="arrow-right-end-on-rectangle" class="border-white/30 text-white hover:bg-white/10 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800/60 backdrop-blur-sm px-6 py-3" wire:navigate>
                                {{ __('Iniciar Sesión') }}
                            </flux:button>
                        @endauth

                        <a href="#about" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:text-white transition-colors">
                            <span>Conocer más</span>
                            <flux:icon.chevron-down class="size-4 animate-bounce" />
                        </a>
                    </div>
                </div>
            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 2. QUÉ ES FORNUVI (Filosofía & Ecosistema)                                --}}
        {{-- ========================================================================= --}}
        <section id="about" class="py-16 md:py-24 bg-white dark:bg-zinc-900 relative overflow-hidden border-b border-zinc-200 dark:border-zinc-800">
            <!-- Background subtle decoration -->
            <div class="absolute top-0 right-0 w-1/3 h-full bg-zinc-50 dark:bg-zinc-950/50 -skew-x-12 hidden lg:block pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">

                    <!-- Visual Side with Image -->
                    <div class="w-full lg:w-1/2 relative group">
                        <!-- Ambient Glows -->
                        <div class="absolute -top-6 -left-6 w-40 h-40 bg-primary/10 dark:bg-zinc-800 rounded-full blur-2xl opacity-70"></div>
                        <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-premium/15 dark:bg-zinc-700 rounded-full blur-2xl opacity-70"></div>

                        <div class="relative z-10 rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-xl shadow-zinc-900/10 dark:shadow-none">
                            <img src="https://d1ih8jugeo2m5m.cloudfront.net/2024/09/oportunidades_de_negocio.jpg"
                                 alt="Comunidad colaborativa y oportunidades de negocio"
                                 class="w-full h-auto object-cover transform transition duration-700 group-hover:scale-105" />
                        </div>

                        <!-- Floating Stat Overlay Badge -->
                        <div class="absolute -bottom-5 -left-4 sm:left-6 z-20 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-lg flex items-center gap-3.5 backdrop-blur-md">
                            <div class="size-11 rounded-xl bg-primary/10 dark:bg-zinc-800 text-primary dark:text-zinc-200 flex items-center justify-center font-bold">
                                <flux:icon.trophy class="size-6" />
                            </div>
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold">Modelo Probado</p>
                                <p class="text-sm font-bold text-ink dark:text-zinc-100">Crecimiento Sostenible</p>
                            </div>
                        </div>
                    </div>

                    <!-- Text Content Side -->
                    <div class="w-full lg:w-1/2 text-center lg:text-left">
                        <span class="inline-block text-primary dark:text-zinc-300 font-bold uppercase tracking-widest text-xs sm:text-sm mb-3">
                            ¿Qué es Fornuvi?
                        </span>

                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-ink dark:text-zinc-50 mb-6 leading-tight">
                            Más que una plataforma, un
                            <span class="text-primary dark:text-zinc-200 relative whitespace-nowrap">
                                Ecosistema
                                <svg class="absolute w-full h-3 -bottom-1 left-0 text-premium dark:text-zinc-600" viewBox="0 0 100 10" preserveAspectRatio="none">
                                    <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="3" fill="none" />
                                </svg>
                            </span>
                        </h2>

                        <p class="text-zinc-600 dark:text-zinc-300 mb-8 text-base sm:text-lg leading-relaxed">
                            Conectamos a personas que consumen productos y servicios con comercios que buscan clientes leales.
                            Nuestra filosofía es el <strong class="text-ink dark:text-zinc-100 bg-primary/10 dark:bg-zinc-800 px-2 py-0.5 rounded">Ganar-Ganar</strong>.
                        </p>

                        <!-- Feature Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                            <!-- Feature 1 -->
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-center sm:text-left transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                                <div class="shrink-0 size-12 rounded-xl bg-primary/10 dark:bg-zinc-800 text-primary dark:text-zinc-200 flex items-center justify-center">
                                    <flux:icon.user-group class="size-6" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-ink dark:text-zinc-100 mb-1">Crecimiento Comunitario</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                                        La comunidad crece distribuyendo la riqueza generada de forma equitativa.
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-center sm:text-left transition-all hover:border-zinc-300 dark:hover:border-zinc-700">
                                <div class="shrink-0 size-12 rounded-xl bg-premium/10 dark:bg-zinc-800 text-premium dark:text-zinc-200 flex items-center justify-center">
                                    <flux:icon.arrow-trending-up class="size-6" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-ink dark:text-zinc-100 mb-1">Ventas y Fidelización</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                                        El comercio gana ventas sostenibles y clientes fieles sin costos fijos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 3. EL CICLO SOSTENIBLE (Proceso en 4 Pasos)                              --}}
        {{-- ========================================================================= --}}
        <section id="como-funciona" class="py-16 md:py-24 bg-zinc-50 dark:bg-zinc-950 relative border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Heading -->
                <div class="text-center max-w-3xl mx-auto mb-14">
                    <span class="text-primary dark:text-zinc-400 font-bold tracking-widest uppercase text-xs">
                        Proceso Dinámico
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-ink dark:text-zinc-50 mt-2 mb-4 leading-tight">
                        Un ciclo continuo y sostenible
                    </h2>
                    <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                        Así es como generamos valor para todos en
                        <span class="font-semibold text-primary dark:text-zinc-200">4 simples pasos</span>.
                    </p>
                </div>

                <!-- 4 Steps Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 relative z-10">

                    <!-- Paso 1 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                        <div class="size-14 sm:size-16 bg-primary dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-md shadow-primary/20 dark:shadow-none group-hover:scale-110 transition-transform">
                            <flux:icon.shopping-cart class="size-7" />
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold mb-2 text-ink dark:text-zinc-100">
                            1. Consumo Inteligente
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                            El afiliado compra lo que ya necesitaba priorizando a los
                            <span class="font-semibold text-primary dark:text-zinc-200">Comercios Aliados</span>.
                        </p>
                    </div>

                    <!-- Paso 2 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                        <div class="size-14 sm:size-16 bg-secondary dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-md shadow-secondary/20 dark:shadow-none group-hover:scale-110 transition-transform">
                            <flux:icon.heart class="size-7" />
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold mb-2 text-ink dark:text-zinc-100">
                            2. Venta y Fidelización
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                            El comercio recibe nuevos clientes gracias a la recomendación continua de la comunidad.
                        </p>
                    </div>

                    <!-- Paso 3 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                        <div class="size-14 sm:size-16 bg-premium dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-md shadow-premium/20 dark:shadow-none group-hover:scale-110 transition-transform">
                            <flux:icon.banknotes class="size-7" />
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold mb-2 text-ink dark:text-zinc-100">
                            3. Comisión por Éxito
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                            El comercio paga comisión solo por ventas reales.
                            <span class="font-semibold text-ink dark:text-zinc-200">Sin gastos fijos</span>.
                        </p>
                    </div>

                    <!-- Paso 4 -->
                    <div class="bg-white dark:bg-zinc-900 p-6 sm:p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                        <div class="size-14 sm:size-16 bg-danger dark:bg-zinc-800 text-white dark:text-zinc-100 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-md shadow-danger/20 dark:shadow-none group-hover:scale-110 transition-transform">
                            <flux:icon.wallet class="size-7" />
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold mb-2 text-ink dark:text-zinc-100">
                            4. Distribución
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed">
                            Fornuvi reparte las comisiones en la comunidad, generando ingresos directos para ti.
                        </p>
                    </div>
                </div>

                <!-- Cycle Loop Visual Banner -->
                <div class="mt-10 relative w-full h-56 sm:h-64 md:h-72 rounded-3xl overflow-hidden shadow-lg border border-zinc-200 dark:border-zinc-800 group">
                    <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1700&q=80"
                         alt="Ciclo del dinero y flujo económico"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />

                    <!-- Dark mode / Light mode Overlays -->
                    <div class="absolute inset-0 bg-ink/75 dark:bg-zinc-950/85 backdrop-blur-xs flex items-center justify-center p-6">
                        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-white text-sm sm:text-base md:text-xl font-bold text-center leading-relaxed">
                            <span class="flex items-center gap-2">
                                <flux:icon.user class="size-5 text-secondary dark:text-zinc-400" /> Afiliados consumen
                            </span>
                            <flux:icon.arrow-right class="size-4 sm:size-5 text-primary dark:text-zinc-500" />
                            <span class="flex items-center gap-2">
                                <flux:icon.building-storefront class="size-5 text-secondary dark:text-zinc-400" /> Comercios venden
                            </span>
                            <flux:icon.arrow-right class="size-4 sm:size-5 text-primary dark:text-zinc-500" />
                            <span class="flex items-center gap-2">
                                <flux:icon.cpu-chip class="size-5 text-secondary dark:text-zinc-400" /> Fornuvi distribuye
                            </span>
                            <flux:icon.arrow-right class="size-4 sm:size-5 text-primary dark:text-zinc-500" />
                            <span class="text-secondary dark:text-zinc-200 font-extrabold flex items-center gap-2">
                                <flux:icon.arrow-path class="size-5 animate-spin" /> ¡El ciclo se repite!
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 4. BENEFICIOS (Split Section: Afiliados vs Comercios)                    --}}
        {{-- ========================================================================= --}}
        <section id="beneficios" class="py-16 md:py-24 bg-white dark:bg-zinc-900 relative border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

                    <!-- Tarjeta: Para Afiliados -->
                    <div class="flex flex-col bg-zinc-50 dark:bg-zinc-950 rounded-3xl p-8 sm:p-12 border border-zinc-200 dark:border-zinc-800 relative overflow-hidden shadow-sm hover:border-zinc-300 dark:hover:border-zinc-700 transition-all">
                        <!-- Decorative Letter Watermark -->
                        <div class="absolute -top-6 -right-6 text-9xl font-black text-primary/5 dark:text-zinc-800/40 select-none pointer-events-none">
                            A
                        </div>

                        <div class="relative z-10">
                            <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-400">Beneficios Exclusivos</span>
                            <h3 class="text-3xl font-extrabold text-ink dark:text-zinc-50 mt-1 mb-2">Para Afiliados</h3>
                            <p class="text-primary dark:text-zinc-300 font-semibold mb-8">Fortaleciendo Nuestra Vida</p>

                            <ul class="space-y-6">
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-primary/10 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-primary dark:text-zinc-200">
                                        <flux:icon.check class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-ink dark:text-zinc-100 text-lg">Crecimiento Económico</strong>
                                        <span class="text-zinc-600 dark:text-zinc-400 text-sm">Genera ingresos sin cambiar tus hábitos de consumo habituales.</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-primary/10 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-primary dark:text-zinc-200">
                                        <flux:icon.check class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-ink dark:text-zinc-100 text-lg">Desarrollo Personal</strong>
                                        <span class="text-zinc-600 dark:text-zinc-400 text-sm">Formación integral, mentoría y acompañamiento continuo en tu crecimiento.</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-primary/10 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-primary dark:text-zinc-200">
                                        <flux:icon.check class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-ink dark:text-zinc-100 text-lg">Impacto Social</strong>
                                        <span class="text-zinc-600 dark:text-zinc-400 text-sm">Conecta con personas y emprendedores afines creando comunidad de valor.</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tarjeta: Para Comercios / Tu Negocio -->
                    <div id="aliados" class="flex flex-col bg-ink dark:bg-zinc-950 rounded-3xl p-8 sm:p-12 border border-zinc-700 dark:border-zinc-800 relative overflow-hidden text-white shadow-xl">
                        <!-- Decorative Letter Watermark -->
                        <div class="absolute -top-6 -right-6 text-9xl font-black text-white/5 dark:text-zinc-800/40 select-none pointer-events-none">
                            B
                        </div>

                        <!-- Gradient background light in light mode -->
                        <div class="absolute inset-0 bg-gradient-to-br from-ink to-primary/40 dark:from-zinc-950 dark:to-zinc-900 pointer-events-none"></div>

                        <div class="relative z-10">
                            <span class="text-xs font-bold uppercase tracking-wider text-secondary dark:text-zinc-400">Expansión Empresarial</span>
                            <h3 class="text-3xl font-extrabold mt-1 mb-2 text-white">Para Tu Negocio</h3>
                            <p class="text-zinc-300 dark:text-zinc-400 font-semibold mb-8">Impulsa tus ventas sin riesgos</p>

                            <ul class="space-y-6">
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-white/15 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-white dark:text-zinc-200">
                                        <flux:icon.star class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-white text-lg">Clientes Fidelizados</strong>
                                        <span class="text-zinc-300 dark:text-zinc-400 text-sm">Una comunidad activa lista para consumir con preferencia lo que ofreces.</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-white/15 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-white dark:text-zinc-200">
                                        <flux:icon.star class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-white text-lg">Cero Riesgo Publicitario</strong>
                                        <span class="text-zinc-300 dark:text-zinc-400 text-sm">Pagas comisión única y exclusivamente cuando se concreta una venta.</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 size-7 rounded-full bg-white/15 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-white dark:text-zinc-200">
                                        <flux:icon.star class="size-4" />
                                    </div>
                                    <div>
                                        <strong class="block text-white text-lg">Visibilidad Gratuita</strong>
                                        <span class="text-zinc-300 dark:text-zinc-400 text-sm">Tu marca expuesta permanentemente frente a miles de afiliados locales.</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 5. FORMAS DE GENERAR INGRESOS                                            --}}
        {{-- ========================================================================= --}}
        <section id="ingresos" class="py-16 md:py-24 bg-zinc-50 dark:bg-zinc-950 relative border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center max-w-3xl mx-auto mb-14">
                    <span class="text-primary dark:text-zinc-400 font-bold tracking-widest uppercase text-xs">
                        Oportunidad de Compensación
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-ink dark:text-zinc-50 mt-2 mb-4 leading-tight">
                        Formas de Generar Ingresos
                    </h2>
                    <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400">
                        Nuestro sistema de compensación es flexible, transparente y altamente poderoso.
                    </p>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">

                    <!-- Ingreso 1: Por Red -->
                    <div class="bg-white dark:bg-zinc-900 p-8 sm:p-10 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col items-center text-center group">
                        <div class="size-20 bg-primary/10 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-primary dark:text-zinc-200 mb-6 group-hover:bg-primary group-hover:text-white dark:group-hover:bg-zinc-700 transition-colors duration-300">
                            <flux:icon.share class="size-10" />
                        </div>
                        <h3 class="text-2xl font-bold text-ink dark:text-zinc-100 mb-3">
                            Ingresos por Red
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-base">
                            Gana porcentajes consistentes por el consumo inteligente mensual de todo tu equipo de afiliados en la red.
                        </p>
                    </div>

                    <!-- Ingreso 2: Expansión Comercial (Destacado POPULAR) -->
                    <div class="bg-white dark:bg-zinc-900 p-8 sm:p-10 rounded-3xl border-2 border-premium/50 dark:border-zinc-700 shadow-md relative overflow-hidden flex flex-col items-center text-center group">
                        <div class="absolute top-0 right-0 bg-premium dark:bg-zinc-700 text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl tracking-wider">
                            POPULAR
                        </div>

                        <div class="size-20 bg-premium/10 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-premium dark:text-zinc-200 mb-6 group-hover:bg-premium group-hover:text-white dark:group-hover:bg-zinc-700 transition-colors duration-300">
                            <flux:icon.building-storefront class="size-10" />
                        </div>

                        <h3 class="text-2xl font-bold text-ink dark:text-zinc-100 mb-3">
                            Bono Expansión Comercial
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-6 leading-relaxed text-base">
                            Invita comercios aliados a Fornuvi y genera comisiones continuas por su crecimiento y volumen.
                        </p>

                        <div class="bg-premium/10 dark:bg-zinc-800/80 text-premium dark:text-zinc-200 px-6 py-3.5 rounded-2xl text-sm font-semibold w-full border border-premium/20 dark:border-zinc-700">
                            Ganas un porcentaje de sus ventas. <br />
                            <span class="uppercase font-extrabold tracking-wide">¡De por vida!</span>
                        </div>
                    </div>

                </div>

                <!-- Nota Aclaratoria -->
                <div class="mt-12 text-center">
                    <div class="inline-flex items-center gap-3 bg-white dark:bg-zinc-900 px-6 py-3 rounded-full shadow-sm border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:icon.information-circle class="size-5 text-primary dark:text-zinc-300 shrink-0" />
                        <span>No vendemos productos. Administramos tecnología y repartimos comisiones.</span>
                    </div>
                </div>

            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 6. MISIÓN Y VISIÓN                                                       --}}
        {{-- ========================================================================= --}}
        <section class="py-16 md:py-24 bg-white dark:bg-zinc-900 relative border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden py-12 sm:py-16 px-6 sm:px-12 bg-gradient-to-r from-ink via-primary to-ink dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 rounded-3xl text-white shadow-2xl border border-zinc-700 dark:border-zinc-800">

                    <!-- Background glows -->
                    <div class="absolute -top-10 -left-10 size-64 bg-primary/20 dark:bg-zinc-800/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -right-10 size-64 bg-secondary/20 dark:bg-zinc-700/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 sm:gap-14 relative z-10">
                        <!-- Misión -->
                        <div class="relative pl-6 sm:pl-8 border-l-4 border-secondary dark:border-zinc-600">
                            <h3 class="flex items-center text-xl sm:text-2xl font-extrabold mb-3 text-white">
                                <flux:icon.rocket-launch class="size-6 mr-3 text-secondary dark:text-zinc-300" />
                                Nuestra Misión
                            </h3>
                            <p class="text-zinc-200 dark:text-zinc-400 text-sm sm:text-base leading-relaxed">
                                Crear un ecosistema donde afiliados y comercios locales crezcan juntos,
                                impulsando la economía real mediante la cooperación.
                            </p>
                        </div>

                        <!-- Visión -->
                        <div class="relative pl-6 sm:pl-8 border-l-4 border-secondary dark:border-zinc-600">
                            <h3 class="flex items-center text-xl sm:text-2xl font-extrabold mb-3 text-white">
                                <flux:icon.eye class="size-6 mr-3 text-secondary dark:text-zinc-300" />
                                Nuestra Visión
                            </h3>
                            <p class="text-zinc-200 dark:text-zinc-400 text-sm sm:text-base leading-relaxed">
                                Ser la red de fidelización y marketing por recomendación más sólida
                                de Latinoamérica, transformando la vida de miles de familias y negocios.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 7. FAQ (Preguntas Frecuentes Interactivas)                               --}}
        {{-- ========================================================================= --}}
        <section id="faq" class="py-16 md:py-24 bg-zinc-50 dark:bg-zinc-950 relative border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12">
                    <span class="text-primary dark:text-zinc-400 font-bold tracking-widest uppercase text-xs">
                        Resolvemos tus Dudas
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-zinc-50 mt-2">
                        Preguntas Frecuentes
                    </h2>
                </div>

                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs transition-colors">
                        <button type="button"
                                x-on:click="activeFaq = (activeFaq === 1 ? null : 1)"
                                class="w-full p-6 text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-ink dark:text-zinc-100 hover:text-primary dark:hover:text-white transition-colors">
                            <span class="flex items-center gap-3">
                                <flux:icon.question-mark-circle class="size-6 text-primary dark:text-zinc-400 shrink-0" />
                                ¿Cuesta dinero unirse?
                            </span>
                            <span class="transition-transform duration-200 inline-flex" :class="{ 'rotate-180': activeFaq === 1 }">
                                <flux:icon.chevron-down class="size-5" />
                            </span>
                        </button>
                        <div x-show="activeFaq === 1" x-collapse x-cloak>
                            <p class="px-6 pb-6 text-zinc-600 dark:text-zinc-400 leading-relaxed pl-14">
                                No. Unirte a Fornuvi es completamente gratuito. No existen costos de inscripción ni mensualidades ocultas.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs transition-colors">
                        <button type="button"
                                x-on:click="activeFaq = (activeFaq === 2 ? null : 2)"
                                class="w-full p-6 text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-ink dark:text-zinc-100 hover:text-primary dark:hover:text-white transition-colors">
                            <span class="flex items-center gap-3">
                                <flux:icon.question-mark-circle class="size-6 text-primary dark:text-zinc-400 shrink-0" />
                                ¿Fornuvi es una tienda?
                            </span>
                            <span class="transition-transform duration-200 inline-flex" :class="{ 'rotate-180': activeFaq === 2 }">
                                <flux:icon.chevron-down class="size-5" />
                            </span>
                        </button>
                        <div x-show="activeFaq === 2" x-collapse x-cloak>
                            <p class="px-6 pb-6 text-zinc-600 dark:text-zinc-400 leading-relaxed pl-14">
                                No. Fornuvi es una plataforma tecnológica que conecta personas con comercios aliados para generar beneficios mutuos mediante la recomendación y la fidelización.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs transition-colors">
                        <button type="button"
                                x-on:click="activeFaq = (activeFaq === 3 ? null : 3)"
                                class="w-full p-6 text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-ink dark:text-zinc-100 hover:text-primary dark:hover:text-white transition-colors">
                            <span class="flex items-center gap-3">
                                <flux:icon.question-mark-circle class="size-6 text-primary dark:text-zinc-400 shrink-0" />
                                ¿Cómo puedo invitar comercios aliados?
                            </span>
                            <span class="transition-transform duration-200 inline-flex" :class="{ 'rotate-180': activeFaq === 3 }">
                                <flux:icon.chevron-down class="size-5" />
                            </span>
                        </button>
                        <div x-show="activeFaq === 3" x-collapse x-cloak>
                            <p class="px-6 pb-6 text-zinc-600 dark:text-zinc-400 leading-relaxed pl-14">
                                Como afiliado registrado, tienes acceso a un enlace y código de recomendación para vincular negocios locales. Cada vez que ellos facturen clientes de la red, recibirás comisiones de por vida.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs transition-colors">
                        <button type="button"
                                x-on:click="activeFaq = (activeFaq === 4 ? null : 4)"
                                class="w-full p-6 text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-ink dark:text-zinc-100 hover:text-primary dark:hover:text-white transition-colors">
                            <span class="flex items-center gap-3">
                                <flux:icon.question-mark-circle class="size-6 text-primary dark:text-zinc-400 shrink-0" />
                                ¿Cómo consulto mis comisiones acumuladas?
                            </span>
                            <span class="transition-transform duration-200 inline-flex" :class="{ 'rotate-180': activeFaq === 4 }">
                                <flux:icon.chevron-down class="size-5" />
                            </span>
                        </button>
                        <div x-show="activeFaq === 4" x-collapse x-cloak>
                            <p class="px-6 pb-6 text-zinc-600 dark:text-zinc-400 leading-relaxed pl-14">
                                Puedes ingresar a tu Oficina Virtual en cualquier momento para ver tu árbol de afiliados, historial de compras, balances y liquidaciones en tiempo real.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        {{-- ========================================================================= --}}
        {{-- 8. CTA FINAL (Llamado a la acción)                                      --}}
        {{-- ========================================================================= --}}
        <section class="py-16 sm:py-20 bg-gradient-to-r from-primary via-secondary to-primary dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 text-center text-white relative overflow-hidden">
            <!-- Background pattern overlay -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 leading-tight">
                    ¿Listo para transformar tu economía?
                </h2>
                <p class="text-lg md:text-xl text-zinc-100 dark:text-zinc-300 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Únete hoy a Fornuvi y comienza a generar ingresos con lo que ya consumes, sin inversiones ni riesgos.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4">
                    @auth
                        <flux:button :href="route('office.dashboard')" variant="primary" icon="briefcase" class="!bg-white !text-ink hover:!bg-zinc-100 dark:!bg-zinc-800 dark:!text-white dark:hover:!bg-zinc-700 shadow-xl font-bold px-8 py-3.5 border-none" wire:navigate>
                            {{ __('Ir a mi Oficina') }}
                        </flux:button>
                    @else
                        <flux:button :href="route('register')" variant="primary" icon="sparkles" class="!bg-white !text-ink hover:!bg-zinc-100 dark:!bg-zinc-800 dark:!text-white dark:hover:!bg-zinc-700 shadow-xl font-bold px-8 py-3.5 border-none" wire:navigate>
                            {{ __('Registrarme Gratis') }}
                        </flux:button>
                        <flux:button :href="route('login')" variant="outline" icon="arrow-right-end-on-rectangle" class="border-white text-white hover:bg-white/10 dark:border-zinc-700 dark:hover:bg-zinc-800 font-semibold px-8 py-3.5" wire:navigate>
                            {{ __('Iniciar Sesión') }}
                        </flux:button>
                    @endauth
                </div>
            </div>
        </section>


        {{-- Floating Back to Top Button --}}
        <button x-show="backToTop"
                x-cloak
                x-on:click="window.scrollTo({top: 0, behavior: 'smooth'})"
                type="button"
                title="Volver arriba"
                class="fixed z-50 bottom-6 right-6 size-12 rounded-full bg-secondary hover:bg-primary dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110">
            <flux:icon.arrow-up class="size-5" />
        </button>

    </div>
</x-layouts::app.header>