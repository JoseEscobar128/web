<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    public array $order = [];
    public $orderId;
    public array $estados = [];

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrderData();
    }

    public function loadOrderData()
    {
        // Cargamos los estados para poder buscar sus IDs
        $estadosResponse = Http::orderApi()->get('/estado_pedidos');
        if ($estadosResponse->successful()) {
            $this->estados = $estadosResponse->json()['data'] ?? [];
        }

        // Cargamos el detalle del pedido
        Log::info("--- INICIO: Cargando datos para Pedido #{$this->orderId} ---");
        $response = Http::orderApi()->get("/pedidos/{$this->orderId}");

        if ($response->successful()) {
            Log::info("Respuesta exitosa de la API para Pedido #{$this->orderId}:", $response->json());
            $this->order = $response->json()['data'];
        } else {
            $this->order = [];
            session()->flash('error', 'No se pudo cargar el detalle del pedido.');
            Log::error("Fallo al cargar pedido #{$this->orderId}", ['status' => $response->status(), 'body' => $response->body()]);
        }
    }

    public function cambiarEstado($nuevoEstadoNombre)
    {
        $usuarioId = session('user')->id ?? null;
        $estado = collect($this->estados)->firstWhere('nombre', $nuevoEstadoNombre);
        
        if (!$estado || is_null($usuarioId)) {
            session()->flash('error', 'No se pudo procesar la solicitud.');
            return;
        }

        $response = Http::orderApi()->patch("/pedidos/{$this->orderId}/estado", [
            'estadoPedidoId' => $estado['id'],
            'cambiadoPor' => $usuarioId,
        ]);
        
        if ($response->successful()) {
            session()->flash('success', 'Estado del pedido actualizado.');
            $this->loadOrderData(); // Recargamos los datos para ver el cambio
        } else {
            session()->flash('error', 'Error al actualizar el estado del pedido.');
        }
    }

    public function finalizarYCobrar()
    {
        Log::info("--- INICIO: finalizarYCobrar para Pedido #{$this->orderId} ---");

        $mesaId = $this->order['mesaId'] ?? null;
        $estadoCompletado = collect($this->estados)->firstWhere('nombre', 'Completado');
        $usuarioId = session('user')->id ?? null;

        Log::info('Datos iniciales para finalizar:', [
            'mesaId' => $mesaId,
            'estadoCompletado' => $estadoCompletado,
            'usuarioId' => $usuarioId
        ]);

        if (!$estadoCompletado || is_null($usuarioId)) {
            session()->flash('error', 'No se pudo procesar la finalización.');
            Log::error('Fallo de pre-condición en finalizarYCobrar.', ['estado' => $estadoCompletado, 'usuario' => $usuarioId]);
            return;
        }

        // 1. ACTUALIZAMOS EL ESTADO DEL PEDIDO A "COMPLETADO"
        $responsePedido = Http::orderApi()->patch("/pedidos/{$this->orderId}/estado", [
            'estadoPedidoId' => $estadoCompletado['id'],
            'cambiadoPor' => $usuarioId,
        ]);

        Log::info("Respuesta de API al cambiar estado del pedido:", ['status' => $responsePedido->status(), 'body' => $responsePedido->json()]);

        if ($responsePedido->successful()) {
            // 2. SI EL PEDIDO TENÍA MESA, LA LIBERAMOS
            if ($mesaId) {
                Log::info("El pedido tenía una mesa ({$mesaId}). Intentando liberarla...");
                $responseMesa = Http::orderApi()->put("/mesas/{$mesaId}", [
                    'ocupada' => false,
                ]);
                Log::info('Respuesta de API al liberar la mesa:', ['status' => $responseMesa->status(), 'body' => $responseMesa->json()]);
            } else {
                Log::info("El pedido no tenía una mesa asignada, no se libera ninguna.");
            }
            
            session()->flash('success', "Orden #{$this->orderId} finalizada correctamente.");
            return redirect()->route('admin.orders.index');
        } else {
            session()->flash('error', 'No se pudo finalizar la orden.');
            Log::error("Fallo al actualizar el pedido #{$this->orderId} a 'Completado'.");
        }
    }

    public function render()
    {
        return view('livewire.admin.orders.show')->layout('layouts.app');
    }
}