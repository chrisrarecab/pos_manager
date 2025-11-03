<?php

namespace App\Services;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Setting;
use App\Models\SettingOption;
use App\Models\TerminalSetting;

use App\DataTransferObjects\ServiceResponse;
use App\Repositories\Interfaces\TerminalSettingInterface;
use App\Repositories\Interfaces\ClientBaseDetailsRepositoryInterface;

class TerminalSettingService
{
    public function __construct(
        protected ClientBaseDetailsRepositoryInterface $clientBaseRepo,
        protected TerminalSettingInterface $terminalSettingRepo
    ) {}

    public function processSettings(array $settings): ServiceResponse
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
            ];
        }

        return ServiceResponse::success(
            'Settings processed successfully.',
            ['result' => $result]
        );
    }

    public function fetchTerminalSettings($terminalId, $softwareId)
    {
        $settings = $this->terminalSettingRepo->getSettingsByTerminalId($terminalId, $softwareId);

         return $settings->map(function ($setting) {
            $ts = $setting->terminalSetting;
            $rawValue = $ts?->value;
            $resolvedValue = $rawValue;

            switch ($setting->form_element) {
                case 'dropdown':
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

                'value'              => $resolvedValue,
            ];
        });
    }

    public function storeTerminalSettings(array $data) : ServiceResponse
    {
        $result = [];
        try {
            $uuid = $data['uuid'];
            $identifyUuid = $this->clientBaseRepo->getClientTerminalIdByUuid($uuid);

            if (!$identifyUuid) {
                return ServiceResponse::failure( 'Request failed.', 'Client terminal not found.', 404);
            }

            // CIRMS
            if ($identifyUuid->pos_type == 10) {
                $terminalId = $identifyUuid->id;

                if (!empty($data['settings'])) {
                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 2;
                    }
                    unset($setting); 

                    if (!empty($data['terminalConnections'])) {
                        $hasTerminalConnections = collect($data['settings'])->contains(function ($setting) {
                            return isset($setting['name']) && $setting['name'] === 'terminalConnections';
                        });

                        if (!$hasTerminalConnections) {
                            $data['settings'][] = [
                                'name' => 'terminalConnections',
                                'value' => json_encode($data['terminalConnections']),
                                'software_id' => 2
                            ];
                        }
                    }

                   $processed = $this->processSettings($data['settings']);

                    if (!$processed->success) {
                        return $processed; 
                    }

                    $result = $processed->values['result'];

                    foreach ($result as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $terminalId);
                    }
                }
            } else {
            // CORE            
                $terminalId = $identifyUuid->id;

                if (!empty($data['settings'])) {
                    foreach ($data['settings'] as &$setting) {
                        $setting['software_id'] = 1;
                    }

                    if (!empty($data['terminalConnections'])) {
                        $hasTerminalConnections = collect($data['settings'])->contains(function ($setting) {
                            return isset($setting['name']) && $setting['name'] === 'terminalConnections';
                        });

                        if (!$hasTerminalConnections) {
                            $data['settings'][] = [
                                'name' => 'terminalConnections',
                                'value' => json_encode($data['terminalConnections']),
                                'software_id' => 1
                            ];
                        }
                    }

                    $processed = $this->processSettings( $data['settings']);
                    if (!$processed->success) {
                        return $processed;
                    }

                    $result = $processed->values['result'];

                    foreach ($result as $row) {
                        $this->terminalSettingRepo->upsertSetting($row, $terminalId);
                    }
                }

            }
            return ServiceResponse::success("Terminal settings saved successfully.");
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }

    public function updateTerminalSettings(array $data) 
    {
        try {
            $settings = $data['settings'];
            $terminalId = $data['terminalId'];
           
            if (empty($settings)) {
                return ServiceResponse::failure('Request failed.', 'No settings provided.', 400);
            }

            foreach ($settings as $setting) {
                $this->terminalSettingRepo->upsertSetting($setting, $terminalId);
            }

            return ServiceResponse::success("Terminal settings saved successfully.");
       
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }

    public function applySettingsToMultipleTerminals(array $data)
    {
        try {
           $settings = $data['settings'];

            if (empty($settings)) {
                return ServiceResponse::failure('Request failed.', 'No settings provided.', 400);
            }

            $terminalIds = $data['terminalId'];
            
            foreach ($terminalIds as $terminalId) {
                foreach ($settings as $row) {
                    $this->terminalSettingRepo->upsertSetting($row, $terminalId);
                }
            }

            return ServiceResponse::success('Settings applied to multiple terminals successfully.');

        } catch (\Throwable $e) {
            return ServiceResponse::failure('Something went wrong.', $e->getMessage(), 500);
        }
    }

    
}