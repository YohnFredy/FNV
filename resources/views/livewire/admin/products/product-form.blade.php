<div class="space-y-6 max-w-5xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.index') }}" wire:navigate>
            {{ __('Inicio') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('admin.products.index') }}" wire:navigate>
            {{ __('Productos') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ $isEditMode ? __('Editar') : __('Nuevo') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink dark:text-zinc-50">
                {{ $isEditMode ? __('Editar Producto: :name', ['name' => $product->name]) : __('Crear Nuevo Producto') }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ __('Configura los detalles de catálogo, inventario y comisiones de red.') }}
            </p>
        </div>

        <div>
            <livewire:admin.products.calculadora-financiera />
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'save' }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Nombre del Producto -->
                <div class="md:col-span-4">
                    <x-input
                        type="text"
                        label="Nombre del Producto:"
                        wire:model.live="name"
                        required
                        autofocus
                        placeholder="Ej: Colágeno Hidrolizado Premium"
                    />
                </div>

                <!-- Marca -->
                <div class="md:col-span-2">
                    <x-select-l label="Marca:" for="brand_id" wire:model.live="brand_id">
                        <option value="">{{ __('Sin Marca / Marca Propia') }}</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </x-select-l>
                </div>

                <!-- Precios e Impuestos -->
                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Precio Público (Con IVA):"
                        wire:model.blur="final_price"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="IVA %:"
                        wire:model.blur="tax_percent"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Precio Base (Sin IVA):"
                        wire:model.blur="price"
                        required
                    />
                </div>

                <!-- Puntos MLM y Comisiones -->
                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Bono Inicio ($):"
                        wire:model="commission_income"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Pts Base (Puntos MLM):"
                        wire:model.live="pts_base"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Pts Bono:"
                        wire:model.live="pts_bonus"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        step="any"
                        label="Pts Distribución:"
                        wire:model.live="pts_dist"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        label="Descuento Máximo (%):"
                        wire:model.live="maximum_discount"
                        min="0"
                        max="100"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-input
                        type="number"
                        label="Stock / Inventario:"
                        wire:model="stock"
                        min="0"
                    />
                </div>

                <!-- Opciones de Producto -->
                <div class="md:col-span-2">
                    <x-select-l label="Tipo de Producto:" for="is_physical" wire:model.live="is_physical">
                        <option value="1">{{ __('Producto Físico (Requiere Envío)') }}</option>
                        <option value="0">{{ __('Producto Digital / Servicio') }}</option>
                    </x-select-l>
                </div>

                <div class="md:col-span-2">
                    <x-select-l label="Permitir Pedidos Pendientes:" for="allow_backorder" wire:model.live="allow_backorder">
                        <option value="1">{{ __('Permitir venta sin stock') }}</option>
                        <option value="0">{{ __('No permitir compra si está agotado') }}</option>
                    </x-select-l>
                </div>

                <div class="md:col-span-2">
                    <x-select-l label="Estado de Visibilidad:" for="is_active" wire:model.live="is_active">
                        <option value="1">{{ __('Activo y Visible en Tienda') }}</option>
                        <option value="0">{{ __('Inactivo / Oculto') }}</option>
                    </x-select-l>
                </div>

                <!-- Categorías Dinámicas en Cascada -->
                <div class="md:col-span-6 border-t border-zinc-200 dark:border-zinc-800 pt-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">
                        {{ __('Categoría del Producto') }} <span class="text-danger">*</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($categoryLevels as $level => $categoriesList)
                            @if ($categoriesList->isNotEmpty())
                                <div wire:key="prod-level-{{ $level }}">
                                    <x-select-l
                                        label="{{ $level === 0 ? 'Categoría Principal:' : 'Subcategoría Nivel ' . $level }}"
                                        for="prod_level_{{ $level }}"
                                        wire:model.live="selectedLevels.{{ $level }}"
                                    >
                                        <option value="">{{ __('Seleccione categoría...') }}</option>
                                        @foreach ($categoriesList as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </x-select-l>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if ($hasChildCategories === false && !empty($category_id))
                        <p class="mt-2 text-xs text-danger flex items-center gap-1">
                            <flux:icon.exclamation-triangle class="size-3.5" />
                            {{ __('La categoría seleccionada contiene subcategorías. Por favor elija una del último nivel.') }}
                        </p>
                    @endif
                    @error('category_id')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripciones -->
                <div class="md:col-span-6 space-y-1.5">
                    <label for="description" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ __('Descripción del Producto:') }}
                    </label>
                    <textarea
                        id="description"
                        wire:model.live="description"
                        rows="4"
                        class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500"
                        placeholder="Descripción atractiva y beneficios del producto..."
                    ></textarea>
                    @error('description')
                        <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3 space-y-1.5">
                    <label for="specifications" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ __('Especificaciones Técnicas:') }}
                    </label>
                    <textarea
                        id="specifications"
                        wire:model.live="specifications"
                        rows="3"
                        class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500"
                        placeholder="Ingredientes, peso, registro sanitario, etc."
                    ></textarea>
                </div>

                <div class="md:col-span-3 space-y-1.5">
                    <label for="information" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                        {{ __('Información de Uso / Posología:') }}
                    </label>
                    <textarea
                        id="information"
                        wire:model.live="information"
                        rows="3"
                        class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500"
                        placeholder="Modo de consumo o precauciones..."
                    ></textarea>
                </div>

                <!-- Subida de Imágenes -->
                <div class="md:col-span-6 border-t border-zinc-200 dark:border-zinc-800 pt-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('Imágenes del Producto') }}
                    </h3>

                    <x-input-file-l
                        label="Subir nuevas imágenes:"
                        for="newImages"
                        wire:model="newImages"
                        wire:loading.attr="disabled"
                    />

                    @if (!empty($newImages))
                        <div class="mt-3 flex flex-wrap gap-3">
                            @foreach ($newImages as $tempImg)
                                <div class="size-20 rounded-xl border border-primary/30 overflow-hidden relative shadow-xs">
                                    <img src="{{ $tempImg->temporaryUrl() }}" class="size-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Imágenes Existentes -->
                    @if ($isEditMode && !empty($images))
                        <div class="mt-4">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Imágenes actuales:') }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                @foreach ($images as $imgId => $imgPath)
                                    <div class="relative group size-24 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800 overflow-hidden shadow-xs" wire:key="img-{{ $imgId }}">
                                        <img src="{{ asset('storage/' . $imgPath) }}" class="size-full object-cover" />
                                        <button
                                            type="button"
                                            wire:click="removeMedia({{ $imgId }})"
                                            wire:confirm="¿Eliminar esta imagen?"
                                            class="absolute top-1 right-1 size-6 rounded-full bg-danger text-white flex items-center justify-center text-xs font-bold hover:bg-danger/80 cursor-pointer shadow-sm"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button :href="route('admin.products.index')" variant="ghost" wire:navigate>
                    {{ __('Cancelar') }}
                </flux:button>
                <flux:button type="submit" class="!bg-primary hover:!bg-secondary text-white border-none shadow-sm">
                    <span wire:loading.remove wire:target="save,update">
                        {{ $isEditMode ? __('Actualizar Producto') : __('Guardar Producto') }}
                    </span>
                    <span wire:loading wire:target="save,update">
                        {{ __('Guardando...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
