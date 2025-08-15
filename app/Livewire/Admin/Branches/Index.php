<?php

namespace App\Livewire\Admin\Branches;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Index extends Component
{
    public array $branches = [];
    public ?int $branchToDeleteId = null;
    public bool $showConfirmModal = false;

    // --- PROPIEDAD PARA EL BUSCADOR ---
    public string $search = '';

    protected $queryString = ['search' => ['except' => '']];

    public function mount()
    {
        $this->loadBranches();
    }

    public function updatedSearch()
    {
        $this->loadBranches();
    }

    private function loadBranches()
    {
        // Prepara los parámetros para la API, solo si hay algo en el buscador.
        $query = array_filter(['nombre' => $this->search]);

        $response = Http::orderApi()->get('/sucursales', $query);

        if ($response->successful()) {
            $this->branches = $response->json()['data'];
        } else {
            $this->branches = [];
            session()->flash('error', 'No se pudo cargar la lista de sucursales.');
        }
    }

    public function confirmBranchDeletion($id)
    {
        $this->branchToDeleteId = $id;
        $this->showConfirmModal = true;
    }

    public function deleteBranch()
    {
        if ($this->branchToDeleteId === null) return;

        $response = Http::orderApi()->delete("/sucursales/{$this->branchToDeleteId}");

        if ($response->successful()) {
            session()->flash('success', 'Sucursal eliminada correctamente.');
        } else {
            session()->flash('error', 'No se pudo eliminar la sucursal.');
        }
        
        $this->showConfirmModal = false;
        $this->loadBranches(); // Recargar la lista
    }

    public function render()
    {
        return view('livewire.admin.branches.index')->layout('layouts.app');
    }
}