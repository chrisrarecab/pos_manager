<?php

namespace  App\Http\Controllers;
use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
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
});


