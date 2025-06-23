<?php
use Illuminate\Http\Request;
// use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\GisController;

// Route::post('/reverse-geocode', [GeocodeController::class, 'reverseGeocode']);

Route::post('/population', [GisController::class, 'getPopulationStats']);
