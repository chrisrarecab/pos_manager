<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use App\Models\pos_settings;

class PosSettingsController extends Controller
{
    public function store(Request $request){
        $rules = [
            'clientterminalid' => 'required|integer',
            'source' => 'required|integer|in:1,2',
            'key' => 'required|string|max:128',
            'value' => 'nullable|string|max:1024',
            'datemodified' => 'nullable|date',
        ];

        if($request->isMethod('post') && is_array($request->input('data'))) {
            $responses = [];

            foreach($request->input('data') as $item){
                $validator = Validator::make($item, $rules);

                if($validator->fails()){
                    $responses[] = [
                        'status' => 'error',
                        'errors' => $validator->errors(),
                    ];
                    continue; 
                }

                try{
                    $posSetting = pos_settings::where('clientterminalid', $item['clientterminalid'])
                        ->where('source', $item['source'])
                        ->where('key', $item['key'])
                        ->first();

                    if($posSetting){
                        pos_settings::where('clientterminalid', $item['clientterminalid'])
                        ->where('source', $item['source'])
                        ->where('key', $item['key'])
                        ->update([
                            'value' => $item['value'],
                            'datemodified' => $item['datemodified'] ?? now(),
                        ]);
                        $responses[] = ['status' => 'success', 'method'=>'update'];
                    }else{
                        $posSetting = pos_settings::create([
                            'clientterminalid' => $item['clientterminalid'],
                            'source' => $item['source'],
                            'key' => $item['key'],
                            'value' => $item['value'],
                            'datemodified' => $item['datemodified'] ?? now(),
                        ]);
                        $responses[] = ['status' => 'success', 'method'=>'create'];
                    }
                }catch(QueryException $e){
                    if ($e->errorInfo[1] === 1062) {
                        $responses[] = [
                            'status' => 'error',
                            'message' => 'Duplicate entry for primary key.',
                            'data' => $item,
                        ];
                    }else{
                        $responses[] = [
                            'status' => 'error',
                            'message' => 'Database error: ' . $e->getMessage(),
                            'data' => $item,
                        ];
                    }
                }
            }
            return response()->json($responses, 201);

        }else{
            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            try{
                $posSetting = pos_settings::where('clientterminalid', $request->input('clientterminalid'))
                    ->where('source', $request->input('source'))
                    ->where('key', $request->input('key'))
                    ->first();

                if($posSetting){
                    pos_settings::where('clientterminalid', $request->input('clientterminalid'))
                        ->where('source', $request->input('source'))
                        ->where('key', $request->input('key'))
                        ->update([
                            'value' => $request->input('value'),
                            'datemodified' => $request->input('datemodified', now()),
                        ]);

                    return response()->json(['status' => 'success', 'method' => 'update'], 200);
                }else{
                    $posSetting = pos_settings::create([
                        'clientterminalid' => $request->input('clientterminalid'),
                        'source' => $request->input('source'),
                        'key' => $request->input('key'),
                        'value' => $request->input('value'),
                        'datemodified' => $request->input('datemodified', now()),
                    ]);

                    return response()->json(['status' => 'success', 'method' => 'create'], 201);
                }
            }catch (QueryException $e){
                if ($e->errorInfo[1] === 1062) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Duplicate entry for primary key.',
                    ], 409);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Database error: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }
    }

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
