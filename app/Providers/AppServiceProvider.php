<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// --- IMPORTACIONES PARA FORZAR LA CONEXIÓN DE LIVEWIRE ---
use Livewire\Livewire;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\VerifyOtp;
use Illuminate\Support\Facades\URL; // <-- Agregado

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
        // --- REGISTRO MANUAL DE LOS COMPONENTES ---
        // Esto le dice a Laravel exactamente dónde encontrar la lógica para cada vista.
        Livewire::component('auth.login', Login::class);
        Livewire::component('auth.verify-otp', VerifyOtp::class);

        // --- FORZAR HTTPS EN PRODUCCIÓN ---
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
