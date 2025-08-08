<?php

namespace App\Services;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Setting;
use App\Models\SettingOption;
use App\Models\TerminalSetting;

use App\DataTransferObjects\ServiceResponse;
use App\Repositories\Interfaces\TerminalSettingInterface;
use App\Repositories\Interfaces\ClientBaseRepositoryInterface;

class TerminalSettingService
{
    public function __construct(
        protected ClientBaseRepositoryInterface $clientBaseRepo,
        protected TerminalSettingInterface $terminalSettingRepo
    ) {}

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
                $settingData = $this->terminalSettingRepo->getSettings([
                    'name' =>  $setting['name'],
                    'software_id' =>  $setting['software_id'],
                ]);
            } 
            
            if (!$settingData) {
                return ServiceResponse::failure(
                    'Setting lookup failed.',
                    'Setting not found for: ' . json_encode($setting),
                    404
                );
            }

            $settingId = $settingData->id;
            $formElement = $settingData->form_element;
            $softwareId= $settingData->software_id;

            switch ($formElement) {
                case 'radio_button':
                    $settingValue = $setting['value'];
                    break;
                case 'dropdown':
                    $settingValue = $this->terminalSettingRepo->getDropdownOptionId($setting['value'], $settingId);
                    break;  
                case 'multi_select_dropdown':
                    $values = is_array($setting['value']) ? $setting['value'] : [$setting['value']];
                    $optionIds = $this->terminalSettingRepo->getMultiSelectOptionIds($values, $settingId);
                    $settingValue = implode(',', $optionIds); 
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

    public function fetchTerminalSettings($clientTerminalId, $softwareId)
    {
        $settings = $this->terminalSettingRepo->getSettingsByTerminalId($clientTerminalId, $softwareId);

         return $settings->map(function ($setting) {
            $ts = $setting->terminalSetting;
            $rawValue = $ts?->value;
            $resolvedValue = $rawValue;

            switch ($setting->form_element) {
                case 'dropdown':
                case 'radio_button':
                    $resolvedValue = $setting->options->firstWhere('id', (int) $rawValue)?->id ?? null;
                    break;
                case 'multi_select_dropdown':
                    $resolvedValue = $rawValue ? array_map('intval', explode(',', $rawValue)) : [];
                    break;
                case 'json':
                    $cleanJson = stripslashes($rawValue);
                    $resolvedValue = json_decode($cleanJson, true);
                    break;
                default:
                    $resolvedValue = $rawValue;
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
    }

    // store uses name and value whlie update uses setting id and setting option id
    public function storeTerminalSettings(array $data) : ServiceResponse
    {
        $cirmsTerminalId = '';
        $result = [];

        try {
            if ( !empty($data['terminalNo']) && empty($data['clientGroupId']) && empty($data['clientNetworkId']) 
                && !empty( $data['clientId']) && !empty( $data['locationId'])) {

                $coreTerminalId = 0;
                $cirmsTerminalId = implode('-', [
                     $data['clientId'],
                     $data['clientBranchId'],
                     $data['locationId'],
                     $data['terminalNo']
                ]);

                if (!empty($data['settings'])) {
                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 2;
                    }
                    unset($setting); 

                    $data['settings'][] = [
                        'name' => 'terminalConnections',
                        'value' => json_encode($data['terminalConnections']),
                        'software_id' => 2
                    ];

                    $processed = $this->processSettings( $data['settings']);
                    $result = $processed['result'];
                    

                    foreach ($result as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $coreTerminalId, $cirmsTerminalId);
                    }
                }
            } elseif (!empty( $data['clientGroupId']) && !empty( $data['clientNetworkId']) && !empty( $data['posType'])) {
                $terminal = $this->clientBaseRepo->getCoreTerminalId(
                     $data['clientGroupId'],
                     $data['clientNetworkId'],
                     $data['clientBranchId'],
                     $data['terminalNo'],
                     $data['posType']
                );
                
                $cirmsTerminalId = 0;                
                $coreTerminalId = $terminal->id;
                    
                if ($terminal && $terminal->pos_type == 10) {
                    return ServiceResponse::failure( 'Request failed.', 'Terminal details belong to CIRMS. Please specify both location_id and client_id.', 400);
                }
                
                if (!$terminal) {
                    return ServiceResponse::failure( 'Request failed.', 'Client terminal not found.', 404);
                }
               
                if (!empty($data['settings'])) {
                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 1;
                    }
                    
                    $processed = $this->processSettings( $data['settings']);
                    $result = $processed['result'];

                    foreach ($result as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $coreTerminalId, $cirmsTerminalId);
                    }
                }
            }
            return ServiceResponse::success("Terminal settings saved successfully.");
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }

    }

    public function updateTerminalSettings(Request $request) 
    {
        try {
            $settings = $request->settings;
            $softwareId = session('software_id');
            
            if ($request->func !== 'save-terminal-table') {
                
                if ($softwareId == 1) {
                    $coreTerminalId = $request->client_terminal_id;
                    $cirmsTerminalId = 0;
                } else {
                    $cirmsTerminalId = (string)$request->client_terminal_id;
                    $coreTerminalId = 0;
                }

            } else {
                $cirmsTerminalId = $request->cirms_terminal_id;
                $coreTerminalId = $request->core_terminal_id;
            }
           
            if (empty($settings)) {
                return ServiceResponse::failure('Request failed.', 'No settings provided.', 400);
            }

            foreach ($settings as $setting) {
                $this->terminalSettingRepo->upsertSetting($setting, $coreTerminalId, $cirmsTerminalId);
            }

            return ServiceResponse::success("Terminal settings saved successfully.");
       
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }

    public function applySettingsToMultipleTerminals(Request $request)
    {
        try {
            $settings = $request->settings;

            if (empty($settings)) {
                return ServiceResponse::failure('Request failed.', 'No settings provided.', 400);
            }

            $softwareId = session('software_id');
            
            if ($softwareId == 1) {
                $coreTerminalIds = $request->client_terminal_ids;
                $cirmsTerminalIds = 0;

                foreach ($coreTerminalIds as $coreTerminalId) {
                    foreach ($settings as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $coreTerminalId, $cirmsTerminalIds);
                    }
                }
            }  else {
                $cirmsTerminalIds = $request->client_terminal_ids;
                $coreTerminalIds = 0;

                foreach ($cirmsTerminalIds as $cirmsTerminalId) {
                    foreach ($settings as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $coreTerminalIds, $cirmsTerminalId);
                    }
                }
            }

            // if (!is_array($coreTerminalIds) || empty($coreTerminalIds)) {
            //     return ServiceResponse::failure('Request failed.', 'No terminal IDs provided.', 400);
            // }
       return ServiceResponse::success('Settings applied to multiple terminals successfully.');

        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }

    
}