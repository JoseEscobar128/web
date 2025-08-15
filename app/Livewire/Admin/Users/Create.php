<?php

namespace App\Livewire\Admin\Users;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Create extends Component
{
    // Propiedades para los datos del formulario
    public $usuario;
    public $email;
    public $role_id;
    public $password;
    public $password_confirmation;
    public $status = 1;
    public $empleado_id;

    // Propiedades para llenar los <select>
    public array $roles = [];
    public array $employees = [];

    // Propiedad para la notificación
    public ?array $notification = null;

    /**
     * Define las reglas de validación para el componente.
     */
    protected function rules()
    {
        return [
            'usuario' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required|integer',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|boolean',
            'empleado_id' => 'nullable|integer',
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
            'role_id.required' => 'Debes seleccionar un rol.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    public function mount()
    {
        $this->loadRolesAndEmployees();
    }

    /**
     * Este método se ejecuta automáticamente cada vez que una propiedad
     * con wire:model.live cambia, limpiando los errores en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveUser()
    {
        try {
            // Ahora simplemente llamamos a validate(), que usará los métodos rules() y messages().
            $this->validate();

        } catch (ValidationException $e) {
            $errors = $e->errors();
            if (count($errors) > 1) {
                $this->showNotification('Te faltan campos obligatorios.', 'error');
            } else {
                $errorMessage = array_values($errors)[0][0];
                $this->showNotification($errorMessage, 'error');
            }
            // Re-lanzamos la excepción para que Livewire muestre los errores en los campos.
            throw $e;
        }

        $payload = [
            'usuario' => $this->usuario,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'contrasena' => $this->password,
            'esta_activo' => $this->status,
            'empleado_id' => $this->empleado_id,
        ];
        
        $response = Http::authApi()->post('/usuarios/register', $payload);

        if ($response->successful()) {
            session()->flash('success', 'Usuario creado correctamente.');
            return redirect()->route('admin.users.index');
        } else {
            $errorMessage = $response->json('message', 'Ocurrió un error inesperado.');
            $this->showNotification('Error al crear usuario: ' . $errorMessage, 'error');
        }
    }

    private function loadRolesAndEmployees()
    {
        // Cargar Roles
        $rolesResponse = Http::authApi()->get('/roles');
        if ($rolesResponse->successful()) {
            $filteredRoles = collect($rolesResponse->json()['data'])->filter(function ($role) {
                return !in_array($role['name'], ['SUPERADMIN', 'ADMIN_SUC', 'CLIENTE']);
            });
            $this->roles = $filteredRoles->values()->all();
        }

        // Cargar Empleados
        $employeesResponse = Http::authApi()->get('/empleados');
        if ($employeesResponse->successful()) {
            $this->employees = $employeesResponse->json()['data'];
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
        return view('livewire.admin.users.create')->layout('layouts.app');
    }
}