<x-app-layout>
    <x-slot name="header">
        Detail Bimtek
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('bimtek.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Header Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                @php
                                    $statusColors = [
                                        'persiapan' => 'bg-yellow-100 text-yellow-800',
                                        'berlangsung' => 'bg-blue-100 text-blue-800',
                                        'selesai' => 'bg-green-100 text-green-800',
                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$bimtek->status_pelaksanaan] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($bimtek->status_pelaksanaan) }}
                                </span>

                                
                            </div>

                            {{-- Invite code for shared registration link (PIC/Panitia) --}}
                            @if($canManage)
                                <div class="mt-4 pt-4 border-t">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-700 text-sm font-semibold">Link Pendaftaran (untuk disisipkan di Surat)</span>
                                    </div>
                                    @if($bimtek->invite_code)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                            <input id="invite-link" class="flex-1 border p-2 rounded" readonly value="{{ $bimtek->invite_link }}">
                                            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-link').value)" class="px-3 py-2 bg-blue-600 text-white rounded">Copy</button>
                                            <form action="{{ route('bimtek.generate-invite', $bimtek) }}" method="post">
                                                @csrf
                                                <button class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Regenerate</button>
                                            </form>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('bimtek.activation-tokens.export', $bimtek) }}" class="inline-block mt-2 px-3 py-2 bg-green-600 text-white rounded">Export Tokens (CSV)</a>
                                        </div>
                                    @else
                                        <form action="{{ route('bimtek.generate-invite', $bimtek) }}" method="post">
                                            @csrf
                                            <button class="px-3 py-2 bg-blue-600 text-white rounded">Buat Link Pendaftaran</button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $bimtek->judul_final }}</h2>

                            @php
                                $modeCode = $bimtek->mode_pelaksanaan_code;
                                $modeBadge = [
                                    'offline' => 'bg-slate-100 text-slate-800',
                                    'online' => 'bg-sky-100 text-sky-800',
                                    'hybrid' => 'bg-emerald-100 text-emerald-800',
                                ];
                            @endphp

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Mode Pelaksanaan:</span>
                                    <span class="inline-flex mt-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $modeBadge[$modeCode] ?? 'bg-gray-100 text-gray-800' }}">{{ $bimtek->pengajuan?->mode_pelaksanaan_label ?? $bimtek->mode_pelaksanaan_label }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Tautan Virtual:</span>
                                    @if($bimtek->virtual_meeting_url)
                                        <a href="{{ $bimtek->virtual_meeting_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-700 font-medium block break-all">Buka Ruang Virtual</a>
                                    @else
                                        <span class="text-gray-500 block">{{ in_array($modeCode, ['online', 'hybrid']) ? 'Belum ditentukan' : '-' }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-gray-500">Lokasi:</span>
                                    <span class="text-gray-900 font-medium block">{{ $bimtek->lokasi_aktual ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Tanggal:</span>
                                    <span class="text-gray-900 font-medium block">
                                        {{ $bimtek->tanggal_mulai_aktual?->format('d M Y') ?? '-' }}
                                        @if($bimtek->tanggal_selesai_aktual && $bimtek->tanggal_mulai_aktual != $bimtek->tanggal_selesai_aktual)
                                            - {{ $bimtek->tanggal_selesai_aktual->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Anggaran Disetujui:</span>
                                    <span class="text-gray-900 font-medium block">Rp {{ number_format($bimtek->anggaran_disetujui ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Asal Pengajuan:</span>
                                    <span class="text-gray-900 font-medium block">
                                        @if($bimtek->pengajuan)
                                            <a href="#" class="text-primary-600 hover:text-primary-700">{{ $bimtek->pengajuan->user->name ?? '-' }}</a>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Deskripsi Jadwal --}}
                            @if($bimtek->deskripsi_jadwal)
                                <div class="mt-4 pt-4 border-t">
                                    <span class="text-gray-500 text-sm">Deskripsi Jadwal:</span>
                                    <p class="text-gray-700 mt-1">{{ $bimtek->deskripsi_jadwal }}</p>
                                </div>
                            @endif

                            {{-- Surat Undangan Draft (by PIC/Panitia) --}}
                            <div class="mt-4 pt-4 border-t">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-700 text-sm font-semibold">Surat Undangan Draft</span>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">Dibuat oleh PIC/Panitia</span>
                                </div>
                                
                                @if($bimtek->file_surat_draft_path)
                                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg mb-2">
                                        <div class="p-2 bg-white rounded-lg border border-blue-200">
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ basename($bimtek->file_surat_draft_path) }}</p>
                                            <p class="text-xs text-gray-500">Surat Draft</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('bimtek.preview-draft', $bimtek) }}" target="_blank" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('bimtek.download-draft', $bimtek) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                            @if($bimtek->status_pelaksanaan == 'persiapan')
                                                @php $currentUser = auth()->user(); @endphp
                                                @if($currentUser && ($currentUser->isPicDiBimtek($bimtek) || $currentUser->isPanitiaDiBimtek($bimtek)))
                                                    <button type="button" 
                                                        x-data 
                                                        @click="$dispatch('open-modal', 'upload-draft')"
                                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Ganti">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $draftUploader = $bimtek->draftUploader;
                                        $draftUploadedAt = $bimtek->file_surat_draft_uploaded_at ?? null;
                                    @endphp
                                    @if($draftUploader || $draftUploadedAt)
                                        <div class="text-xs text-gray-500 ml-0 mb-3">
                                            @if($draftUploader)
                                                Diunggah oleh: <span class="font-medium text-gray-700">{{ $draftUploader->name }}</span>
                                            @endif
                                            @if($draftUploadedAt)
                                                pada <span class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($draftUploadedAt)->translatedFormat('d F Y H:i') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-600 mb-3">Belum ada draft surat.</p>
                                    @if($bimtek->status_pelaksanaan == 'persiapan')
                                        @php $currentUser = auth()->user(); @endphp
                                        @if($currentUser && ($currentUser->isPicDiBimtek($bimtek) || $currentUser->isPanitiaDiBimtek($bimtek)))
                                            <button type="button" 
                                                x-data 
                                                @click="$dispatch('open-modal', 'upload-draft')"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                Upload Draft
                                            </button>
                                        @endif
                                    @endif
                                @endif
                            </div>

                            {{-- Surat Undangan Final (by Persuratan) --}}
                            <div class="mt-3 pt-3 border-t">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-700 text-sm font-semibold">Surat Undangan Final</span>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Dibuat oleh Persuratan</span>
                                </div>
                                
                                @if($bimtek->file_surat_final_path)
                                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg mb-2">
                                        <div class="p-2 bg-white rounded-lg border border-green-200">
                                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ basename($bimtek->file_surat_final_path) }}</p>
                                            <p class="text-xs text-gray-500">Surat Final (Resmi)</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('bimtek.preview-final', $bimtek) }}" target="_blank" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-100 rounded-lg transition" title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('bimtek.download-final', $bimtek) }}" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-100 rounded-lg transition" title="Download">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                            @if($bimtek->status_pelaksanaan == 'persiapan')
                                                @php $currentUser = auth()->user(); @endphp
                                                @if($currentUser && $currentUser->isPersuratan())
                                                    <button type="button" 
                                                        x-data 
                                                        @click="$dispatch('open-modal', 'upload-final')"
                                                        class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-100 rounded-lg transition" title="Ganti">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $finalUploader = $bimtek->finalUploader;
                                        $finalUploadedAt = $bimtek->file_surat_final_uploaded_at ?? null;
                                    @endphp
                                    @if($finalUploader || $finalUploadedAt)
                                        <div class="text-xs text-gray-500 ml-0">
                                            @if($finalUploader)
                                                Diunggah oleh: <span class="font-medium text-gray-700">{{ $finalUploader->name }}</span>
                                            @endif
                                            @if($finalUploadedAt)
                                                pada <span class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($finalUploadedAt)->translatedFormat('d F Y H:i') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-600 mb-3">Belum ada surat final. Menunggu Bagian Persuratan.</p>
                                    @if($bimtek->status_pelaksanaan == 'persiapan')
                                        @php $currentUser = auth()->user(); @endphp
                                        @if($currentUser && $currentUser->isPersuratan())
                                            <button type="button" 
                                                x-data 
                                                @click="$dispatch('open-modal', 'upload-final')"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                Upload Surat Final
                                            </button>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Verification Alert for Peserta --}}
                    @php
                        $isPeserta = $bimtek->peserta->contains(auth()->id());
                        if ($isPeserta && $bimtek->butuh_verifikasi_dokumen) {
                            $pesertaPivot = $bimtek->users()
                                ->where('users.id', auth()->id())
                                ->where('bimtek_user.peran_kontekstual', 'peserta')
                                ->first();
                            $statusVerifikasi = $pesertaPivot ? ($pesertaPivot->pivot->status_verifikasi ?? 'invited') : 'invited';
                        } else {
                            $statusVerifikasi = null;
                        }
                    @endphp

                    @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        @if($statusVerifikasi === 'invited')
                                            Verifikasi Dokumen Diperlukan
                                        @elseif($statusVerifikasi === 'pending')
                                            Dokumen Sedang Diverifikasi
                                        @elseif($statusVerifikasi === 'rejected')
                                            Dokumen Ditolak - Upload Ulang Diperlukan
                                        @endif
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        @if($statusVerifikasi === 'invited')
                                            <p>Anda perlu mengupload dokumen persyaratan sebelum dapat mengakses fitur Absensi, Tugas, dan Sertifikat.</p>
                                        @elseif($statusVerifikasi === 'pending')
                                            <p>Dokumen Anda sedang dalam proses verifikasi oleh panitia. Anda akan dapat mengakses fitur setelah dokumen disetujui.</p>
                                        @elseif($statusVerifikasi === 'rejected')
                                            <p>Dokumen Anda ditolak. Silakan upload ulang dokumen yang sesuai untuk mendapatkan akses ke fitur Bimtek.</p>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                                            @if($statusVerifikasi === 'invited' || $statusVerifikasi === 'rejected')
                                                Upload Dokumen
                                            @else
                                                Lihat Status Dokumen
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Quick Actions for PIC/Panitia --}}
                    @php
                        $canUpdateStatus = $bimtek->pic_user_id === auth()->id() ||
                            $bimtek->panitia()->where('users.id', auth()->id())->exists();
                    @endphp
                    @if($canUpdateStatus)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelola Status Bimtek</h3>
                                <div class="flex flex-wrap gap-3">
                                    @if($bimtek->status_pelaksanaan == 'persiapan')
                                        <form action="{{ route('bimtek.update-status', $bimtek) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_pelaksanaan" value="berlangsung">
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition" onclick="return confirm('Mulai bimtek? Status akan berubah ke Berlangsung. Anda masih dapat membatalkan jika tidak sengaja.')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Mulai Bimtek
                                            </button>
                                        </form>
                                        @if($isPic)
                                            <form action="{{ route('bimtek.update-status', $bimtek) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_pelaksanaan" value="dibatalkan">
                                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm transition" onclick="return confirm('Yakin ingin membatalkan bimtek ini? Status akan berubah menjadi Dibatalkan.')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Batalkan Bimtek
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    @if($bimtek->status_pelaksanaan == 'berlangsung')
                                        <form action="{{ route('bimtek.update-status', $bimtek) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_pelaksanaan" value="persiapan">
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium text-sm transition" onclick="return confirm('Batal Mulai? Status akan kembali ke Persiapan.')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                                Batal Mulai
                                            </button>
                                        </form>
                                        <form action="{{ route('bimtek.update-status', $bimtek) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_pelaksanaan" value="selesai">
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Selesaikan
                                            </button>
                                        </form>
                                    @endif

                                    @if($isPic && $bimtek->pengajuan && $bimtek->pengajuan->status_pengajuan === 'disetujui_final' && $bimtek->status_pelaksanaan == 'persiapan')
                                        <form action="{{ route('bimtek.request-revisi', $bimtek) }}" method="POST" class="inline" onsubmit="return confirm('Melakukan revisi? Pengajuan akan kembali ke proses persetujuan Kepala dan PPK.')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium text-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                                </svg>
                                                Revisi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabs: Materi, Tugas, Absensi, dll --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ tab: 'materi' }">
                        <div class="border-b overflow-x-auto">
                            <nav class="flex -mb-px min-w-max">
                                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm whitespace-nowrap">
                                    Materi
                                </button>
                                @if($bimtek->has_tugas)
                                    <button @click="tab = 'tugas'" :class="tab === 'tugas' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm whitespace-nowrap">
                                        Tugas
                                    </button>
                                @endif
                                <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm whitespace-nowrap">
                                    Absensi
                                </button>
                                <button @click="tab = 'peserta'" :class="tab === 'peserta' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm whitespace-nowrap">
                                    Peserta
                                </button>
                                @if($bimtek->has_sertifikat)
                                    <button @click="tab = 'sertifikat'" :class="tab === 'sertifikat' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm whitespace-nowrap">
                                        Sertifikat
                                    </button>
                                @endif
                            </nav>
                        </div>

                        <div class="p-6">
                            {{-- Materi Tab --}}
                            <div x-show="tab === 'materi'" x-cloak>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm text-gray-500">{{ $bimtek->materis->count() }} materi tersedia</span>
                                    <a href="{{ route('bimtek.materi.index', $bimtek) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        Lihat Semua →
                                    </a>
                                </div>
                                @if($bimtek->materis && $bimtek->materis->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($bimtek->materis->take(5) as $materi)
                                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    <div>
                                                        <span class="text-gray-800">{{ $materi->judul }}</span>
                                                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $materi->tipe === 'materi' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                            {{ $materi->tipe === 'materi' ? 'Materi' : 'Panduan' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($materi->file_path)
                                                    @php
                                                        $ext = strtolower(pathinfo($materi->file_path, PATHINFO_EXTENSION));
                                                        $canPreview = in_array($ext, ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                                                    @endphp
                                                    <div class="flex items-center space-x-2">
                                                        @if($canPreview)
                                                            <a href="{{ route('bimtek.materi.preview', [$bimtek, $materi]) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm">
                                                                Lihat
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('bimtek.materi.download', [$bimtek, $materi]) }}" class="text-primary-600 hover:text-primary-700 text-sm">
                                                            Download
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($bimtek->materis->count() > 5)
                                            <p class="text-center text-sm text-gray-500 pt-2">
                                                + {{ $bimtek->materis->count() - 5 }} materi lainnya
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-gray-500 mt-2">Belum ada materi.</p>
                                        <a href="{{ route('bimtek.materi.index', $bimtek) }}" class="mt-2 inline-block text-primary-600 hover:text-primary-700 text-sm">
                                            Kelola Materi →
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Tugas Tab --}}
                            @if($bimtek->has_tugas)
                                <div x-show="tab === 'tugas'" x-cloak>
                                {{-- Verification Gate for Peserta --}}
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto text-yellow-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Verifikasi Dokumen Diperlukan</h3>
                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                            Anda perlu menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses fitur Tugas.
                                        </p>
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                            Upload Dokumen Sekarang
                                        </a>
                                    </div>
                                @else
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-sm text-gray-500">{{ $bimtek->tugas->count() }} tugas tersedia</span>
                                        <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                            Lihat Semua →
                                        </a>
                                    </div>
                                    @if($bimtek->tugas && $bimtek->tugas->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($bimtek->tugas->take(5) as $tugas)
                                                <div class="p-3 border rounded-lg">
                                                    <div class="flex justify-between items-start">
                                                        <a href="{{ route('bimtek.tugas.show', [$bimtek, $tugas]) }}" class="font-medium text-gray-800 hover:text-primary-600">{{ $tugas->judul }}</a>
                                                        <span class="text-xs {{ $tugas->isDeadlinePassed() ? 'text-red-500' : 'text-gray-500' }}">
                                                            Deadline: {{ $tugas->deadline?->format('d M Y, H:i') }}
                                                            @if($tugas->isDeadlinePassed())
                                                                (Terlewat)
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if($tugas->deskripsi)
                                                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($tugas->deskripsi, 100) }}</p>
                                                    @endif
                                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        {{ $tugas->pengumpulanTugas->count() }} pengumpulan
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($bimtek->tugas->count() > 5)
                                                <p class="text-center text-sm text-gray-500 pt-2">
                                                    + {{ $bimtek->tugas->count() - 5 }} tugas lainnya
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            <p class="text-gray-500 mt-2">Belum ada tugas.</p>
                                            <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="mt-2 inline-block text-primary-600 hover:text-primary-700 text-sm">
                                                Kelola Tugas →
                                            </a>
                                        </div>
                                    @endif
                                @endif
                                </div>
                            @endif

                            {{-- Absensi Tab --}}
                            <div x-show="tab === 'absensi'" x-cloak>
                                {{-- Verification Gate for Peserta --}}
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto text-yellow-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Verifikasi Dokumen Diperlukan</h3>
                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                            Anda perlu menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses fitur Absensi.
                                        </p>
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                            Upload Dokumen Sekarang
                                        </a>
                                    </div>
                                @else
                                    @if($bimtek->sesiAbsensis && $bimtek->sesiAbsensis->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($bimtek->sesiAbsensis->take(5) as $sesi)
                                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                                    <div>
                                                        <h4 class="font-medium text-gray-800">{{ $sesi->nama_sesi }}</h4>
                                                        <span class="text-xs text-gray-500">{{ $sesi->created_at->format('d M Y, H:i') }}</span>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $sesi->isOpen() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $sesi->isOpen() ? 'Terbuka' : 'Ditutup' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($bimtek->sesiAbsensis->count() > 5)
                                            <p class="text-sm text-gray-500 mt-3">dan {{ $bimtek->sesiAbsensis->count() - 5 }} sesi lainnya...</p>
                                        @endif
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-gray-500 mt-2">Belum ada sesi absensi.</p>
                                        </div>
                                    @endif
                                    {{-- Link ke Kelola Absensi --}}
                                    @if($canManage || $isPeserta)
                                        <div class="mt-4 pt-4 border-t">
                                            <a href="{{ route('bimtek.absensi.index', $bimtek) }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                                                Kelola Absensi →
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Peserta Tab --}}
                            <div x-show="tab === 'peserta'" x-cloak>
                                @php
                                    $totalPeserta = $bimtek->users()->count();
                                    $totalPIC = $bimtek->pic()->count();
                                    $totalPanitia = $bimtek->panitia()->count();
                                    $totalPesertaOnly = $bimtek->peserta()->count();
                                    $totalPemateri = is_array($bimtek->daftar_pemateri) ? count($bimtek->daftar_pemateri) : 0;
                                @endphp
                                
                                {{-- Statistik Peserta --}}
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-gray-800">{{ $totalPeserta }}</div>
                                        <div class="text-xs text-gray-500">Total</div>
                                    </div>
                                    <div class="bg-red-50 p-3 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-red-600">{{ $totalPIC }}</div>
                                        <div class="text-xs text-red-500">PIC</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ $totalPanitia }}</div>
                                        <div class="text-xs text-purple-500">Panitia</div>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $totalPesertaOnly }}</div>
                                        <div class="text-xs text-blue-500">Peserta</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $totalPemateri }}</div>
                                        <div class="text-xs text-green-500">Pemateri</div>
                                    </div>
                                </div>

                                @if($totalPeserta > 0)
                                    <div class="space-y-2">
                                        @foreach($bimtek->users()->take(5)->get() as $user)
                                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                                        <span class="text-primary-600 font-medium text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-gray-800">{{ $user->name }}</h4>
                                                        <span class="text-xs text-gray-500">{{ $user->email }}</span>
                                                    </div>
                                                </div>
                                                @php
                                                    $peran = $user->pivot->peran_kontekstual ?? 'peserta';
                                                    $peranColors = [
                                                        'pic' => 'bg-red-100 text-red-800',
                                                        'panitia' => 'bg-purple-100 text-purple-800',
                                                        'peserta' => 'bg-blue-100 text-blue-800',
                                                        'pemateri' => 'bg-green-100 text-green-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded-full {{ $peranColors[$peran] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($peran) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($totalPeserta > 5)
                                        <p class="text-sm text-gray-500 mt-3">dan {{ $totalPeserta - 5 }} peserta lainnya...</p>
                                    @endif
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-gray-500 mt-2">Belum ada peserta terdaftar.</p>
                                    </div>
                                @endif
                                
                                {{-- Link ke Kelola Peserta --}}
                                @if($canManage)
                                    <div class="mt-4 pt-4 border-t">
                                        <a href="{{ route('bimtek.peserta.index', $bimtek) }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                                            Kelola Peserta →
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Sertifikat Tab --}}
                            @if($bimtek->has_sertifikat)
                                <div x-show="tab === 'sertifikat'" x-cloak>
                                {{-- Verification Gate for Peserta --}}
                                @if($isPeserta && $bimtek->butuh_verifikasi_dokumen && $statusVerifikasi !== 'verified')
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto text-yellow-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Verifikasi Dokumen Diperlukan</h3>
                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                            Anda perlu menyelesaikan verifikasi dokumen terlebih dahulu untuk mengakses fitur Sertifikat.
                                        </p>
                                        <a href="{{ route('bimtek.verifikasi-dokumen.upload-form', $bimtek) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                            Upload Dokumen Sekarang
                                        </a>
                                    </div>
                                @else
                                    @if($bimtek->sertifikats && $bimtek->sertifikats->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($bimtek->sertifikats->take(5) as $sertifikat)
                                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                                    <div>
                                                        <h4 class="font-medium text-gray-800">{{ $sertifikat->user->name ?? '-' }}</h4>
                                                        <span class="text-xs text-gray-500">{{ $sertifikat->nomor_sertifikat }}</span>
                                                    </div>
                                                    @if($sertifikat->file_path)
                                                        <a href="{{ route('bimtek.sertifikat.download', [$bimtek, $sertifikat]) }}" class="text-primary-600 hover:text-primary-700 text-sm">
                                                            Download
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($bimtek->sertifikats->count() > 5)
                                            <p class="text-sm text-gray-500 mt-3">dan {{ $bimtek->sertifikats->count() - 5 }} sertifikat lainnya...</p>
                                        @endif
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            </svg>
                                            <p class="text-gray-500 mt-2">Belum ada sertifikat.</p>
                                        </div>
                                    @endif
                                    {{-- Link ke Kelola Sertifikat --}}
                                    @if($canManage || $isPeserta)
                                        <div class="mt-4 pt-4 border-t">
                                            <a href="{{ route('bimtek.sertifikat.index', $bimtek) }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                                                Kelola Sertifikat →
                                            </a>
                                        </div>
                                    @endif
                                @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar: PIC, Panitia, Peserta --}}
                <div class="space-y-6">
                    {{-- Verification Dashboard Link (for Kepala/PIC/Panitia) --}}
                @if($bimtek->butuh_verifikasi_dokumen && auth()->user()->role && (auth()->user()->role->nama_peran === 'Kepala' || $bimtek->pic_user_id === auth()->id() || $bimtek->panitia->contains(auth()->id())))
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 overflow-hidden shadow-sm sm:rounded-lg border border-blue-200">
                            <div class="p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                                    </svg>
                                    <h3 class="font-semibold text-blue-900">Verifikasi Dokumen</h3>
                                </div>
                                <p class="text-xs text-blue-700 mb-3">Kelola verifikasi dokumen peserta</p>
                                @php
                                    $pendingCount = $bimtek->pesertaPendingVerifikasi->count() ?? 0;
                                @endphp
                                @if($pendingCount > 0)
                                    <div class="mb-3 p-2 bg-yellow-100 border border-yellow-300 rounded-md">
                                        <p class="text-xs text-yellow-800 font-medium">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $pendingCount }} dokumen menunggu verifikasi
                                        </p>
                                    </div>
                                @endif
                                <a href="{{ route('bimtek.verifikasi-dokumen.index', $bimtek) }}" class="block w-full text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                                    Dashboard Verifikasi
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- PIC (Penanggung Jawab) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold text-gray-900">PIC (Penanggung Jawab)</h3>
                            <p class="text-xs text-gray-500 mt-1">Ditetapkan otomatis saat pembuatan Bimtek</p>
                        </div>
                        <div class="p-4">
                            @if($bimtek->pic)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-xs font-semibold">
                                        {{ strtoupper(substr($bimtek->pic->name, 0, 2)) }}
                                    </div>
                                    <span class="ml-2 text-sm text-gray-700">{{ $bimtek->pic->name }}</span>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center">Belum ada PIC.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Panitia --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Panitia</h3>
                            @if($isPic)
                                <button x-data @click="$dispatch('open-modal', 'assign-panitia')" class="text-xs text-primary-600 hover:text-primary-700">
                                    + Tambah
                                </button>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($bimtek->panitia->count() > 0)
                                <div class="space-y-2">
                                    @foreach($bimtek->panitia as $user)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xs font-semibold">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm text-gray-700">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $user->pivot->fungsi_panitia ?? 'Panitia' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center">Belum ada panitia.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Peserta --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Peserta ({{ $bimtek->peserta->count() }})</h3>
                            @if(auth()->user()->role && (auth()->user()->role->nama_peran === 'Kepala' || $bimtek->pic_user_id === auth()->id()))
                                <a href="#" class="text-xs text-primary-600 hover:text-primary-700">
                                    Kelola
                                </a>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($bimtek->peserta->count() > 0)
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach($bimtek->peserta->take(10) as $user)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-semibold">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">{{ $user->name }}</span>
                                        </div>
                                    @endforeach
                                    @if($bimtek->peserta->count() > 10)
                                        <p class="text-xs text-gray-500 text-center pt-2">+ {{ $bimtek->peserta->count() - 10 }} peserta lainnya</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center">Belum ada peserta.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Pemateri --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Pemateri</h3>
                            @if($canManage ?? false)
                                <button x-data @click="$dispatch('open-modal', 'kelola-pemateri')" class="text-xs text-primary-600 hover:text-primary-700">
                                    + Tambah
                                </button>
                            @endif
                        </div>
                        <div class="p-4">
                            @if(is_array($bimtek->daftar_pemateri) && count($bimtek->daftar_pemateri) > 0)
                                <div class="space-y-2">
                                    @foreach($bimtek->daftar_pemateri as $pemateri)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-semibold">
                                                {{ strtoupper(substr($pemateri['nama'] ?? '?', 0, 2)) }}
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">{{ $pemateri['nama'] ?? '-' }}</span>
                                            @if(!empty($pemateri['asal_instansi']))
                                                <span class="ml-2 text-xs text-gray-500">{{ $pemateri['asal_instansi'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center">Belum ada pemateri.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Assign Panitia --}}
    <x-modal name="assign-panitia" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.assign-panitia', $bimtek) }}" method="POST" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Panitia</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih User</label>
                <select name="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">-- Pilih User --</option>
                    @foreach($availableUsers as $user)
                        @if(!$bimtek->panitia->contains($user->id))
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->nama_peran ?? '-' }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fungsi/Jabatan Panitia</label>
                <select name="fungsi_panitia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">-- Pilih Fungsi --</option>
                    <option value="Penanggung Jawab">Penanggung Jawab</option>
                    <option value="Ketua">Ketua</option>
                    <option value="Sekretaris">Sekretaris</option>
                    <option value="Anggota">Anggota</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Tambah
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Kelola Pemateri --}}
    <x-modal name="kelola-pemateri" :show="false" maxWidth="lg">
        <form action="{{ route('bimtek.pemateri.update', $bimtek) }}" method="POST" class="p-6"
            x-data="{ items: @json($bimtek->daftar_pemateri ?? []) }" x-init="if (items.length === 0) items.push({ nama: '', asal_instansi: '' })">
            @csrf
            @method('PATCH')
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kelola Pemateri</h3>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-3 items-start">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pemateri</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :name="'daftar_pemateri[' + index + '][nama]'" x-model="item.nama" placeholder="Nama pemateri">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Asal Instansi</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                :name="'daftar_pemateri[' + index + '][asal_instansi]'" x-model="item.asal_instansi" placeholder="Instansi (opsional)">
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="button" class="w-full px-3 py-2 text-xs text-red-700 bg-red-50 hover:bg-red-100 rounded-lg"
                                @click="items.splice(index, 1)" x-show="items.length > 1">
                                Hapus
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" class="mt-3 inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs text-gray-700"
                @click="items.push({ nama: '', asal_instansi: '' })">
                + Tambah Pemateri
            </button>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Upload Surat Draft --}}
    <x-modal name="upload-draft" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-draft', $bimtek) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Surat Undangan Draft</h3>
            <p class="text-sm text-gray-600 mb-4">Surat draft akan diberikan kepada Bagian Persuratan untuk dilengkapi nomor, tanggal, dan tanda tangan.</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File Draft (PDF)</label>
                <input type="file" name="surat_draft" accept=".pdf" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Upload Draft
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Upload Surat Final --}}
    <x-modal name="upload-final" :show="false" maxWidth="md">
        <form action="{{ route('bimtek.upload-final', $bimtek) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Surat Undangan Final</h3>
            <p class="text-sm text-gray-600 mb-4">Surat final sudah dilengkapi nomor, tanggal, dan tanda tangan dari Kepala BBPMP.</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File Final (PDF)</label>
                <input type="file" name="surat_final" accept=".pdf" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Upload Final
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
