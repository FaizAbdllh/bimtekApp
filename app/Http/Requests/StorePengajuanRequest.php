<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isPegawaiInternal();
    }

    /**
     * Normalize verification options before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('jenis_kegiatan') === 'internal') {
            $this->merge([
                'butuh_verifikasi_dokumen' => false,
                'syarat_dokumen' => null, // 💡 DISINKRONKAN: Menggunakan kunci syarat_dokumen
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Jika simpan draft, validasi lebih longgar
        $isDraft = $this->has('save_draft');

        $base = [
            'judul_rencana' => [$isDraft ? 'required' : 'required', 'string', 'max:255'],
            'tempat_kegiatan_rencana' => [$isDraft ? 'nullable' : 'required', 'string', 'max:255'],
            'sumber_pembiayaan' => [$isDraft ? 'nullable' : 'required', 'string', 'max:255'],
            'tanggal_mulai_rencana' => [$isDraft ? 'nullable' : 'required', 'date', $isDraft ? null : 'after_or_equal:today'],
            'tanggal_selesai_rencana' => [$isDraft ? 'nullable' : 'required', 'date', 'after_or_equal:tanggal_mulai_rencana'],
            'deskripsi_rencana' => [$isDraft ? 'nullable' : 'required', 'string', 'max:5000'],
            'mode_pelaksanaan' => [$isDraft ? 'nullable' : 'required', 'in:offline,online,hybrid'],
            'jumlah_peserta' => ['nullable', 'integer', 'min:1'],
            'jenis_kegiatan' => [$isDraft ? 'nullable' : 'required', 'in:internal,eksternal'],
            'catatan_logistik' => ['nullable', 'string', 'max:5000'],
        ];

        // 💡 DISINKRONKAN: Validasi verifikasi dokumen menggunakan 'syarat_dokumen'
        $verif = [
            'butuh_verifikasi_dokumen' => ['nullable', 'boolean'],
            'syarat_dokumen' => ['nullable', 'array'],
        ];

        if (! $isDraft && $this->boolean('butuh_verifikasi_dokumen')) {
            $verif['syarat_dokumen'] = ['required', 'array', 'min:1'];
        }

        return array_merge($base, $verif);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul_rencana' => 'judul kegiatan',
            'jumlah_peserta' => 'jumlah peserta',
            'tempat_kegiatan_rencana' => 'tempat kegiatan',
            'sumber_pembiayaan' => 'sumber pembiayaan',
            'tanggal_mulai_rencana' => 'tanggal mulai',
            'tanggal_selesai_rencana' => 'tanggal selesai',
            'deskripsi_rencana' => 'deskripsi kegiatan',
            'mode_pelaksanaan' => 'mode pelaksanaan',
            'jenis_kegiatan' => 'jenis kegiatan',
            'catatan_logistik' => 'catatan logistik',
            'syarat_dokumen' => 'dokumen persyaratan',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul_rencana.required' => 'Judul kegiatan wajib diisi.',
            'jumlah_peserta.integer' => 'Jumlah peserta harus berupa angka.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal :min orang.',
            'tempat_kegiatan_rencana.required' => 'Tempat kegiatan wajib diisi.',
            'sumber_pembiayaan.required' => 'Sumber pembiayaan wajib diisi.',
            'tanggal_mulai_rencana.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai_rencana.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai_rencana.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai_rencana.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'deskripsi_rencana.required' => 'Deskripsi kegiatan wajib diisi.',
            'mode_pelaksanaan.required' => 'Mode pelaksanaan wajib dipilih.',
            'mode_pelaksanaan.in' => 'Mode pelaksanaan tidak valid.',
            'jenis_kegiatan.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis_kegiatan.in' => 'Jenis kegiatan tidak valid.',
            'syarat_dokumen.required' => 'Minimal cantumkan 1 dokumen persyaratan jika verifikasi dokumen diaktifkan.',
        ];
    }
}