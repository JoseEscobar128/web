<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // ... (Este método no necesita cambios)
        $state = $this->fixedState;
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
        Log::debug('Inicio callback OAuth', [ /* ... */ ]);

        if ($request->state !== $this->fixedState) {
            Log::error('Error de estado: No coincide con state fijo', [ /* ... */ ]);
            return redirect('/')->with('error', 'Estado inválido. Intente nuevamente.');
        }

        // --- INICIO DE LA SECCIÓN CORREGIDA ---

        // 1. Intercambiar código por token (Restauramos los parámetros)
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
            Log::error('Error al obtener token', [ /* ... */ ]);
            return redirect('/')->with('error', 'Error al autenticar. Intente nuevamente.');
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];

        // 2. Obtener información del usuario (Restauramos los parámetros)
        $userResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get(config('services.auth_api.base_url').'/usuarios/me');

        if ($userResponse->failed()) {
            Log::error('Error al obtener perfil', [ /* ... */ ]);
            return redirect('/')->with('error', 'Error al cargar su perfil.');
        }

        $userData = $userResponse->json()['data'];

        // --- FIN DE LA SECCIÓN CORREGIDA ---

        // 3. Sincronizar y autenticar al usuario local
        $localUser = User::updateOrCreate(
            ['api_id' => $userData['id']],
            [
                'name' => $userData['usuario'],
                'email' => $userData['email'],
                'password' => bcrypt(str()->random(16)), 
            ]
        );

        Auth::login($localUser);

        $request->session()->regenerate();
        $request->session()->put('access_token', $accessToken);

        Log::info('Autenticación y login local exitosos', ['user_id' => $localUser->id]);

        return redirect()->intended('/dashboard');
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        // ... (Este método no necesita cambios)
        $token = $request->session()->get('access_token');
        try {
            Http::withToken($token)->post(config('services.auth_api.base_url').'/usuarios/logout');
        } catch (\Exception $e) { /* ... */ }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Ha cerrado sesión correctamente.');
    }
}
