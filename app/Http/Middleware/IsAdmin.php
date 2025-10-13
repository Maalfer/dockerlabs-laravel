<?php

namespace App\Http\Middleware;  // Verifica que esté usando este namespace

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->name === 'admin') {
            return $next($request);
        }

        return redirect('/');
    }
}
