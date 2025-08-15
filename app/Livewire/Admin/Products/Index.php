<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    public array $products = [];
    public ?string $productToDeleteId = null;
    public bool $showConfirmModal = false;

    // --- PROPIEDADES PARA FILTROS ---
    public string $search = '';
    public array $categories = [];
    public string $selectedCategory = ''; // Usamos string para que el valor inicial sea ""

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'selectedCategory'])) {
            $this->loadData();
        }
    }

    private function loadData()
    {
        // Cargar categorías para el filtro
        $categoriesResponse = Http::orderApi()->get('/categorias');
        if ($categoriesResponse->successful()) {
            $this->categories = $categoriesResponse->json()['data'] ?? [];
        }

        // Preparar los parámetros para la API, filtrando valores vacíos
        $query = array_filter([
            'nombre' => $this->search,
            'categoria_id' => $this->selectedCategory,
        ]);

        // Cargar productos con los filtros aplicados
        $response = Http::orderApi()->get('/productos', $query);

        if ($response->successful()) {
            $this->products = $response->json()['data'] ?? [];
        } else {
            $this->products = [];
            session()->flash('error', 'No se pudo cargar la lista de productos.');
        }
    }

    public function confirmProductDeletion($id)
    {
        $this->productToDeleteId = $id;
        $this->showConfirmModal = true;
    }

    public function deleteProduct()
    {
        if ($this->productToDeleteId === null) return;

        $response = Http::orderApi()->delete("/productos/{$this->productToDeleteId}");

        if ($response->successful()) {
            session()->flash('success', 'Producto eliminado correctamente.');
        } else {
            session()->flash('error', 'No se pudo eliminar el producto.');
        }

        $this->showConfirmModal = false;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.products.index')->layout('layouts.app');
    }
}