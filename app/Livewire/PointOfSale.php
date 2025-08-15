<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;

class PointOfSale extends Component
{
    public array $products = [];
    public array $orderItems = [];
    public array $modalidades = [];
    public array $mesas = [];

    // --- PROPIEDADES PARA EL NUEVO FLUJO ---
    public $selectedModalidadId = 1; // ID 1: "Para comer aqui", ID 2: "Para llevar"
    public $selectedMesaId = '';
    public string $nombreCliente = ''; // Para guardar el nombre en pedidos "Para llevar"

    public ?array $notification = null;

    public function mount()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        // Productos
        $productsResponse = Http::orderApi()->get('/productos');
        if ($productsResponse->successful()) {
            $this->products = $productsResponse->json()['data'] ?? [];
        }

        // Modalidades: Filtramos para que en el POS solo aparezcan las opciones relevantes.
        $modalidadesResponse = Http::orderApi()->get('/modalidades_pedido');
        if ($modalidadesResponse->successful()) {
            $allModalidades = $modalidadesResponse->json()['data'] ?? [];
            // Solo mostramos "Para comer aqui" (ID 1) y "Para llevar" (ID 2)
            $this->modalidades = collect($allModalidades)->whereIn('id', [1, 2])->all();
        }

        // Mesas
        $mesasResponse = Http::orderApi()->get('/mesas');
        if ($mesasResponse->successful()) {
            $this->mesas = $mesasResponse->json()['data'] ?? [];
        }
    }

    #[Computed]
    public function total()
    {
        return array_reduce($this->orderItems, function ($carry, $item) {
            return $carry + ($item['cantidad'] * $item['precio']);
        }, 0);
    }

    public function addToOrder($productId)
    {
        $product = collect($this->products)->firstWhere('id', $productId);

        if (!$product) { return; }

        $index = collect($this->orderItems)->search(fn($item) => $item['id'] === $productId);

        if ($index !== false) {
            $this->orderItems[$index]['cantidad']++;
        } else {
            $this->orderItems[] = [
                'id' => $product['id'],
                'nombre' => $product['nombre'],
                'precio' => $product['precio'],
                'cantidad' => 1,
            ];
        }
    }

    public function removeFromOrder($productId)
    {
        $this->orderItems = collect($this->orderItems)
            ->filter(fn($item) => $item['id'] !== $productId)
            ->values()
            ->all();
    }

    public function clearOrder()
    {
        $this->orderItems = [];
        $this->selectedMesaId = '';
        $this->nombreCliente = ''; // Limpiar también el nombre
        $this->selectedModalidadId = 1; // Regresar a la modalidad por defecto
    }

    public function processOrder()
    {
        if (empty($this->orderItems)) {
            $this->showNotification('No hay productos en la orden.', 'error');
            return;
        }

        $userId = session('user')->id ?? null;
        if (is_null($userId)) {
            $this->showNotification('Debes iniciar sesión para crear una orden.', 'error');
            return;
        }

        // --- VALIDACIÓN POR MODALIDAD ---
        // Si es "Para comer aqui" (ID 1), requiere una mesa.
        if ($this->selectedModalidadId == 1 && empty($this->selectedMesaId)) {
            $this->showNotification('Debes seleccionar una mesa.', 'error');
            return;
        }

        // Si es "Para llevar" (ID 2), requiere un nombre de cliente.
        if ($this->selectedModalidadId == 2 && empty(trim($this->nombreCliente))) {
            $this->showNotification('Debes ingresar el nombre del cliente.', 'error');
            return;
        }

        $itemsPayload = collect($this->orderItems)->map(function ($item) {
            return [
                'productoId' => $item['id'],
                'cantidad' => $item['cantidad'],
                // Asigna la nota solo si es un pedido "Para llevar"
                'nota' => $this->selectedModalidadId == 2 ? 'A nombre de: ' . $this->nombreCliente : null,
            ];
        })->values()->all();

        $estadoInicialId = 1; // El pedido nace "En preparación"

        $payload = [
            'sucursalId' => 1,
            'clienteId' => null,
            'usuarioCreoId' => $userId,
            'modalidadPedidoId' => $this->selectedModalidadId,
            'estadoPedidoId' => $estadoInicialId,
            'items' => $itemsPayload,
            // Asigna la mesa solo si es "Para comer aqui"
            'mesaId' => $this->selectedModalidadId == 1 ? $this->selectedMesaId : null,
        ];

        $response = Http::orderApi()->post('/pedidos', $payload);

        if ($response->successful()) {
            $this->showNotification('Orden creada y enviada a cocina.', 'success');
            
            if ($this->selectedModalidadId == 1 && !empty($this->selectedMesaId)) {
                Http::orderApi()->put("/mesas/{$this->selectedMesaId}", ['ocupada' => true]);
            }
            
            $this->clearOrder();
        } else {
            $errorMessage = $response->json('message', 'Ocurrió un error inesperado.');
            $this->showNotification('Error al crear la orden: ' . $errorMessage, 'error');
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
        return view('livewire.point-of-sale')->layout('layouts.app');
    }
}