<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MarcaCoche",
 *     type="object",
 *     title="Marcas de Coche (Recurso API)",
 *     required={"id", "nombre"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único de la marca del coche",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         description="Nombre de la marca del coche",
 *         example="Toyota"
 *     )
 * )
 */
class MarcaCocheResource extends JsonResource
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
