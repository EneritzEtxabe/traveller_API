<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LugarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Lugar;

class LugarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/lugares",
     *     summary="Listar todos los lugares",
     *     tags={"Lugares"},
     *     description="Obtiene la lista de todos los lugares disponibles. Accesible tanto para usuarios registrados como no registrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lugares obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Lugar")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $lugares = Lugar::with('pais','tipoLugares')->get();
        $resultado = LugarResource::collection($lugares);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/lugares",
     *     summary="Crear un nuevo lugar",
     *     tags={"Lugares"},
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir un nuevo lugar a la Base de Datos.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LugarDB")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lugar creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Solo administradores pueden crear lugares.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo los administradores pueden guardar un nuevo lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validación fallida",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar un nuevo lugar'], 403);
        };

        $data = $request->validate(
        [
            'nombre' => 'required|string|unique:lugares,nombre|max:255',
            'pais_id' => 'required|exists:paises,id',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|url',
            'web_url' => 'nullable|url',
            'localizacion_url' => 'nullable|url',

            'tipoLugares' => 'array',
            'tipoLugares.*' => 'exists:tipo_lugares,id',
        ],
        [
            'nombre.required' => 'Introduce el nombre del lugar.',
            'nombre.string' => 'El nombre del lugar debe ser una cadena de texto.',
            'nombre.unique' => 'Ya existe un lugar con ese nombre.',
            'nombre.max' => 'El nombre del lugar no puede superar los 255 caracteres.',

            'pais_id.required' => 'Introduce un id del país.',
            'pais_id.exists' => 'El país seleccionado no es válido.',

            'descripcion.string' => 'La descripción debe ser una cadena de texto.',

            'imagen_url.url' => 'La URL de la imagen no tiene un formato válido.',
            'web_url.url' => 'La URL del sitio web no tiene un formato válido.',
            'localizacion_url.url' => 'La URL de localización no tiene un formato válido.',

            'tipoLugares.array' => 'El campo tipo de lugares debe ser un array.',
            'tipoLugares.*.exists' => 'Uno o más tipos de lugares seleccionados no son válidos.',
        ],
        );

        $lugar = Lugar::create($data);

        // Asignar tipos en la tabla intermedia si vienen
        if (isset($data['tipoLugares'])) {
            $lugar->tipoLugares()->sync($data['tipoLugares']);
        }
        
        $resultado = new LugarResource($lugar);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/lugares/{id}",
     *     summary="Mostrar un lugar específico (por id)",
     *     tags={"Lugares"},
     *     description="Obtiene los detalles de un lugar por su ID. Accesible tanto para usuarios registrados como no registrados",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del lugar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lugar no encontrado")
     *         )
     *     )
     * )
     */

    public function show(string $id)
    {
        $lugar = Lugar::with('pais', 'tipoLugares')->find($id);
        if(!$lugar){
            return response()->json(['message'=>'Lugar no encontrado'], 404);
        }
        $resultado = new LugarResource($lugar);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/lugares/{id}",
     *     summary="Actualizar un lugar",
     *     tags={"Lugares"},
     *     description="Permite modificar los datos de un lugar. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del lugar a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LugarDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede editar un lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lugar no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe un lugar con ese nombre en nuestra BBDD.")
     *         )
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar un lugar'], 403);
        };
        $lugar = Lugar::find($id);
        if(!$lugar){
            return response()->json(['message'=>'Lugar no encontrado'], 404);
        }

        $data = $request->validate(
            [
                'nombre' => 'sometimes|string|unique:lugares,nombre|max:255',
                'pais_id' => 'sometimes|exists:paises,id',
                'descripcion' => 'nullable|string',
                'imagen_url' => 'nullable|url',
                'web_url' => 'nullable|url',
                'localizacion_url' => 'nullable|url',

                'tipoLugares' => 'array',
                'tipoLugares.*' => 'exists:tipo_lugares,id',
            ],
            [
                'nombre.string' => 'El nombre del lugar debe ser una cadena de texto.',
                'nombre.unique' => 'Ya existe un lugar con ese nombre.',
                'nombre.max' => 'El nombre del lugar no puede superar los 255 caracteres.',

                'pais_id.exists' => 'El país seleccionado no es válido.',

                'descripcion.string' => 'La descripción debe ser una cadena de texto.',

                'imagen_url.url' => 'La URL de la imagen no tiene un formato válido.',
                'web_url.url' => 'La URL del sitio web no tiene un formato válido.',
                'localizacion_url.url' => 'La URL de localización no tiene un formato válido.',

                'tipoLugares.array' => 'El campo tipo de lugares debe ser un array.',
                'tipoLugares.*.exists' => 'Uno o más tipos de lugares seleccionados no son válidos.',
            ],
        );

        $lugar->update($data);

        if (isset($data['tipoLugares'])) {
            $lugar->tipoLugares()->sync($data['tipoLugares']);
        }

        $resultado = new LugarResource($lugar);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/lugares/{id}",
     *     summary="Eliminar un lugar",
     *     tags={"Lugares"},
     *     description="Permite eliminar un lugar. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del lugar a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lugar borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar un lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lugar no encontrado")
     *         )
     *     )
     * )
     */

    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar un lugar'], 403);
        };
        $lugar = Lugar::find($id);
        if(!$lugar){
            return response()->json(['message'=>'Lugar no encontrado'], 404);
        }
        $lugar->delete();

        return response()->json(['message'=>'Lugar borrado correctamente'], 200);
    }
}
