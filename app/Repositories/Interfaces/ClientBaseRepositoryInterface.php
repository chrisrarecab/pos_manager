<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\Setting;

interface ClientBaseRepositoryInterface
{
	public function getTerminalDetails($id);
    public function getCoreTerminalId(
        $clientGroupId, 
        $clientNetworkId, 
        $clientBranchId, 
        $terminalNo,
        $posType
    );
}

