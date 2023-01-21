<?php

class Email_reader
{

	// imap server connection
	public $conn;

	// inbox storage and inbox message count
	public $inbox;
	private $msg_cnt;

	// email login credentials
	private $server = 'mail.spgmanagement.com';
	private $user   = 'info@mgmgmt.ca';
	private $pass   = '+^%(K@(9ZKU8';
	private $port   = 143; // adjust according to server settings

	// connect to the server and get the inbox emails
	function __construct()
	{
		$this->connect();
		$this->inbox();
	}

	// close the server connection
	function close()
	{
		$this->inbox = array();
		$this->msg_cnt = 0;

		imap_close($this->conn);
	}

	// open the server connection
	// the imap_open function parameters will need to be changed for the particular server
	// these are laid out to connect to a Dreamhost IMAP server
	function connect()
	{
		$this->conn = imap_open('{' . $this->server . '/notls}', $this->user, $this->pass);
	}

	// move the message to a new folder
	function move($msg_index, $folder = 'INBOX.Processed')
	{
		// move on server
		imap_mail_move($this->conn, $msg_index, $folder);
		imap_expunge($this->conn);

		// re-read the inbox
		$this->inbox();
	}

	function delete($msg_index)
	{
		imap_delete($this->conn, $msg_index);
		imap_expunge($this->conn);
	}

	// get a specific message (1 = first email, 2 = second email, etc.)
	function get($msg_index = NULL)
	{
		if (count($this->inbox) <= 0) {
			return array();
		} elseif (!is_null($msg_index) && isset($this->inbox[$msg_index])) {
			return $this->inbox[$msg_index];
		}

		return $this->inbox[0];
	}

	// read the inbox
	function inbox()
	{
		$this->msg_cnt = imap_num_msg($this->conn);

		$in = array();
		for ($i = 1; $i <= $this->msg_cnt; $i++) {
			$in[] = array(
				'index'     => $i,
				'header'    => imap_headerinfo($this->conn, $i),
				'body'      => imap_body($this->conn, $i),
				'structure' => imap_fetchstructure($this->conn, $i)
			);
		}

		$this->inbox = $in;
	}
}

?>
<?
$patternEmail = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
$NotDeliveredArray = array();
$emails = new Email_reader();
$inbox = $emails->inbox;
//var_dump($inbox);
foreach ($inbox as $email) {
	$body = $email['body'];
	$errorEmail = "";
	$errorSubject = "";
	$errorDate = "";
	$emailIndex = $email['index'];
	$strpos = strpos($body, "The following address(es) failed:\r");
	if ($strpos) {
		$subBodyEmail = substr($body, $strpos, 200);
		preg_match_all($patternEmail, $subBodyEmail, $matchesEmail);
		$errorEmail = $matchesEmail[0][0];

		$strposSubject = strpos($body, "Subject: ");
		if ($strposSubject) {
			$subBodySubject = substr($body, $strposSubject + 9, 250);
			preg_match_all("/.*/i", $subBodySubject, $matchesSubject);
			// $errorSubject=rtrim($matchesSubject[0][0]," ");
			$errorSubject = substr($matchesSubject[0][0], 0, -1);
		}
		$strposDate = strpos($body, "Date: ");
		if ($strposDate) {
			$subBodyDate = substr($body, $strposDate + 6, 250);
			preg_match_all("/.*/i", $subBodyDate, $matchesDate);
			$errorDate = date('Y-m-d H:i:s', strtotime($matchesDate[0][0]));
		}

		$error = "No Deliver to: " . $errorEmail . "<br>ErrorSubject: $errorSubject+++";
		$ErrorArray = array("emailIndex" => $emailIndex, "email" => $errorEmail, "subject" => $errorSubject, "emailDate" => $errorDate,);
		array_push($NotDeliveredArray, $ErrorArray);
	} else {
		$error = "";
	}
	if (!empty($error)) {
		echo $error . "<br>\n";
	}

	//  foreach ($email as $emailDetail) {
	$emailDetail = $email['header'];

	//die(var_dump($emailDetail));
	$fromaddress = $emailDetail->fromaddress;
	$toaddress = $emailDetail->to;
	$email_date = $emailDetail->date;
	$toaddress = $toaddress[0]->mailbox . "@" . $toaddress[0]->host;
	$subject = $emailDetail->subject;
	if (!empty($email_date)) {
		echo "Date: $email_date<br>\n";
	}
	if (!empty($fromaddress)) {
		echo "From: $fromaddress<br>\n";
	}
	if (!empty($toaddress)) {
		echo "To: $toaddress<br>\n";
	}
	if (!empty($subject)) {
		echo "Subject Email: $subject<br>\n";
	}

	//   }
	echo "<hr>";
	//echo $email['header']."<br>";
}
//var_dump($NotDeliveredArray);

?>
<?php
include '../../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
for ($i = 0; $i < count($NotDeliveredArray); $i++) {
	$email = $NotDeliveredArray[$i]['email'];
	$subject = $NotDeliveredArray[$i]['subject'];
	$emailIndex = $NotDeliveredArray[$i]['emailIndex'];
	$emailDate = $NotDeliveredArray[$i]['emailDate'];

	$sql = "UPDATE history SET history_type_id=5 WHERE history_type_id=1 and email='$email' and subject='$subject'"; //Not Delivered
	echo $sql . "<br>";
	$Crud->query($sql);
	$Crud->execute();
	$sql = "insert into not_delivered (email,subject,email_date) values ('$email','$subject','$emailDate')";
	echo $sql . "<br>";
	$Crud->query($sql);
	$Crud->execute();
	$emails->delete($emailIndex);
}

$emails->close();
?>