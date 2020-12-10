<?php

namespace App\Http\Controllers\API;


use App\User;
use App\users_details;
use App\users_passwords;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoginControlle extends BaseController
{ 
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
            'mobile_no' => 'required|max:10',
            'created_by'=>'required',
//            'password' => 'required',
//            'c_password' => 'required|same:password',
//            'login_id' => 'required',
        ]);
//        username, role_id, email, mobile_no, created_by
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

//        $input = $request->all();
//        $input['password'] = bcrypt('1234');
//        array_merge($input,['status'=>1]);
//        array_merge($input,['co_sl'=>1]);
//        array_merge($input,['login_id'=>rand(11111, 99999)]);
//
        $input_data = User::create([
            'username' => request('username'),
            'email' => request('email'),
            'role_id' => request('role_id'),
            'mobile_no' => request('mobile_no'),
            'password' => bcrypt('1234'),
            'co_sl' => "1",
            'status'=>1,
            'login_id'=>rand(11111, 99999),
            'created_by' => request('created_by'),

        ]);
//        dd($input_data);
//        $input['status']->status=1;
//        $input['login_id']->login_id=rand(11111, 99999);

//        $user = User::create($input);
        $success['token'] =  $input_data->createToken('MyApp')->accessToken;
        $success['username'] =  $input_data->username;
        $success['id'] =  $input_data->id;
        $success['role_id'] =  $input_data->role_id;
        $success['login_id'] =  $input_data->login_id;
        $success['password'] =  $input_data->password;
        $success['email'] =  $input_data->email;
        $success['mobile_no'] =  $input_data->mobile_no;
        $success['status'] =  $input_data->status;
        $success['created_by'] =  $input_data->created_by;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
//        public function login(Request $request)
//     {
//         if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
//             $user = Auth::user();
// //            dd($user);
// //        $success = $request->all();
//             $success['token'] =  $user->createToken('MyApp')-> accessToken;
//             $success['name'] =  $user->username;
//             $success['id'] =  $user->id;
//             $success['role_id'] =  $user->role_id;
//             $success['login_id'] =  $user->login_id;
//             $success['password'] =  $user->password;
//             $success['email'] =  $user->email;
//             $success['mobile_no'] =  $user->mobile_no;
//             $success['status'] =  $user->status;
//             $success['created_by'] =  $user->created_by;
// //
//             return $this->sendResponse($success, 'User login successfully.');
//         }
//         else{
//             return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
//         }
//     }
 public function login(Request $request)
    {

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
//            dd($user);

            $device_token=$request->device_token;

            $user->device_token=$request->device_token;
            $user->created_by=$user['user_id'];
            $user->save();

            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['name'] =  $user->username;
            $success['id'] =  $user->id;
            $success['role_id'] =  $user->role_id;
            $success['login_id'] =  $user->login_id;
            $success['password'] =  $user->password;
            $success['email'] =  $user->email;
            $success['mobile_no'] =  $user->mobile_no;
            $success['status'] =  $user->status;
            $success['created_by'] =  $user->created_by;
//
            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return $this->sendResponse([], 'Logged out successfully.');
    }
}
