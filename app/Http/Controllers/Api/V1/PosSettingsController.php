<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;        
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\Models\pos_settings;

class PosSettingsController extends Controller
{
    public function save(Request $request){
        $request->merge([
            'source' => $request->source ?? 2,
            'settings' => array_map(function ($item) {
                return array_merge([
                    'client_visible' => false,
                    'setting_type' => 1
                ], $item);
            }, $request->input('settings', []))
        ]);
        
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|uuid',
            'source' => 'required|integer|max:2',
            'settings' => 'required|array|min:1',
            'settings.*' => 'array',
            'settings.*.client_visible' => 'required|boolean'
        ], [
            'uuid.required' => 'Missing required field: uuid',
            'uuid.uuid' => 'Invalid type: uuid must be a valid UUID',
            'source.required' => 'Missing required field: source',
            'source.integer' => 'Invalid type: source must be an integer',
            'settings.required' => 'Missing required field: settings',
            'settings.array' => 'Invalid type: settings must be an array',
            'settings.*.array' => 'Invalid type: each item in settings must be an array'
        ]);

        $validator->after(function ($validator) use ($request){
            if ($request->has('settings') && is_array($request->input('settings'))){
                foreach ($request->input('settings') as $index => $item){
                    if (!isset($item['column_name']) || trim($item['column_name']) === ''){
                        $validator->errors()->add("settings.$index.column_name", 'Missing required field: column_name');
                    }
                    if (!array_key_exists('value', $item)){
                        $validator->errors()->add("settings.$index.value", 'Missing required field: value');
                    }                    
                }
            }
        });


        if ($validator->fails()){
            $errors = $validator->errors();
            $detailedErrors = '';

            foreach ($errors->messages() as $field => $messages){
                foreach ($messages as $message){
                    $type = null;
                    if(strpos($message, 'required') !== false){
                        $type = 'Missing required field. ';
                    }elseif(strpos($message, 'must be') !== false){
                        $type = 'Invalid format.';
                    } 
                    $detailedErrors .= "{$type}";
                }
            }

            return response()->json([
                'isSuccessful' => false, 
                'settings'=> [], 
                'message'=> $detailedErrors, 
                'errors' => $validator->errors()], 422);
        }

        $settings = $request->input('settings') ?? [];

        foreach ($settings as $item) {
            DB::table('pos_settings')->updateOrInsert(
                [
                    'uuid' => $request->input('uuid'),
                    'source' => $request->input('source'),
                    'key' => $item['column_name'],
                ],
                [
                    'value' => $item['value'],
                    'client_visible' => $item['client_visible'],
                    'setting_type' => $item['setting_type'],
                    'datemodified' =>  DB::raw('CURRENT_TIMESTAMP(6)'),
                ]
            );
        }
        
        return response()->json([
            'isSuccessful' => 'true', 
            'settings'=> [], 
            'message' => 'POS settings saved successfully.', 
            'errors' =>[]], 201);
    }
}
