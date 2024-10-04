<?php

use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function(){

    Route::post('/changeVat', [SuperadminToolFlagsController::class, 'store']);
    Route::get('/changeVat/getPending', [SuperadminToolFlagsController::class,'show']);
    Route::patch('/changeVat/processDone', [SuperadminToolFlagsController::class,'update']);
});


Route::get('/user', function (Request $request) { return $request->user(); })->middleware('auth:sanctum');
