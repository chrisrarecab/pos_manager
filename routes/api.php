<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
use App\Http\Controllers\Api\V1\PosSettingsController;

Route::prefix('v1')->group(function(){

    Route::post('/changeVat', [SuperadminToolFlagsController::class, 'store']);
    Route::get('/changeVat/getPending', [SuperadminToolFlagsController::class,'show']);
    Route::patch('/changeVat/processDone', [SuperadminToolFlagsController::class,'update']);
    Route::post('/settings', [PosSettingsController::class, 'show']);
    Route::post('/posSetting', [PosSettingsController::class, 'store']);
});


Route::get('/user', function (Request $request) { return $request->user(); })->middleware('auth:sanctum');
