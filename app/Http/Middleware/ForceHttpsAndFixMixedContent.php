<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class ForceHttpsAndFixMixedContent
{
    public function handle(Request $request, Closure $next)
    {
        // --- Detectar HTTPS correctamente ---
        $isHttps = $request->secure() || $request->header('X-Forwarded-Proto') === 'https';

        // Forzar HTTPS en URLs generadas por Laravel
        if ($isHttps) {
            URL::forceScheme('https');
        }

        // --- Configurar cookies y sesiones seguras ---
        Config::set('session.secure', $isHttps);
        Config::set('session.same_site', 'lax'); // o 'strict' según tu necesidad
        Config::set('sanctum.secure_cookie', $isHttps);

        // --- Log completo para depuración ---
        Log::info('========== ForceHttpsAndFixMixedContent ==========');
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Scheme Laravel detecta: ' . $request->getScheme());
        Log::info('X-Forwarded-Proto header: ' . $request->header('X-Forwarded-Proto'));
        Log::info('Es HTTPS?: ' . ($isHttps ? 'true' : 'false'));
        Log::info('Cabeceras recibidas:', $request->headers->all());

        $response = $next($request);

        // --- Reescribir recursos HTTP a HTTPS en HTML para prevenir Mixed Content ---
        if ($response->headers->get('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')) {

            $content = $response->getContent();

            // Cambiar recursos de HTTP a HTTPS
            $content = preg_replace(
                '#(http://(www\.)?pagina-prueba\.com)#i',
                'https://pagina-prueba.com',
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}
