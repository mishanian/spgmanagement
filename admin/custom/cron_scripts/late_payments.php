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
include($file);
// require_once("../../pdo/dbconfig.php");
include_once($path . 'Class.Company.php');
$DB_company = new Company($DB_con);
include_once($path . "Class.LeasePayment.php");
$DB_ls_payment  = new LeasePayment($DB_con);
include_once($path . 'Class.Calendar.php');
$DB_calendar  = new Calendar($DB_con);
// echo "file=$file";
// Get all leases which are not inactive, cancelled,terminated
$activeLeases = "SELECT * FROM lease_infos where lease_status_id not in (3,4,5,6) AND start_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
$activeLeasesStmt = $DB_con->prepare($activeLeases);
$activeLeasesStmt->execute();
$paymentsUpdatedCount = 0;

if ($activeLeasesStmt->rowCount() > 0) {
    foreach ($activeLeasesStmt->fetchAll(PDO::FETCH_ASSOC) as $lease) {
        $latePaymentGap = intval($DB_company->getLatePaymentGap($lease["company_id"]));
        $latePaymentType = intval($DB_company->getLatePaymentType($lease["company_id"]));
        $latePaymentAmt = $DB_company->getLatePaymentAmt($lease["company_id"]);
        $latePaymentTypeShow = $latePaymentType == 1 ? "$" : "%";
        $dateToday = date("Y-m-d");

        foreach (($leasePayments = $DB_ls_payment->getLeasePaymentsByLeaseId($lease["id"])) as $payment) {
            if (intval($payment["payment_status_id"]) != 1 || intval($payment["invoice_type_id"]) == 3 || intval($payment["payment_type_id"]) == 2) {
                continue;
            }

            // Check if the payment due date is passed by and if its 5 days from the due date : modify the payment data for the month
            $paymentLateDueDate = date_format(date_add(date_create($payment['due_date']), date_interval_create_from_date_string("$latePaymentGap day")), "Y-m-d");

            // Check if there is a late payment record already for the month - else add - dont add duplicates of the late payment record for the lease for the month
            $lateAmountCheck = $DB_ls_payment->checkIfLateAmountAlreadyApplied($lease["id"]); // this is the count of the late payment records for the current month

            if (strtotime('now') >= strtotime($paymentLateDueDate)) {

                /* if the outstanding amount for the lease payment is 0 ; do not add the late payment amount :
            	* Scenario : When a full lease amount discount is applied - the outstanding will be 0
            	*/
                if ($payment["outstanding"] <= 0) {
                    continue;
                }

                if ($lateAmountCheck["late_amount"] > 0) {
                    // this is when there is already a late amount assigned to the lease payment record for the month
                    continue;
                }

                $data = array();
                $data["lp_id"] = $payment["id"];
                $data["lease_amount"] = $latePaymentAmt;
                $data["comments"] = "Payment due date has passed and a late fee of $latePaymentAmt $latePaymentTypeShow will be charged.";
                if ($DB_ls_payment->updateLateAmount($data) > 0) {
                    echo  $payment["id"] . " late amount is updated. <hr>";
                }
            }
        }
    }
}

//if($paymentsUpdatedCount > 0){
//    echo "$paymentsUpdatedCount Lease Payments have modified";
//}