@php
    $isMaster = !\App\Models\BinaryNode::whereNull('parent_id')->exists();
    
    // Captura desde parámetro de ruta (/register/{sponsor}/{leg}) o desde query string
    $routeSponsor = $routeSponsor ?? request()->route('sponsor');
    $routeLeg = $routeLeg ?? request()->route('leg');
    
    $defaultSponsor = $routeSponsor ?? request('sponsor', request('ref', old('sponsor_username', '')));
    
    $rawLeg = strtolower((string) ($routeLeg ?? request('leg', old('binary_leg', 'left'))));
    $defaultLeg = match($rawLeg) {
        'right', 'r' => 'R',
        default => 'L',
    };

    $documentTypes = \App\Models\DocumentType::where('is_active', true)->orderBy('id')->get();
    $countries = \App\Models\Country::orderBy('name')->get();
@endphp
<x-layouts::auth :title="$isMaster ? __('Registro Nodo Maestro') : __('Crear Cuenta de Afiliado')" max-width="max-w-2xl">
    <div class="flex flex-col gap-6">
        <x-auth-header 
            :title="$isMaster ? __('Registro de Nodo Maestro') : __('Crear cuenta de Afiliado')" 
            :description="$isMaster ? __('Configura el usuario raíz de la red multinivel') : __('Completa tus datos personales y de red para afiliarte a la organización')" 
        />

        <!-- Estado de sesión / Alertas -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Componente Livewire Interactivo con Validación en Tiempo Real y Cascada Geográfica -->
        <livewire:auth.register-form 
            :sponsor="$defaultSponsor" 
            :leg="$defaultLeg" 
            :is-master="$isMaster" 
        />

        <!-- Link al Login -->
        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('¿Ya tienes una cuenta de afiliado?') }}</span>
            <flux:link :href="route('login')" class="font-semibold text-primary hover:text-secondary dark:text-zinc-200 dark:hover:text-white" wire:navigate>
                {{ __('Iniciar sesión') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>