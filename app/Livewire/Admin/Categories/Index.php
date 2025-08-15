<?php

namespace App\Livewire\Admin\Categories;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Index extends Component
{
    public array $categories = [];
    public ?int $categoryToDeleteId = null;
    public bool $showConfirmModal = false;
    public string $search = '';

    protected $queryString = ['search'];

    public function mount()
    {
        $this->loadCategories();
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    private function loadCategories()
    {
        //dd(session('access_token')); 
        $query = ['nombre' => $this->search];
        $response = Http::orderApi()->get('/categorias', $query);

         \Log::info('Respuesta de la API de categorías', ['status' => $response->status(), 'body' => $response->json()]);

        if ($response->successful()) {
            $this->categories = $response->json()['data'];
        } else {
            $this->categories = [];
            session()->flash('error', 'No se pudo cargar la lista de categorías.');
        }
    }

    public function confirmCategoryDeletion($id)
    {
        $this->categoryToDeleteId = $id;
        $this->showConfirmModal = true;
    }

    public function deleteCategory()
    {
        if ($this->categoryToDeleteId === null) return;

        $response = Http::orderApi()->delete("/categorias/{$this->categoryToDeleteId}");

        if ($response->successful()) {
            session()->flash('success', 'Categoría eliminada correctamente.');
        } else {
            $errorMessage = $response->json('message', 'No se pudo eliminar la categoría.');
            session()->flash('error', $errorMessage);
        }
        
        $this->showConfirmModal = false;
        $this->loadCategories(); // Recargar la lista
    }

    public function render()
    {
        return view('livewire.admin.categories.index')->layout('layouts.app');
    }
}