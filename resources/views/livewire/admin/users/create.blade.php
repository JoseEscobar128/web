<div>
    <div class="w-full h-full p-6 bg-mostaza">
        <div class="flex h-full items-center space-x-6 pr-16">
            <div class="w-3/5 flex items-center justify-center">
                <img src="{{ asset('images/registeruser.png') }}" alt="Ilustración de registro de usuario" class="w-full max-w-xl mr-20">
            </div>
            <div>
                <h2 class="text-black text-2xl font-bold mb-4">Registro de usuario</h2>
                <form wire:submit.prevent="saveUser" class="mb-8">
                    <div class="w-[600px] bg-black/50 backdrop-blur-sm p-8 rounded-[20px] shadow-lg">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                            
                            <div class="col-span-2">
                                <label for="usuario" class="block text-white font-semibold mb-1">Usuario</label>
                                <input wire:model.live="usuario" type="text" id="usuario" placeholder="Nombre de usuario" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('usuario') ring-2 ring-red-500 @enderror">
                                @error('usuario') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-white font-semibold mb-1">Email</label>
                                <input wire:model.live="email" type="email" id="email" placeholder="Email" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('email') ring-2 ring-red-500 @enderror">
                                @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="role" class="block text-white font-semibold mb-1">Rol</label>
                                <select wire:model.live="role_id" id="role" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700 @error('role_id') ring-2 ring-red-500 @enderror">
                                    <option value="">Selecciona un rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('role_id') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-white font-semibold mb-1">Contraseña</label>
                                <input wire:model.live="password" type="password" id="password" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('password') ring-2 ring-red-500 @enderror">
                                @error('password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-white font-semibold mb-1">Confirmar Contraseña</label>
                                <input wire:model.live="password_confirmation" type="password" id="password_confirmation" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('password') ring-2 ring-red-500 @enderror">
                            </div>
                            
                            <div>
                                <label for="empleado" class="block text-white font-semibold mb-1">Asignar a Empleado</label>
                                <select wire:model.live="empleado_id" id="empleado" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700 @error('empleado_id') ring-2 ring-red-500 @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee['id'] }}">{{ $employee['nombre'] }} {{ $employee['apellido_paterno'] }}</option>
                                    @endforeach
                                </select>
                                @error('empleado_id') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-white font-semibold mb-1">Estatus</label>
                                <select wire:model.live="status" id="status" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700 @error('status') ring-2 ring-red-500 @enderror">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                @error('status') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button type="submit" class="w-[550px] bg-terracota text-white font-bold py-3 px-4 rounded-md hover:bg-opacity-90 transition-colors">
                            Agregar usuario
                        </button>
                    </div>
                </form>
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