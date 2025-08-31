<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User (Recurso API)",
 *     description="Información del usuario cliente",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", format="email", example="juan@ebis.com"),
 *     @OA\Property(property="telefono", type="string", example="123456789"),
 *     @OA\Property(property="dni", type="string", example="12345678X"),
 *     @OA\Property(property="rol", type="string", example="cliente"),
 * )
 */
class UserResource extends JsonResource
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
            'nombre'=>$this->nombre,
            'email'=>$this->email,
            'telefono'=>$this->telefono,
            'dni' => $this->dni,
            'rol' => $this->rol,
        ];
        return $resultado;
    }
}
