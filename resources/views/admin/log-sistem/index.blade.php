<x-app-layout>
    @section('title', 'Log Sistem')

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Log Sistem Aplikasi
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Pantau rekaman aktivitas berkala dan audit trail error pada sistem DIPA</p>
            </div>
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide shrink-0">
                <a href="{{ route('admin.log-sistem.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor CSV
                </a>
                @if($statistics['total'] > 0)
                    <button type="button" onclick="document.getElementById('modal-clear').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Kosongkan Log
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Flash Message Alerts --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-800 rounded-xl text-xs font-semibold shadow-sm flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl text-xs font-semibold shadow-sm flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Widgets Metrik Penghitung Log --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 text-sm">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Record</p>
                    <p class="text-2xl font-black text-gray-900 mt-0.5">{{ number_format($statistics['total']) }}</p>
                </div>
                <div class="bg-blue-50/50 rounded-xl shadow-sm border border-blue-100 p-5">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Metrik Info</p>
                    <p class="text-2xl font-black text-blue-700 mt-0.5">{{ number_format($statistics['info']) }}</p>
                </div>
                <div class="bg-amber-50/50 rounded-xl shadow-sm border border-amber-100 p-5">
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Peringatan</p>
                    <p class="text-2xl font-black text-amber-700 mt-0.5">{{ number_format($statistics['warning']) }}</p>
                </div>
                <div class="bg-red-50/50 rounded-xl shadow-sm border border-red-100 p-5">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Sistem Error</p>
                    <p class="text-2xl font-black text-red-700 mt-0.5">{{ number_format($statistics['error']) }}</p>
                </div>
                <div class="bg-green-50/50 rounded-xl shadow-sm border border-green-100 p-5">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Log Hari Ini</p>
                    <p class="text-2xl font-black text-green-700 mt-0.5">{{ number_format($statistics['today']) }}</p>
                </div>
            </div>

            {{-- Panel Parameter Penyaringan (Filter Form) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form action="{{ route('admin.log-sistem.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-sm font-semibold text-gray-700">
                    <div class="flex-1 min-w-[240px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari potongan deskripsi pesan..." class="w-full px-4 py-2 border border-gray-300 rounded-xl text-xs font-medium focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                    </div>
                    <div class="w-40">
                        <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:ring-primary-500 shadow-sm">
                            <option value="">Semua Level</option>
                            <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:ring-primary-500 shadow-sm">
                            <option value="">Semua Aktor User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ Str::limit($user->name, 22) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-36">
                        <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full px-3 py-1.5 border border-gray-300 rounded-xl text-xs font-medium focus:ring-primary-500 shadow-sm">
                    </div>
                    <div class="w-36">
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full px-3 py-1.5 border border-gray-300 rounded-xl text-xs font-medium focus:ring-primary-500 shadow-sm">
                    </div>
                    <div class="flex items-center gap-1.5 font-bold text-xs uppercase tracking-wide shrink-0">
                        <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition shadow-sm">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.log-sistem.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABEL UTAMA REKAMAN AUDIT LOG --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="p-12 text-center text-gray-400 font-medium">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-xs text-gray-400">Tidak ditemukan adanya rekaman jejak log sistem yang sesuai kriteria.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left w-44">Waktu Kejadian</th>
                                    <th class="px-4 py-3 text-center w-28">Tingkat Urgensi</th>
                                    <th class="px-6 py-3 text-left w-48">Aktor Eksekutor</th>
                                    <th class="px-6 py-3 text-left">Pesan Log / Jejak Error</th>
                                    <th class="px-6 py-3 text-right pr-6 w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">
                                            <div class="font-bold text-gray-800">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                        </td>
                                        <td class="px-4 py-4 text-center whitespace-nowrap align-middle">
                                            @if($log->level == 'info')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 border border-blue-100 text-blue-700">
                                                    INFO
                                                </span>
                                            @elseif($log->level == 'warning')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700">
                                                    WARN
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 animate-pulse">
                                                    CRIT ERROR
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap whitespace-nowrap font-semibold">
                                            @if($log->user)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-primary-50 text-primary-700 border border-primary-100/40 flex items-center justify-center text-[10px] font-bold">
                                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-gray-900 truncate max-w-[140px]">{{ $log->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic font-medium">Automated System</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium break-all max-w-md">
                                            {{ Str::limit($log->pesan, 90) }}
                                        </td>
                                        <td class="px-6 py-4 text-right pr-6 whitespace-nowrap align-middle">
                                            <div class="flex items-center justify-end gap-1.5 font-bold text-xs uppercase tracking-wide">
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute detail log --}}
                                                <a href="{{ route('admin.log-sistem.show', $log->id) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Lihat Rincian Parameter">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                {{-- REFAKTORISASI: Kestabilan ID parameter rute hapus log --}}
                                                <form action="{{ route('admin.log-sistem.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus baris rekam jejak log audit ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg transition" title="Hapus Record">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Footer Navigator --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/20">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL COMPONENT WINDOW: Pembersihan Total Record Log (Zona Bahaya Mandatori) --}}
    <div id="modal-clear" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-clear').classList.add('hidden')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-100 relative text-left">
                <button type="button" onclick="document.getElementById('modal-clear').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="text-center mb-5">
                    <div class="w-14 h-14 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Wipe-Out Semua Data Log?</h3>
                    <p class="text-xs text-gray-400 font-medium leading-relaxed">Tindakan ini akan mengosongkan total keseluruhan <strong>{{ number_format($statistics['total']) }}</strong> baris log audit dari basis data. Data yang terhapus tidak dapat dikembalikan lagi.</p>
                </div>

                <form action="{{ route('admin.log-sistem.clear-all') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="confirm-text" class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Ketik teks <strong class="text-red-600 font-black">HAPUS SEMUA LOG</strong> untuk konfirmasi:</label>
                        <input type="text" name="confirm" id="confirm-text" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-bold tracking-wide focus:ring-2 focus:ring-red-500 focus:border-red-500 text-center uppercase" placeholder="HAPUS SEMUA LOG">
                    </div>
                    <div class="flex gap-3 font-bold text-xs uppercase tracking-wide">
                        <button type="button" onclick="document.getElementById('modal-clear').classList.add('hidden')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition shadow-sm shadow-red-50">
                            Wipe-Out Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>