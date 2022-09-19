<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProgramController;
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

Route::put('/dictionary/create', [DictionaryController::class, 'createEntry'])->middleware('auth:sanctum');
Route::get('/dictionary/get', [DictionaryController::class, 'getAll'])->middleware('auth:sanctum');
Route::delete('/dictionary/delete/{id}', [DictionaryController::class, 'deleteItem'])->middleware('auth:sanctum');
Route::post('/dictionary/update', [DictionaryController::class, 'updateItem'])->middleware('auth:sanctum');

Route::put('/program/create', [ProgramController::class, 'createEntry'])->middleware('auth:sanctum');
Route::get('/program/get', [ProgramController::class, 'getPrograms'])->middleware('auth:sanctum');
Route::delete('/program/delete/{id}', [ProgramController::class, 'deleteProgram'])->middleware('auth:sanctum');
Route::post('/program/update', [ProgramController::class, 'updateProgram'])->middleware('auth:sanctum');
