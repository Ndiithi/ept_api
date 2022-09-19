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

Route::put('/role/create', [RoleController::class, 'createRole'])->middleware('auth:sanctum');
Route::get('/role/get', [RoleController::class, 'getRoles'])->middleware('auth:sanctum');
Route::delete('/role/delete/{id}', [RoleController::class, 'deleteRole'])->middleware('auth:sanctum');
Route::post('/role/update', [RoleController::class, 'updateRole'])->middleware('auth:sanctum');

Route::put('/permission/create', [PermissionController::class, 'createPermission'])->middleware('auth:sanctum');
Route::get('/permission/get', [PermissionController::class, 'getPermissions'])->middleware('auth:sanctum');
Route::delete('/permission/delete/{id}', [PermissionController::class, 'deletePermission'])->middleware('auth:sanctum');
Route::post('/permission/update', [PermissionController::class, 'updatePermission'])->middleware('auth:sanctum');
