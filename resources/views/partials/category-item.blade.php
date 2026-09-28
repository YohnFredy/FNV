<li class="py-1" wire:key="category-{{ $category->id }}">
    @if ($category->children->isEmpty())
        <button wire:click="category({{ $category->id }})" 
            class="cursor-pointer w-full text-left flex items-center justify-between px-2.5 py-1.5 hover:text-white hover:bg-primary/90 dark:hover:bg-zinc-800 rounded-xl group text-xs font-semibold text-ink/80 dark:text-zinc-200 transition-all duration-200 hover:shadow-sm hover:shadow-ink/40">
            <span class="truncate">{{ $category->name }}</span>
            <span class="opacity-0 group-hover:opacity-100 transition-opacity text-white">
                <flux:icon.chevron-right class="size-3.5" />
            </span>
        </button>
    @else
        <button 
            type="button"
            class="w-full px-2.5 py-1.5 hover:text-primary hover:bg-primary/5 dark:hover:bg-zinc-800 rounded-xl cursor-pointer text-xs font-semibold text-ink dark:text-zinc-200 transition-colors"
            x-on:click="openCategories[{{ $category->id }}] = !openCategories[{{ $category->id }}]">
            <span class="flex items-center justify-between">
                <span>{{ $category->name }}</span>
                <flux:icon.chevron-down
                    class="size-3.5 transition-transform duration-200"
                    x-bind:class="openCategories[{{ $category->id }}] ? 'rotate-180 text-primary dark:text-zinc-300' : 'text-zinc-400'"
                />
            </span>
        </button>

        <div x-show="openCategories[{{ $category->id }}]" x-collapse class="ml-2 pl-2 border-l border-zinc-200 dark:border-zinc-800 mt-1">
            <ul class="space-y-0.5">
                @foreach ($category->children as $child)
                    @include('partials.category-item', [
                        'category' => $child,
                        'level' => ($level ?? 0) + 1,
                    ])
                @endforeach
            </ul>
        </div>
    @endif
</li>
