<x-app-layout>
    <div class="w-full h-full p-6 bg-mostaza">
        <div class="flex h-full items-center space-x-6 pr-16">

            <div class="w-3/5 flex items-center justify-center">
                <img src="{{ asset('images/registeruser.png') }}" alt="Ilustración de registro de usuario" class="w-full max-w-xl mr-20">
            </div>

            <div>
                <h2 class="text-black text-2xl font-bold mb-4">Registro de usuario</h2>

                <form class="mb-8">
                    <div class="w-[600px] h-[400px] bg-black/50 backdrop-blur-sm p-8 rounded-[20px] shadow-lg">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label for="name" class="block text-white font-semibold mb-1">Nombres</label>
                                <input type="text" id="name" placeholder="Nombres" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            </div>

                            <div>
                                <label for="last_name" class="block text-white font-semibold mb-1">Apellidos</label>
                                <input type="text" id="last_name" placeholder="Apellidos" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            </div>

                            <div>
                                <label for="email" class="block text-white font-semibold mb-1">Email</label>
                                <input type="email" id="email" placeholder="Email" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            </div>
                            
                            <div>
                                <label for="role" class="block text-white font-semibold mb-1">Rol</label>
                                <select id="role" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
                                    <option>Selecciona un rol</option>
                                    <option value="1">Superadmin</option>
                                    <option value="2">Administrador</option>
                                    <option value="3">Cocinero</option>
                                </select>
                            </div>

                            <div>
                                <label for="password" class="block text-white font-semibold mb-1">Contraseña</label>
                                <input type="password" id="password" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-white font-semibold mb-1">Confirmar Contraseña</label>
                                <input type="password" id="password_confirmation" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
                            </div>
                            
                            <div>
                                <label for="status" class="block text-white font-semibold mb-1">Estatus</label>
                                <select id="status" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-white font-semibold mb-1">Seleccionar empleado</label>
                                <select id="status" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
                                    <option value="0"></option>
                                    <option value="1">Empleado</option>
                                </select>
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
</x-app-layout>

