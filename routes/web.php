<?php
use App\Http\Controllers\UserController;
use app\Http\Controller\Api\V1\UserlistController;
use App\Http\Controllers\Api\V1\TerminalSettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Dev urls
Route::get('/phpinfo', function () { phpinfo(); });
Route::get('/debug-session', function () { return session()->all(); });
Route::get('/sample', function () {  return view('sample'); });
Route::get('/', function () {  return view('dashboard'); });

// Registration
Route::get('/register', function () { return redirect('/register/core'); });
Route::get('/register/core', function (Request $request) {
    $secretKey = $request->query('secret', '');
    return view('auth/register', ['software' => 'POS-CORE','secret' => $secretKey]); 
});

// Log in and out
Route::get('/login', function (Request $request) { return view('auth.login'); })->name('login');
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/login/cirms', [UserController::class, 'bypassLoginCirms'])->name('login.cirms');
Route::post('/logout', [UserController::class, 'logout']);

// Dashboard
Route::get('/dashboard', function () {  return view('possettings'); });

// Users
Route::get('/user/list', function (Request $request) {
    return view('userlist');
});
Route::get('/user', function (Request $request) {
    $default = session('userId');
    $userId = $request->query('id', $default);
    return view('userdetails', ['detail' => $userId]);
});

// POS Settings
Route::get('/client/details', [TerminalSettingController::class, 'setClientTerminalDetails']);
Route::get('/pos/settings', function (Request $request) {
    return view('possettings');
});

// Project Tools
Route::get('/project/tools', function () {  return view('projecttools'); });


