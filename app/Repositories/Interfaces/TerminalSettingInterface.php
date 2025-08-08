<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\TerminalSetting;

interface TerminalSettingInterface
{
	public function getSettings(array $data) :?Setting ;
	public function getDropdownOptionId(string $value, int $settingId): ?int;
	public function getMultiSelectOptionIds(array $values, int $settingId): array;
	public function getSettingsByTerminalId(string $terminalId, $softwareId);
	public function upsertSetting(array $row, $coreTerminalId, $cirmsTerminalId): void;
	public function checkClientTerminalId($clientTerminalId): ?TerminalSetting;
}

