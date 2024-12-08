<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function (Illuminate\Http\Request $request) {  return view('auth/login'); });
Route::get('/register', function (Illuminate\Http\Request $request) {  return view('auth/register'); });
Route::get('/dashboard', function (Illuminate\Http\Request $request) {  return view('dashboard'); });
Route::post('logout', [UserController::class, 'logout']);