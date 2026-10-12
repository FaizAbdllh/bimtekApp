{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0">
    @auth
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
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                    
                    Dashboard
                </a>
            </div>

            {{-- Menu berdasarkan Role --}}
            @php $user = Auth::user(); @endphp

            {{-- Admin IT Menu --}}
            @if($user->isAdminIt())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Administrasi</p>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Manajemen User
                    </a>
                    <a href="{{ route('admin.log-sistem.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.log-sistem.*') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Log Sistem
                    </a>
                </div>
            @endif

            {{-- Kepala Menu --}}
            @if($user->isKepala())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Persetujuan</p>
                    <a href="{{ route('approval.kepala.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('approval.kepala.*') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Pengajuan Bimtek
                    </a>
                </div>
            @endif

            {{-- PPK Menu --}}
            @if($user->isPpk())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Anggaran</p>
                    <a href="{{ route('approval.ppk.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('approval.ppk.*') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Persetujuan Anggaran
                    </a>
                </div>
            @endif

            {{-- Koordinator RT Menu --}}
            @if($user->isRt())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Rumah Tangga</p>
                    <a href="{{ route('rt.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('rt.*') ? 'bg-primary-700' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Kebutuhan Fasilitas
                    </a>
                </div>
            @endif

            {{-- Pegawai Internal Menu --}}
            @if($user->isPegawaiInternal())
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Pengajuan</p>
                    <a href="{{ route('pengajuan.create') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pengajuan.create') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Ajukan Bimtek
                    </a>
                    <a href="{{ route('pengajuan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') || request()->routeIs('pengajuan.edit') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500/50' }}">
                        
                        Pengajuan Saya
                    </a>
                </div>
            @endif

            {{-- Menu Bimtek - Untuk yang terlibat dalam bimtek --}}
            @php
                // Cek keterlibatan berdasarkan kolom pic_user_id riil atau keanggotaan di tabel bimtek_panitias
                $terlibatDiBimtek = \App\Models\Bimtek::where('pic_user_id', $user->id)
                    ->orWhereHas('panitia', function($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->exists();
            @endphp
            @if($user->isPegawaiInternal() || $user->isAdminIt() || $user->isKepala() || $user->isPpk() || $terlibatDiBimtek)
                <div class="mb-4">
                    <p class="px-3 mb-2 text-xs font-semibold text-primary-300 uppercase tracking-wider">Bimtek</p>
                    <a href="{{ route('bimtek.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('bimtek.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                        
                        Kelola Bimtek
                    </a>
                    @if($user->isAdminIt() || $user->isKepala() || $user->isPpk() || $terlibatDiBimtek)
                        <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('laporan.*') ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-500' }}">
                            
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
    @endauth
</aside>
