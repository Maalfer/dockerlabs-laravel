<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Si estás dentro del espacio BunkerLabs (p. ej. /bunkerlabs/*), manda a su login
        if ($request->routeIs('bunkerlabs.*') || str_starts_with($request->path(), 'bunkerlabs')) {
            return route('bunkerlabs.login');
        }

        // Para DockerLabs (p. ej. /mis-writeups, /admin, etc.), usa su login con nombre dockerlabs.login
        return route('dockerlabs.login');
    }
}
