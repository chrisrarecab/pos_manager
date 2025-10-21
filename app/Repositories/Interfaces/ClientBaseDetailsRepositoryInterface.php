<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\Setting;

interface ClientBaseDetailsRepositoryInterface
{
    public function getTerminalDetails($clientGroupId, $softwareId);
    public function getClientTerminalIdByUuid(string $uuid): ?object;

}

