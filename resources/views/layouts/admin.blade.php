<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 dark:bg-zinc-950 antialiased text-zinc-900 dark:text-zinc-100">
        <flux:sidebar stashable sticky class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <x-app-logo :sidebar="true" href="{{ route('admin.index') }}" wire:navigate />
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-premium/10 text-premium border border-premium/30 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700">
                        <flux:icon.shield-check class="size-3 text-premium dark:text-zinc-300" />
                        Admin
                    </span>
                </div>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Otras Plataformas')" class="grid">
                    <flux:sidebar.item icon="briefcase" :href="route('office.dashboard')" wire:navigate>
                        {{ __('Oficina Virtual') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="globe-alt" :href="route('home')" wire:navigate>
                        {{ __('Sitio Web') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @can(\App\Enums\PermissionName::ORDERS_VIEW->value)
                    <flux:sidebar.group :heading="__('Ventas y Red')" class="grid">
                        <flux:sidebar.item
                            icon="shopping-cart"
                            :href="route('admin.orders.index')"
                            :current="request()->routeIs('admin.orders.*')"
                            wire:navigate
                        >
                            {{ __('Pedidos') }}
                            @php
                                $pendingOrdersCount = \App\Models\Order::where('status', \App\Models\Order::STATUS_SALE_PENDING)->count();
                            @endphp
                            @if ($pendingOrdersCount > 0)
                                <flux:badge size="sm" color="zinc" class="ml-auto !bg-primary/10 !text-primary !border-primary/30 font-bold">
                                    {{ $pendingOrdersCount }}
                                </flux:badge>
                            @endif
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan

                <flux:sidebar.group :heading="__('Compensación y Red MLM')" class="grid">
                    <flux:sidebar.item
                        icon="banknotes"
                        :href="route('admin.mlm.settlements')"
                        :current="request()->routeIs('admin.mlm.settlements')"
                        wire:navigate
                    >
                        {{ __('Liquidaciones y Cierres') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="adjustments-horizontal"
                        :href="route('admin.mlm.settings')"
                        :current="request()->routeIs('admin.mlm.settings')"
                        wire:navigate
                    >
                        {{ __('Reglas de Calificación') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Catálogo de Productos')" class="grid">
                    <flux:sidebar.item
                        icon="shopping-bag"
                        :href="route('admin.products.index')"
                        :current="request()->routeIs('admin.products.*') || request()->routeIs('admin.index')"
                        wire:navigate
                    >
                        {{ __('Productos') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="tag"
                        :href="route('admin.categories.index')"
                        :current="request()->routeIs('admin.categories.*')"
                        wire:navigate
                    >
                        {{ __('Categorías') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="bookmark"
                        :href="route('admin.brands.index')"
                        :current="request()->routeIs('admin.brands.*')"
                        wire:navigate
                    >
                        {{ __('Marcas') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @can(\App\Enums\PermissionName::ROLES_VIEW->value)
                    <flux:sidebar.group :heading="__('Seguridad y Accesos')" class="grid">
                        <flux:sidebar.item
                            icon="shield-check"
                            :href="route('admin.roles')"
                            :current="request()->routeIs('admin.roles')"
                            wire:navigate
                        >
                            {{ __('Roles y Permisos') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan
            </flux:sidebar.nav>

            <flux:spacer />

            @auth
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            @endauth
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="flex items-center gap-2">
                <flux:icon.shield-check class="size-4 text-premium dark:text-zinc-300" />
                <span class="text-sm font-bold text-zinc-900 dark:text-white">Panel Administrativo</span>
            </div>

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <flux:avatar
                                        :name="auth()->user()->name"
                                        :initials="auth()->user()->initials()"
                                    />

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                        <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('office.dashboard')" icon="briefcase" wire:navigate>
                                {{ __('Oficina Virtual') }}
                            </flux:menu.item>
                            <flux:menu.item :href="route('home')" icon="globe-alt" wire:navigate>
                                {{ __('Sitio Web') }}
                            </flux:menu.item>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                {{ __('Ajustes') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Cerrar sesión') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @endauth
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
