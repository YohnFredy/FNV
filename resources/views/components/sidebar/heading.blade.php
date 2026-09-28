@props([
'icon' => null,
'title' => null,
'variant' => 'primary',
'first' => false,
])

@php
$badgeClasses = match($variant) {
'secondary' => 'bg-secondary/10 text-secondary dark:bg-zinc-800 dark:text-zinc-300',
'premium' => 'bg-premium/15 text-premium dark:bg-zinc-800 dark:text-zinc-300',
'ink' => 'bg-ink/10 text-ink dark:bg-zinc-800 dark:text-zinc-300',
'danger' => 'bg-danger/10 text-danger dark:bg-zinc-800 dark:text-zinc-300',
'zinc' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
default => 'bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-300',
};

$textClasses = match($variant) {
'premium' => 'text-premium dark:text-zinc-400',
'secondary' => 'text-ink/70 dark:text-zinc-400',
'ink' => 'text-ink/70 dark:text-zinc-400',
'danger' => 'text-danger dark:text-zinc-400',
default => 'text-ink/70 dark:text-zinc-400',
};

$spacingClasses = $first ? 'pb-1' : 'pt-3 pb-1';
@endphp

<div {{ $attributes->merge(['class' => "px-2.5 $spacingClasses text-[11px] font-black uppercase tracking-widest in-data-flux-sidebar-collapsed-desktop:hidden flex items-center gap-1.5 $textClasses"]) }}>
    @if ($icon)
    <span class="p-1 rounded-md {{ $badgeClasses }} flex items-center justify-center shrink-0">
        <flux:icon :icon="$icon" class="size-3" />
    </span>
    @endif
    <span>{{ $title ?? $slot }}</span>
</div>