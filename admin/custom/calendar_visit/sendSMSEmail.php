<?php

/**
 * this sendSMSEmail file to control the notification for Calendar module.
 */

function SendSMS($To,$Message){
    try {
        $Debug = 0;
        define(API_KEY, 'ec3a7e46');
        define(API_SECRET, '9gaZrfpD9Qi7Xyfs');
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

        if ($Debug == 1) {
            echo "<hr>You sent $SentNumber Messages <br>";
            echo "You sent Messages To: $NumberTo<br>";
            echo "Messages Status: $StatusMessage<br>";
            echo "Messages Error: $Error<br>";
            echo "Messages Network: $Network<br>";
            echo "Your Balance for $MessageID: $Balance  Price: $Price<br>";
        }
    } catch (Exception $e) {
        return;
    }
}



function SendEmail($FromEmail="info@ilivein.xyz",$FromName="Info - ILivein.xyz",$ToEmail,$ToName,$Subject,$Message){
include_once ('../class.phpmailer.php');

    $mail = new PHPMailer;
    $mail->setFrom($FromEmail, $FromName);
    $mail->addAddress($ToEmail, $ToName);
    $mail->Subject = $Subject;
    $mail->isHTML(true);
    $mail->Mailer = "sentmail"; //mail
    //$mail->IsSMTP();                                      // Set mailer to use SMTP
    //$mail->Host = 'smtp.ilivein.xyz';                 // Specify main and backup server
    //$mail->Port = 25;                                    // Set the SMTP port
    //$mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'info@ilivein.xyz';                // SMTP username
    $mail->Password = 'Baz8$B77';                         // SMTP password
    //$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

    $mail->Body = $Message;
    if(!$mail->send()) {
        echo 'Email was not sent.';
        echo 'Email error: ' . $mail->ErrorInfo;
    } else {
        echo 'Email has been sent.';
    }
}


?>