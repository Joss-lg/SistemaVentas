<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        if (!Auth::user()->tienePermiso($slug)) {
            abort(403, 'No tienes permiso para esto.');
        }

        return $next($request);
    }
}