<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPermissions;

class UserPermissionsController extends Controller
{
    public function index (Request $request)
    {
        $userId = $request->input('detail');
        // echo $clientNetworkId;

        if (is_array($userId)) {
            $permissions = UserPermissions::whereIn('user_id', $userId)
            ->select('code')
            ->get();
        } else {
            $permissions = UserPermissions::where('user_id', $userId)
            ->select('code')
            ->get();
        }
        // $users = UserList::select('id', 'full_name', 'username', 'status')
        //     ->get();
        return json_encode(compact('permissions'));
    }

    public function edit (Request $request)
    {
        $userId = $request->input('id');
        $permissions = $request->input('permissions');

        UserPermissions::where('user_id', $userId)->delete();

        if (count($permissions) > 0) {
            foreach($permissions as $permission){
                $insertValue = userPermissions::insert([
                    'user_id' => $userId, 
                    'code' => $permission
                ]);
            } 
        }
            
        $data = Array(
            "status" => "Successful"
        );
        
        return json_encode($data);
    }
}
