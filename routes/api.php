<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

# Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

# Auth require
Route::middleware('auth:api')->controller(TasksController::class)->prefix('tasks')->group(function () {
    # GET ROUTES
    Route::get('/', 'listAll');
    Route::get('/{id}', 'taskDetail');

    # PUT ROUTES
    Route::put('/{id}', 'edit');

    # POST ROUTES
    Route::post('/', 'store');

    # DELETE ROUTES
    Route::delete('/{id}', 'delete');
});
