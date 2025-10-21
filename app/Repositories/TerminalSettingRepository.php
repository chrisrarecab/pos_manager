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
            ->whereHas('terminalSetting', function ($q) use ($terminalId) {
                $q->where('terminal_id', $terminalId);
            })
            ->with([
                'options:id,setting_id,name,value',
                'issues:terminal_id,setting_id,issue_id',
                'user:id,full_name',
                'terminalSetting' => fn($q) => $q->where('terminal_id', $terminalId)
            ])
            ->get();
    }

    public function upsertSetting(array $row, $terminalId): void
    {
        TerminalSetting::updateOrInsert(
            [
                'terminal_id' => $terminalId,
                'setting_id' => $row['setting_id'],
            ],
            [
                'value' => $row['value'],
                'last_modified_by' => 1,
                'last_modified_date' => Carbon::now()->toDateTimeString(),
            ]
        );
    }
}
 