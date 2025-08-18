<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfilePage extends Component
{
    public $userId;
    public string $usuario = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?array $notification = null;

    /**
     * Reglas de validación
     */
    protected function rules()
    {
        return [
            'usuario' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    protected function messages()
    {
        return [
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo no es válido.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    /**
     * Se ejecuta al inicializar el componente
     */
    public function mount()
    {
        Log::info('Livewire mount: URL actual -> ' . url()->current());

        if (!session()->has('access_token')) {
            Log::warning('No hay access_token en sesión. Redirigiendo...');
            return redirect()->route('home')->with('error', 'Tu sesión ha expirado.');
        }

        $response = Http::authApi()->get('/usuarios/me');
        Log::info('Respuesta API /usuarios/me', ['status' => $response->status(), 'body' => $response->body()]);

        if ($response->failed()) {
            Log::error('Fallo al obtener info de usuario', ['status' => $response->status()]);
            return redirect()->route('home')->with('error', 'No se pudo obtener la información del usuario.');
        }

        $userData = $response->json('data');
        Log::info('Datos de usuario cargados', ['userData' => $userData]);

        $this->userId  = $userData['id'];
        $this->usuario = $userData['usuario'];
        $this->email   = $userData['email'];

        session(['user' => (object) $userData]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateProfileInformation()
    {
        try {
            $this->validate([
                'usuario' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            $this->showNotification(array_values($e->errors())[0][0], 'error');
            Log::warning('Validación fallida updateProfileInformation', ['errors' => $e->errors()]);
            throw $e;
        }

        $payload = ['usuario' => $this->usuario, 'email' => $this->email];
        Log::info('Intentando actualizar perfil', ['userId' => $this->userId, 'payload' => $payload]);

        $response = Http::authApi()->put("/usuarios/{$this->userId}", $payload);
        Log::info('Respuesta API updateProfileInformation', ['status' => $response->status(), 'body' => $response->body()]);

        if ($response->successful()) {
            $user = session('user');
            $user->usuario = $this->usuario;
            $user->email = $this->email;
            session(['user' => $user]);
            $this->showNotification('Cambios guardados correctamente.', 'success');
        } else {
            $this->showNotification('Error al actualizar el perfil: ' . $response->json('message', ''), 'error');
        }
    }

    public function updatePassword()
    {
        try {
            $this->validate(['password' => 'required|min:8|confirmed']);
        } catch (ValidationException $e) {
            $this->showNotification(array_values($e->errors())[0][0], 'error');
            Log::warning('Validación fallida updatePassword', ['errors' => $e->errors()]);
            throw $e;
        }

        Log::info('Intentando actualizar contraseña para userId: ' . $this->userId);

        $response = Http::authApi()->put("/usuarios/{$this->userId}", ['contrasena' => $this->password]);
        Log::info('Respuesta API updatePassword', ['status' => $response->status(), 'body' => $response->body()]);

        if ($response->successful()) {
            $this->showNotification('Contraseña actualizada correctamente.', 'success');
            $this->reset(['password', 'password_confirmation']);
        } else {
            $this->showNotification('Error al actualizar la contraseña: ' . $response->json('message', ''), 'error');
        }
    }

    public function showNotification(string $message, string $type = 'success')
    {
        Log::info('Notificación', ['type' => $type, 'message' => $message]);
        $this->notification = ['message' => $message, 'type' => $type];
    }

    public function resetNotification()
    {
        $this->notification = null;
    }

    public function render()
    {
        Log::info('Renderizando componente Livewire profile-page');
        return view('livewire.profile-page')->layout('layouts.app');
    }
}
