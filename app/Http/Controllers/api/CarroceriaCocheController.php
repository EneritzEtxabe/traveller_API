<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarroceriaCocheResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\CarroceriaCoche;

class CarroceriaCocheController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/carroceriaCoches",
     *     summary="Listar todas las carrocerías de coche",
     *     description="Obtiene la lista de todas las carrocerías de coche disponibles. Solo accesible para usuarios registrados",
     *     tags={"Carrocerías de Coche"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de carrocerías de coche",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=3),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CarroceriaCoche")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $carroceriasCoche = CarroceriaCoche::get();
        $resultado = CarroceriaCocheResource::collection($carroceriasCoche);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/carroceriaCoches",
     *     summary="Crear una nueva carrocería de coche",
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir una nueva carrocería a la Base de Datos.",
     *     tags={"Carrocerías de Coche"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CarroceriaCocheDB")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Carrocería de coche creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/CarroceriaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Solo administradores pueden crear carrocerías.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo los administradores pueden guardar una nueva carrocería")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="nombre",
     *                     type="array",
     *                     @OA\Items(type="string", example="Ya existe esa carrocería en nuestra BBDD'")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar una nueva carrocería'], 403);
        };

        $data = $request->validate([
                'nombre' => 'required|string|unique:carroceria_coches,nombre'
            ],
            [
                'nombre.reqired' => 'Introduce una carroceria',
                'nombre.string'=>'El nombre de la carrocería tiene que ser una cadena de texto',
                'nombre.unique' =>'Ya existe esa carrocería en nuestra BBDD',
            ],
        );

        $carroceriaCoche = CarroceriaCoche::create($data);
        
        $resultado = new CarroceriaCocheResource($carroceriaCoche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/carroceriaCoches/{id}",
     *     summary="Mostrar una carrocería de coche específica (por id)",
     *     description="Obtiene los detalles de la carrocería de un coche por su ID. Solo accesible para usuarios registrados",
     *     tags={"Carrocerías de Coche"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la carrocería",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrocería encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/CarroceriaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrocería no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrocería no encontrada")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $carroceriaCoche = CarroceriaCoche::find($id);
        if(!$carroceriaCoche){
            return response()->json(['message'=>'Carrocería no encontrada'], 404);
        }
        $resultado = new CarroceriaCocheResource($carroceriaCoche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/carroceriaCoches/{id}",
     *     summary="Actualizar una carrocería de coche",
     *     description="Permite modificar los datos de la carrocería de un coche. Solo accesible para administradores y superadministradores.",
     *     tags={"Carrocerías de Coche"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la carrocería a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CarroceriaCocheDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrocería actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/CarroceriaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Sin permisos para actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede editar una carrocería")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrocería no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrocería no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El campo nombre es obligatorio.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar una carrocería'], 403);
        };
        $carroceriaCoche = CarroceriaCoche::find($id);
        if(!$carroceriaCoche){
            return response()->json(['message'=>'Carrocería no encontrada'], 404);
        }
        $data = $request->validate([
                'nombre' => 'required|string|unique:carroceria_coches,nombre'
            ],
            [
                'nombre.string'=>'El nombre de la carrocería tiene que ser una cadena de texto',
                'nombre.unique' =>'Ya existe esa carrocería en nuestra BBDD',
            ],
        );
        

        $carroceriaCoche->update($data);

        $resultado = new CarroceriaCocheResource($carroceriaCoche);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ],200);
    }

    /**
     * @OA\Delete(
     *     path="/api/carroceriaCoches/{id}",
     *     summary="Borrar una carrocería de coche",
     *     description="Permite eliminar una carrocería de coche. Solo accesible para administradores y superadministradores.",
     *     tags={"Carrocerías de Coche"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la carrocería a borrar",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrocería borrada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrocería borrada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Sin permisos para borrar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar una carrocería")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrocería no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Carrocería no encontrada")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar una carrocería'], 403);
        };
        $carroceriaCoche = CarroceriaCoche::find($id);
        if(!$carroceriaCoche){
            return response()->json(['message'=>'Carrocería no encontrada'], 404);
        }
        $carroceriaCoche->delete();

        return response()->json(['message'=>'Carrocería borrada correctamente'], 200);
    }
}
