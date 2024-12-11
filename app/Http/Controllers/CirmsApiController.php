<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CirmsApiController extends Controller
{
    private $hostadmin = "api.cirms.ph";

    public function userAuthentication(Request $data)
    {
        $response = Http::withOptions(['verify' => false])->get('https://'.$data->domain.'/api/user/login?username='.$data->username.'&password='.$data->password);

        if ($response->failed()) {
            throw ValidationException::withMessages(['message' => $response['errors']]);
        }
        return $response;
    }
}
