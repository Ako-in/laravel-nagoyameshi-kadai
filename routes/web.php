<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;


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
    Route::get('restaurants/index',[Admin\RestaurantController::class,'index'])->name('restaurants.index');
    Route::get('restaurants/create',[Admin\RestaurantController::class,'create'])->name('restaurants.create');
    Route::post('restaurants',[Admin\RestaurantController::class,'store'])->name('restaurants.store');
    Route::get('restaurants/{restaurant}',[Admin\RestaurantController::class,'show'])->name('restaurants.show');
    Route::get('restaurants/{restaurant}/edit',[Admin\RestaurantController::class,'edit'])->name('restaurants.edit');
    Route::put('restaurants/{restaurant}',[Admin\RestaurantController::class,'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}',[Admin\RestaurantController::class,'destroy'])->name('restaurants.destroy');
    // Route::resource('restaurants',Admin\RestaurantController::class);
});

