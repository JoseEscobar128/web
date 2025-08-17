<div>
    <div class="w-full h-full p-6 bg-mostaza">
        <div class="flex h-full items-center space-x-6 pr-16">

            <div class="w-2/5 flex items-center justify-center">
                <img src="{{ asset('images/registeruser.png') }}" alt="Ilustración de perfil de usuario" class="w-full max-w-sm">
            </div>

            <div class="w-3/5 space-y-6">
                
                @php
                    $userRole = session('user')->rol ?? null;
                @endphp

                {{-- Dump para depurar --}}
                <div class="mb-6 p-4 bg-gray-100 border rounded">
                    <h2 class="font-bold mb-2">Debug info</h2>
                    @dump($usuario, $email)
                </div>

                <!-- Tarjeta para actualizar información del perfil -->
                <div class="bg-black/50 backdrop-blur-sm p-6 rounded-[20px] shadow-lg">
                    <header>
                        <h2 class="text-xl font-bold text-white">Información del Perfil</h2>
                        <p class="mt-1 text-sm text-gray-300">Actualiza la información de tu cuenta.</p>
                    </header>

                    <form wire:submit.prevent="updateProfileInformation" class="mt-6 space-y-4">
                        <div>
                            <label for="usuario" class="block font-semibold text-white mb-1">Usuario</label>
                            <input wire:model="usuario" id="usuario" type="text" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('usuario') ring-2 ring-red-500 @enderror" required autofocus>
                            @error('usuario') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC']))
                            <div>
                                <label for="email" class="block font-semibold text-white mb-1">Email</label>
                                <input wire:model="email" id="email" type="email" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('email') ring-2 ring-red-500 @enderror" required>
                                @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="bg-terracota text-white font-bold py-2 px-4 rounded-md hover:bg-opacity-90 transition-colors">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC']))
                    <div class="bg-black/50 backdrop-blur-sm p-6 rounded-[20px] shadow-lg">
                        <header>
                            <h2 class="text-xl font-bold text-white">Actualizar Contraseña</h2>
                        </header>

                        <form wire:submit.prevent="updatePassword" class="mt-6 space-y-4">
                            <div>
                                <label for="password" class="block font-semibold text-white mb-1">Nueva Contraseña</label>
                                <input wire:model="password" id="password" type="password" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('password') ring-2 ring-red-500 @enderror" required>
                                @error('password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block font-semibold text-white mb-1">Confirmar Contraseña</label>
                                <input wire:model="password_confirmation" id="password_confirmation" type="password" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza @error('password') ring-2 ring-red-500 @enderror" required>
                            </div>
                            <div class="flex items-center gap-4 pt-2">
                                <button type="submit" class="bg-terracota text-white font-bold py-2 px-4 rounded-md hover:bg-opacity-90 transition-colors">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

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
