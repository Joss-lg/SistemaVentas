<?php

use App\Http\Middleware\SoloAdmin;
use App\Http\Middleware\VerificarPermiso;
use \App\Http\Middleware\VerificarCajaAbierta;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'soloAdmin' => SoloAdmin::class,
            'permiso' => VerificarPermiso::class,
            'caja.abierta' => VerificarCajaAbierta::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();