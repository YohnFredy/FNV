<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\RegisterForm;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\Parish;
use App\Models\User;
use App\Services\PointDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_friendly_registration_urls_render_with_prefilled_sponsor_and_leg(): void
    {
        // 1. Registrar usuario maestro
        $this->post(route('register.store'), [
            'name' => 'Master Root',
            'username' => 'master',
            'email' => 'master@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        auth()->logout();

        // 2. Probar URL amigable izquierda: /register/master/left
        $responseLeft = $this->get('/register/master/left');
        $responseLeft->assertOk();
        $responseLeft->assertSee('value="master"', false);
        $responseLeft->assertSee('Pierna Izquierda (Left)');

        // 3. Probar URL amigable derecha: /register/master/right
        $responseRight = $this->get('/register/master/right');
        $responseRight->assertOk();
        $responseRight->assertSee('value="master"', false);
        $responseRight->assertSee('Pierna Derecha (Right)');
    }

    public function test_subsequent_user_can_register_with_friendly_leg_and_full_profile_data(): void
    {
        // 1. Registrar Master
        $this->post(route('register.store'), [
            'name' => 'Master Root',
            'username' => 'master',
            'email' => 'master@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        auth()->logout();

        // 2. Registrar afiliado con pierna 'left' y todos los datos de users y user_data
        $response = $this->post(route('register.store'), [
            'name' => 'Carlos',
            'last_name' => 'Gómez',
            'dni' => '1020304050',
            'username' => 'carlos_g',
            'email' => 'carlos@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'sponsor_username' => 'master',
            'binary_leg' => 'left',
            'sex' => 'male',
            'birthdate' => '1990-05-15',
            'phone' => '3001234567',
            'city' => 'Bogotá',
            'address' => 'Calle 100 # 15-20',
            'bank_name' => 'Bancolombia',
            'account_type' => 'Ahorros',
            'account_number' => '123456789',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        // 3. Validar persistencia en users
        $this->assertDatabaseHas('users', [
            'username' => 'carlos_g',
            'name' => 'Carlos',
            'last_name' => 'Gómez',
            'dni' => '1020304050',
            'email' => 'carlos@example.com',
        ]);

        // 4. Validar persistencia en user_data
        $this->assertDatabaseHas('user_data', [
            'sex' => 'male',
            'phone' => '3001234567',
            'city' => 'Bogotá',
            'address' => 'Calle 100 # 15-20',
            'bank_name' => 'Bancolombia',
            'account_type' => 'Ahorros',
            'account_number' => '123456789',
        ]);

        // 5. Validar que inicia en la Sala de Espera sin nodo binario inmediato
        $carlos = User::where('username', 'carlos_g')->first();
        $this->assertNotNull($carlos);
        $this->assertNotNull($carlos->userData);
        $this->assertEquals('1990-05-15', $carlos->userData->birthdate?->format('Y-m-d'));
        $this->assertTrue($carlos->isInWaitingRoom());
        $this->assertNull($carlos->binaryNode);

        // Al realizar compras acumuladas de al menos 1.80 pts, se gradúa y ocupa posición oficial
        app(PointDistributionService::class)->distributePoints($carlos, 1.80);
        $carlos->refresh();
        $this->assertNotNull($carlos->binaryNode);
        $this->assertEquals('L', $carlos->binaryNode->position);
        $this->assertEquals('master', $carlos->binaryNode->parentUser->username);
    }

    public function test_livewire_registration_form_provides_realtime_feedback_and_registers(): void
    {
        // 1. Crear Master
        $this->post(route('register.store'), [
            'name' => 'Master Root',
            'username' => 'master',
            'email' => 'master@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        auth()->logout();

        // 2. Crear geografía de prueba
        $country = Country::create(['name' => 'Colombia', 'country_code' => 'COL']);
        $dept = Department::create(['country_id' => $country->id, 'name' => 'Valle del Cauca']);
        $city = City::create(['department_id' => $dept->id, 'name' => 'Cali']);
        $parish = Parish::create(['city_id' => $city->id, 'name' => 'San Antonio']);

        // 3. Probar componente Livewire
        Livewire::test(RegisterForm::class, ['sponsor' => 'master', 'leg' => 'left'])
            // A. Validación en tiempo real de username
            ->set('username', 'master') // Usuario existente
            ->assertSee('ya se encuentra registrado')
            ->set('username', 'ana_maria') // Usuario disponible
            ->assertSee('Nombre de usuario disponible')

            // B. Validación en tiempo real de email
            ->set('email', 'master@example.com') // Email existente
            ->assertSee('Este correo ya está registrado')
            ->set('email', 'ana@example.com') // Email nuevo
            ->assertSee('Correo electrónico disponible')

            // C. Validación en tiempo real de contraseñas
            ->set('password', 'secret1234')
            ->set('password_confirmation', 'secret9999')
            ->assertSee('Las contraseñas no coinciden')
            ->set('password_confirmation', 'secret1234')
            ->assertSee('Las contraseñas coinciden')

            // D. Cascada geográfica
            ->set('country_id', $country->id)
            ->set('department_id', $dept->id)
            ->assertCount('cities', 1)
            ->set('city_id', $city->id)
            ->assertCount('parishes', 1)
            ->set('parish_id', $parish->id)

            // E. Intento de registro sin aceptar términos -> debe fallar
            ->set('name', 'Ana María')
            ->set('terms_accepted', false)
            ->call('register')
            ->assertHasErrors(['terms_accepted'])

            // F. Registro con términos aceptados -> éxito y activación de modal de bienvenida
            ->set('terms_accepted', true)
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('showSuccessModal', true)
            ->call('continueAfterRegister')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'ana_maria', 'name' => 'Ana María']);
        $this->assertDatabaseHas('user_data', ['department_id' => $dept->id, 'city_id' => $city->id, 'city' => 'San Antonio']);
    }

    public function test_legal_pages_are_accessible_and_linked_in_footer(): void
    {
        // 1. Términos y Condiciones
        $termsResponse = $this->get(route('legal.terms'));
        $termsResponse->assertStatus(200);
        $termsResponse->assertSee('TÉRMINOS Y CONDICIONES GENERALES DE FORNUVI S.A.S.');
        $termsResponse->assertSee('NIT 901.953.881-1');

        // 2. Contrato de Afiliación
        $contractResponse = $this->get(route('legal.contract'));
        $contractResponse->assertStatus(200);
        $contractResponse->assertSee('CONTRATO DE VINCULACIÓN COMERCIAL PARA DISTRIBUIDOR INDEPENDIENTE');
        $contractResponse->assertSee('Ley 1700 de 2013');

        // 3. Footer en Landing (welcome)
        $welcomeResponse = $this->get(route('home'));
        $welcomeResponse->assertStatus(200);
        $welcomeResponse->assertSee(route('legal.terms'));
        $welcomeResponse->assertSee(route('legal.contract'));
    }

    public function test_country_division_terms_and_parish_reset_when_country_changes(): void
    {
        $country1 = Country::create([
            'name' => 'Ecuador',
            'country_code' => 'ECU',
            'division_term_1' => 'Provincia',
            'division_term_2' => 'Cantón',
            'division_term_3' => 'Parroquia Rural',
        ]);
        $dept1 = Department::create(['country_id' => $country1->id, 'name' => 'Pichincha']);
        $city1 = City::create(['department_id' => $dept1->id, 'name' => 'Quito']);
        $parish1 = Parish::create(['city_id' => $city1->id, 'name' => 'Cumbayá']);

        $country2 = Country::create([
            'name' => 'México',
            'country_code' => 'MEX',
            'division_term_1' => 'Estado',
            'division_term_2' => 'Municipio',
            'division_term_3' => 'Colonia',
        ]);

        Livewire::test(RegisterForm::class, ['sponsor' => 'master', 'leg' => 'left'])
            ->set('country_id', $country1->id)
            ->assertSee('Provincia')
            ->assertSee('Cantón')
            ->set('department_id', $dept1->id)
            ->set('city_id', $city1->id)
            ->assertSee('Parroquia Rural')
            ->set('parish_id', $parish1->id)
            // Cambiar de país -> Debe resetear departamento, ciudad, parroquia y ocultar el campo de parroquia
            ->set('country_id', $country2->id)
            ->assertSet('department_id', null)
            ->assertSet('city_id', null)
            ->assertSet('parish_id', null)
            ->assertDontSee('Parroquia Rural')
            ->assertSee('Estado')
            ->assertSee('Municipio');
    }

    public function test_authenticated_user_can_access_friendly_registration_url_without_redirect(): void
    {
        // 1. Registrar usuario maestro
        $this->post(route('register.store'), [
            'name' => 'Master Root',
            'username' => 'lider_top',
            'email' => 'lider@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // 2. Intentar ingresar a su URL de referido estando logueado
        $response = $this->get('/register/lider_top/right');

        // Debe cargar con código 200 sin redirigir al Dashboard
        $response->assertOk();
        $response->assertSee('value="lider_top"', false);
        $response->assertSee('Sesión activa: lider_top');
    }

    public function test_registration_displays_welcome_modal_and_preserves_sponsor_session(): void
    {
        // 1. Registrar Master con unilevel y binario completos
        $this->post(route('register.store'), [
            'name' => 'Master Root',
            'username' => 'master',
            'email' => 'master@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $master = User::where('username', 'master')->firstOrFail();
        $this->actingAs($master);

        // 2. Ejecutar registro de nuevo afiliado vía Livewire
        $component = Livewire::test(RegisterForm::class, ['sponsor' => 'master', 'leg' => 'right'])
            ->set('name', 'Pedro')
            ->set('last_name', 'Pérez')
            ->set('dni', '987654321')
            ->set('username', 'pedroperez')
            ->set('email', 'pedro@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terms_accepted', true)
            ->call('register');

        // 3. Debe abrir el modal de bienvenida sin redirigir de golpe
        $component->assertSet('showSuccessModal', true)
            ->assertSet('registeredUsername', 'pedroperez')
            ->assertSet('wasAuthenticatedBefore', true)
            ->assertSee('¡Bienvenido a FORNUVI!')
            ->assertSee('@pedroperez');

        // La sesión de master sigue activa
        $this->assertAuthenticatedAs($master);

        // Al presionar continuar, redirige al dashboard
        $component->call('continueAfterRegister')
            ->assertRedirect(route('dashboard'));
    }
}
