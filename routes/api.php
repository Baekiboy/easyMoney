<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use App\Http\Controllers\CardConroller;
use App\Http\Controllers\AdminController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'index']);

Route::group(['middleware' => 'auth:sanctum'], function () {



    Route::post('/profile/update', [ProfileController::class, 'index']);
    Route::post('/verify/docs', [ProfileController::class, 'doc_verify']);
    Route::post('/bank/account', [ProfileController::class, 'bank_verify']);
    Route::get('/verified', [ProfileController::class, 'current']);
    Route::get('/card/create', [cardConroller::class, 'make_card']);
    Route::get('/card/fetch', [cardConroller::class, 'card']);
    Route::post('/card/convert', [cardConroller::class, 'conversion']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/user', [AdminController::class, 'user']);
    Route::post('/user/verify', [AdminController::class, 'verify']);

});
