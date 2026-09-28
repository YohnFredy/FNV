<x-layouts::admin :title="__('Nueva Marca')">
    <div class="space-y-6 max-w-2xl">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
                {{ __('Inicio') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('admin.brands.index') }}" wire:navigate>
                {{ __('Marcas') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>
                {{ __('Nueva') }}
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-6">
                <h1 class="text-xl font-bold tracking-tight text-ink dark:text-zinc-50">{{ __('Crear Nueva Marca') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Registra una nueva marca en el catálogo.') }}</p>
            </div>

            <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('admin.brands.form')

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button :href="route('admin.brands.index')" variant="ghost" wire:navigate>
                        {{ __('Cancelar') }}
                    </flux:button>
                    <flux:button type="submit" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm">
                        {{ __('Guardar Marca') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::admin>
