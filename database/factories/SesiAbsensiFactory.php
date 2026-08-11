<?php

namespace Database\Factories;

use App\Models\Bimtek;
use App\Models\SesiAbsensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SesiAbsensi>
 */
class SesiAbsensiFactory extends Factory
{
    protected $model = SesiAbsensi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bimtek_id' => Bimtek::factory(),
            'nama_sesi' => 'Sesi '.fake()->numberBetween(1, 10),
            'status' => 'ditutup',
            'user_id' => null,
        ];
    }

    /**
     * Indicate that the sesi is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terbuka',
        ]);
    }
}
