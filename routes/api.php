<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiningRoomController;
use App\Http\Controllers\Api\DiningTableController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductClassController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\SalesPromotionController;
use App\Http\Controllers\Api\SettingController;
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

    Route::get('store', [StoreController::class, 'index']);
    Route::post('store', [StoreController::class, 'store']);
    Route::put('store/{id}', [StoreController::class, 'update']);
    Route::delete('store/{id}', [StoreController::class, 'destroy']);

    Route::get('customer-type', [CustomerTypeController::class, 'index']);
    Route::post('customer-type', [CustomerTypeController::class, 'customer-type']);
    Route::put('customer-type/{id}', [CustomerTypeController::class, 'update']);
    Route::delete('customer-type/{id}', [CustomerTypeController::class, 'destroy']);

    Route::get('sales-promotion', [SalesPromotionController::class, 'index']);
    Route::post('sales-promotion', [SalesPromotionController::class, 'store']);
    Route::put('sales-promotion/{id}', [SalesPromotionController::class, 'update']);
    Route::delete('sales-promotion/{id}', [SalesPromotionController::class, 'destroy']);

    Route::get('dining-room', [DiningRoomController::class, 'index']);
    Route::post('dining-room', [DiningRoomController::class, 'store']);
    Route::put('dining-room/{id}', [DiningRoomController::class, 'update']);
    Route::delete('dining-room/{id}', [DiningRoomController::class, 'destroy']);

    Route::get('dining-table', [DiningTableController::class, 'index']);
    Route::post('dining-table', [DiningTableController::class, 'store']);
    Route::put('dining-table/{id}', [DiningTableController::class, 'update']);
    Route::delete('dining-table/{id}', [DiningTableController::class, 'destroy']);

    Route::post('order', [OrderController::class, 'store']);

    Route::get('setting', [SettingController::class, 'index']);
    Route::post('setting', [SettingController::class, 'store']);
});
