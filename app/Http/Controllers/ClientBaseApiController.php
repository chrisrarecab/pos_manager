<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Repositories\ClientBaseDetailsRepository;

class ClientBaseApiController extends Controller
{
    private $hostname = "client-api.nelsoft.ph";
    protected $clientbase;
    
    public function __construct(ClientBaseDetailsRepository $details) {
        $configValue = env('CLIENTBASE_API_PORT');
        $configValue = (strlen($configValue) > 0) ? $configValue : $this->hostname;
        $this->hostname = $configValue;
        $this->clientbase = $details;
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
            case "cancel_pos_ptu":
                return $this->hostname."/api/v1/ptuStatus/Cancel";
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

    public function getClientGroupList()
    {
        return $this->clientbase->getClientGroupIds();
    }

    public function getClientNetworkList($id)
    {
        return $this->clientbase->getClientNetworkIds($id);
    }

    public function getClientBranchList($id)
    {
        return $this->clientbase->getClientBranchIds($id);
    }

    public function getClientTerminalList($id)
    {
        return $this->clientbase->getClientTerminalIds($id);
    }

    public function getClientDetails($id)
    {
        return $this->clientbase->getClientDetails($id);
    }

    public function postCancelPosPTU($id)
    {
        $endpoint = $this->getUrl("cancel_pos_ptu");
        $response = Http::withHeaders(["Authorization" => $this->getAuthorization()])->post($endpoint, [ "PosId" => $id ]);
        $data['isSuccessful'] = true;
        $data['message'] = "Successfully cancelled PTU.";
        $data['error'] = "";
        $data['status'] = 200;
        
        if (! isset($response['Status'])) {
            $data['isSuccessful'] = false;
            $data['message'] = "Failed to process the request.";
            $data['error'] = "Error: Unable to update client base. Please update the status of the terminal in client base manually";
            $data['status'] = 500;
        } elseif ($response->status() != 200) {
            $data['isSuccessful'] = false;
            $data['message'] = "Failed to process the request.";
            $data['error'] = $response['Status'];
            $data['status'] = $response->status();
        }
        return $data;
    }
}
