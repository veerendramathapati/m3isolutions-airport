<?php

namespace App\Http\Controllers\API;

use App\checklists;
use App\input_datas;
use App\input_datas_status;
use App\input_image;
use App\parameter_lists;
use App\restroom_master;
use App\role_masters;
use App\type_master;
use App\User;
use App\users_details;
use App\users_passwords;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Type;
use Carbon\Carbon;


class ServicesController extends BaseController
{

   public function fetch_restroom_details(Request $request){
        $input = $request->all();
//dd($input);
        $validator = Validator::make($input, [
            'rm_uid'=>'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

//        $rm = restroom_master::find($input['r_m_id']);
//
//        $comments = $rm->id;
//
//        dd($rm);
//        $comment = checklists::find(3);
////
//////        $post = $comment->id;
////
////        dd($comment);
//        $group_details=$comment->restroom_master()->attach($rm);

        $data= restroom_master::where('unique_identifier',$input['rm_uid'])->first();

 if (is_null($data)) {
            return $this->sendError('Please check the data entered.');
        }
        $r_m_id=$data->id;
        $r_name=$data->r_name;
        $location=$data->location;
        $unique_identifier=$data->unique_identifier;
        $co_sl=$data->co_sl;


        $parameters =parameter_lists ::select('parameter_lists.p_m_id','parameter_master.p_name')
            ->where('parameter_lists.r_m_id', $r_m_id)
            ->join('parameter_master','parameter_master.id','=','parameter_lists.p_m_id')
//            ->distinct('parameter_lists.p_m_id')
            ->get();

        if (is_null($parameters)) {
            return $this->sendError('parameter data not found.');
        }

        return $this->sendResponse_restroomdata($parameters,$r_m_id,$r_name,$location,$unique_identifier,$co_sl,'data retrieved successfully.');

    }
   
   public function get_restroom(Request $request)
    {
      
        $data = restroom_master::all();
 if (is_null($data)) {
            return $this->sendError('Please check the data entered.');
        }
        return $this->sendResponse($data, 'data retrieved successfully.');

    }
   
   public function fetch_parameter_details(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'pm_id'=>'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $parameters =checklists ::select('checklists.c_m_id','check_masters.c_name')
            ->where('checklists.p_m_id', $input['pm_id'])
            ->join('check_masters','check_masters.id','=','checklists.c_m_id')
            ->get();

        if (is_null($parameters)) {
            return $this->sendError('parameter data not found.');
        }

        return $this->sendResponse($parameters,'data retrieved successfully.');

    }


   public function inputdata_test(Request $request)
    {
        $input = $request->all();

        $target_path = public_path('image_uploads/');
        $rm_id = $request->rm_id;
        $pm_id = $request->pm_id;
        $cm_id = $request->cm_id;
        $description = $request->description;
        $size =  $_POST['size'];
        $created_by = $request->created_by;

        $input_data = input_datas::create([
            'description' => request('description'),
            'r_m_id' => request('rm_id'),
            'p_m_id' => request('pm_id'),
            'c_m_id' => request('cm_id'),
            'co_sl' => "1",
            'created_by' => request('created_by'),

        ]);
        $input_id = $input_data->id;


        if (!empty($_FILES))
        {
            for ($x = 0; $x < $size; $x++)
            {
                try
                {

                    $newname =  $created_by."ran" . rand(11111, 99999) . '.jpg';
                    // Throws exception incase file is not being moved
                    if (!move_uploaded_file($_FILES['image'.$x]['tmp_name'], $target_path .$newname))
                    {
                        // make error flag true
                        return $this->sendResponse([], 'File did not Upload.');
                    }


                    else{

                        // $path= '/'.$target_path .$newname;
                        $path = '/kiab/public/image_uploads/' . $newname;
                        $input_image = input_image::create([
                            'input_id' => $input_id,
                            'img_path' => $path,
                            'co_sl' => "1",
                            'created_by' => request('created_by'),
                        ]);

                        // File successfully uploaded
                        // echo json_encode(array('status'=>'success', 'message'=>'Image Uploaded'));
                        return $this->sendResponse([], 'File Uploaded.');

                    }
                }
                catch (Exception $e)
                {
                    // Exception occurred. Make error flag true
                    // echo json_encode(array('status'=>'fail1', 'message'=>$e->getMessage()));
                    return $this->sendError(' Error.', $e->getMessage());

                }
            }

        }
        else
        {
            // File parameter is missing
            // echo json_encode(array('status'=>'fail2', 'message'=>'Not Received Any File'.$input));
              return $this->sendResponse([], 'Data Inserted Successful and Not Received Any File');
        }
    }
    
    public function fetch_input_datas(Request $request)
    {
//
        $fetch_input_datas = input_datas::all();

        if (is_null($fetch_input_datas)) {
            return $this->sendError('Data not found.');
        }

        $data= input_datas::select('input_datas.id as input_data_id',
            'input_datas.description','input_datas.status'
            ,'input_image.img_path','restroom_masters.id as rm_id',
            'restroom_masters.r_name as rm_name'
            ,'restroom_masters.location as rm_location'
            ,'parameter_master.id as pm_id','parameter_master.p_name as pm_name',
            'check_masters.id as cm_id','check_masters.c_name as cm_name'
            ,'type_masters.id as type_id','type_masters.t_name as type_name'
            ,'users.username as username','input_datas.co_sl',
            'input_datas.created_by','input_datas.created_at')

            ->leftjoin('input_image','input_image.input_id','=', 'input_datas.id')
            ->join('restroom_masters','restroom_masters.id','=', 'input_datas.r_m_id')
            ->join('parameter_master','parameter_master.id','=', 'input_datas.p_m_id')
            ->join('check_masters','check_masters.id','=','input_datas.c_m_id')
            ->join('type_masters','type_masters.id','=', 'check_masters.type_id')
            ->join('users','users.id','=', 'input_datas.created_by')
            ->where('input_datas.status','open')
            ->orderBy('input_datas.created_at', 'asc')

            ->get();

//        dd($data->img_path);
        if (is_null($data)) {
            return $this->sendError('parameter data not found.');
        }
        return $this->sendResponse($data, 'data retrieved successfully.');

    }

    public function inputdata_update_status(Request $request){
        $input_date=$request->all();
        $validator=Validator::make($input_date,[
            'input_data_id'=>'required',
            'status'=>'required',
            'updated_by'=>'required',
        ]);
      if($validator->fails())
      {
          return $this->sendError('Validation Error',$validator->errors());
      }
      $fetch_input_datas=input_datas::where('id',$input_date['input_data_id'])->first();
      $fetch_input_datas->status=$input_date['status'];
      $fetch_input_datas->updated_by=$input_date['updated_by'];
      $fetch_input_datas->save();

        $fetch_input_datas_status=input_datas_status::create([
            'input_id' =>$input_date['input_data_id'],
            'status' => $input_date['status'],
            'co_sl' => "1",
            'updated_by' => $input_date['updated_by'],
//            'updated_by' => created_at,
        ]);


      return $this->sendResponse($fetch_input_datas,'Status Updated Successfully.');

}

    public function get_type(Request $request)
    {

        $data = type_master::all();
        if (is_null($data)) {
            return $this->sendError('Please check the data entered.');
        }
        return $this->sendResponse($data, 'data retrieved successfully.');

    }

    public function change_password(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'user_id' => 'required',
            'old_pass' => 'required',
            'new_pass' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $parameters = User::where('id', $input['user_id'])
//                     ->where('password', $input['old_pass'])
            ->first();
//        dd($parameters);

        if (is_null($parameters)) {
            return $this->sendError('Please check the data entred.');
        }
        $hash_password=$parameters->password;
//        dd($input['new_pass'],$hash_password);
//dd(Hash::check($input['old_pass'], $hash_password));
        $check=Hash::check($input['old_pass'],$hash_password);
        if($check==true)
        {
            $change_pass = User::where('id', $input['user_id'])
                ->first();

            $change_pass->password = bcrypt($input['new_pass']);
            $change_pass->save();
//            dd($change_pass);
            return $this->sendResponse($change_pass, 'Password Updated successfully.');
        }
        else{
            return $this->sendError('Password Incorrect..');
        }

    }

    public function fetch_all_role_details(Request $request)
    {
//
        $fetch_roles = role_masters::all();

        if (is_null($fetch_roles)) {
            return $this->sendError('Data not found.');
        }

        return $this->sendResponse($fetch_roles, 'data retrieved successfully.');

    }

    public function fetch_all_users_details(Request $request)
    {
//
//        $fetch_roles = User::all();

        $fetch_roles = User::select('users.id as user_id','users.username', 'role_masters.role_name as role_name',
            'role_masters.id as role_id','users.login_id','users.created_by','users.created_at',
        'users.password', 'users.email', 'users.status','users.mobile_no',
            'users.updated_by','users.updated_at')
            ->where('users.status', 1)
            ->join('role_masters', 'role_masters.id', '=', 'users.role_id')
            ->get();

        if (is_null($fetch_roles)) {
            return $this->sendError('Data not found.');
        }

        return $this->sendResponse($fetch_roles, 'data retrieved successfully.');

    }

    public function add_user(Request $request)
    {
//        dd('hello');
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'role_id' => 'required',
            'mobile_no' => 'required|digits:10|unique:users',
            'created_by'=>'required',
        ]);
//dd($validator);
        $errors = $validator->errors();
//        if ($validator->fails()) {
//            return $this->sendError('Validation Error.', $validator->errors());
//        }
//        dd($errors->first('mobile_no'));

        if ($errors->first('email')) {
            return $this->sendError_msg([], $errors->first('email'));

        }
        if ($errors->first('mobile_no')) {
            return $this->sendError_msg([], $errors->first('mobile_no'));

        }
        if ($errors->first('username')||$errors->first('role_id')||$errors->first('created_by')) {
            return $this->sendError_msg([], 'Please enter valid data.');

        }

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

        $insert_latest_login1=users_passwords::create([
            'user_id' => $input_data->id,
            'password'=>$input_data->password,
            'created_by' => $input_data->id,
        ]);

        return $this->sendResponse($success, 'User added successfully.');

    }
    
     public function edit_user(Request $request)
    {
        $input_data = $request->all();
//        dd($input_data);
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'username' => 'required',
            'email' => 'email',
            'role_id' => 'required',
            'mobile_no' => 'digits:10',
            'updated_by'=>'required',
        ]);
//dd($validator);
        $errors = $validator->errors();

//        dd($errors->first('mobile_no'));
        if ($errors->first('user_id')) {
            return $this->sendError_msg([], $errors->first('user_id'));

        }
        if ($errors->first('email')) {
            return $this->sendError_msg([], $errors->first('email'));

        }
        if ($errors->first('mobile_no')) {
            return $this->sendError_msg([], $errors->first('mobile_no'));

        }
        if ($errors->first('username')||$errors->first('role_id')||$errors->first('updated_by')) {
            return $this->sendError_msg([], 'Please enter valid data.');

        }

        $change_pass = User::where('id', $input_data['user_id'])
            ->first();
if (is_null($change_pass)) {
            return $this->sendError('Data not found.');
        }

// dd($input_data);
// dd($change_pass);
        $change_pass->username=$input_data['username'];
        
        $change_pass->email=$input_data['email'];
        $change_pass->role_id=$input_data['role_id'];
        $change_pass->mobile_no=$input_data['mobile_no'];
        $change_pass->co_sl="1";
        $change_pass->status=1;
        $change_pass->login_id=rand(11111, 99999);
        $change_pass->updated_by=$input_data['updated_by'];
         $change_pass->updated_at=time();
        $change_pass->save();


//        $success['token'] =  $input_data->createToken('MyApp')->accessToken;
        $success['username'] =  $change_pass->username;
        $success['id'] =  $change_pass->id;
        $success['role_id'] =  $change_pass->role_id;
        $success['login_id'] =  $change_pass->login_id;
        $success['password'] =  $change_pass->password;
        $success['email'] =  $change_pass->email;
        $success['mobile_no'] =  $change_pass->mobile_no;
        $success['status'] =  $change_pass->status;
        $success['updated_by'] =  $change_pass->updated_by;

        return $this->sendResponse($success, 'User Details Edited successfully.');

    }
    
    public function delete_user(Request $request)
    {
//
        $input_data=$request->all();

        $validator = Validator::make($input_data, [
            'user_id'=>'required',
            'updated_by'=>'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $delete_users = User::find($input_data['user_id']);
//        dd($delete_users);
        if (is_null($delete_users)) {
            return $this->sendError('Data not found.');
        }

        $delete_users->status=0;
        $delete_users->updated_by=$input_data['updated_by'];
        $delete_users->updated_at=time();
        $delete_users->save();
//        $post->fill($request->input())->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }

//     public function fetch_report_datas(Request $request)
//     {
// //
//         $datas = $request->all();
        
        
//      if (!is_null($request->start_date) && is_null($request->end_date)) {
//             return $this->sendError('Please enter both start date and end date.');
//         }
//         if (is_null($request->start_date) && !is_null($request->end_date)) {
//             return $this->sendError('Please enter both start date and end date.');
//         }

//         // if (is_null($datas)) {
//         //     return $this->sendError('Data not found.');
//         // }
        
//         //        when all 3 are given
//         if(($datas['type'])&&($datas['status'])&&($datas['start_date'])&&($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('type_masters.id',$datas['type'])
//                 ->where('input_datas.status',$datas['status'])
//                 ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//       if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when all 3 are not specified
//         if(is_null($datas['type'])&&is_null($datas['status'])&&is_null($datas['start_date'])&&is_null($datas['end_date'])
//         ) {
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when only type is given
//         if(($datas['type'])&&is_null($datas['status'])&&is_null($datas['start_date'])&&is_null($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('type_masters.id',$datas['type'])
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when only status given
//         if(is_null($datas['type'])&&($datas['status'])&&is_null($datas['start_date'])&&is_null($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('input_datas.status',$datas['status'])
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when only date is given
//         if(is_null($datas['type'])&&is_null($datas['status'])&&($datas['start_date'])&&($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when type and status is given
//         if(($datas['type'])&&($datas['status'])&&is_null($datas['start_date'])&&is_null($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('type_masters.id',$datas['type'])
//                 ->where('input_datas.status',$datas['status'])
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

// //        dd($data[]);
//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }

//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when type and date is given
//         if(($datas['type'])&&is_null($datas['status'])&&($datas['start_date'])&&($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('type_masters.id',$datas['type'])
//                 ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

//             if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }

//         //        when status and date is given
//         if(is_null($datas['type'])&&($datas['status'])&&($datas['start_date'])&&($datas['end_date'])){
//             $data = input_datas::select('input_datas.id as input_data_id',
//                 'input_datas.description', 'input_datas.status'
//                 , 'input_image.img_path'
//                 , 'restroom_masters.id as rm_id',
//                 'restroom_masters.r_name as rm_name'
//                 , 'restroom_masters.location as rm_location'
//                 , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
//                 'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
//                 , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
//                 , 'users.username as username', 'input_datas.co_sl',
//                 'input_datas.created_by', 'input_datas.created_at')
//                 ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
//                 ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
//                 ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
//                 ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
//                 ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
//                 ->join('users', 'users.id', '=', 'input_datas.created_by')
//                 ->where('input_datas.status',$datas['status'])
//                 ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
//                 ->orderBy('input_datas.created_at', 'desc')
//                 ->get();

// if ($data->isEmpty()) {
//                 return $this->sendError('Data not found.');
//             }
//             return $this->sendResponse($data, 'data retrieved successfully.');
//         }


//     }

    public function fetch_report_datas(Request $request)
    {
//
        $datas = $request->all();


        if (!is_null($request->start_date) && is_null($request->end_date)) {
            return $this->sendError('Please enter both start date and end date.');
        }
        if (is_null($request->start_date) && !is_null($request->end_date)) {
            return $this->sendError('Please enter both start date and end date.');
        }
        // $start_date = Carbon::parse($request->input('start_date'));
        // $end_date = Carbon::parse($request->input('end_date'));

        // if (is_null($datas)) {
        //     return $this->sendError('Data not found.');
        // }
        
        
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        //        when all 3 are given
        if(($datas['type'])&&($datas['status'])&&($start_date)&&($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('type_masters.id',$datas['type'])
                ->where('input_datas.status',$datas['status'])
//                ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
                ->whereDate('input_datas.created_at', '>=', $start_date)
                ->whereDate('input_datas.created_at', '<=', $end_date)
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

      if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when all 3 are not specified
        if(is_null($datas['type'])&&is_null($datas['status'])&&is_null($start_date)&&is_null($end_date)
        ) {
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when only type is given
        if(($datas['type'])&&is_null($datas['status'])&&is_null($start_date)&&is_null($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('type_masters.id',$datas['type'])
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when only status given
        if(is_null($datas['type'])&&($datas['status'])&&is_null($start_date)&&is_null($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('input_datas.status',$datas['status'])
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when only date is given
        if(is_null($datas['type'])&&is_null($datas['status'])&&($start_date)&&($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
//                ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
                ->whereDate('input_datas.created_at', '>=', $start_date)
                ->whereDate('input_datas.created_at', '<=', $end_date)
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when type and status is given
        if(($datas['type'])&&($datas['status'])&&is_null($start_date)&&is_null($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('type_masters.id',$datas['type'])
                ->where('input_datas.status',$datas['status'])
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

//        dd($data[]);
            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }

            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when type and date is given
        if(($datas['type'])&&is_null($datas['status'])&&($start_date)&&($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('type_masters.id',$datas['type'])
//                ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
                ->whereDate('input_datas.created_at', '>=', $start_date)
                ->whereDate('input_datas.created_at', '<=', $end_date)
                ->orderBy('input_datas.created_at', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }

        //        when status and date is given
        if(is_null($datas['type'])&&($datas['status'])&&($start_date)&&($end_date)){
            $data = input_datas::select('input_datas.id as input_data_id',
                'input_datas.description', 'input_datas.status'
                , 'input_image.img_path'
                , 'restroom_masters.id as rm_id',
                'restroom_masters.r_name as rm_name'
                , 'restroom_masters.location as rm_location'
                , 'parameter_master.id as pm_id', 'parameter_master.p_name as pm_name',
                'check_masters.id as cm_id', 'check_masters.c_name as cm_name'
                , 'type_masters.id as type_id', 'type_masters.t_name as type_name'
                , 'users.username as username', 'input_datas.co_sl',
                'input_datas.created_by', 'input_datas.created_at')
                ->leftjoin('input_image', 'input_image.input_id', '=', 'input_datas.id')
                ->join('restroom_masters', 'restroom_masters.id', '=', 'input_datas.r_m_id')
                ->join('parameter_master', 'parameter_master.id', '=', 'input_datas.p_m_id')
                ->join('check_masters', 'check_masters.id', '=', 'input_datas.c_m_id')
                ->join('type_masters', 'type_masters.id', '=', 'check_masters.type_id')
                ->join('users', 'users.id', '=', 'input_datas.created_by')
                ->where('input_datas.status',$datas['status'])
//                ->whereBetween('input_datas.created_at',array($datas['start_date'],$datas['end_date']))
                ->whereDate('input_datas.created_at', '>=', $start_date)
                ->whereDate('input_datas.created_at', '<=', $end_date)
                ->orderBy('input_datas.created_at', 'asc')
                ->get();

if ($data->isEmpty()) {
                return $this->sendError('Data not found.');
            }
            return $this->sendResponse($data, 'data retrieved successfully.');
        }


    }

  public function latest_active(Request $request)
    {

        $user = $request->all();
//dd($user);
                $updated_date= date('Y-m-d h:i:s');
                $insert_latest_login1=users_details::create([
                    'user_id' => $user['user_id'],
                    'lastest_login'=>$updated_date,
                    'created_by' => $user['user_id'],
                ]);

            $insert_password=users_passwords::where('user_id',$user['user_id'])->first();

            $date1=$insert_password->updated_at;
            $now = Carbon::now();
            $diff = $date1->diffInDays($now);
           if($diff >='90')
            {
                return $this->sendResponse_active('Please Update your Password.');

            }

            return $this->sendResponse_active(' Data Inserted successful.');
        }

}