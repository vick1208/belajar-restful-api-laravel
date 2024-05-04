<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactsController;
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
    Route::patch('/users/current',[UsersController::class,'update']);
    Route::delete('/users/logout',[UsersController::class,'logout']);

    Route::post('/contacts',[ContactsController::class,'create']);
    Route::get('/contacts', [ContactsController::class, 'search']);
    Route::get('/contacts/{id}',[ContactsController::class,'get'])->where('id','[0-9]+');
    Route::put('/contacts/{id}',[ContactsController::class,'update'])->where('id','[0-9]+');
    Route::delete('/contacts/{id}',[ContactsController::class,'delete'])->where('id','[0-9]+');

    Route::post('/contacts/{idContact}/addresses',[AddressController::class,'create'])
    ->where('idContact','[0-9]+');
    Route::get('/contacts/{idContact}/addresses/{idAddress}',[AddressController::class,'get'])
    ->where('idContact','[0-9]+')
    ->where('idAddress','[0-9]+');
});
