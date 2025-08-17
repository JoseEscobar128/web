<?php

namespace App\Http\Controllers;

use App\Models\User; // <-- 1. IMPORTANTE: Añade el modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- 2. IMPORTANTE: Añade la fachada Auth
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
        // ... (Este método ya estaba bien, no necesita cambios)
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
        // ... (Toda tu lógica de validación de state y obtención de token se queda igual)
        Log::debug('Inicio callback OAuth', [ /* ... */ ]);
        if ($request->state !== $this->fixedState) { /* ... */ }
        $tokenResponse = Http::withoutVerifying()->asForm()->post(/* ... */);
        if ($tokenResponse->failed()) { /* ... */ }
        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];
        $userResponse = Http::withToken($accessToken)->acceptJson()->get(/* ... */);
        if ($userResponse->failed()) { /* ... */ }
        $userData = $userResponse->json()['data'];


        // ------------------------------------------------------------------
        // --- INICIO DE LA SECCIÓN CORREGIDA Y DEFINITIVA ---
        // ------------------------------------------------------------------

        // 3. SINCRONIZAR CON LA BASE DE DATOS LOCAL
        // Buscamos un usuario local que coincida con el ID de la API.
        // Si no existe, lo creamos. Si ya existe, actualizamos sus datos.
        $localUser = User::updateOrCreate(
            ['api_id' => $userData['id']], // Condición para buscar
            [
                'name' => $userData['usuario'], // Datos para crear o actualizar
                'email' => $userData['email'],
                // Laravel necesita un password, podemos poner uno aleatorio y seguro.
                'password' => bcrypt(str()->random(16)), 
            ]
        );

        // 4. ¡EL PASO CLAVE!
        // Le decimos al sistema de autenticación de Laravel que este usuario
        // ha iniciado sesión oficialmente. Esto es lo que el middleware 'auth' revisa.
        Auth::login($localUser);

        // ------------------------------------------------------------------
        // --- FIN DE LA SECCIÓN CORREGIDA ---
        // ------------------------------------------------------------------

        // Regenerar ID de sesión por seguridad
        $request->session()->regenerate();

        // Guardamos solo el token en la sesión, ya que Laravel ahora gestiona al usuario.
        $request->session()->put('access_token', $accessToken);

        Log::info('Autenticación y login local exitosos', [
            'user_id' => $localUser->id,
            'session_id' => session()->getId()
        ]);

        // Redirigimos al dashboard. Ahora el middleware 'auth' lo reconocerá.
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
            Log::error('Error al cerrar sesión en API', ['error' => $e->getMessage()]);
        }

        // 5. Usamos el método oficial de Laravel para cerrar la sesión
        Auth::logout();
        
        // Invalidamos la sesión y regeneramos el token CSRF por seguridad
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Ha cerrado sesión correctamente.');
    }
}1~<<<<?php

namespace App\Http\Controllers;

use App\Models\User; // <-- 1. IMPORTANTE: Añade el modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- 2. IMPORTANTE: Añade la fachada Auth
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
        // ... (Este método ya estaba bien, no necesita cambios)
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
        // ... (Toda tu lógica de validación de state y obtención de token se queda igual)
        Log::debug('Inicio callback OAuth', [ /* ... */ ]);
        if ($request->state !== $this->fixedState) { /* ... */ }
        $tokenResponse = Http::withoutVerifying()->asForm()->post(/* ... */);
        if ($tokenResponse->failed()) { /* ... */ }
        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];
        $userResponse = Http::withToken($accessToken)->acceptJson()->get(/* ... */);
        if ($userResponse->failed()) { /* ... */ }
        $userData = $userResponse->json()['data'];


        // ------------------------------------------------------------------
        // --- INICIO DE LA SECCIÓN CORREGIDA Y DEFINITIVA ---
        // ------------------------------------------------------------------

        // 3. SINCRONIZAR CON LA BASE DE DATOS LOCAL
        // Buscamos un usuario local que coincida con el ID de la API.
        // Si no existe, lo creamos. Si ya existe, actualizamos sus datos.
        $localUser = User::updateOrCreate(
            ['api_id' => $userData['id']], // Condición para buscar
            [
                'name' => $userData['usuario'], // Datos para crear o actualizar
                'email' => $userData['email'],
                // Laravel necesita un password, podemos poner uno aleatorio y seguro.
                'password' => bcrypt(str()->random(16)), 
            ]
        );

        // 4. ¡EL PASO CLAVE!
        // Le decimos al sistema de autenticación de Laravel que este usuario
        // ha iniciado sesión oficialmente. Esto es lo que el middleware 'auth' revisa.
        Auth::login($localUser);

        // ------------------------------------------------------------------
        // --- FIN DE LA SECCIÓN CORREGIDA ---
        // ------------------------------------------------------------------

        // Regenerar ID de sesión por seguridad
        $request->session()->regenerate();

        // Guardamos solo el token en la sesión, ya que Laravel ahora gestiona al usuario.
        $request->session()->put('access_token', $accessToken);

        Log::info('Autenticación y login local exitosos', [
            'user_id' => $localUser->id,
            'session_id' => session()->getId()
        ]);

        // Redirigimos al dashboard. Ahora el middleware 'auth' lo reconocerá.
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
            Log::error('Error al cerrar sesión en API', ['error' => $e->getMessage()]);
        }

        // 5. Usamos el método oficial de Laravel para cerrar la sesión
        Auth::logout();
        
        // Invalidamos la sesión y regeneramos el token CSRF por seguridad
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Ha cerrado sesión correctamente.');
    }
}
