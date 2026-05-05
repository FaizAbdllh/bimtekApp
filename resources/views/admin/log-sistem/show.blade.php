<x-app-layout>
    @section('title', 'Detail Log')

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.log-sistem.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Detail Log
                </h2>
                <p class="text-gray-600 text-sm mt-1">ID: {{ $logSistem->id }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Header with Level Badge --}}
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @if($logSistem->level == 'info')
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg font-semibold text-blue-700">INFO</span>
                        @elseif($logSistem->level == 'warning')
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg font-semibold text-yellow-700">WARNING</span>
                        @else
                            <div class="p-3 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg font-semibold text-red-700">ERROR</span>
                        @endif
                    </div>
                    <form action="{{ route('admin.log-sistem.destroy', $logSistem) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Hapus Log
                        </button>
                    </form>
                </div>

                {{-- Log Details --}}
                <div class="p-6 space-y-6">
                    {{-- Timestamp --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Waktu</label>
                        <p class="text-gray-900">{{ $logSistem->created_at->format('d F Y, H:i:s') }}</p>
                        <p class="text-sm text-gray-500">{{ $logSistem->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- User --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">User</label>
                        @if($logSistem->user)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-medium">
                                    {{ substr($logSistem->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $logSistem->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $logSistem->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 italic">System</p>
                        @endif
                    </div>

                    {{-- Message --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pesan</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $logSistem->pesan }}</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.log-sistem.index') }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                        &larr; Kembali ke Daftar Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
