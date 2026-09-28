@props([
    'for' => '',
    'label' => '',
])

<div class="relative flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $for }}" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $label }}</label>
    @endif

    <div class="relative">
        <select id="{{ $for }}" name="{{ $for }}"
            {{ $attributes->merge([
                'class' => 'w-full appearance-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500 cursor-pointer pr-10',
            ]) }}>
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-zinc-500 dark:text-zinc-400">
            <flux:icon.chevron-down class="size-4" />
        </div>
    </div>

    @error($for)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
