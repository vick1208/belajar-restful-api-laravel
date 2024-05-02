<?php

use App\Http\Controllers\UsersController;
use App\Http\Middleware\APIAuthMiddleware;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/users',[UsersController::class,'register']);
Route::post('/users/login',[UsersController::class,'login']);

Route::middleware(APIAuthMiddleware::class)->group(function(){
    Route::get('/users/current',[UsersController::class,'get']);
});
