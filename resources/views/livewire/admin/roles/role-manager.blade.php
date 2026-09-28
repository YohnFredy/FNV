<div class="space-y-6">
    {{-- Encabezado Principal --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <flux:heading size="xl" class="font-black text-zinc-900 dark:text-zinc-50">
                    Seguridad y Control de Acceso
                </flux:heading>
                <flux:badge color="zinc" size="sm" class="font-mono text-xs">
                    RBAC Spatie
                </flux:badge>
            </div>
            <flux:subheading class="text-zinc-600 dark:text-zinc-400">
                Gestión profesional de roles, matriz de permisos granulares y asignación a miembros del equipo.
            </flux:subheading>
        </div>

        @if ($this->canCreate())
            <flux:button
                wire:click="openCreateRole"
                variant="primary"
                icon="plus"
                class="shadow-sm"
            >
                Nuevo Rol
            </flux:button>
        @endif
    </div>

    {{-- Pestañas de Navegación --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <nav class="-mb-px flex gap-6" aria-label="Tabs">
            <button
                type="button"
                wire:click="$set('activeTab', 'roles')"
                class="cursor-pointer pb-3 text-sm font-semibold transition-colors border-b-2 flex items-center gap-2 {{ $activeTab === 'roles' ? 'border-primary text-primary dark:border-zinc-100 dark:text-zinc-100' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.shield-check class="size-4" />
                Roles del Sistema ({{ count($roles) }})
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'permissions')"
                class="cursor-pointer pb-3 text-sm font-semibold transition-colors border-b-2 flex items-center gap-2 {{ $activeTab === 'permissions' ? 'border-primary text-primary dark:border-zinc-100 dark:text-zinc-100' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.key class="size-4" />
                Matriz de Permisos
            </button>

            <button
                type="button"
                wire:click="$set('activeTab', 'users')"
                class="cursor-pointer pb-3 text-sm font-semibold transition-colors border-b-2 flex items-center gap-2 {{ $activeTab === 'users' ? 'border-primary text-primary dark:border-zinc-100 dark:text-zinc-100' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.user-group class="size-4" />
                Asignación de Usuarios
            </button>
        </nav>
    </div>

    {{-- TAB 1: LISTADO DE ROLES --}}
    @if ($activeTab === 'roles')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($roles as $role)
                @php
                    $enumRole = \App\Enums\RoleName::tryFrom($role->name);
                    $isProtected = $enumRole?->isProtected() ?? false;
                @endphp
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/80 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base">
                                        {{ $enumRole?->label() ?? ucfirst($role->name) }}
                                    </h3>
                                    @if ($isProtected)
                                        <flux:badge color="zinc" size="sm">Sistema</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm" class="opacity-80">Personalizado</flux:badge>
                                    @endif
                                </div>
                                <code class="text-xs font-mono text-zinc-500 dark:text-zinc-400">{{ $role->name }}</code>
                            </div>

                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                <flux:icon.users class="size-3" />
                                {{ $role->users_count }}
                            </span>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-400 min-h-[32px] line-clamp-2">
                            {{ $enumRole?->description() ?? 'Rol personalizado con permisos específicos configurados por la administración.' }}
                        </p>

                        {{-- Resumen de permisos --}}
                        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80">
                            <div class="flex items-center justify-between text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-2">
                                <span>Permisos Activos</span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-200">{{ $role->permissions_count }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto pr-1">
                                @forelse ($role->permissions->take(6) as $perm)
                                    <span class="inline-block rounded px-1.5 py-0.5 text-[10px] font-mono bg-zinc-200/80 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $perm->name }}
                                    </span>
                                @empty
                                    <span class="text-[11px] italic text-zinc-400 dark:text-zinc-500">Sin permisos explícitos asignados.</span>
                                @endforelse
                                @if ($role->permissions_count > 6)
                                    <span class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 self-center">
                                        +{{ $role->permissions_count - 6 }} más
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Acciones del Rol --}}
                    <div class="mt-5 flex items-center justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        @if ($this->canEdit())
                            <flux:button
                                wire:click="editRole({{ $role->id }})"
                                size="sm"
                                variant="subtle"
                                icon="pencil-square"
                            >
                                Permisos
                            </flux:button>
                        @endif

                        @if (! $isProtected && $this->canDelete())
                            <flux:button
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="¿Estás completamente seguro de eliminar este rol? Esta acción no se puede deshacer."
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                class="text-danger hover:bg-danger/10"
                            >
                                Eliminar
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- TAB 2: MATRIZ DE PERMISOS --}}
    @if ($activeTab === 'permissions')
        <div class="space-y-6">
            @foreach ($groupedPermissions as $groupTitle => $groupList)
                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/60 overflow-hidden">
                    <div class="bg-zinc-50 px-5 py-3 border-b border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon.folder class="size-4 text-zinc-500 dark:text-zinc-400" />
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">{{ $groupTitle }}</h4>
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                            {{ count($groupList) }} permisos
                        </span>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                        @foreach ($groupList as $perm)
                            @php
                                $enumPerm = \App\Enums\PermissionName::tryFrom($perm->name);
                            @endphp
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs font-mono font-semibold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                            {{ $perm->name }}
                                        </code>
                                    </div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                        {{ $enumPerm?->label() ?? 'Permiso del sistema.' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @php
                                        $rolesWithThisPerm = $roles->filter(fn($r) => $r->hasPermissionTo($perm->name));
                                    @endphp
                                    @forelse ($rolesWithThisPerm as $rWithPerm)
                                        @php
                                            $eR = \App\Enums\RoleName::tryFrom($rWithPerm->name);
                                        @endphp
                                        <flux:badge color="zinc" size="sm">
                                            {{ $eR?->label() ?? $rWithPerm->name }}
                                        </flux:badge>
                                    @empty
                                        <span class="text-xs italic text-zinc-400 dark:text-zinc-600">No asignado a ningún rol</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- TAB 3: ASIGNACIÓN A USUARIOS --}}
    @if ($activeTab === 'users')
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/60 p-5 space-y-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="w-full sm:w-80">
                    <flux:input
                        wire:model.live.debounce.300ms="userSearch"
                        placeholder="Buscar por nombre, correo, usuario o DNI..."
                        icon="magnifying-glass"
                        clearable
                    />
                </div>
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                    Mostrando {{ $users->total() }} usuarios registrados
                </flux:text>
            </div>

            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-left text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-900 text-xs uppercase font-semibold text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Correo & Usuario</th>
                            <th class="px-4 py-3">Roles Actuales</th>
                            <th class="px-4 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-950/40">
                        @forelse ($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $user->name }} {{ $user->last_name }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                        ID: #{{ $user->id }} {{ $user->dni ? '| DNI: '.$user->dni : '' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-300">
                                    <div>{{ $user->email }}</div>
                                    <div class="text-zinc-500 dark:text-zinc-400 font-mono">@<span>{{ $user->username }}</span></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($user->roles as $uRole)
                                            @php
                                                $eRole = \App\Enums\RoleName::tryFrom($uRole->name);
                                            @endphp
                                            <flux:badge color="zinc" size="sm" class="font-medium">
                                                {{ $eRole?->label() ?? $uRole->name }}
                                            </flux:badge>
                                        @empty
                                            <span class="text-xs italic text-zinc-400 dark:text-zinc-600">Sin roles asignados</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button
                                        wire:click="openUserRolesModal({{ $user->id }})"
                                        size="xs"
                                        variant="subtle"
                                        icon="pencil"
                                    >
                                        Gestionar Roles
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    No se encontraron usuarios que coincidan con la búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    @endif

    {{-- MODAL: CREAR / EDITAR ROL Y MATRIZ DE PERMISOS --}}
    @if ($showRoleModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-xs"
            wire:keydown.escape="$set('showRoleModal', false)"
        >
            <div class="w-full max-w-3xl max-h-[90vh] flex flex-col rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-zinc-900 dark:text-zinc-50">
                            {{ $editingRoleId ? 'Configurar Rol y Permisos' : 'Crear Nuevo Rol de Usuario' }}
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Selecciona los permisos que tendrán los usuarios asignados a este rol.
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showRoleModal', false)"
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1 cursor-pointer"
                    >
                        <flux:icon.x-mark class="size-5" />
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <form wire:submit="saveRole" id="roleForm" class="space-y-6">
                        <div>
                            <flux:field>
                                <flux:label>Identificador del Rol (Slug único)</flux:label>
                                <flux:input
                                    wire:model="roleName"
                                    placeholder="ej: supervisor_ventas, gestor_soporte"
                                    required
                                    :disabled="$editingRoleId && (\App\Enums\RoleName::tryFrom($roleName)?->isProtected() ?? false)"
                                />
                                <flux:error name="roleName" />
                                <flux:description>
                                    Usa formato en minúsculas y guiones bajos (ej: <code class="font-mono text-xs">gestor_red</code>).
                                </flux:description>
                            </flux:field>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                    Matriz de Permisos por Módulo
                                </h4>
                                <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                                    {{ count($selectedPermissions) }} permisos seleccionados
                                </span>
                            </div>

                            <div class="space-y-4">
                                @foreach ($groupedPermissions as $moduleName => $permCases)
                                    @php
                                        $permNamesArray = collect($permCases)->pluck('name')->toArray();
                                        $allInGroupChecked = count(array_intersect($permNamesArray, $selectedPermissions)) === count($permNamesArray);
                                    @endphp
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/50 p-4 space-y-3">
                                        <div class="flex items-center justify-between border-b border-zinc-200/80 dark:border-zinc-800/80 pb-2">
                                            <span class="font-bold text-xs uppercase tracking-wide text-zinc-800 dark:text-zinc-200">
                                                {{ $moduleName }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="toggleGroupPermissions({{ json_encode($permNamesArray) }})"
                                                class="text-xs text-primary dark:text-zinc-300 hover:underline cursor-pointer font-medium"
                                            >
                                                {{ $allInGroupChecked ? 'Deseleccionar Módulo' : 'Seleccionar Todo' }}
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                            @foreach ($permCases as $permItem)
                                                @php
                                                    $enumCase = \App\Enums\PermissionName::tryFrom($permItem->name);
                                                @endphp
                                                <label class="flex items-start gap-2.5 p-2 rounded-lg border border-transparent hover:border-zinc-200 dark:hover:border-zinc-800 hover:bg-white dark:hover:bg-zinc-900/60 transition cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="selectedPermissions"
                                                        value="{{ $permItem->name }}"
                                                        class="mt-1 rounded border-zinc-300 text-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-800 dark:checked:bg-zinc-200 dark:checked:border-transparent cursor-pointer"
                                                    />
                                                    <div class="text-xs">
                                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                            {{ $enumCase?->label() ?? $permItem->name }}
                                                        </div>
                                                        <code class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400">
                                                            {{ $permItem->name }}
                                                        </code>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer Modal --}}
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/60 flex items-center justify-end gap-3">
                    <flux:button
                        wire:click="$set('showRoleModal', false)"
                        variant="subtle"
                    >
                        Cancelar
                    </flux:button>
                    <flux:button
                        type="submit"
                        form="roleForm"
                        variant="primary"
                    >
                        Guardar Rol y Permisos
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: ASIGNAR ROLES A USUARIO --}}
    @if ($showUserModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-xs"
            wire:keydown.escape="$set('showUserModal', false)"
        >
            <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-50">
                            Asignar Roles a Usuario
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Selecciona los roles que tendrá este usuario en la plataforma.
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('showUserModal', false)"
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1 cursor-pointer"
                    >
                        <flux:icon.x-mark class="size-5" />
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        @foreach ($allRolesList as $rItem)
                            @php
                                $eR = \App\Enums\RoleName::tryFrom($rItem->name);
                            @endphp
                            <label class="flex items-center justify-between p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition cursor-pointer">
                                <div class="space-y-0.5">
                                    <div class="font-bold text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $eR?->label() ?? ucfirst($rItem->name) }}
                                    </div>
                                    <div class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                                        {{ $rItem->name }}
                                    </div>
                                </div>
                                <input
                                    type="checkbox"
                                    wire:model="userSelectedRoles"
                                    value="{{ $rItem->name }}"
                                    class="size-4 rounded border-zinc-300 text-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-800 dark:checked:bg-zinc-200 dark:checked:border-transparent cursor-pointer"
                                />
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/60 flex items-center justify-end gap-3">
                    <flux:button
                        wire:click="$set('showUserModal', false)"
                        variant="subtle"
                    >
                        Cancelar
                    </flux:button>
                    <flux:button
                        wire:click="saveUserRoles"
                        variant="primary"
                    >
                        Guardar Asignación
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
