<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../../pdo/dbconfig.php');
include_once('../../../pdo/Class.LeasePayment.php');
$DB_ls_payment = new LeasePayment($DB_con);
include_once("../../../pdo/Class.Employee.php");
$DB_employee = new Employee($DB_con);
include_once("../../../pdo/Class.Building.php");
$DB_building = new Building($DB_con);




$loggedInUserId = 0;

if (isset($_POST["action"]) && $_POST["action"] == "deposit") {

    // AJAX call for deposit slip
    if (isset($_POST["data"])) {
        $depositData = $_POST["data"];
        $updated = 0;
        $loggedInUserId = $_POST["userId"];

        if ($depositData) {
            foreach ($depositData as $pid => $transaction) {
                $updated += intval(depositIterate($DB_ls_payment, $pid, $loggedInUserId, date("Y-m-d h:i:s")));
            }
            echo $updated;
            exit;
        }
    }
}

if (isset($_POST["action"]) && $_POST["action"] == "createDepositRec") {
    // AJAX call for deposit slip report record
    if (isset($_POST["data"])) {
        $depositSlipData = $_POST["data"];
        // $depositSlipDataArr contains the Array details to be stored to the DB
        echo $DB_ls_payment->createDepositRecord(json_encode($depositSlipData)); // Deposit record created for use later in slip report generation
    }
}

// Get All the deposited slips for the Report generation
if (isset($_POST["action"]) && $_POST["action"] == "reportData") {
    $reportDataAll = array();
    $depositRecords = $DB_ls_payment->getAllDepositRecords();

    require_once('return_address.php');

    // var_dump($depositRecords);
    foreach ($depositRecords as $singleDeposit) {
        $singleReport = array();
        $singledepositData = json_decode($singleDeposit["deposit_data"], true);

        $building_names = return_address($singledepositData['chequeData']);
        $singleReport["depositDate"] = date('Y-m-d', strtotime(str_replace('/', '-', $singleDeposit["deposit_date"])));
        $singleReport["paidTotal"] = $singledepositData["paidTotal"];
        $singleReport["link"] = "<a target='_blank' class='reportLink' id='lclrepo' style='color:black;' href='custom/deposit/ds_printpdf.php?fromPid=" . $singleDeposit["id"] . "'>Report</a>";
        $singleReport["recordId"] = $singleDeposit["id"]; // Id of the row in the table - used to fetch the data again in the ds_print.php file
        $singleReport["depositBy"] = $DB_employee->getEmployeeName(intval($singledepositData["userId"]));
        // if (!empty($singledepositData["buildingId"])) {
        //     $singleReport["building"] = $DB_building->getBdName(intval($singledepositData["buildingId"]));
        // } else {
        //     $singleReport["building"] = "";
        // }
        $singleReport["building"] = $building_names;
        // $singleReport["building"] = "aaaaa";

        array_push($reportDataAll, $singleReport);
    }
    echo json_encode(array("data" => $reportDataAll));
}

function depositIterate($DB_ls_payment, $pid, $userId, $date)
{
    return $DB_ls_payment->updateDepositedRecord($pid, $userId, $date);
}