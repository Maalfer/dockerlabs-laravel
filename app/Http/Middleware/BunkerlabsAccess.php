<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BunkerlabsAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.login')->withErrors([
                'token' => 'Necesitas un token válido para acceder.'
            ]);
        }
        return $next($request);
    }
}
