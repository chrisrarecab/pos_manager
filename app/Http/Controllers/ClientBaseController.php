<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClientBaseService;

class ClientBaseController extends Controller
{
    public function __construct(
        protected ClientBaseService $clientBaseService
    ) {}

    public function getClientTerminalDetails()
    {
        $response = $this->clientBaseService->getClientTerminalDetails();
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }
    
    public function getCoreTerminalId(Request $request)
    {
        $response = $this->clientBaseService->getCoreTerminalId(
            $request->clientGroupId,
            $request->clientNetworkId,
            $request->clientBranchId,
            $request->terminalNo,
            $request->posType
        );

        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values,
            'message' => $response->message,
            'errors' => $response->error,
        ], $response->statusCode);
    }
}
