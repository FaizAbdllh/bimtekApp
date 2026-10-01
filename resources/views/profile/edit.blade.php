<x-app-layout>
    @section('title', 'Pengaturan Profil Akun')

    <x-slot name="header">
        Pengaturan Profil Akun
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <p class="text-sm text-gray-500">Kelola informasi data diri, pembaruan kata sandi keamanan, dan privasi akun Anda</p>
            
            {{-- Blok 1: Formulir Pembaruan Informasi Profil Dasar --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Blok 2: Formulir Modifikasi Keamanan Kata Sandi --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Blok 3: Zona Bahaya Penghapusan Akun Permanen --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>