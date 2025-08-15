<?php

namespace App\Livewire\Admin\Users;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    public $userId;
    public $usuario;
    public $email;
    public $role_id;
    public $status;
    public $password;
    public $password_confirmation;

    // --- NUEVA PROPIEDAD PARA EL NOMBRE DEL ROL ---
    public ?string $role_name = null;

    public array $roles = [];
    public ?array $notification = null;

    protected function rules()
    {
        return [
            'usuario' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required|integer',
            'status' => 'required|boolean',
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    protected function messages()
    {
        return [
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo no es válido.',
            'role_id.required' => 'Debes seleccionar un rol.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    public function mount($id)
    {
        $this->userId = $id;
        $this->loadRoles();

        $response = Http::authApi()->get("/usuarios/{$this->userId}");

        if ($response->successful()) {
            $user = $response->json()['data'];
            $this->usuario = $user['usuario'] ?? '';
            $this->email = $user['email'] ?? '';
            $this->role_id = $user['role_id'] ?? null;
            $this->status = $user['esta_activo'] ?? 1;
            // --- GUARDAMOS EL NOMBRE DEL ROL ---
            $this->role_name = $user['rol'] ?? null;
        } else {
            session()->flash('error', 'No se pudo cargar la información del usuario.');
            return redirect()->route('admin.users.index');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateUser()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errors = $e->errors();
            if (count($errors) > 1) {
                $this->showNotification('Te faltan campos obligatorios.', 'error');
            } else {
                $errorMessage = array_values($errors)[0][0];
                $this->showNotification($errorMessage, 'error');
            }
            throw $e;
        }

        $payload = [
            'usuario' => $this->usuario,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'esta_activo' => $this->status,
        ];
        
        if (!empty($this->password)) {
            $payload['contrasena'] = $this->password;
        }

        $response = Http::authApi()->put("/usuarios/{$this->userId}", $payload);

        if ($response->successful()) {
            session()->flash('success', 'Usuario actualizado correctamente.');
            return redirect()->route('admin.users.index');
        } else {
            $this->showNotification('Error al actualizar usuario: ' . $response->json('message', ''), 'error');
        }
    }

    private function loadRoles()
    {
        $rolesResponse = Http::authApi()->get('/roles');
        if ($rolesResponse->successful()) {
            $this->roles = collect($rolesResponse->json()['data'])->filter(function ($role) {
                return !in_array($role['name'], ['SUPERADMIN', 'CLIENTE', 'ADMIN_SUC']);
            })->all();
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
        return view('livewire.admin.users.edit')->layout('layouts.app');
    }
}
