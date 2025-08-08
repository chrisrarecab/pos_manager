<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\SettingTab;
use App\Models\SettingOption;
use App\Models\TerminalSetting;

use App\Http\Requests\StoreTerminalSettingRequest;
use App\Http\Controllers\Controller;
use App\Services\TerminalSettingService;
use App\Services\ClientBaseService;
use App\Services\CirmsApiService;

class TerminalSettingController extends Controller
{ 
    public function __construct(
        protected TerminalSettingService $terminalSettingService,
        protected ClientBaseService $clientBaseService,
        protected CirmsApiService $cirmsApiService
    ) {}
    
    public function setClientTerminalDetails() 
    {
         $softwareId = session('software_id');
         
         if($softwareId == 1) {
            $response = $this->clientBaseService->getClientTerminalDetails();

            return response()->json([
                'isSuccessful' => $response->success,
                'values' => $response->values ?? null,
                'message' => $response->message,
                'errors' => $response->error ?? [],
            ],  $response->statusCode);
         } else {
            $response = $this->cirmsApiService->getCirmsTerminalDetails();

            return response()->json([
                'isSuccessful' => $response->success,
                'values' => $response->values ?? null,
                'message' => $response->message,
                'errors' => $response->error ?? [],
            ],  $response->statusCode);
         }
    }

    public function storeTerminalSettings(StoreTerminalSettingRequest $request)
    {
        $validated = $request->validated();
        $response = $this->terminalSettingService->storeTerminalSettings($validated);
        
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

    public function fetchTerminalSettingsTabs() 
    {
        $tabs = SettingTab::where('is_deleted', 0)->orderBy('order')->get();
        return response()->json($tabs);
    }

    public function fetchTerminalSettings(Request $request) 
    {
        $clientTerminalId = $request->query('clientTerminalId');
        $softwareId = $request->query('softwareId');
        $settings = $this->terminalSettingService->fetchTerminalSettings($clientTerminalId, $softwareId);

        return response()->json($settings);
    }     
    
    public function updateTerminalSettings(Request $request) 
    {
        $response = $this->terminalSettingService->updateTerminalSettings($request);

        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

    public function applySettingsToMultipleTerminals(Request $request) 
    {
        $response = $this->terminalSettingService->applySettingsToMultipleTerminals($request);
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

}
