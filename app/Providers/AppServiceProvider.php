<?php

namespace App\Providers;

use App\Enums\RoleName;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureSecurityGates();
    }

    /**
     * Configuración del interceptor Super-Admin para otorgar acceso omnipotente.
     */
    protected function configureSecurityGates(): void
    {
        Gate::before(function ($user, string $ability): ?bool {
            if (is_object($user) && method_exists($user, 'hasRole') && $user->hasRole(RoleName::SUPER_ADMIN->value)) {
                return true;
            }

            return null;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // Sincronización regional para fechas y textos de Carbon
        $locale = (string) config('app.locale', 'es');
        CarbonImmutable::setLocale($locale);
        Carbon::setLocale($locale);
        setlocale(LC_TIME, 'es_CO.UTF-8', 'es_CO', 'esp', 'es');

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
