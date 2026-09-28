@props([
'icon' => null,
'iconTrailing' => null,
'href' => '#',
'current' => false,
'badge' => null,
'variant' => 'default',
])

@php
$isActive = (bool) $current;

$baseClasses = "group relative flex items-center gap-2.5 px-2 py-1 rounded-xl text-sm font-semibold transition-all duration-200 border cursor-pointer select-none in-data-flux-sidebar-collapsed-desktop:justify-center in-data-flux-sidebar-collapsed-desktop:px-2";

$activeClasses = match($variant) {
'premium' => "bg-gradient-to-r from-premium to-amber-700 text-white font-bold border-premium/50 shadow-md shadow-ink/70 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 dark:shadow-none",
default => "bg-zinc-50 text-primary font-bold border-primary/30 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 ",
};

$inactiveClasses = match($variant) {
'premium' => "text-premium bg-premium/10 border-premium/30 hover:bg-premium/15 hover:border-premium/40 hover:translate-x-1 dark:bg-zinc-800/80 dark:text-zinc-200 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-100",
default => "text-secondary border-transparent hover:text-primary hover:bg-primary/5 hover:border-primary/20 hover:translate-x-1 dark:text-zinc-300 dark:hover:text-zinc-100 dark:hover:bg-zinc-800/80 dark:hover:border-zinc-700",
};

$itemClasses = $isActive ? $activeClasses : $inactiveClasses;

$iconClasses = $isActive
? "size-4 text-primary dark:text-zinc-100 shrink-0"
: ($variant === 'premium'
? "size-4 text-primary dark:text-zinc-300 group-hover:scale-110 transition-transform shrink-0"
: "size-4 text-ink/70 group-hover:text-primary dark:text-zinc-400 dark:group-hover:text-zinc-100 group-hover:scale-110 transition-transform shrink-0");
@endphp

<a
    href="{{ $href }}"
    @if ($isActive) aria-current="page" data-current @endif
    {{ $attributes->merge(['class' => "$baseClasses $itemClasses"]) }}>
    @if ($icon)
    <flux:icon :icon="$icon" class="{{ $iconClasses }}" />
    @endif

    <span class="truncate in-data-flux-sidebar-collapsed-desktop:hidden flex-1">
        {{ $slot }}
    </span>

    @if ($badge !== null && $badge !== '')
    <span class="in-data-flux-sidebar-collapsed-desktop:hidden ml-auto inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[12px] font-black rounded-full {{ $isActive ? 'bg-white/25 text-white' : 'bg-danger text-white shadow-xs' }}">
        {{ $badge }}
    </span>
    @endif

    @if ($iconTrailing)
    <flux:icon :icon="$iconTrailing" class="size-3.5 in-data-flux-sidebar-collapsed-desktop:hidden opacity-60 group-hover:opacity-100 transition-opacity ml-auto" />
    @endif
</a>