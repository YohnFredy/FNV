<x-layouts::app.header :title="__('Términos y Condiciones Generales - Fornuvi')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            {{-- Barra Superior de Acciones --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                <a 
                    href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" 
                    class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-white transition-colors"
                >
                    <flux:icon.arrow-left class="size-4" />
                    <span>{{ __('Volver') }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <a 
                        href="{{ route('legal.contract') }}" 
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-200 underline transition-colors"
                    >
                        <flux:icon.briefcase class="size-4" />
                        <span>{{ __('Ver Contrato de Afiliación') }}</span>
                    </a>
                </div>
            </div>

            {{-- Contenedor del Documento Legal --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-10 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @include('pages.legal.partials.terms-content')
            </div>

            {{-- Pie del Documento --}}
            <div class="mt-8 text-center text-xs text-zinc-500 dark:text-zinc-400">
                <p>© {{ date('Y') }} FORNUVI S.A.S. — NIT 901.953.881-1 — Cali, Colombia.</p>
                <div class="mt-2 flex items-center justify-center gap-4">
                    <a href="{{ route('legal.terms') }}" class="font-semibold text-primary dark:text-zinc-300 hover:underline">{{ __('Términos y Condiciones') }}</a>
                    <span>•</span>
                    <a href="{{ route('legal.contract') }}" class="font-semibold text-primary dark:text-zinc-300 hover:underline">{{ __('Contrato de Afiliación') }}</a>
                    <span>•</span>
                    <a href="{{ route('home') }}" class="hover:underline">{{ __('Página de Inicio') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.header>
