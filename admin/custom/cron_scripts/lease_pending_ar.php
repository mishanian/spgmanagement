<?php

/* * *
 * Lease Pending Renewal to change to Active - Auto Renewed
 * * */

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
include_once($path . '../../pdo/dbconfig.php');
include_once($path . '../../pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once($path . '../../pdo/Class.Template.php');
$template  = new Template();

$dateToday = date("Y-m-d");

// Query for getting all the pending renewal leases 
$SelectSql = "select LI.id,LI.company_id, LI.employee_id, LI.renewal_notice_date, LI.move_out_date, LI.tenant_ids, building_name, unit_number, monthly_price 
FROM lease_infos LI 
LEFT JOIN company_infos CI ON LI.company_id=CI.id
LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
where lease_status_id = 1";
$statement = $DB_con->prepare($SelectSql);
$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);



$leaseIdListAR = array();

foreach ($result as $lease) {
    $building_name = $lease['building_name'];
    $unit_number = $lease['unit_number'];
    $renewal_notice_date = $lease['renewal_notice_date'];
    $company_id = $lease['company_id'];
    $employee_id = $lease['employee_id'];
    $monthly_price = $lease['monthly_price'];
    $from_date = date_format(date_add(date_create($lease['move_out_date']), date_interval_create_from_date_string("1 day")), "Y-m-d");
    $to_date = date_format(date_add(date_create($lease['move_out_date']), date_interval_create_from_date_string("12 month")), "Y-m-d");
    if (isset($lease["renewal_notice_date"])) {
        $renewalGapDay = $DB_company->getRenewalGapDay($lease['company_id']);

        $autoRenewalDate = date_format(date_add(date_create($lease['renewal_notice_date']), date_interval_create_from_date_string("$renewalGapDay day")), "Y-m-d");
        echo "<p>Find leases before renewal gap day " . $renewalGapDay . " -> autoRenewalDate=$autoRenewalDate</p>";
        $SelectSqlTenant = "select full_name, email from tenant_infos where tenant_id in (" . $lease['tenant_ids'] . ")";
        $statement = $DB_con->prepare($SelectSqlTenant);
        $statement->execute();
        $tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tenants as $rowTenant) {
            // die(var_dump($rowTenant));
            $tenant_name = $rowTenant['full_name'];
            $tenant_email = $rowTenant['email'];
            // echo "tenant name=" . $tenant['full_name'] . "<br>";
            // echo "tenant email=" . $tenant['email'] . "<br>";
            //             $emailBody = "<p>Dear " . $tenant['full_name'] . ",<br>
            // Your dwelling lease has been automatically renewed.<br></p>
            // <p><b>Important Reminder:</b></p>
            // <p>You need to come to office to renew your electric access card.Your card will expire if you don't renew.
            // You need to provide a copy of dwelling insurance renewal with your lease renewal before the new lease term.
            // To make an appointment for card renewal, please email to: <a href='mailto:admin@mgmgmt.ca'>admin@mgmgmt.ca</a></p>
            // ";
            $emailBody = "<p>This is to confirm that your lease has been auto renewed. You received the renewal notice on <b>$renewal_notice_date</b>. 
            30 days has passed and we did not receive any response from you. Therefore your lease is considered as auto renewed.</p>

            <p>Your new lease term is from $ $from_date  to $to_date . New rent is $ $monthly_price.</p>
            
            <p>PLEASE MAKE SURE TO RENEW YOUR KEYS BEFORE THE END OF CURRENT LEASE TERM. Failing to do so, you will not be able to open your apartment when the new term starts.</p>
            
            <p>Thank you.</p>
            Administration<br>MG Real Estate Management<br>
";
            /* Email */
            $subject        = "Your lease has been automatically renewed";
            $name = $tenant_name;
            $body1          = $emailBody;
            $body2          = "";
            //$bodyFull = $body2."<img border='0' src='https://www.spgmanagement.com/admin/custom/email_tracker.php?u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject' width='1' height='1' alt='I Live In' >";
            $button_url     = "https://spgmanagement.com/admin/";
            $button_content = "Login";
            $subtitle       = " ";
            $text = $template->emailTemplate($subject, $subtitle, $name, $body1, $body2, $button_url, $button_content, $company_id, $employee_id);
            // echo "<hr>$text<hr>";
            MySendEmail("info@mgmgmt.ca", "Info - SPG Management", $tenant_email, $tenant_name, "Important - Your lease has been automatically renewed - $building_name # $unit_number", $text, false, "", "");
        }

        if (strtotime('now') >= strtotime($autoRenewalDate)) {
            array_push($leaseIdListAR, $lease["id"]);
        }
    }
}

if (count($leaseIdListAR) > 0) {
    $leaseIdImplode = implode(",", $leaseIdListAR);
    // Query to update the lease status to Auto Renewed if the notice period has passed by 30 days
    $SelectSql = "update lease_infos set lease_status_id = 8 where id in ($leaseIdImplode)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    echo $statement->rowCount() . " leases updated to Active - Auto Renewal.";
} else {
    echo "No lease found eligible to update to status : Active - Auto Renewal.";
}