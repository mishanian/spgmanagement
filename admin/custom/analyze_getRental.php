<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}


include_once("$path/dbconfig.php");
include_once("$path/Class.Analyze.php");
if (empty($_SESSION)){session_start();}
$DB_analyze = new Analyze($DB_con);
$company_id = $_SESSION['company_id'];
if (!empty($_GET['bid'])) {
    $building_id = $_GET["bid"];
} else {
    $building_id = 0;
}
if (!empty($_GET['uid'])) {
    $apartment_id = $_GET["uid"];
} else {
    $apartment_id = 0;
}
if (!empty($_GET['pmid'])) {
    $payment_method_id = $_GET["pmid"];
} else {
    $payment_method_id = 0;
}
if (!empty($_GET['ptid'])) {
    $payment_type_id = $_GET["ptid"];
} else {
    $payment_type_id = 0;
}
if (!empty($_GET['yid'])) {
    $year_due_date = $_GET["yid"];
} else {
    $year_due_date = date("Y");
}
if (!empty($_GET['mid'])) {
    $month_due_date = $_GET["mid"];
    list($year_due_date, $month_due_date) = explode("-", $month_due_date);
} else {
    $month_due_date = date("m");
}
if (!empty($_GET['provid'])) {
    $province_id = $_GET["provid"];
} else {
    $province_id = 0;
}
if (!empty($_GET['sid'])) {
    $size_type_id = $_GET["sid"];
} else {
    $size_type_id = 0;
}

$RentalPaymentsArray = $DB_analyze->getRentalPaymentParams($company_id, $building_id, $apartment_id, $payment_method_id, $payment_type_id, $year_due_date, $month_due_date, $province_id, $size_type_id);

//var_dump($result);?>
<table class="table table-bordered" id="body_payment">
    <thead>
    <tr>
        <th class="text_clr">Timestamp</th>
        <th class="text_clr">Building Name</th>
        <th class="text_clr">Unit</th>
        <th class="text_clr">Paid Amount</th>
        <th class="text_clr">Date Paid</th>
        <th class="text_clr">Payment Author</th>
        <th class="text_clr">Payment Method</th>
        <th class="text_clr">Size Type</th>
        <th class="text_clr">Comments</th>
        <th class="text_clr">Tenant Comments</th>
    </tr>
    </thead>
    <tbody>
    <?
    $TotalAmount = 0;
    for ($i = 0; $i < count($RentalPaymentsArray); $i++) {
        $DueDate = $RentalPaymentsArray[$i]['due_date'];
        $TenantComments = $RentalPaymentsArray[$i]['tenant_comments'];
        $Comments = $RentalPaymentsArray[$i]['comments'];
        $UnitNumber = $RentalPaymentsArray[$i]['unit_number'];
        $PaidAmount = $RentalPaymentsArray[$i]['paid'];
        $SizeTypeName = $DB_analyze->getSizeTypeName($RentalPaymentsArray[$i]['size_type_id']);
        $TotalAmount += $PaidAmount;
        $BuildingName = $DB_analyze->getBdName($RentalPaymentsArray[$i]['building_id']);
        $TenantIds = explode(",", $RentalPaymentsArray[$i]['tenant_ids']);
        $TenantNames = array();
        foreach ($TenantIds as $tenantID) {
            $TenantNames[] = $DB_analyze->getTenantName($tenantID);
        }
        $TenantNames = implode(",", $TenantNames);
        if (!empty($RentalPaymentsArray[$i]['payment_method_id'])) {
            $PaymentMethod = $DB_analyze->getPaymentMethod($RentalPaymentsArray[$i]['payment_method_id']);
        } else {
            $PaymentMethod = "";
        }

        ?>
        <tr>
            <td><?= $DueDate ?></td>
            <td><?= $BuildingName ?></td>
            <td><?= $UnitNumber ?></td>
            <td><?= $PaidAmount ?></td>
            <td><?= $DueDate ?></td>
            <td><?= $TenantNames ?></td>
            <td><?= $PaymentMethod ?></td>
            <td><?= $SizeTypeName ?></td>
            <td><?= $Comments ?></td>
            <td><?= $TenantComments ?></td>
        </tr>
    <? } ?>
    </tbody>
</table>
<!--ResultNoPayments=<?= count($RentalPaymentsArray) ?>&ResultTotal=<?= $TotalAmount ?>=-->
