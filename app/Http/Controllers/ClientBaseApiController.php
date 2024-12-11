<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

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
            case "verify_domain":
                return $this->hostname."/api/v1/PosManagerToken/verifyDomain";
            default:
                return $this->hostname."/";
        }
    }

    public function verifyClientDomain($domain)
    {
        $endpoint = $this->getUrl("verify_domain");
        $params = "?domain=".$domain;
        $response = Http::withHeaders(["Authorization" => $this->getAuthorization()])->get($endpoint.$params);
        if (! isset($response['isSuccessful'])) {
            throw ValidationException::withMessages(['message' => "Error: Unable to reach server."]);
        }
        if ($response['isSuccessful'] == false) {
            throw ValidationException::withMessages(['message' => $response['error']]);
        }
        return $response;
    }

    public function validateSecretKey($secret)
    {
        $endpoint = $this->getUrl("validate_secret");
        $params = "?secretKey=".$secret;
        $response = Http::withHeaders(["Authorization" => $this->getAuthorization()])->get($endpoint.$params);
        if (! isset($response['isSuccessful'])) {
            throw ValidationException::withMessages(['message' => "Error: Unable to reach server."]);
        }
        if ($response['isSuccessful'] == false) {
            throw ValidationException::withMessages(['message' => $response['error']]);
        }
        return $response;
    }

    public function activateSecretKey($secret, $userId)
    {
        $endpoint = $this->getUrl("activate_secret");
        $params = "?secretKey=".$secret;
        $response = Http::withHeaders(["Authorization" => $this->getAuthorization()])->post($endpoint, [
            "secretKey" => $secret, 
            "posManagerUserId" => $userId,
        ]);
        if (! isset($response['isSuccessful'])) {
            throw ValidationException::withMessages(['message' => "Error: Unable to reach server."]);
        }
        if ($response['isSuccessful'] == false) {
            throw ValidationException::withMessages(['message' => $response['error']]);
        }
        return $response;
    }
}
