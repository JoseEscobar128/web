<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // La lógica principal no cambia: si no hay token, no hay acceso.
        if (!session()->has('access_token')) {

            // ¡LA MEJORA ESTÁ AQUÍ!
            // Verificamos si la petición viene de Livewire (una navegación en segundo plano).
            if ($request->header('X-Livewire')) {
                // Si es así, en lugar de una redirección normal, enviamos una respuesta
                // que le dice al navegador que haga una recarga completa a la página de inicio.
                // Esto "rompe" el ciclo de Livewire de forma segura y evita el error.
                return response('<script>window.location.href = "'.route('home').'";</script>');
            }

            // Si no es una petición de Livewire, hacemos la redirección normal.
            return redirect()->route('home')->with('error', 'Por favor, inicia sesión para continuar.');
        }

        // Si la sesión sí tiene el token, permite que la petición continúe.
        return $next($request);
    }
}
