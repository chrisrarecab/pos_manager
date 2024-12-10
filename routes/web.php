<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


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