<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Idioma",
 *     type="object",
 *     title="Idioma (Recurso API)",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID del idioma"
 *     ),
 *     @OA\Property(
 *         property="iso_639_1",
 *         type="string",
 *         example="es",
 *         description="Código ISO 639-1 del idioma"
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="Español",
 *         description="Nombre del idioma"
 *     )
 * )
 */
class IdiomaResource extends JsonResource
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
            'iso_639_1'=>$this->iso_639_1,
            'nombre' => $this->nombre,
        ];
        return $resultado;
    }
}
