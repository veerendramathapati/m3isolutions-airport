<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

    public function sendResponse_restroomdata($result,$id,$r_name,$location,$unique_identifier,$co_sl,$message)
    {
        $response = [
            'success' => true,
            'id'=>$id,
            'r_name'=>$r_name,
            'location'=>$location,
            'unique_identifier'=>$unique_identifier,
            'co_sl'=>$co_sl,
            'parameter'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
       public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
    public function sendError_msg($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
//            'message' => "The email must be a valid email address.",
            'message' => $errorMessages,
        ];


//        if(!empty($errorMessages)){
//            $response['data'] = $errorMessages;
//        }

        return response()->json($response, $code);
    }
    public function sendError_msg_pass($message)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
    public function sendResponse_active($message)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }
}
