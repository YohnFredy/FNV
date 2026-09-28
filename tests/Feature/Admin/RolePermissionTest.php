<?php

namespace Tests\Feature\Admin;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Livewire\Admin\Roles\RoleManager;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_cannot_access_roles_section(): void
    {
        $this->get(route('admin.roles'))
            ->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(RoleName::AFFILIATE->value);

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.roles'))
            ->assertForbidden();
    }

    public function test_admin_user_can_access_roles_section(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole(RoleName::ADMIN->value);

        $this->actingAs($admin)
            ->get(route('admin.roles'))
            ->assertOk()
            ->assertSee('Seguridad y Control de Acceso')
            ->assertSee('Roles del Sistema')
            ->assertSee('Matriz de Permisos');
    }

    public function test_super_admin_gate_allows_all_abilities(): void
    {
        $superAdmin = User::factory()->create(['email_verified_at' => now()]);
        $superAdmin->assignRole(RoleName::SUPER_ADMIN->value);

        $this->actingAs($superAdmin);

        $this->assertTrue(Gate::allows(PermissionName::ADMIN_ACCESS->value));
        $this->assertTrue(Gate::allows(PermissionName::ROLES_VIEW->value));
        $this->assertTrue(Gate::allows('some.arbitrary.nonexistent.permission'));
    }

    public function test_can_create_custom_role_with_permissions(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole(RoleName::ADMIN->value);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->set('roleName', 'auditor_red')
            ->set('selectedPermissions', [
                PermissionName::ADMIN_ACCESS->value,
                PermissionName::PRODUCTS_VIEW->value,
            ])
            ->call('saveRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', ['name' => 'auditor_red']);

        $role = Role::findByName('auditor_red');
        $this->assertTrue($role->hasPermissionTo(PermissionName::PRODUCTS_VIEW->value));
    }

    public function test_cannot_delete_protected_system_role(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole(RoleName::SUPER_ADMIN->value);

        $superAdminRole = Role::findByName(RoleName::SUPER_ADMIN->value);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('deleteRole', $superAdminRole->id);

        $this->assertDatabaseHas('roles', ['name' => RoleName::SUPER_ADMIN->value]);
    }

    public function test_can_assign_roles_to_user(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole(RoleName::ADMIN->value);

        $targetUser = User::factory()->create(['email_verified_at' => now()]);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->set('selectedUserId', $targetUser->id)
            ->set('userSelectedRoles', [RoleName::SUPPORT->value])
            ->call('saveUserRoles')
            ->assertHasNoErrors();

        $this->assertTrue($targetUser->fresh()->hasRole(RoleName::SUPPORT->value));
    }
}
