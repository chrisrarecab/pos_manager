<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ClientTerminalDetailRepository;

class ClientTerminalDetailController extends Controller
{
    protected $clientTerminalDetails;

    public function __construct(ClientTerminalDetailRepository $details)
    {
        $this->clientTerminalDetails = $details;
    }

    public function getClientTerminalDetails($id){
        $clientTerminalDetails = $this->clientTerminalDetails->getClientTerminalDetails($id);
        return response()->json(['data' => $clientTerminalDetails]);
    }

}
