<?php

use Illuminate\Support\Facades\Route;
use app\Http\Controller\Api\V1\UserlistController;
use Illuminate\Http\Request;

Route::get('/', function () {  return view('sample'); });

Route::get('/userlist', function (Illuminate\Http\Request $request) {
    $clientNetworkId = $request->query('detail', 'default_value');
    return view('userlist', ['detail' => $clientNetworkId]);
});

Route::get('/userdetails', function (Illuminate\Http\Request $request) {
    $userId = $request->query('detail', 'default_value');
    return view('userdetails', ['detail' => $userId]);
});

