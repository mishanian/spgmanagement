<?php
namespace PhpImap;
use \Crud;
require 'vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//error_reporting(E_COMPILE_ERROR);

set_time_limit(3000);
include '../../../pdo/dbconfig.php';

$Crud=new CRUD($DB_con);

// Configuration for the Mailbox class
$hoststring = '{mail.spgmanagement.com:143/imap/notls}INBOX';
$username   = 'att@spgmanagement.com';
$password   = 'r^dOwrM?xwcp';
$attachdir  = '.';
$attachmentsDir="../../files/attachments/";

// Construct the $mailbox handle
$mailbox = new Mailbox($hoststring, $username, $password, $attachdir);
if(isset($_GET['c'])){
    echo($mailbox->countMails());
    die();
}

$mailbox->setExpungeOnDisconnect(true);
$mailbox->setAttachmentsDir($attachmentsDir);
// Get INBOX emails after date 2017-01-01
$mailsIds = $mailbox->searchMailbox('SINCE "20170101"');
if(!$mailsIds) exit('Mailbox is empty');

// Show the total number of emails loaded
echo 'Number of Loaded  Emails= '.count($mailsIds).'';

// Put the latest email on top of listing
rsort($mailsIds);

// Get the last 15 emails only
array_splice($mailsIds, 15);


    $count = 1;
    $countEmailWithOutAtt=0;
    $countEmailWithAtt=0;
    // Loop through emails one by one
    foreach($mailsIds as $mailId) {
        $head = $mailbox->getMailHeader($mailId);
        $subject = $mailbox->getMailHeader($mailId)->subject;
        $subject = str_replace("Fwd: ","", $subject);
        $subject = str_replace("Re: ","", $subject);
        $fromaddress = $head->fromName;
        $email_date=$head->date;
        $email = $head->toString;

        $markAsSeen = false;
        $mail = $mailbox->getMail($mailId, $markAsSeen);
        if ($mail->textHtml)
            $body= $mail->textHtml;
        else
            $body= $mail->textPlain;

        $bodyplain=$mail->textPlain;
        $countEmailWithOutAtt++;

        $attachments = array();
 //      echo "$subject\n";//$date -
        //echo "Attachments:<br>";
        $attachments=$mail->getAttachments();

        $filenames=array();
        /* iterate through each attachment and save it */
        if($mail->hasAttachments()) {
            foreach ($attachments as $attachment) {
 //               echo "<pre>";
 //               print_r($attachment);
 //               echo "</pre>";
                $filename = $attachment->name;
                $fileSystemName=$attachment->filePath;
               // if (empty($filename)) $filename = $attachment['filename'];

        //        if (empty($filename)) $filename = time() . ".dat";
                $filename = "email_" . date("_His") . $filename;
                rename( $fileSystemName, $attachmentsDir.$filename);
                array_push($filenames, $filename);
                //               echo "$filename<br>\n";
                /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */

         //       $fp = fopen($attachmentsDir . $filename, "w+");
         //       fwrite($fp, $attachment->data);
         //       fclose($fp);


            }
            if (!empty(count($filenames))) {
                $body=trim(preg_replace('/\s+/', ' ', str_replace("&nbsp;"," ",strip_tags ($body))));
               // die($body);
                $filenames = implode("|", $filenames);
                $sql = "insert into attachment_infos (document_category_id, `file`, remarks, keywords, employee_id, company_id, createddatetime) values (0,:filenames,:remarks,:keywords, 68, 9, '" . date("Y-m-d H:i:s") . "')";
       //     echo $sql . "<br>";
                $Crud->query($sql);
                $Crud->bind('remarks', $subject);
                $Crud->bind('keywords', $body);
                $Crud->bind('filenames', $filenames);
                $Crud->execute();
            }
        //    $mailbox->saveToDisk();
        }

 //       if($count++ >= $max_emails) break;
        $mailbox->deleteMail($mailId);
//////////////        echo "<hr>";
    }


 
/* close the connection */
//imap_close($mailbox);
$mailbox->disconnect();
//echo "$countEmailWithOutAtt emails are loaded.";
 
?>