<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

if (isset($_POST['submit_form'])) {

  $amount = $_POST['amountEnterByUsers'];
  
  $merchantKey = '3cb6a6d7-5ae4-4a01-9bdc-61f27ccefe46';
  $transaction_id= substr(hash('sha256', mt_rand() . microtime()), 0, 20);
  $data = array(
      "merchantId" => "PHONEPEPGTEST43",
      "merchantTransactionId" => $transaction_id,
      "merchantUserId" => "MUID123",
      "amount" => $amount*100,
      "redirectUrl" => "http://localhost/phone-pe/paymentsuccess.php",
      "redirectMode" => "POST",
      "callbackUrl" => "http://localhost/phone-pe/paymentsuccess.php",
      "mobileNumber" => "9825454588",
      "paymentInstrument" => array( "type" => "PAY_PAGE") 
  );
 
// Convert the Payload to JSON and encode as Base64
  $payloadMain = base64_encode(json_encode($data));

  $payload = $payloadMain."/pg/v1/pay".$merchantKey;
  $Checksum = hash('sha256', $payload);
  $Checksum = $Checksum.'###1';

 $requestBody = json_encode(['request' => $payloadMain]);
//X-VERIFY  -	SHA256(base64 encoded payload + "/pg/v1/pay" + salt key) + ### + salt index
$response = $client->request('POST', 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay',
 [
  'body' => $requestBody,
  'headers' => [
    'Content-Type' => 'application/json',
    'X-VERIFY' => $Checksum,
    'accept' => 'application/json',
  ],
]);

  $response=$response->getBody();

  $responseData=json_decode($response,true);
  //echo '<pre>';
   //print_r($responseData);
  // print_r($responseData['data']['merchantId']);
  // print_r($responseData['success']);
  // print_r($responseData['data']['merchantTransactionId']); 
  // print_r($data);
  
   //die();   
 $url = $responseData['data']['instrumentResponse']['redirectInfo']['url'];

 header('Location:'.$url);
}

