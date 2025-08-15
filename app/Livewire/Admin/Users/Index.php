<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public array $users = [];
    public ?int $userToDeleteId = null;
    public bool $showConfirmModal = false;

    // --- PROPIEDAD PARA EL BUSCADOR ---
    public string $search = '';

    /**
     * Mantiene el término de búsqueda en la URL.
     */
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * Se ejecuta cuando el componente es inicializado.
     */
    public function mount()
    {
        $this->loadUsers();
    }

    /**
     * Hook que se dispara cuando la propiedad 'search' cambia.
     */
    public function updatedSearch()
    {
        $this->loadUsers();
    }

    /**
     * Carga la lista de usuarios desde la API, aplicando el filtro de búsqueda.
     */
    public function loadUsers()
    {
        // Prepara los parámetros para la API, solo si hay algo en el buscador.
        $query = array_filter(['search' => $this->search]);

        $response = Http::authApi()->get('/usuarios', $query);

        if ($response->successful()) {
            $this->users = $response->json()['data'];
        } else {

            Log::error('Error al cargar usuarios desde la API', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
            ]);
            if ($response->status() === 401) {
                session()->forget(['access_token', 'user']);
                return redirect()->route('home')->with('error', 'Tu sesión ha expirado.');
            }
            $this->users = [];
            session()->flash('error', 'No se pudo cargar la lista de usuarios.');
        }
    }

    public function confirmUserDeletion($id)
    {
        $this->userToDeleteId = $id;
        $this->showConfirmModal = true;
    }

    public function deleteUser()
    {
        if ($this->userToDeleteId === null) return;

        $response = Http::authApi()->delete("/usuarios/{$this->userToDeleteId}");

        if ($response->successful()) {
            session()->flash('success', 'Usuario eliminado correctamente.');
        } else {
            session()->flash('error', 'No se pudo eliminar el usuario.');
        }
        
        $this->showConfirmModal = false;
        $this->loadUsers(); // Recargamos la lista de usuarios para reflejar el cambio
    }

    public function render()
    {
        return view('livewire.admin.users.index')->layout('layouts.app');
    }
}