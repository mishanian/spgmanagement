<?php
namespace PhpImap;
use \Crud;
require 'vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


set_time_limit(3000);
include '../../pdo/dbconfig.php';

$Crud=new CRUD($DB_con);
 
/* connect to gmail with your credentials */
$hostname = '{mail.spgmanagement.com/notls}';
$username = 'att@spgmanagement.com'; # e.g somebody@gmail.com
$password = 'r^dOwrM?xwcp';
 $path="../files/";
 
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Email: ' . imap_last_error());
 
 
/* get all new emails. If set to 'ALL' instead 
 * of 'NEW' retrieves all the emails, but can be 
 * resource intensive, so the following variable, 
 * $max_emails, puts the limit on the number of emails downloaded.
 * 
 */
$emails = imap_search($inbox,'ALL');
// var_dump($emails);
/* useful only if the above search is set to 'ALL' */
$max_emails = 16;


/* if any emails found, iterate through each email */
if($emails) {
 
    $count = 1;
    $countEmailWithOutAtt=0;
    $countEmailWithAtt=0;
    /* put the newest emails on top */
    rsort($emails);
 
    /* for every email... */
    foreach($emails as $email_number) 
    {
        $countEmailWithOutAtt++;
        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox,$email_number,0);
 
        /* get mail message */
        $message = imap_fetchbody($inbox,$email_number,2);
 
        /* get mail structure */
        $structure = imap_fetchstructure($inbox, $email_number);

//        $body = imap_qprint(imap_body($inbox, $email_number));
        $body= quoted_printable_decode(imap_fetchbody($inbox,$email_number,1.1));
    //    echo $body;
        $header = imap_headerinfo($inbox, $email_number);


        $date=$header->Date;
        $subject=imap_mime_header_decode($header->subject)[0]->text;
        $attachments = array();
       echo "$subject\n";//$date -
        //echo "Attachments:<br>";
        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
 
                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
 
                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
 
                if($attachments[$i]['is_attachment']) 
                {
                    $countEmailWithAtt++;
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
 
                    /* 4 = QUOTED-PRINTABLE encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }
        $filenames=array();
        /* iterate through each attachment and save it */
        foreach($attachments as $attachment)
        {
            if($attachment['is_attachment'] == 1)
            {
                $filename = $attachment['name'];

                if(empty($filename)) $filename = $attachment['filename'];
 
                if(empty($filename)) $filename = time() . ".dat";
                $filename = "email_".date("_His").$filename;
                array_push($filenames,$filename);
 //               echo "$filename<br>\n";
                /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */

                $fp = fopen($path.$filename, "w+");
                fwrite($fp, $attachment['attachment']);
                fclose($fp);
            }
 
        }
        if(!empty(count($filenames))) {
            $filenames=implode("|",$filenames);
            $sql = "insert into attachment_infos (document_category_id, `file`, remarks, keywords, employee_id, company_id, createddatetime) values (0,'$filenames',:remarks,:keywords, 68, 9, '".date("Y-m-d H:i:s")."')";
//            echo $sql . "<br>";
            $Crud->query($sql);
            $Crud->bind('remarks', $subject);
            $Crud->bind('keywords', $body);
            $Crud->execute();
        }


        if($count++ >= $max_emails) break;
        imap_delete($inbox, $email_number);
        imap_expunge($inbox);
//////////////        echo "<hr>";
    }

 
} 
 
/* close the connection */
imap_close($inbox);
 
echo "$countEmailWithOutAtt emails are loaded.";
 
?>