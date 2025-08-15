<div>
    <div class="p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detalle del Pedido #{{ $order['id'] ?? $orderId }}</h1>
            <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 font-semibold">
                &larr; Volver a la lista de órdenes
            </a>
        </div>

        @if (!empty($order))
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Columna principal con los productos --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold mb-4">Productos en la Orden</h3>
                        <table class="w-full">
                            <thead class="border-b">
                                <tr>
                                    <th class="py-2 text-left text-sm font-semibold text-gray-600">Cantidad</th>
                                    <th class="py-2 text-left text-sm font-semibold text-gray-600">Producto</th>
                                    <th class="py-2 text-right text-sm font-semibold text-gray-600">Precio Unit.</th>
                                    <th class="py-2 text-right text-sm font-semibold text-gray-600">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order['items'] as $item)
                                <tr class="border-b">
                                    <td class="py-3">{{ $item['cantidad'] }}x</td>
                                    <td class="py-3 font-semibold">{{ $item['nombre'] ?? 'Producto no encontrado' }}</td>
                                    <td class="py-3 text-right">${{ number_format($item['precioUnitario'], 2) }}</td>
                                    <td class="py-3 text-right font-bold">${{ number_format($item['cantidad'] * $item['precioUnitario'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Este pedido no tiene productos.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="font-bold">
                                <tr>
                                    <td colspan="3" class="pt-4 text-right text-lg">Total del Pedido:</td>
                                    <td class="pt-4 text-right text-lg">${{ number_format($order['totalPedido'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Columna lateral con información y acciones --}}
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold mb-4">Información del Pedido</h3>
                        <p class="mb-2"><span class="font-bold">Modalidad:</span> {{ $order['modalidad'] ?? 'N/A' }}</p>
                        
                        @if (isset($order['mesaId']))
                            <p class="mb-2"><span class="font-bold">Mesa:</span> {{ $order['mesaNombre'] ?? $order['mesaId'] }}</p>
                        @endif

                        @php
                            $notaPedido = collect($order['items'])->pluck('nota')->first(fn ($nota) => !empty($nota));
                        @endphp

                        @if ($notaPedido)
                            <p class="mb-2"><span class="font-bold">Nota del Pedido:</span> {{ $notaPedido }}</p>
                        @endif
                        
                        <p class="mb-4"><span class="font-bold">Estado Actual:</span> <span class="font-bold text-blue-600">{{ $order['estadoPedido'] ?? 'N/A' }}</span></p>
                        
                        <h3 class="text-xl font-bold mt-6 mb-4">Acciones</h3>
                        <div class="space-y-3">
                            @php
                                $estado = $order['estadoPedido'] ?? '';
                                $modalidad = $order['modalidad'] ?? '';
                            @endphp

                            {{-- CORRECCIÓN: El nombre de la modalidad debe coincidir con el de la BD --}}
                            @if ($modalidad === 'En tienda')
                                @if ($estado === 'En preparación')
                                    <button wire:click="cambiarEstado('Listo para entregar')" class="w-full text-center py-2 px-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600">Marcar como Listo para Entregar</button>
                                @elseif ($estado === 'Listo para entregar')
                                    <button wire:click="cambiarEstado('Entregado')" class="w-full text-center py-2 px-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">Marcar como Entregado</button>
                                @elseif ($estado === 'Entregado')
                                    <button wire:click="finalizarYCobrar" class="w-full text-center py-3 px-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">Completado</button>
                                @endif
                            @endif

                            {{-- Flujo para "Para llevar" --}}
                            @if ($modalidad === 'Para llevar')
                                @if ($estado === 'En preparación')
                                    <button wire:click="cambiarEstado('Listo para recoger')" class="w-full text-center py-2 px-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600">Marcar como Listo para Recoger</button>
                                @elseif ($estado === 'Listo para recoger')
                                    <button wire:click="finalizarYCobrar" class="w-full text-center py-3 px-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">Completado</button>
                                @endif
                            @endif

                            {{-- Botón de Cancelar (siempre disponible mientras no esté completado o ya cancelado) --}}
                            @if (!in_array($estado, ['Completado', 'Cancelado']))
                                <button wire:click="cambiarEstado('Cancelado')" class="w-full text-center py-2 px-4 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600">Cancelar Pedido</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                Cargando información del pedido...
            </div>
        @endif
    </div>
</div>