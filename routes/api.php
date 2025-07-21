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
use Illuminate\Support\Facades\Log;

Route::prefix('v1')->group(function () {
    
    // VAT Tools
    Route::controller(SuperadminToolFlagsController::class)->prefix('changeVat')->group(function () {
        Route::post('/', 'store'); // POST /v1/changeVat
        Route::post('/getPending', 'show');
        Route::post('/processDone', 'update');
    });

    // User List
    Route::controller(UserlistController::class)->group(function () {
        Route::get('/userlistApi', 'index');
        Route::get('/userDetailsApi', 'show');
        Route::post('/userDetailsEdit', 'edit');
        Route::post('/userAddApi', 'add');
        Route::post('/deleteUserApi', 'delete');
        Route::get('/checkUsernameApi', 'checkUsername');
    });

    // User Permissions
    Route::controller(UserPermissionsController::class)->group(function () {
        Route::get('/userPermissionsApi', 'index');
        Route::post('/userPermissionEdit', 'edit');
    });

    // User Branch
    Route::controller(UserBranchController::class)->group(function () {
        Route::get('/userBranchApi', 'index');
        Route::post('/userBranchEdit', 'edit');
    });

    // Client Terminal Details
    Route::get('/client/details/{id}', [ClientTerminalDetailController::class, 'getClientTerminalDetails']);

    // Terminal Settings
    Route::prefix('terminal/settings')->controller(TerminalSettingController::class)->group(function () {
        Route::get('/fetch/tabs', 'fetchTerminalSettingsTabs');
        Route::get('/', 'fetchTerminalSettings');
        Route::post('/', 'storeTerminalSettings');
        Route::post('/update', 'updateTerminalSettings');
        Route::post('/apply-to-all', 'applySettingsToMultipleTerminals');
    });
});

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'registerBySecretKey');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::get('/checkUserSession', 'checkAuth');
    Route::get('/getSession', 'getSession');
    Route::post('/register/cirms', 'bypassRegisterCirms');
});



