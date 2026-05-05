<?php

namespace Database\Factories;

use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengajuan>
 */
class PengajuanFactory extends Factory
{
    protected $model = Pengajuan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul_rencana' => fake()->sentence(5),
            'tempat_kegiatan' => fake()->city(),
            'sumber_pembiayaan' => fake()->randomElement(['APBD', 'APBN', 'Hibah', 'Swadaya']),
            'tanggal_mulai_rencana' => fake()->dateTimeBetween('now', '+1 month'),
            'tanggal_selesai_rencana' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'deskripsi_rencana' => fake()->paragraph(),
            'jenis_kegiatan' => fake()->randomElement(['internal', 'eksternal']),
            'status_pengajuan' => 'diajukan',
            'is_draft' => false,
            'catatan_kepala' => null,
            'kepala_approved_at' => null,
            'catatan_ppk' => null,
            'status_rt' => 'belum_dipenuhi',
            'catatan_rt' => null,
            'catatan_logistik' => null,
        ];
    }

    /**
     * Indicate that the pengajuan is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pengajuan' => 'disetujui_final',
            'kepala_approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the pengajuan is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_pengajuan' => 'draft',
            'is_draft' => true,
        ]);
    }
}
