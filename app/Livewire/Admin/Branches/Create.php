<?php

namespace App\Livewire\Admin\Branches;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Create extends Component
{
    public $nombre = '';
    public $direccion = '';
    public $ciudad = '';
    public $telefono = '';

    // Propiedad para la notificación
    public ?array $notification = null;

    public function saveBranch()
    {
        try {
            $this->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:10',
            ],
            // Mensajes de validación en español
            [
                'nombre.required' => 'El nombre de la sucursal es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
                'ciudad.required' => 'La ciudad es obligatoria.',
                'telefono.max' => 'El teléfono no debe exceder los 10 dígitos.',
            ]);

        } catch (ValidationException $e) {
            $errors = $e->errors();
            if (count($errors) > 1) {
                $this->showNotification('Te faltan campos obligatorios por llenar.', 'error');
            } else {
                $errorMessage = array_values($errors)[0][0];
                $this->showNotification($errorMessage, 'error');
            }
            throw $e;
        }

        $response = Http::orderApi()->post('/sucursales', [
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
        ]);

        if ($response->successful()) {
            $this->showNotification('Sucursal registrada exitosamente.', 'success');
            sleep(2); // Pausa para que se vea la notificación
            return redirect()->route('admin.branches.index');
        } else {
            $this->showNotification('Error de la API: ' . $response->json('message', ''), 'error');
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
        return view('livewire.admin.branches.create')->layout('layouts.app');
    }
}