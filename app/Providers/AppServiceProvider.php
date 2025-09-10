<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        // Evita errores de longitud de índice en MySQL antiguos (utf8mb4)
        Schema::defaultStringLength(191);

        // Fuerza HTTPS en producción (útil si despliegas con https)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Comparte el usuario autenticado con todas las vistas como $authUser
        View::composer('*', function ($view) {
            $view->with('authUser', Auth::user());
        });
    }
}
