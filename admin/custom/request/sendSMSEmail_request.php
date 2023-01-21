<?php
/**
 * This sendSMSEmail file to control the notification for Request module
*/

function SendSMS($To,$Message){
    
    $To = '15149989986'; // For Testing Daniel
    $Debug=0;
    define(API_KEY, 'd95c8736');
    define(API_SECRET, 'da2b1a087829ad44');
    $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
            [
                'api_key' => API_KEY,
                'api_secret' => API_SECRET,
                'to' => $To,
                'from' => '12048190542',
                'text' => $Message
            ]
        );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    //var_dump($response);
    $SentNumber = $response["message-count"];
    $NumberTo = $response['messages']['0']['to'];
    $StatusMessage = $response['messages']['0']['status'];
    $Error = $response['messages']['0']['error-text'];
    $Network = $response['messages']['0']['network'];
    $MessageID = $response['messages']['0']['message-id'];
    $Balance = $response['messages']['0']['remaining-balance'];
    $Price = $response['messages']['0']['message-price'];

    if ($Debug==1) {
        echo "<hr>You sent $SentNumber Messages <br>";
        echo "You sent Messages To: $NumberTo<br>";
        echo "Messages Status: $StatusMessage<br>";
        echo "Messages Error: $Error<br>";
        echo "Messages Network: $Network<br>";
        echo "Your Balance for $MessageID: $Balance  Price: $Price<br>";
    }
}



function SendEmail($FromEmail="info@mgmgmt.ca",$FromName="Info - spgmanagement.com",$ToEmail,$ToName,$Subject,$Message){

    $mail = new PHPMailer;
    $mail->setFrom($FromEmail, $FromName);
    $mail->addAddress($ToEmail, $ToName);
    $mail->addBCC("L5149989986@qq.com", "Frank");

    $mail->Subject = $Subject;
    $mail->isHTML(true);
    $mail->Mailer = "mail";  //sentmail
    //$mail->IsSMTP();                                      // Set mailer to use SMTP
    //$mail->Host = 'smtp.spgmanagement.com';                 // Specify main and backup server
    //$mail->Port = 25;                                    // Set the SMTP port
    //$mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'info@mgmgmt.ca';                // SMTP username
    $mail->Password = 'Baz8$B77';                         // SMTP password
    //$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

    $mail->Body = $Message;
    if(!$mail->send()) {
        echo 'Email was not sent.';
        echo 'Email error: ' . $mail->ErrorInfo;
    }
}