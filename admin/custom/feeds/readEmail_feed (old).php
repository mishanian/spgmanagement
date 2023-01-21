<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<?php
use \ForceUTF8\Encoding;
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'ForceUTF8/Encoding.php';
class Email_reader {

	// imap server connection
	public $conn;

	// inbox storage and inbox message count
	public $inbox;
	private $msg_cnt;

	// email login credentials
	private $server = 'mail.spgmanagement.com';
	private $user   = 'adv@spgmanagement.com';
	private $pass   = 'XuW6jBhA2';
	private $port   = 143; // adjust according to server settings

	// connect to the server and get the inbox emails
	function __construct() {
		$this->connect();
		$this->inbox();
	}

	// close the server connection
	function close() {
		$this->inbox = array();
		$this->msg_cnt = 0;

		imap_close($this->conn);
	}

	// open the server connection
	// the imap_open function parameters will need to be changed for the particular server
	// these are laid out to connect to a Dreamhost IMAP server
	function connect() {
		$this->conn = imap_open('{'.$this->server.'/notls}', $this->user, $this->pass);
	}

	// move the message to a new folder
	function move($msg_index, $folder='INBOX.Processed') {
		// move on server
		imap_mail_move($this->conn, $msg_index, $folder);
		imap_expunge($this->conn);

		// re-read the inbox
		$this->inbox();
	}

	function delete($msg_index){
        imap_delete($this->conn, $msg_index);
        imap_expunge($this->conn);
    }

	// get a specific message (1 = first email, 2 = second email, etc.)
	function get($msg_index=NULL) {
		if (count($this->inbox) <= 0) {
			return array();
		}
		elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) {
			return $this->inbox[$msg_index];
		}

		return $this->inbox[0];
	}

	// read the inbox
	function inbox() {
		$this->msg_cnt = imap_num_msg($this->conn);

		$in = array();
		for($i = 1; $i <= $this->msg_cnt; $i++) {

            // Get the message body.
            $body = imap_fetchbody($this->conn, $i, 1.2);
            if (!strlen($body) > 0) {
                $body = imap_fetchbody($this->conn, $i, 1);
            }
            // Get the message body encoding.
            $encoding = $this->getEncodingType($i);
            // Decode body into plaintext (8bit, 7bit, and binary are exempt).
            if ($encoding == 'BASE64') {
                $body = $this->decodeBase64($body);
            }
            elseif ($encoding == 'QUOTED-PRINTABLE') {
                $body = $this->decodeQuotedPrintable($body);
            }
            elseif ($encoding == '8BIT') {
                $body = $this->decode8Bit($body);
            }
            elseif ($encoding == '7BIT') {
                $body = $this->decode7Bit($body);
            }



			$in[] = array(
				'index'     => $i,
				'header'    => imap_headerinfo($this->conn, $i),
				'body'      => imap_body($this->conn, $i),
				'decodedbody' => $body,
				'bodyplain'=>imap_fetchbody($this->conn, $i,1),
                'bodyhtml'=>imap_fetchbody($this->conn, $i,2),
				'structure' => imap_fetchstructure($this->conn, $i),
                'subject'=>imap_headerinfo($this->conn, $i)->Subject
			);
		}

		$this->inbox = $in;
	}
    public function getInbox() {
        $inbox = $this->inbox;
        return $inbox;
    }

}
//$emails= new Email_reader();
//$inbox=$emails->getInbox();
//print_r($inbox[0]['body']);
//die();

?>
<?
// https://hotexamples.com/examples/-/-/imap_utf8/php-imap_utf8-function-examples.html
function mimedecode($text, $encoding = 'UTF-8')
{
	if (function_exists('imap_mime_header_decode') && ($parts = imap_mime_header_decode($text))) {
		$str = '';
		foreach ($parts as $part) {
//			$str .= Charset::transcode($part->text, $part->charset, $encoding);
		}
		$text = $str;
	} elseif ($text[0] == '=' && function_exists('iconv_mime_decode')) {
		$text = iconv_mime_decode($text, 0, $encoding);
	} elseif (!strcasecmp($encoding, 'utf-8') && function_exists('imap_utf8')) {
		$text = imap_utf8($text);
	}
	return $text;
}

$patternEmail = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
$ZumperArray=array("validate"=>"You have received a Zumper lead.","email"=>"mailto:","address"=>"The following lead is interested in ","phone"=>"rel=\"noreferrer\">","link"=>"https://","message"=>"padding: 13px\">");
$RentBoardArray=array("validate"=>"This is an inquiry","email"=>"E-mail Address: ","address"=>"Property Address: ","phone"=>"Telephone: ","link"=>"https://","message"=>"Message:=20\n");
$RentCompass=array("validate"=>"Hello Rental Agent,","email"=>"From: ","address"=>"Address: ","rate"=>'Rate: $',"link"=>"You can find the listing on RentCompass at <a href=3D\"","message"=>"Message:<br>","unit_type"=>"Type: ");
$Patterns=array("Zumper"=>$ZumperArray);//"RentCompass"=>$RentCompass, "www.RentBoard.ca"=>$RentBoardArray); //

$EmailArray=array();
$emails= new Email_reader();
$inbox=$emails->inbox;

foreach ($inbox as $email){


	//$subject=iconv_mime_decode($email['header']->subject,0, "ISO-8859-1");
//	$subject=iconv_mime_decode($email['header']->subject,0,"ISO-8859-1");
	//$subject=base64_decode($email['header']->subject);
    $subject = $email['header']->subject;
/*
    if(stripos($subject, "=?UTF-8?B?") !== false) {
      //  echo "-----------------NOTU--$subject";
        $output = str_ireplace("=?UTF-8?B?", "", $subject);
        $output = str_replace("==?=", "", $output);
        $output = base64_decode($output);
    }else{
        $output = $subject;
    }
    $item['subject'] = $output;
    $subject=$output;
*/
    $subject = imap_utf8($subject);



   // die($subject);
	//$fromaddress = iconv_mime_decode($email['header']->fromaddress,0, "ISO-8859-1");
	$fromaddress = imap_utf8($email['header']->fromaddress);
    $errorEmail="";
    $errorSubject="";
    $errorDate="";
    $emailIndex=$email['index'];
	//$body= quoted_printable_decode( $email['bodyhtml']);
	$body= quoted_printable_decode( $email['bodyhtml']);

  //  $body= utf8_encode(quoted_printable_decode( $email['bodyhtml']));
//    $body = imap_body($emails, $emailIndex);
 //   die($body);
	/*
	$body=str_replace("=\r\n","", $body);
	$ccr=chr(13);
	$body=str_replace($ccr,PHP_EOL, $body);
	$body=str_replace("pstuid=3D","pstuid=", $body);
*/

	if (!empty($subject)) {
		echo "<hr>Checking Email <br>From Address:<b>$fromaddress</b><br> - Subject: <b>$subject</b><br><hr>\n";
		echo "Body: <hr><hr>".$body."<hr><hr>";
		die();
	}





    foreach ($Patterns as $BrokerName=>$Pattern) {
		$SubjectCheck = strpos($subject, $BrokerName);
		if ($SubjectCheck!==false){
			echo "Check for <b>$BrokerName</b> in  ---$subject---<br> Checking in body: ---$body---<br>\n";
		}

	//	$SubjectCheck=substr($fromaddress,0,strlen($BrokerName));
		//$endLine=substr($string, $pos, ( strpos($string, PHP_EOL, $pos) ) - $pos);
		//echo "$SubjectCheck--$BrokerName--$subject <br>";
	//	var_dump($SubjectCheck!==false);
		$source=$BrokerName;
		if ($SubjectCheck!==false && strpos($body, $Pattern['validate'])!==false ){

		//	die(var_dump($source));

			echo "<hr>Find: <b>".$BrokerName."</b> in $subject<hr>\n\n";
			echo "----Body:$body---End of Body---<br>\n\n";
//			$a=strpos($body, "Quebec")+strlen("Quebec");
//			$b=strpos($body, 'Date', $a);
//$c=substr($body, $a, $b-$a+1);

//die(var_dump($a."=".$b."=".$c."=".ord($c)));
			$fromaddress = $emailDetail->fromaddress;
			$toaddress = $emailDetail->to;
			$email_date = $emailDetail->date;
			$toaddress = $toaddress[0]->mailbox . "@" . $toaddress[0]->host;
			$subject = $emailDetail->subject;
			if (!empty($email_date)) {
			//	echo "Date: $email_date<br>\n";
			}
			if (!empty($fromaddress)) {
			//	echo "From: $fromaddress<br>\n";
			}
			if (!empty($toaddress)) {
			//	echo "To: $toaddress<br>\n";
			}
			if (!empty($subject)) {
			//	echo "Subject Email: $subject<br>\n";
			}

			//  	die("Found: ".$subject);
		//	echo "Start Body:---$body---<hr>\n";
		//	echo "Start to disassemble:<br>";
			$EmailObj = array();
			foreach($Pattern as $key=>$value){
				$pos=strpos($body, $value);
				//$endLine=substr($body, $pos+strlen($value), ( strpos($body, PHP_EOL, $pos) ) - $pos);
				$endLinePos=strpos($body, PHP_EOL, $pos);
//				$endLine2=strpos($body, "<br>", $pos);
//				$endLine3=strpos($body, "\r", $pos);
//				$endLine4=strpos($body, "\n", $pos);
//				if($endLine1<$endLine2){$endLinePos=$endLine1;}else{$endLinePos=$endLine2;}
				$endLine=substr($body, $pos+strlen($value), ($endLinePos ) - ($pos+strlen($value)));
			//	$endLine=str_replace("\n","",$endLine);
				$endLine=str_replace("<br>","",$endLine);
				$endLine=str_replace("\n","<br>",$endLine);
				${$key}=$endLine;
				echo "<b>$key</b>=$endLine<br>\n";

			}
			$link=substr($link, strpos($link,">")+1);
			$link=substr($link, 0, strpos($link,"<"));
//			$link_content=file_get_contents($link);
//			$bedrooms=substr($link_content, strpos($link_content,"Bedrooms: <b>"),2);

	//		echo "<b>Link Content from $link<br>\n</b> BD=$bedrooms<br>\n";
			//var_dump($link_content);
//echo "----------Address--------------$address------------------<br>";
			$EmailObj = array("address" => $address, "email" => $email, "message" => $message,"rate" => $rate,"link" => $link, "unit_type"=>$unit_type, "phone"=>$phone, "source="=>$source);
			array_push($EmailArray, $EmailObj);
			echo "<hr>";


		} else {
			$error = "";
		}
		if (!empty($error)) {
			echo $error . "<br>\n";
		}

		//  foreach ($email as $emailDetail) {
		//$emailDetail = $email['header'];

    //die(var_dump($emailDetail));
	}

 //   }

//echo $email['header']."<br>";
//	$emails->delete($emailIndex);
}
//var_dump($EmailArray);

?>
<?php
//die(var_dump($EmailArray));
include '../../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
for ($i=0;$i<count($EmailArray);$i++) {
	$address=$EmailArray[$i]['address'];
    $email=$EmailArray[$i]['email'];
	$message=$EmailArray[$i]['message'];
	$rate=$EmailArray[$i]['rate'];
	$link=$EmailArray[$i]['link'];
	$source=$EmailArray[$i]['source'];
	$unit_type=$EmailArray[$i]['unit_type'];
	$phone=$EmailArray[$i]['phone'];
    $sql = "insert into potential_tenant_infos (address,email, comment,rate, link, unit_type, mobile_number, source,  created_dt, employee_id, company_id) values ('$address','$email','".addslashes($message)."','$rate','$link','$unit_type','$phone','$source','".date("Y-m-d H:i:s")."',54,9)";
    echo $sql . "<br>";
    die();
    $Crud->query($sql);
    $Crud->execute();

}
$emails->imap_errors();
$emails->imap_alerts();
$emails->close();
?>