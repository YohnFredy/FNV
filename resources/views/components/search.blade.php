@props(['placeholder' => 'Buscar...', 'disabled' => false])

<div class="relative flex w-full md:w-64">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' =>
            'block w-full ps-10 pr-12 bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary p-2.5 dark:bg-zinc-900 dark:border-zinc-700 dark:text-zinc-100 dark:focus:border-zinc-500 transition-colors',
    ]) !!} type="text" placeholder="{{ $placeholder }}">

    <button type="button" wire:click="searchEnter"
        class="absolute right-1 top-1 bottom-1 px-3 bg-primary text-white rounded-lg hover:bg-secondary transition flex items-center justify-center cursor-pointer shadow-sm">
        <flux:icon.magnifying-glass class="size-4" />
    </button>
    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
        <flux:icon.magnifying-glass class="size-4" />
    </div>
</div>
