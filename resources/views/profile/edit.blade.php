<x-app-layout>
    @section('title', 'Pengaturan Profil Akun')

    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Pengaturan Profil Akun
            </h2>
            <p class="text-gray-500 text-sm mt-0.5">Kelola informasi data diri, pembaruan kata sandi keamanan, dan privasi akun Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Blok 1: Formulir Pembaruan Informasi Profil Dasar --}}
            <div class="p-6 sm:p-8 bg-white shadow-sm rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Blok 2: Formulir Modifikasi Keamanan Kata Sandi --}}
            <div class="p-6 sm:p-8 bg-white shadow-sm rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Blok 3: Zona Bahaya Penghapusan Akun Permanen --}}
            <div class="p-6 sm:p-8 bg-white shadow-sm rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>