<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-500 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="px-6 py-5 sm:px-8 sm:py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-white">
                    <h2 class="text-xl sm:text-2xl font-bold">Selamat datang, {{ $user->name }}!</h2>
                    <p class="text-primary-100 mt-1">Anda login sebagai <span class="font-semibold text-white">{{ $role }}</span></p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-white text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Dashboard Content berdasarkan Role --}}
    @if($user->isAdminIt())
        @include('dashboard.partials.admin-it')
    @elseif($user->isKepala())
        @include('dashboard.partials.kepala')
    @elseif($user->isPpk())
        @include('dashboard.partials.ppk')
    @elseif($user->isRt())
        @include('dashboard.partials.koordinator-rt')
    @elseif($user->isPersuratan())
        @include('dashboard.partials.persuratan')
    @elseif($user->isPegawaiInternal())
        @include('dashboard.partials.pegawai-internal')
    @elseif($user->isPesertaEksternal())
        @include('dashboard.partials.peserta-eksternal')
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-xl">
            <div class="p-6 text-gray-900">
                {{ __("Selamat datang di Sistem Informasi Bimtek BBPMP Sumbar!") }}
            </div>
        </div>
    @endif
</x-app-layout>
