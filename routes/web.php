<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function (Illuminate\Http\Request $request) {  return view('auth/login'); });
Route::get('/register', function (Illuminate\Http\Request $request) {
    $secretKey = $request->query('secret', '');
    return view('auth/register', ['secret' => $secretKey]); 
});
Route::get('/dashboard', function (Illuminate\Http\Request $request) {  return view('dashboard'); });
Route::post('logout', [UserController::class, 'logout']);