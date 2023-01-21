<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function SendSMS($To, $Message)
{
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
        }
        $is_prod = $DB_request_ntf->is_prod_or_not_sms($To);
        //		if (!$is_prod) {
        //			return;
        //		}
        //-------------------------------------------------------------
        require_once "sms/vendor/autoload.php";

        $Debug = 1;
        define(API_KEY, 'ec3a7e46');
        define(API_SECRET, '9gaZrfpD9Qi7Xyfs');
        $client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET));
        $message = $client->message()->send([
            'to' => $To,
            'from' => '12046500678', //12046500678
            'text' => $Message
        ]);
        //       echo "Sent message to " . $message['to'] . ". Balance is now " . $message['remaining-balance'] . PHP_EOL;
        //        echo "<Pre>";
        //        var_dump($message);
        $SentNumber    = $message["message-count"];
        $NumberTo      = $message['to'];
        $StatusMessage = $message['status'];
        $Error         = $message['error-text'];
        $Network       = $message['network'];
        $MessageID     = $message['message-id'];
        $Balance       = $message['remaining-balance'];
        $Price         = $message['message-price'];
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


function MySendEmail($FromEmail = "info@mgmgmt.ca", $FromName = "Info - spgmanagement.com", $ToEmail, $ToName, $Subject, $Message, $attachment = false)
{
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

    // if($_SERVER["HTTP_HOST"] == "beaveraittesting.site"){
    // 	$ToEmail = 'sharan.bait@gmail.com';
    // }

    include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/Exception.php');
    include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php');
    include_once(dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src/SMTP.php');

    $mail = new PHPMailer(true);                                // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 4;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'mail.mgmgmt.ca';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'admin@mgmgmt.ca';                 // SMTP username
        $mail->Password = '87^3aLw@';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to 587

        //Recipients
        $mail->setFrom($FromEmail, $FromName);
        $mail->addAddress($ToEmail, $ToName);     // Add a recipient
        //$mail->addReplyTo('info@example.com', 'Information');
        //   $mail->addCC($_SESSION['UserEmail'], $_SESSION['UserFullName']);
        // $mail->addBCC("beaverait@gmail.com", "Beavertest");
        // $mail->addBCC("L5149989986@qq.com", "Frank");

        if ($attachment) {
            $mail->addStringAttachment(file_get_contents($attachment), 'Request.pdf');
        }

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $Subject;
        $mail->Body    = $Message;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $debuglog = '';
        $mail->Debugoutput = function ($str, $level) use (&$debuglog) {
            $debuglog .= $str;
            //   echo $debuglog;
        };
        $mail->send();
        echo "Message sent";
        return $debuglog;
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}

function getUserIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) //if from shared
    {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //if from a proxy
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$debug = MySendEmail("info@mgmgmt.ca", "Info - spgmanagement.com", "mishanian@gmail.com", "Mishan", "Test", "Test");
echo "debug1=" . $debug;