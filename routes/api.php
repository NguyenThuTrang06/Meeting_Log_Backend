<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// CRUD endpoints cho frontend React
Route::apiResource('meetings', MeetingController::class);
Route::apiResource('members', App\Http\Controllers\MemberController::class);

// Webhook endpoint cho n8n gửi dữ liệu vào
Route::post('webhook/n8n-meeting', [MeetingController::class, 'webhook']);
