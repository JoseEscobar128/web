<div>
    <div class="w-full h-full p-6 bg-mostaza">
        <div class="bg-cafe-rustico p-8 rounded-2xl shadow-lg max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-white text-2xl font-bold">Editar Producto</h2>
                <a href="{{ route('admin.products.index') }}" wire:navigate class="text-sm text-white underline hover:opacity-80">
                    &larr; Volver a la lista
                </a>
            </div>

            <form wire:submit.prevent="updateProduct">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div x-data="{ isUploading: false, progress: 0 }"
                         x-on:livewire-upload-start="isUploading = true"
                         x-on:livewire-upload-finish="isUploading = false"
                         x-on:livewire-upload-error="isUploading = false"
                         x-on:livewire-upload-progress="progress = $event.detail.progress"
                         class="md:col-span-1"
                    >
                        {{-- Mostrar imagen (Lógica corregida) --}}
                        @if ($new_image)
                            <img src="{{ $new_image->temporaryUrl() }}" class="w-full h-48 object-cover rounded-lg shadow-md">
                        @elseif (!empty($imagen_principal[0]))
    			<img src="{{ asset('storage/images/' . $imagen_principal[0]) }}" class="w-full h-48 object-cover rounded-lg shadow-md">
                        @else
                            <div class="w-full h-48 bg-gray-700/50 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400">
                                <span class="text-sm text-gray-300">Sin imagen</span>
                            </div>
                        @endif

                        <label for="new_image_input" class="cursor-pointer mt-4 block text-center bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-md hover:bg-gray-300">
                            Cambiar imagen
                        </label>
                        <input wire:model="new_image" type="file" id="new_image_input" class="sr-only">

                        <div x-show="isUploading" class="mt-2">
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-500 rounded-full transition-all duration-150 ease-in-out" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Campos del formulario --}}
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label for="nombre" class="block text-white mb-1">Nombre</label>
                            <input wire:model="nombre" type="text" id="nombre" class="w-full rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            @error('nombre') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="precio" class="block text-white mb-1">Precio</label>
                            <input wire:model="precio" type="text" id="precio" class="w-full rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            @error('precio') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="categoria" class="block text-white mb-1">Categoría</label>
                            <select wire:model="categoria_id" id="categoria" class="w-full rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
                                <option value="">Seleccionar</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['nombre'] }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label for="sucursal" class="block text-white mb-1">Sucursal</label>
                            <select wire:model="sucursal_id" id="sucursal" class="w-full rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
                                <option value="">Seleccionar</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal['id'] }}">{{ $sucursal['nombre'] }}</option>
                                @endforeach
                            </select>
                            @error('sucursal_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label for="descripcion" class="block text-white mb-1">Descripción</label>
                            <textarea wire:model="descripcion" id="descripcion" rows="3" class="w-full rounded-md border-none focus:ring-2 focus:ring-mostaza"></textarea>
                            @error('descripcion') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-terracota text-white font-bold py-3 px-6 rounded-md hover:bg-opacity-90">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

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
