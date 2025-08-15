<?php

namespace App\Livewire\Admin\Branches;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Edit extends Component
{
    public $branchId;
    public $nombre;
    public $direccion;
    public $ciudad;
    public $telefono;

    // Propiedad para la notificación
    public ?array $notification = null;

    public function mount($id)
    {
        $this->branchId = $id;
        $this->loadBranch();
    }

    private function loadBranch()
    {
        $response = Http::orderApi()->get("/sucursales/{$this->branchId}");

        if ($response->successful()) {
            $branch = $response->json()['data'];
            $this->nombre = $branch['nombre'] ?? '';
            $this->direccion = $branch['direccion'] ?? '';
            $this->ciudad = $branch['ciudad'] ?? '';
            $this->telefono = $branch['telefono'] ?? '';
        } else {
            session()->flash('error', 'No se pudo cargar la información de la sucursal.');
            return redirect()->route('admin.branches.index');
        }
    }

    public function updateBranch()
    {
        try {
            $this->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:10',
            ],
            [
                'nombre.required' => 'El nombre de la sucursal es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
                'ciudad.required' => 'La ciudad es obligatoria.',
            ]);
        } catch (ValidationException $e) {
            $errorMessage = array_values($e->errors())[0][0];
            $this->showNotification($errorMessage, 'error');
            throw $e;
        }

        $response = Http::orderApi()->put("/sucursales/{$this->branchId}", [
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
        ]);

        if ($response->successful()) {
            $this->showNotification('Cambios guardados exitosamente.', 'success');
            sleep(2);
            return redirect()->route('admin.branches.index');
        } else {
            $errorMessage = $response->json('message', 'Error al actualizar la sucursal.');
            $this->showNotification($errorMessage, 'error');
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
        return view('livewire.admin.branches.edit')->layout('layouts.app');
    }
}
