<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="IdiomaDB",
 *     type="object",
 *     title="Idioma (BBDD)",
 *     required={"nombre", "iso_639_1"},
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="Catalán",
 *         description="Nombre del idioma"
 *     ),
 *     @OA\Property(
 *         property="iso_639_1",
 *         type="string",
 *         example="ca",
 *         description="Código ISO 639-1 del idioma"
 *     ),
 * )
 */
class Idioma extends Model
{
    protected $table = 'idiomas';
    protected $fillable = ['nombre','iso_639_1'];

    //Relación pais:idiomas (N:N) 
    public function paises()
    {
        return $this->belongsToMany(Pais::class, 'idioma_pais_rel','pais_id','idioma_id');
    }
}
