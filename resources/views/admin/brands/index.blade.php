<x-layouts::admin :title="__('Gestión de Marcas')">
    <div class="space-y-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
                {{ __('Inicio') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>
                {{ __('Marcas') }}
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
                <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">{{ __('Gestión de Marcas') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Administra las marcas oficiales de los productos.') }}</p>
            </div>

            @can(\App\Enums\PermissionName::BRANDS_CREATE->value)
                <flux:button :href="route('admin.brands.create')" icon="plus" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm" wire:navigate>
                    {{ __('Nueva Marca') }}
                </flux:button>
            @endcan
        </div>

        <!-- Tarjeta principal y Tabla -->
        <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.bookmark class="size-5 text-primary dark:text-zinc-400" />
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Lista de Marcas') }}</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                            <th class="px-6 py-3.5">{{ __('Nombre') }}</th>
                            <th class="px-6 py-3.5">{{ __('Estado') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($brands as $brand)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $brand->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($brand->is_active)
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
                                        @can(\App\Enums\PermissionName::BRANDS_EDIT->value)
                                            <flux:button :href="route('admin.brands.edit', $brand)" size="sm" variant="ghost" icon="pencil-square" class="text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-100" wire:navigate title="Editar" />
                                        @endcan

                                        @can(\App\Enums\PermissionName::BRANDS_DELETE->value)
                                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('¿Eliminar la marca {{ $brand->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" size="sm" variant="ghost" icon="trash" class="text-danger hover:bg-danger/10" title="Eliminar" />
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No se encontraron marcas registradas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($brands->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $brands->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::admin>
