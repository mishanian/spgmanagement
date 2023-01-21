<?php
namespace PhpImap;
use \Crud;
require 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
<style>
    div#wrap {width:860px; margin:20px auto;}
</style>
</head>
<body>
<div id="wrap">
<?php

// Configuration for the Mailbox class
$hoststring = '{mail.spgmanagement.com:143/imap/notls}INBOX';
$username   = 'adv@spgmanagement.com';
$password   = 'XuW6jBhA2';
$attachdir  = '.';

// Construct the $mailbox handle
$mailbox = new Mailbox($hoststring, $username, $password, $attachdir);

// Get INBOX emails after date 2017-01-01
$mailsIds = $mailbox->searchMailbox('SINCE "20170101"');
if(!$mailsIds) exit('Mailbox is empty');

// Show the total number of emails loaded
echo '<p>Number of Loaded  Emails= <b>'.count($mailsIds).'</b></p>';

// Put the latest email on top of listing
rsort($mailsIds);

// Get the last 15 emails only
array_splice($mailsIds, 15);

$Zumper=array(
    "validate"=>"You have received a Zumper lead.",
    "address"=>"The following lead is interested in ([\w\dàâçéèêëîïôûùüÿñæœ .\-\(\)#]*):",
    "building_name"=>"The following lead is interested in ([\w\dàâçéèêëîïôûùüÿñæœ .\-\(\)]*)",
    "unit_number"=>"The following lead is interested in [\w\dàâçéèêëîïôûùüÿñæœ .\-#]*(#\d*):",
    "link"=>"https:\/\/([\w\d\-\.\:\=\/\%]*)>"
    ); //,"email"=>"mailto:","address"=>"The following lead is interested in ","phone"=>"rel=\"noreferrer\">","link"=>"https://","message"=>"padding: 13px\">");
$RentBoard=array(
    "validate"=>"This is an inquiry from a visitor on the www.rentboard.ca web site",
    "address"=>"Property Address: ([\w\dàâçéèêëîïôûùüÿñæœ \,.\-\(\)#]*)",
    "building_name"=>"Property Address: ([\w\dàâçéèêëîïôûùüÿñæœ .\-\(\)#]*)",
    "email"=>"E-mail Address: ([\w\@\.]*)",
    "phone"=>"Telephone: ([\d\-]*)",
    "link"=>"https:\/\/([\w\d\-\.\:\=\/\%\?]*)"
    );
$RentCompass=array(
    "validate"=>"You can find the listing on RentCompass",
    "address"=>"Address: ([\w\dàâçéèêëîïôûùüÿñæœ \,.\-\(\)#]*)",
    "building_name"=>"Address: ([\w\dàâçéèêëîïôûùüÿñæœ .\-\(\)#]*)",
    "email"=>"From: ([\w\@\.]*)",
 //   "rate"=>'Rate: $([\d\.]*)',
    "link"=>"https:\/\/([\w\d\-\.\:\=\/\%\?]*)"
);
$PadMapper=array(
    "validate"=>"You have received a PadMapper lead",
    "address"=>"The following lead is interested in ([\w\dàâçéèêëîïôûùüÿñæœ \,.\-\(\)#]*)",
    "building_name"=>"The following lead is interested in ([\w\dàâçéèêëîïôûùüÿñæœ .\-\(\)#]*)",
    "email"=>'\(([a-z0-9_\.\-]+\@[a-z0-9\-]+\.+[a-z0-9]{2,4})\)',
    //   "rate"=>'Rate: $([\d\.]*)',
    "link"=>"https:\/\/([\w\d\-\.\:\=\/\%\?]*)"
);
$Patterns=array("Zumper"=>$Zumper, "RentBoard"=>$RentBoard,"RentCompass"=>$RentCompass,"PadMapper"=>$PadMapper);
$EmailArray=array();
// Loop through emails one by one
foreach($mailsIds as $num) {
    $head = $mailbox->getMailHeader($num);
    $subject = $mailbox->getMailHeader($num)->subject;
    $fromaddress = $head->fromName;
    $email_date=$head->date;
    $email = $head->toString;

    $errorEmail="";
    $errorSubject="";
    $errorDate="";
    $markAsSeen = false;
    $mail = $mailbox->getMail($num, $markAsSeen);
    if ($mail->textHtml)
        $body= $mail->textHtml;
    else
        $body= $mail->textPlain;

    $bodyplain=$mail->textPlain;
    $message=$body;
    $rate=0;
    $link="";
    $unit_type="";
    $phone="";
    $building_name="";
    $unit_number="";
    $address="";
    if (!empty($subject)) {
        echo "<hr>Checking Email <br>From Address:<b>$fromaddress</b> on $email_date<br> - Subject: <b>$subject</b><br><hr>\n";

    }


    foreach ($Patterns as $BrokerName=>$Pattern) {
        $SubjectCheck = strpos($subject, $BrokerName);
        if ($SubjectCheck!==false){
            echo "Found <b>$BrokerName</b> in  ---$subject---<br>\n";
 //           echo "Body: <hr><hr>".$bodyplain."<hr><hr>";
        }

        $source=$BrokerName;

        if ($SubjectCheck!==false && strpos($bodyplain, $Pattern['validate'][0])!==false ){
            echo "Now check the keywords in ".strlen($bodyplain)."\n\n\n<hr><hr>$bodyplain<hr><hr>\n\n";
            foreach($Pattern as $key=>$value){
                preg_match("/".$value."/",$bodyplain,$foundTexts);
//print_r($foundTexts);
                if (isset($foundTexts[1])){
                    $foundText=$foundTexts[1];
                }elseif(isset($foundTexts[0])){
                    $foundText=$foundTexts[0];
                }else {
                    $foundText = "";
                }
                echo ("$key=<b>".$foundText."</b><br>");
                $foundText_key=trim($foundText);
                $$key=trim($foundText_key,"#");
            }

            $EmailObj = array("address" => $address, "building_name"=>$building_name, "unit_number"=>$unit_number, "email" => $email, "message" => $message,"rate" => $rate,"link" => $link, "unit_type"=>$unit_type, "phone"=>$phone, "source"=>$source, "email_date"=>$email_date);
      //      die(print_r($EmailObj));
            array_push($EmailArray, $EmailObj);
            echo "<hr>";


        } else {
            $error = "";
        }
        if (!empty($error)) {
            echo $error . "<br>\n";
        }
    }
    // Load eventual attachment into attachments directory
//    $mail->getAttachments();

}
?>
<?

//die(var_dump($EmailArray));
include '../../../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
for ($i = 0; $i < count($EmailArray); $i++) {
    $address = $EmailArray[$i]['address'];
    $email = $EmailArray[$i]['email'];
    $message = $EmailArray[$i]['message'];
    $rate = $EmailArray[$i]['rate'];
    $link = $EmailArray[$i]['link'];
    $source = $EmailArray[$i]['source'];
    $unit_type = $EmailArray[$i]['unit_type'];
    $phone = $EmailArray[$i]['phone'];
    $unit_number = $EmailArray[$i]['unit_number'];
    $building_name = $EmailArray[$i]['building_name'];
    $email_date=$EmailArray[$i]['email_date'];

if(!empty($building_name)){
    $sql="select building_id, employee_id, company_id from building_infos where address='$building_name' or building_name='$building_name'";
//   die($sql);
    $Crud->query($sql);
 //   echo (var_dump($Crud->resultSingle()));
    $row=$Crud->resultSingle();

    //list($building_id,$employee_id, $company_id);
    if(!empty($row['building_id'])){$building_id=$row['building_id'];}else{$building_id='NULL';}
    if(!empty($row['employee_id'])){$employee_id=$row['employee_id'];}else{$employee_id=54;}
    if(!empty($row['company_id'])){$company_id=$row['company_id'];}else{$company_id=9;}
}else{
    $building_id='NULL';
    $employee_id=54;
    $company_id=9;
}
    if(!empty($unit_number)){
        $sql="select apartment_id from apartment_infos where unit_number='$unit_number'";
        $Crud->query($sql);
        $apartment_id=$Crud->resultField();
        if(empty($apartment_id)){$apartment_id='NULL';}
    }else{
        $apartment_id='NULL';
    }

//die($building_id."-".$apartment_id);
    $sql = "insert into potential_tenant_infos (building_id, apartment_id, address,email, comment,rate, link, unit_type, mobile_number, source,  created_dt, employee_id, company_id) values ($building_id,$apartment_id,:address,'$email',:message,'$rate','$link','$unit_type','$phone','$source','" . $email_date . "',$employee_id,$company_id)";
//    echo $sql . "<br>";
//    die();
    $Crud->query($sql);
    $Crud->bind(':message', $message);
    $Crud->bind(':address', $address);
    $Crud->execute();
    $potential_tenant_id=$Crud->lastInsertId();

    $sql = "select name,id from source_infos";
    $Crud->query($sql);
    $row=$Crud->resultGetId();
    //die(print_r($row[$source]));
    $source_id=$row[$source];
//    $employee_id=54;
//    $company_id=9;
    $sql = "insert into adv_tracker (building_id, apartment_id, address, email, message, mobile_number, source_id, potential_tenant_id, created_dt, employee_id, company_id) values ($building_id,$apartment_id,:address,'$email',:message,'$phone','$source_id', '$potential_tenant_id', '$email_date', $employee_id, $company_id)";
    $Crud->query($sql);
    $Crud->bind(':message', $message);
    $Crud->bind(':address', $address);
    $Crud->execute();
}


$mailbox->disconnect();
?>
</div>
</body>
</html>