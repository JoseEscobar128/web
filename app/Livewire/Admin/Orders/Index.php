<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public array $orders = [];

    // --- PROPIEDADES PARA FILTROS ---
    public string $filtroModalidad = '';
    public string $filtroEstado = '';
    public string $filtroFechaInicio = '';
    public string $filtroFechaFin = '';

    public array $modalidades = [];
    public array $estados = [];

    protected $queryString = [
        'filtroModalidad' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
        'filtroFechaInicio' => ['except' => ''],
        'filtroFechaFin' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadFilterOptions();
        $this->loadOrders();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filtroModalidad', 'filtroEstado', 'filtroFechaInicio', 'filtroFechaFin'])) {
            $this->loadOrders();
        }
    }

    public function loadFilterOptions()
    {
        $modalidadesResponse = Http::orderApi()->get('/modalidades_pedido');
        if ($modalidadesResponse->successful()) {
            $this->modalidades = $modalidadesResponse->json()['data'] ?? [];
        }

        $estadosResponse = Http::orderApi()->get('/estado_pedidos');
        if ($estadosResponse->successful()) {
            $this->estados = $estadosResponse->json()['data'] ?? [];
        }
    }

    public function loadOrders()
    {
        $queryParams = http_build_query(array_filter([
            'modalidad_pedido_id' => $this->filtroModalidad,
            'estado_pedido_id' => $this->filtroEstado,
            'fecha_inicio' => $this->filtroFechaInicio,
            'fecha_fin' => $this->filtroFechaFin,
        ]));

        $url = "/pedidos?" . $queryParams;
        Log::info("Cargando órdenes con URL: {$url}");

        $response = Http::orderApi()->get($url);

        if ($response->successful()) {
            $this->orders = $response->json()['data'] ?? [];
        } else {
            $this->orders = [];
            session()->flash('error', 'No se pudieron cargar las órdenes.');
        }
    }

    public function resetFilters()
    {
        $this->reset(['filtroModalidad', 'filtroEstado', 'filtroFechaInicio', 'filtroFechaFin']);
        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.admin.orders.index')->layout('layouts.app');
    }
}