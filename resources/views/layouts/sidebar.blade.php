{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0">
    <div class="h-full flex flex-col bg-primary-600 text-white">
        {{-- Logo & Brand --}}
        <div class="flex items-center justify-center px-4 py-5 border-b border-primary-500">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-bbpmp.png') }}" alt="Logo BBPMP" class="h-12 w-12">
                <div>
                    <h1 class="text-lg font-bold leading-tight">BBPMP</h1>
                    <p class="text-xs text-primary-200">Sumatera Barat</p>
                </div>
            </a>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">
            {{-- Dashboard - Semua Role --}}
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>
            </div>

            {{-- Menu berdasarkan Role --}}
            @php $user = Auth::user(); @endphp

            {{-- Admin IT Menu --}}
            @if($user->isAdminIt())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Administrasi</p>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Manajemen User
                    </a>
                    <a href="{{ route('admin.log-sistem.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.log-sistem.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Log Sistem
                    </a>
                </div>
            @endif

            {{-- Kepala Menu --}}
            @if($user->isKepala())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Persetujuan</p>
                    <a href="{{ route('approval.kepala.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('approval.kepala.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Pengajuan Bimtek
                    </a>
                </div>
            @endif

            {{-- PPK Menu --}}
            @if($user->isPpk())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Anggaran</p>
                    <a href="{{ route('approval.ppk.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('approval.ppk.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Persetujuan Anggaran
                    </a>
                </div>
            @endif

            {{-- Koordinator RT Menu --}}
            @if($user->isRt())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Rumah Tangga</p>
                    <a href="{{ route('rt.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('rt.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Kebutuhan Fasilitas
                    </a>
                </div>
            @endif

            {{-- Pegawai Internal Menu --}}
            @if($user->isPegawaiInternal())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Pengajuan</p>
                    <a href="{{ route('pengajuan.create') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pengajuan.create') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ajukan Bimtek
                    </a>
                    <a href="{{ route('pengajuan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') || request()->routeIs('pengajuan.edit') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Pengajuan Saya
                    </a>
                </div>
            @endif

            {{-- Menu Bimtek - Untuk yang terlibat dalam bimtek --}}
            @php
                // Cek apakah user terlibat dalam bimtek (sebagai PIC, Panitia, Pemateri - BUKAN Peserta)
                $terlibatDiBimtek = \App\Models\Bimtek::where('pic_user_id', $user->id)
                    ->orWhereHas('users', function($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->whereIn('peran_kontekstual', ['pic', 'panitia', 'pemateri']);
                    })
                    ->orWhereHas('pengajuan', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->exists();
            @endphp
            @if($user->isPegawaiInternal() || $user->isAdminIt() || $user->isKepala() || $user->isPpk() || $terlibatDiBimtek)
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Bimtek</p>
                    <a href="{{ route('bimtek.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('bimtek.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Kelola Bimtek
                    </a>
                    @if($user->isAdminIt() || $user->isKepala() || $user->isPpk() || $terlibatDiBimtek)
                        <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('laporan.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Laporan
                        </a>
                    @endif
                </div>
            @endif

            {{-- Menu Peserta Eksternal --}}
            @if($user->isPesertaEksternal())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Bimtek Saya</p>
                    <a href="{{ route('bimtek.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('bimtek.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Daftar Bimtek
                    </a>
                </div>
            @endif
        </nav>

        {{-- Sidebar Footer --}}
        <div class="px-3 py-4 border-t border-primary-500">
            <div class="flex items-center px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white truncate" style="max-width: 140px;">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-primary-300">{{ Auth::user()->role->nama_peran ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
