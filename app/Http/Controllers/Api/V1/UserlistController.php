<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Userlist;
use Illuminate\Support\Facades\Hash;

class UserlistController extends Controller
{
    //
    public function index(Request $request)
    {
        $clientNetworkId = $request->input('detail');
        // echo $clientNetworkId;

        if (is_array($clientNetworkId)) {
            $users = UserList::whereIn('client_network_id', $request->input('detail'))
            ->where('is_deleted', 0)
            ->select('id', 'full_name', 'username', 'status')
            ->get();
        } else {
            $users = UserList::where('client_network_id', $clientNetworkId)
            ->where('is_deleted', 0)
            ->select('id', 'full_name', 'username', 'status')
            ->get();
        }
        // $users = UserList::select('id', 'full_name', 'username', 'status')
        //     ->get();
        return json_encode(compact('users'));
    }
    
    public function add(Request $request)
    {
        $insertId = userlist::insertGetId([
            'full_name' => '',
            'username' => '',
            'status' => 2,
            'client_network_id' => $request->clientNetworkId,
            'is_deleted' => 1
        ]);

        return $insertId;
    }

    public function delete(Request $request)
    {
        $deleteValue = Userlist::where('id', $request->userId)
                        ->update(['is_deleted' => 1, 
                            'last_modified_date' => now()]);

        if($deleteValue) {
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

    public function show(Request $request)
    {
        $userId = $request->input('detail');
        if (is_array($userId)) {
            $userDetails = UserList::whereIn('id', $userId)
            ->select('full_name', 'username', 'status', 'password', 'client_network_id')
            ->get();
        } else {
            $userDetails = UserList::where('id', $userId)
            ->select('full_name', 'username', 'status', 'password', 'client_network_id')
            ->get();
        }
        // $userDetails = UserList::whereIn('id', $userId)
        //     ->select('full_name', 'username', 'status', 'password')
        //     ->get();
        return json_encode(compact('userDetails'));
    }

    public function edit(Request $request)
    {
        $userId = $request->input('id');
        $userName = $request->input('username');
        $fullName = $request->input('fullname');
        $status = $request->input('status');
        $password = $request->input('password');
        // $branches = $request->input('branches');

        $updateValue = Userlist::where('id', $userId)
                        ->update(['full_name' => $fullName, 
                            'username' => $userName,
                            'status' => $status,
                            'password' => Hash::make($password),
                            'last_modified_date' => now(),
                            'is_deleted' => 0]);

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
    
}
