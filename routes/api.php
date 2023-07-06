<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    //Event routes
    Route::post('events', [EventController::class, 'create']);
    Route::put('events/{id}', [EventController::class, 'update'])->middleware('event.authorize');
    Route::delete('events/{id}', [EventController::class, 'delete'])->middleware('event.authorize');
    Route::get('events/{id}', [EventController::class, 'getById']);
    Route::get('/events', [EventController::class, 'getByDateRange']);
    Route::get('/locations/events', [EventController::class, 'getLocationsByDateInterval']);
});