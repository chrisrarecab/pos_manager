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


//Render page
Route::get('/login', function () {     
    if (Auth::check()) {
        return redirect()->route('dashboard');
    } 
    return Inertia::render('Auth/Login');
})->name('login');
Route::get('/register', function () { return Inertia::render('Auth/Register'); })->name('register');

//User
Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/register', 'registerBySecretKey')->name('register.submit');
    Route::post('/login/cirms', 'bypassLoginCirms')->name('login.cirms');   
});


// Authenticated user
Route::middleware(['web', 'auth'])->group(function () {
    //Render page
    Route::get('/', function () {  return Inertia::render('Dashboard/Dashboard'); });
    Route::get('/dashboard', function () {  return Inertia::render('Dashboard/Dashboard'); })->name('dashboard');

    //User 
    Route::controller(UserController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });

    //Terminal Settings
    Route::controller(TerminalSettingController::class)->group(function () {
        Route::get('/terminal/config', 'setClientTerminalDetails')->name('terminalConfig');
    });
    

});

