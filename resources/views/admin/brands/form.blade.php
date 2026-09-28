<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
            {{ __('Nombre de la marca:') }} <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $brand->name ?? '') }}"
            required
            class="mt-1.5 w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500"
            placeholder="Ej: NutriSalud"
        />
        @error('name')
            <p class="mt-1 text-xs text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }}
            class="size-4 rounded border-zinc-300 text-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-900"
        />
        <label for="is_active" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 cursor-pointer">
            {{ __('Marca activa y visible') }}
        </label>
    </div>
</div>
