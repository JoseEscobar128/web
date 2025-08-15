{{-- Ya no necesitamos la lógica de Livewire/Volt aquí, la hemos eliminado --}}

<nav class="bg-white border-b border-gray-100">
    <div class="w-full px-4">
        <div class="flex justify-end h-16">
            <div class="flex items-center">

                <!-- ========================================================= -->
                <!-- CAMBIO: El botón ahora está dentro de un formulario POST -->
                <!-- ========================================================= -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="relative text-[15px] capitalize font-semibold font-['Roboto'] text-[#0f38a1] text-left border border-[#0f38a1] rounded-full px-4 py-1 hover:bg-[#0f38a1] hover:text-white transition-colors">
                        Cerrar Sesión
                    </button>
                </form>

            </div>
        </div>
    </div>
</nav>