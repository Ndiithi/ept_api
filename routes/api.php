<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\FormController;
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


Route::get('/getUITemplates/{round}/{schema}', [UITemplateController::class, 'getUITemplate'])->middleware('auth:sanctum');

Route::post('/auth/register', [AuthController::class, 'createUser'])->name('register');;
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name('login');
Route::post('/auth/logout', [AuthController::class, 'logoutUser'])->name('logout')->middleware('auth:sanctum');
Route::get('/auth/user', [AuthController::class, 'getUser'])->name('user')->middleware('auth:sanctum');

Route::post('/role/new', [RoleController::class, 'createRole'])->middleware('auth:sanctum');
Route::get('/roles', [RoleController::class, 'getRoles'])->middleware('auth:sanctum');
Route::get('/role/{uuid}', [RoleController::class, 'getRole'])->middleware('auth:sanctum');
Route::delete('/role/delete/{uuid}', [RoleController::class, 'deleteRole'])->middleware('auth:sanctum');
Route::put('/role/edit/{uuid}', [RoleController::class, 'updateRole'])->middleware('auth:sanctum');

Route::post('/permission/new', [PermissionController::class, 'createPermission'])->middleware('auth:sanctum');
Route::get('/permissions', [PermissionController::class, 'getPermissions'])->middleware('auth:sanctum');
Route::get('/permission/{uuid}', [PermissionController::class, 'getPermission'])->middleware('auth:sanctum');
Route::delete('/permission/delete/{uuid}', [PermissionController::class, 'deletePermission'])->middleware('auth:sanctum');
Route::put('/permission/edit/{uuid}', [PermissionController::class, 'updatePermission'])->middleware('auth:sanctum');

Route::post('/dictionary/new', [DictionaryController::class, 'createEntry'])->middleware('auth:sanctum');
Route::get('/dictionary', [DictionaryController::class, 'getAll'])->middleware('auth:sanctum');
Route::get('/dictionary/{uuid}', [DictionaryController::class, 'getItem'])->middleware('auth:sanctum');
Route::delete('/dictionary/delete/{uuid}', [DictionaryController::class, 'deleteItem'])->middleware('auth:sanctum');
Route::put('/dictionary/edit/{uuid}', [DictionaryController::class, 'updateItem'])->middleware('auth:sanctum');

Route::post('/program/new', [ProgramController::class, 'createProgram'])->middleware('auth:sanctum');
Route::get('/programs', [ProgramController::class, 'getPrograms'])->middleware('auth:sanctum');
Route::get('/program/{uuid}', [ProgramController::class, 'getProgram'])->middleware('auth:sanctum');
Route::delete('/program/delete/{uuid}', [ProgramController::class, 'deleteProgram'])->middleware('auth:sanctum');
Route::put('/program/edit/{uuid}', [ProgramController::class, 'updateProgram'])->middleware('auth:sanctum');

Route::post('/user_program/new', [UserProgramController::class, 'mapUserProgram'])->middleware('auth:sanctum');
Route::get('/user_programs', [UserProgramController::class, 'getUserPrograms'])->middleware('auth:sanctum');
Route::get('/user_program/{id}', [UserProgramController::class, 'getUserProgram'])->middleware('auth:sanctum');
Route::delete('/user_program/delete/{id}', [UserProgramController::class, 'deleteUserPrograms'])->middleware('auth:sanctum');

Route::post('/form/new', [FormController::class, 'createForm'])->middleware('auth:sanctum');
Route::get('/forms', [FormController::class, 'getForms'])->middleware('auth:sanctum');
Route::get('/form/{uuid}', [FormController::class, 'getForm'])->middleware('auth:sanctum');
Route::delete('/form/delete/{uuid}', [FormController::class, 'deleteForm'])->middleware('auth:sanctum');
Route::put('/form/edit/{uuid}', [FormController::class, 'updateForm'])->middleware('auth:sanctum');
