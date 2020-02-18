<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use Response;
use App\User;
use App\Access;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
  public $attributes;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function takeAuthCode(Request $request) {

      $response_type = $request->response_type;
      $client_id = $request->client_id;
      $client_secret = $request->client_secret;

      $user_id = $request->user_id;
      $user_secret = $request->user_secret;

      $device_id = $request->device_id;

      $url = 'https://apiconnect.quantum.az/open-banking/v1.0/authorize';
      $data = array('response_type' => $response_type, 'client_id' => $client_id,
      'client_secret' => $client_secret);

      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/json\r\n",
              "Accept: application/json\r\n",
              'method'  => 'POST',
              'content' =>json_encode($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);

      $json_obj = json_decode($result, true);
      $auth_code = $json_obj["auth_code"];


      $token = $this->takeToken($auth_code, $client_id, $user_id, $user_secret);
      // Session::get('session', $token);
      $access = Access::where('access_code', $token)->select('access_code')->get();
      // echo $xuy[0]->access_code;

      // echo $access;
      $arr = json_decode(json_encode($access), TRUE);
      $device = User::where('deviceID', $device_id)->select('deviceID')->get();

      $salam = $device[0]->deviceID;
      

      if(!empty($arr)) { 

        if(strcmp($access[0]->access_code, $token) == 0) {

        }


        else {

          if($device_id == $salam) {
            Access::create([
          'access_code'=>$token,
          'device_ID' => $device_id,
          ]);
          }
          
          
        }
      }
      else {
        if($device_id == $salam) {
            Access::create([
          'access_code'=>$token,
          'device_ID' => $device_id,
          ]);
          }
      }

      
      // $next = Response::make('hello world');
      // return $next->withCookie(Cookie::make('token', $token, 30));
      return response()->json(['access_token' => $token]);
    }


    public function takeToken($auth_code, $client_id, $user_id, $user_secret) {
      $auth_credentials = array('user_id' => $user_id, 'user_secret' => $user_secret);
      $data = array('grant_type' => 'authorization_code', 'code' => $auth_code,
      'client_id' => $client_id, 'auth_credentials' => $auth_credentials);

      $url = 'https://apiconnect.quantum.az/open-banking/v1.0/token';
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/json\r\n",
              "Accept: application/json\r\n",
              'method'  => 'POST',
              'content' =>json_encode($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);


      $json_obj = json_decode($result, true);
      $access_token = $json_obj["access_token"];
      // $xuy->attributes->add(['access_token' =>$xuy->access_token]);

      return $access_token;
      
    }

}
  ?>
