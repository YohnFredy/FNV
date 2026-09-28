@props(['label' => '', 'for' => '', 'disabled' => false])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $for }}" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $label }}</label>
    @endif

    <div class="w-full max-w-full overflow-hidden">
        <input id="{{ $for }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
            'class' =>
                'w-full max-w-full text-xs text-zinc-700 bg-zinc-50 border border-zinc-300 rounded-xl cursor-pointer focus:outline-none file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-secondary transition shadow-xs dark:bg-zinc-900 dark:border-zinc-700 dark:text-zinc-300 truncate',
        ]) !!} type="file" multiple />
    </div>

    @error($for)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
    @error($for . '.*')
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
