<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SI Bimtek BBPMP Sumbar') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
            <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" class="h-24 w-auto mb-4">
            <div class="text-center mb-8">
                <p class="text-lg sm:text-2xl text-white font-semibold mb-2 tracking-wide uppercase">Selamat Datang di</p>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-white mb-2 drop-shadow-lg tracking-wide">SISTEM INFORMASI BIMBINGAN TEKNIS</h2>
                <p class="text-base sm:text-xl text-white font-medium mt-4">Balai Besar Penjaminan Mutu Pendidikan <span class="font-semibold">(BBPMP) Sumatera Barat</span></p>
            </div>
            @if (Route::has('login'))
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 bg-primary-700 text-white font-bold rounded-xl shadow-xl hover:bg-primary-800 transition duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-700 font-bold rounded-xl shadow-xl hover:bg-primary-100 transition duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk ke Sistem
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>
