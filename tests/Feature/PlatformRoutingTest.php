<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_platform_home_is_accessible_to_guests(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Economía Colaborativa');
        $response->assertSee('Oficina');
    }

    public function test_office_platform_requires_authentication(): void
    {
        $response = $this->get(route('office.dashboard'));
        $response->assertRedirect(route('login'));

        $binaryResponse = $this->get(route('office.network.binary'));
        $binaryResponse->assertRedirect(route('login'));
    }

    public function test_office_platform_accessible_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('office.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_platform_forbidden_for_regular_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.index'));
        $response->assertStatus(403);
    }
}
