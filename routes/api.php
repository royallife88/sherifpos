<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductClassController;
use App\Http\Controllers\Api\ProductController;
use App\Models\Tutorial;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('api')->get('/tutorials/get-tutorials-data-array-by-category/{category_id}', 'TutorialController@getTutorialsDataArrayByCategory');
Route::middleware('api')->get('/tutorials/get-tutorials-categories-array', 'TutorialController@getTutorialsCategoryArray');

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::resource('product-class', ProductClassController::class);
    Route::get('product-class', [ProductClassController::class, 'index']);
    Route::post('product-class', [ProductClassController::class, 'store']);
    Route::put('product-class/{id}', [ProductClassController::class, 'update']);
    Route::delete('product-class/{id}', [ProductClassController::class, 'destroy']);

    Route::get('product', [ProductController::class, 'index']);
    Route::post('product', [ProductController::class, 'store']);
    Route::put('product/{id}', [ProductController::class, 'update']);
    Route::delete('product/{id}', [ProductController::class, 'destroy']);

    Route::get('size', [SizeController::class, 'index']);
    Route::post('size', [SizeController::class, 'store']);
    Route::put('size/{id}', [SizeController::class, 'update']);
    Route::delete('size/{id}', [SizeController::class, 'destroy']);
});
