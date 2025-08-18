<div> {{-- Elemento raíz principal para Livewire --}}

    <div class="w-full h-full flex p-3 lg:p-4 bg-mostaza space-x-3 lg:space-x-4">

        {{-- Columna Izquierda: Menú --}}
        <div class="w-full lg:w-2/3 bg-white rounded-lg shadow p-3 flex flex-col">
            <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-3">Menú</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 overflow-y-auto"
                 style="max-height: calc(100vh - 140px);">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col">

                        {{-- ========================================================== --}}
                        {{-- ===== INICIO DE LA CORRECCIÓN DE IMAGEN ================== --}}
                        {{-- ========================================================== --}}
                        @php
                            // Imagen por defecto
                            $imageUrl = 'https://placehold.co/150x150/F5BB20/FFFFFF?text=Imagen';
                            // Si existe imagen principal, usar storage público
                            if (!empty($product['imagen_principal'][0])) {
                                $imageUrl = asset('storage/images/' . $product['imagen_principal'][0]);
                            }
                        @endphp

                        <img src="{{ $imageUrl }}"
                             alt="{{ $product['nombre'] }}"
                             class="w-full h-24 lg:h-28 object-cover rounded-lg">
                        {{-- ========================================================== --}}
                        {{-- ===== FIN DE LA CORRECCIÓN DE IMAGEN ===================== --}}
                        {{-- ========================================================== --}}

                        <div class="mt-1 flex-1 flex flex-col">
                            <h4 class="font-bold text-xs lg:text-sm">{{ $product['nombre'] }}</h4>
                            <p class="text-[10px] text-gray-500 flex-1 leading-tight">{{ $product['descripcion'] }}</p>
                            <p class="font-bold text-xs lg:text-base mt-1">
                                ${{ number_format($product['precio'], 2) }}
                            </p>
                        </div>

                        <button wire:click="addToOrder({{ $product['id'] }})"
                                class="mt-1 w-full bg-mostaza text-white font-bold py-1.5 px-2 rounded-xl hover:bg-opacity-90 text-[10px] lg:text-xs">
                            + Añadir
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Columna Derecha: Orden --}}
        <div class="w-full lg:w-1/3 bg-white rounded-lg shadow p-3 flex flex-col">
            <h3 class="text-lg lg:text-xl font-bold text-center">Orden</h3>
            <div class="text-[10px] lg:text-xs text-gray-500 flex justify-between mt-1">
                <span>Encargado de caja: {{ session('user')->usuario ?? 'Admin' }}</span>
            </div>
            <hr class="my-2">

            {{-- Lista de productos con scroll interno --}}
            <div class="flex-1 overflow-y-auto space-y-1"
                 style="max-height: calc(100vh - 350px);">
                @forelse ($orderItems as $item)
                    <div class="flex items-center justify-between py-1 text-[11px] lg:text-xs">
                        <div>
                            <p class="font-bold">{{ $item['cantidad'] }} x {{ $item['nombre'] }}</p>
                            <p class="text-gray-500">${{ number_format($item['precio'], 2) }} c/u</p>
                        </div>
                        <div class="flex items-center">
                            <p class="font-bold me-2">${{ number_format($item['cantidad'] * $item['precio'], 2) }}</p>
                            <button wire:click="removeFromOrder({{ $item['id'] }})"
                                    class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-gray-400 text-xs">
                        Añade productos a la orden...
                    </div>
                @endforelse
            </div>

            {{-- Parte inferior de la orden --}}
            <div class="mt-auto pt-3 border-t space-y-3">
            
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-xs">Modalidad del Pedido</label>
                    <div class="flex space-x-2">
                        @foreach ($modalidades as $modalidad)
                            <label for="modalidad_{{ $modalidad['id'] }}" class="flex-1">
                                <input type="radio" wire:model.live="selectedModalidadId" value="{{ $modalidad['id'] }}" id="modalidad_{{ $modalidad['id'] }}" class="sr-only peer">
                                <div class="text-center w-full p-1.5 text-xs font-bold rounded-lg cursor-pointer peer-checked:bg-mostaza peer-checked:text-white bg-gray-200 text-gray-700">
                                    {{ $modalidad['nombre'] }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                @if ($selectedModalidadId == 1)
                <div wire:key="field-mesa">
                    <label for="mesa" class="block text-gray-700 font-semibold mb-1 text-xs">Seleccionar mesa</label>
                    <select id="mesa" wire:model="selectedMesaId" class="w-full border border-gray-300 rounded p-1.5 text-xs">
                        <option value="">-- Seleccionar --</option>
                        @foreach ($mesas as $mesa)
                            @if(!$mesa['ocupada'])
                                <option value="{{ $mesa['id'] }}">{{ $mesa['nombre'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if ($selectedModalidadId == 2)
                <div wire:key="field-cliente">
                    <label for="nombreCliente" class="block text-gray-700 font-semibold mb-1 text-xs">¿A nombre de quién?</label>
                    <input type="text" id="nombreCliente" wire:model="nombreCliente" placeholder="Ej: Juan Pérez"
                           class="w-full border border-gray-300 rounded p-1.5 text-xs">
                </div>
                @endif

                <div class="flex justify-between items-center font-bold text-base lg:text-lg mt-1">
                    <span>Total:</span>
                    <span>${{ number_format($this->total, 2) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-1.5 mt-2">
                    <button wire:click="clearOrder" class="w-full bg-red-500 text-white font-bold py-1.5 lg:py-2 rounded-lg hover:bg-red-600 text-xs">
                        Eliminar
                    </button>
                    <button wire:click="processOrder" wire:loading.attr="disabled" class="w-full bg-green-500 text-white font-bold py-1.5 lg:py-2 rounded-lg hover:bg-green-600 text-xs disabled:opacity-50">
                        <span wire:loading.remove wire:target="processOrder">Crear orden</span>
                        <span wire:loading wire:target="processOrder">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notificación --}}
    @if ($notification)
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => { show = false; $wire.resetNotification() }, 3000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
            <div class="p-3 rounded-lg shadow-lg text-white font-semibold flex items-center
                        {{ $notification['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' }}">
                @if ($notification['type'] === 'success')
                    <svg class="w-5 h-5 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                @endif
                <span class="text-xs">{{ $notification['message'] }}</span>
            </div>
        </div>
    @endif
</div>
