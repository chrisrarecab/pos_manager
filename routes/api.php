<?php

namespace  App\Http\Controllers;
use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\V1\TerminalSettingController;
use App\Http\Controllers\ClientBaseController;
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

    // Cancel PTU
    Route::controller(SuperadminToolFlagsController::class)->prefix('cancelPTU')->group(function () {
        Route::post('/', 'storeCancelPTU');
        Route::post('/getPending', 'getCancelPTU');
        Route::post('/processDone', 'updateValue');
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

    // Terminal Settings
    Route::prefix('terminal/settings')->controller(TerminalSettingController::class)->group(function () {
        Route::get('/fetch/tabs', 'fetchTerminalSettingsTabs');
        Route::get('/', 'fetchTerminalSettings');
        Route::post('/', 'storeTerminalSettings');
        Route::post('/update', 'updateTerminalSettings');
        Route::post('/apply-to-all', 'applySettingsToMultipleTerminals');
    });
});

Route::controller(ClientBaseApiController::class)->group(function () {
    Route::get('/clientbase/group/list', 'getClientGroupList');
    Route::get('/clientbase/network/list/{id}', 'getClientNetworkList');
    Route::get('/clientbase/branch/list/{id}', 'getClientBranchList');
    Route::get('/clientbase/terminal/list/{id}', 'getClientTerminalList');
    Route::get('/clientbase/client/head/{id}', 'getClientDetails');
    Route::post('/clientbase/terminal/cancel-ptu/{id}', 'postCancelPosPTU');
});

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'registerBySecretKey');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::get('/checkUserSession', 'checkAuth');
    Route::get('/getSession', 'getSession');
    Route::post('/register/cirms', 'bypassRegisterCirms');
});



