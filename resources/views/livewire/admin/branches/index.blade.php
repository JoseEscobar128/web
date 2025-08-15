<div x-data="{ showConfirmModal: @entangle('showConfirmModal') }">
    <div class="px-8 sm:px-12 lg:px-16 pb-12">

        <div class="bg-white p-6 rounded-lg shadow-sm mt-8 mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-semibold font-['Roboto'] text-black">Sucursales</h1>
            
            <!-- Buscador por Nombre -->
            <div class="relative w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Buscar por nombre..."
                       class="block w-full pl-10 pr-3 py-2 bg-white rounded-lg border border-neutral-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-stone-300">
            </div>

            <a href="{{ route('admin.branches.create') }}" wire:navigate
               class="flex items-center bg-[#922F06] text-white font-bold py-2 px-4 rounded-lg hover:bg-[#7A2805] transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Sucursal
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dirección
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ciudad
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Teléfono
                        </th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($branches as $branch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $branch['nombre'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $branch['direccion'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $branch['ciudad'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ $branch['telefono'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.branches.edit', ['id' => $branch['id']]) }}" wire:navigate>
                                    <button
                                        class="inline-flex items-center font-bold py-1 px-3 rounded-md text-xs text-[#12991F] hover:bg-green-50"
                                        title="Editar">
                                        <img src="{{ asset('icons/edit.svg') }}" alt="Editar" class="w-4 h-4 mr-1"><span>Editar</span>
                                    </button>
                                </a>
                                <button wire:click="confirmBranchDeletion({{ $branch['id'] }})"
                                    class="inline-flex items-center font-bold py-1 px-3 rounded-md text-xs text-[#F67F20] hover:bg-orange-50 transition-colors ml-2"
                                    title="Eliminar">
                                    <img src="{{ asset('icons/delete.svg') }}" alt="Eliminar" class="w-4 h-4 mr-1"><span>Eliminar</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center px-6 py-4 text-gray-500">No se encontraron sucursales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Confirmación de eliminación --}}
        <div x-show="showConfirmModal" x-transition @keydown.escape.window="showConfirmModal = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
                <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-xl font-bold text-gray-900">Confirmar Eliminación</h3>
                    <p class="mt-2 text-gray-600">
                        ¿Estás seguro de que quieres eliminar esta sucursal? Esta acción es permanente y no se puede deshacer.
                    </p>
                </div>
                <div class="mt-6 flex justify-center space-x-4">
                    <button @click="showConfirmModal = false"
                            class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-semibold">
                        Cancelar
                    </button>
                    <button wire:click="deleteBranch" @click="showConfirmModal = false"
                            class="px-6 py-2 bg-terracota text-white font-bold rounded-md hover:bg-opacity-90">
                        Sí, Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>