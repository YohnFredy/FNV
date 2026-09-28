<div class="space-y-6 max-w-3xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('admin.categories.index') }}" wire:navigate>
            {{ __('Categorías') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ $isEditMode ? __('Editar') : __('Nueva') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mb-6">
            <h1 class="text-xl font-bold tracking-tight text-ink dark:text-zinc-50">
                {{ $isEditMode ? __('Editar Categoría: :name', ['name' => $name]) : __('Crear Nueva Categoría') }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Configura los niveles de jerarquía y propiedades de la categoría.') }}
            </p>
        </div>

        <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'save' }}" class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <x-input
                        type="text"
                        label="Nombre de la categoría:"
                        wire:model.live="name"
                        required
                        autofocus
                        placeholder="Ej: Suplementos Dietarios"
                    />
                </div>

                @foreach ($categoryLevels as $level => $categoriesList)
                    @if ($categoriesList->isNotEmpty())
                        <div class="sm:col-span-3" wire:key="level-{{ $level }}">
                            <x-select-l
                                label="{{ $level === 0 ? 'Categoría Principal (Raíz):' : 'Subcategoría Nivel ' . $level }}"
                                for="level_{{ $level }}"
                                wire:model.live="selectedLevels.{{ $level }}"
                            >
                                <option value="">{{ __('Ninguna / Categoría Raíz') }}</option>
                                @foreach ($categoriesList as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </x-select-l>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-950/50 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ __('Configuración Avanzada') }}
                </h3>

                <div class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        id="is_final"
                        wire:model="is_final"
                        class="mt-1 size-4 rounded border-zinc-300 text-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900"
                    />
                    <div>
                        <label for="is_final" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 cursor-pointer">
                            {{ __('Categoría Final (Hoja)') }}
                        </label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Si está activado, no se permitirá agregar subcategorías dependientes de esta.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        id="is_active"
                        wire:model="is_active"
                        class="mt-1 size-4 rounded border-zinc-300 text-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900"
                    />
                    <div>
                        <label for="is_active" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 cursor-pointer">
                            {{ __('Categoría Activa') }}
                        </label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Controla si los productos de esta categoría son visibles en la tienda pública.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button :href="route('admin.categories.index')" variant="ghost" wire:navigate>
                    {{ __('Cancelar') }}
                </flux:button>
                <flux:button type="submit" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm">
                    <span wire:loading.remove wire:target="save,update">
                        {{ $isEditMode ? __('Actualizar Categoría') : __('Guardar Categoría') }}
                    </span>
                    <span wire:loading wire:target="save,update">
                        {{ __('Guardando...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
