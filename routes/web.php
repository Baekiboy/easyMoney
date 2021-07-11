<?php

use App\Http\Controllers\AdminController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth.admin'])->group(function () {

        // Route::get('/', function () {
        //     return view('/welcome');
        // });

        Route::get('/', [AdminController::class,'users_list'])->name('home');
        Route::post('users/{id}', function ($id) {
            User::destroy($id);
            return redirect('/');
                });

        Route::get('/waiting', [AdminController::class,'waiting_users'])->name('waiting');
        Route::post('/verify/{id}', [AdminController::class, 'accept_user']);
        Route::delete('/verify/{id}', [AdminController::class, 'refuse_user']);

        Route::get('/dashboard', function () {
            return view('/dashboard');
        })->name('dashboard');
    });
});



require __DIR__ . '/auth.php';
