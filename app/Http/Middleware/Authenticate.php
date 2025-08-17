<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si la petición no espera una respuesta JSON (es decir, es un navegador),
        // la redirigimos al inicio del flujo de autenticación de nuestra API.
        if (! $request->expectsJson()) {
            
            // Construimos la URL de login de la API con los parámetros necesarios
            $queryParams = http_build_query([
                'client_id'     => env('OAUTH_CLIENT_ID', 'web1'),
                'redirect_uri'  => env('OAUTH_REDIRECT_URI', 'https://pagina-prueba.com/web/callback'),
                'response_type' => 'code', // Estándar para el flujo OAuth 2.0
                'scope'         => '', // Puedes añadir scopes si los necesitas
                'state'         => 'web_state_fijo', // El estado que mencionaste
            ]);

            // Devolvemos la URL completa a la API de autenticación
            return env('AUTH_API_URL', 'https://pagina-prueba.com/api/v1') . '/login-cliente?' . $queryParams;
        }

        return null;
    }
}