<?php

use App\Http\Controllers\Web\OfferController;
use App\Http\Controllers\Web\ApplicationController;
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
        ->name('web.offers.index');

    Route::get('/offers/create', [OfferController::class, 'create'])
        ->name('web.offers.create');

    Route::post('/offers', [OfferController::class, 'store'])
        ->name('web.offers.store');

    Route::get('/offers/{id}', [OfferController::class, 'show'])
        ->name('web.offers.show');

    Route::get('/offers-fetch-more', [OfferController::class, 'fetchMore'])
        ->name('web.offers.fetch-more');
        
    // Application routes
    Route::get('/offers/{offer_id}/applications/create', [ApplicationController::class, 'create'])
        ->name('web.offers.applications.create');
    Route::post('/offers/{offer_id}/applications', [ApplicationController::class, 'store'])
        ->name('web.offers.applications.store');
    Route::post('/applications/{id}/retract', [ApplicationController::class, 'retract'])
        ->name('web.applications.retract');
        
    Route::get('/applications', [ApplicationController::class, 'index'])
        ->name('web.applications.index');
    Route::get('/applications/{id}', [ApplicationController::class, 'show'])
        ->name('web.applications.show');
    Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])
        ->name('web.applications.approve');
    Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])
        ->name('web.applications.reject');

    // Route::get('/list-offers', function () {
    //     return Inertia::render('Offers');
    // })->name('list-offers');

    Route::group([
        'middleware' => ['admin'],
        'prefix' => 'admin', //config('backpack.base.route_prefix', 'admin'),
        'namespace' => 'App\Http\Controllers\Admin',
    ], function () {
        // Route::get('/admin/login', function () {
        //     return Inertia::render('dashboard');
        // })->name('dashboard');
        // Route::get('login', 'Auth\\LoginController@showLoginForm')->name('backpack.auth.login');
        // Route::post('login', 'Auth\\LoginController@login');
        // Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
        // ->name('logout');

    // Route::post('logout', 'Auth\\LoginController@logout');
    Route::get('dashboard', function () {
            return view('vendor.backpack.ui.dashboard');
        })->name('backpack.dashboard');
    Route::get('/', function () {
        return view('vendor.backpack.ui.dashboard');
    })->name('backpack');
    });

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
