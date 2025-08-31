<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="CarroceriaCocheDB",
 *     title="Carrocería de Coche (BBDD)",
 *     description="Esquema para crear o actualizar una carrocería de coche (entrada)",
 *     type="object",
 *     required={"nombre"},
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="Hatchback",
 *         description="Nombre de la carrocería"
 *     )
 * )
 */
class CarroceriaCoche extends Model
{
    protected $table = 'carroceria_coches';
    protected $fillable = ['nombre'];

    // Relación carroceria:coche 1:N
    public function coches()
    {
        return $this->hasMany(Coche::class);
    }
}
