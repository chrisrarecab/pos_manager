<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Setting;
use App\Models\SettingTab;
use App\Models\SettingOption;
use App\Models\TerminalSetting;

use App\Http\Requests\Terminal\UpdateTerminalSettingRequest;
use App\Http\Requests\Terminal\StoreTerminalSettingRequest;
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
        return Inertia::render('Settings/TerminalConfig');
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

    public function fetchAllTerminalSettings() 
    {
        $res = TerminalSetting::where('is_deleted', 0)->select('terminal_id')->distinct()->get()->pluck('terminal_id');
        return response()->json($res);
    }

    public function fetchTerminalSettings(Request $request) 
    {
        $terminalId = $request->query('terminalId');
        $softwareId = $request->query('softwareId');
        $settings = $this->terminalSettingService->fetchTerminalSettings($terminalId, $softwareId);

        return response()->json($settings);
    }     
    
    public function updateTerminalSettings(UpdateTerminalSettingRequest $request) 
    {
        $validated = $request->validated();
        $response = $this->terminalSettingService->updateTerminalSettings($validated);

        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

    public function applySettingsToMultipleTerminals(UpdateTerminalSettingRequest $request) 
    {
        set_time_limit(300); 
        $validated = $request->validated();
        $response = $this->terminalSettingService->applySettingsToMultipleTerminals($validated);
        return response()->json([
            'isSuccessful' => $response->success,
            'values' => $response->values ?? null,
            'message' => $response->message,
            'errors' => $response->error ?? [],
        ],  $response->statusCode);
    }

    public function searchTerminalSettings(Request $request)
    {
        $terminalId = $request->query('terminalId');
        $softwareId = $request->query('softwareId');
        $value = $request->query('value');
        $settings = $this->terminalSettingService->searchTerminalSettings($terminalId, $softwareId, $value);

        return response()->json($settings);
    }
}
