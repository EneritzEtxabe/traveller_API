<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('es-admin', function ($user){
            return $user->rol ==="admin";
        });
        Gate::define('es-superadmin', function ($user){
            return $user->rol ==="superadmin";
        });
        Gate::define('es-creador-alquiler', function ($user, $alquiler){
            return $user->id === $alquiler->cliente_id;
        });
        Gate::define('es-creador-usuario', function ($user, $tablaUser){
            return $user->id === $tablaUser->id;
        });
    }
}
