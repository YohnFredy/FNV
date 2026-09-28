<div class="w-full" x-data="{ openCategories: {} }" x-cloak> 
    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
        <span class="p-1.5 rounded-lg bg-primary/10 text-primary dark:bg-zinc-800 dark:text-zinc-300">
            <flux:icon.tag class="size-4" />
        </span>
        <h3 class="text-xs font-bold uppercase tracking-wider text-primary dark:text-zinc-300">
            {{ __('Categorías') }}
        </h3>
    </div>

    <ul class="space-y-0.5">
        @foreach ($categories as $category)
            @include('partials.category-item', ['category' => $category, 'level' => 0])
        @endforeach
    </ul>
</div>
