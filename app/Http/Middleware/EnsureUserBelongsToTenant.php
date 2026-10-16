<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    /**
     * Impedisce a un utente autenticato di accedere ai dati di un tenant diverso dal proprio
     * cambiando lo slug del tenant nell'URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && tenant() && $user->tenant_id !== tenant()->getTenantKey()) {
            abort(403);
        }

        return $next($request);
    }
}
