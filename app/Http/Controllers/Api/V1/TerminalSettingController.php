<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTerminalSettingRequest;
use App\Repositories\ClientTerminalDetailRepository;
use App\Repositories\TerminalSettingRepository;
use App\DataTransferObjects\RepositoryResponse;

use App\Models\Setting;
use App\Models\SettingTab;
use App\Models\SettingOption;
use App\Models\TerminalSetting;


class TerminalSettingController extends Controller
{ 
    protected $clientTerminalDetails;
    protected $terminalSettingRepo;

    public function __construct(
        ClientTerminalDetailRepository $details,
        TerminalSettingRepository $terminalSettingRepo
    ){
         
        $this->clientTerminalDetails = $details;
        $this->terminalSettingRepo = $terminalSettingRepo;

    }

    public function storeTerminalSettings(StoreTerminalSettingRequest $request)
    {
        $validated = $request->validated();
        $response = $this->terminalSettingRepo->storeTerminalSettingsRepo($validated);
        
        return response()->json([
            'isSuccessful' => $response->success,
            'message' => $response->message,
            'error' => $response->error,
            'code' => $response->code,
            'settings' => $response->data
        ], $response->code);
    }

    public function fetchTerminalSettingsTabs() 
    {
        $tabs = SettingTab::where('is_deleted', 0)->orderBy('order')->get();
        return response()->json($tabs);
    }

    public function fetchTerminalSettings(Request $request) 
    {
       return $this->terminalSettingRepo->fetchTerminalSettingsRepo($request);
    }  
    
    
    public function updateTerminalSettings(Request $request) 
    {
       $response = $this->terminalSettingRepo->updateTerminalSettingsRepo($request);
        return response()->json([
            'isSuccessful' => $response->success,
            'message' => $response->message,
            'error' => $response->error,
            'code' => $response->code,
            'settings' => $response->data
        ], $response->code);
    }

    public function applySettingsToMultipleTerminals(Request $request) 
    {
       $response = $this->terminalSettingRepo->applySettingsToMultipleTerminalsRepo($request);
        return response()->json([
            'isSuccessful' => $response->success,
            'message' => $response->message,
            'error' => $response->error,
            'code' => $response->code,
            'settings' => $response->data
        ], $response->code);
    }

}
