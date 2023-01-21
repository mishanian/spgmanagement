<?php
session_start();

include_once("../../pdo/dbconfig.php");
include_once("../../pdo/Class.Analyze.php");

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
if (!empty($_GET['oshid'])) {
    $online_shopping_id = $_GET["oshid"];
} else {
    $online_shopping_id = 0;
}
if (!empty($_GET['vid'])) {
    $vendor_id = $_GET["vid"];
} else {
    $vendor_id = 0;
}
if (!empty($_GET['rtid'])) {
    $request_type = $_GET["rtid"];
} else {
    $request_type = 0;
}
if (!empty($_GET['vtid'])) {
    $vendor_type_id = $_GET["vtid"];
} else {
    $vendor_type_id = 0;
}
if (!empty($_GET['vid'])) {
    $vendor_id = $_GET["vid"];
} else {
    $vendor_id = 0;
}
if (!empty($_GET['reqid'])) {
    $request_type_id = $_GET["reqid"];
} else {
    $request_type_id = 0;
}

$ExpenseArray = $DB_analyze->getExpenseDetailParams($company_id, $building_id, $apartment_id, $online_shopping_id, $vendor_id, $vendor_type_id, $request_type_id);
?>
<table class="table table-bordered" id="body_payment">
    <thead>
    <tr>
        <th class="text_clr">Date</th>
        <th class="text_clr">Job Type</th>
        <th class="text_clr">Price</th>
        <th class="text_clr">Description</th>
        <th class="text_clr">Vendor</th>
        <th class="text_clr">VendorType</th>
        <th class="text_clr">Store</th>
        <th class="text_clr">Property</th>
        <th class="text_clr">Location</th>


    </tr>
    </thead>
    <tbody>
    <?
  //  die(var_dump($ExpenseArray));
    $TotalAmount = 0;
    for ($i = 0; $i < count($ExpenseArray); $i++) {
        $BillDate = $ExpenseArray[$i]['bill_date'];
        $description = $ExpenseArray[$i]['memo'];
        $request_id = $ExpenseArray[$i]['request_id'];
        if (!empty($ExpenseArray[$i]['building_id'])){$building_name = $DB_analyze->getBdName($ExpenseArray[$i]['building_id']);}else{$building_name = "";}
        if (!empty($ExpenseArray[$i]['apartment_id'])){$UnitNumber = $DB_analyze->getUnitNumber($ExpenseArray[$i]['apartment_id']);
        $apartment_infos = $DB_analyze->getApartmentInfo($ExpenseArray[$i]['apartment_id']);
           $size_type_id = $apartment_infos[0]['size_type_id'];
            $SizeTypeName = $DB_analyze->getSizeTypeName($size_type_id);
        }else{$UnitNumber="-";$SizeTypeName = "";}



        $grand_total = $ExpenseArray[$i]['grand_total'];
        $total = $ExpenseArray[$i]['total'];
        $Store = $DB_analyze->getOnlineShipping($request_id);

        $vendor_id = $ExpenseArray[$i]['vendor_id'];
        $vendor_infos = $DB_analyze->getVendorInfos($vendor_id);
        $vendor_name = $vendor_infos[0]['company_name'];
        $vendor_type_name = $DB_analyze->getVendorTypeName($vendor_infos[0]['vendor_type_id']);


        $request_type_id = $DB_analyze->getRequestInfos($request_id);
        if (!empty($request_type_id)){$request_type_name = $DB_analyze->getRequestTypeName($request_type_id);}else{$request_type_name="";}

        $TotalAmount = $TotalAmount + $total;
        
        ?>
        <tr>
            <td><?= $ExpenseArray[$i]['bill_date'] ?></td>
            <td><?= $request_type_name ?></td>
            <td><?= $total ?></td>
            <td><?= $description ?></td>
            <td><?= $vendor_name ?></td>
            <td><?= $vendor_type_name ?></td>
            <td><? if(!empty($export)){echo mb_convert_encoding($Store, 'UTF-16LE', 'UTF-8');}else{echo $Store;} ?></td>
            <td><?= $building_name ?></td>
            <td><?= $UnitNumber ?></td>
        </tr>
    <? } ?>
    </tbody>
</table>
<!--ResultNoPayments=<?= count($ExpenseArray) ?>&ResultTotal=<?= $TotalAmount ?>=-->
