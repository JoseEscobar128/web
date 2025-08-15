<?php
namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public ?string $recaptchaToken = null;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
            'recaptchaToken' => 'required',
        ], [
            'recaptchaToken.required' => 'Por favor, verifica que no eres un robot.'
        ]);

        // Aseguramos que el login se haga a la API 1
        $response = Http::authApi()->post('/api/v1/usuarios/login', [
            'email' => $this->email,
            'contrasena' => $this->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['access_token'];
            $user_data = $data['user'];

            // 💡 Acción clave: Almacenamos el token Y los datos del usuario en la sesión
            session()->put('access_token', $token);
            session()->put('user', $user_data);

            Log::info('Login federado exitoso. Datos de usuario guardados en sesión.', ['user' => $user_data]);

            // Redireccionamos al dashboard o donde corresponda
            return redirect()->intended(route('dashboard'));
        } else {
            Log::warning('Intento de login fallido', ['email' => $this->email, 'status' => $response->status(), 'body' => $response->body()]);
            $errorMessage = $response->json('message', 'Ocurrió un error inesperado.');
            $this->addError('email', $errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}