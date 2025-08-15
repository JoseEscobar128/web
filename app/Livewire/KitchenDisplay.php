<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KitchenDisplay extends Component
{
    public array $orders = [];
    public array $estados = []; // Para almacenar los estados y sus IDs
    
    // ==========================================================
    // ===== INICIO DE LA MODIFICACIÓN ==========================
    // ==========================================================

    public ?array $notification = null; // Propiedad para la notificación

    // ==========================================================
    // ===== FIN DE LA MODIFICACIÓN =============================
    // ==========================================================

    public function mount()
    {
        $this->loadDependencies();
        $this->loadOrders();
    }
    
    public function loadDependencies()
    {
        // Cargamos los estados de la API para que el componente sepa sus IDs
        $response = Http::orderApi()->get('/estado_pedidos');
        if ($response->successful()) {
            $this->estados = $response->json()['data'] ?? [];
        }
    }

    public function loadOrders()
    {
        // ID del estado 'En preparación'
        $estadoEnPreparacionId = collect($this->estados)->firstWhere('nombre', 'En preparación')['id'] ?? null;

        if (!$estadoEnPreparacionId) {
            session()->flash('error', 'No se pudo encontrar el ID del estado "En preparación".');
            return;
        }

        $response = Http::orderApi()->get('/pedidos', [
            'estado_pedido_id' => $estadoEnPreparacionId
        ]);

        if ($response->successful()) {
            $this->orders = $response->json()['data'] ?? [];
        } else {
            session()->flash('error', 'No se pudieron cargar las órdenes.');
        }
    }

    public function marcarComoListo($orderId)
    {
        $usuarioId = session('user')->id ?? null;
        if (is_null($usuarioId)) {
            session()->flash('error', 'Debes iniciar sesión para actualizar un pedido.');
            return;
        }

        // ID del estado 'Listo para entregar'
        $estadoListoId = collect($this->estados)->firstWhere('nombre', 'Listo para entregar')['id'] ?? null;
        if (!$estadoListoId) {
            session()->flash('error', 'No se pudo encontrar el ID del estado "Listo para entregar".');
            return;
        }

        $response = Http::orderApi()->patch("/pedidos/{$orderId}/estado", [
            'estadoPedidoId' => $estadoListoId,
            'cambiadoPor' => $usuarioId,
        ]);

        if ($response->successful()) {
            // ==========================================================
            // ===== INICIO DE LA MODIFICACIÓN ==========================
            // ==========================================================
            
            // Usamos el nuevo sistema de notificación
            $this->showNotification("Orden #{$orderId} lista para entrega", 'success');

            // ==========================================================
            // ===== FIN DE LA MODIFICACIÓN =============================
            // ==========================================================
            
            $this->loadOrders();
        } else {
            session()->flash('error', 'No se pudo actualizar el estado de la orden.');
        }
    }
    
    // ==========================================================
    // ===== INICIO DE LA MODIFICACIÓN ==========================
    // ==========================================================

    // Métodos para manejar la notificación
    public function showNotification(string $message, string $type = 'success')
    {
        $this->notification = ['message' => $message, 'type' => $type];
    }
    
    public function resetNotification()
    {
        $this->notification = null;
    }
    
    // ==========================================================
    // ===== FIN DE LA MODIFICACIÓN =============================
    // ==========================================================

    public function render()
    {
        return view('livewire.kitchen-display')->layout('layouts.app');
    }
}