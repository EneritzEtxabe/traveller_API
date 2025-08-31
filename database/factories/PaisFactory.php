<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Pais;
use App\Models\Continente;
use App\Models\Idioma;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pais>
 */
class PaisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->country(),
            'capital' => $this->faker->city(),
            'bandera_url' => $this->faker->imageUrl(300, 200, 'flag', true),
            'conduccion' => $this->faker->randomElement(['Izquierda', 'Derecha']),
            'continente_id' => Continente::inRandomOrder()->first()?->id ?? Continente::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Pais $pais) {
            $idiomas = Idioma::inRandomOrder()->take(rand(1, 2))->pluck('id');
            $pais->idiomas()->syncWithoutDetaching($idiomas);
        });
    }
}
