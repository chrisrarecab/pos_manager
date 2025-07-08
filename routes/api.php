<?php

use App\Http\Controllers\Api\V1\SuperadminToolFlagsController;
use App\Http\Controllers\Api\V1\TerminalSettingController;
use App\Http\Controllers\Api\V1\ClientTerminalDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function(){
    Route::post('/changeVat', [SuperadminToolFlagsController::class, 'store']);
    Route::get('/changeVat/getPending', [SuperadminToolFlagsController::class,'show']);
    Route::patch('/changeVat/processDone', [SuperadminToolFlagsController::class,'update']);

    Route::get('/client/details/{id}', [ClientTerminalDetailController::class, 'getClientTerminalDetails']);
});
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


 Route::get('/user', function (Request $request) { return $request->user(); })->middleware('auth:sanctum');
