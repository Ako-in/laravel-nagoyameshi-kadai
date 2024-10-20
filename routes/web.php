<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController; // Admin用のエイリアス
use App\Http\Controllers\Admin\TermController as AdminTermController;;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CompanyController as UserCompanyController; // 通常用のエイリアス
use App\Http\Controllers\TermController as UserTermController;

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
    
    // Route::get('user',[UserController::class,'index'])->name('user.index');
    // Route::get('user/{user}/edit',[UserController::class,'edit'])->name('user.edit');
    // Route::put('user/{user}',[UserController::class,'update'])->name('user.update');

    Route::get('restaurants/index',[Admin\RestaurantController::class,'index'])->name('restaurants.index');
    Route::get('restaurants/create',[Admin\RestaurantController::class,'create'])->name('restaurants.create');
    Route::post('restaurants/',[Admin\RestaurantController::class,'store'])->name('restaurants.store');
    Route::get('restaurants/{restaurant}',[Admin\RestaurantController::class,'show'])->name('restaurants.show');
    Route::get('restaurants/{restaurant}/edit',[Admin\RestaurantController::class,'edit'])->name('restaurants.edit');
    Route::put('restaurants/{restaurant}',[Admin\RestaurantController::class,'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}',[Admin\RestaurantController::class,'destroy'])->name('restaurants.destroy');
    // Route::resource('restaurants',Admin\RestaurantController::class);
    Route::get('categories/index',[Admin\CategoryController::class,'index'])->name('categories.index');
    Route::post('categories/',[Admin\CategoryController::class,'store'])->name('categories.store');
    Route::put('categories/{category}',[Admin\CategoryController::class,'update'])->name('categories.update');
    Route::get('categories/{category}/edit',[Admin\CategoryController::class,'edit'])->name('categories.edit');
    Route::delete('categories/{category}',[Admin\CategoryController::class,'destroy'])->name('categories.destroy');
    // Route::resource('categories',Admin\CategoryController::class);

    // Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);
    Route::get('company/index', [AdminCompanyController::class, 'index'])->name('company.index');
    Route::patch('company/{company}', [AdminCompanyController::class, 'update'])->name('company.update');
    Route::get('company/{company}/edit', [AdminCompanyController::class, 'edit'])->name('company.edit');

    Route::get('terms/index',[AdminTermController::class,'index'])->name('terms.index');
    Route::get('terms/{term}/edit',[AdminTermController::class,'edit'])->name('terms.edit');
    Route::put('terms/{term}',[AdminTermController::class,'update'])->name('terms.update');
});

// Route::group(['middleware' => 'guest:admin'], function () {
//     // Route::get('/',[HomeController::class,'index'])->name('home');
//     // Route::get('/home', function () {
//     //     if (auth()->user()->isAdmin()) {
//     //         return redirect()->route('admin.home');
//     //     }
//     //     return view('home');
//     // })->middleware('auth')->name('home');

// });
Route::get('restaurants/index',[RestaurantController::class,'index'])->name('restaurants.index');
// Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

Route::get('restaurants/{restaurant}',[RestaurantController::class,'show'])->name('restaurants.show');


Route::get('company',[UserCompanyController::class,'index'])->name('company.index');
Route::get('terms',[UserTermController::class,'index'])->name('terms.index');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');

    Route::resource('restaurants.reviews', ReviewController::class)
    ->only(['create'])
    ->names(['create'=>'restaurants.reviews.create']);
    Route::get('restaurants/{restaurant}/reviews',[ReviewController::class,'index'])->name('restaurants.reviews.index');
    Route::get('restaurants/{restaurant}/reviews/{review}',[ReviewController::class,'show'])->name('restaurants.reviews.show');
    Route::post('restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->middleware('auth')->name('restaurants.reviews.store');
    Route::get('restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('restaurants.reviews.edit');
    Route::match(['put', 'patch'],'restaurants/{restaurant}/reviews/{review}',[ReviewController::class,'update'])->name('restaurants.reviews.update');
    // Route::get('restaurants/{restaurant}/reviews/create',[ReviewController::class,'create'])->name('restaurants.reviews.create');
    // Route::post('restaurants/{restaurant}/reviews',[ReviewController::class,'store'])->name('restaurants.reviews.store');
    // Route::get('restaurants/{restaurant}/reviews/{review}/edit',[ReviewController::class,'edit'])->name('restaurants.reviews.edit');
    Route::delete('restaurants/{restaurant}/reviews/{review}',[ReviewController::class,'destroy'])->name('restaurants.reviews.destroy');


    Route::get('reservations',[ReservationController::class,'index'])->name('reservation.index');
    Route::get('restaurants/{restaurant}/reservations/create',[ReservationController::class,'create'])->name('restaurants.reservations.create');
    Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
    // Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store')->middleware('can:create,restaurant');
    // Route::delete('restaurants/reservations/{resercation}',[ReservationController::class,'destroy'])->name('restaurants.reservations.destroy');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservation.destroy');

    Route::get('favorites',[FavoriteController::class,'index'])->name('favorites.index');
    Route::post('favorites/{restaurant_id}',[FavoriteController::class,'store'])->name('favorites.store');
    Route::delete('favorites/{restaurant_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

});

Route::group(['middleware' => ['auth','verified','not.subscribed']], function () {
    // Route::group(['middleware' => [NotSubscribed::class]], function () {
        Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
        Route::post('subscription/store', [SubscriptionController::class, 'store'])->name('subscription.store');
});

Route::group(['middleware' => ['auth', 'verified','subscribed']], function () {
    // Route::group(['middleware' => [Subscribed::class]], function () {
        Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
        Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
        Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
});


