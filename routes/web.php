<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

Route::group(['prefix'=>'admin','as'=>'admin.','middleware'=>'auth:admin'],function(){
    Route::get('home',[Admin\HomeController::class,'index'])->name('home');
    Route::get('users/index',[Admin\UserController::class,'index'])->name('users.index');
    // Route::post('users/{id}',[Admin\UserController::class,'show'])->whereNumber('id')->name('users.show');
    Route::get('users/{id}',[Admin\UserController::class,'show'])->name('users.show');
});

