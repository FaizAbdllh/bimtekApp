<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SI Bimtek BBPMP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col">
            {{-- Header dengan background biru --}}
            <div class="bg-gradient-to-br from-primary-600 via-primary-600 to-primary-500 relative overflow-hidden">
                {{-- Decorative shapes --}}
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary-500 opacity-50 transform rotate-45"></div>
                    <div class="absolute top-20 -left-10 w-32 h-32 bg-primary-700 opacity-30 transform rotate-12"></div>
                    <div class="absolute bottom-0 right-1/4 w-24 h-24 bg-primary-400 opacity-20 transform -rotate-12"></div>
                </div>
                
                {{-- Logo dan Judul --}}
                <div class="relative z-10 flex flex-col items-center justify-center py-8 sm:py-12">
                    <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" class="h-16 sm:h-20 w-16 sm:w-20 mb-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">BBPMP</h1>
                    <p class="text-primary-200 text-sm sm:text-base">Sumatera Barat</p>
                </div>
            </div>

            {{-- Form Container --}}
            <div class="flex-1 flex items-start justify-center bg-gray-100 px-4 py-8 -mt-6">
                <div class="w-full sm:max-w-md bg-white shadow-lg rounded-lg overflow-hidden">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-100 py-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} BBPMP Sumatera Barat. All rights reserved.
            </div>
        </div>
    </body>
</html>
