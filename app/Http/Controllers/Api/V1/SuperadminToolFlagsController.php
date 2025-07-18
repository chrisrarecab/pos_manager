<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Resources\superadmin_tool_flagsResource;

use App\Models\superadmin_tool_flags;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storesuperadmin_tool_flagsRequest;
use App\Http\Requests\Updatesuperadmin_tool_flagsRequest;
use App\Http\Requests\Getsuperadmin_tool_flagsRequest;
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
            $date = DB::table('superadmin_tool_flags')
                ->select('id','meta_data->vat_status as vat_status', 'meta_data->date as effectivity_date')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->first();

            if(DATE($date->effectivity_date) > DATE($request->meta_data['date'])){
                $data = Array(
                    "status" => "Date Invalid"
                );
                return response(json_encode($data), 200);
            } else {
                $flag = $this->updateDate($request->meta_data['date'], $request->meta_data['vat_status'], $date->id);  
                $flag1 = json_decode($flag, true);
                if($flag1['status'] == 'Successful') {
                    return response("Successful", 200);
                } else {
                    return response("Failed", 406);
                }
            }
        } else {
            $flag = superadmin_tool_flags::create($request->validated());  
            return response("Successful", 200);

        }
                        
    }

    /**
     * Display the specified resource.
     */
    public function show(superadmin_tool_flags $superadmin_tool_flags, Getsuperadmin_tool_flagsRequest $request)
    {
        // return superadmin_tool_flagsResource::make($superadmin_tool_flags);

        $inDb = DB::table('superadmin_tool_flags')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->where('type', $request->type)
                ->where('meta_data->vat_status', $request->newvatstatus)
                ->where('value', true)
                ->exists();

        if($inDb) {
            $data = DB::table('superadmin_tool_flags')
                ->select('id', 'meta_data->vat_status as vat_status','meta_data->date as date')
                ->where('clientgroupid', $request->clientgroupid)
                ->where('networkid', $request->networkid)
                ->where('branchid', $request->branchid)
                ->where('terminalno', $request->terminalno)
                ->where('type', $request->type)
                ->where('value', true)
                ->first();

            $response = Array(
                "id"    => $data->id,
                "effectivityDate" =>  $data->date
            );

            return response($response,200)->header('Content-Type', 'application/json'); 
        } else {
            $response = Array(
                "id"    => 0,
                "effectivityDate" =>  '0000-00-00'
            );

            return response($response,200)->header('Content-Type', 'application/json');  
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
                ->update(['value' => false, 'updated_at' => now()]);

            if($updateValue) {
                return response("Successful",200);
            } else {
                return response("Not Found",400);
            }
        } else {
            return response("Failed",422);
        }
    }

    public function updateDate($date, $vatStatus, $id)
    {

        $updateValue = DB::table('superadmin_tool_flags')
                        ->where('id', $id)
                        ->where('type', 1)
                        ->update(['value' => '1', 
                            'meta_data->date' => $date,
                            'meta_data->vat_status' => $vatStatus,
                            'updated_at' => now()]);

        if($updateValue) {
            $data = Array(
                "status" => "Successful"
            );
            return json_encode($data);
        } else {
            $data = Array(
                "status" => "Failed"
            );
            return json_encode($data);
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
