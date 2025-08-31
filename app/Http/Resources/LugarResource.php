<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Lugar",
 *     title="Lugar (Recurso API)",
 *     description="Respuesta de la API para un lugar turístico",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="nombre", type="string", example="Cataratas del Iguazú"),
 *     @OA\Property(property="pais", type="string", example="Argentina"),
 *     @OA\Property(property="descripcion", type="string", nullable=true, example="Impresionante sistema de cascadas en la frontera de Argentina y Brasil."),
 *     @OA\Property(property="imagen_url", type="string", format="url", nullable=true, example="https://example.com/iguazu.jpg"),
 *     @OA\Property(property="web_url", type="string", format="url", nullable=true, example="https://turismo.argentina.gob.ar/iguazu"),
 *     @OA\Property(
 *         property="tipoLugares",
 *         type="array",
 *         description="Tipos de lugar (ej: playa, montaña, parque natural...)",
 *         @OA\Items(type="string", example="Parque natural")
 *     )
 * )
 */

class LugarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing(['pais','tipoLugares']);

        $resultado =[
            'id' => $this->id,
            'nombre' => $this->nombre,
            'pais'=>$this->pais->nombre,
            'descripcion'=>$this->descripcion,
            'imagen_url'=>$this->imagen_url,
            'web_url' => $this->web_url,
            'tipo_lugar' => $this->tipoLugares->pluck('nombre'),
        ];
        return $resultado;
    }
}
