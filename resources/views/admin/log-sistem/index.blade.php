<x-app-layout>
    @section('title', 'Log Sistem')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Log Sistem Aplikasi</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message Alerts --}}
            @if(session('success'))
                <div class="mb-6 p-3 bg-green-50 border border-green-100 text-sm text-green-800 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-3 bg-red-50 border border-red-100 text-sm text-red-800 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Widgets Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Record</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ number_format($statistics['total']) }}</p>
                </div>
                <div class="bg-primary-50 border border-primary-100 rounded-xl shadow-sm p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-primary-800">Info</p>
                    <p class="text-2xl font-semibold text-primary-800 mt-1">{{ number_format($statistics['info']) }}</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-100 rounded-xl shadow-sm p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-yellow-800">Peringatan</p>
                    <p class="text-2xl font-semibold text-yellow-800 mt-1">{{ number_format($statistics['warning']) }}</p>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl shadow-sm p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-red-800">Sistem Error</p>
                    <p class="text-2xl font-semibold text-red-800 mt-1">{{ number_format($statistics['error']) }}</p>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-xl shadow-sm p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-800">Log Hari Ini</p>
                    <p class="text-2xl font-semibold text-green-800 mt-1">{{ number_format($statistics['today']) }}</p>
                </div>
            </div>

            {{-- Filter Form + Tombol Aksi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <form action="{{ route('admin.log-sistem.index') }}" method="GET" class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex-1 min-w-[240px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari potongan deskripsi pesan..." class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="w-40">
                        <select name="level" class="w-full rounded-lg border-gray-300 text-sm bg-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Semua Level</option>
                            <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <select name="user_id" class="w-full rounded-lg border-gray-300 text-sm bg-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Semua Aktor User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ Str::limit($user->name, 22) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-36">
                        <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="w-36">
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.log-sistem.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            Reset
                        </a>
                        <a href="{{ route('admin.log-sistem.export', request()->query()) }}" class="px-4 py-2 bg-white border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            Ekspor CSV
                        </a>
                        @if($statistics['total'] > 0)
                            <button type="button" onclick="document.getElementById('modal-clear').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-sm font-semibold text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Kosongkan Log
                            </button>
                        @endif
                    </div>
                </form>
            </div>
            {{-- Tabel Utama Rekaman Audit Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ditemukan rekaman log sesuai kriteria.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Kejadian</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesan Log</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($log->level == 'info')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800">INFO</span>
                                            @elseif($log->level == 'warning')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">WARNING</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">ERROR</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->user)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-primary-50 text-primary-800 border border-primary-100 flex items-center justify-center text-xs font-semibold">
                                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-gray-900 truncate max-w-[140px]">{{ $log->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500 italic">Automated System</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-700 break-all max-w-md">
                                            {{ Str::limit($log->pesan, 90) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.log-sistem.show', $log->id) }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.log-sistem.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors" title="Hapus">
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

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
            {{-- Modal Konfirmasi Penghapusan Log --}}
            <div id="modal-clear" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-clear').classList.add('hidden')"></div>
                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-sm max-w-md w-full p-6 border border-gray-100 relative">
                        <button type="button" onclick="document.getElementById('modal-clear').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
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
                            <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Semua Data Log?</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                Tindakan ini akan mengosongkan <strong>{{ number_format($statistics['total']) }}</strong> baris log audit dari basis data. Data yang terhapus tidak dapat dikembalikan lagi.
                            </p>
                        </div>

                        <form action="{{ route('admin.log-sistem.clear-all') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="confirm-text" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Ketik teks <strong class="text-red-600 font-bold">HAPUS SEMUA LOG</strong> untuk konfirmasi:
                                </label>
                                <input type="text" name="confirm" id="confirm-text" required class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 text-center" placeholder="HAPUS SEMUA LOG">
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="document.getElementById('modal-clear').classList.add('hidden')" class="flex-1 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-sm font-semibold text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Hapus Log
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
