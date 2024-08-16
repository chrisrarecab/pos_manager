<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Resources\superadmin_tool_flagsResource;

use App\Models\superadmin_tool_flags;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storesuperadmin_tool_flagsRequest;
use App\Http\Requests\Updatesuperadmin_tool_flagsRequest;
use Illuminate\Http\Request;
use DB;


class SuperadminToolFlagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return superadmin_tool_flags::all();
        // return superadmin_tool_flagsResource::collection(superadmin_tool_flags::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storesuperadmin_tool_flagsRequest $request)
    {
        $inDb = DB::table('superadmin_tool_flags')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->exists();
 
        if($inDb) {
            return response("already exists", 406);
        } else {
            $flag = superadmin_tool_flags::create($request->validated());  
            return response("Successful", 200);
        }
                        
    }

    /**
     * Display the specified resource.
     */
    public function show(superadmin_tool_flags $superadmin_tool_flags, Request $request)
    {
        // return superadmin_tool_flagsResource::make($superadmin_tool_flags);
        $inDb = DB::table('superadmin_tool_flags')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->where('type', $request->type)
                ->where('value', true)
                ->exists();

        if($inDb) {
            $data = DB::table('superadmin_tool_flags')
                ->select('id', 'vatchange_date')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->where('type', $request->type)
                ->where('value', true)
                ->first();

            $response = Array(
                "ID"    => $data->id,
                "ChangeVatDate" =>  $data->vatchange_date
            );

            return json_encode($response);  
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Updatesuperadmin_tool_flagsRequest $request, superadmin_tool_flags $superadmin_tool_flags)
    {
        $superadmin_tool_flags->update($request->validated());

        if($request->isdone) {
            $updateValue = DB::table('superadmin_tool_flags')
                ->where('id', $request->requestid)
                ->update(['value' => false]);

            if($updateValue ) {
                return response("Successful",200);
            } else {
                return response("Not Found",400);
            }
        } else {
            return response("Failed",400);
        }



        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(superadmin_tool_flags $superadmin_tool_flags)
    {
        //
    }
}
