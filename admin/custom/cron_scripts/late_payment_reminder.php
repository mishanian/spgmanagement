<?php
/* * *
 * Late Payments - change the status to Unpaid and add comments
 * Author : Mehran
 * Date : 2020-12-04
 * * */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (strpos(getcwd(), "cron_scripts") == false) {
	$path = "../../pdo/";
} else {
	$path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
global $DB_con;

include_once($path . 'Class.Company.php');
$DB_company = new Company($DB_con);
include_once($path . "Class.LeasePayment.php");
$DB_ls_payment  = new LeasePayment($DB_con);
include_once($path . 'Class.Tenant.php');
$DB_tenant  = new Tenant($DB_con);

if (strpos(getcwd(), "cron_scripts") == false) {
	include_once("sendSMSEmail.php");
} else {
	include_once("../sendSMSEmail.php");
}
// Get all leases which are not inactive, cancelled,terminated
$activeLeasesSql     = "SELECT * FROM lease_infos where lease_status_id not in (3,4,5,6) and start_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
$activeLeasesStmt = $DB_con->prepare($activeLeasesSql);
$activeLeasesStmt->execute();
$activeLeases = $activeLeasesStmt->fetchAll(PDO::FETCH_ASSOC);
$paymentsUpdatedCount = 0;
$latePaymentReminderDays = $DB_company->getLatePaymentReminderDays($lease["company_id"]);
if ($activeLeasesStmt->rowCount() > 0) {
	foreach ($activeLeases as $lease) {
		$latePaymentReminderDay = intval($latePaymentReminderDays);

		$dateToday              = date("Y-m-d");
		// $dateToday              = "2020-11-27";

		foreach (($leasePayments = $DB_ls_payment->getLeasePaymentsByLeaseId($lease["id"])) as $payment) {
			if (intval($payment["payment_status_id"]) != 1) {
				continue;
			}

			// Check if the payment due date is passed by and if its 3/5 days (Company level setting) from the due date : modify the payment data for the month
			$paymentLateDueDate = date_format(date_add(date_create($payment['due_date']), date_interval_create_from_date_string("$latePaymentReminderDay day")), "Y-m-d");

			$check3daysBeforeDueDate = date('Y-m-d', strtotime('-3 days', strtotime($payment['due_date'])));

			/* If the current date is 3 days before the due date : send a reminder */
			if ((strtotime($check3daysBeforeDueDate) == strtotime(date('Y-m-d'))) ||
				((date('Y-m-d') > $paymentLateDueDate) && ($payment["payment_status_id"] == 1))
			) {
				// Update the payment reminder notification sent flag
				$DB_ls_payment->updateLateReminderSent($payment["id"]);
				$tenantInfo = $DB_tenant->getTenantViewByLeaseId($payment["lease_id"]);

				if ($tenantInfo) {
					if (count($tenantInfo) > 1) {
						foreach ($tenantInfo as $tenantSingle) {
							$notification_text = "Hello " . $tenantSingle["full_name"] . ", <br><br>";
							$notification_text = "You have a lease payment due on " . $payment["due_date"] . " for the Apartment : " . $tenantSingle["building_name"] . " - " . $tenantSingle["unit_number"];
							$notification_text .= "<br><br> Lease Amount : " . $payment["lease_amount"];
							$notification_text .= "<br><br> Late Amount : " . $payment["late_amount"];
							$notification_text .= "<br><br> Outstanding Amount : " . $payment["outstanding"];
							$notification_text .= "<br> <br> Please ignore if already paid.";
							$notification_text .= "<br><br> Thanks, <br> IliveIn Team";
							MySendEmail("info@mgmgmt.ca", "Info - spgmanagement.com", $tenantSingle["email"], $tenantSingle["full_name"], "Payment Due - Reminder", $notification_text);
						}
					} else {
						// Send an email
						if (!empty($tenantInfo) && isset($tenantInfo[0])) {
							$notification_text = "Hello " . $tenantInfo[0]["full_name"] . ", <br><br>";
							$notification_text = "You have a lease payment due on " . $payment["due_date"] . " for the Apartment : " . $tenantInfo[0]["building_name"] . " - " . $tenantInfo[0]["unit_number"];
							$notification_text .= "<br><br> Lease Amount : " . $payment["lease_amount"];
							$notification_text .= "<br><br> Late Amount : " . $payment["late_amount"];
							$notification_text .= "<br><br> Outstanding Amount : " . $payment["outstanding"];
							$notification_text .= "<br> <br> Please ignore if already paid.";
							$notification_text .= "<br><br> Thanks, <br> IliveIn Team";
							MySendEmail("info@mgmgmt.ca", "Info - spgmanagement.com", $tenantInfo[0]["email"], $tenantInfo[0]["full_name"], "Payment Due - Reminder", $notification_text);
						}
					}
				}
			}
		}
	}
}
echo "Late Payment sent<br>";