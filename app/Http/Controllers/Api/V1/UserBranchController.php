<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserBranch;
use DB;

class UserBranchController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('detail');
        if (is_array($userId)) {
            $branches = UserBranch::whereIn('user_id', $userId)
            ->select('client_branch_id as id')
            ->get();
        } else {
            $branches = UserBranch::where('user_id', $userId)
            ->select('client_branch_id as id')
            ->get();
        } 
        // $userDetails = UserList::whereIn('id', $userId)
        //     ->select('full_name', 'username', 'status', 'password')
        //     ->get();
        return json_encode(compact('branches'));
    }

    public function edit(Request $request)
    {
        $userId = $request->input('id');
        $branches = $request->input('branches');
        // return count($branches);
        UserBranch::where('user_id', $userId)->delete();

        if (count($branches) > 0) {
            // $flag = userBranch::create($request->validated());  
            // return response("Successful", 200);
            // if(in_array(1, $branches)){
            //     $insertValue = UserBranch::insert([
            //         'user_id' => $userId, 
            //         'client_branch_id' => 1
            //     ]);
            // } else {
                foreach($branches as $branch){
                    $insertValue = UserBranch::insert([
                        'user_id' => $userId, 
                        'client_branch_id' => $branch
                    ]);
                } 
            // }
            
        }

        $data = Array(
            "status" => "Successful"
        );
        return json_encode($data);
        // return 'very good!';

    }
}
