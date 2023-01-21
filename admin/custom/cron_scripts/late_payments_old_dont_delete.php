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

// Get all leases which are not inactive, cancelled,terminated 
$activeLeases = "SELECT * FROM lease_infos where lease_status_id not in (3,4,5,6)";
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
            if (intval($payment["payment_status_id"]) != 1) {
                continue;
            }
            // Check if the payment due date is passed by and if its 5 days from the due date : modify the payment data for the month
            $paymentLateDueDate = date_format(date_add(date_create($payment['due_date']), date_interval_create_from_date_string("$latePaymentGap day")), "Y-m-d");

            // Check if there is a late payment record already for the month - else add - dont add duplicates of the late payment record for the lease for the month
            $countOfLatePaymentRecords = $DB_ls_payment->checkIfLatePaymentRecordExists($lease["id"]); // this is the count fo the late payment records for the current month

            if (strtotime('now') >= strtotime($paymentLateDueDate)) {

                if($countOfLatePaymentRecords["count_id"] > 0){
                    // this is when there is already a late payment record for the current month - this is to avoid duplicates being created each month
                    continue;
                }

                // if the late payment amount for the company is less than 1$ - do not create the record even though there is a late payment scenario
                if($latePaymentAmt < 1){
                    continue;
                }

                // Checking if the Due date has passed by.
                $latePaymentDetails["lease_id"] = $lease["id"];
                $latePaymentDetails["due_date"] = $paymentLateDueDate;
                $latePaymentDetails["lease_amount"] = $latePaymentAmt;
                $latePaymentDetails["total"] = $latePaymentAmt;
                $latePaymentDetails["outstanding"] = $latePaymentAmt;
                $latePaymentDetails["comments"] = "Payment due date has passed and a late fee of $latePaymentAmt $latePaymentTypeShow will be charged.";
                $DB_ls_payment->addLatePaymentRecord($latePaymentDetails);
            }
        }
    }
}

//if($paymentsUpdatedCount > 0){
//    echo "$paymentsUpdatedCount Lease Payments have modified";
//}