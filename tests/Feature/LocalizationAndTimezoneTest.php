<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class LocalizationAndTimezoneTest extends TestCase
{
    public function test_application_timezone_is_colombia(): void
    {
        $this->assertSame('America/Bogota', config('app.timezone'));
        $this->assertSame('America/Bogota', now()->getTimezone()->getName());
    }

    public function test_default_locale_is_spanish(): void
    {
        $this->assertSame('es', config('app.locale'));
        $this->assertSame('es', app()->getLocale());
    }

    public function test_spanish_translations_are_functional(): void
    {
        $this->assertSame('Iniciar sesión', __('Log in'));
        $this->assertSame('Estas credenciales no coinciden con nuestros registros.', trans('auth.failed'));
        $this->assertSame('El campo correo electrónico es obligatorio.', trans('validation.required', [
            'attribute' => trans('validation.attributes.email'),
        ]));
    }

    public function test_carbon_dates_are_formatted_in_spanish(): void
    {
        Carbon::setLocale('es');
        CarbonImmutable::setLocale('es');

        $date = CarbonImmutable::parse('2026-09-28 10:00:00');
        $this->assertStringContainsString('septiembre', $date->translatedFormat('F'));
        $this->assertStringContainsString('lunes', $date->translatedFormat('l'));
    }

    public function test_language_switch_route_updates_session_locale(): void
    {
        $response = $this->get(route('locale.switch', ['locale' => 'en']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        // Petición subsiguiente debe reflejar el idioma inglés
        $responseNext = $this->withSession(['locale' => 'en'])->get(route('home'));
        $responseNext->assertOk();
        $this->assertSame('en', app()->getLocale());
        $this->assertSame('Log In', __('Iniciar Sesión'));
        $this->assertSame('Settings', __('Ajustes'));
        $this->assertSame('My Orders', __('Mis Pedidos'));
        $this->assertSame('Home', __('Inicio'));
    }
}
