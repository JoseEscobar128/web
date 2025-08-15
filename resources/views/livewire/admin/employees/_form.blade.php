{{-- Este div contiene todos los campos del formulario --}}
<div class="grid grid-cols-2 gap-x-8 gap-y-4">
    {{-- Nombre --}}
    <div>
        <label for="nombre" class="block text-white font-semibold mb-1">Nombres</label>
        <input wire:model="nombre" type="text" id="nombre" placeholder="Nombre(s) del empleado" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('nombre') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Apellido Paterno --}}
    <div>
        <label for="apellido_paterno" class="block text-white font-semibold mb-1">Apellido Paterno</label>
        <input wire:model="apellido_paterno" type="text" id="apellido_paterno" placeholder="Apellido paterno" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('apellido_paterno') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Apellido Materno --}}
    <div>
        <label for="apellido_materno" class="block text-white font-semibold mb-1">Apellido Materno</label>
        <input wire:model="apellido_materno" type="text" id="apellido_materno" placeholder="Apellido materno" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('apellido_materno') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Teléfono --}}
    <div>
        <label for="telefono" class="block text-white font-semibold mb-1">Teléfono</label>
        <input wire:model="telefono" type="tel" id="telefono" placeholder="10 dígitos" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('telefono') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- RFC --}}
    <div>
        <label for="rfc" class="block text-white font-semibold mb-1">RFC</label>
        <input wire:model="rfc" type="text" id="rfc" placeholder="RFC del empleado" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('rfc') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- CURP --}}
    <div>
        <label for="curp" class="block text-white font-semibold mb-1">CURP</label>
        <input wire:model="curp" type="text" id="curp" placeholder="CURP del empleado" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('curp') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- NSS --}}
    <div>
        <label for="nss" class="block text-white font-semibold mb-1">NSS</label>
        <input wire:model="nss" type="text" id="nss" placeholder="NSS del empleado" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza">
        @error('nss') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Fecha de Contratación --}}
    <div>
        <label for="fecha_contratacion" class="block text-white font-semibold mb-1">Fecha de Contratación</label>
        <input wire:model="fecha_contratacion" type="date" id="fecha_contratacion" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-500">
        @error('fecha_contratacion') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Estatus --}}
    <div class="col-span-2">
        <label for="estatus" class="block text-white font-semibold mb-1">Estatus</label>
        <select wire:model="estatus" id="estatus" class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-mostaza text-gray-700">
            <option value="activo">Activo</option>
            <option value="baja">Baja</option>
        </select>
        @error('estatus') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
    </div>
</div>