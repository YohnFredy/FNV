<?php

namespace App\Livewire\Admin\Roles;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\User;
use App\Traits\HasCrudPermissions;
use Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Componente Livewire 4 para la Administración Centralizada de Roles y Permisos (RBAC).
 *
 * @property-read Collection<int, Role> $roles
 * @property-read array<string, array<int, Permission>> $groupedPermissions
 * @property-read LengthAwarePaginator<int, User> $users
 *
 * @author Agente Director & Spatie Security Architect
 */
#[Layout('layouts.admin')]
#[Title('Seguridad - Roles y Permisos')]
class RoleManager extends Component
{
    use HasCrudPermissions;
    use WithPagination;

    protected function permissionModule(): string
    {
        return 'roles';
    }

    /**
     * Pestaña activa ('roles', 'permissions', 'users').
     */
    public string $activeTab = 'roles';

    // Estado para Crear/Editar Rol
    public bool $showRoleModal = false;

    public ?int $editingRoleId = null;

    public string $roleName = '';

    /**
     * @var array<int, string>
     */
    public array $selectedPermissions = [];

    // Estado para Asignación de Roles a Usuarios
    public string $userSearch = '';

    public bool $showUserModal = false;

    public ?int $selectedUserId = null;

    /**
     * @var array<int, string>
     */
    public array $userSelectedRoles = [];

    public function mount(): void
    {
        $this->authorizeView();
    }

    /**
     * Lista de roles del sistema con conteo de usuarios y permisos precargados.
     *
     * @return Collection<int, Role>
     */
    #[Computed]
    public function roles(): Collection
    {
        return Role::withCount(['users', 'permissions'])
            ->with('permissions')
            ->orderBy('id')
            ->get();
    }

    /**
     * Permisos registrados en base de datos agrupados por categorías.
     *
     * @return array<string, array<int, Permission>>
     */
    #[Computed]
    public function groupedPermissions(): array
    {
        $allPermissions = Permission::orderBy('name')->get();
        $grouped = [];

        foreach ($allPermissions as $perm) {
            $enumCase = PermissionName::tryFrom($perm->name);
            $groupName = $enumCase ? $enumCase->group() : 'Otros Permisos';
            $grouped[$groupName][] = $perm;
        }

        return $grouped;
    }

    /**
     * Usuarios paginados para la pestaña de asignación.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::with('roles')
            ->when(filled($this->userSearch), function ($query): void {
                $search = '%'.trim($this->userSearch).'%';
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('dni', 'like', $search);
                });
            })
            ->latest('id')
            ->paginate(15);
    }

    /**
     * Abre el modal para crear un nuevo rol.
     */
    public function openCreateRole(): void
    {
        $this->authorizeCreate();

        $this->reset(['editingRoleId', 'roleName', 'selectedPermissions']);
        $this->resetValidation();
        $this->showRoleModal = true;
    }

    /**
     * Abre el modal para editar un rol existente y sus permisos.
     */
    public function editRole(int $roleId): void
    {
        $this->authorizeEdit();

        $role = Role::with('permissions')->findOrFail($roleId);
        $this->editingRoleId = (int) $role->id;
        $this->roleName = (string) $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        $this->resetValidation();
        $this->showRoleModal = true;
    }

    /**
     * Selecciona o deselecciona todos los permisos de un grupo específico.
     *
     * @param  array<int, string>  $permissionNames
     */
    public function toggleGroupPermissions(array $permissionNames): void
    {
        $allSelected = true;
        foreach ($permissionNames as $name) {
            if (! in_array($name, $this->selectedPermissions, true)) {
                $allSelected = false;
                break;
            }
        }

        if ($allSelected) {
            // Deseleccionar todos los del grupo
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, $permissionNames)
            );
        } else {
            // Seleccionar todos los del grupo
            $this->selectedPermissions = array_values(
                array_unique(array_merge($this->selectedPermissions, $permissionNames))
            );
        }
    }

    /**
     * Guarda (crea o actualiza) un rol y sincroniza sus permisos.
     */
    public function saveRole(): void
    {
        if ($this->editingRoleId) {
            $this->authorizeEdit();
        } else {
            $this->authorizeCreate();
        }

        $this->validate([
            'roleName' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('roles', 'name')->ignore($this->editingRoleId),
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'roleName.required' => 'El identificador del rol es obligatorio.',
            'roleName.unique' => 'Ya existe un rol con este identificador.',
            'roleName.regex' => 'El rol solo puede contener letras, números, guiones y guiones bajos.',
        ]);

        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);

            // Proteger nombre de roles nativos
            $enumCase = RoleName::tryFrom($role->name);
            if ($enumCase && $enumCase->isProtected()) {
                // Mantener el nombre del rol protegido
                $this->roleName = $role->name;
            } else {
                $role->name = $this->roleName;
                $role->save();
            }
        } else {
            $role = Role::create([
                'name' => $this->roleName,
                'guard_name' => 'web',
            ]);
        }

        // Sincronizar permisos seleccionados
        $role->syncPermissions($this->selectedPermissions);

        // Invalidar caché de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showRoleModal = false;
        $this->reset(['editingRoleId', 'roleName', 'selectedPermissions']);

        Flux::toast(
            text: 'Rol y matriz de permisos guardados exitosamente.',
            heading: 'Operación Exitosa',
            variant: 'success'
        );
    }

    /**
     * Elimina un rol si no está protegido ni tiene usuarios asignados.
     */
    public function deleteRole(int $roleId): void
    {
        $this->authorizeDelete();

        $role = Role::withCount('users')->findOrFail($roleId);

        $enumCase = RoleName::tryFrom($role->name);
        if ($enumCase && $enumCase->isProtected()) {
            Flux::toast(
                text: 'Este rol es fundamental para el sistema y está protegido contra eliminación.',
                heading: 'Acción No Permitida',
                variant: 'danger'
            );

            return;
        }

        if ($role->users_count > 0) {
            Flux::toast(
                text: "No se puede eliminar el rol porque tiene {$role->users_count} usuario(s) asignado(s). Reasigna los usuarios primero.",
                heading: 'Rol en Uso',
                variant: 'danger'
            );

            return;
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Flux::toast(
            text: 'El rol ha sido eliminado permanentemente.',
            heading: 'Rol Eliminado',
            variant: 'success'
        );
    }

    /**
     * Abre el modal para asignar o revocar roles a un usuario específico.
     */
    public function openUserRolesModal(int $userId): void
    {
        $this->ensurePermission(PermissionName::PERMISSIONS_ASSIGN);

        $user = User::with('roles')->findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->userSelectedRoles = $user->roles->pluck('name')->toArray();

        $this->showUserModal = true;
    }

    /**
     * Guarda la asignación de roles para el usuario seleccionado.
     */
    public function saveUserRoles(): void
    {
        $this->ensurePermission(PermissionName::PERMISSIONS_ASSIGN);

        if (! $this->selectedUserId) {
            return;
        }

        $user = User::findOrFail($this->selectedUserId);

        // Protección contra despojar al último Super Admin
        if ($user->hasRole(RoleName::SUPER_ADMIN->value) && ! in_array(RoleName::SUPER_ADMIN->value, $this->userSelectedRoles, true)) {
            $superAdminCount = Role::findByName(RoleName::SUPER_ADMIN->value)->users()->count();
            if ($superAdminCount <= 1) {
                Flux::toast(
                    text: 'No es posible remover el rol Super Administrador al único titular registrado.',
                    heading: 'Operación Bloqueada',
                    variant: 'danger'
                );

                return;
            }
        }

        $user->syncRoles($this->userSelectedRoles);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showUserModal = false;
        $this->reset(['selectedUserId', 'userSelectedRoles']);

        Flux::toast(
            text: 'Roles de usuario actualizados correctamente.',
            heading: 'Roles Asignados',
            variant: 'success'
        );
    }

    public function render(): View
    {
        return view('livewire.admin.roles.role-manager', [
            'roles' => $this->roles(),
            'groupedPermissions' => $this->groupedPermissions(),
            'users' => $this->users(),
            'allRolesList' => Role::orderBy('name')->get(),
        ]);
    }
}
