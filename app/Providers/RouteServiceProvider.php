<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SuperadminOnly;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Alias para middleware personalizado
        Route::aliasMiddleware('superadmin', SuperadminOnly::class);
    }
}
