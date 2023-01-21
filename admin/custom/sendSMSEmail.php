<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$isSendSMS = false; // Don't send any sms now

function SendSMS($To, $Message)
{
    global $isSendSMS;
    if ($isSendSMS == true) {
        $whitelist = array(
            '127.0.0.1',
            '::1',
            'localhost'
        );

        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            try {
                //---------------------- intercept --------------------------
                if (!isset($DB_request_ntf)) {
                    if (strpos(getcwd(), "admin") != false) {
                        $path = "..";
                    }
                    if (strpos(getcwd(), "custom") != false) {
                        $path = "../..";
                    }
                    if (strpos(getcwd(), "invoice_receipt") != false || strpos(getcwd(), "request") != false || strpos(getcwd(), "cron_scripts") != false) {
                        $path = "../../..";
                    }
                    include($path . "/pdo/dbconfig.php");
                } else {
                    $is_prod = $DB_request_ntf->is_prod_or_not_sms($To);
                }
                //		if (!$is_prod) {
                //			return;
                //		}
                //-------------------------------------------------------------

                require_once "sms/vendor/autoload.php";

                $Debug = 0;
                define('API_KEY', '3115a86d');  //ec3a7e46
                define('API_SECRET', 'gTUVpaUne0Ewcq7t'); //9gaZrfpD9Qi7Xyfs
                $basic  = new \Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET);
                $client = new \Nexmo\Client($basic);

                //$client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET));
                try {
                    $message = $client->message()->send([
                        'to' => $To,
                        'from' => '15062672590',
                        'text' => $Message
                    ]);
                    $response = $message->getResponseData();

                    if ($response['messages'][0]['status'] == 0) {
                        echo "The message was sent successfully\n";
                    } else {
                        echo "The message failed with status: " . $response['messages'][0]['status'] . "\n";
                    }
                } catch (Exception $e) {
                    echo "The message was not sent. Error: " . $e->getMessage() . "\n";
                }







                //       echo "Sent message to " . $message['to'] . ". Balance is now " . $message['remaining-balance'] . PHP_EOL;
                //        echo "<Pre>";
                //        var_dump($message);

                $SentNumber = $message["message-count"];
                $NumberTo = $message['to'];
                $StatusMessage = $message['status'];
                $Error = $message['error-text'];
                $Network = $message['network'];
                $MessageID = $message['message-id'];
                $Balance = $message['remaining-balance'];
                $Price = $message['message-price'];
                /*
                $Debug = 0;
                define(API_KEY, 'ec3a7e46');
                define(API_SECRET, '9gaZrfpD9Qi7Xyfs');
                $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                        [
                            'api_key' => API_KEY,
                            'api_secret' => API_SECRET,
                            'to' => $To,
                            'from' => '12262412513',
                            'text' => $Message
                        ]
                    );

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                $response      = json_decode($response, true);
                $SentNumber    = $response["message-count"];
                $NumberTo      = $response['messages']['0']['to'];
                $StatusMessage = $response['messages']['0']['status'];
                $Error         = $response['messages']['0']['error-text'];
                $Network       = $response['messages']['0']['network'];
                $MessageID     = $response['messages']['0']['message-id'];
                $Balance       = $response['messages']['0']['remaining-balance'];
                $Price         = $response['messages']['0']['message-price'];
                */

                if ($Debug == 1) {
                    echo "<hr>You sent $SentNumber Messages <br>";
                    echo "You sent Messages To: $NumberTo<br>";
                    echo "Messages Status: $StatusMessage<br>";
                    echo "Messages Error: $Error<br>";
                    echo "Messages Network: $Network<br>";
                    echo "Your Balance for $MessageID: $Balance  Price: $Price<br>";
                    echo "<pre>";
                    var_dump($message);
                    echo "</pre>";
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
/*
function MySendEmail($FromEmail = "info@mgmgmt.ca", $FromName = "Info - SPG Management", $ToEmail, $ToName, $Subject, $Message, $attachment = false) {
    SendEmail($FromEmail, $FromName, $ToEmail, $ToName, $Subject, $Message, $attachment);
}
*/
function MySendEmail($FromEmail, $FromName, $ToEmail, $ToName, $Subject, $Message, $attachment = false, $CC = "", $BCC = "")
{
    if ($FromEmail == "") {
        $FromEmail = "info@mgmgmt.ca";
    }
    if ($FromName == "") {
        $FromName = "Info - SPG Management";
    }
    //   die("Email = $FromEmail, $FromName, $ToEmail, $ToName, $Subject, $Message, $attachment ");
    //  die(var_dump($ToEmail));
    //die(   print_r(func_get_args()));
    $whitelist = array(
        '127.0.0.1',
        '::1',
        'localhost'
    );

    if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {

        //    require './phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
        //    require './phpmailer/src/PHPMailer.php';
        //    require './phpmailer/src/SMTP.php';
        //	include_once('class.phpmailer.php');

        //	//---------------------- intercept --------------------------
        //	if (!isset($DB_request_ntf)) {
        //		if (strpos(getcwd(), "admin") != false) {
        //			$path = "..";
        //		}
        //		if (strpos(getcwd(), "custom") != false) {
        //			$path = "../..";
        //		}
        //		if (strpos(getcwd(), "invoice_receipt") != false || strpos(getcwd(), "request") != false || strpos(getcwd(), "cron_scripts") != false) {
        //			$path = "../../..";
        //		}
        //		include($path . "/pdo/dbconfig.php");
        //	}


        //Load Composer's autoloader
        //      require 'phpmailer/vendor/autoload.php';
        //      die(dirname(__FILE__));



        include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/Exception.php');
        include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php');
        include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/SMTP.php');

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 3;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'mail.mgmgmt.ca'; //'www.spgmanagement.com'; localhost // Specify main and backup SMTP servers
            $mail->SMTPAuth = false;                               // Enable SMTP authentication
            $mail->Username = 'admin@mgmgmt.ca'; // 'info@mgmgmt.ca';                 // SMTP username
            $mail->Password = '87^3aLw@'; // ')}gVs0k#Hzey';                           // SMTP password
            //   $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 25; // 587;                                    // TCP port to connect to 587

            //Recipients
            $mail->setFrom($FromEmail, $FromName);
            if (is_array($ToEmail)) {
                foreach ($ToEmail as $ToEmailSingle) {
                    $mail->addAddress($ToEmailSingle);
                }
            } else {
                $mail->addAddress($ToEmail, $ToName);     // Add a recipient
            }
            if (!empty($CC)) {
                $mail->addCC($CC, $CC);
            }
            if (!empty($BCC)) {
                $mail->addBCC($BCC, $BCC);
            }
            $mail->addBCC("mishanian@gmail.com", "SPGmanagementTest");
            $mail->addBCC("inspect@mgmgmt.ca", "SPGMGMGMT");
            $mail->addReplyTo('admin@mgmgmt.ca', 'MG Management');
            //   $mail->addCC($_SESSION['UserEmail'], $_SESSION['UserFullName']);
            //           $mail->addBCC("mishanian@gmail.com", "SPGManagementTest");

            // $mail->addBCC("L5149989986@qq.com", "Frank");
            //        $email_header = "Disposition-Notification-To: $FromEmail\r\n";
            //        $email_header .= "X-Confirm-Reading-To: $FromEmail\r\n";
            //        $mail->addCustomHeader("Disposition-Notification-To",$FromEmail);
            //        $mail->addCustomHeader("X-Confirm-Reading-To",$FromEmail);
            $mail->AddCustomHeader("X-Confirm-Reading-To: $FromEmail");
            $mail->AddCustomHeader("Return-receipt-to: $FromEmail");
            $mail->ConfirmReadingTo = $FromEmail;
            if ($attachment) {
                $mail->addStringAttachment(file_get_contents($attachment), 'Request.pdf');
            }

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $Subject;
            $mail->Body = $Message;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $debuglog = '';
            $mail->Debugoutput = function ($str, $level) use (&$debuglog) {
                $debuglog .= $str . "<br>";
                //     die($debuglog);
            };

            if (!$mail->send()) {
                $debuglog .=  '\n<br> Message was not sent.\n<br>';
                $debuglog .= ' Mailer error: ' . $mail->ErrorInfo;
            } else {
                $debuglog .=  '\n<br> Message has been sent.\n<br> ';
            }
            // echo "Message sent";
            //         die("debuglog=$debuglog");
            return $debuglog;
        } catch (Exception $e) {
            // echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }
}