<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa Fácil - Iniciar Sesión</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        
        <div class="w-full max-w-sm bg-white dark:bg-gray-200 p-8 rounded-2xl shadow-xl text-center">

            <img src="{{ asset('mesa-facil-logo.png') }}" alt="Logo de Mesa Fácil" class="w-32 mx-auto mb-6">

            <h1 class="text-2xl font-bold text-[#0F38A1] mb-2">
                Panel de Control
            </h1>

            <p class="text-gray-800 dark:text-gray-800 mb-8">
                Por favor, inicia sesión para continuar.
            </p>

            {{-- MENSAJE DE ERROR --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <a href="{{ route('login') }}"
               class="inline-block w-full bg-[#D78D16] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#C27C14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D78D16] transition-colors duration-300">
                Iniciar Sesión
            </a>
            
        </div>

        <footer class="text-center mt-8 text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Mesa Fácil. Todos los derechos reservados.</p>
        </footer>

    </div>
</body>
</html>
