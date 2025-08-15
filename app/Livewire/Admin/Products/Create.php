<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // <-- AÑADIDO

class Create extends Component
{
    use WithFileUploads;

    // Propiedades del formulario
    public $nombre, $precio, $categoria_id, $descripcion, $sucursal_id;
    public $new_image;

    // Propiedades para los <select>
    public array $categories = [];
    public array $sucursales = [];
    
    // Propiedad para la notificación
    public ?array $notification = null;

    public function mount()
    {
        $this->loadDependencies();
    }

    private function loadDependencies()
    {
        // Cargar Categorías
        $categoriesResponse = Http::orderApi()->get('/categorias');
        if ($categoriesResponse->successful()) {
            $this->categories = $categoriesResponse->json()['data'] ?? [];
        }

        // Cargar Sucursales
        $sucursalesResponse = Http::orderApi()->get('/sucursales');
        if ($sucursalesResponse->successful()) {
            $this->sucursales = $sucursalesResponse->json()['data'] ?? [];
        }
    }

    public function saveProduct()
    {
        try {
            // 1. Validamos todos los campos.
            $this->validate([
                'nombre' => 'required|string|max:150',
                'precio' => 'required|numeric|min:0',
                'categoria_id' => 'required|integer',
                'sucursal_id' => 'required|integer',
                'new_image' => 'required|image|max:2048',
            ], 
            // Mensajes de validación en español
            [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'precio.required' => 'El campo precio es obligatorio.',
                'categoria_id.required' => 'Debes seleccionar una categoría.',
                'sucursal_id.required' => 'Debes seleccionar una sucursal.',
                'new_image.required' => 'Debes cargar una imagen para el producto.',
                'new_image.image' => 'El archivo debe ser una imagen (jpg, png, etc.).',
                'new_image.max' => 'La imagen no debe pesar más de 2MB.',
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

        // ==========================================================
        // ===== INICIO DEL BLOQUE DE CÓDIGO MODIFICADO =============
        // ==========================================================
        
        // 2. Generamos un nombre de archivo limpio y único.
        $extension = $this->new_image->getClientOriginalExtension();
        $filename = Str::slug($this->nombre) . '-' . time() . '.' . $extension;
        
        // 3. Guardamos la imagen en el storage con el nombre personalizado.
        $this->new_image->storeAs('images', $filename, 'public');

        // 4. Preparamos el payload con el formato de array de string.
        $payload = [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'categoria_id' => $this->categoria_id,
            'sucursal_id' => $this->sucursal_id,
            'imagen_principal' => [$filename] // <-- FORMATO CORREGIDO
        ];
        
        // ==========================================================
        // ===== FIN DEL BLOQUE DE CÓDIGO MODIFICADO ================
        // ==========================================================
        
        // 5. Hacemos la petición a la API.
        $response = Http::orderApi()->post('/productos', $payload);

        if ($response->successful()) {
            $this->showNotification('Producto creado exitosamente.', 'success');
            sleep(2);
            return redirect()->route('admin.products.index');
        } else {
            $this->showNotification('Error de la API: ' . $response->json('message', 'Error desconocido'), 'error');
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
        return view('livewire.admin.products.create')->layout('layouts.app');
    }
}