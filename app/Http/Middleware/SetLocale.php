<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Idiomas soportados por la plataforma.
     *
     * @var array<int, string>
     */
    protected array $supportedLocales = ['es', 'en'];

    /**
     * Maneja la petición entrante configurando el idioma activo.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Detección por parámetro de consulta (ej. ?lang=es o ?lang=en)
        if ($request->has('lang')) {
            $requested = (string) $request->query('lang');
            if (in_array($requested, $this->supportedLocales, true)) {
                $request->session()->put('locale', $requested);
            }
        }

        // 2. Obtención de idioma: Sesión -> Configuración (.env) -> Fallback
        /** @var string $locale */
        $locale = $request->session()->get('locale', (string) config('app.locale', 'es'));

        if (! in_array($locale, $this->supportedLocales, true)) {
            $locale = (string) config('app.fallback_locale', 'es');
        }

        // 3. Sincronización en Laravel y Carbon
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        return $next($request);
    }
}
