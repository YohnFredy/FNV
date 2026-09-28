<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 antialiased text-zinc-900 dark:text-zinc-100">
    @php
    $authUser = auth()->user();
    $cartCount = collect(session('cart', []))->sum('quantity');
    @endphp

    <flux:sidebar stashable sticky class="border-e border-zinc-200/90 bg-white dark:border-zinc-800 dark:bg-zinc-900/95 ">

        <a href="{{ route('office.dashboard') }}" class="" wire:navigate title="Fornuvi - Oficina Virtual">
            <x-side-app-logo />
        </a>

        {{-- Navegación del Menú Lateral --}}
        <flux:sidebar.nav class="flex flex-col gap-1 ">
            {{-- GRUPO: MI OFICINA --}}
            <x-sidebar.heading :first="true">
                {{ __('Mi Oficina') }}
            </x-sidebar.heading>

            <x-sidebar.item
                icon="squares-2x2"
                :href="route('office.dashboard')"
                :current="request()->routeIs('office.dashboard') || request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </x-sidebar.item>

            {{-- GRUPO: RED MULTINIVEL --}}
            <x-sidebar.heading>
                {{ __('Red & Genealogía') }}
            </x-sidebar.heading>

            <x-sidebar.item
                icon="cpu-chip"
                :href="route('office.network.binary')"
                :current="request()->routeIs('office.network.binary') || request()->routeIs('network.binary')"
                wire:navigate>
                {{ __('Árbol Binario') }}
            </x-sidebar.item>

            <x-sidebar.item
                icon="user-group"
                :href="route('office.network.unilevel')"
                :current="request()->routeIs('office.network.unilevel') || request()->routeIs('network.unilevel')"
                wire:navigate>
                {{ __('Árbol Unilevel') }}
            </x-sidebar.item>



            {{-- GRUPO: ADMINISTRACIÓN (Si el usuario es admin) --}}
            @if ($authUser?->isAdmin())
            <x-sidebar.heading icon="shield-check" variant="premium">
                {{ __('Administración') }}
            </x-sidebar.heading>

            <x-sidebar.item
                icon="shield-check"
                variant="premium"
                :href="route('admin.index')"
                :current="request()->routeIs('admin.*')"
                wire:navigate>
                {{ __('Panel Admin') }}
            </x-sidebar.item>
            @endif

            {{-- GRUPO: ACCESOS Y CONFIGURACIÓN --}}
            <x-sidebar.heading icon="cog" variant="ink">
                {{ __('General') }}
            </x-sidebar.heading>

            <x-sidebar.item
                icon="globe-alt"
                :href="route('home')"
                :current="request()->routeIs('home')"
                wire:navigate>
                {{ __('Sitio Web') }}
            </x-sidebar.item>

            <x-sidebar.item
                icon="cog"
                :href="route('profile.edit')"
                :current="request()->routeIs('profile.*')"
                wire:navigate>
                {{ __('Ajustes de Perfil') }}
            </x-sidebar.item>
        </flux:sidebar.nav>

        <flux:spacer />

        {{-- Menú de Usuario en Footer --}}
        @if ($authUser)
        <div class="pt-2 border-t border-zinc-200/80 dark:border-zinc-800">
            <x-desktop-user-menu class="hidden lg:block" :name="$authUser->name" />
        </div>
        @endif
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header class="lg:hidden border-b border-zinc-200/90 bg-white/95 dark:border-zinc-800 dark:bg-zinc-900/95 backdrop-blur-md px-3 py-2 flex items-center justify-between">
        <flux:sidebar.toggle class="lg:hidden text-ink dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg p-1 cursor-pointer" icon="bars-2" inset="left" />

        <a href="{{ route('office.dashboard') }}" wire:navigate class="flex items-center gap-2">
            <div class="flex items-center justify-center p-1.5 rounded-xl bg-white border border-zinc-200/80 dark:border-zinc-700 shadow-xs">
                <img src="{{ asset('storage/logo/logo_fornuvi.png') }}" alt="Fornuvi" class="h-7 w-auto object-contain">
            </div>
            <span class="text-xs font-bold uppercase tracking-wider text-ink dark:text-zinc-100">Oficina</span>
        </a>

        <div class="flex items-center gap-2">
            @if ($cartCount > 0)
            <a href="{{ route('products.cart') }}" wire:navigate class="relative p-1.5 text-primary hover:text-secondary dark:text-zinc-300">
                <flux:icon.shopping-cart class="size-5" />
                <span class="absolute -top-1 -right-1 size-4 rounded-full bg-danger text-white text-[10px] font-bold flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            </a>
            @endif

            @if ($authUser)
            <flux:dropdown position="bottom" align="end">
                <flux:profile
                    :initials="$authUser->initials()"
                    icon-trailing="chevron-down"
                    class="cursor-pointer" />

                <flux:menu class="w-64 border border-zinc-200 bg-white shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none p-1.5">
                    <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/60">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-secondary text-white font-bold text-xs shadow-xs">
                                {{ $authUser->initials() }}
                            </span>
                            <div class="grid flex-1 text-start text-xs leading-tight min-w-0">
                                <span class="truncate font-bold text-ink dark:text-zinc-100">{{ $authUser->name }}</span>
                                <span class="truncate text-zinc-500 dark:text-zinc-400 text-[11px]">{{ $authUser->email }}</span>
                            </div>
                        </div>
                    </div>

                    <flux:menu.separator class="my-1.5 border-zinc-200/80 dark:border-zinc-800" />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('office.dashboard')" icon="squares-2x2" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Dashboard') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('office.network.binary')" icon="cpu-chip" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Árbol Binario') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('office.network.unilevel')" icon="user-group" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Árbol Unilevel') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('products.index')" icon="shopping-bag" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Catálogo') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('orders.index')" icon="clipboard-document-list" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Mis Pedidos') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('home')" icon="globe-alt" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Sitio Web') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="text-ink hover:text-primary dark:text-zinc-200">
                            {{ __('Ajustes') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator class="my-1.5 border-zinc-200/80 dark:border-zinc-800" />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer text-danger hover:bg-danger/10 dark:text-danger dark:hover:bg-danger/15 font-medium"
                            data-test="logout-button">
                            {{ __('Cerrar sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
            @endif
        </div>
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>