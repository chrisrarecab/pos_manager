<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Setting;
use App\Models\SettingOption;
use App\Models\TerminalSetting;
use App\DataTransferObjects\RepositoryResponse;

class TerminalSettingRepository
{
    protected $clientTerminalDetails;

    public function __construct(ClientTerminalDetailRepository $details)
    {
        $this->clientTerminalDetails = $details;
    }
    
    /*
    Handle settings,
    Converts value to setting_option_id,
    Creates the setting option if it doesn't exist.
    */
    public function processSettings(array $settings): array
    {
        $result = [];
        foreach ($settings as $setting) {
            if (isset($setting['name']) && !empty($setting['name'])) {
                $settingData = Setting::select('id', 'form_element', 'software_id')
                    ->where('name', $setting['name'])
                    ->where('software_id', $setting['software_id'])
                    ->first();
            } 
            
            if (!$settingData) {
                throw new \Exception("Setting not found for: " . json_encode($setting));
            }

            $settingId = $settingData->id;
            $formElement = $settingData->form_element;
            $softwareId= $settingData->software_id;

            if(!empty($setting['type']) && $setting['type'] == 'boolean'){
                $booleanOptions = [
                        ['option_name' => 'True', 'option_value' => '1'],
                        ['option_name' => 'False', 'option_value' => '0'],
                    ];

                foreach ($booleanOptions as $opt) {
                    $existingOption = SettingOption::where('setting_id', $settingId)
                        ->where('value', $opt['option_value'])
                        ->first();

                    if (!$existingOption) {
                        SettingOption::create([
                            'setting_id' => $settingId,
                            'name' => $opt['option_name'],
                            'value' => $opt['option_value']
                        ]);
                    } 
                }
            }
                
            if(!empty($setting['options']) && is_array($setting['options'])){
                foreach($setting['options'] as $opt){
                    $optionName = $opt['option_name'] ?? null;
                    $optionValue = $opt['option_value'] ?? null;

                    if ($optionValue == null || $optionName == null){
                        continue;
                    }

                    $existingOption = SettingOption::where('setting_id', $settingId)
                        ->where('value', $optionValue)
                        ->first();
                
                    if (!$existingOption) {
                        SettingOption::create([
                            'setting_id' => $settingId,
                            'name' => $optionName,
                            'value'=> $optionValue
                        ]);
                    }
                }

            }

            switch ($formElement) {
                case 'radio_button':
                     $settingValue = SettingOption::where('value', $setting['value'])
                        ->where('setting_id', $settingId)
                        ->value('id');
                    break;
                case 'dropdown':
                    $settingValue = SettingOption::where('value', $setting['value'])
                        ->where('setting_id', $settingId)
                        ->value('id');
                    break;  
                case 'multi_select_dropdown':
                   $values = is_array($setting['value']) ? $setting['value'] : [$setting['value']];

                    $optionIds = SettingOption::whereIn('value', $values)
                        ->where('setting_id', $settingId)
                        ->pluck('id')
                        ->toArray();

                    $settingValue = implode(',', $optionIds); 
                    break;
                case 'text':
                    $settingValue = $setting['value'];
                    break;
                case 'text_area':
                    $settingValue = $setting['value'];
                    break;
                default:
                    $settingValue = $setting['value'];
                    break;
            }

            $result[] = [
                'setting_id' => $settingId,
                'value' => $settingValue,
                'original_value' => $setting['value'],
                'form_element' => $formElement
            ];
        }

        return [
            'result' => $result
        ];
    }

    public function fetchTerminalSettingsRepo(Request $request)
    {
        $clientTerminalId = $request->query('client_terminal_id');

        $settings = Setting::whereHas('TerminalSetting', function ($query) use ($clientTerminalId) {
            $query->where('client_terminal_id', $clientTerminalId);
        })
        ->where('is_deleted', 0)
        ->with(['options', 'issues', 'terminalSetting', 'user:id,full_name'])
        ->get()
        ->map(function ($setting) {
            $ts = $setting->terminalSetting;

            $rawValue = $ts?->value;
            $resolvedValue = $rawValue;

            switch ($setting->form_element) {
                case 'dropdown':
                    $resolvedValue = $setting->options->firstWhere('id', (int)$rawValue)?->id ?? null;
                    break;
                case 'radio_button':
                    $resolvedValue = $setting->options->firstWhere('id', (int)$rawValue)?->id ?? null;
                    break;
                case 'multi_select_dropdown':
                    $values = $rawValue ? explode(',', $rawValue) : [];
                    $resolvedValue = array_map('intval', $values); 
                    break;
                default:
                    $resolvedValue;
                    break;

            }

            return [
                'id'                 => $setting->id,
                'name'               => $setting->name,
                'tip'                => $setting->tip,
                'description'        => $setting->description,
                'form_element'       => $setting->form_element,
                'type'               => $setting->type,
                'software_id'        => $setting->software_id,
                'setting_tab_id'     => $setting->setting_tab_id,
                'created_by'         => $setting->created_by,
                'created_date'       => $setting->created_date,
                'last_modified_by'   => $setting->last_modified_by,
                'last_modified_date' => $setting->last_modified_date,
                'is_deleted'         => $setting->is_deleted,

                'options'            => $setting->options,
                'issues'             => $setting->issues,
                'terminal_setting'   => $ts,
                'user'               => $setting->user,

                'raw_value'          => $rawValue,
                'value'              => $resolvedValue,
            ];
        });
        
        return response()->json($settings);
    }

    // store uses name and value whlie update uses setting id and setting option id
    public function storeTerminalSettingsRepo(array $data): RepositoryResponse
    {
        $clientTerminalId = '';
        $cirmsClientTerminalId = '';
        $result = [];

        try {
            if ( $data['terminalNo'] <> 0 && $data['posType'] == 10 && !empty( $data['clientId']) && !empty( $data['locationId'])) {
                $cirmsClientTerminalId = implode('-', [
                     $data['clientGroupId'],
                     $data['clientNetworkId'],
                     $data['clientBranchId'],
                     $data['locationId'],
                     $data['terminalNo']
                ]);

                if (!empty($data['settings'])) {

                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 2;
                    }

                    $processed = $this->processSettings( $data['settings']);
                    $result = $processed['result'];

                    foreach ($result as $row) {
                        TerminalSetting::updateOrInsert(
                            [
                                'cirms_client_terminal_id' => $cirmsClientTerminalId,
                                'setting_id' => $row['setting_id'],
                            ],
                            [
                                'client_terminal_id' => 0,
                                'value' => $row['value'],
                                'last_modified_by' => 1,
                                'last_modified_date' => now(),
                            ]
                        );
                    }
                }
            } else {
                $terminal = $this->clientTerminalDetails->getClientTerminalId(
                     $data['clientGroupId'],
                     $data['clientNetworkId'],
                     $data['clientBranchId'],
                     $data['terminalNo'],
                     $data['posType']
                );

                if ($terminal && $terminal->pos_type == 10) {
                    throw new \Exception("Cannot save settings: Terminal details belong to CIRMS. Please specify both location_id and client_id.");
                }
                
                if (!$terminal || $data['terminalNo'] == 0) {
                    throw new \Exception("Client terminal not found.");
                }
               
                if (!empty($data['settings'])) {
                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 1;
                    }
                    $processed = $this->processSettings( $data['settings']);
                    $result = $processed['result'];

                    foreach ($result as $row) {
                        TerminalSetting::updateOrInsert(
                            [
                                'client_terminal_id' => $terminal->id,
                                'setting_id' => $row['setting_id'],
                            ],
                            [
                                'cirms_client_terminal_id' => '0',
                                'value' => $row['value'],
                                'last_modified_by' => 1,
                                'last_modified_date' =>  Carbon::now()->toDateTimeString(),
                            ]
                        );
                    }
                }
            }
            return new RepositoryResponse(
                success: true,
                message: 'Terminal settings saved successfully.',
                error: [],
                code: 200,
                data: []
            );

        } catch (\Throwable $e) {
            return new RepositoryResponse(
                success: false,
                message: 'Something went wrong.',
                error: $e->getMessage(),
                code: is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() <= 599
                    ? $e->getCode()
                    : 500,
                data: $result
            );

        }

    }

    public function updateTerminalSettingsRepo(Request $request) 
    {
        try {
            $clientTerminalId = $request->client_terminal_id;
            $settings = $request->settings;
            
            if (!empty($settings)) {
                foreach ($settings as $row) {
                    TerminalSetting::updateOrInsert(
                        [
                            'client_terminal_id' => $clientTerminalId,
                            'setting_id' => $row['setting_id'],
                        ],
                        [
                            'value' => $row['value'],
                            'last_modified_by' => 1,
                            'last_modified_date' => Carbon::now()->toDateTimeString()
                        ]
                    );
                }
                 return new RepositoryResponse(
                    success: true,
                    message: 'Terminal settings saved successfully.',
                    error: [],
                    code: 200,
                    data: []
                );
            }
            return new RepositoryResponse(
                success: false,
                message: 'No settings provided.',
                error: [],
                code: 400,
                data: []
            );
        } catch (\Throwable $e) {
            return new RepositoryResponse(
                success: false,
                message: 'Something went wrong.',
                error: $e->getMessage(),
                code: is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() <= 599
                    ? $e->getCode()
                    : 500,
                data: []
            );
        }
    }

    public function applySettingsToMultipleTerminalsRepo(Request $request)
    {
        $clientTerminalIds = $request->client_terminal_ids;
        $settings = $request->settings;

        try {
            if (!is_array($clientTerminalIds) || empty($clientTerminalIds)) {
                return new RepositoryResponse(
                    success: false,
                    message: 'No terminal IDs provided.',
                    error: [],
                    code: 400,
                    data: []
                );
            }

            if (empty($settings)) {
                return new RepositoryResponse(
                    success: false,
                    message: 'No settings provided.',
                    error: [],
                    code: 400,
                    data: []
                );
            }

            foreach ($clientTerminalIds as $clientTerminalId) {
                foreach ($settings as $row) {
                    TerminalSetting::updateOrInsert(
                        [
                            'client_terminal_id' => $clientTerminalId,
                            'setting_id' => $row['setting_id'],
                        ],
                        [
                            'value' => $row['value'],
                            'last_modified_by' => 1, 
                            'last_modified_date' => now(),
                        ]
                    );
                }
            }

            return new RepositoryResponse(
                success: true,
                message: 'Settings applied to multiple terminals successfully.',
                error: [],
                code: 200,
                data: [
                    'terminal_ids' => $clientTerminalIds,
                    'settings_applied' => $settings
                ]
            );

        } catch (\Throwable $e) {
            return new RepositoryResponse(
                success: false,
                message: 'Something went wrong.',
                error: $e->getMessage(),
                code: is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() <= 599
                    ? $e->getCode()
                    : 500,
                data: []
            );
        }
    }

}
 