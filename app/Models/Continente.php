<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ContinenteDB",
 *     type="object",
 *     title="Continente (BBDD)",
 *     description="Modelo Continente que representa un continente",
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="América del Sur",
 *         description="Nombre del continente"
 *     )
 * )
 */
class Continente extends Model
{
    protected $table = 'continentes';
    protected $fillable = ['nombre'];

    //Relación continente:país (1:N)
    public function paises()
    {
        return $this->hasMany(Pais::class);
    }
}
