<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use App\Transaction;
use Illuminate\Support\Facades\Hash;
use App\Code;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Session;
use App\Access;
use App\Payment;
use App\tempTransaction;


use DB;
use DateTime;



class TransactionController extends Controller {

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      //
  }



  public function generateCode(Request $request) {
    
    $generator = "1357902468";
    $access_code = $request->access_code;
    $player_id = $request->player_id;
    
    $access = Access::where('access_code', $access_code)->select('access_code')->get();
    if($access_code == $access[0]->access_code){
      $tempRes = "";
      for ($i = 1; $i <= 6; $i++) {
          $tempRes .= substr($generator, (rand()%(strlen($generator))), 1);
      }

      $transaction = Code::create([
          'generated_code' => $tempRes,
          'api_token'=> $access_code,
          'player_id'=>$player_id,
        ]);
      return response()->json(['code'=>$transaction]);

    }

    return response()->json(['status'=>'token either expired or not valid']);
      
      
    
    // return response()->json(['status'=>'token either expired or not correct']);
    // echo $code;
  }

  public function checkCode(Request $request) { 
    $code = $request->code; 
    // dd($code); 
  if($code == implode(json_decode(Code::where('generated_code', $code)->pluck('generated_code')))) { 
    $timestamp = Code::where('generated_code', $code)->select('created_at')->get();
   if(time() -  strtotime($timestamp[0]->created_at) < 90) {
        return response()->json(['success', '200'],200); 
   }

   return response()->json(['token expired'], 500);



    // $query->whereDate('created_at', '=', Carbon::today()->toDateString());


    // // return $timestamp;
    // $sosi = strtotime($timestamp->created_at);
    // $day = date('D', $timestamp);

    // echo $created_at;

    // echo $timestamp;
    

  // $_SESSION['data'] = "The content should be displayed as alert in the JS file"; 
    } 
  } 

  public function retrieveInfo(Request $request) { 
    
    $merchant_name = "hesab.az"; 
    $name = $request->name; 
    $code = $request->code; 
    $amount = $request->amount; 
    $currency = $request->currency; 
    $transaction_id = $request->transaction_id; 
    $logo_url = $request->logo_url;
    $generated_code = $request->generated_code;

    $varP = Code::where('generated_code', $generated_code)->select('player_id')->get();
    $var = Code::where('generated_code', $generated_code)->select('generated_code')->get();
    $codeFromDb = $var[0]->generated_code;
    $player_id = $varP[0]->player_id;
    // echo $codeFromDb;

    $array = array(
        "name" => $name,
        "code" => $code,
        "amount" => $amount,
        "currency" => $currency,
        "transaction_id" => $transaction_id,
        "logo_url" => $logo_url
    );

    if($generated_code == $codeFromDb) {
       
      $content = array(
      "en" => 'Notification from merchant',
      
      );
    
    $fields = array(
      'app_id' => "5bca7fae-070c-489f-85b2-9f5f2f4acef7",
      'include_player_ids' => array($player_id),
      'data' => $array,
      'contents' => $content
    );
    
    $fields = json_encode($fields);
      print("\nJSON sent:\n");
      print($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return response()->json(['data'=>$response]);
    }





   
    return response()->json(['status'=>'error']);

  }






  public function getCardsInfo(Request $request) {
      $api_token = $request->api_token;
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://apiconnect.quantum.az/open-banking/v1.0/accounts",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "accept: application/json",
          "access_token: $api_token"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      $data = json_decode($response, true);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        return $data;
      }

  }

  public function getTransactionData() {
    $petani = DB::select('select * from temp');

    return response()->json(['data'=>$petani[0]]);
  }


  public function getTransactionHistory(Request $request) {
    $api_token = $request->api_token;

    $var = Code::where('api_token', $api_token)->select('generated_code')->get();

    // echo gettype($var);
    $data = [];
    foreach ($var as $obj) {
      $code = $obj->generated_code;
      $items = DB::select(DB::raw('SELECT * FROM temp WHERE temp.generated_code = '.$code.'  ;'));
      // $items->push($items);
      array_push($data, $items);

      // $tempArray = json_decode($jsonstring, true);
      
    }

    return response()->json(['status'=>$data]);
    // $users = DB::table('temp')->select('select *')->get();
    
    // echo $new;
    // $code = $var[0]->generated_code;

  //   for ($x = 0; $x <= 10; $x++) {
  //   echo "The number is: $x <br>";
  // }


    // return $data;
  }


  public function checkBalance(Request $request) {
    $api_token = $request->api_token;
    $account_id = $request->account_id;
    $amount = $request->amount;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://apiconnect.quantum.az/open-banking/v1.0/accounts/coverage-control",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"amount\":{\"InstdAmt\":\"$amount\",\"Ccy\":\"AZN\"},\"accountId\":\"$account_id\"}",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "access_token: $api_token",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);
      $data = json_decode($response, true);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      return $data;
    }
  }

  public function requestPayment(Request $request) {
    $api_token = $request->api_token;
    $ccy = $request->ccy;
    $amount = $request->amount;
    $transaction_id = $request->transaction_id;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://apiconnect.quantum.az/open-banking/v1.0/payment_request",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"PmtInf\":{\"PmtMtd\":\"TRF\",\"PmtTpInf\":{\"InstrPrty\":\"NORM\"},\"DbtrAcct\":{\"Id\":{\"Iban\":\"AZ70BRES00380194400152601801\"}},\"CdtTrfTx\":[{\"Amt\":{\"InstdAmt\":\"$amount\",\"Ccy\":\"$ccy\"},\"CdtrAcct\":{\"Id\":{\"Iban\":\"AZ86BRES00380194400152601901\"}},\"Purp\":\"Hesabdan hesaba\"}]}}",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "access_token: $api_token",
        "content-type: application/json"
      ),
    ));




    $response = curl_exec($curl);
    $data = json_decode($response, true);
    $err = curl_error($curl);
    foreach ($data as $key => $value) {
    
      if($key == "PaymentId") {
        $payment_id = $value;

      }
    }


    $payment = Payment::create([
      'payment_id'=>$payment_id,
      'transaction_id'=>$transaction_id,
      'api_token'=>$api_token
    ]);

    

    
    // foreach ($data['data'] as $key) {
    //   echo $item['PaymentId'];
    // }
    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      return response()->json(['data'=>$data]);
    }

    


  }



  public function makePayment(Request $request) {
    
    $transaction_id = $request->transaction_id;

    $payment_id = Payment::where('transaction_id', $transaction_id)->select('payment_id')->get();
    $api_token = Payment::where('transaction_id', $transaction_id)->select('api_token')->get();

    
    $dataP = $payment_id[0]->payment_id;
 
    $dataA = $api_token[0]->api_token;
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://apiconnect.quantum.az/open-banking/v1.0/payment-requests/$dataP/report",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "access_token: $dataA"
      ),
    ));

    $response = curl_exec($curl);
    $data = json_decode($response, true);

    $err = curl_error($curl);

    curl_close($curl);

    foreach ($data as $key => $value) {
    
      if($key == "result") {
        $result = $value;
      }
    }
    if($result == "Pending") {
      return response()->json(['status'], 500);
    }
    else if($result == "Accepted") {
      return response()->json(['status'], 200);
    }


  }
}






?>