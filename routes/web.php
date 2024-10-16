<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {  return view('sample'); });
Route::get('/terminal', function (Illuminate\Http\Request $request) {
    $clientNetwork = $request->query('detail', 'default_value');
    return view('terminalList', ['detail' => $clientNetwork]);
});