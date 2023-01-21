<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$path = "./"; // default /custom if run from cron
if (strpos($_SERVER['REQUEST_URI'], "cron_scripts") !== false) {
    $path = '../';
    echo "<p>run from inside of crone_scripts " . $_SERVER['REQUEST_URI'] . "</p>";
} else {
    $path = './';
    echo "<p>run not from inside of crone_scripts " . $_SERVER['REQUEST_URI'] . "</p>";
}
include_once($path . 'sendSMSEmail.php');
include($path . "tenant_portal/renewal_notice_content.php");
echo "<hr>Start to find relevant emails<br><table>";

//Initialize for Email
$email = 1;
$pdf = 0;
$sign = "mgmgmt_sign.jpg";
$logo = "";
$is_signed = 0;

if (!empty($_GET['b'])) {
    $where_building = " BI.building_id=" . $_GET['b']; // for which building
} else {
    $where_building = "true";
}

if (!empty($_GET['dd'])) {
    $where_datediff = " LI.end_date<='" . $_GET['dd'] . "'"; // expires before this date
} else {
    $where_datediff = "FALSE";
}
// echo "where_datediff=$where_datediff<br>";
include($path . '../../pdo/dbconfig.php');
$Crud = new CRUD($DB_con);



$where = "lease_status_id IN(1,7) AND $where_building
HAVING datediffrenew=CI.renewal_notification_day OR
datediffnotif=CI.send_renewal_notification_after_notice OR
datediffnotif=CI.send_renewal_notification_after_notice2
OR $where_datediff";

// $where = "LI.id=2427";
// $where = "LI.end_date<'2023-04-01' and LI.lease_status_id in (1,2,3,7,8,9,10,11,12)"; //Remove this where on production
$where = "LI.end_date<'2023-07-01' and LI.lease_status_id in (1,2,3,7,8,9,10,11,12)"; //Remove this where on production

$sqlSend = "SELECT LI.id as lease_id, LI.tenant_ids, LI.renewal_notice_date, LI.lease_status_id, LI.employee_id, LI.company_id,
CI.renewal_notification_day, CI.send_renewal_notification_after_notice, CI.renewal_gap_day,
CI.send_renewal_notification_after_notice2, building_name, unit_number, `start_date`, LI.end_date,
BI.address, BI.city, BI.building_id, AI.apartment_id, PV.name as province_name , BI.postal_code ,
AI.monthly_price as monthly_amount,
DATEDIFF(LI.end_date, CURDATE())AS datediffrenew ,
DATEDIFF(renewal_notice_date, CURDATE())AS datediffnotif
FROM lease_infos LI
LEFT JOIN company_infos CI ON LI.company_id=CI.id
LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
LEFT JOIN provinces PV ON BI.province_id=PV.id
WHERE $where
";
// echo $sqlSend . "<br>";
$Crud->query($sqlSend);
$rows = $Crud->resultSet();



foreach ($rows as $row) {
    // echo $row['tenant_ids']."<br>";
    $end_date = "";
    foreach ($row as $key => $value) {
        // echo "$key=$value<br>\n";
        $$key = $value;
    }

    // echo "<hr>end date=" . $end_date . " end_date_onj=";

    foreach (explode(",", $tenant_ids) as $tenant_id) {
        //Find tenant Name
        $sqlTenant = "select full_name as tenant_name, email as tenant_email from tenant_infos where tenant_id=$tenant_id";
        $Crud->query($sqlTenant);
        $rowTenant = $Crud->resultSingle();
        $tenant_name = $rowTenant['tenant_name'];
        $tenant_email = $rowTenant['tenant_email'];
        $parking_amount = 0;
        $parking_number = "";
        $storage_number = "";
        $storage_amount = 0;

        $total_amount = $monthly_amount;
        // $renewal_notice_date = date("Y-m-d"); //renewal_notice_date should not be date of sent, but date that open it.
        $end_date_onj = date_create($end_date);
        // echo $end_date_onj->format('Y-m-d H:i:s'), "<hr>";

        $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
        // echo "end_date=$end_date";
        // echo " end_date_onj=", $end_date_onj->format('Y-m-d H:i:s'), " renewal_notification_day=$renewal_notification_day renewal_letter_date=$renewal_letter_date<br> ";
        $last_day_renewal = date_format(date_add(date_create($renewal_notice_date ? $renewal_notice_date : date("Y-m-d")), date_interval_create_from_date_string("+" . $renewal_gap_day . " days")), "Y-m-d");
        $params = array(
            "lease_id" => $lease_id, "tenant_id" => $tenant_id,
            "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
            "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
            "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date
        );
        // print_r($params);

        $text = render_renewal($params);
        echo "<tr><td>Send to $tenant_name</td><td>$tenant_email</td><td>$end_date</td></tr>\n";
        // echo $text;
        // echo "<hr>";
        $text .= "<h3><a href='https://spgmanagement.com/admin/custom/tenant_portal/renewal_notice.php?tenant_id=$tenant_id&lease_id=$lease_id' target='_blank'> Click here to sign the lease</a></h3>";
        if ($email == 1) {
            $text .= "<img src='https://spgmanagement.com/admin/custom/email_tracker.php?u=$tenant_id&h=13&id=$lease_id&e=$tenant_email&s=Important-Lease Renewal Notice - $building_name # $unit_number&c=Renewal Notification Email'>";
        }
        // MySendEmail("info@mgmgmt.ca", "Info - SPG Management", $tenant_email, $tenant_name, "Important - Lease Renewal Notice - $building_name # $unit_number", $text, false, "", "");

        $params['building_id'] = $building_id;
        $params['apartment_id'] = $apartment_id;
        $params['employee_id'] = $employee_id;
        $params['company_id'] = $company_id;
        $params['tenant_name'] = str_replace("'", "\'", $params['tenant_name']);
        unset($params['lease_status_id']); //  lease_status_id should get from lease_infos not lease_renewal_notice (to have current lease status )
        unset($params['email']); //  email should get from caller of the renewal_notice_content.php
        unset($params['pdf']); //  email should get from caller of the renewal_notice_content.php
        $sqlInsertInto = "INSERT IGNORE INTO lease_renewal_notice (" . implode(", ", array_keys($params)) . ") VALUES ('" . implode("','", array_values($params)) . "')";
        // echo $sqlInsertInto . "<br>";
        $insertIntoStmt = $DB_con->prepare($sqlInsertInto);
        $insertIntoStmt->execute();
        // die($text);
    }
}
echo "</table><p>End of find relevant emails</p><hr>";

// die();

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    //   echo $js_code;
}