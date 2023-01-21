<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("sendSMSEmail.php");
$debugText = MySendEmail("info@mgmgmt.ca", "Info - spgmanagement.com", "mishanian@gmail.com", "Mehran", "test message bcc", "JJJJ BCC message");
//SendSMS("15149296960","Hi, this  is from server"); //+15149296960
echo "Done<hr>Debug=$debugText";