<?php
use Illuminate\Http\Request;
use App\Http\Controllers\GeocodeController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/reverse-geocode', [GeocodeController::class, 'reverseGeocode']);