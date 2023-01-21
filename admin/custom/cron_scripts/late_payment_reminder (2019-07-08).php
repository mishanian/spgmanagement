<?php
/* * *
 * Late Payments - change the status to Unpaid and add comments
 * Author : Sharan
 * Date : 2018-03-09
 * * */
if (strpos(getcwd(), "cron_scripts") == false) {
	$path = "../../pdo/";
} else {
	$path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

if (strpos(getcwd(), "cron_scripts") == false) {
	include_once("sendSMSEmail.php");
} else {
	include_once("../sendSMSEmail.php");
}

// Get all leases which are not inactive, cancelled,terminated
$activeLeases     = "SELECT * FROM lease_infos where lease_status_id not in (3,4,5,6)";
$activeLeasesStmt = $DB_con->prepare($activeLeases);
$activeLeasesStmt->execute();
$paymentsUpdatedCount = 0;

if ($activeLeasesStmt->rowCount() > 0) {
	foreach ($activeLeasesStmt->fetchAll(PDO::FETCH_ASSOC) as $lease) {
		$latePaymentReminderDay = intval($DB_company->getLatePaymentReminderDays($lease["company_id"]));

		$dateToday              = date("Y-m-d");

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
							// SendEmail("info@spgmanagement.com", "Info - spgmanagement.com", $tenantSingle["email"], $tenantSingle["full_name"], "Payment Due - Reminder", $notification_text);
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
							// SendEmail("info@spgmanagement.com", "Info - spgmanagement.com", $tenantInfo[0]["email"], $tenantInfo[0]["full_name"], "Payment Due - Reminder", $notification_text);
						}
					}
				}
			}

			//			if (strtotime('now') >= strtotime($paymentLateDueDate)) {
			//				if ($payment["latereminder_sent"] != 1) {
			//
			//					// Update the payment reminder notification sent flag
			//					$DB_ls_payment->updateLateReminderSent($payment["id"]);
			//					$tenantInfo = $DB_tenant->getTenantViewByLeaseId($payment["lease_id"]);
			//					if (count($tenantInfo) > 1) {
			//						foreach ($tenantInfo as $tenantSingle) {
			//							$notification_text = "Hello " . $tenantSingle["full_name"] . ", <br><br>";
			//							$notification_text = "You have a lease payment due on " . $payment["due_date"] . " for the Apartment : " . $tenantSingle["building_name"] . " - " . $tenantSingle["unit_number"];
			//							$notification_text .= "<br><br> Lease Amount : " . $payment["lease_amount"];
			//							$notification_text .= "<br><br> Outstanding Amount : " . $payment["outstanding"];
			//							$notification_text .= "<br> <br> Please ignore if already paid.";
			//							$notification_text .= "<br><br> Thanks, <br> IliveIn Team";
			//							SendEmail("info@spgmanagement.com", "Info - spgmanagement.com", $tenantSingle["email"], $tenantSingle["full_name"], "Payment Due - Reminder", $notification_text);
			//						}
			//					}
			//					else {
			//						// Send an email
			//						$notification_text = "Hello " . $tenantInfo[0]["full_name"] . ", <br><br>";
			//						$notification_text = "You have a lease payment due on " . $payment["due_date"] . " for the Apartment : " . $tenantInfo[0]["building_name"] . " - " . $tenantInfo[0]["unit_number"];
			//						$notification_text .= "<br><br> Lease Amount : " . $payment["lease_amount"];
			//						$notification_text .= "<br><br> Outstanding Amount : " . $payment["outstanding"];
			//						$notification_text .= "<br> <br> Please ignore if already paid.";
			//						$notification_text .= "<br><br> Thanks, <br> IliveIn Team";
			//						SendEmail("info@spgmanagement.com", "Info - spgmanagement.com", $tenantInfo[0]["email"], $tenantInfo[0]["full_name"], "Payment Due - Reminder", $notification_text);
			//					}
			//				}
			//			}

		}
	}
}