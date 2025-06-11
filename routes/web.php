<?php

use App\Http\Controllers\Web\OfferController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/list-offers', function () {
//     return Inertia::render('ListOffers');
// })->middleware(['auth', 'verified'])->name('list-offers');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/offers', [OfferController::class, 'index'])
        ->name('offers.index');

    Route::get('/offers/{id}', [OfferController::class, 'show'])
        ->name('offers.show');

    // Route::get('/list-offers', function () {
    //     return Inertia::render('Offers');
    // })->name('list-offers');

    Route::group([
        'middleware' => ['admin'],
        'prefix' => config('backpack.base.route_prefix', 'admin'),
        'namespace' => 'App\Http\Controllers\Admin',
    ], function () {
        // Route::get('/admin/login', function () {
        //     return Inertia::render('dashboard');
        // })->name('dashboard');
        Route::get('login', 'Auth\\LoginController@showLoginForm')->name('backpack.auth.login');
// Route::post('login', 'Auth\\LoginController@login');
Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
// Route::post('logout', 'Auth\\LoginController@logout');
Route::get('dashboard', function () {
        return view('vendor.backpack.ui.dashboard');
    })->name('backpack.dashboard');
Route::get('/', function () {
        return view('vendor.backpack.ui.dashboard');
    })->name('backpack');
    // Route::inertia('/admin/dashboard', 'Admin/Dashboard');
        // return Inertia::render('admin');

        // Route::get('/admin/dashboard', function () {
        //     return Inertia::render('dashboard');
        // })->name('dashboard');
    });

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
