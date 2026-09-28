<?php

namespace Tests\Feature\MLM;

use App\Actions\AffiliateUserAction;
use App\Actions\GraduateAffiliateAction;
use App\DTOs\AffiliationData;
use App\Enums\MlmStatus;
use App\Models\BinaryNode;
use App\Models\BinarySummary;
use App\Models\UnilevelNode;
use App\Models\UnilevelSummary;
use App\Models\User;
use App\Services\PointDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AffiliationPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected AffiliateUserAction $affiliateAction;

    protected PointDistributionService $pointService;

    protected GraduateAffiliateAction $graduateAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->affiliateAction = app(AffiliateUserAction::class);
        $this->pointService = app(PointDistributionService::class);
        $this->graduateAction = app(GraduateAffiliateAction::class);
    }

    public function test_first_registration_becomes_master_root_for_both_binary_and_unilevel(): void
    {
        $this->assertFalse($this->affiliateAction->isMasterRegistered());

        $masterData = new AffiliationData(
            name: 'Master Admin',
            username: 'master_root',
            email: 'master@mlm.com',
            password: 'Password123!',
        );

        $masterUser = $this->affiliateAction->execute($masterData);

        $this->assertTrue($this->affiliateAction->isMasterRegistered());
        $this->assertEquals('master_root', $masterUser->username);
        $this->assertEquals(MlmStatus::ACTIVE_AFFILIATE, $masterUser->mlm_status);

        // Validar que el nodo binario es raíz
        $binaryNode = BinaryNode::where('user_id', $masterUser->id)->first();
        $this->assertNotNull($binaryNode);
        $this->assertNull($binaryNode->parent_id);
        $this->assertNull($binaryNode->position);
        $this->assertEquals(0, $binaryNode->depth);

        // Validar que el nodo unilevel es raíz
        $unilevelNode = UnilevelNode::where('user_id', $masterUser->id)->first();
        $this->assertNotNull($unilevelNode);
        $this->assertNull($unilevelNode->sponsor_id);
        $this->assertEquals(1, $unilevelNode->level);

        // Validar resúmenes iniciales en ceros
        $binarySummary = BinarySummary::find($masterUser->id);
        $this->assertEquals(0, $binarySummary->total_left_members);
        $this->assertEquals(0, $binarySummary->total_right_members);

        $unilevelSummary = UnilevelSummary::find($masterUser->id);
        $this->assertEquals(0, $unilevelSummary->direct_sponsors_count);
        $this->assertEquals(0, $unilevelSummary->total_network_members);
    }

    public function test_subsequent_registration_requires_sponsor_and_leg(): void
    {
        // 1. Crear Master
        $this->affiliateAction->execute(new AffiliationData(
            name: 'Master Admin',
            username: 'master',
            email: 'master@mlm.com',
            password: 'Password123!',
        ));

        // 2. Intentar registrar sin sponsor debe fallar
        $this->expectException(ValidationException::class);
        $this->affiliateAction->execute(new AffiliationData(
            name: 'User 2',
            username: 'user2',
            email: 'user2@mlm.com',
            password: 'Password123!',
            sponsorUsername: null,
            binaryLeg: null,
        ));
    }

    public function test_subsequent_registration_enters_waiting_room_without_immediate_tree_placement(): void
    {
        $master = $this->affiliateAction->execute(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'Password123!',
        ));

        $user = $this->affiliateAction->execute(new AffiliationData(
            name: 'Nuevo Afiliado',
            username: 'nuevo_afiliado',
            email: 'nuevo@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));

        // Queda en sala de espera
        $this->assertEquals(MlmStatus::WAITING_ROOM, $user->mlm_status);
        $this->assertTrue($user->isInWaitingRoom());
        $this->assertEquals($master->id, $user->sponsor_id);
        $this->assertEquals('R', $user->preferred_leg);

        // NO debe tener nodo binario ni unilevel todavía
        $this->assertNull(BinaryNode::where('user_id', $user->id)->first());
        $this->assertNull(UnilevelNode::where('user_id', $user->id)->first());

        // NO puede patrocinar a otros mientras esté en sala de espera
        $this->assertFalse($user->canSponsorOthers());

        // El Master ve a este usuario en sus waitingRoomMembers
        $this->assertCount(1, $master->waitingRoomMembers);
        $this->assertEquals($user->id, $master->waitingRoomMembers->first()->id);
    }

    public function test_waiting_room_user_purchase_propagates_delta_points_and_auto_graduates_at_minimum(): void
    {
        $master = $this->affiliateAction->execute(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'Password123!',
        ));

        // Afiliado B se registra en pierna derecha de Master
        $userB = $this->affiliateAction->execute(new AffiliationData(
            name: 'Usuario B',
            username: 'usuario_b',
            email: 'b@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));

        $this->assertTrue($userB->isInWaitingRoom());

        // 1. Primera compra de B: 1.00 punto (no llega a 1.80)
        $res1 = $this->pointService->distributePoints($userB, 1.00, 101, 'Primera compra parcial');

        $this->assertEquals(1.00, $res1['points_distributed']);
        $this->assertFalse($res1['graduated']);

        // B sigue en sala de espera con 1.00 punto personal
        $userB->refresh();
        $this->assertTrue($userB->isInWaitingRoom());
        $this->assertEquals(1.00, (float) $userB->unilevelSummary->personal_points);

        // Master recibe 1.00 punto en pierna derecha
        $masterBinary = BinarySummary::find($master->id);
        $this->assertEquals(0.0, (float) $masterBinary->total_left_points);
        $this->assertEquals(1.00, (float) $masterBinary->total_right_points);
        // Y 1.00 punto grupal unilevel
        $this->assertEquals(1.00, (float) UnilevelSummary::find($master->id)->group_points);

        // 2. Segunda compra de B en el mismo mes: 0.80 puntos (total acumulado = 1.80)
        $res2 = $this->pointService->distributePoints($userB, 0.80, 102, 'Segunda compra complementaria');

        $this->assertEquals(0.80, $res2['points_distributed']);
        $this->assertTrue($res2['graduated']);

        // B se gradúa y ocupa posición oficial
        $userB->refresh();
        $this->assertEquals(MlmStatus::ACTIVE_AFFILIATE, $userB->mlm_status);
        $this->assertFalse($userB->isInWaitingRoom());
        $this->assertTrue($userB->isPlacedInTree());
        $this->assertTrue($userB->canSponsorOthers());
        $this->assertEquals(1.80, (float) $userB->unilevelSummary->personal_points);

        // Nodos creados físicamente
        $bNode = BinaryNode::where('user_id', $userB->id)->first();
        $this->assertNotNull($bNode);
        $this->assertEquals($master->id, $bNode->parent_id);
        $this->assertEquals('R', $bNode->position);

        $uNode = UnilevelNode::where('user_id', $userB->id)->first();
        $this->assertNotNull($uNode);
        $this->assertEquals($master->id, $uNode->sponsor_id);

        // Los puntos acumulados de Master en pierna derecha son exactamente 1.80 (1.00 + 0.80)
        $masterBinary->refresh();
        $this->assertEquals(1.80, (float) $masterBinary->total_right_points);
        $this->assertEquals(1, $masterBinary->total_right_members);
    }

    public function test_cannot_register_with_sponsor_who_is_in_waiting_room(): void
    {
        $master = $this->affiliateAction->execute(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'Password123!',
        ));

        // Usuario B en sala de espera
        $userB = $this->affiliateAction->execute(new AffiliationData(
            name: 'Usuario B',
            username: 'usuario_b',
            email: 'b@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));

        $this->assertFalse($userB->canSponsorOthers());

        // Intentar que Usuario C se afilie teniendo como patrocinador a B debe arrojar ValidationException
        $this->expectException(ValidationException::class);
        $this->affiliateAction->execute(new AffiliationData(
            name: 'Usuario C',
            username: 'usuario_c',
            email: 'c@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'usuario_b',
            binaryLeg: 'L',
        ));
    }

    public function test_binary_placement_spillover_in_depth_and_accurate_member_counters(): void
    {
        // 1. Crear Master
        $master = $this->affiliateAction->execute(new AffiliationData(
            name: 'Master',
            username: 'master',
            email: 'master@mlm.com',
            password: 'Password123!',
        ));

        // 2. Afiliar User Left 1 (U2) y graduarlo directamente con compra de 1.80 pts
        $u2 = $this->affiliateAction->execute(new AffiliationData(
            name: 'User Left 1',
            username: 'user_l1',
            email: 'l1@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));
        $this->pointService->distributePoints($u2, 1.80, 1, 'Activacion U2');

        $u2Node = BinaryNode::where('user_id', $u2->id)->first();
        $this->assertEquals($master->id, $u2Node->parent_id);
        $this->assertEquals('L', $u2Node->position);

        // Verificar contadores del Master
        $masterBinary = BinarySummary::find($master->id);
        $this->assertEquals(1, $masterBinary->total_left_members);
        $this->assertEquals(0, $masterBinary->total_right_members);

        // 3. Afiliar User Left 2 (U3) a la pierna izquierda del Master y graduarlo
        $u3 = $this->affiliateAction->execute(new AffiliationData(
            name: 'User Left 2',
            username: 'user_l2',
            email: 'l2@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'L',
        ));
        $this->pointService->distributePoints($u3, 1.80, 2, 'Activacion U3');

        $u3Node = BinaryNode::where('user_id', $u3->id)->first();
        // U3 debe ser hijo de U2 en su pierna izquierda por derrame exterior
        $this->assertEquals($u2->id, $u3Node->parent_id);
        $this->assertEquals('L', $u3Node->position);

        // Verificar que Master ahora tiene 2 en la izquierda
        $masterBinary->refresh();
        $this->assertEquals(2, $masterBinary->total_left_members);
        $this->assertEquals(0, $masterBinary->total_right_members);

        // U2 debe tener 1 en su pierna izquierda
        $u2Binary = BinarySummary::find($u2->id);
        $this->assertEquals(1, $u2Binary->total_left_members);
        $this->assertEquals(0, $u2Binary->total_right_members);

        // 4. Afiliar User Right 1 (U4) a la pierna derecha del Master y graduarlo
        $u4 = $this->affiliateAction->execute(new AffiliationData(
            name: 'User Right 1',
            username: 'user_r1',
            email: 'r1@mlm.com',
            password: 'Password123!',
            sponsorUsername: 'master',
            binaryLeg: 'R',
        ));
        $this->pointService->distributePoints($u4, 1.80, 3, 'Activacion U4');

        $u4Node = BinaryNode::where('user_id', $u4->id)->first();
        $this->assertEquals($master->id, $u4Node->parent_id);
        $this->assertEquals('R', $u4Node->position);

        $masterBinary->refresh();
        $this->assertEquals(2, $masterBinary->total_left_members);
        $this->assertEquals(1, $masterBinary->total_right_members);
        $this->assertEquals($u3->id, $masterBinary->extreme_left_user_id);
        $this->assertEquals($u4->id, $masterBinary->extreme_right_user_id);

        // U2 debe tener a U3 como su extremo izquierdo
        $this->assertEquals($u3->id, $u2Binary->refresh()->extreme_left_user_id);

        // 5. Verificar red unilevel del Master: tiene 3 directos y 3 en total de red
        $masterUnilevel = UnilevelSummary::find($master->id);
        $this->assertEquals(3, $masterUnilevel->direct_sponsors_count);
        $this->assertEquals(3, $masterUnilevel->total_network_members);
    }

    public function test_registration_form_and_fortify_route_creates_user_in_waiting_room(): void
    {
        // Registrar Master primero
        $responseMaster = $this->post(route('register.store'), [
            'name' => 'Root Master',
            'username' => 'root_system',
            'email' => 'root@system.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $responseMaster->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'username' => 'root_system',
            'email' => 'root@system.com',
            'mlm_status' => MlmStatus::ACTIVE_AFFILIATE->value,
        ]);

        // Cerrar sesión
        auth()->logout();

        // Registrar un nuevo afiliado con patrocinador y pierna izquierda
        $responseAffiliate = $this->post(route('register.store'), [
            'name' => 'Afiliado Uno',
            'username' => 'afiliado1',
            'email' => 'afiliado1@system.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'sponsor_username' => 'root_system',
            'binary_leg' => 'L',
        ]);

        $responseAffiliate->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'username' => 'afiliado1',
            'mlm_status' => MlmStatus::WAITING_ROOM->value,
            'preferred_leg' => 'L',
        ]);

        $newAffiliate = User::where('username', 'afiliado1')->first();
        $this->assertTrue($newAffiliate->isInWaitingRoom());
        $this->assertNull(BinaryNode::where('user_id', $newAffiliate->id)->first());
    }

    public function test_waiting_room_purchases_propagate_points_to_all_intermediary_binary_nodes_between_sponsor_and_extreme_leaf(): void
    {
        // 1. Registrar Master
        $master = $this->affiliateAction->execute(new AffiliationData(
            name: 'Master User',
            username: 'master_user',
            email: 'master@test.com',
            password: 'Password123!',
        ));

        // 2. Afiliar y activar Intermediario A en la pierna izquierda
        $userA = $this->affiliateAction->execute(new AffiliationData(
            name: 'User A',
            username: 'user_a',
            email: 'user_a@test.com',
            password: 'Password123!',
            sponsorUsername: 'master_user',
            binaryLeg: 'L',
        ));
        $this->pointService->distributePoints($userA, 1.80, 101, 'Activacion User A');

        // 3. Afiliar y activar Intermediario B en la pierna izquierda (queda bajo User A por derrame exterior)
        $userB = $this->affiliateAction->execute(new AffiliationData(
            name: 'User B',
            username: 'user_b',
            email: 'user_b@test.com',
            password: 'Password123!',
            sponsorUsername: 'master_user',
            binaryLeg: 'L',
        ));
        $this->pointService->distributePoints($userB, 1.80, 102, 'Activacion User B');

        // Verificar que B quedó debajo de A en posición izquierda
        $nodeB = BinaryNode::where('user_id', $userB->id)->first();
        $this->assertEquals($userA->id, $nodeB->parent_id);
        $this->assertEquals('L', $nodeB->position);

        // Resetear contadores de puntos para evaluar limpiamente la compra del nuevo pre-afiliado C
        BinarySummary::query()->update(['total_left_points' => 0, 'total_right_points' => 0]);

        // 4. Afiliar a User C bajo el Master en pierna izquierda (queda en Sala de Espera)
        $userC = $this->affiliateAction->execute(new AffiliationData(
            name: 'User C',
            username: 'user_c',
            email: 'user_c@test.com',
            password: 'Password123!',
            sponsorUsername: 'master_user',
            binaryLeg: 'L',
        ));

        $this->assertTrue($userC->isInWaitingRoom());
        $this->assertNull(BinaryNode::where('user_id', $userC->id)->first());

        // 5. User C realiza primera compra de 1.00 pt (aún no califica para ocupar posición)
        $this->pointService->distributePoints($userC, 1.00, 201, 'Primera compra parcial 1 pt');

        // Mientras está en sala de espera, los puntos van al patrocinador (Master) y su línea ascendente.
        // Los nodos de la red por debajo del patrocinador (A y B) NO deben recibir puntos todavía porque C no ocupa posición.
        $summaryB = BinarySummary::where('user_id', $userB->id)->first();
        $summaryA = BinarySummary::where('user_id', $userA->id)->first();
        $summaryMaster = BinarySummary::where('user_id', $master->id)->first();

        $this->assertEquals(0.00, (float) $summaryB->total_left_points, 'User B no debe recibir puntos mientras C está en sala de espera');
        $this->assertEquals(0.00, (float) $summaryA->total_left_points, 'User A no debe recibir puntos mientras C está en sala de espera');
        $this->assertEquals(1.00, (float) $summaryMaster->total_left_points, 'Master (patrocinador) debe recibir el 1.00 pt en su pierna L');

        // User C sigue en sala de espera
        $this->assertTrue($userC->refresh()->isInWaitingRoom());

        // 6. User C realiza segunda compra de 0.80 pt (acumula 1.80 pts y se gradúa automáticamente)
        $this->pointService->distributePoints($userC, 0.80, 202, 'Segunda compra 0.80 pt');

        // User C ahora debe estar formalmente graduado y colocado bajo User B en pierna izquierda
        $this->assertFalse($userC->refresh()->isInWaitingRoom());
        $this->assertEquals(MlmStatus::ACTIVE_AFFILIATE, $userC->mlm_status);

        $nodeC = BinaryNode::where('user_id', $userC->id)->first();
        $this->assertNotNull($nodeC);
        $this->assertEquals($userB->id, $nodeC->parent_id);
        $this->assertEquals('L', $nodeC->position);

        // Al graduarse, los 1.80 pts se suman a los nodos intermediarios de la red ascendente de C (B y A).
        // Y al patrocinador (Master) que ya recibió los puntos (1.00 + 0.80) NO se le repite el puntaje (permanece en 1.80).
        $summaryB->refresh();
        $summaryA->refresh();
        $summaryMaster->refresh();

        $this->assertEquals(1.80, (float) $summaryB->total_left_points, 'User B (padre inmediato) debe recibir 1.80 pts tras la colocación');
        $this->assertEquals(1.80, (float) $summaryA->total_left_points, 'User A (intermediario) debe recibir 1.80 pts tras la colocación');
        $this->assertEquals(1.80, (float) $summaryMaster->total_left_points, 'Master no debe recibir puntaje repetido (mantiene 1.80 pts)');
    }
}
