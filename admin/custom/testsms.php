<?php
define(API_KEY,'d95c8736');
define(API_SECRET,'da2b1a087829ad44');
$url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
    [
      'api_key' =>  API_KEY,
      'api_secret' => API_SECRET,
      'to' => '15149296960', //15149296960 15145627588
      'from' => '12048190542',
      'text' => 'Test Message From Mehran by Nexmo'
    ]
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$response = json_decode($response, true);
//var_dump($response);
$SentNumber=$response["message-count"];
$NumberTo=$response['messages']['0']['to'];
$StatusMessage=$response['messages']['0']['status'];
$Error=$response['messages']['0']['error-text'];
$Network=$response['messages']['0']['network'];
$MessageID=$response['messages']['0']['message-id'];
$Balance=$response['messages']['0']['remaining-balance'];
$Price=$response['messages']['0']['message-price'];
echo "<hr>You sent $SentNumber Messages <br>";
echo "You sent Messages To: $NumberTo<br>";
echo "Messages Status: $StatusMessage<br>";
echo "Messages Error: $Error<br>";
echo "Messages Network: $Network<br>";
echo "Your Balance for $MessageID: $Balance  Price: $Price<br>";
?>