<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Valor fijo del state
    private $fixedState = 'web_state_fijo';

    /**
     * Redirige al proveedor OAuth
     */
    public function redirectToProvider(Request $request)
    {
        $state = $this->fixedState;

        Log::debug('Inicio flujo OAuth', [
            'generated_state' => $state,
            'session_id' => session()->getId(),
            'session_data' => $request->session()->all()
        ]);

        $query = http_build_query([
            'client_id' => config('services.oauth.client_id'),
            'redirect_uri' => config('services.oauth.redirect_uri'),
            'response_type' => 'code',
            'state' => $state,
            'prompt' => 'login'
        ]);

        return redirect(config('services.auth_api.base_url').'/oauth/authorize?'.$query);
    }

    /**
     * Maneja el callback del proveedor OAuth
     */
    public function handleCallback(Request $request)
    {
        Log::debug('Inicio callback OAuth', [
            'received_state' => $request->state,
            'session_id' => session()->getId(),
            'session_data' => $request->session()->all()
        ]);

        // Validar state fijo
        if ($request->state !== $this->fixedState) {
            Log::error('Error de estado: No coincide con state fijo', [
                'received_state' => $request->state
            ]);
            return redirect('/')->with('error', 'Estado inválido. Intente nuevamente.');
        }

        // Intercambiar código por token
        $tokenResponse = Http::withoutVerifying()
            ->asForm()
            ->post(config('services.auth_api.base_url').'/oauth2/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.oauth.client_id'),
                'client_secret' => config('services.oauth.client_secret'),
                'redirect_uri' => config('services.oauth.redirect_uri'),
                'code' => $request->code,
            ]);

        if ($tokenResponse->failed()) {
            Log::error('Error al obtener token', [
                'status' => $tokenResponse->status(),
                'response' => $tokenResponse->body()
            ]);
            return redirect('/')->with('error', 'Error al autenticar. Intente nuevamente.');
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];

        // Obtener información del usuario
        $userResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get(config('services.auth_api.base_url').'/usuarios/me');

        if ($userResponse->failed()) {
            Log::error('Error al obtener perfil', [
                'status' => $userResponse->status(),
                'response' => $userResponse->body()
            ]);
            return redirect('/')->with('error', 'Error al cargar su perfil.');
        }

        $userData = $userResponse->json()['data'];

        // Regenerar ID de sesión por seguridad
        $request->session()->regenerate();

        // Guardar datos de usuario en sesión
        $request->session()->put([
            'access_token' => $accessToken,
            'user' => (object) $userData
        ]);

        Log::info('Autenticación exitosa', [
            'user_id' => $userData['id'] ?? null,
            'session_id' => session()->getId()
        ]);

        return redirect()->intended('/dashboard');
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        $token = $request->session()->get('access_token');

        try {
            Http::withToken($token)
                ->post(config('services.auth_api.base_url').'/usuarios/logout');
        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión en API', [
                'error' => $e->getMessage()
            ]);
        }

        // Limpiar sesión local
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Ha cerrado sesión correctamente.');
    }
}
