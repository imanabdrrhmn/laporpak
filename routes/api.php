<?php
use Illuminate\Http\Request;
// use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\Api\WhatsAppWebhookController;

// Route::post('/reverse-geocode', [GeocodeController::class, 'reverseGeocode']);

Route::post('/population', [GisController::class, 'getPopulationStats']);


Route::match(['get', 'post'], '/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);