<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ __('Productos') }}
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
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">{{ __('Gestión de Productos') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Administra los productos, precios, inventario y puntos MLM.') }}</p>
        </div>

        @can(\App\Enums\PermissionName::PRODUCTS_CREATE->value)
            <flux:button :href="route('admin.products.create')" icon="plus" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm" wire:navigate>
                {{ __('Nuevo Producto') }}
            </flux:button>
        @endcan
    </div>

    <!-- Tarjeta principal -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2">
                <flux:icon.shopping-bag class="size-5 text-primary dark:text-zinc-400" />
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Catálogo de Productos') }}</h2>
            </div>

            <x-search wire:model="search" wire:keydown.enter="searchEnter" placeholder="Buscar productos..." />
        </div>

        <!-- Tabla de productos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                        <th class="px-6 py-3.5">{{ __('Producto') }}</th>
                        <th class="px-6 py-3.5">{{ __('Categoría / Marca') }}</th>
                        <th class="px-6 py-3.5">{{ __('Precio') }}</th>
                        <th class="px-6 py-3.5">{{ __('Pts Base') }}</th>
                        <th class="px-6 py-3.5">{{ __('Stock') }}</th>
                        <th class="px-6 py-3.5">{{ __('Estado') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($products as $prod)
                        <tr wire:key="{{ $prod->id }}" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-12 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if ($prod->latestImage)
                                            <img src="{{ asset('storage/' . $prod->latestImage->path) }}" alt="{{ $prod->name }}" class="size-full object-cover" />
                                        @else
                                            <flux:icon.photo class="size-6 text-zinc-400" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100 truncate max-w-xs">{{ $prod->name }}</p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">{{ $prod->slug }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-400 text-xs">
                                <div>
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $prod->category?->name ?? 'Sin categoría' }}</span>
                                    @if ($prod->brand)
                                        <span class="block text-zinc-400 dark:text-zinc-500">{{ $prod->brand->name }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">
                                ${{ formatear_precio($prod->final_price) }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap font-medium text-premium dark:text-zinc-300">
                                {{ number_format($prod->pts_base, 2) }} pts
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-400">
                                @if ($prod->is_physical)
                                    <span class="text-xs {{ ($prod->stock ?? 0) > 0 ? 'text-zinc-700 dark:text-zinc-300' : 'text-danger font-semibold' }}">
                                        {{ $prod->stock ?? 0 }} {{ __('unid.') }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400 italic">{{ __('Digital') }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($prod->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/20 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                                        <span class="size-1.5 rounded-full bg-primary dark:bg-zinc-400"></span>
                                        {{ __('Activo') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger/10 text-danger border border-danger/20 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700">
                                        <span class="size-1.5 rounded-full bg-danger"></span>
                                        {{ __('Inactivo') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <flux:button :href="route('products.show', $prod)" target="_blank" size="sm" variant="ghost" icon="arrow-top-right-on-square" class="text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-100" title="Ver en tienda" />

                                    @can(\App\Enums\PermissionName::PRODUCTS_EDIT->value)
                                        <flux:button :href="route('admin.products.edit', $prod)" size="sm" variant="ghost" icon="pencil-square" class="text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-100" wire:navigate title="Editar" />
                                    @endcan

                                    @can(\App\Enums\PermissionName::PRODUCTS_DELETE->value)
                                        <flux:button wire:click="destroy({{ $prod->id }})" wire:confirm="¿Estás seguro de eliminar el producto {{ $prod->name }}?" size="sm" variant="ghost" icon="trash" class="text-danger hover:bg-danger/10" title="Eliminar" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No se encontraron productos registrados.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
