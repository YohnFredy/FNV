@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'required' => false,
])

@php
$checkboxId = $id ?? $name ?? $attributes->wire('model')->value() ?? 'checkbox-' . uniqid();
$errorKey = $name ?? $attributes->wire('model')->value() ?? $id;
@endphp

<div {{ $attributes->only('class') }}>
    <div class="flex items-start gap-3">
        <input
            type="checkbox"
            id="{{ $checkboxId }}"
            @if ($required) required @endif
            {{ $attributes->except('class')->class([
                'mt-1 size-5 rounded border-zinc-300 text-primary focus:ring-primary',
                'dark:border-zinc-700 dark:bg-zinc-800 dark:checked:bg-primary cursor-pointer transition-colors',
            ]) }}
        />

        <label for="{{ $checkboxId }}" class="text-xs leading-relaxed text-zinc-700 dark:text-zinc-300 select-none cursor-pointer">
            {{ $slot->isNotEmpty() ? $slot : $label }}
        </label>
    </div>

    @if ($errorKey && $errors->has($errorKey))
        <p class="mt-2 text-xs font-semibold text-danger">{{ $errors->first($errorKey) }}</p>
    @endif
</div>
