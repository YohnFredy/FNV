<?php

namespace App\Http\Middleware;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Maneja una solicitud entrante y verifica si el usuario cuenta con autorización administrativa.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'No autenticado.');
        }

        $hasAdminPrivileges = $user->hasRole([
            RoleName::SUPER_ADMIN->value,
            RoleName::ADMIN->value,
            RoleName::SUPPORT->value,
        ])
            || $user->can(PermissionName::ADMIN_ACCESS->value)
            || $user->isAdmin();

        if (! $hasAdminPrivileges) {
            abort(403, 'Acceso denegado: Esta sección está reservada exclusivamente para personal administrativo autorizado.');
        }

        return $next($request);
    }
}
