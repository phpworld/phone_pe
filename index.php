<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Phone Integration</title>
</head>
<body>
  <br>
  <center><h1>Phone Pay Integration </h1>
  <br>
  <hr>
  <form action="index2.php" method="post">
    <label for="amountEnterByUsers">Enter Amount</label>
    <input type="number" name="amountEnterByUsers" id="amountEnterByUsers">
    <br><hr>
    <button type="submit" name="submit_form">Pay Now</button>
  </form>
</center>

</body>
</html>

<?php

if (isset($_POST['submit_form'])) {

  $amount = $_POST['amountEnterByUsers'];

  $merchantKey = '3cb6a6d7-5ae4-4a01-9bdc-61f27ccefe46';
  $data = array(
      "merchantId" => "PHONEPEPGTEST43",
      "merchantTransactionId" => "MT785058104",
      "merchantUserId" => "MUID123",
      "amount" => $amount*100,
      "redirectUrl" => "paymentsuccess.php",
      "redirectMode" => "POST",
      "callbackUrl" => "paymentsuccess.php",
      "mobileNumber" => "9825454588",
      "paymentInstrument" => array(
          "type" => "PAY_PAGE"
      )
  );
  // Convert the Payload to JSON and encode as Base64
  $payloadMain = base64_encode(json_encode($data));
  $payload = $payloadMain."/pg/v1/pay".$merchantKey;
  $Checksum = hash('sha256', $payload);
  $Checksum = $Checksum.'###1';

//X-VERIFY  -	SHA256(base64 encoded payload + "/pg/v1/pay" + salt key) + ### + salt index

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
      'request' => $payloadMain
    ]),
    CURLOPT_HTTPHEADER => [
      "Content-Type: application/json",
      "X-VERIFY: ".$Checksum,
      "accept: application/json"
    ],
  ]);

   $response = curl_exec($curl);
  
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
      //   echo "cURL Error #:" . $err;
      header('Location: paymentfailed.php?cURLError='.$err);
  } else {
      $responseData = json_decode($response, true);
     
      $url = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
     
     header('Location: '.$url);
  }

}?>