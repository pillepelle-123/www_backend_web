<?php

use App\Http\Controllers\Web\OfferController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::get('/offers', [OfferController::class, 'Index'])
        ->name('offers.index');

    // Route::get('/list-offers', function () {
    //     return Inertia::render('Offers');
    // })->name('list-offers');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
