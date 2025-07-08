<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {  return view('sample'); });
Route::get('/pos/settings', function (Illuminate\Http\Request $request) {
    $clientGroup = $request->query('detail', '0000');
    return view('possettings', ['detail' => $clientGroup]);
});