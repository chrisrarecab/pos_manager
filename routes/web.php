<?php
use App\Http\Controllers\UserController;
use app\Http\Controller\Api\V1\UserlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/phpinfo', function () { phpinfo(); });
Route::get('/debug-session', function () { return session()->all(); });
Route::get('/sample', function () {  return view('sample'); });
Route::get('/', function () {  return view('dashboard'); });

Route::get('/register', function () { return redirect('/register/core'); });
Route::get('/register/core', function (Request $request) {
    $secretKey = $request->query('secret', '');
    return view('auth/register', ['software' => 'POS-CORE','secret' => $secretKey]); 
});

Route::get('/login', function (Request $request) { return view('auth.login'); })->name('login');
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/login/cirms', [UserController::class, 'bypassLoginCirms'])->name('login.cirms');
Route::post('logout', [UserController::class, 'logout']);

Route::get('/dashboard', function () {  return view('dashboard'); });
Route::get('/userlist', function (Request $request) {
    $clientNetworkId = $request->query('detail', 'default_value');
    return view('userlist', ['detail' => $clientNetworkId]);
});
Route::get('/userdetails', function (Request $request) {
    $userId = $request->query('detail', 'default_value');
    return view('userdetails', ['detail' => $userId]);
});
Route::get('/pos/settings', function (Request $request) {
    $clientGroup = $request->query('detail', '0000');
    return view('possettings', ['detail' => $clientGroup]);
});

Route::get('/project/tools', function () {  return view('projecttools'); });
