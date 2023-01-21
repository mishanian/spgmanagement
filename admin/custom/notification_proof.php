<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
ignore_user_abort(true);
set_time_limit(0);
include_once ('./../../pdo/dbconfig.php');
include_once ('./../../pdo/Class.Company.php');
$DB_company=new Company($DB_con);
$row=$DB_company->getCompanyInfo($_SESSION['company_id']);
$company_name=$row['name'];
if (!empty($row['registration_name'])){ $company_register=" (".$row['registration_name'].")";}else{ $company_register="";}
$secret = 'Bulletin';
include_once ('./../../pdo/Class.Request.php');
$DB_Request=new Request($DB_con);
$bulletin_info=$DB_Request->getBulletinInfo($_GET['id']);
//var_dump($bulletin_info);
include_once "./../../pdo/Class.Template.php";
$template       = new Template();
foreach($bulletin_info[0] as $key=>$value){
//    echo "<hr>$key=$value<br>";
    $$key=$value;
}
$create_time=date("l, F d Y,  H:i:s",strtotime($create_time));

/* Tracker */
$user_tracker_id=$user_id;
$user_tracker_email=$tenant_email;
$history_type_id=3; // Opened
$subject        = $title;
$name=$tenant_full_name;
$body1          = $content;
$body2          = "";
//$bodyFull = $body2."<img border='0' src='https://www.spgmanagement.com/admin/custom/email_tracker.php?u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject' width='1' height='1' alt='I Live In' >";
$button_url     = "https://spgmanagement.com/admin/";
$button_content = "View Notification";
$subtitle       = "From: $company_name $company_register";
$emailTemplate=$template->emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content);

$body=<<<BODY
<table width="100%" align="center"><tr>
<td><span style="color:darkblue; font-family: 'Arial'; font-size: 16px"><b>Email Certify Report</b></span><br>Co </td>
<td align="right"><img src="../../images/logo.png"></td></tr>
<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 10px"></td></tr>

<tr><td colspan="2"><b>$title</b><br>
$create_time</td></tr>
<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 2px"></td></tr>
<tr><td colspan="2">From : <b>$employee_full_name</b> &lt;$employee_email&gt;<br>
To : <b>$tenant_full_name</b> &lt;$tenant_email&gt;</td></tr>

<tr><td colspan="2">


<table width="100%" align="center" style="border: 1px;" border="1" cellpadding="20">
<tr><td><b>Message</b></td></tr>
<tr><td>$emailTemplate</td></tr>
<tr><td><b>Document(s)</b></td></tr>
<tr><td>$attachment</td></tr>
</table>

</td></tr>
</table>

BODY;
require_once 'mpdf/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->SetTitle('notificatoin_proof.pdf');
$mpdf->WriteHTML($body);
//$mpdf->AddPage();
//$mpdf->WriteHTML($body2);
$mpdf->Output('notificatoin_proof.pdf','D');
//echo $body;
?>