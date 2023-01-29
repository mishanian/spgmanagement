<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ignore_user_abort(true);
set_time_limit(0);
include_once('./../../pdo/dbconfig.php');
include_once('./../../pdo/Class.Company.php');
$DB_company = new Company($DB_con);
$row = $DB_company->getCompanyInfo($_SESSION['company_id']);
$company_name = $row['name'];
if (!empty($row['registration_name'])) {
    $company_register = " (" . $row['registration_name'] . ")";
} else {
    $company_register = "";
}
$secret = 'Bulletin';
include_once('./../../pdo/Class.Request.php');
$DB_Request = new Request($DB_con);
$bulletin_info = $DB_Request->getBulletinInfo($_GET['id']);
// var_dump($bulletin_info);

foreach ($bulletin_info[0] as $key => $value) {
    //    echo "<hr>$key=$value<br>";
    $$key = $value;
}
$create_time = date("l, F d Y,  H:i:s", strtotime($create_time));
$today = date("l, F d Y,  H:i:s", strtotime($create_time));
$report_no = "N" . date("ymd") . "R" . $bulletin_id;

$sender_info = $DB_Request->get_user_info($sender_id);
$sender_from = $sender_info['full_name'];
$sender_email = $sender_info['email'];

$body1 = <<<BODY
<table width="100%" align="center"><tr>
<td><span style="color:darkblue; font-family: 'Arial'; font-size: 16px"><b>Email Proof Report</b></span><br>Company </td>
<td align="right"><img src="../../images/logo.png"></td></tr>


<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 2px"></td></tr>

<tr><td colspan="2"><table width="100%">
<tr><td>Subject:</td><td>$message_title</td></tr>
<tr><td>Generate on:</td><td>$today</td></tr>
<tr><td>Report number</td><td>$report_no</td></tr>
</table></td></tr>

<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 2px"></td></tr>

<tr><td colspan="2"><b>Document(s)</b><br>

<table width="100%" align="center" style="border:1px" border="1" cellpadding="20" >
<tr><td>File Name</td><td>Document Integrity</td></tr>
<tr><td>$attachment</td><td>(SHA256 checksum):<br>$checksum</td></tr>
</table></td></tr>

<tr><td colspan="2">&nbsp;</td></tr>

<tr><td colspan="2"><b>Message</b><br>
<table width="100%" align="center" style="border: 1px;" border="1" cellpadding="20">
<tr><td>$message_body</td></tr>
</table>

<tr><td colspan="2">&nbsp;</td></tr>


<tr><td>
<b>Sent from</b><br>
<table width="100%" align="center" cellpadding="20" style="border:1px;" border="1">
<tr style="border:1px">
<td>Name: </td><td><b>$employee_full_name</b></td></tr>
<tr><td>Email </td><td><b>&lt;$employee_email&gt;</b></td>
</tr>
</table>

</td><td>
<b>Sent To</b><br>
<table width="100%" align="center" cellpadding="20" style="border:1px;" border="1">
<tr style="border:1px">
<td>Name: </td><td><b>$tenant_full_name</b></td></tr>
<tr><td>Email </td><td><b>&lt;$tenant_email&gt;</b></td>
</tr>
</table>

</td>
</table>

BODY;


$body2 = <<<BODY
<table width="100%" align="center"><tr>
<td><span style="color:darkblue; font-family: 'Arial'; font-size: 16px"><b>Email Proof Report</b></span><br>Co </td>
<td align="right"><img src="../../images/logo.png"></td></tr>

<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 2px"></td></tr>

<tr><td colspan="2"><table width="100%">
<tr><td>Subject:</td><td>$message_title</td></tr>
<tr><td>Generate on:</td><td>$today</td></tr>
<tr><td>Report number</td><td>$report_no</td></tr>
</table></td></tr>

<tr><td colspan="2"><b>Proof of Transmission</b><br>

<table width="100%" align="center" style="border:1px" border="1" cellpadding="20" >
<tr><td>Date & Time</td><td>$create_time</td></tr>
<tr><td>SMTP</td><td>$smtp_log</td></tr>
<tr><td>Sent From</td><td>$server_ip</td></tr>
</table></td></tr>

</td>
</table>
BODY;


$history_info = $DB_Request->getTrackingEmailInfo($bulletin_id);
$activities_no = count($history_info);
$body3 = <<<BODY
<table width="100%" align="center"><tr>
<td><span style="color:darkblue; font-family: 'Arial'; font-size: 16px"><b>Email Proof Report</b></span><br>Co </td>
<td align="right"><img src="../../images/logo.png"></td></tr>

<tr><td colspan="2"><hr style="color:black;  background-color:black; height: 2px"></td></tr>

<tr><td colspan="2"><table width="100%">
<tr><td>Subject:</td><td>$message_title</td></tr>
<tr><td>Generate on:</td><td>$today</td></tr>
<tr><td>Report number</td><td>$report_no</td></tr>
</table></td></tr>


<tr><td colspan="2"><b>Timeline of Activity ($activities_no)</b><br>

<table width="100%" align="center" style="border:1px" border="1" cellpadding="20" >
BODY;

//$body .="<tr><td>Date & Time</td><td>$create_time</td></tr>";
//var_dump($history_info);
for ($i = 0; $i < $activities_no; $i++) {
    foreach ($history_info[$i] as $key => $value) {
        $$key = $value;
        //        echo "<tr><td>$key = $value";
    }
    if ($history_type_id == 1) {
        $body3 .= "<tr><td>Email sent to $email on $open_datetime<br>\n</td></tr>";
    }
    if ($history_type_id == 3) {
        $body3 .= "<tr><td>Email opened to $email on $open_datetime<br>\n</td></tr>";
    }
    if ($history_type_id == 4) {
        $body3 .= "<tr><td>Attachment $attachment downloaded on $open_datetime<br>\n</td></tr>";
    }
    $body3 .= "</td></tr>";
}



$body3 .= "
</table></td></tr>

</td>
</table>";


require_once 'mpdf/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->SetTitle('notificatoin_report.pdf');
$mpdf->WriteHTML($body1);
$mpdf->AddPage();
$mpdf->WriteHTML($body2);
$mpdf->AddPage();
$mpdf->WriteHTML($body3);
// $mpdf->Output('notificatoin_report.pdf','D');
echo $body1 . $body2 . $body3;
//echo $body2;