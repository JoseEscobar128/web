<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Redirige al usuario a la API para que inicie el proceso de autorización.
     */
    public function redirectToProvider(Request $request)

    {
        
        $request->session()->put('state', $state = Str::random(40));
        

        $query = http_build_query([
            'client_id'     => config('services.oauth.client_id'),
            'redirect_uri'  => config('services.oauth.redirect_uri'),
            'response_type' => 'code',
            'state'         => $state,
        ]);

        // Esta URL ya estaba correcta, la dejamos como está.
        return redirect(config('services.auth_api.base_url') . '/oauth/authorize?' . $query);
    }

    /**
     * Recibe al usuario de vuelta desde la API y canjea el código por un token.
     */
    public function handleCallback(Request $request)
    {
        $state = $request->session()->pull('state');
        if (! (strlen($state) > 0 && $state === $request->state)) {
            return redirect('/')->with('error', 'Estado inválido.');
        }

        // CORRECCIÓN: Se quita '/api/v1/' duplicado.
        $tokenEndpoint = config('services.auth_api.base_url') . '/oauth2/token';
        
        $tokenResponse = Http::withoutVerifying()
                             ->asForm()
                             ->post($tokenEndpoint, [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.oauth.client_id'),
            'client_secret' => config('services.oauth.client_secret'),
            'redirect_uri'  => config('services.oauth.redirect_uri'),
            'code'          => $request->code,
        ]);

        if ($tokenResponse->failed()) {
            // Ya no necesitamos el dd(), puedes comentarlo o borrarlo.
            // dd($tokenResponse->status(), $tokenResponse->json(), $tokenResponse->body()); 
            return redirect('/')->with('error', 'No se pudo obtener el token de acceso desde la API.');
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];

        // CORRECCIÓN: Se quita '/api/v1/' duplicado.
        $profileEndpoint = config('services.auth_api.base_url') . '/usuarios/me';

        $userResponse = Http::withToken($accessToken)
                            ->acceptJson()
                            ->get($profileEndpoint);

        if ($userResponse->failed()) {
            return redirect('/')->with('error', 'No se pudieron obtener los datos del perfil desde la API.');
        }

        $userData = $userResponse->json()['data'];

        $request->session()->regenerate();

        session([
            'access_token' => $accessToken,
            'user'         => (object) $userData
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        $token = $request->session()->get('access_token');
        
        // CORRECCIÓN: Se quita '/api/v1/' duplicado.
        $logoutEndpoint = config('services.auth_api.base_url') . '/usuarios/logout';

        Http::withToken($token)->post($logoutEndpoint);

        $request->session()->flush();

        return redirect()->route('home')->with('success', 'Has cerrado sesión correctamente.');
    }
}