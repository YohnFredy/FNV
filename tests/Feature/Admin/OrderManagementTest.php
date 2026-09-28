<?php

namespace Tests\Feature\Admin;

use App\Actions\Orders\GenerateOrderPointsAction;
use App\Enums\RoleName;
use App\Livewire\Admin\Orders\OrderIndex;
use App\Livewire\Admin\Orders\OrderShow;
use App\Models\BinaryPath;
use App\Models\BinarySummary;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\UnilevelPath;
use App\Models\UnilevelSummary;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    protected function createAdminUser(): User
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RoleName::ADMIN->value);

        return $admin;
    }

    protected function createOrderWithItems(User $user, float $totalPts = 25.50): Order
    {
        $order = Order::create([
            'public_order_number' => 'ORD-'.strtoupper(bin2hex(random_bytes(4))),
            'user_id' => $user->id,
            'status' => Order::STATUS_SALE_PENDING,
            'shipping_type' => Order::SHIPPING_TYPE_DELIVERY,
            'shipping_name' => $user->name,
            'subtotal' => 100000,
            'discount' => 10000,
            'taxable_amount' => 90000,
            'tax_amount' => 0,
            'shipping_cost' => 0,
            'total' => 90000,
            'total_pts' => $totalPts,
            'shipping_address' => 'Carrera 10 # 20-30',
        ]);

        $category = Category::create([
            'name' => 'Salud',
            'slug' => 'salud-'.uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Fornuvi',
            'slug' => 'fornuvi-'.uniqid(),
        ]);

        $product = Product::create([
            'name' => 'Producto de Prueba',
            'slug' => 'producto-de-prueba-'.uniqid(),
            'description' => 'Descripción de prueba',
            'price' => 100000,
            'tax_percent' => 0,
            'pts_base' => $totalPts,
            'pts_dist' => $totalPts,
            'is_active' => true,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => 'Producto de Prueba',
            'unit_price' => 100000,
            'pts' => $totalPts,
            'quantity' => 1,
            'discount' => 10000,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'unit_sales_price' => 90000,
            'total_pts' => $totalPts,
        ]);

        return $order;
    }

    public function test_guest_cannot_access_orders_section(): void
    {
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_orders_section(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(RoleName::AFFILIATE->value);

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_orders_index(): void
    {
        $admin = $this->createAdminUser();
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $order = $this->createOrderWithItems($buyer);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->public_order_number)
            ->assertSee($buyer->name);
    }

    public function test_admin_can_view_order_show(): void
    {
        $admin = $this->createAdminUser();
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $order = $this->createOrderWithItems($buyer);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->public_order_number)
            ->assertSee('Producto de Prueba')
            ->assertSee('$90.000');
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = $this->createAdminUser();
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $order = $this->createOrderWithItems($buyer);

        Livewire::actingAs($admin)
            ->test(OrderIndex::class)
            ->call('updateOrderStatus', $order->id, Order::STATUS_SALE_APPROVED)
            ->assertHasNoErrors();

        $this->assertEquals(Order::STATUS_SALE_APPROVED, $order->fresh()->status);
    }

    public function test_admin_can_generate_points_and_propagate_to_trees(): void
    {
        $admin = $this->createAdminUser();

        // Estructura de red: Sponsor (Raíz) -> Comprador (Descendiente)
        $sponsor = User::factory()->create(['email_verified_at' => now()]);
        $buyer = User::factory()->create(['email_verified_at' => now()]);

        // Resúmenes de red
        UnilevelSummary::create(['user_id' => $sponsor->id, 'personal_points' => 0, 'group_points' => 0]);
        UnilevelSummary::create(['user_id' => $buyer->id, 'personal_points' => 0, 'group_points' => 0]);
        BinarySummary::create(['user_id' => $sponsor->id, 'total_left_points' => 0, 'total_right_points' => 0]);
        BinarySummary::create(['user_id' => $buyer->id, 'total_left_points' => 0, 'total_right_points' => 0]);

        // Rutas Unilevel (sponsor es ancestro de buyer)
        UnilevelPath::create(['ancestor_id' => $sponsor->id, 'descendant_id' => $buyer->id, 'depth' => 1]);
        UnilevelPath::create(['ancestor_id' => $buyer->id, 'descendant_id' => $buyer->id, 'depth' => 0]);

        // Rutas Binarias (sponsor es ancestro en pierna izquierda 'L')
        BinaryPath::create(['ancestor_id' => $sponsor->id, 'descendant_id' => $buyer->id, 'leg' => 'L', 'depth' => 1]);
        BinaryPath::create(['ancestor_id' => $buyer->id, 'descendant_id' => $buyer->id, 'leg' => null, 'depth' => 0]);

        $order = $this->createOrderWithItems($buyer, 50.00);
        $order->update(['status' => Order::STATUS_SALE_APPROVED]);

        // Ejecutar acción de generación de puntos a través de Livewire
        Livewire::actingAs($admin)
            ->test(OrderShow::class, ['order' => $order])
            ->call('generatePoints')
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertEquals(Order::STATUS_PTS_GENERATED, $order->status);

        // 1. Puntos personales del comprador
        $buyerSummary = UnilevelSummary::where('user_id', $buyer->id)->first();
        $this->assertEquals(50.00, (float) $buyerSummary->personal_points);

        // 2. Puntos grupales del sponsor en Unilevel
        $sponsorUnilevel = UnilevelSummary::where('user_id', $sponsor->id)->first();
        $this->assertEquals(50.00, (float) $sponsorUnilevel->group_points);

        // 3. Puntos en pierna izquierda del sponsor en Binario
        $sponsorBinary = BinarySummary::where('user_id', $sponsor->id)->first();
        $this->assertEquals(50.00, (float) $sponsorBinary->total_left_points);

        // 4. Registros en el libro contable inmutable
        $ledgerCount = PointTransaction::where('source_type', 'order')
            ->where('order_id', $order->id)
            ->count();
        $this->assertEquals(3, $ledgerCount); // Personal + Unilevel grupal + Binario pierna L
    }

    public function test_cannot_generate_points_twice_idempotency(): void
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        UnilevelSummary::create(['user_id' => $buyer->id, 'personal_points' => 0, 'group_points' => 0]);

        $order = $this->createOrderWithItems($buyer, 20.00);
        $order->update(['status' => Order::STATUS_SALE_APPROVED]);

        $action = app(GenerateOrderPointsAction::class);

        // Primera ejecución exitosa
        $action->execute($order);
        $this->assertEquals(Order::STATUS_PTS_GENERATED, $order->fresh()->status);

        // Segunda ejecución debe arrojar RuntimeException
        $this->expectException(RuntimeException::class);
        $action->execute($order);
    }
}
