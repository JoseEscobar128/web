<?php

namespace App\Livewire\Admin\Categories;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Create extends Component
{
    public $nombre = '';
    public $descripcion = '';

    // Propiedad para la notificación
    public ?array $notification = null;

    /**
     * Define las reglas de validación para el componente.
     */
    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    /**
     * Define los mensajes de error personalizados.
     */
    protected function messages()
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
        ];
    }

    /**
     * Valida en tiempo real cuando una propiedad cambia.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveCategory()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errorMessage = array_values($e->errors())[0][0];
            $this->showNotification($errorMessage, 'error');
            throw $e;
        }

        $response = Http::orderApi()->post('/categorias', [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        if ($response->successful()) {
            session()->flash('success', 'Categoría creada exitosamente.');
            return redirect()->route('admin.categories.index');
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
        return view('livewire.admin.categories.create')->layout('layouts.app');
    }
}