<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ __('Categorías') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    @if (session('success'))
        <div class="p-4 rounded-xl bg-primary/10 border border-primary/30 text-primary dark:text-zinc-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon.check-circle class="size-5 text-primary dark:text-zinc-200" />
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Encabezado de la sección -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">{{ __('Gestión de Categorías') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Organiza el catálogo de productos en estructuras jerárquicas.') }}</p>
        </div>

        @can(\App\Enums\PermissionName::CATEGORIES_CREATE->value)
            <flux:button :href="route('admin.categories.create')" icon="plus" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm" wire:navigate>
                {{ __('Nueva Categoría') }}
            </flux:button>
        @endcan
    </div>

    <!-- Tarjeta principal -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2">
                <flux:icon.tag class="size-5 text-primary dark:text-zinc-400" />
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Lista de Categorías') }}</h2>
            </div>

            <x-search wire:model="search" wire:keydown.enter="searchEnter" placeholder="Buscar categorías..." />
        </div>

        <!-- Tabla de categorías -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                        <th class="px-6 py-3.5">{{ __('Nombre') }}</th>
                        <th class="px-6 py-3.5">{{ __('Categoría Padre') }}</th>
                        <th class="px-6 py-3.5">{{ __('Estado') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($categories as $cat)
                        <tr wire:key="{{ $cat->id }}" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="size-8 rounded-lg bg-primary/10 dark:bg-zinc-800 flex items-center justify-center text-primary dark:text-zinc-300">
                                        <flux:icon.folder class="size-4" />
                                    </div>
                                    <div>
                                        <span class="font-medium">{{ $cat->name }}</span>
                                        <span class="block text-xs text-zinc-400 dark:text-zinc-500 font-mono">{{ $cat->slug }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                                @if ($cat->parent)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                        {{ $cat->parent->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('Categoría Raíz') }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($cat->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/20 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                        <span class="size-1.5 rounded-full bg-primary dark:bg-zinc-400"></span>
                                        {{ __('Activa') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger/10 text-danger border border-danger/20 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                        <span class="size-1.5 rounded-full bg-danger"></span>
                                        {{ __('Inactiva') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    @can(\App\Enums\PermissionName::CATEGORIES_EDIT->value)
                                        <flux:button :href="route('admin.categories.edit', $cat)" size="sm" variant="ghost" icon="pencil-square" class="text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-100" wire:navigate title="Editar" />
                                    @endcan

                                    @can(\App\Enums\PermissionName::CATEGORIES_DELETE->value)
                                        <flux:button wire:click="destroy({{ $cat->id }})" wire:confirm="¿Estás seguro de eliminar la categoría {{ $cat->name }}?" size="sm" variant="ghost" icon="trash" class="text-danger hover:bg-danger/10" title="Eliminar" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No se encontraron categorías.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
