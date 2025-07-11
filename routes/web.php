<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use app\Http\Controller\Api\V1\UserlistController;
use Illuminate\Http\Request;


Route::get('/login', function (Illuminate\Http\Request $request) {  return view('auth/login'); });
Route::post('logout', [UserController::class, 'logout']);
Route::get('/dashboard', function (Illuminate\Http\Request $request) {  return view('dashboard'); });
Route::get('/', function () {
    return redirect('/dashboard');
});
Route::get('/register', function () {
    return redirect('/register/core');
});
Route::get('/register/core', function (Illuminate\Http\Request $request) {
    $secretKey = $request->query('secret', '');
    return view('auth/register', ['source_project' => 'core','secret' => $secretKey]); 
});
Route::get('/register/cirms', function (Illuminate\Http\Request $request) {
    return view('auth/register', ['source_project' => 'cirms']); 
});
Route::get('/', function () {  return view('sample'); });

Route::get('/userlist', function (Illuminate\Http\Request $request) {
    $clientNetworkId = $request->query('detail', 'default_value');
    return view('userlist', ['detail' => $clientNetworkId]);
});

Route::get('/userdetails', function (Illuminate\Http\Request $request) {
    $userId = $request->query('detail', 'default_value');
    return view('userdetails', ['detail' => $userId]);
});

Route::get('/pos/settings', function (Illuminate\Http\Request $request) {
    $clientGroup = $request->query('detail', '0000');
    return view('possettings', ['detail' => $clientGroup]);
});
