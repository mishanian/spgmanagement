<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$today = date("Y-m-d"); // "2023-06-08"; //  // "2023-01-06"; //
$email_body_management = "Dear Management,<br><br>The renewal notice is generated:<br><br>";
$generatedCount = 0;
$sentCount = 0;

$path = "./"; // default /custom if run from cron
if (strpos($_SERVER['REQUEST_URI'], "cron_scripts") !== false) {
    $path = '../';
    echo "<p>run from inside of cron_scripts " . $_SERVER['REQUEST_URI'] . "</p>";
} else {
    $path = './';
    echo "<p>run not from inside of cron_scripts " . $_SERVER['REQUEST_URI'] . "</p>";
}
include_once($path . 'sendSMSEmail.php');
include($path . "tenant_portal/renewal_notice_content.php");
echo "<hr>Start to find relevant emails<br><table>";

//Initialize for Email
$email = 1;
$pdf = 0;
$logo = "";
$is_signed = 0;
$next_length_of_lease = 365;
$terms_en = null;
$terms_fr = null;

if (!empty($_GET['c'])) {
    $company_id = $_GET['c'];
} else {
    $company_id = 9; // SPG
}

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

// find company days
$select_company = "select renewal_notification_day, generate_renewal_notification_records, sign from company_infos where id=$company_id";
$Crud->query($select_company);
$_company = $Crud->resultSingle();
$sign = $_company['sign'];
$generate_renewal_notification_records = $_company['generate_renewal_notification_records'];
$end_date_for_generate_renewal = date('Y-m-d', strtotime($today . ' + ' . $generate_renewal_notification_records . ' days'));
$renewal_notification_day = $_company['renewal_notification_day'];
$end_date_for_sending_renewal = date('Y-m-d', strtotime($today . ' + ' . $renewal_notification_day . ' days'));
$end_date_for_sending_renewal_second_try = date('Y-m-d', strtotime($today . ' + ' . $renewal_notification_day - 2 . ' days'));
$end_date_for_sending_renewal_third_try = date('Y-m-d', strtotime($today . ' + ' . $renewal_notification_day - 4 . ' days'));
$renewal_letter_date = date("Y-m-d");
$renewal_notice_date = null;
$last_day_renewal = null;

// echo "today=$today end_date_for_renewal=$end_date_for_renewal end_date_for_generate_renewal=$end_date_for_generate_renewal<br>";
// echo "renewal_notification_day=$renewal_notification_day generate_renewal_notification_records=$generate_renewal_notification_records<br>";
// echo "end_date_for_sending_renewal=$end_date_for_sending_renewal end_date_for_sending_renewal_second_try=$end_date_for_sending_renewal_second_try end_date_for_sending_renewal_third_try=$end_date_for_sending_renewal_third_try<br>";

// Let's generate lease_renewal_notifications 175 days + 60 days before end date
$sql_select = "SELECT LI.id as lease_id, LI.tenant_ids, LI.lease_status_id, LI.employee_id, LI.company_id,
building_name, unit_number, `start_date`, LI.end_date,
BI.address, BI.city, BI.building_id, AI.apartment_id, PV.name as province_name , BI.postal_code ,
AI.monthly_price as monthly_amount
FROM lease_infos LI
LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
LEFT JOIN provinces PV ON BI.province_id=PV.id
WHERE lease_status_id IN(1,7) AND
LI.end_date ='$end_date_for_generate_renewal'
AND $where_building";
// echo "sql_select=$sql_select<br>";
$Crud->query($sql_select);
$rows = $Crud->resultSet();
foreach ($rows as $row) {
    $end_date = "";
    $fields = ['lease_id', "end_date", "building_name", "unit_number", "address", "city", "province_name", "postal_code", "monthly_amount", "lease_status_id", "company_id", "employee_id", 'building_id', 'apartment_id'];
    foreach ($fields as $field) {
        $$field = $row[$field];
    }
    $tenant_ids = explode(",", $row['tenant_ids']);
    foreach ($tenant_ids as $tenant_id) {
        $sql_tenant = "select full_name as tenant_name from tenant_infos where tenant_id=$tenant_id";
        // echo "sql_tenant=$sql_tenant<br>";
        $Crud->query($sql_tenant);
        $tenant_name = $Crud->resultField();


        $params = array(
            "lease_id" => $lease_id, "tenant_id" => $tenant_id,
            "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "next_length_of_lease" => $next_length_of_lease, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
            "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
            "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date,
            "terms_en" => $terms_en, "terms_fr" => $terms_fr
        );
        // print_r($params);
        // $text = render_renewal($params);
        unset($params['lease_status_id']); //  lease_status_id should get from lease_infos not lease_renewal_notice (to have current lease status )
        unset($params['email']); //  email should get from caller of the renewal_notice_content.php
        unset($params['pdf']); //  email should get from caller of the renewal_notice_content.php
        unset($params['renewal_notice_date']);
        unset($params['last_day_renewal']);
        unset($params['terms_en']);
        unset($params['terms_fr']);
        $params['building_id'] = $building_id;
        $params['apartment_id'] = $apartment_id;
        $params['employee_id'] = $employee_id;
        $params['company_id'] = $company_id;
        $sqlInsertInto = "INSERT IGNORE INTO lease_renewal_notice (`" . implode("`, `", array_keys($params)) . "`) VALUES ('" . implode("','", array_values($params)) . "')";
        // echo $sqlInsertInto . "<br>";
        $insertIntoStmt = $DB_con->prepare($sqlInsertInto);
        $insertIntoStmt->execute();
        $email_body_management .= "Building: $building_name, Unit: $unit_number, Tenant: $tenant_name, End Date: $end_date, Lease ID: $lease_id";
        $generatedCount++;
    }
}
if ($generatedCount > 0) {
    $email_body_management .= "Generated on " . date("Y-m-d H:i:s") . "<br>";
    $management_email = "info@mgmgmt.ca";
    $management_name = "SPG Management";
    MySendEmail("info@mgmgmt.ca", "Info - SPG Management", $management_email, $management_name, "Important - $generatedCount Lease Renewal Notice are Generated", $email_body_management, false, "", "");
    echo "$generatedCount Generated and email to $management_email<br>";
}

echo "Generated $generatedCount lease renewal notices for end date $end_date_for_generate_renewal<br><hr><br>";
unset($rows);
unset($row);
unset($params);

/////////////////////////////////////////////////////////////
// start to send email for 175 days before end date


$sqlSend = "SELECT LRN.*, BI.building_name FROM lease_renewal_notice LRN
LEFT JOIN building_infos BI ON LRN.building_id=BI.building_id
where LRN.end_date IN ('$end_date_for_sending_renewal','$end_date_for_sending_renewal_second_try','$end_date_for_sending_renewal_third_try')
AND $where_building";
// echo $sqlSend;
$Crud->query($sqlSend);
$rows = $Crud->resultSet();
foreach ($rows as $row) {
    $fields = [
        'lease_id', "tenant_id", "sign", "logo", "end_date", "next_length_of_lease", "building_name", "unit_number", "address",
        "city", "province_name", "postal_code",    "tenant_name", "last_day_renewal",
        "monthly_amount", "lease_status_id", "is_signed", "empty",
        "terms_en", "terms_fr", 'building_id', 'apartment_id', "employee_id", "company_id",
    ];
    foreach ($fields as $field) {
        $$field = $row[$field];
    }
    // print_r($row);
    $sql_tenant = "select email as tenant_name from tenant_infos where tenant_id=$tenant_id";
    // echo "sql_tenant=$sql_tenant<br>";
    $Crud->query($sql_tenant);
    $tenant_email = $Crud->resultField();

    $params = array(
        "lease_id" => $lease_id, "tenant_id" => $tenant_id,
        "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "next_length_of_lease" => $next_length_of_lease, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
        "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
        "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date,
        "terms_en" => $terms_en, "terms_fr" => $terms_fr
    );
    // print_r($params);
    // die();
    $text = render_renewal($params);
    // echo $text;
    $text .= "<h3><a href='https://spgmanagement.com/admin/custom/tenant_portal/renewal_notice.php?tenant_id=$tenant_id&lease_id=$lease_id' target='_blank'> Click here to sign the lease</a></h3>";
    if ($email == 1) {
        $text .= "<img src='https://spgmanagement.com/admin/custom/email_tracker.php?u=$tenant_id&h=13&id=$lease_id&e=$tenant_email&s=Important-Lease Renewal Notice - $building_name # $unit_number&c=Renewal Notification Email'>";
    }
    // $tenant_email = "mishanian@yahoo.com";
    // echo "<tr><td>Send to $tenant_name</td><td>$tenant_email</td><td>$end_date</td></tr>\n";
    MySendEmail("info@mgmgmt.ca", "Info - SPG Management", $tenant_email, $tenant_name, "Important - Lease Renewal Notice - $building_name # $unit_number", $text, false, "", "");
    $sentCount++;
}

if ($sentCount > 0) {
    $email_body_management .= "Sent on " . date("Y-m-d H:i:s") . "<br>";
    $management_email = "info@mgmgmt.ca";
    $management_name = "SPG Management";
    MySendEmail("info@mgmgmt.ca", "Info - SPG Management", $management_email, $management_name, "Important - $sentCount Lease Renewal Notice Sent", $email_body_management, false, "", "");
    echo "$sentCount Sent and emailed to $management_email<br>";
}

echo "Sent $sentCount lease renewal notices for end date $end_date_for_sending_renewal<br><hr><br>";
