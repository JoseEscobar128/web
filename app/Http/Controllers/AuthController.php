<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Asegúrate de que esté importado
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

        return redirect(config('services.auth_api.base_url') . '/oauth/authorize?' . $query);
    }

    /**
     * Recibe al usuario de vuelta desde la API y canjea el código por un token.
     */
    public function handleCallback(Request $request)
    {
        // LOG: Inicio del proceso de callback
        Log::info('[OAuth Callback] Se ha recibido la redirección desde la API.', ['query' => $request->all()]);

        $state = $request->session()->pull('state');
        if (! (strlen($state) > 0 && $state === $request->state)) {
            // LOG: Error de estado
            Log::error('[OAuth Callback] El estado (state) no es válido o no coincide.', [
                'session_state' => $state,
                'request_state' => $request->state
            ]);
            return redirect('/')->with('error', 'Estado inválido.');
        }

        // LOG: El estado es válido
        Log::info('[OAuth Callback] El estado (state) es válido. Procediendo a solicitar el token de acceso.');

        $tokenEndpoint = config('services.auth_api.base_url') . '/oauth2/token';
        
        $payload = [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.oauth.client_id'),
            'client_secret' => config('services.oauth.client_secret'),
            'redirect_uri'  => config('services.oauth.redirect_uri'),
            'code'          => $request->code,
        ];

        // LOG: Detalles de la petición para obtener el token (sin el secreto)
        Log::info('[OAuth Callback] Enviando petición POST a: ' . $tokenEndpoint, [
            'payload' => collect($payload)->except('client_secret')->all()
        ]);

        $tokenResponse = Http::withoutVerifying()
                             ->asForm()
                             ->post($tokenEndpoint, $payload);

        if ($tokenResponse->failed()) {
            // LOG: La petición del token falló
            Log::error('[OAuth Callback] La petición para obtener el token de acceso falló.', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->body()
            ]);
            return redirect('/')->with('error', 'No se pudo obtener el token de acceso desde la API.');
        }
        
        // LOG: La petición del token fue exitosa
        Log::info('[OAuth Callback] Token de acceso obtenido correctamente.');

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];

        $profileEndpoint = config('services.auth_api.base_url') . '/usuarios/me';
        
        // LOG: Solicitando perfil del usuario
        Log::info('[OAuth Callback] Solicitando perfil del usuario desde: ' . $profileEndpoint);

        $userResponse = Http::withToken($accessToken)
                            ->acceptJson()
                            ->get($profileEndpoint);

        if ($userResponse->failed()) {
            // LOG: La petición del perfil falló
            Log::error('[OAuth Callback] La petición para obtener el perfil del usuario falló.', [
                'status' => $userResponse->status(),
                'body' => $userResponse->body()
            ]);
            return redirect('/')->with('error', 'No se pudieron obtener los datos del perfil desde la API.');
        }

        // LOG: El perfil del usuario se obtuvo correctamente
        $userData = $userResponse->json()['data'];
        Log::info('[OAuth Callback] Perfil de usuario obtenido.', ['usuario' => $userData['usuario'] ?? null]);

        $request->session()->regenerate();

        session([
            'access_token' => $accessToken,
            'user'         => (object) $userData
        ]);

        // LOG: Sesión creada, redirigiendo al dashboard
        Log::info('[OAuth Callback] Sesión creada. Redirigiendo al dashboard.');

        return redirect()->intended('/web/web/dashboard');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        $token = $request->session()->get('access_token');
        
        $logoutEndpoint = config('services.auth_api.base_url') . '/usuarios/logout';
        Http::withToken($token)->post($logoutEndpoint);

        $request->session()->flush();

        return redirect()->route('home')->with('success', 'Has cerrado sesión correctamente.');
    }
}
