<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Lugar;
use App\Models\Pais;
use App\Models\TipoLugar;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lugar>
 */
class LugarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->city(),
            'descripcion' => $this->faker->paragraph(),
            'imagen_url' => $this->faker->imageUrl(640, 480, 'travel', true),
            'web_url' => $this->faker->url(),
            'localizacion_url' => "https://www.google.com/maps?q={$this->faker->latitude()},{$this->faker->longitude()}",
            'pais_id' => Pais::inRandomOrder()->first()?->id ?? Pais::factory(),
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (Lugar $lugar) {
            $tipos = TipoLugar::inRandomOrder()->take(rand(1, 2))->pluck('id');
            $lugar->tipoLugares()->syncWithoutDetaching($tipos);
        });
    }
}
