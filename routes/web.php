<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\UserController;
use app\Http\Controller\Api\V1\UserlistController;
use App\Http\Controllers\Api\V1\TerminalSettingController;

/**
 * LARAVEL | REACT | INERTIA  
 */

// Dev URLs
Route::get('/phpinfo', function () { phpinfo(); });
Route::get('/debug-session', function () { return session()->all(); });
Route::get('/log-test', function () {
    Log::channel('systemInfoLog')->info('Custom info message');
    Log::channel('systemErrorLog')->error('Custom error message');
    return 'done';
});
Route::get('/sample', function () {  return Inertia::render('Example/SamplePage'); });

Route::controller(UserController::class)->group(function () {
    Route::post('/login/cirms', 'bypassLoginCirms')->name('login.cirms');    
});

/**
 * Pages
 */
Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () { return Inertia::render('Auth/Login'); })->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {  return Inertia::render('Dashboard/Dashboard'); });
    Route::get('/dashboard', function () {  return Inertia::render('Dashboard/Dashboard'); })->name('dashboard');
    Route::get('/terminal/config', [TerminalSettingController::class, 'setClientTerminalDetails'])->name('terminalConfig');

    // Logout POST
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

