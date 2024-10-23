<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use App\Models\pos_settings;

class PosSettingsController extends Controller
{
    public function show(Request $request){
        $clientTerminalId = $request->input('clientTerminalId');

        if (is_array($clientTerminalId)) {
            $posSettings = pos_settings::whereIn('clientterminalid', $clientTerminalId)->get();
        } else {
            $posSettings = pos_settings::where('clientterminalid', $clientTerminalId)->get();
        }
        return response()->json($posSettings);
    }

}
