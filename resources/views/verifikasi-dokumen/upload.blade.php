<x-app-layout>
    <x-slot name="header">
        Upload Dokumen Persyaratan - {{ $bimtek->judul_final }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('bimtek.index') }}" class="text-gray-500 hover:text-primary-600">
                            Bimtek
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('bimtek.show', $bimtek) }}" class="ml-1 text-gray-500 hover:text-primary-600">
                                {{ $bimtek->judul_final }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">Upload Dokumen</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('bimtek.show', $bimtek) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Detail Bimtek
                </a>
            </div>

            {{-- Status Banner --}}
            @php
                $statusColors = [
                    'invited' => 'bg-blue-100 border-blue-400 text-blue-800',
                    'pending' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    'verified' => 'bg-green-100 border-green-400 text-green-800',
                    'rejected' => 'bg-red-100 border-red-400 text-red-800',
                ];
                $statusLabels = [
                    'invited' => 'Belum Upload Dokumen',
                    'pending' => 'Menunggu Verifikasi',
                    'verified' => 'Dokumen Terverifikasi',
                    'rejected' => 'Dokumen Ditolak - Silakan Upload Ulang',
                ];
                
                // Get rejected documents detail
                $rejectedDocs = $userDokumen->where('status', 'rejected');
            @endphp
            <div class="mb-6 p-4 rounded-lg border-l-4 {{ $statusColors[$assignment->status_verifikasi] ?? 'bg-gray-100 border-gray-400 text-gray-800' }}">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <span class="font-semibold">Status: {{ $statusLabels[$assignment->status_verifikasi] ?? $assignment->status_verifikasi }}</span>
                        @if($assignment->status_verifikasi === 'rejected' && $rejectedDocs->count() > 0)
                            <div class="mt-2 text-sm">
                                <p class="font-medium mb-1">Dokumen yang ditolak:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($rejectedDocs as $doc)
                                        <li>{{ \Illuminate\Support\Str::of($doc->jenis_dokumen)->replace('_', ' ')->title() }}
                                            @if($doc->catatan_verifikasi)
                                                <span class="text-xs italic">- {{ $doc->catatan_verifikasi }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Bimtek --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Bimtek</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Lokasi</p>
                            <p class="text-gray-900">{{ $bimtek->lokasi_aktual ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="text-gray-900">
                                {{ $bimtek->tanggal_mulai_aktual?->format('d F Y') ?? '-' }}
                                @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                                    - {{ $bimtek->tanggal_selesai_aktual->format('d F Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instruksi --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Petunjuk Upload Dokumen
                </h4>
                @php
                    $jenisDokumenLabel = collect($jenisDokumenWajib)
                        ->map(fn ($jenis) => \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title())
                        ->implode(', ');
                @endphp
                <ul class="text-sm text-blue-800 space-y-1 ml-7">
                    <li>• Format file yang diterima: <strong>PDF, JPG, atau PNG</strong></li>
                    <li>• Ukuran maksimal file: <strong>2 MB</strong></li>
                    <li>• Dokumen wajib: <strong>{{ $jenisDokumenLabel }}</strong></li>
                    <li>• Pastikan dokumen <strong>jelas dan terbaca</strong></li>
                    <li>• Anda dapat upload ulang jika dokumen ditolak</li>
                </ul>
            </div>

            {{-- Upload Forms --}}
            @foreach($jenisDokumenWajib as $jenis)
                @php
                    $dokumen = $dokumenMap[$jenis] ?? null;
                    $label = \Illuminate\Support\Str::of($jenis)->replace('_', ' ')->title();
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $label }}
                        </h3>

                        @if($dokumen)
                            <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $dokumen->file_name }}</p>
                                        <p class="text-xs text-gray-500">Diupload {{ $dokumen->uploaded_at->format('d F Y H:i') }}</p>
                                        <div class="mt-2">
                                            @if($dokumen->status === 'pending')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Menunggu Verifikasi</span>
                                            @elseif($dokumen->status === 'approved')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Disetujui</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">✗ Ditolak</span>
                                            @endif
                                        </div>
                                        @if($dokumen->catatan_verifikasi)
                                            <div class="mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-sm text-yellow-800">
                                                <p class="font-semibold">Catatan:</p>
                                                <p>{{ $dokumen->catatan_verifikasi }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex gap-2">
                                        @if(str_ends_with($dokumen->file_path, '.pdf'))
                                            <a href="{{ route('bimtek.verifikasi-dokumen.preview', $dokumen) }}" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat
                                            </a>
                                        @endif
                                        <a href="{{ route('bimtek.verifikasi-dokumen.download', $dokumen) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!$dokumen || $dokumen->status === 'rejected')
                            <form action="{{ route('bimtek.verifikasi-dokumen.upload', $bimtek) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $dokumen ? 'Upload Ulang' : 'Upload' }} {{ $label }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                                           class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                    @error('file')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg font-semibold text-sm hover:bg-primary-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ $dokumen ? 'Upload Ulang' : 'Upload' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
