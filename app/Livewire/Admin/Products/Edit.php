<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // <-- AÑADIDO

class Edit extends Component
{
    use WithFileUploads;

    public $productId;
    public $nombre, $precio, $categoria_id, $descripcion, $sucursal_id;
    public $imagen_principal = null;
    public $new_image;

    public array $categories = [];
    public array $sucursales = [];
    
    public ?array $notification = null;

    public function mount($id)
    {
        $this->productId = $id;
        $this->loadDependencies();
        $this->loadProduct();
    }

    private function loadDependencies()
    {
        $categoriesResponse = Http::orderApi()->get('/categorias');
        if ($categoriesResponse->successful()) {
            $this->categories = $categoriesResponse->json()['data'] ?? [];
        }

        $sucursalesResponse = Http::orderApi()->get('/sucursales');
        if ($sucursalesResponse->successful()) {
            $this->sucursales = $sucursalesResponse->json()['data'] ?? [];
        }
    }

    private function loadProduct()
    {
        Log::info("--- Cargando datos para producto ID: {$this->productId} ---");
        $response = Http::orderApi()->get("/productos/{$this->productId}");

        if ($response->successful()) {
            $product = $response->json()['data'];
            
            Log::info("Datos recibidos de la API:", $product);

            $this->nombre = $product['nombre'] ?? '';
            $this->precio = $product['precio'] ?? 0;
            $this->categoria_id = $product['categoria_id'] ?? null;
            $this->descripcion = $product['descripcion'] ?? '';
            $this->sucursal_id = $product['sucursal_id'] ?? null;
            
            $imageData = $product['imagen_principal'] ?? null;

            Log::info("Valor crudo de 'imagen_principal':", ['data' => $imageData, 'type' => gettype($imageData)]);
            
            if (is_string($imageData)) {
                $decodedData = json_decode($imageData, true);
                Log::info("Resultado de json_decode:", ['decoded' => $decodedData]);
                $this->imagen_principal = $decodedData;
            } else {
                $this->imagen_principal = $imageData;
            }

            Log::info("Valor final de \$this->imagen_principal:", ['data' => $this->imagen_principal]);

        } else {
            Log::error("Fallo al cargar producto ID: {$this->productId}", ['status' => $response->status(), 'body' => $response->body()]);
            session()->flash('error', 'No se pudo cargar la información del producto.');
            return redirect()->route('admin.products.index');
        }
    }

    public function updatedNewImage()
    {
        $this->validate(['new_image' => 'image|max:2048']);
    }

    public function updateProduct()
    {
        try {
            $this->validate([
                'nombre' => 'required|string|max:150',
                'precio' => 'required|numeric|min:0',
                'categoria_id' => 'required|integer',
                'sucursal_id' => 'required|integer',
                'new_image' => 'nullable|image|max:2048',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $errorMessage = array_values($errors)[0][0] ?? 'Te faltan campos por llenar.';
            $this->showNotification($errorMessage, 'error');
            return;
        }
        
        $payload = [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'categoria_id' => $this->categoria_id,
            'sucursal_id' => $this->sucursal_id,
        ];
        
        if ($this->new_image) {
            // Lógica para borrar la imagen anterior
            if (is_array($this->imagen_principal) && !empty($this->imagen_principal[0])) {
                $oldFilename = $this->imagen_principal[0];
                Storage::disk('public')->delete('images/' . $oldFilename);
            }

            // Generar un nombre de archivo limpio y predecible
            $extension = $this->new_image->getClientOriginalExtension();
            $filename = Str::slug($this->nombre) . '-' . time() . '.' . $extension;

            // Guardar la imagen con nuestro nombre personalizado
            $this->new_image->storeAs('images', $filename, 'public');
            
            // ==========================================================
            // ===== CAMBIO AQUÍ: QUITA EL `json_encode()` ==============
            // ==========================================================
            $payload['imagen_principal'] = [$filename]; // <-- Se pasa el array directamente
            // ==========================================================
        }

        $response = Http::orderApi()->put("/productos/{$this->productId}", $payload);

        if ($response->successful()) {
            $this->showNotification('Producto actualizado exitosamente.', 'success');
            sleep(2);
            return redirect()->route('admin.products.index');
        } else {
            Log::error('Error al actualizar producto en API', ['status' => $response->status(), 'body' => $response->json()]);
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
        return view('livewire.admin.products.edit')->layout('layouts.app');
    }
}