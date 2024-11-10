<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function (Illuminate\Http\Request $request) {  return view('auth/login'); });
Route::get('/register', function (Illuminate\Http\Request $request) {  return view('auth/register'); });
Route::get('/sample', function (Illuminate\Http\Request $request) {  return view('sample'); });
