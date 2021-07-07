<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/**
 * ============================================================================================================
 * Auth Routes
 * ============================================================================================================
 */
Route::post('/students', [StudentsController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function (Request $request) {
    return response()->json(["message" => ["type" => 'error', "description" => 'Unauthorised']], 401);
})->name('login');

/**
 * ===========================================================================================================
 * Private Routes
 * ===========================================================================================================
 */
Route::group(['middleware' => ['auth:sanctum', 'checkAuthSession', 'adminAuth']], function () {
    /**
     * =======================================================================================================
     * Auth Routes
     * =======================================================================================================
     */
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});