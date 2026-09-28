@props([
'title' => null,
'metaDescription' => null,
'ogTitle' => null,
'ogDescription' => null,
'ogImage' => null,
'ogUrl' => null,
'ogType' => null,
])

@php
// Array combinado para navbar (rutas y dropdowns en orden)
$navbarItems = [
[
'type' => 'route',
'name' => 'Inicio',
'icon' => 'home',
'route' => 'home',
'routeIs' => 'home',
],
[
'type' => 'route',
'name' => 'Productos',
'icon' => 'shopping-cart',
'route' => 'products.index',
'routeIs' => 'products*',
],
[
'type' => 'route',
'name' => 'Aliados',
'icon' => 'shopping-bag',
'route' => 'companies.index',
'routeIs' => 'companies*',
],
[
'type' => 'route',
'name' => 'Oficina',
'icon' => 'building-office-2',
'route' => 'office.dashboard',
'routeIs' => 'office.*',
],
];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-950 text-ink dark:text-zinc-100 antialiased">
    <flux:header container class="border-b border-zinc-200 bg-white shadow-md shadow-primary/80 dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none flex items-center">
        <flux:sidebar.toggle class="lg:hidden bg-danger/5! text-danger! hover:bg-secondary/5! hover:text-primary dark:bg-zinc-600! hover:dark:bg-zinc-900! dark:text-zinc-200! cursor-pointer"
            icon="bars-3" inset="left" />

        <a href="{{ route('home') }}" class="w-auto sm:w-40 lg:w-45 mr-2 ml-2 lg:ml-0" wire:navigate>
            <x-app-logo />
        </a>

        <nav class="flex items-center gap-1.5 -mb-px max-lg:hidden">
            @foreach ($navbarItems as $item)
            @php
            $isActive = ($item['type'] === 'route' && request()->routeIs($item['routeIs']))
            || ($item['type'] === 'anchor' && request()->routeIs($item['routeIs'] ?? ''));
            $href = $item['type'] === 'route' ? route($item['route']) : ($item['url'] ?? '#');
            @endphp
            <a href="{{ $href }}" wire:navigate class="group flex flex-col items-center">
                <div class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition-all duration-200 {{ $isActive ? 'text-secondary dark:text-zinc-100 font-semibold' : 'text-primary hover:bg-secondary hover:text-white dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}">
                    <flux:icon :name="$item['icon']" class="shrink-0 transition-colors" />
                    <span>{{ __($item['name']) }}</span>
                </div>
                {{-- Línea indicadora --}}
                <span class="mt-0.5 h-0.5 w-full rounded-full transition-all duration-200 {{ $isActive ? 'bg-primary dark:bg-secondary' : 'bg-transparent group-hover:bg-secondary/40 dark:group-hover:bg-zinc-700' }}"></span>
            </a>
            @endforeach

            @if (auth()->check() && auth()->user()->isAdmin())
            @php
            $isAdminActive = request()->routeIs('admin.*');
            @endphp
            <a href="{{ route('admin.index') }}" wire:navigate class="group flex flex-col items-center">
                <div class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition-all duration-200 {{ $isAdminActive ? 'text-secondary dark:text-zinc-100 font-semibold' : 'text-primary hover:bg-secondary hover:text-white dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}">
                    <flux:icon name="shield-check" variant="mini" class="shrink-0 transition-colors" />
                    <span>Admin</span>
                </div>
                <span class="mt-0.5 h-0.5 w-full rounded-full transition-all duration-200 {{ $isAdminActive ? 'bg-primary dark:bg-secondary' : 'bg-transparent group-hover:bg-secondary/40 dark:group-hover:bg-zinc-700' }}"></span>
            </a>
            @endif
        </nav>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="end">
            @auth
            <flux:profile
                class="cursor-pointer text-ink hover:text-primary dark:text-zinc-300 dark:hover:text-zinc-100 transition-colors"
                avatar:class="bg-primary text-white font-semibold dark:bg-zinc-800 dark:text-zinc-100"
                :initials="auth()->user()->initials()" />
            <flux:menu class="w-60 border border-zinc-200 bg-white shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                <flux:menu.radio.group>
                    <div class="p-1">
                        <div class="flex items-center gap-2.5 rounded-lg border border-zinc-200/80 bg-zinc-50 px-2 py-2 text-left text-sm dark:border-zinc-800 dark:bg-zinc-800/50">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-premium text-white font-semibold text-xs">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight text-ink dark:text-zinc-100 min-w-0">
                                <span class="truncate font-semibold text-ink dark:text-zinc-100">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1 border-zinc-200 dark:border-zinc-800" />

                <flux:menu.radio.group>
                    <flux:menu.item
                        href="{{ route('profile.edit') }}"
                        icon="cog"
                        wire:navigate
                        class="text-ink hover:text-primary hover:bg-primary/5 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                        {{ __('Ajustes de Perfil') }}
                    </flux:menu.item>
                    <flux:menu.item
                        href="{{ route('orders.index') }}"
                        icon="arrow-path-rounded-square"
                        wire:navigate
                        class="text-ink hover:text-primary hover:bg-primary/5 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                        {{ __('Mis Pedidos') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1 border-zinc-200 dark:border-zinc-800" />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer text-danger hover:bg-danger/10 dark:text-danger dark:hover:bg-danger/15">
                        {{ __('Cerrar sesión') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
            @else
            <flux:link
                class="flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary dark:text-zinc-300 dark:hover:text-zinc-50 transition-colors"
                href="{{ route('login') }}"
                wire:navigate>
                <span>{{ __('Iniciar Sesión') }}</span>
                <flux:icon.arrow-right-start-on-rectangle class="size-4" />
            </flux:link>
            @endauth
        </flux:dropdown>

        <!-- cart -->
        @php
        $cartCount = collect(session('cart', []))->sum('quantity');
        @endphp
        <a href="{{ route('products.cart') }}"
            wire:navigate
            class="relative flex items-center ml-3 
            {{ request()->routeIs('products.cart') ? 'text-secondary hover:text-primary border-b-2 border-primary' : 'text-primary hover:text-secondary' }}">
            <div class="text-2xl"><i class="fas fa-cart-arrow-down"></i></div>
            <div class="absolute -top-1.5 left-5.5 w-5 h-5 rounded-full bg-danger flex items-center justify-center">
                <p class="text-white text-[11px] font-bold">
                    {{ $cartCount ?: 0 }}
                </p>
            </div>
        </a>
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="-mt-12" wire:navigate>
            <x-side-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Plataforma')">
                @foreach ($navbarItems as $item)
                @if ($item['type'] === 'route')
                <flux:navlist.item icon="{{ $item['icon'] }}" :href="route($item['route'])"
                    :current="request()->routeIs($item['routeIs'])" wire:navigate>
                    {{ __($item['name']) }}
                </flux:navlist.item>
                @elseif ($item['type'] === 'anchor')
                <a href="{{ $item['url'] ?? '#' }}"
                    x-on:click="document.body.removeAttribute('data-show-stashed-sidebar')" wire:navigate>
                    <flux:navlist.item icon="{{ $item['icon'] }}" class="cursor-pointer">
                        {{ __($item['name']) }}
                    </flux:navlist.item>
                </a>
                @endif
                @endforeach

                @if (auth()->check() && auth()->user()->isAdmin())
                <flux:navlist.item icon="shield-check" href="{{ route('admin.index') }}" wire:navigate>Admin</flux:navlist.item>
                @endif
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        @auth
        <flux:navlist variant="outline" class="border-t border-zinc-200 dark:border-zinc-800 pt-3">
            <flux:navlist.item icon="cog" href="{{ route('profile.edit') }}" wire:navigate>{{ __('Ajustes de Perfil') }}</flux:navlist.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:navlist.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full text-danger">
                    {{ __('Cerrar sesión') }}
                </flux:navlist.item>
            </form>
        </flux:navlist>
        @else
        <div class="p-2 flex flex-col gap-2">
            <flux:button :href="route('login')" variant="ghost" class="w-full justify-center text-primary" wire:navigate>
                {{ __('Iniciar Sesión') }}
            </flux:button>
            <flux:button :href="route('register')" variant="primary" class="w-full justify-center !bg-primary text-white" wire:navigate>
                {{ __('Registrarse') }}
            </flux:button>
        </div>
        @endauth
    </flux:sidebar>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-app-footer />

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>