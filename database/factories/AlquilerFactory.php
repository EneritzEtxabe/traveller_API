<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Alquiler;
use App\Models\Coche;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alquiler>
 */
class AlquilerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fecha_inicio = $this->faker->dateTimeBetween('-30 days', 'now');
        $fecha_fin = $this->faker->dateTimeBetween($fecha_inicio, $fecha_inicio->format('Y-m-d'). ' +15 days');

        return [
            'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $fecha_fin->format('Y-m-d'),
            'coste' => $this->faker->randomFloat(2, 50, 1000),
            'coche_id' => Coche::inRandomOrder()->first()?->id ?? Coche::factory(),
            'cliente_id' => User::where('rol','cliente')->inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
