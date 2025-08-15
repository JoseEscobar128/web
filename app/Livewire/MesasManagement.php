<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MesasManagement extends Component
{
    public array $mesas = [];
    public array $mesasConOrdenActiva = [];
    public ?array $notification = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        Log::info('--- INICIO: loadData en MesasManagement ---');
        
        // 1. OBTENEMOS LAS ÓRDENES ACTIVAS
        $pedidosResponse = Http::orderApi()->get('/pedidos');
        if ($pedidosResponse->successful()) {
            $pedidos = $pedidosResponse->json('data') ?? [];
            Log::info('Pedidos recibidos de la API:', ['cantidad' => count($pedidos)]);
            
            $this->mesasConOrdenActiva = collect($pedidos)
                ->whereNotIn('estadoPedido', ['Completado', 'Cancelado'])
                ->whereNotNull('mesaId')
                ->pluck('mesaId')
                ->unique()
                ->all();
            
            Log::info('IDs de mesas con órdenes activas calculados:', $this->mesasConOrdenActiva);
        } else {
            Log::error('Fallo al cargar pedidos para la gestión de mesas.');
            $this->mesasConOrdenActiva = [];
        }

        // 2. OBTENEMOS LAS MESAS
        $mesasResponse = Http::orderApi()->get('/mesas');
        if ($mesasResponse->successful()) {
            $this->mesas = $mesasResponse->json('data') ?? [];
        } else {
            session()->flash('error', 'No se pudieron cargar las mesas.');
            $this->mesas = [];
        }
    }

    public function toggleOcupada(int $mesaId, bool $ocupadaActual)
    {
        Log::info("--- INICIO: toggleOcupada para Mesa #{$mesaId} ---");
        Log::info('Estado actual de la mesa:', ['ocupada' => $ocupadaActual]);
        Log::info('Lista de mesas con órdenes activas:', $this->mesasConOrdenActiva);

        // Si la mesa está ocupada y el usuario intenta liberarla...
        if ($ocupadaActual === true) {
            Log::info("La mesa está ocupada, verificando si tiene una orden activa...");
            
            // ...verificamos si está en nuestra lista de mesas con órdenes activas.
            if (in_array($mesaId, $this->mesasConOrdenActiva)) {
                Log::warning("¡BLOQUEADO! Intento de liberar la mesa #{$mesaId} mientras tiene una orden activa.");
                $this->showNotification('Hay una orden activa en esta mesa. No se puede liberar manualmente.', 'error');
                return;
            }
            Log::info("La mesa #{$mesaId} no tiene una orden activa. Se puede liberar.");
        }

        // Si pasa la validación, procedemos a cambiar el estado.
        $nuevoEstado = !$ocupadaActual;
        $response = Http::orderApi()->put("/mesas/{$mesaId}", [
            'ocupada' => $nuevoEstado,
        ]);

        if ($response->successful()) {
            $this->showNotification('Estado de la mesa actualizado.', 'success');
            $this->loadData();
        } else {
            $this->showNotification('Error al actualizar la mesa.', 'error');
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
        return view('livewire.mesas-management')->layout('layouts.app');
    }
}