<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Livewire\Order\OrderCreate;
use App\Livewire\PurchasePolicyAndConditions;
use App\Models\ActivationPt;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Order;
use App\Models\OrderBillingData;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        ActivationPt::create(['min_pts_first' => 1.8, 'min_pts_monthly' => 1.8]);
    }

    public function test_products_listing_page_is_accessible(): void
    {
        $category = Category::create([
            'name' => 'Salud',
            'slug' => 'salud',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Fornuvi Brand',
            'slug' => 'fornuvi-brand',
        ]);

        $product = Product::create([
            'name' => 'Hepavid',
            'slug' => 'hepavid-1',
            'description' => 'Producto hepático',
            'price' => 100000,
            'tax_percent' => 19,
            'pts_base' => 10,
            'pts_dist' => 15,
            'is_active' => true,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertSee('Hepavid');
    }

    public function test_product_detail_page_is_accessible_by_slug(): void
    {
        $category = Category::create([
            'name' => 'Nutrición',
            'slug' => 'nutricion',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Hepavid Test',
            'slug' => 'hepavid-test',
            'description' => 'Excelente producto para el hígado',
            'price' => 50000,
            'tax_percent' => 19,
            'pts_base' => 5,
            'pts_dist' => 8,
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('Hepavid Test');
        $response->assertSee('Añadir al Carrito');
    }

    public function test_cart_and_order_create_require_authentication(): void
    {
        $this->get(route('products.cart'))->assertRedirect(route('login'));
        $this->get(route('orders.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_cart_and_order_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('products.cart'))->assertStatus(200);
        $this->actingAs($user)->get(route('orders.create'))->assertStatus(200);
    }

    public function test_admin_can_access_admin_catalog_routes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::ADMIN->value);

        $this->actingAs($admin)->get(route('admin.products.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.categories.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.brands.index'))->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_catalog(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.products.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.categories.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.brands.index'))->assertStatus(403);
    }

    public function test_user_can_view_bold_checkout_for_their_order(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'code' => 'CO',
            'phone_code' => '+57',
        ]);

        $department = Department::create([
            'name' => 'Valle del Cauca',
            'code' => '76',
            'country_id' => $country->id,
        ]);

        $docType = DocumentType::create([
            'name' => 'Cédula de Ciudadanía',
            'code' => 'CC',
            'country_id' => $country->id,
        ]);

        $order = Order::create([
            'public_order_number' => '6AAC174354C9BFF4',
            'user_id' => $user->id,
            'status' => Order::STATUS_SALE_PENDING,
            'shipping_type' => 1,
            'subtotal' => 100000,
            'total' => 119000,
            'total_pts' => 10,
        ]);

        OrderBillingData::create([
            'order_id' => $order->id,
            'name' => 'Juan Perez',
            'document_type_id' => $docType->id,
            'document' => '123456789',
            'email' => 'juan@test.com',
            'phone' => '3001234567',
            'country_id' => $country->id,
            'department_id' => $department->id,
            'address' => 'Calle 10 #20-30',
        ]);

        $response = $this->actingAs($user)->get(route('bold.checkout', $order));
        $response->assertStatus(200);
        $response->assertSee('6AAC174354C9BFF4');
        $response->assertSee('Pago en Línea con Bold');
    }

    public function test_order_create_preselects_user_data_automatically(): void
    {
        $country = Country::create([
            'name' => 'Colombia',
            'code' => 'CO',
            'is_active' => true,
        ]);

        $docType = DocumentType::create([
            'country_id' => $country->id,
            'name' => 'Cédula de Ciudadanía',
            'code' => 'CC',
            'is_active' => true,
        ]);

        $department = Department::create([
            'country_id' => $country->id,
            'name' => 'Valle del Cauca',
            'code' => '76',
        ]);

        $city = City::create([
            'department_id' => $department->id,
            'name' => 'Cali',
            'cost' => 15000,
        ]);

        $user = User::factory()->create([
            'name' => 'Carlos',
            'last_name' => 'Gomez',
            'document_type_id' => $docType->id,
            'dni' => '987654321',
            'email' => 'carlos@test.com',
        ]);

        $user->userData()->create([
            'phone' => '3119876543',
            'country_id' => $country->id,
            'department_id' => $department->id,
            'city_id' => $city->id,
            'address' => 'Avenida Siempre Viva 123',
        ]);

        Livewire::actingAs($user)
            ->test(OrderCreate::class)
            ->assertSet('name', 'Carlos Gomez')
            ->assertSet('document', '987654321')
            ->assertSet('email', 'carlos@test.com')
            ->assertSet('phone', '3119876543')
            ->assertSet('selectedCountry', $country->id)
            ->assertSet('selectedDepartment', $department->id)
            ->assertSet('selectedCity', $city->id)
            ->assertSet('address', 'Avenida Siempre Viva 123')
            ->assertSet('shipping_selectedCountry', $country->id)
            ->assertSet('shipping_selectedDepartment', $department->id)
            ->assertSet('shipping_selectedCity', $city->id)
            ->assertSet('shipping_address', 'Avenida Siempre Viva 123')
            ->assertSet('shipping_cost', 15000);
    }

    public function test_purchase_policy_and_conditions_modal_displays_official_terms(): void
    {
        Livewire::test(PurchasePolicyAndConditions::class)
            ->assertSet('terms', false)
            ->call('policy')
            ->assertSet('terms', true)
            ->assertSee('Política de Términos y Condiciones de Compra')
            ->assertSee('Descripción de Productos')
            ->assertSee('Política de Envío y Entrega')
            ->assertSee('Pago del envío contra entrega')
            ->assertSee('Aceptación de los Términos');
    }
}
