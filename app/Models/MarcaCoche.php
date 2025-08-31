<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="MarcaCocheDB",
 *     type="object",
 *     title="Marca de Coche (BBDD)",
 *     required={"nombre"},
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         description="Nombre de la marca del coche",
 *         example="Seat"
 *     )
 * )
 */
class MarcaCoche extends Model
{
    protected $table = 'marca_coches';
    protected $fillable = ['nombre'];

    // Relación Marca:Coche (1:N)
    public function coches()
    {
        return $this->hasMany(Coche::class);
    }
}
