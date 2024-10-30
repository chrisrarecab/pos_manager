<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\Models\pos_settings;

class PosSettingsController extends Controller
{
    public function show(Request $request){
        $clientTerminalUuid = $request->input('uuid');

        if (is_array($clientTerminalUuid)) {
            $posSettings = pos_settings::selectRaw('DATE(datemodified) as settings_date, uuid, source, `key`, `value`')
                ->whereIn('uuid', $clientTerminalUuid)
                ->orderBy('datemodified', 'desc')
                ->get();
        } else {
            $posSettings = pos_settings::selectRaw('DATE(datemodified) as settings_date, uuid, source, `key`, `value`')
                ->where('uuid', $clientTerminalUuid)
                ->orderBy('datemodified', 'desc') 
                ->get();
        }
        
        return response()->json($posSettings);
        
    }

}
