<div class="px-8 sm:px-12 lg:px-16 pb-12">

    <!-- CABECERA CON FILTROS INTEGREADOS -->
    <div class="bg-white p-6 rounded-lg shadow-sm mt-8 mb-6">
        <div class="flex flex-col md:flex-row justify-between md:items-center">
            <h1 class="text-3xl font-semibold text-black mb-4 md:mb-0">Registros de Asistencia</h1>
        </div>

        <!-- Controles de Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mt-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Buscar por Empleado</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search"
                           placeholder="Nombre o apellido del empleado..."
                           class="block w-full pl-10 pr-3 py-2 bg-white rounded-lg border border-neutral-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-stone-300">
                </div>
            </div>

            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                <input type="date" wire:model.live="fecha_inicio" id="fecha_inicio" class="mt-1 block w-full pl-3 pr-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                <input type="date" wire:model.live="fecha_fin" id="fecha_fin" class="mt-1 block w-full pl-3 pr-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
             <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Limpiar Filtros
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Empleado</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo de Registro</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($asistencias as $asistencia)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $asistencia['empleado']['nombre'] ?? 'N/A' }} {{ $asistencia['empleado']['apellido_paterno'] ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($asistencia['fecha_hora'])->format('d/m/Y h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($asistencia['tipo_registro'] == 'Entrada')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Entrada
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Salida
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center p-6 text-gray-500">No hay registros de asistencia que coincidan con los filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Controles de Paginación -->
        @if ($total > $perPage)
            <div class="px-6 py-4 bg-white border-t">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-700">
                        Mostrando {{ count($asistencias) }} de {{ $total }} resultados
                    </p>
                    <div class="flex items-center space-x-2">
                        <button wire:click="goToPage(1)" @if($currentPage == 1) disabled @endif class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Primera
                        </button>
                        <button wire:click="goToPage({{ $currentPage - 1 }})" @if($currentPage <= 1) disabled @endif class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-700">Página {{ $currentPage }} de {{ $lastPage }}</span>
                        <button wire:click="goToPage({{ $currentPage + 1 }})" @if($currentPage >= $lastPage) disabled @endif class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Siguiente
                        </button>
                        <button wire:click="goToPage({{ $lastPage }})" @if($currentPage == $lastPage) disabled @endif class="px-3 py-1 text-sm rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Última
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>