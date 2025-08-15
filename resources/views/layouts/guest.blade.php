<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
        @livewireStyles
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-48 h-48 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-5 px-8 py-7 bg-input-rect shadow-md overflow-hidden sm:rounded-lg">
                {{-- Aquí es donde se inserta el contenido de tu vista de login --}}
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>