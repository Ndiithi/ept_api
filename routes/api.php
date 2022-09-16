<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UITemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/getUITemplates', [UITemplateController::class, 'getUITemplate'])->middleware('auth:sanctum');

Route::post('/auth/register', [AuthController::class, 'createUser'])->name('register');;
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name('login');

// viewAny, view, create, update, delete, restore, and forceDelete
Route::post('/role/update', [RoleController::class, 'updateRole'])->middleware('auth:sanctum');

