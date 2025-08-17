<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ProfilePage extends Component
{
    public $userId;
    public string $usuario = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Propiedad para la notificación
    public ?array $notification = null;

    /**
     * Define las reglas de validación para todos los campos del componente.
     */
    protected function rules()
    {
        return [
            'usuario' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // La contraseña es opcional, pero si se escribe, debe cumplir las reglas.
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    /**
     * Define los mensajes de error personalizados.
     */
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

    public function mount()
    {
        if (!session()->has('access_token')) {
            return redirect()->route('home')->with('error', 'Tu sesión ha expirado.');
        }

        $response = Http::authApi()->get('/usuarios/me');

        if ($response->failed()) {
            return redirect()->route('home')->with('error', 'No se pudo obtener la información del usuario.');
        }

        // 👇 aquí extraes el objeto de usuario
        $userData = $response->json('data');

        // Guardar en propiedades
        $this->userId  = $userData['id'];
        $this->usuario = $userData['usuario'];
        $this->email   = $userData['email'];

        // También actualizar la sesión si quieres usarla en otras partes
        session(['user' => (object) $userData]);
    }



    /**
     * Valida en tiempo real cuando una propiedad cambia.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateProfileInformation()
    {
        try {
            // Validamos solo los campos de este formulario
            $this->validate([
                'usuario' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            $this->showNotification(array_values($e->errors())[0][0], 'error');
            throw $e;
        }

        $response = Http::authApi()->put("/usuarios/{$this->userId}", [
            'usuario' => $this->usuario,
            'email' => $this->email,
        ]);

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
            // Validamos solo los campos de este formulario
            $this->validate([
                'password' => 'required|min:8|confirmed',
            ]);
        } catch (ValidationException $e) {
            $this->showNotification(array_values($e->errors())[0][0], 'error');
            throw $e;
        }

        $response = Http::authApi()->put("/usuarios/{$this->userId}", [
            'contrasena' => $this->password,
        ]);

        if ($response->successful()) {
            $this->showNotification('Contraseña actualizada correctamente.', 'success');
            $this->reset(['password', 'password_confirmation']);
        } else {
            $this->showNotification('Error al actualizar la contraseña: ' . $response->json('message', ''), 'error');
        }
    }

    public function showNotification(string $message, string $type = 'success')
    {
        $this->notification = ['message' => $message, 'type' => $type];
    }
    
    public function resetNotification()
    {
        $this->notification = null;
    }

    public function render()
    {
        return view('livewire.profile-page')->layout('layouts.app');
    }
}