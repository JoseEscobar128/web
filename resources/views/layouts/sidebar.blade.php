<div 
    x-data="{
        open: JSON.parse(localStorage.getItem('sidebarOpen')) ?? true,
        toggleSidebar() {
            this.open = !this.open;
            localStorage.setItem('sidebarOpen', this.open);
        }
    }"
>
    {{-- Contenedor principal que ahora ocupa toda la altura de la pantalla y controla la estructura --}}
    <div :class="open ? 'w-64' : 'w-20'" class="h-screen flex flex-col bg-white border-r border-gray-200 transition-all duration-300 ease-in-out">
        
        {{-- SECCIÓN SUPERIOR (FIJA): Logo --}}
        <div class="flex-shrink-0 p-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex justify-center mb-4">
                <x-application-logo class="w-12 h-12" />
            </a>
        </div>

        {{-- Obtenemos el rol del usuario desde la sesión para usarlo en las condiciones --}}
        @php
            $userRole = session('user')->rol ?? null;
        @endphp

        {{-- SECCIÓN CENTRAL (CON SCROLL): Navegación principal --}}
        <nav class="flex-1 overflow-y-auto px-2 space-y-2 text-sm font-medium text-gray-600">
            <p x-show="open" class="px-3 pt-2 text-xs font-semibold uppercase tracking-wider text-gray-400">General</p>

            {{-- Visible para todos los roles internos --}}
            <a href="{{ route('dashboard') }}" wire:navigate title="Dashboard"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                <img src="{{ asset('icons/home.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Home Icon">
                <span x-show="open" class="whitespace-nowrap">Inicio</span>
            </a>

            {{-- Visible para Cajero, Admin y Superadmin --}}
            @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC', 'CAJERO']))
                <a href="{{ route('admin.pos.index') }}" wire:navigate title="Punto de Venta"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                       {{ request()->routeIs('admin.pos.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/pos.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="POS Icon">
                    <span x-show="open" class="whitespace-nowrap">Punto de Venta</span>
                </a>
            @endif

            {{-- Visible para Cajero, Admin y Superadmin --}}
            @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC', 'CAJERO']))
                <a href="{{ route('admin.orders.index') }}" wire:navigate title="Órdenes"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.orders.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/order.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Order Icon">
                    <span x-show="open" class="whitespace-nowrap">Órdenes</span>
                </a>
            @endif

            {{-- Visible para Cocinero, Admin y Superadmin --}}
            @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC', 'COCINERO']))
                <a href="{{ route('admin.kitchen.display') }}" wire:navigate title="Cocina"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.kitchen.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/notification.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Notification Icon">
                    <span x-show="open" class="whitespace-nowrap">Cocina</span>
                </a>
            @endif

            {{-- SECCIÓN DE GESTIÓN (Solo para Admin y Superadmin) --}}
            @if(in_array($userRole, ['SUPERADMIN', 'ADMIN_SUC']))
                <p x-show="open" class="px-3 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Gestión</p>

                <a href="{{ route('admin.products.index') }}" wire:navigate title="Productos"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.products.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/product.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Product Icon">
                    <span x-show="open" class="whitespace-nowrap">Productos</span>
                </a>

                <a href="{{ route('admin.users.index') }}" wire:navigate title="Usuarios"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.users.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/customers.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Users Icon">
                    <span x-show="open" class="whitespace-nowrap">Usuarios</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" wire:navigate title="Categorías"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.categories.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/categories.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Categories Icon">
                    <span x-show="open" class="whitespace-nowrap">Categorías</span>
                </a>

                <a href="{{ route('admin.branches.index') }}" wire:navigate title="Sucursales"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.branches.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/branches.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Branches Icon">
                    <span x-show="open" class="whitespace-nowrap">Sucursales</span>
                </a>

                <a href="{{ route('admin.mesas.index') }}" wire:navigate title="Mesas"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.mesas.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/table.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Mesas Icon">
                    <span x-show="open" class="whitespace-nowrap">Mesas</span>
                </a>

                <!-- NUEVO ENLACE PARA ASISTENCIAS -->
                <a href="{{ route('admin.attendance.index') }}" wire:navigate title="Asistencias"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                         {{ request()->routeIs('admin.attendance.*') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                    <img src="{{ asset('icons/attendance.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Asistencias Icon">
                    <span x-show="open" class="whitespace-nowrap">Asistencias</span>
                </a>
            @endif
        </nav>

        {{-- SECCIÓN INFERIOR (FIJA): Sistema y Toggle --}}
        <div class="flex-shrink-0 p-2 border-t border-gray-200">
            <p x-show="open" class="px-3 pt-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Sistema</p>
            
            <a href="{{ route('profile') }}" wire:navigate title="Configuración"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                     {{ request()->routeIs('profile') ? 'bg-mostaza text-white' : 'text-gray-500 hover:bg-mostaza hover:text-white' }}">
                <img src="{{ asset('icons/setting.svg') }}" class="w-6 h-6 object-contain flex-shrink-0" alt="Setting Icon">
                <span x-show="open" class="whitespace-nowrap">Configuración</span>
            </a>

            <button @click="toggleSidebar"
                    class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 text-gray-500 mt-2">
                <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </button>
        </div>
    </div>
</div>