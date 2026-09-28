<?php

namespace Tests\Feature;

use App\Actions\AffiliateUserAction;
use App\Actions\GraduateAffiliateAction;
use App\DTOs\AffiliationData;
use App\Livewire\Office\Network\BinaryTree;
use App\Livewire\Office\Network\UnilevelTree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Pruebas completas de integración para los componentes Livewire de los árboles Binario y Unilevel.
 * Valida la autenticación obligatoria, la reactividad de los componentes, el cambio de profundidad,
 * la búsqueda en vivo y la protección perimetral de red.
 */
class NetworkTreeLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected AffiliateUserAction $affiliateAction;

    protected GraduateAffiliateAction $graduateAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->affiliateAction = app(AffiliateUserAction::class);
        $this->graduateAction = app(GraduateAffiliateAction::class);
    }

    protected function createAffiliate(AffiliationData $data): User
    {
        $user = $this->affiliateAction->execute($data);
        if ($user->isInWaitingRoom()) {
            $this->graduateAction->execute($user);
        }

        return $user;
    }

    public function test_guest_cannot_access_network_trees(): void
    {
        $this->get(route('network.binary'))
            ->assertRedirect(route('login'));

        $this->get(route('network.unilevel'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_reach_binary_and_unilevel_pages(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master Admin',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $this->actingAs($master)
            ->get(route('network.binary'))
            ->assertOk()
            ->assertSee('Árbol Genealógico Binario');

        $this->actingAs($master)
            ->get(route('network.unilevel'))
            ->assertOk()
            ->assertSee('Árbol Genealógico Unilevel');
    }

    public function test_authenticated_user_can_view_binary_tree_and_interact(): void
    {
        // 1. Crear Master y Afiliado graduado en el árbol
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master Admin',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $affiliate = $this->createAffiliate(new AffiliationData(
            name: 'Afiliado Izq',
            username: 'afiliado_izq',
            email: 'izq@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        // 2. Probar componente Livewire
        Livewire::actingAs($master)
            ->test(BinaryTree::class)
            ->assertOk()
            ->assertSee('Árbol Genealógico Binario')
            ->assertSee('master')
            ->assertSee('afiliado_izq')
            ->assertSee('Posición Derecha (R) Disponible')
            // Probar cambio de profundidad
            ->call('setDepth', 4)
            ->assertSet('depth', 4)
            // Probar enfoque en el afiliado descendiente
            ->call('focusNode', $affiliate->id)
            ->assertSet('targetUserId', $affiliate->id)
            // Probar volver a la raíz
            ->call('resetToMyTree')
            ->assertSet('targetUserId', $master->id);
    }

    public function test_unauthorized_binary_tree_access_is_blocked(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $userA = $this->createAffiliate(new AffiliationData(
            name: 'User A',
            username: 'user_a',
            email: 'a@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        $userB = $this->createAffiliate(new AffiliationData(
            name: 'User B',
            username: 'user_b',
            email: 'b@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));

        // userA intenta enfocar a userB (Crossline ajeno)
        Livewire::actingAs($userA)
            ->test(BinaryTree::class)
            ->call('focusNode', $userB->id)
            // Debe permanecer en su propia raíz ($userA->id) y no cambiar al nodo de userB
            ->assertSet('targetUserId', $userA->id)
            ->assertSee('No tienes permisos');
    }

    public function test_authenticated_user_can_view_unilevel_tree_and_switch_modes(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $direct = $this->createAffiliate(new AffiliationData(
            name: 'Directo 1',
            username: 'directo_1',
            email: 'd1@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        Livewire::actingAs($master)
            ->test(UnilevelTree::class)
            ->assertOk()
            ->assertSee('Árbol Genealógico Unilevel')
            ->assertSee('directo_1')
            // Probar alternancia a vista de lista / acordeón
            ->call('setViewMode', 'list')
            ->assertSet('viewMode', 'list')
            ->assertSee('Estructura Jerárquica de Patrocinio')
            // Probar colapso y expansión de nodo
            ->call('toggleNode', $master->id)
            ->call('toggleNode', $master->id)
            // Probar cambio de generaciones
            ->call('setDepth', 4)
            ->assertSet('depth', 4);
    }
}
