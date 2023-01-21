<?php
session_start();
$employeeId = $_SESSION['employee_id'];
$companyId = $_SESSION["company_id"];

/**
 * Handling the Inclusion of database config file for different directories
 */
// $cwd = getcwd();
// $cwdArray = explode("/", $cwd);
// if ($cwdArray[count($cwdArray) - 1] == "report") {
//     include("../../../pdo/dbconfig.php");
// } else {
//     include("../pdo/dbconfig.php");
// }

$path = "../../../pdo";
include_once("$path/dbconfig.php");
include_once("$path/Class.Building.php");
$DB_building = new Building($DB_con);
include_once("$path/Class.LeasePayment.php");
$DB_ls_payment = new LeasePayment($DB_con);
include_once("$path/Class.Employee.php");
$DB_employee = new Employee($DB_con);

include_once("$path/Class.Apt.php");
$DB_apt = new Apt($DB_con);
include_once("$path/Class.Lease.php");
$DB_lease = new Lease($DB_con);
include_once("$path/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);

if (isset($_POST['request']) && $_POST['request'] == 'paymentData') {
    $buildingId = $_POST['b_id'];
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $dateFilter = $_POST['date'];
        echo reportPaymentData($buildingId, $DB_building, $DB_ls_payment, $DB_employee, $dateFilter);
    } else {
        echo reportPaymentData($buildingId, $DB_building, $DB_ls_payment, $DB_employee);
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'invoiceData') {
    $paymentId = $_POST['payId'];
    $paymentDetailsId = $_POST['paymentDetId'];
    echo reportInvoiceData($paymentDetailsId, $paymentId, $DB_building, $DB_ls_payment, $DB_lease, $DB_apt, $DB_tenant);
}

if (isset($_POST['request']) && $_POST['request'] == 'buildingReportData') {
    $buildingId = $_POST['b_id'];
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $dateFilter = $_POST['date'];
        echo buildingReportData($buildingId, $DB_building, $DB_ls_payment, $DB_employee, $DB_lease, $DB_apt, $DB_tenant, $dateFilter);
    } else {
        echo buildingReportData($buildingId, $DB_building, $DB_ls_payment, $DB_employee, $DB_lease, $DB_apt, $DB_tenant);
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'allEftReports') {
    $selectedDate = $_POST['sDate'];
    $previousDate = $_POST['pDate'];
    echo allEftReportData($DB_building, $DB_ls_payment, $DB_employee, $DB_lease, $DB_apt, $DB_tenant, $selectedDate, $previousDate, $companyId, $employeeId);
}

function buildingReportData($buildingId, $DB_building, $DB_ls_payment, $DB_employee, $DB_lease, $DB_apt, $DB_tenant, $dateFilter = "")
{

    $building = $DB_building->getBdInfo($buildingId);

    $date = new DateTime($dateFilter);
    // $date = new DateTime();
    $selectedDay = $date->format('Y-m-d 11:00:00');


    $date->modify('-1 day');
    $previousDay = $date->format('Y-m-d 11:00:00');

    $paymentData = $DB_ls_payment->getPaymentsData($buildingId, $selectedDay, $previousDay);

    $paymentOutput = array();

    if (count($paymentData) < 1) {
        return json_encode(array("data" => false));
    } else {
        // Payment records exist - Get additional details
        foreach ($paymentData as $paymentIndex => $payment) {

            $reportDataPartial = reportInvoiceData($payment['id'], $payment['lease_payment_id'], $DB_building, $DB_ls_payment, $DB_lease, $DB_apt, $DB_tenant, false);

            $reportDataPartial["paidAmt"] = $payment['amount'] + $payment['cf_amount'];
            $reportDataPartial["bName"] = $building['building_name'];
            $reportDataPartial["bAddress"] = $building['address'];

            array_push($paymentOutput, $reportDataPartial);
        }
        return json_encode(array("data" => true, "value" => $paymentOutput));
    }
}

function reportPaymentData($buildingId, $DB_building, $DB_ls_payment, $DB_employee, $dateFilter = "")
{
    $building = $DB_building->getBdInfo($buildingId);

    //    $date = new DateTime('2018-01-06');
    $date = new DateTime($dateFilter);
    $selectedDay = $date->format('Y-m-d 11:00:00');

    $date->modify('-1 day');
    $previousDay = $date->format('Y-m-d 11:00:00');

    $paymentData = $DB_ls_payment->getPaymentsData($buildingId, $selectedDay, $previousDay);

    $paymentOutput = array();

    if (count($paymentData) < 1) {
        return json_encode(array("data" => false));
    } else {
        //       Payment records exist - Get additional details
        foreach ($paymentData as $paymentIndex => $payment) {

            $paymentMethodName = $DB_ls_payment->getPaymentMethod($payment['payment_method_id'])['name'];

            $enteredByName = $DB_employee->getEmployeeName($payment['entry_user_id']);

            $enteredByName = $enteredByName == null ? '-' : $enteredByName;

            $paymentDateToFormat = $payment['payment_date'];

            $formattedPaymentdate = new DateTime($paymentDateToFormat);
            $paymentDateFormatted = $formattedPaymentdate->format('Y-m-d h:m:s');

            array_push($paymentOutput, array(
                "payment_date" => $paymentDateFormatted,
                "bname" => $building['building_name'],
                "method" => $paymentMethodName,
                "enteredBy" => $enteredByName,
                "total" => $payment['amount'] + $payment['cf_amount'],
                "orderID" => $payment['id'],
                "paymentID" => $payment['lease_payment_id']
            ));
        }

        return json_encode(array("data" => true, "value" => $paymentOutput));
    }
}

/**
 *
 * @param type $paymentDetailsId
 * @param type $paymentId
 * @param type $DB_building
 * @param type $DB_ls_payment
 * @param type $DB_employee
 * @return type
 * Invoice Details for the second popup after the Payment reports view
 */
function reportInvoiceData($paymentDetailsId, $paymentId, $DB_building, $DB_ls_payment, $DB_lease, $DB_apt, $DB_tenant, $json = true)
{
    $lease_payment_details = $DB_ls_payment->get_payment_info_by_lease_payment_id($paymentId);

    // Get Lease ID from Lease payment
    $leaseID = $lease_payment_details['lease_id'];

    // Get lease details
    $leaseDetails = $DB_lease->getLeaseInfoByLeaseId($leaseID);

    // Apartment ID from the Lease Details
    $apartment_id = $leaseDetails['apartment_id'];

    // Tenant IDs as a concatenated string
    $tenantIdsString = $leaseDetails['tenant_ids'];

    // Tenant IDs exploded
    $tenantIds = explode(',', $tenantIdsString);

    // Array to hold Tenant Names
    $tenantNames = array();

    foreach ($tenantIds as $index => $tenant) {
        array_push($tenantNames, $DB_tenant->getTenantName($tenant));
    }

    if (count($tenantNames) > 1) {
        $tenantName = implode(",", $tenantNames);
    } else {
        $tenantName = $tenantNames[0];
    }

    $unit_number = $DB_apt->getUnitNumber($apartment_id);

    // Make an array for response output
    $invoiceOutput = array();

    $leasePaymentDueToFormat = $lease_payment_details["due_date"];
    $leasePaymentDueToFormatDate = new DateTime($leasePaymentDueToFormat);
    $leasePaymentDueFormatted = $leasePaymentDueToFormatDate->format('Y-m-d h:m:s');

    $leasePaymentDateToFormat = $lease_payment_details["date_paid"];
    $leasePaymentDateToFormatDate = new DateTime($leasePaymentDateToFormat);
    $leasePaymentDateFormatted = $leasePaymentDateToFormatDate->format('Y-m-d');

    $invoiceOutput["due"] = $leasePaymentDueFormatted;
    $invoiceOutput["lease_amount"] = $lease_payment_details["lease_amount"];
    $invoiceOutput["parking_amount"] = $lease_payment_details["parking_amount"];
    $invoiceOutput["storage_amount"] = $lease_payment_details["storage_amount"];
    $invoiceOutput["total"] = $lease_payment_details["total"];
    $invoiceOutput["unit"] = $unit_number;
    $invoiceOutput["tenant"] = $tenantName;
    $invoiceOutput["tenantIds"] = $tenantIds;
    $invoiceOutput["date_paid"] = $leasePaymentDateFormatted;
    $invoiceOutput["status"] = intval($lease_payment_details["lease_amount"]) > intval($lease_payment_details["total"]) ? "Partially Paid" : "Paid";

    // If return value isn't a JSON
    if ($json == false) {
        return $invoiceOutput;
    }

    // JSON encode the array and return it to calling function
    return json_encode($invoiceOutput);
}

function allEftReportData($DB_building, $DB_ls_payment, $DB_employee, $DB_lease, $DB_apt, $DB_tenant, $selectedDate, $previousDate, $companyId, $employeeId)
{
    $allPayments = $DB_ls_payment->getAllPayments($selectedDate, $previousDate);
    $allEftPayments = array();
    $isEmployeeAdmin = false;

    $employeeData = $DB_employee->getEmployeeInfo($employeeId);
    if ($employeeData["admin_id"] == 1) {
        $isEmployeeAdmin = true;
    }

    if (count($allPayments) < 1) {
        return json_encode(array("data" => false));
    }
    foreach ($allPayments as $index => $payment) {
        $leasePayment = array();
        $leaseInfo = $DB_ls_payment->get_lease_info_by_lease_payment_id($payment['lease_payment_id']);

        if (!$isEmployeeAdmin) {
            if ($leaseInfo["company_id"] != $companyId || $leaseInfo["employee_id"] != $employeeId) {
                continue;
            }
        }

        // If employee is not an ADMIN - try to get only the payments of the company they belongs to.
        if ($leaseInfo["company_id"] != $companyId) {
            continue;
        }

        $leasePayment["buildingId"] = $leaseInfo["building_id"];
        $leasePayment["buildingName"] = $DB_building->getBdName($leaseInfo["building_id"]);
        $leasePayment["unit"] = $leaseInfo["unit"];
        $leasePayment["paymentData"] = $payment;
        array_push($allEftPayments, $leasePayment);
    }
    if (count($allEftPayments) > 0) {
        return json_encode(array("data" => true, "value" => $allEftPayments));
    }
    return json_encode(array("data" => false));
}
