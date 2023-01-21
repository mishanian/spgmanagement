<?
error_reporting(E_ALL);
ini_set('display_errors', 1);
require("Class.Imap.php");
$host = 'mail.spgmanagement.com';
$user = 'adv@spgmanagement.com';
$pass = 'XuW6jBhA2';
$port = 143;
$ssl = false;
$folder = 'INBOX';
$mailbox = new Imap($host, $user, $pass, $port, $ssl, $folder);
//var_dump($mailbox);
echo "<pre>";
//print_r($mailbox->getMailboxInfo());
//print_r($mailbox->getCurrentMailboxInfo());
//print_r($mailbox->getMessageIds());
$id = 1;
print_r($mailbox->getMessage($id)['subject']);
echo "</pre>";
?>