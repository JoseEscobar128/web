<div>
    <div class="p-8 h-full">
        <div class="bg-white rounded-lg shadow-lg max-w-full h-full flex flex-col">
            
            <div class="p-6 flex-shrink-0">
                <h2 class="text-3xl font-bold text-gray-800 border-b pb-4">Gestión de Mesas</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($mesas as $mesa)
                        <div class="border rounded-lg p-5 flex flex-col justify-between shadow hover:shadow-xl transition-shadow duration-300
                                    {{ $mesa['ocupada'] ? 'bg-red-50' : 'bg-green-50' }}">
                            <div>
                                <h3 class="text-xl font-semibold mb-3">Mesa {{ $mesa['nombre'] }}</h3>
                                <p class="text-sm text-gray-600 mb-4">Estado: 
                                    @if ($mesa['ocupada'])
                                        <span class="text-red-600 font-bold">Ocupada</span>
                                    @else
                                        <span class="text-green-600 font-bold">Disponible</span>
                                    @endif
                                </p>
                            </div>

                            {{-- El botón ahora es más simple. La lógica está en el componente. --}}
                            <button
                                wire:click="toggleOcupada({{ $mesa['id'] }}, {{ $mesa['ocupada'] ? 'true' : 'false' }})"
                                class="py-2 mt-4 rounded font-semibold transition-colors
                                    {{ $mesa['ocupada'] ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                                {{ $mesa['ocupada'] ? 'Marcar como Disponible' : 'Marcar como Ocupada' }}
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-10">
                            No hay mesas registradas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- CÓDIGO DE LA NOTIFICACIÓN EMERGENTE -->
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
</div>
