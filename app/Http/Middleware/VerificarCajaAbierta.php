<?php

namespace App\Http\Middleware;

use App\Models\CorteCaja;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarCajaAbierta
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = auth()->user();

        if ($usuario->esAdmin()) {
            return $next($request);
        }

        if (!CorteCaja::turnoActivo($usuario->id)) {
            return redirect()->route('caja.apertura')
                ->with('error', 'Debes abrir caja antes de continuar.');
        }

        return $next($request);
    }
}