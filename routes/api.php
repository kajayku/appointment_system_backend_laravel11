<?php

use App\Http\Controllers\API\AppointmentController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
 
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');


    Route::get('/appointments', [AppointmentController::class, 'viewAllAppointments']);
    Route::post('/appointments', [AppointmentController::class, 'bookAppointment']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'cancelAppointment']);

});
