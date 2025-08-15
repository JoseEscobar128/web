<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class Dashboard extends Component
{
    public array $summary = [];

    public function mount()
    {
        $this->loadSummaryData();
    }

    private function loadSummaryData()
    {
        try {
            //Log::info('--- INICIO DE CARGA DE DATOS DEL DASHBOARD ---');

            // 1. Obtenemos todos los datos crudos de las APIs
            $pedidosResponse = Http::orderApi()->get('/pedidos');
            $pedidos = $pedidosResponse->successful() ? $pedidosResponse->json()['data'] ?? [] : [];
            //Log::info('Paso 1: Pedidos obtenidos de la API.', ['cantidad' => count($pedidos), 'primer_pedido_ejemplo' => $pedidos[0] ?? 'No hay pedidos']);

            $mesasResponse = Http::orderApi()->get('/mesas');
            $mesas = $mesasResponse->successful() ? $mesasResponse->json()['data'] ?? [] : [];
            
            $productosResponse = Http::orderApi()->get('/productos');
            $productos = $productosResponse->successful() ? collect($productosResponse->json()['data'] ?? [])->keyBy('id') : collect();
            //Log::info('Paso 2: Productos obtenidos de la API.', ['cantidad' => $productos->count()]);

            // 2. Calculamos las métricas simples
            $totalIngresos = collect($pedidos)->sum('totalPedido');
            $pedidosHoy = collect($pedidos)->filter(fn ($p) => Carbon::parse($p['createdAt'])->isToday())->count();
            $mesasOcupadas = collect($mesas)->where('ocupada', true)->count();

            // --- LÓGICA PARA LOS MÁS VENDIDOS ---

            // 3. Extraemos todos los items de todos los pedidos en una sola lista
            $allItems = collect($pedidos)->flatMap(function ($pedido) {
                return $pedido['items'] ?? [];
            });
            //Log::info('Paso 3: Todos los items de los pedidos han sido aplanados.', ['total_items' => $allItems->count(), 'primer_item_ejemplo' => $allItems->first()]);

            // 4. Agrupamos por ID de producto y sumamos las cantidades
            $productSales = $allItems->groupBy('producto_id')
                ->map(function ($items, $productoId) {
                    return ['productoId' => $productoId, 'total_vendido' => $items->sum('cantidad')];
                })
                ->sortByDesc('total_vendido')
                ->take(3);
            //Log::info('Paso 4: Ventas por producto calculadas y ordenadas.', ['top_ventas' => $productSales->toArray()]);

            // 5. Enriquecemos los datos con la información completa del producto
            $masVendidos = $productSales->map(function ($sale) use ($productos) {
                $productInfo = $productos->get($sale['productoId']);
                if ($productInfo) {
                    return array_merge($productInfo, ['cantidad_vendida' => $sale['total_vendido']]);
                }
                //Log::warning('Producto no encontrado en la colección de productos.', ['productoId' => $sale['productoId']]);
                return null;
            })->filter()->values()->all();
            //Log::info('Paso 5: Datos de "Más Vendidos" enriquecidos.', ['mas_vendidos' => $masVendidos]);

            // 6. Ensamblamos el resumen final
            $this->summary = [
                'total_ingresos' => $totalIngresos,
                'pedidos_hoy' => $pedidosHoy,
                'mesas_ocupadas' => $mesasOcupadas,
                'mesas_totales' => count($mesas),
                'mas_vendidos' => $masVendidos,
            ];
            //Log::info('--- FIN DE CARGA DE DATOS DEL DASHBOARD ---');

        } catch (\Exception $e) {
            //Log::error('--- ERROR AL CARGAR LOS DATOS DEL DASHBOARD ---');
            //Log::error('Mensaje de Excepción: ' . $e->getMessage());
            //Log::error('Archivo: ' . $e->getFile() . ' en línea ' . $e->getLine());
            session()->flash('error', 'No se pudieron cargar los datos del dashboard.');
            $this->summary = [];
        }
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}