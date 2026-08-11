<x-app-layout>
    @section('title', 'Detail Log Sistem')

    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Tombol Navigasi Kembali --}}
            <a href="{{ route('admin.log-sistem.index') }}" class="p-2.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Rincian Log Audit
                </h2>
                <p class="text-gray-400 font-mono text-xs mt-0.5">UUID: {{ $logSistem->id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                {{-- Bagian Atasan Kartu dengan Lencana Level Kegawatan --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if($logSistem->level == 'info')
                            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-black text-blue-700 tracking-wider">LEVEL: INFO</span>
                        @elseif($logSistem->level == 'warning')
                            <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl border border-amber-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-black text-amber-700 tracking-wider">LEVEL: WARNING</span>
                        @else
                            <div class="p-2.5 bg-red-50 text-red-600 rounded-xl border border-red-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-sm font-black text-red-700 tracking-wider animate-pulse">LEVEL: CRIT ERROR</span>
                        @endif
                    </div>
                    
                    {{-- Form Tunggal Penghapusan Baris Log --}}
                    {{-- REFAKTORISASI: Kestabilan ID parameter rute destroy --}}
                    <form action="{{ route('admin.log-sistem.destroy', $logSistem->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen rekaman data log audit ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wide rounded-xl transition shadow-sm shadow-red-50">
                            Hapus Record
                        </button>
                    </form>
                </div>

                {{-- Konten Utama Rincian Isi Metadata Payload --}}
                <div class="p-6 space-y-6">
                    {{-- Blok Informasi Waktu Kejadian --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1.5">Waktu Pencatatan (Timestamp)</label>
                        <p class="text-sm font-bold text-gray-900">{{ $logSistem->created_at->format('d F Y, H:i:s') }} WIB</p>
                        <p class="text-xs text-gray-400 font-semibold mt-0.5">{{ $logSistem->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Blok Informasi Identitas Eksektor --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Aktor Pemicu (User Context)</label>
                        @if($logSistem->user)
                            <div class="flex items-center gap-3 bg-gray-50/50 p-3 rounded-xl border border-gray-100 w-fit pr-6">
                                <div class="w-9 h-9 rounded-full bg-primary-50 text-primary-700 border border-primary-100 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($logSistem->user->name, 0, 1)) }}
                                </div>
                                <div class="text-xs">
                                    <p class="text-gray-900 font-bold">{{ $logSistem->user->name }}</p>
                                    <p class="text-gray-400 font-medium mt-0.5">{{ $logSistem->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 w-fit italic">Automated System Process</p>
                        @endif
                    </div>

                    {{-- Blok Muatan Payload Isi Teks Log / Stack Trace Error --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Isi Pesan Log / Stack Trace Payload</label>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 shadow-inner shadow-gray-50 overflow-x-auto">
                            <p class="text-xs font-mono font-semibold text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $logSistem->pesan }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kaki Pembungkus Kartu Navigasi Mundur --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 font-bold text-xs uppercase tracking-wide">
                    <a href="{{ route('admin.log-sistem.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-800 transition-colors gap-1">
                        &larr; Kembali ke Daftar Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>