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
Route::post('/email/verify/{user}', [AuthController::class, 'verify']);
Route::post('/forgot-password',[AuthController::class,'reset_email'])->name('password.request');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.reset');
Route::group(['middleware' => 'auth:sanctum'], function () {



    Route::post('/profile/update', [ProfileController::class, 'index']);
    Route::post('/verify/docs', [ProfileController::class, 'doc_verify']);
    Route::post('/bank/account', [ProfileController::class, 'bank_verify']);
    Route::get('/verified', [ProfileController::class, 'current']);


    Route::prefix('card')->group(function () {
        Route::get('/create', [cardConroller::class, 'make_card']);
        Route::get('/fetch', [cardConroller::class, 'card']);
        Route::post('/convert', [cardConroller::class, 'conversion']);
        Route::post('/transfer', [cardConroller::class, 'transfer']);
        Route::post('/make_transfer', [cardConroller::class, 'make_transfer']);
    });

    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/user', [AdminController::class, 'user']);
    Route::post('/user/verify', [AdminController::class, 'verify']);
});
