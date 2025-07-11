<?php

namespace  App\Http\Controllers;
use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\V1\TerminalSettingController;
use App\Http\Controllers\Api\V1\ClientTerminalDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserlistController;
use App\Http\Controllers\Api\V1\UserPermissionsController;
use App\Http\Controllers\Api\V1\UserBranchController;

Route::prefix('v1')->group(function(){
    Route::post('/changeVat', [SuperadminToolFlagsController::class, 'store']);
    Route::post('/changeVat/getPending', [SuperadminToolFlagsController::class,'show']);
    Route::post('/changeVat/processDone', [SuperadminToolFlagsController::class,'update']);

    Route::get('/userlistApi',[UserlistController::class, 'index']);
    Route::get('/userDetailsApi',[UserlistController::class, 'show']);
    Route::post('/userDetailsEdit',[UserlistController::class, 'edit']);
    Route::post('/userAddApi',[UserlistController::class, 'add']);
    Route::post('/deleteUserApi', [UserlistController::class, 'delete']);
    Route::get('/checkUsernameApi', [UserlistController::class, 'checkUsername']);

    Route::get('/userPermissionsApi',[UserPermissionsController::class, 'index']);
    Route::post('/userPermissionEdit',[UserPermissionsController::class, 'edit']);
    
    Route::get('/userBranchApi',[UserBranchController::class, 'index']);
    Route::post('/userBranchEdit',[UserBranchController::class, 'edit']);

    
    Route::get('/client/details/{id}', [ClientTerminalDetailController::class, 'getClientTerminalDetails']);
});


Route::get('/user', function (Request $request) { return $request->user(); })->middleware('auth:sanctum');
Route::post('register', [UserController::class, 'registerBySecretKey']);
Route::post('registerCirms', [UserController::class, 'registerByDomain']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout']);
Route::get('checkUserSession', [UserController::class, 'checkAuth']);
Route::get('getSession', [UserController::class, 'getSession']);

Route::prefix('/v1/terminal/settings')->controller(TerminalSettingController::class)->group(function () {
    Route::get('/fetch/tabs', 'fetchTerminalSettingsTabs');
    Route::get('/', 'fetchTerminalSettings');
    Route::post('/', 'storeTerminalSettings');
    Route::post('/update', 'updateTerminalSettings');
    Route::post('/apply-to-all', 'applySettingsToMultipleTerminals');
});
Route::get('/check-time', function () {
    return [
        'now' => now(),
        'default' => date_default_timezone_get(),
        'carbon' => \Carbon\Carbon::now()->toDateTimeString(),
    ];
});
