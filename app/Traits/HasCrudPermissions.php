<?php

namespace App\Traits;

use App\Enums\PermissionName;
use Illuminate\Support\Facades\Gate;

/**
 * Trait para estandarizar el control de autorización RBAC en CRUDs,
 * componentes de Livewire y Controladores del Panel Administrativo.
 */
trait HasCrudPermissions
{
    /**
     * Obtiene el prefijo base de permisos para el módulo (ej: 'users', 'roles', 'commissions').
     */
    abstract protected function permissionModule(): string;

    /**
     * Valida si el usuario tiene permiso para visualizar el módulo o listado.
     */
    protected function authorizeView(): void
    {
        $this->ensurePermission($this->permissionModule().'.view');
    }

    /**
     * Valida si el usuario tiene permiso para crear nuevos registros.
     */
    protected function authorizeCreate(): void
    {
        $this->ensurePermission($this->permissionModule().'.create');
    }

    /**
     * Valida si el usuario tiene permiso para editar registros existentes.
     */
    protected function authorizeEdit(): void
    {
        $this->ensurePermission($this->permissionModule().'.edit');
    }

    /**
     * Valida si el usuario tiene permiso para eliminar registros.
     */
    protected function authorizeDelete(): void
    {
        $this->ensurePermission($this->permissionModule().'.delete');
    }

    /**
     * Verifica una habilidad o permiso específico usando Laravel Gates.
     */
    protected function ensurePermission(string|PermissionName $permission): void
    {
        $ability = $permission instanceof PermissionName ? $permission->value : $permission;

        Gate::authorize($ability);
    }

    /**
     * Métodos booleanos auxiliares para condiciones en Blade o renderizado condicional.
     */
    public function canView(): bool
    {
        return Gate::allows($this->permissionModule().'.view');
    }

    public function canCreate(): bool
    {
        return Gate::allows($this->permissionModule().'.create');
    }

    public function canEdit(): bool
    {
        return Gate::allows($this->permissionModule().'.edit');
    }

    public function canDelete(): bool
    {
        return Gate::allows($this->permissionModule().'.delete');
    }
}
