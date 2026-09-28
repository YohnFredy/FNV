<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar la caché de permisos de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Obtener nombres vigentes de permisos reales
        $validPermissionNames = array_map(fn ($case) => $case->value, PermissionName::cases());

        // Eliminar permisos antiguos o no vigentes que ya no tengan archivo real
        Permission::whereNotIn('name', $validPermissionNames)->delete();

        // Registrar los permisos activos
        foreach ($validPermissionNames as $permName) {
            Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'web'],
            );
        }

        // 3. Crear roles del sistema
        $superAdminRole = Role::firstOrCreate(
            ['name' => RoleName::SUPER_ADMIN->value, 'guard_name' => 'web'],
        );
        // Super Admin tiene todos los permisos activos
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(
            ['name' => RoleName::ADMIN->value, 'guard_name' => 'web'],
        );
        $adminRole->syncPermissions([
            PermissionName::ADMIN_ACCESS->value,
            PermissionName::ROLES_VIEW->value,
            PermissionName::ROLES_CREATE->value,
            PermissionName::ROLES_EDIT->value,
            PermissionName::PERMISSIONS_ASSIGN->value,
            PermissionName::PRODUCTS_VIEW->value,
            PermissionName::PRODUCTS_CREATE->value,
            PermissionName::PRODUCTS_EDIT->value,
            PermissionName::PRODUCTS_DELETE->value,
            PermissionName::CATEGORIES_VIEW->value,
            PermissionName::CATEGORIES_CREATE->value,
            PermissionName::CATEGORIES_EDIT->value,
            PermissionName::CATEGORIES_DELETE->value,
            PermissionName::BRANDS_VIEW->value,
            PermissionName::BRANDS_CREATE->value,
            PermissionName::BRANDS_EDIT->value,
            PermissionName::BRANDS_DELETE->value,
            PermissionName::ORDERS_VIEW->value,
            PermissionName::ORDERS_EDIT->value,
            PermissionName::ORDERS_POINTS->value,
        ]);

        $supportRole = Role::firstOrCreate(
            ['name' => RoleName::SUPPORT->value, 'guard_name' => 'web'],
        );
        $supportRole->syncPermissions([
            PermissionName::ADMIN_ACCESS->value,
            PermissionName::PRODUCTS_VIEW->value,
            PermissionName::CATEGORIES_VIEW->value,
            PermissionName::BRANDS_VIEW->value,
            PermissionName::ORDERS_VIEW->value,
        ]);

        $affiliateRole = Role::firstOrCreate(
            ['name' => RoleName::AFFILIATE->value, 'guard_name' => 'web'],
        );
        $affiliateRole->syncPermissions([]);

        $leaderRole = Role::firstOrCreate(
            ['name' => RoleName::LEADER->value, 'guard_name' => 'web'],
        );
        $leaderRole->syncPermissions([]);

        // 4. Asignar rol Super Admin al usuario principal
        $primaryUser = User::where('email', 'fredy.guapacha@gmail.com')->first() ?? User::first();
        if ($primaryUser && ! $primaryUser->hasRole(RoleName::SUPER_ADMIN->value)) {
            $primaryUser->assignRole($superAdminRole);
        }

        // 5. Volver a limpiar caché
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
