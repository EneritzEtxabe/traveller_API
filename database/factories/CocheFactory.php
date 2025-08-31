<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coche>
 */
class CocheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marca_id' => rand(1, 10),
            'carroceria_id' => rand(1, 7),
            'ano' => $this->faker->numberBetween(2010, 2024),
            'nPlazas' => $this->faker->randomElement([2, 4, 5, 7]),
            'cambio' => $this->faker->randomElement(['manual', 'automático']),
            'estado' => $this->faker->randomElement(['disponible', 'mantenimiento']),
            'costeDia' => $this->faker->randomFloat(2, 20, 99.99),
            'pais_id' => rand(1, 10),
        ];
    }
}
