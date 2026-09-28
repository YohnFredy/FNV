<?php

namespace App\Enums;

enum PermissionName: string
{
    // Acceso Base al Panel Administrativo
    case ADMIN_ACCESS = 'admin.access';

    // Módulo de Roles y Seguridad (RoleManager)
    case ROLES_VIEW = 'roles.view';
    case ROLES_CREATE = 'roles.create';
    case ROLES_EDIT = 'roles.edit';
    case ROLES_DELETE = 'roles.delete';
    case PERMISSIONS_ASSIGN = 'permissions.assign';

    // Módulo de Catálogo: Productos
    case PRODUCTS_VIEW = 'products.view';
    case PRODUCTS_CREATE = 'products.create';
    case PRODUCTS_EDIT = 'products.edit';
    case PRODUCTS_DELETE = 'products.delete';

    // Módulo de Catálogo: Categorías
    case CATEGORIES_VIEW = 'categories.view';
    case CATEGORIES_CREATE = 'categories.create';
    case CATEGORIES_EDIT = 'categories.edit';
    case CATEGORIES_DELETE = 'categories.delete';

    // Módulo de Catálogo: Marcas
    case BRANDS_VIEW = 'brands.view';
    case BRANDS_CREATE = 'brands.create';
    case BRANDS_EDIT = 'brands.edit';
    case BRANDS_DELETE = 'brands.delete';

    // Módulo de Pedidos y Puntos MLM
    case ORDERS_VIEW = 'orders.view';
    case ORDERS_EDIT = 'orders.edit';
    case ORDERS_POINTS = 'orders.points';

    /**
     * Módulo o Grupo al que pertenece el permiso.
     */
    public function group(): string
    {
        return match ($this) {
            self::ADMIN_ACCESS => 'Acceso Global',
            self::ROLES_VIEW,
            self::ROLES_CREATE,
            self::ROLES_EDIT,
            self::ROLES_DELETE,
            self::PERMISSIONS_ASSIGN => 'Roles y Seguridad',
            self::PRODUCTS_VIEW,
            self::PRODUCTS_CREATE,
            self::PRODUCTS_EDIT,
            self::PRODUCTS_DELETE => 'Catálogo: Productos',
            self::CATEGORIES_VIEW,
            self::CATEGORIES_CREATE,
            self::CATEGORIES_EDIT,
            self::CATEGORIES_DELETE => 'Catálogo: Categorías',
            self::BRANDS_VIEW,
            self::BRANDS_CREATE,
            self::BRANDS_EDIT,
            self::BRANDS_DELETE => 'Catálogo: Marcas',
            self::ORDERS_VIEW,
            self::ORDERS_EDIT,
            self::ORDERS_POINTS => 'Gestión de Pedidos',
        };
    }

    /**
     * Nombre descriptivo para mostrar en interfaces administrativas.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN_ACCESS => 'Ingresar al Panel Administrativo',
            self::ROLES_VIEW => 'Ver Roles y Permisos',
            self::ROLES_CREATE => 'Crear Roles Personalizados',
            self::ROLES_EDIT => 'Modificar Roles y Permisos',
            self::ROLES_DELETE => 'Eliminar Roles',
            self::PERMISSIONS_ASSIGN => 'Asignar Roles a Usuarios',
            self::PRODUCTS_VIEW => 'Ver Productos',
            self::PRODUCTS_CREATE => 'Crear Productos',
            self::PRODUCTS_EDIT => 'Editar Productos',
            self::PRODUCTS_DELETE => 'Eliminar Productos',
            self::CATEGORIES_VIEW => 'Ver Categorías',
            self::CATEGORIES_CREATE => 'Crear Categorías',
            self::CATEGORIES_EDIT => 'Editar Categorías',
            self::CATEGORIES_DELETE => 'Eliminar Categorías',
            self::BRANDS_VIEW => 'Ver Marcas',
            self::BRANDS_CREATE => 'Crear Marcas',
            self::BRANDS_EDIT => 'Editar Marcas',
            self::BRANDS_DELETE => 'Eliminar Marcas',
            self::ORDERS_VIEW => 'Ver Pedidos',
            self::ORDERS_EDIT => 'Gestionar Estados de Pedidos',
            self::ORDERS_POINTS => 'Generar Puntos en Árboles MLM',
        };
    }

    /**
     * Retorna todos los casos agrupados por módulo.
     *
     * @return array<string, array<int, self>>
     */
    public static function groupedCases(): array
    {
        $grouped = [];

        foreach (self::cases() as $case) {
            $grouped[$case->group()][] = $case;
        }

        return $grouped;
    }
}
