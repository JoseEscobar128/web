<x-app-layout>
    {{-- Contenedor principal con el color de fondo de tu diseño --}}
    <div class="w-full h-full p-6 bg-mostaza">

        {{-- Usamos un Grid para acomodar las 4 tarjetas principales.
             En pantallas pequeñas será 1 columna, en grandes (lg) serán 2 columnas. --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold text-gray-800">Ingreso Total</h3>
                <div class="flex items-center justify-around mt-4">
                    {{-- Placeholder para la gráfica de dona. Luego la reemplazaremos con Chart.js --}}
                    <div class="w-40 h-40 bg-gray-200 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-700">$80,000</span>
                    </div>
                    {{-- Leyenda de la gráfica --}}
                    <div class="space-y-2">
                        <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-mostaza me-2"></div><span>Entradas</span></div>
                        <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-cafe-rustico me-2"></div><span>Bebidas frias</span></div>
                        <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-gray-300 me-2"></div><span>Otros</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold text-gray-800">Pedidos por hora</h3>
                {{-- Placeholder para la gráfica de línea --}}
                <div class="w-full h-48 mt-4 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500">Gráfica de Línea</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold text-gray-800">Balance Total</h3>
                <p class="text-4xl font-bold text-green-600 mt-4">$1,20,000</p>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-gray-100 rounded-lg me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5l.415-.207a.75.75 0 011.085.67V10.5m0 0h6m-6 0a.75.75 0 001.085.67l.415-.207m-1.5-3.375a.75.75 0 01-1.085.67l-.415-.207a.75.75 0 01-.67-1.085l.415.207a.75.75 0 011.085-.67zM12 21a8.25 8.25 0 100-16.5 8.25 8.25 0 000 16.5z" /></svg>
                        </div>
                        <div>
                            <p class="text-gray-500">Mesas ocupadas</p>
                            <p class="font-bold text-lg">4</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="p-3 bg-gray-100 rounded-lg me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.75A.75.75 0 013 4.5h.75m0 0a.75.75 0 01.75.75v.75m0 0a.75.75 0 01-.75.75h-.75m0 0a.75.75 0 01.75-.75h.75M9 4.5v.75a.75.75 0 01-.75.75h-.75m0 0v-.75a.75.75 0 01.75-.75h.75m0 0a.75.75 0 01.75.75v.75m0 0a.75.75 0 01-.75.75h-.75m0 0a.75.75 0 01.75-.75h.75m-6 12v.75a.75.75 0 01-.75.75h-.75m0 0v-.75a.75.75 0 01.75-.75h.75m0 0a.75.75 0 01.75.75v.75m0 0a.75.75 0 01-.75.75h-.75m0 0a.75.75 0 01.75-.75h.75" /></svg>
                        </div>
                        <div>
                            <p class="text-gray-500">Venta total</p>
                            <p class="font-bold text-lg">$65,500</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold text-gray-800">Más vendidos</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://via.placeholder.com/50" alt="Platillo" class="w-12 h-12 rounded-lg object-cover me-4">
                            <div>
                                <p class="font-bold">Grill Sandwich</p>
                                <p class="text-sm text-gray-500">$30.00</p>
                            </div>
                        </div>
                        <span class="font-bold text-gray-700">500</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://via.placeholder.com/50" alt="Platillo" class="w-12 h-12 rounded-lg object-cover me-4">
                            <div>
                                <p class="font-bold">Chicken Popeyes</p>
                                <p class="text-sm text-gray-500">$20.00</p>
                            </div>
                        </div>
                        <span class="font-bold text-gray-700">800</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>