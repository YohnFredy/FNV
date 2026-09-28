<?php

namespace Tests\Feature\MLM;

use App\Actions\AffiliateUserAction;
use App\Actions\GraduateAffiliateAction;
use App\DTOs\AffiliationData;
use App\Models\User;
use App\Services\TreeQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas automatizadas de alto nivel para TreeQueryService.
 * Valida la seguridad de aislamiento de red (Downline Protection),
 * el cálculo de migas de pan y la estructuración jerárquica con ranuras vacías.
 */
class TreeQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AffiliateUserAction $affiliateAction;

    protected TreeQueryService $treeQueryService;

    protected GraduateAffiliateAction $graduateAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->affiliateAction = app(AffiliateUserAction::class);
        $this->treeQueryService = app(TreeQueryService::class);
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

    public function test_security_rules_prevent_viewing_uplines_and_crosslines(): void
    {
        // 1. Crear usuario Master (ID 1)
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        // 2. Afiliar a Usuario A en pierna izquierda
        $userA = $this->createAffiliate(new AffiliationData(
            name: 'Usuario A',
            username: 'user_a',
            email: 'a@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        // 3. Afiliar a Usuario B en pierna derecha (Crossline de A)
        $userB = $this->createAffiliate(new AffiliationData(
            name: 'Usuario B',
            username: 'user_b',
            email: 'b@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));

        // 4. Afiliar a Sub-A (hijo de A en pierna izquierda)
        $subA = $this->createAffiliate(new AffiliationData(
            name: 'Sub A',
            username: 'sub_a',
            email: 'sub_a@mlm.com',
            password: 'password',
            sponsorUsername: 'user_a',
            binaryLeg: 'L',
        ));

        // REGLA 1: El Master puede ver a cualquiera
        $this->assertTrue($this->treeQueryService->canAccessBinaryUser($master, $userA->id));
        $this->assertTrue($this->treeQueryService->canAccessBinaryUser($master, $userB->id));
        $this->assertTrue($this->treeQueryService->canAccessBinaryUser($master, $subA->id));

        // REGLA 2: Usuario A puede verse a sí mismo y a su descendiente subA
        $this->assertTrue($this->treeQueryService->canAccessBinaryUser($userA, $userA->id));
        $this->assertTrue($this->treeQueryService->canAccessBinaryUser($userA, $subA->id));

        // REGLA 3: Usuario A NUNCA puede ver a su padre (Master / Upline)
        $this->assertFalse($this->treeQueryService->canAccessBinaryUser($userA, $master->id));

        // REGLA 4: Usuario A NUNCA puede ver a Usuario B (Crossline)
        $this->assertFalse($this->treeQueryService->canAccessBinaryUser($userA, $userB->id));

        // REGLA 5: Usuario B NUNCA puede ver a subA
        $this->assertFalse($this->treeQueryService->canAccessBinaryUser($userB, $subA->id));
    }

    public function test_breadcrumbs_stop_strictly_at_authenticated_user_root(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $userA = $this->createAffiliate(new AffiliationData(
            name: 'Usuario A',
            username: 'user_a',
            email: 'a@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        $subA = $this->createAffiliate(new AffiliationData(
            name: 'Sub A',
            username: 'sub_a',
            email: 'sub_a@mlm.com',
            password: 'password',
            sponsorUsername: 'user_a',
            binaryLeg: 'L',
        ));

        // Cuando Usuario A navega hacia subA, las migas de pan solo deben mostrar: [Usuario A (Tú) -> Sub A]
        // NUNCA debe incluir al Master por encima de Usuario A
        $breadcrumbs = $this->treeQueryService->getBinaryBreadcrumbs($userA, $subA->id);

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals($userA->id, $breadcrumbs[0]['id']);
        $this->assertTrue($breadcrumbs[0]['is_root']);
        $this->assertEquals($subA->id, $breadcrumbs[1]['id']);
    }

    public function test_binary_tree_structure_generates_empty_slots_and_respects_depth(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        // Afiliar solo a la izquierda
        $userA = $this->createAffiliate(new AffiliationData(
            name: 'Usuario A',
            username: 'user_a',
            email: 'a@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        // Consultar árbol con profundidad 2
        $tree = $this->treeQueryService->getBinaryTree($master->id, maxDepth: 2);

        $this->assertNotNull($tree);
        $this->assertEquals('user_node', $tree['type']);
        $this->assertEquals('master', $tree['username']);

        // El hijo izquierdo debe ser un usuario (userA)
        $this->assertNotNull($tree['left_child']);
        $this->assertEquals('user_node', $tree['left_child']['type']);
        $this->assertEquals('user_a', $tree['left_child']['username']);

        // El hijo derecho debe ser una ranura vacía (empty_slot)
        $this->assertNotNull($tree['right_child']);
        $this->assertEquals('empty_slot', $tree['right_child']['type']);
        $this->assertEquals('R', $tree['right_child']['position']);
    }

    public function test_search_downline_only_returns_descendants_of_auth_user(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master Root',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        $userA = $this->createAffiliate(new AffiliationData(
            name: 'Carlos Mendez',
            username: 'carlos_m',
            email: 'carlos@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        $userB = $this->createAffiliate(new AffiliationData(
            name: 'Carlos Zapata',
            username: 'carlos_z',
            email: 'carlosz@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));

        // Afiliar a subCarlos bajo userA
        $subA = $this->createAffiliate(new AffiliationData(
            name: 'Carlos Junior',
            username: 'carlos_jr',
            email: 'carlosjr@mlm.com',
            password: 'password',
            sponsorUsername: 'carlos_m',
            binaryLeg: 'L',
        ));

        // Búsqueda realizada por userA con término 'carlos':
        // Solo debe encontrar a 'carlos_jr' (su descendiente)
        // NUNCA debe encontrar a 'carlos_z' (que está en la pierna de userB)
        $results = $this->treeQueryService->searchDownline($userA, 'binary', 'carlos');

        $foundUsernames = array_column($results, 'username');
        $this->assertContains('carlos_jr', $foundUsernames);
        $this->assertNotContains('carlos_z', $foundUsernames);
    }

    public function test_unilevel_tree_structure_and_breadcrumbs_work_correctly(): void
    {
        $master = $this->createAffiliate(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'password',
        ));

        // Nivel 1 (Frontal de Master)
        $user1 = $this->createAffiliate(new AffiliationData(
            name: 'Directo 1',
            username: 'directo_1',
            email: 'd1@mlm.com',
            password: 'password',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        // Nivel 2 (Directo de Directo 1)
        $user2 = $this->createAffiliate(new AffiliationData(
            name: 'Directo 2',
            username: 'directo_2',
            email: 'd2@mlm.com',
            password: 'password',
            sponsorUsername: 'directo_1',
            binaryLeg: 'L',
        ));

        // Validar seguridad Unilevel
        $this->assertTrue($this->treeQueryService->canAccessUnilevelUser($user1, $user2->id));
        $this->assertFalse($this->treeQueryService->canAccessUnilevelUser($user1, $master->id));

        // Validar migas de pan unilevel
        $breadcrumbs = $this->treeQueryService->getUnilevelBreadcrumbs($user1, $user2->id);
        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals($user1->id, $breadcrumbs[0]['id']);
        $this->assertEquals($user2->id, $breadcrumbs[1]['id']);

        // Validar árbol unilevel
        $unilevelTree = $this->treeQueryService->getUnilevelTree($master->id, maxDepth: 2);
        $this->assertNotNull($unilevelTree);
        $this->assertEquals('unilevel_node', $unilevelTree['type']);
        $this->assertCount(1, $unilevelTree['children']);
        $this->assertEquals('directo_1', $unilevelTree['children'][0]['username']);
        $this->assertCount(1, $unilevelTree['children'][0]['children']);
        $this->assertEquals('directo_2', $unilevelTree['children'][0]['children'][0]['username']);
    }
}
