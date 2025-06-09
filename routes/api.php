<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\V1\UserController;
// use App\Http\Controllers\App\V1\CompanyController;
// use App\Http\Controllers\App\V1\OfferController;
use App\Http\Controllers\Api\V1\{UserController, CompanyController, OfferController, RatingController, UserMatchController};



Route::get('/user', function (Request $request) {
    return $request->user();
}); //->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function() {
    // Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
    Route::apiResource('users', UserController::class); //->middleware('auth:sanctum');
    Route::apiResource('companies', CompanyController::class); //->middleware('auth:sanctum');
    Route::apiResource('offers', OfferController::class); //->middleware('auth:sanctum');
    Route::apiResource('user_matches', UserMatchController::class); //->middleware('auth:sanctum');
    Route::apiResource('ratings', RatingController::class);
}); //->middleware('auth:sanctum');
