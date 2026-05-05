<x-app-layout>
    <x-slot name="header">
        Detail Tugas
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Back --}}
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('bimtek.tugas.index', $bimtek) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>

                @if($canManage)
                    <a href="{{ route('bimtek.tugas.edit', [$bimtek, $tugas]) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Tugas
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Tugas Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Tugas Detail Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $tugas->judul }}</h2>

                            {{-- Deadline Status --}}
                            <div class="mb-4 p-4 rounded-lg {{ $tugas->isDeadlinePassed() ? 'bg-red-50 border border-red-100' : 'bg-blue-50 border border-blue-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 {{ $tugas->isDeadlinePassed() ? 'text-red-500' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium {{ $tugas->isDeadlinePassed() ? 'text-red-800' : 'text-blue-800' }}">
                                            Deadline: {{ $tugas->deadline->format('d F Y, H:i') }} WIB
                                        </p>

                        @if($canManage)
                            {{-- Grade Modal --}}
                            <div id="grade-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div id="grade-modal-overlay" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

                                    <div class="relative bg-white rounded-lg max-w-lg w-full shadow-xl">
                                        <form id="grade-form" method="POST" data-action-base="{{ url('bimtek/' . $bimtek->id . '/tugas/' . $tugas->id . '/pengumpulan') }}">
                                            @csrf
                                            <div class="p-6">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Beri Nilai Final</h3>
                                                <p class="text-sm text-gray-500 mb-4">Peserta: <span id="grade-peserta-nama"></span></p>

                                                <div class="mb-4 rounded-lg border border-blue-100 bg-blue-50 p-4">
                                                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Rubrik Penilaian Umum</h4>
                                                    <ul class="space-y-1 text-sm text-blue-900/90 list-disc list-inside">
                                                        <li>Kesesuaian tugas dengan instruksi</li>
                                                        <li>Kelengkapan isi dan jawaban</li>
                                                        <li>Ketepatan substansi atau analisis</li>
                                                        <li>Kerapian, sistematika, dan penyajian</li>
                                                        <li>Ketepatan waktu pengumpulan</li>
                                                    </ul>
                                                    <p class="mt-3 text-xs text-blue-800">Panitia menilai secara manual berdasarkan rubrik ini, lalu memasukkan nilai final 0-100 ke sistem.</p>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Nilai Final (0-100) <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="number"
                                                           name="nilai"
                                                           id="grade-nilai"
                                                           min="0"
                                                           max="100"
                                                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                           required>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Catatan Penilaian (Opsional)
                                                    </label>
                                                    <textarea name="feedback"
                                                              id="grade-feedback"
                                                              rows="3"
                                                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                              placeholder="Tuliskan alasan singkat atau masukan untuk peserta..."></textarea>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-lg">
                                                <button type="button" id="grade-modal-close" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                                                    Simpan Nilai Final
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const modal = document.getElementById('grade-modal');
                                    if (!modal) return;

                                    const overlay = document.getElementById('grade-modal-overlay');
                                    const closeBtn = document.getElementById('grade-modal-close');
                                    const nameEl = document.getElementById('grade-peserta-nama');
                                    const nilaiInput = document.getElementById('grade-nilai');
                                    const feedbackInput = document.getElementById('grade-feedback');
                                    const form = document.getElementById('grade-form');
                                    const actionBase = form.getAttribute('data-action-base');

                                    function openModal(button) {
                                        const id = button.getAttribute('data-pengumpulan-id');
                                        const nama = button.getAttribute('data-nama') || '-';
                                        const nilai = button.getAttribute('data-nilai');
                                        const feedback = button.getAttribute('data-feedback');

                                        nameEl.textContent = nama;
                                        nilaiInput.value = nilai || '';
                                        feedbackInput.value = feedback || '';
                                        form.action = `${actionBase}/${id}/grade`;

                                        modal.classList.remove('hidden');
                                        document.body.classList.add('overflow-y-hidden');
                                    }

                                    function closeModal() {
                                        modal.classList.add('hidden');
                                        document.body.classList.remove('overflow-y-hidden');
                                    }

                                    document.querySelectorAll('.js-grade-button').forEach(function (button) {
                                        button.addEventListener('click', function () {
                                            openModal(button);
                                        });
                                    });

                                    overlay.addEventListener('click', closeModal);
                                    closeBtn.addEventListener('click', closeModal);
                                    document.addEventListener('keydown', function (event) {
                                        if (event.key === 'Escape') {
                                            closeModal();
                                        }
                                    });
                                });
                            </script>
                        @endpush
                                        @if($tugas->isDeadlinePassed())
                                            <p class="text-sm text-red-600">Deadline sudah terlewat {{ $tugas->deadline->diffForHumans() }}</p>
                                        @else
                                            <p class="text-sm text-blue-600">Sisa waktu: {{ $tugas->deadline->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            @if($tugas->deskripsi)
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Deskripsi Tugas</h3>
                                    <p class="text-gray-600 whitespace-pre-line">{{ $tugas->deskripsi }}</p>
                                </div>
                            @endif

                            {{-- File Instruksi --}}
                            @if($tugas->file_instruksi_path)
                                <div class="pt-4 border-t">
                                    <h3 class="text-sm font-medium text-gray-700 mb-3">File Instruksi</h3>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ basename($tugas->file_instruksi_path) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('bimtek.tugas.preview-instruksi', [$bimtek, $tugas]) }}" target="_blank" class="px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                                Lihat
                                            </a>
                                            <a href="{{ route('bimtek.tugas.download-instruksi', [$bimtek, $tugas]) }}" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pengumpulan Section (For PIC/Panitia) --}}
                    @if($canManage)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Pengumpulan Tugas</h3>
                                    <span class="text-sm text-gray-500">
                                        {{ $tugas->pengumpulanTugas->count() }} / {{ $peserta->count() }} peserta mengumpulkan
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                @if($tugas->pengumpulanTugas->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($tugas->pengumpulanTugas as $pengumpulan)
                                                    <tr>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                                                    <span class="text-primary-700 font-medium">{{ substr($pengumpulan->user->nama ?? $pengumpulan->user->name ?? 'U', 0, 1) }}</span>
                                                                </div>
                                                                <div class="ml-3">
                                                                    <p class="text-sm font-medium text-gray-900">{{ $pengumpulan->user->nama ?? $pengumpulan->user->name }}</p>
                                                                    <p class="text-xs text-gray-500">{{ $pengumpulan->user->email }}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">{{ $pengumpulan->created_at->format('d M Y') }}</div>
                                                            <div class="text-xs text-gray-500">{{ $pengumpulan->created_at->format('H:i') }} WIB</div>
                                                            @if($pengumpulan->created_at->gt($tugas->deadline))
                                                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terlambat</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek, $tugas, $pengumpulan]) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700">Lihat</a>
                                                                <span class="text-gray-300">|</span>
                                                                <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek, $tugas, $pengumpulan]) }}" class="text-sm text-primary-600 hover:text-primary-700">Download</a>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            @if($pengumpulan->nilai !== null)
                                                                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-medium {{ $pengumpulan->nilai >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                    {{ $pengumpulan->nilai }}
                                                                </span>
                                                            @else
                                                                <span class="text-sm text-gray-400">Belum dinilai</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <button type="button"
                                                                    class="js-grade-button px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
                                                                    data-pengumpulan-id="{{ $pengumpulan->id }}"
                                                                    data-nama="{{ $pengumpulan->user->nama ?? $pengumpulan->user->name }}"
                                                                    data-nilai="{{ $pengumpulan->nilai ?? '' }}"
                                                                    data-feedback="{{ $pengumpulan->feedback ?? '' }}">
                                                                {{ $pengumpulan->nilai !== null ? 'Edit Nilai Final' : 'Beri Nilai Final' }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Peserta yang belum mengumpulkan --}}
                                    @php
                                        $submittedUserIds = $tugas->pengumpulanTugas->pluck('user_id')->toArray();
                                        $belumMengumpulkan = $peserta->whereNotIn('id', $submittedUserIds);
                                    @endphp
                                    @if($belumMengumpulkan->count() > 0)
                                        <div class="mt-6 pt-6 border-t">
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Belum Mengumpulkan ({{ $belumMengumpulkan->count() }} peserta)</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($belumMengumpulkan as $p)
                                                    <span class="px-3 py-1.5 rounded-full text-sm bg-gray-100 text-gray-700">
                                                        {{ $p->nama ?? $p->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-500">Belum ada peserta yang mengumpulkan tugas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Submit Section (For Peserta) --}}
                    @if($isPeserta)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b bg-gray-50">
                                <h3 class="text-lg font-semibold text-gray-900">Pengumpulan Tugas Anda</h3>
                            </div>
                            <div class="p-6">
                                @if($userSubmission)
                                    {{-- Already Submitted --}}
                                    <div class="bg-green-50 border border-green-100 rounded-lg p-4 mb-4">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="font-medium text-green-800">Tugas Sudah Dikumpulkan</p>
                                                <p class="text-sm text-green-600">Dikumpulkan pada {{ $userSubmission->created_at->format('d F Y, H:i') }} WIB</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- File yang dikumpulkan --}}
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-4">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ basename($userSubmission->file_jawaban_path) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('bimtek.tugas.preview-jawaban', [$bimtek, $tugas, $userSubmission]) }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700">Lihat</a>
                                            <a href="{{ route('bimtek.tugas.download-jawaban', [$bimtek, $tugas, $userSubmission]) }}" class="text-sm text-primary-600 hover:text-primary-700">Download</a>
                                        </div>
                                    </div>

                                    {{-- Nilai --}}
                                    @if($userSubmission->nilai !== null)
                                        <div class="p-4 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700">Nilai Anda:</span>
                                                <span class="text-2xl font-bold {{ $userSubmission->nilai >= 70 ? 'text-green-600' : 'text-red-600' }}">{{ $userSubmission->nilai }}</span>
                                            </div>
                                            @if($userSubmission->feedback)
                                                <div class="mt-3 pt-3 border-t border-gray-200">
                                                    <p class="text-sm font-medium text-gray-700 mb-1">Catatan Penilaian:</p>
                                                    <p class="text-sm text-gray-600">{{ $userSubmission->feedback }}</p>
                                                </div>
                                            @endif
                                            @if($userSubmission->penilai)
                                                <p class="text-xs text-gray-400 mt-2">Dinilai oleh: {{ $userSubmission->penilai->nama ?? $userSubmission->penilai->name }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">Tugas Anda belum dinilai.</p>
                                    @endif
                                @else
                                    {{-- Not Submitted Yet --}}
                                    @if($bimtek->status_pelaksanaan !== 'berlangsung')
                                        {{-- Bimtek not active --}}
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center gap-3">
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-gray-800">Bimtek Tidak Aktif</p>
                                                    <p class="text-sm text-gray-600">
                                                        @if($bimtek->status_pelaksanaan === 'persiapan')
                                                            Pengumpulan tugas belum dibuka. Bimtek belum dimulai.
                                                        @elseif($bimtek->status_pelaksanaan === 'selesai')
                                                            Bimtek sudah selesai. Anda tidak dapat mengumpulkan tugas.
                                                        @else
                                                            Bimtek dibatalkan.
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($tugas->isDeadlinePassed())
                                        <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                                            <div class="flex items-center gap-3">
                                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-medium text-red-800">Deadline Terlewat</p>
                                                    <p class="text-sm text-red-600">Anda tidak dapat mengumpulkan tugas karena deadline sudah terlewat.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <form action="{{ route('bimtek.tugas.submit', [$bimtek, $tugas]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="file_jawaban" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Upload File Jawaban <span class="text-red-500">*</span>
                                                </label>
                                                <input type="file" 
                                                       name="file_jawaban" 
                                                       id="file_jawaban"
                                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('file_jawaban') border-red-500 @enderror"
                                                       required>
                                                @error('file_jawaban')
                                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-1 text-sm text-gray-500">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR (max. 20MB)</p>
                                            </div>

                                            <button type="submit" class="w-full px-4 py-3 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                                Kumpulkan Tugas
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: Stats --}}
                <div class="space-y-6">
                    {{-- Statistics Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total Peserta</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $peserta->count() }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Sudah Mengumpulkan</span>
                                <span class="text-lg font-semibold text-green-600">{{ $tugas->pengumpulanTugas->count() }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Belum Mengumpulkan</span>
                                <span class="text-lg font-semibold text-red-600">{{ $peserta->count() - $tugas->pengumpulanTugas->count() }}</span>
                            </div>

                            {{-- Progress Bar --}}
                            @php
                                $progress = $peserta->count() > 0 ? ($tugas->pengumpulanTugas->count() / $peserta->count()) * 100 : 0;
                            @endphp
                            <div class="pt-2">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-500">Progress</span>
                                    <span class="font-medium">{{ number_format($progress, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Sudah Dinilai --}}
                            @if($canManage && $tugas->pengumpulanTugas->count() > 0)
                                <div class="pt-4 border-t">
                                    @php
                                        $dinilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->count();
                                        $avgNilai = $tugas->pengumpulanTugas->whereNotNull('nilai')->avg('nilai');
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Sudah Dinilai</span>
                                        <span class="text-lg font-semibold text-blue-600">{{ $dinilai }} / {{ $tugas->pengumpulanTugas->count() }}</span>
                                    </div>
                                    @if($avgNilai)
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-sm text-gray-500">Rata-rata Nilai</span>
                                            <span class="text-lg font-semibold text-gray-900">{{ number_format($avgNilai, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
