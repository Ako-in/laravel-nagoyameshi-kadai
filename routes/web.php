<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;

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
    Route::get('categories/',[Admin\CategoryController::class,'store'])->name('categories.store');
    Route::put('categories/{category}',[Admin\CategoryController::class,'update'])->name('categories.update');
    Route::get('categories/{category}/edit',[Admin\CategoryController::class,'edit'])->name('categories.edit');
    
    // Route::resource('categories',Admin\CategoryController::class);

    // Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);
    Route::get('company/index', [Admin\CompanyController::class, 'index'])->name('company.index');
    Route::patch('company/{company}', [Admin\CompanyController::class, 'update'])->name('company.update');
    Route::get('company/{company}/edit', [Admin\CompanyController::class, 'edit'])->name('company.edit');

    Route::get('terms/index',[Admin\TermController::class,'index'])->name('terms.index');
    Route::get('terms/{term}/edit',[Admin\TermController::class,'edit'])->name('terms.edit');
    Route::put('terms/{term}',[Admin\TermController::class,'update'])->name('terms.update');
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
Route::get('restaurants/{restaurant}',[RestaurantController::class,'show'])->name('restaurants.show');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');

    // Route::group(['middleware' => ['auth', 'not.subscribed']], function () {
    // // Route::group(['middleware' => [NotSubscribed::class]], function () {
    //     Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
    //     Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    // });
    // Route::group(['middleware' => ['auth', 'subscribed']], function () {
    // // Route::group(['middleware' => [Subscribed::class]], function () {
    //     Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    //     Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
    //     Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    //     Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    // });
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



// // 通常ユーザー向けルート
// Route::group(['middleware' => ['auth', 'verified']], function () {
//     Route::get('user/index', [UserController::class, 'index'])->name('user.index');
//     Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
//     Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');

//     Route::group(['middleware' => [NotSubscribed::class]], function () {
//         Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('');
//         Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
//     });

//     Route::group(['middleware' => [Subscribed::class]], function () {
//         Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
//         Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
//         Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
//         Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
//     });
// });



// // 通常ユーザー向けルート
// Route::group(['middleware' => ['auth', 'verified']], function () {
//     Route::get('user/index', [UserController::class,'index'])->name('user.index');
//     Route::get('user/{user}/edit', [UserController::class,'edit'])->name('user.edit');
//     Route::put('user/{user}', [UserController::class,'update'])->name('user.update');

//     Route::group(['middleware' => [NotSubscribed::class]]->function () {
//         Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
//         Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
//     });
    
//     Route::group(['middleware' => [Subscribed::class]]->function () {
//         Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
//         Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
//         Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
//         Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
//     });
    
// });
