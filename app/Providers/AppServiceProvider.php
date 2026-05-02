<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

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
    
        // Forzamos al modelo de tokens a usar la conexión que definimos en database.php
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        
        // Le indicamos que el modelo de tokens debe usar la base de datos de auth
        $tokenModel = new PersonalAccessToken();
        $tokenModel->setConnection('mysql_auth');

    }
}
