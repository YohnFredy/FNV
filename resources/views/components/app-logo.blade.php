@props([
    'sidebar' => false,
    'href' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    @if ($sidebar)
        <img class="w-full h-auto max-h-12 object-contain" src="{{ asset('storage/logo/logo_fornuvi.png') }}" alt="Fornuvi">
    @else
        {{-- Pantallas pequeñas (móvil): logotipo.jpg --}}
        <img class="block sm:hidden w-full h-auto object-contain rounded-lg shadow-xs px-1.5" src="{{ asset('storage/logo/logo_fornuvi_texto.png') }}" alt="Fornuvi">

        {{-- Pantallas normales (desktop): logo_fornuvi_texto.png --}}
        <img class="hidden sm:block w-full h-auto object-contain rounded px-1.5" src="{{ asset('storage/logo/logo_fornuvi_texto.png') }}" alt="Fornuvi">
    @endif
</div>