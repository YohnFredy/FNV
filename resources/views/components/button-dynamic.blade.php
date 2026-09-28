@props(['color' => 'primary'])

@php
    $baseClasses = 'inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 disabled:opacity-50 cursor-pointer shadow-sm';

    $colorClasses = match ($color) {
        'primary' => 'bg-primary hover:bg-secondary text-white focus:ring-2 focus:ring-primary/40',
        'danger' => 'bg-danger hover:bg-danger/80 text-white focus:ring-2 focus:ring-danger/40',
        'secondary' => 'bg-secondary hover:bg-secondary/80 text-white',
        'white' => 'bg-white hover:bg-zinc-100 text-zinc-900 border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-700',
        default => 'bg-primary hover:bg-secondary text-white',
    };
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => "$baseClasses $colorClasses"]) }}>
    {{ $slot }}
</button>
