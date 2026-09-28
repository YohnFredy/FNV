<flux:dropdown position="bottom" align="end" class="w-full">
    <button
        type="button"
        data-flux-sidebar-profile
        data-test="sidebar-menu-button"
        class="group flex items-center w-full gap-2.5 p-2 rounded-2xl bg-zinc-50/80 hover:bg-white dark:bg-zinc-900/80 dark:hover:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-800 hover:border-primary/30 dark:hover:border-zinc-700 shadow-xs hover:shadow-md hover:shadow-ink/70 dark:hover:shadow-none transition-all duration-200 cursor-pointer text-start"
    >
        <div class="relative shrink-0">
            <div class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white font-bold text-xs shadow-xs">
                {{ auth()->user()->initials() }}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 size-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900" title="Activo"></span>
        </div>

        <div class="in-data-flux-sidebar-collapsed-desktop:hidden min-w-0 flex-1">
            <div class="flex items-center justify-between gap-1">
                <p class="text-xs font-bold text-ink dark:text-zinc-100 truncate group-hover:text-primary dark:group-hover:text-zinc-50 transition-colors">
                    {{ auth()->user()->name }}
                </p>
                @if (auth()->user()->isAdmin())
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-premium/15 text-premium border border-premium/30 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 shrink-0">Admin</span>
                @else
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-primary/10 text-primary border border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 shrink-0">Distribuidor</span>
                @endif
            </div>
            <p class="text-[11px] font-semibold text-primary dark:text-zinc-400 truncate">
                {{ '@' . (auth()->user()->username ?? 'usuario') }}
            </p>
        </div>

        <flux:icon.chevron-up-down class="in-data-flux-sidebar-collapsed-desktop:hidden size-4 shrink-0 text-zinc-400 group-hover:text-primary dark:text-zinc-500 dark:group-hover:text-zinc-300 transition-colors" />
    </button>

    <flux:menu class="w-68 border border-zinc-200/90 bg-white shadow-md shadow-ink/70 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none p-2 rounded-2xl">
        <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/70 border border-zinc-200/80 dark:border-zinc-700/70">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-secondary text-white font-bold text-xs shadow-xs">
                {{ auth()->user()->initials() }}
            </div>
            <div class="grid flex-1 text-start text-xs leading-tight min-w-0">
                <div class="flex items-center justify-between gap-1">
                    <span class="font-bold text-ink dark:text-zinc-100 truncate">{{ auth()->user()->name }}</span>
                    @if (auth()->user()->isAdmin())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-premium/15 text-premium border border-premium/30 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 shrink-0">Admin</span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-primary/10 text-primary border border-primary/25 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 shrink-0">Distribuidor</span>
                    @endif
                </div>
                <span class="text-primary dark:text-zinc-400 truncate text-[11px] font-semibold">{{ '@' . (auth()->user()->username ?? 'usuario') }}</span>
                <span class="text-zinc-500 dark:text-zinc-400 truncate text-[10px]">{{ auth()->user()->email }}</span>
            </div>
        </div>

        <flux:menu.separator class="my-1.5 border-zinc-200/80 dark:border-zinc-800" />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('orders.index')" icon="shopping-bag" wire:navigate class="text-ink hover:text-primary hover:bg-primary/5 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 rounded-lg">
                {{ __('Mis Pedidos') }}
            </flux:menu.item>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="text-ink hover:text-primary hover:bg-primary/5 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 rounded-lg">
                {{ __('Ajustes de Perfil') }}
            </flux:menu.item>
            <flux:menu.item :href="route('appearance.edit')" icon="swatch" wire:navigate class="text-ink hover:text-primary hover:bg-primary/5 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 rounded-lg">
                {{ __('Apariencia') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator class="my-1.5 border-zinc-200/80 dark:border-zinc-800" />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item
                as="button"
                type="submit"
                icon="arrow-right-start-on-rectangle"
                class="w-full cursor-pointer text-danger hover:bg-danger/10 dark:text-danger dark:hover:bg-danger/15 font-semibold rounded-lg"
                data-test="logout-button"
            >
                {{ __('Cerrar sesión') }}
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
