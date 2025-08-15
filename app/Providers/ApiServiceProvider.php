<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Mensajero para la API de Autenticación y Usuarios
        Http::macro('authApi', function () {
            return Http::baseUrl(config('services.auth_api.base_url')) // <-- CAMBIO: Usa config()
                       ->withToken(session('access_token'))
                       ->acceptJson();
        });

        // 2. Mensajero para la API de Órdenes y Productos
        Http::macro('orderApi', function () {
            return Http::baseUrl(config('services.order_api.base_url')) // <-- CAMBIO: Usa config()
                       ->withToken(session('access_token'))
                       ->acceptJson();
        });
    }
}