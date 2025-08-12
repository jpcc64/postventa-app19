<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SapService; // <-- Importa la clase

class SapServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registra SapService como un 'singleton'. Laravel creará una sola
        // instancia de este servicio por cada petición.
        $this->app->singleton(SapService::class, function ($app) {
            return new SapService();
        });
    }

    // ...
}