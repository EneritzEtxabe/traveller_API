<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\PaisController;
use App\Http\Controllers\api\IdiomaController;
use App\Http\Controllers\api\LugarController;
use App\Http\Controllers\api\TipoLugarController;
use App\Http\Controllers\api\MarcaCocheController;
use App\Http\Controllers\api\CarroceriaCocheController;
use App\Http\Controllers\api\CocheController;
use App\Http\Controllers\api\AlquilerController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\LoginController;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function(Request $request){
        return $request->user();
    });

    Route::apiResource('users', UserController::class)->only(['index','show','update','destroy']);    

    Route::apiResource('paises', PaisController::class)->only(['store','update','destroy']);
    Route::apiResource('idiomas', IdiomaController::class);
    Route::apiResource('lugares', LugarController::class)->only(['store','update','destroy']);
    Route::apiResource('tipoLugares', TipoLugarController::class);

    Route::apiResource('marcaCoches', MarcaCocheController::class);
    Route::apiResource('carroceriaCoches', CarroceriaCocheController::class);
    Route::apiResource('coches', CocheController::class);
    Route::apiResource('alquileres', AlquilerController::class);
});

Route::apiResource('users', UserController::class)->only('store');  
Route::apiResource('paises', PaisController::class)->only(['index','show']);
Route::apiResource('lugares', LugarController::class)->only(['index','show']);

Route::post('login', [LoginController::class,'login']);
