<div class="w-full h-full p-6 bg-mostaza">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Tarjeta 1: Ingreso Total -->
        <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col items-center justify-center">
            <h3 class="text-xl font-bold text-gray-800">Ingreso Total del Día</h3>
            <p class="text-5xl font-bold text-gray-900 mt-4">
                ${{ number_format($summary['total_ingresos'] ?? 0, 2) }}
            </p>
        </div>

        <!-- Tarjeta 2: Pedidos de Hoy -->
        <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col items-center justify-center">
            <h3 class="text-xl font-bold text-gray-800">Pedidos de Hoy</h3>
            <p class="text-5xl font-bold text-gray-900 mt-4">
                {{ $summary['pedidos_hoy'] ?? 0 }}
            </p>
        </div>

        <!-- Tarjeta 3: Estado de Mesas -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-bold text-gray-800">Estado de Mesas</h3>
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg me-3">
                        <svg class="w-6 h-6 text-red-600" xmlns="http://www.w.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    </div>
                    <div>
                        <p class="text-gray-500">Mesas ocupadas</p>
                        <p class="font-bold text-2xl">{{ $summary['mesas_ocupadas'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg me-3">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 18.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    </div>
                    <div>
                        <p class="text-gray-500">Mesas totales</p>
                        <p class="font-bold text-2xl">{{ $summary['mesas_totales'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Más vendidos -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-bold text-gray-800">Más Vendidos</h3>
            <div class="mt-4 space-y-4">
                @forelse ($summary['mas_vendidos'] ?? [] as $producto)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="{{ $producto['imagen_principal']['url_imagen'] ?? 'https://placehold.co/40' }}" alt="{{ $producto['nombre'] }}" class="w-10 h-10 rounded-lg object-cover me-4">
                            <div>
                                <p class="font-bold text-sm">{{ $producto['nombre'] }}</p>
                                <p class="text-xs text-gray-500">${{ number_format($producto['precio'], 2) }}</p>
                            </div>
                        </div>
                        {{-- <span class="font-bold text-gray-700">500</span> --}}
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No hay datos de productos para mostrar.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>