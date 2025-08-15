<div class="w-full h-full p-4 bg-mostaza">
    <div class="p-4 bg-[#D9D9D9] rounded-xl border border-gray-400">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">

            @forelse ($orders as $order)
                <div class="bg-gray-100 rounded-xl shadow-md p-4 flex flex-col space-y-3">
                    <div class="flex justify-between items-center border-b pb-2">
                        <div class="flex items-center">
                            <span class="text-xl font-bold text-gray-800">#{{ $order['id'] }}</span>
                            @php
                                $estado = $order['estadoPedido'];
                                $colorClasses = [
                                    'En preparación' => 'bg-yellow-200 text-yellow-800',
                                    'Listo para entregar' => 'bg-blue-200 text-blue-800',
                                    'Entregado' => 'bg-green-200 text-green-800',
                                    'Cancelado' => 'bg-red-200 text-red-800',
                                ][$estado] ?? 'bg-gray-200 text-gray-800';
                            @endphp
                            <span class="ms-3 px-2 py-1 text-xs font-semibold rounded-full {{ $colorClasses }}">{{ $estado }}</span>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500">Productos: {{ count($order['items']) }}</div>

                    <div class="flex-1 space-y-2 text-sm">
                        @foreach ($order['items'] as $item)
                            <div class="flex justify-between items-center">
                                {{-- ESTA LÍNEA SE MANTIENE COMO LA ORIGINAL --}}
                                <span>{{ $item['cantidad'] ?? '1' }}x {{ $item['nombre'] ?? 'Producto' }}</span>
                            </div>
                            @if(!empty($item['nota']))
                                <p class="text-xs text-red-600 ps-4">&rdsh; {{ $item['nota'] }}</p>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="pt-2">
                        <button wire:click="marcarComoListo({{ $order['id'] }})" class="w-full bg-white text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-200 border transition-colors flex items-center justify-center">
                            <span class="me-2">Listo</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4"><path d="M8 1.75a.75.75 0 0 1 .75.75V3a.75.75 0 0 1-1.5 0V2.5A.75.75 0 0 1 8 1.75ZM12.75 3.5a.75.75 0 0 0-1.06-1.06L10.6 3.525a.75.75 0 0 0 1.06 1.06l1.09-1.085ZM4.5 9.5c0-1.933 1.567-3.5 3.5-3.5s3.5 1.567 3.5 3.5c0 .9-.35 1.75-.954 2.383-.347.362-.758.67-1.203.888a1 1 0 0 1-1.039-.23l-.11-.124a1 1 0 0 1-.22-1.039c.218-.445.526-.856.888-1.202A2.02 2.02 0 0 0 10 9.5a2 2 0 1 0-4 0c0 .415.126.805.351 1.128.362.346.67.757.888 1.202a1 1 0 0 1-.22 1.039l-.11-.124a1 1 0 0 1-1.038.23c-.445-.218-.856-.526-1.203-.888C4.85 11.25 4.5 10.4 4.5 9.5Z" /></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-white font-semibold py-12">
                    No hay órdenes pendientes en cocina.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- ===== INICIO DE LA MODIFICACIÓN ========================== --}}
    {{-- ========================================================== --}}
    
    @if ($notification)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => { show = false; $wire.resetNotification() }, 3000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none"
        >
            <div
                class="p-4 rounded-lg shadow-lg text-white font-semibold flex items-center
                       {{ $notification['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' }}"
            >
                @if ($notification['type'] === 'success')
                    <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                @else
                    <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                @endif
                <span>{{ $notification['message'] }}</span>
            </div>
        </div>
    @endif

    {{-- ========================================================== --}}
    {{-- ===== FIN DE LA MODIFICACIÓN ============================= --}}
    {{-- ========================================================== --}}
</div>