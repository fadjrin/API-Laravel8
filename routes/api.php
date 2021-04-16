<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StoreController;


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

Route::get('/', function(){
    return response()->json([
    	'status' => false,
    	'message' => 'Masukan token anda',
    	'data' => []
    ])->setStatusCode(401);		
})->name('notauth');

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:api']],function(){
	Route::get('product/{search?}', [StoreController::class, 'getproduct']);
	Route::post('cart', [StoreController::class, 'createcart']);
	Route::put('cart/{id?}', [StoreController::class, 'updatecart']);
	Route::get('cart/{user_id?}', [StoreController::class, 'getcart']);
});

