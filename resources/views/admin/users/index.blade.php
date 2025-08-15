<x-app-layout>
    {{-- Contenedor principal con algo de padding --}}
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Aquí se carga el componente de Livewire --}}
        @livewire('admin.users.index')
    </div>
</x-app-layout>