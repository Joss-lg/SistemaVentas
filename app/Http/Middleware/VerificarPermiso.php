<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next, $permisoRequerido)
    {
        // Si es admin, pasa siempre (mismo criterio que SoloAdmin)
        if (auth()->check() && auth()->user()->esAdmin()) {
            return $next($request);
        }

        $permisos = is_array(auth()->user()->permisos)
            ? auth()->user()->permisos
            : json_decode(auth()->user()->permisos ?? '[]', true) ?? [];

        if (in_array($permisoRequerido, $permisos)) {
            return $next($request);
        }

        return redirect('/ventas')->with('error', 'No tienes permisos para realizar esta acción.');
    }
}