<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientBaseApiController extends Controller
{
    private $hostname = "client-api.nelsoft.ph";

    public function __construct() {
        $configValue = env('CLIENTBASE_API_PORT');
        $configValue = (strlen($configValue) > 0) ? $configValue : $this->hostname;
        $this->hostname = $configValue;
    } 

    public function getAuthorization()
    {
        return "Basic bmVsc29mdDoxMjE1ODY=";
    }

    public function getBasicHeaders()
    {
        $data['Content-Type'] = "application/json";
        $data['Authorization'] = "Basic bmVsc29mdDoxMjE1ODY=";
        return $data;
    }

    public function getUrl($endpoint)
    {
        switch ($endpoint) {
            case "validate_secret":
                return $this->hostname."/api/v1/PosManagerToken/validateSecret";
            case "activate_secret":
                return $this->hostname."/api/v1/PosManagerToken/activateSecret";
            default:
                return $this->hostname."/";
        }
    }
}
