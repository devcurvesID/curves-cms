<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public $successStatus = 200;

    public function login(){

        if(Auth::attempt(['username' => request('username'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
//            return response()->json(['success' => $success], $this->successStatus);

            //For LoginTwo
//            return response()->json(['success' => true, 'user' => $success, 'role' => $user->role_id], $this->successStatus);

            // Revisi V3
            if (!empty($user->user_memberpersonal->photo)):
                $user->setAttribute('photo', $user->user_memberpersonal->photo);
            else:
                $user->setAttribute('photo', '');
            endif;

            $user->setAttribute('keytag', $user->user_memberpersonal->key_tag_id);
            $user->setAttribute('role', $user->user_role->role);
            $user->user_club;

            return response()->json(['success' => true, 'user' => $success, 'profile' => $user], $this->successStatus);
        }
        else{
//            return response()->json(['error'=>'Unauthorised'], 401);
//            return response()->json(['success'=>'Unauthorised man'], 401);

            //For LoginTwo
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}
