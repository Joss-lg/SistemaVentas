<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
{
    // Usamos la función esAdmin() que creamos en el modelo Usuario
    if (auth()->check() && auth()->user()->esAdmin()) {
        return $next($request);
    }

    // Si no es admin, lo mandamos a la pantalla de ventas con un error
    return redirect('/ventas')->with('error', 'No tienes permisos para acceder al Dashboard.');
}
}