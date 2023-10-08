<?php

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login", [ApiController::class, 'login']);
Route::post("seller/register", [ApiController::class, 'SellerRegister']);
Route::post("buyer/register", [ApiController::class, 'buyerRegister']);
Route::post("user/update", [ApiController::class, 'updateProfile']);

Route::get('categories', [ApiController::class, 'getCategories']);

Route::get("seller/orders/{seller_id}", [ApiController::class, 'sellerGetOrders']);
Route::post('seller/products/create', [ApiController::class, 'createProduct']);
Route::get('seller/products/{id}', [ApiController::class, 'sellerGetProducts']);

Route::get("buyer/products", [ApiController::class, 'buyerGetProducts']);
Route::get("buyer/orders/{buyer_id}", [ApiController::class, 'buyerGetOrders']);
Route::post("buyer/order/complete", [ApiController::class, 'buyerCompleteOrder']);