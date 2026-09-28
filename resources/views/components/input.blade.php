@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
    'required' => false,
    'placeholder' => null,
    'hint' => null,
    'status' => null,
    'loadingTarget' => null,
    'viewable' => false,
])

@php
$inputId = $id ?? $name ?? $attributes->wire('model')->value() ?? 'input-' . uniqid();
$errorKey = $name ?? $attributes->wire('model')->value() ?? $id;
$isPassword = ($type === 'password' || $viewable);
@endphp

<div {{ $attributes->only('class')->class('flex flex-col gap-2') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex items-center justify-between">
            <span>
                {{ $label }}
                @if ($required)
                    <span class="text-danger">*</span>
                @endif
            </span>

            @if ($loadingTarget)
                <span wire:loading wire:target="{{ $loadingTarget }}" class="text-[11px] text-primary dark:text-zinc-400">
                    {{ __('Verificando...') }}
                </span>
            @endif
        </label>
    @endif

    <div @if($isPassword) x-data="{ show: false }" @endif class="relative">
        <input
            id="{{ $inputId }}"
            @if ($isPassword)
                :type="show ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->except('class')->class([
                'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-zinc-900 transition-colors',
                'focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary',
                'dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-500',
                'disabled:bg-zinc-100 disabled:text-zinc-400 dark:disabled:bg-zinc-800 dark:disabled:text-zinc-600 disabled:cursor-not-allowed',
                'pr-10' => $isPassword,
                'border-zinc-300' => !($status && !$status['valid']) && !($status && $status['valid']) && !$errors->has($errorKey),
                '!border-danger' => ($status && !$status['valid']) || $errors->has($errorKey),
                '!border-green-600' => ($status && $status['valid']),
            ]) }}
        />

        @if ($isPassword)
            <button 
                type="button" 
                @click="show = !show" 
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 cursor-pointer transition-colors focus:outline-none"
                tabindex="-1"
                :title="show ? '{{ __('Ocultar contraseña') }}' : '{{ __('Mostrar contraseña') }}'"
            >
                <flux:icon.eye variant="mini" x-show="!show" class="size-4" />
                <flux:icon.eye-slash variant="mini" x-show="show" x-cloak class="size-4" />
            </button>
        @endif
    </div>

    @if ($status)
        <p class="text-xs font-semibold {{ $status['valid'] ? 'text-green-600 dark:text-green-400' : 'text-danger' }}">
            {{ $status['message'] }}
        </p>
    @endif

    @if ($errorKey && $errors->has($errorKey) && (!$status || $status['valid']))
        <p class="text-xs text-danger">{{ $errors->first($errorKey) }}</p>
    @endif

    @if ($hint)
        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
</div>
