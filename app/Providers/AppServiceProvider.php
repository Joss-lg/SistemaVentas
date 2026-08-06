<?php

namespace App\Providers;

use App\Models\ConfiguracionHardware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bypass global de permisos para usuarios con rol admin o administrador
        Gate::before(function ($user, $ability) {
            $rol = strtolower($user->rol ?? '');
            if ($rol === 'admin' || $rol === 'administrador') {
                return true;
            }
        });

        // Compartir la configuración de hardware en los layouts
        View::composer(['layouts.app', 'layouts.cajero'], function ($view) {
            $view->with('configHardware', ConfiguracionHardware::actual());
        });
    }
}