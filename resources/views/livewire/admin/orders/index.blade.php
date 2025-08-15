<div class="px-8 sm:px-12 lg:px-16 pb-12">

    <!-- CABECERA CON FILTROS INTEGREADOS -->
    <div class="bg-white p-6 rounded-lg shadow-sm mt-8 mb-6">
        <div class="flex flex-col md:flex-row justify-between md:items-center">
            <h1 class="text-3xl font-semibold text-black mb-4 md:mb-0">Órdenes</h1>
        </div>

        <!-- Controles de Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mt-4">
            <!-- Filtro por Modalidad -->
            <div>
                <label for="filtroModalidad" class="block text-sm font-medium text-gray-700">Modalidad</label>
                <select wire:model.live="filtroModalidad" id="filtroModalidad" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Todas</option>
                    @foreach($modalidades as $modalidad)
                        <option value="{{ $modalidad['id'] }}">{{ $modalidad['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro por Estado -->
            <div>
                <label for="filtroEstado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select wire:model.live="filtroEstado" id="filtroEstado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado['id'] }}">{{ $estado['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro por Fecha de Inicio -->
            <div>
                <label for="filtroFechaInicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                <input type="date" wire:model.live="filtroFechaInicio" id="filtroFechaInicio" class="mt-1 block w-full pl-3 pr-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>

            <!-- Filtro por Fecha de Fin -->
            <div>
                <label for="filtroFechaFin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                <input type="date" wire:model.live="filtroFechaFin" id="filtroFechaFin" class="mt-1 block w-full pl-3 pr-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>
            
            <!-- Botón para limpiar -->
            <div>
                <button wire:click="resetFilters" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Pedido</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Modalidad</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><div class="text-sm font-bold text-gray-900">#{{ $order['id'] }}</div></td>
                        <td class="px-6 py-4"><div class="text-sm font-semibold text-gray-900">${{ number_format($order['totalPedido'], 2) }}</div></td>
                        <td class="px-6 py-4"><div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order['createdAt'])->format('d/m/y h:i A') }}</div></td>
                        <td class="px-6 py-4"><div class="text-sm text-gray-500">{{ $order['modalidad'] }}</div></td>
                        <td class="px-6 py-4">
                            @php
                                $estado = $order['estadoPedido'];
                                $colorClasses = [
                                    'En preparación' => 'bg-yellow-100 text-yellow-800',
                                    'Listo para entregar' => 'bg-blue-100 text-blue-800',
                                    'Listo para recoger' => 'bg-blue-100 text-blue-800',
                                    'Entregado' => 'bg-green-100 text-green-800',
                                    'Completado' => 'bg-green-100 text-green-800',
                                    'Cancelado' => 'bg-red-100 text-red-800',
                                    'Pendiente' => 'bg-gray-100 text-gray-800',
                                ][$estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClasses }}">
                                {{ $estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', ['id' => $order['id']]) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                Ver Detalles
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center p-6 text-gray-500">No hay órdenes que coincidan con los filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
