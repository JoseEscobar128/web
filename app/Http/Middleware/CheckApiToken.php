<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('access_token')) {
            // Si no hay token, lo redirigimos a la página de inicio
            return redirect()->route('home')->with('error', 'Necesitas iniciar sesión.');
        }

        // Si hay token, dejamos que la petición continúe
        return $next($request);
    }
}
