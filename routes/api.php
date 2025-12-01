<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\N8nController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/n8n/data', [N8nController::class, 'getData']);
    Route::post('/n8n/alert', [N8nController::class, 'storeAlert']);
});