@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'required' => false,
    'placeholder' => null,
    'loadingTarget' => null,
    'badge' => null,
    'disabled' => false,
    'options' => null,
])

@php
$selectId = $id ?? $name ?? $attributes->wire('model')->value() ?? 'select-' . uniqid();
$errorKey = $name ?? $attributes->wire('model')->value() ?? $id;
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-2') }}>
    @if ($label)
        <label for="{{ $selectId }}" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <span>{{ $label }}</span>
                @if ($required)
                    <span class="text-danger">*</span>
                @endif
            </span>

            @if ($badge)
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ $badge }}
                </span>
            @endif

            @if ($loadingTarget)
                <span wire:loading wire:target="{{ $loadingTarget }}" class="text-[11px] text-primary dark:text-zinc-400">
                    {{ __('Cargando...') }}
                </span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        @disabled($disabled)
        @if ($required) required @endif
        {{ $attributes->except('class')->class([
            'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors',
            'focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary',
            'dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500',
            'disabled:bg-zinc-100 disabled:text-zinc-400 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-600 disabled:cursor-not-allowed',
            'border-zinc-300' => !$errors->has($errorKey),
            '!border-danger' => $errors->has($errorKey),
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if ($options)
            @foreach ($options as $opt)
                @if (is_object($opt))
                    <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                @elseif (is_array($opt))
                    <option value="{{ $opt['id'] ?? '' }}">{{ $opt['name'] ?? '' }}</option>
                @else
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endif
            @endforeach
        @endif

        {{ $slot }}
    </select>

    @if ($errorKey && $errors->has($errorKey))
        <p class="text-xs text-danger">{{ $errors->first($errorKey) }}</p>
    @endif
</div>
