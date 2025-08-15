<?php

namespace App\Livewire\Admin\Attendance;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public array $asistencias = [];
    
    // --- PROPIEDADES PARA FILTROS ---
    public string $search = '';
    public string $fecha_inicio = '';
    public string $fecha_fin = '';

    // --- PROPIEDADES PARA PAGINACIÓN ---
    public int $currentPage = 1;
    public int $lastPage = 1;
    public int $total = 0;
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'fecha_inicio' => ['except' => ''],
        'fecha_fin' => ['except' => ''],
        'currentPage' => ['except' => 1, 'as' => 'page'],
    ];

    public function mount()
    {
        $this->loadAttendances();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'fecha_inicio', 'fecha_fin'])) {
            $this->currentPage = 1; // Resetear la página al cambiar un filtro
            $this->loadAttendances();
        }
    }

    public function loadAttendances()
    {
        $query = array_filter([
            'search' => $this->search,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'page' => $this->currentPage,
        ]);

        $response = Http::authApi()->get('/asistencias', $query);

        if ($response->successful()) {
            $data = $response->json();
            $this->asistencias = $data['data'] ?? [];
            $this->currentPage = $data['current_page'] ?? 1;
            $this->lastPage = $data['last_page'] ?? 1;
            $this->total = $data['total'] ?? 0;
            $this->perPage = $data['per_page'] ?? 15;
        } else {
            $this->asistencias = [];
            session()->flash('error', 'No se pudo cargar la lista de asistencias.');
        }
    }

    public function goToPage($page)
    {
        if ($page >= 1 && $page <= $this->lastPage) {
            $this->currentPage = $page;
            $this->loadAttendances();
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'fecha_inicio', 'fecha_fin', 'currentPage']);
        $this->loadAttendances();
    }

    public function render()
    {
        return view('livewire.admin.attendance.index')->layout('layouts.app');
    }
}