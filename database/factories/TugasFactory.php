<?php

namespace Database\Factories;

use App\Models\Bimtek;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tugas>
 */
class TugasFactory extends Factory
{
    protected $model = Tugas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bimtek_id' => Bimtek::factory(),
            'judul' => fake()->sentence(4),
            'deskripsi' => fake()->paragraph(),
            'deadline' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'file_instruksi_path' => null,
        ];
    }
}
