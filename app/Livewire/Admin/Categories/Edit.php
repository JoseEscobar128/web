<?php

namespace App\Livewire\Admin\Categories;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Edit extends Component
{
    public $categoryId;
    public $nombre;
    public $descripcion;

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

    public function mount($id)
    {
        $this->categoryId = $id;
        $this->loadCategory();
    }

    private function loadCategory()
    {
        $response = Http::orderApi()->get("/categorias/{$this->categoryId}");

        if ($response->successful()) {
            $category = $response->json()['data'];
            $this->nombre = $category['nombre'];
            $this->descripcion = $category['descripcion'];
        } else {
            session()->flash('error', 'No se pudo cargar la categoría.');
            return redirect()->route('admin.categories.index');
        }
    }

    /**
     * Valida en tiempo real cuando una propiedad cambia.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateCategory()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errorMessage = array_values($e->errors())[0][0];
            $this->showNotification($errorMessage, 'error');
            throw $e;
        }

        $response = Http::orderApi()->put("/categorias/{$this->categoryId}", [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        if ($response->successful()) {
            session()->flash('success', 'Categoría actualizada exitosamente.');
            return redirect()->route('admin.categories.index');
        } else {
            $errorMessage = $response->json('message', 'Error al actualizar la categoría.');
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
        return view('livewire.admin.categories.edit')->layout('layouts.app');
    }
}