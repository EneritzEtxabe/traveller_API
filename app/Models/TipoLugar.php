<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="TipoLugarDB",
 *     type="object",
 *     title="Tipo de Lugar (BBDD)",
 *     description="Esquema del tipo de lugar tal y como está almacenado en la base de datos",
 *     @OA\Property(property="nombre", type="string", example="Alta montaña")
 * )
 */
class TipoLugar extends Model
{
    protected $table = 'tipo_lugares';
    protected $fillable = ['nombre'];

    //Relación tipo:lugar (N:N)
    public function lugares()
    {
        return $this->belongsToMany(Lugar::class, 'lugar_tipo_rel');
    }
}
