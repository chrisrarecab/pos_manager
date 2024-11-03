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
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|uuid',
            'source' => 'required|integer|max:2',
            'values' => 'required|array|min:1',
            'values.*' => 'array', 
            'datemodified' => 'nullable|date',
        ], [
            'uuid.required' => 'Missing required field: uuid',
            'uuid.uuid' => 'Invalid type: uuid must be a valid UUID',
            'source.required' => 'Missing required field: source',
            'source.integer' => 'Invalid type: source must be an integer',
            'values.required' => 'Missing required field: values',
            'values.array' => 'Invalid type: values must be an array',
            'values.*.array' => 'Invalid type: each item in values must be an array',
            'datemodified.date' => 'Invalid format: datemodified must be a valid date',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('values') as $index => $item) {
                $keys = array_keys($item);
                foreach ($keys as $key) {
                    if (is_null($key) || $key === '') {
                        $validator->errors()->add("values", 'The key cannot be null or empty.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors();
            $detailedErrors = '';

            foreach ($errors->messages() as $field => $messages) {
                foreach ($messages as $message) {
                    $type = null;
                    if (strpos($message, 'required') !== false) {
                        $type = 'Missing required field';
                    } elseif (strpos($message, 'Invalid type') !== false) {
                        $type = 'Invalid type';
                    } elseif (strpos($message, 'Invalid format') !== false) {
                        $type = 'Invalid format';
                    }
                    $detailedErrors .= "{$type}";
                }
            }

            return response()->json([
                'isSuccessful' => false, 
                'values'=> [], 
                'message'=> $detailedErrors, 
                'errors' => $validator->errors()], 422);
        }

        foreach ($request->input('values') as $item) {
            foreach ($item as $key => $value) {
                DB::table('pos_settings')->updateOrInsert(
                    [
                        'uuid' => $request->input('uuid'),
                        'source' => $request->input('source'),
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                        'datemodified' => $request->input('datemodified') ?? now(),
                    ]
                );
            }
        }
        return response()->json([
            'isSuccessful' => 'true', 
            'values'=> [], 
            'message' => 'POS settings saved successfully.', 
            'errors' =>[]], 201);
    }
}
