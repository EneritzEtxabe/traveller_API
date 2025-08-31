<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TipoLugar",
 *     type="object",
 *     title="Tipo de Lugar (Recurso API)",
 *     description="Recurso que representa un tipo de lugar (por ejemplo, playa, montaña, museo, etc.)",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID único del tipo de lugar"
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="Playa",
 *         description="Nombre del tipo de lugar"
 *     )
 * )
 */
class TipoLugarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resultado =[
            'id' => $this->id,
            'nombre' => $this->nombre,
        ];
        return $resultado;
    }
}
