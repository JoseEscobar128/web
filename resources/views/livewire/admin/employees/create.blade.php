<div class="w-full h-full p-6 bg-mostaza">
    <div class="flex h-full items-center space-x-6 pr-16">

        <div class="w-3/5 flex items-center justify-center">
            <img src="{{ asset('images/registeruser.png') }}" alt="Ilustración de registro de empleado" class="w-full max-w-xl mr-20">
        </div>

        <div>
            <h2 class="text-black text-2xl font-bold mb-4">Registrar Empleado</h2>

            <form wire:submit.prevent="saveEmployee" class="mb-8">
                <div class="w-[600px] bg-black/50 backdrop-blur-sm p-8 rounded-[20px] shadow-lg">
                    {{-- Incluimos el formulario parcial --}}
                    @include('livewire.admin.employees._form')
                </div>

                <div class="mt-6 flex justify-center">
                    <button type="submit" class="w-[550px] bg-terracota text-white font-bold py-3 px-4 rounded-md hover:bg-opacity-90 transition-colors">
                        Registrar Empleado
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>