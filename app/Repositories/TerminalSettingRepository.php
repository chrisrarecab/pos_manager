<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;

use App\Models\Setting;
use App\Models\SettingOption;
use App\Models\TerminalSetting;

use App\Repositories\Interfaces\TerminalSettingInterface;

class TerminalSettingRepository implements TerminalSettingInterface
{
    public function getSettings(array $data) :?Setting 
    {
        return Setting::select('id', 'form_element', 'software_id')
            ->where('name', $data['name'])
            ->where('software_id', $data['software_id'])
            ->first();
    }

    public function getDropdownOptionId(?string $value, int $settingId): ?int
    {
        return SettingOption::where('value', $value)
            ->where('setting_id', $settingId)
            ->value('id');
    }

    public function getMultiSelectOptionIds(array $values, int $settingId): array
    {
        return SettingOption::whereIn('value', $values)
            ->where('setting_id', $settingId)
            ->pluck('id')
            ->toArray();
    }

    public function getSettingsByTerminalId(string $terminalId, $softwareId)
    {
        return Setting::where('software_id', $softwareId)
            ->where('is_deleted', 0)
            ->whereHas('terminalSetting', function ($query) use ($terminalId, $softwareId) {
                if ($softwareId === '1') {
                    $query->where('core_terminal_id', $terminalId);
                } elseif ($softwareId === '2') {
                    $query->where('cirms_terminal_id', $terminalId);
                }
            })
            ->with(['options', 'issues', 'terminalSetting' => function ($q) use ($terminalId, $softwareId) {
                if ($softwareId === '1') {
                    $q->where('core_terminal_id', $terminalId);
                } elseif ($softwareId === '2') {
                    $q->where('cirms_terminal_id', $terminalId);
                }
            }, 'user:id,full_name'])
            ->get();
    }

    public function upsertSetting(array $row, $coreTerminalId, $cirmsTerminalId): void
    {
        TerminalSetting::updateOrInsert(
            [
                'core_terminal_id' => $coreTerminalId,
                'setting_id' => $row['setting_id'],
                'cirms_terminal_id' => $cirmsTerminalId,
            ],
            [
                'value' => $row['value'],
                'last_modified_by' => 1,
                'last_modified_date' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function checkClientTerminalId($clientTerminalId): ?TerminalSetting
    {
        return TerminalSetting::where('core_terminal_id', $clientTerminalId)
            ->orWhere('cirms_terminal_id', $clientTerminalId)
            ->first();
    }
    

}
 