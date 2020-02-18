<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
// use App\Code;




class UserController extends Controller {

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      //
  }



	public function createUser(Request $request) {
		$version = $request->version;
		$os = $request->os;
		$model = $request->model;
		$deviceID = $request->deviceID;	


		$user = User::create([
			'version'=>$version,
			'OS'=>$os,
			'model'=>$model,
			'deviceID'=>$deviceID,
		]);

		return response()->json(['status'=>'success', 'user'=>$user]);
	}

	public function updateUser(Request $request) {
		$id = $request->id;
		$fullname = $request->fullname;
		$email = $request->email;
		$dateofbirth = $request->dateofbirth;
		$gender = $request->gender;	


		$data = [
			
		];

		if(!is_null($fullname)) {
			
			$data['fullname'] = $fullname;
		}
		if(!is_null($email)) {
			$data['email'] = $email;
			
		}
		if(!is_null($dateofbirth)) {
			$data['dateofbirth'] = $dateofbirth;
			
		}
		if(!is_null($gender)) {
			$data['gender'] = $gender;
			
		}
		
		User::where('id', '=', $id)->update($data);

		return response()->json(['status'=>'success']);

		// return response()
	}

	


	public function getUserProfile(Request $request) {
		$user_id = $request->user_id;

		$data = User::where('id', '=', $user_id)->get();

		return response()->json(['user'=>$data]);
	}
}

?>