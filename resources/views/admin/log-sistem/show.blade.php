<x-app-layout>
    @section('title', 'Detail Log Sistem')

    <x-slot name="header">
        <div class="flex items-center gap-3">
            {{-- Tombol Navigasi Kembali --}}
            <a href="{{ route('admin.log-sistem.index') }}" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Rincian Log Audit</h2>
                <p class="text-sm text-gray-500 mt-0.5">UUID: {{ $logSistem->id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Bagian Atas Kartu --}}
                <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($logSistem->level == 'info')
                            <div class="p-2 bg-primary-50 text-primary-800 rounded-lg border border-primary-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-primary-800">Level: Info</span>
                        @elseif($logSistem->level == 'warning')
                            <div class="p-2 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-yellow-800">Level: Warning</span>
                        @else
                            <div class="p-2 bg-red-50 text-red-800 rounded-lg border border-red-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-red-800">Level: Error</span>
                        @endif
                    </div>

                    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.log-sistem.destroy', $logSistem->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-sm font-semibold text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Hapus Record
                        </button>
                    </form>
                </div>

                {{-- Konten Utama --}}
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Waktu Pencatatan</label>
                        <p class="text-sm font-medium text-gray-900">{{ $logSistem->created_at->format('d F Y, H:i:s') }} WIB</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $logSistem->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Aktor Pemicu</label>
                        @if($logSistem->user)
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-100 w-fit">
                                <div class="w-9 h-9 rounded-full bg-primary-50 text-primary-800 border border-primary-100 flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr($logSistem->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $logSistem->user->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $logSistem->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">Automated System Process</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan Log</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 overflow-x-auto">
                            <p class="text-sm font-mono text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $logSistem->pesan }}</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Navigasi --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.log-sistem.index') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                        &larr; Kembali ke Daftar Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
