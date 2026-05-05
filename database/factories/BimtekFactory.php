<?php

namespace Database\Factories;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bimtek>
 */
class BimtekFactory extends Factory
{
    protected $model = Bimtek::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengajuan_id' => Pengajuan::factory(),
            'pic_user_id' => null,
            'judul_final' => fake()->sentence(4),
            'tanggal_mulai_aktual' => fake()->dateTimeBetween('now', '+1 month'),
            'tanggal_selesai_aktual' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'lokasi_aktual' => fake()->city(),
            'anggaran_disetujui' => fake()->randomFloat(2, 5000000, 50000000),
            'deskripsi_jadwal' => fake()->paragraph(),
            'daftar_pemateri' => null,
            'file_surat_undangan_path' => null,
            'status_pelaksanaan' => 'persiapan',
            'syarat_kehadiran_persen' => 80,
            'syarat_tugas_persen' => 70,
            'syarat_tugas_wajib' => false,
            'butuh_verifikasi_dokumen' => false,
            'jenis_dokumen_wajib' => null,
        ];
    }

    /**
     * Indicate that the Bimtek requires document verification.
     */
    public function requiresVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'butuh_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['ktp', 'ijazah'],
        ]);
    }

    /**
     * Indicate that the Bimtek does not require document verification.
     */
    public function noVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'butuh_verifikasi_dokumen' => false,
            'jenis_dokumen_wajib' => null,
        ]);
    }

    /**
     * Set specific status
     */
    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pelaksanaan' => $status,
        ]);
    }
}
