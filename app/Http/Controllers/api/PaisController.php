<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaisResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Pais;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Travel App API",
 *         description="Documentación de la API Travel App"
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Servidor local"
 *     )
 * )
 * * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 */


class PaisController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/paises",
     *     summary="Listar todos los países",
     *     description="Obtiene la lista de todos los paises disponibles. Accesible tanto para usuarios registrados como no registrados",
     *     tags={"Países"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de países",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=3),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pais")
     *             )
     *         )
     *     )
     * )
     */   
    public function index()
    {
        $paises = Pais::with('continente', 'idiomas','lugares')->get();
        $resultado = PaisResource::collection($paises);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);

    }

    /**
     * @OA\Post(
     *     path="/api/paises",
     *     summary="Crear un nuevo país",
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir un nuevo país a la Base de Datos.",
     *     operationId="storePais",
     *     tags={"Países"},
     *     security={{"sanctum":{}}},

     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PaisDB")
     *     ),

     *     @OA\Response(
     *         response=201,
     *         description="País creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Pais")
     *         )
     *     ),

     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado: solo administradores"
     *     ),

     *     @OA\Response(
     *         response=422,
     *         description="Error de validación en los datos enviados"
     *     )
     * )
     */

    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar un nuevo país'], 403);
        };

        $data = $request->validate([
            'nombre' => 'required|unique:paises,nombre|string|max:255',
            'continente_id' => 'required|exists:continentes,id',
            'capital' => 'required|string|max:255',
            'bandera_url' => 'nullable|url',
            'conduccion' => 'nullable|string|in:izquierda,derecha',

            'idiomas' => 'nullable|array',
            'idiomas.*'=>'exists:idiomas,id'
        ],
        [
            'nombre.required' => 'Introduce el nombre del país.',
            'nombre.unique' => 'Ya existe un país con ese nombre.',
            'nombre.string' => 'El nombre del país debe ser una cadena de texto.',
            'nombre.max' => 'El nombre del país no puede superar los 255 caracteres.',

            'continente_id.required' => 'Introduce el id del continente alque pertenece este país.',
            'continente_id.exists' => 'El continente seleccionado no es válido.',

            'capital.required' => 'Introduce la capital de este país.',
            'capital.string' => 'La capital debe ser una cadena de texto.',
            'capital.max' => 'La capital no puede superar los 255 caracteres.',

            'bandera_url.url' => 'La URL de la bandera no tiene un formato válido.',

            'conduccion.in' => 'El campo conducción debe ser "izquierda" o "derecha".',

            'idiomas.array' => 'El campo idiomas debe ser un array.',
            'idiomas.*.exists' => 'Uno o más idiomas seleccionados no existen en nuestra BBDD.',
        ],
        );

        $pais = Pais::create($data);

        // Asignar idiomas en la tabla intermedia si vienen
        if (isset($data['idiomas'])) {
            $pais->idiomas()->sync($data['idiomas']);
        }
        
        $resultado = new PaisResource($pais);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/paises/{id}",
     *     summary="Mostrar un país específico (por id)",
     *     description="Obtiene los detalles de un país por su ID. Accesible tanto para usuarios registrados como no registrados",
     *     operationId="showPais",
     *     tags={"Países"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del país",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="País encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Pais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="País no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="País no encontrado")
     *         )
     *     )
     * )
     */

    public function show(string $id)
    {
        $unPais = Pais::with('continente', 'idiomas','lugares')->find($id);
        if(!$unPais){
            return response()->json(['message'=>'País no encontrado'], 404);
        }
        $resultado = new PaisResource($unPais);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/paises/{id}",
     *     summary="Actualizar un país",
     *     description="Permite modificar los datos de un país. Solo accesible para administradores y superadministradores.",
     *     operationId="updatePais",
     *     tags={"Países"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del país a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PaisDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="País actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Pais")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede editar un país")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="País no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="País no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El campo nombre ya existe.")
     *         )
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar un país'], 403);
        };
        $pais = Pais::find($id);
        if(!$pais){
            return response()->json(['message'=>'País no encontrado'], 404);
        }

        $data = $request->validate([
                'nombre' => 'sometimes|unique:paises,nombre|string|max:255',
                'continente_id' => 'sometimes|exists:continentes,id',
                'capital' => 'sometimes|string|max:255',
                'bandera_url' => 'nullable|url',
                'conduccion' => 'nullable|string|in:izquierda,derecha',

                'idiomas' => 'nullable|array',
                'idiomas.*'=>'exists:idiomas,id'
            ],
            [
                'nombre.unique' => 'Ya existe un país con ese nombre.',
                'nombre.string' => 'El nombre del país debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del país no puede superar los 255 caracteres.',

                'continente_id.exists' => 'El continente seleccionado no es válido.',

                'capital.string' => 'La capital debe ser una cadena de texto.',
                'capital.max' => 'La capital no puede superar los 255 caracteres.',

                'bandera_url.url' => 'La URL de la bandera no tiene un formato válido.',

                'conduccion.in' => 'El campo conducción debe ser "izquierda" o "derecha".',

                'idiomas.array' => 'El campo idiomas debe ser un array.',
                'idiomas.*.exists' => 'Uno o más idiomas seleccionados no existen en nuestra BBDD.',
            ],
        );

        $pais->update($data);

        if (isset($data['idiomas'])) {
            $pais->idiomas()->sync($data['idiomas']);
        }

        $resultado = new PaisResource($pais);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/paises/{id}",
     *     summary="Eliminar un país",
     *     description="Permite eliminar un país. Solo accesible para administradores y superadministradores.",
     *     operationId="destroyPais",
     *     tags={"Países"},
     *     security={{"sanctum": {}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del país a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="País borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="País borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar un país")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="País no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="País no encontrado")
     *         )
     *     )
     * )
     */

    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar un país'], 403);
        };
        $pais = Pais::find($id);
        if(!$pais){
            return response()->json(['message'=>'País no encontrado'], 404);
        }
        $pais->delete();

        return response()->json(['message'=>'País borrado correctamente'], 200);
    }
}
