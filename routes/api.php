<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BateauController;
use App\Http\Controllers\MissileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Routes pour battleship-ia
 */
// ->middleware(['auth:sanctum'])
Route::prefix('battleship-ia')->group(function () {
    Route::post('bateaux/placer', [BateauController::class, 'placer']);
    Route::post('missiles', [MissileController::class, 'lancer']);
    Route::put('missiles/{coordonnees}', [MissileController::class, 'store']);
});

