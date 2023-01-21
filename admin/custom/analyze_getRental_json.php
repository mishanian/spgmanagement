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
    $building_ids = $_GET["bid"];
} else {
    $building_ids = 0;
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

$RentalPaymentsArray = $DB_analyze->getRentalPaymentParams($company_id, $building_ids, $apartment_id, $payment_method_id, $payment_type_id, $year_due_date, $month_due_date, $province_id, $size_type_id);

//var_dump($result);
    $TotalAmount = 0;
    $arrayPayment=array();
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


        $arrayPayment[]=array("DueDate"=>$DueDate,"BuildingName"=>$BuildingName,"UnitNumber"=>$UnitNumber,"PaidAmount"=>$PaidAmount,"TenantNames"=>$TenantNames,"PaymentMethod"=>$PaymentMethod,"SizeTypeName"=>$SizeTypeName,"Comments"=>$Comments,"TenantComments"=>$TenantComments);


 }


$BuildingIncomeArray = $DB_analyze->getBuildingsIncome($company_id,$building_ids);
if (count($BuildingIncomeArray)>0) {
    for ($i = 0; $i < count($BuildingIncomeArray); $i++) {
        $PaidAmount = $BuildingIncomeArray[$i]['paid'];
        $building_name = $BuildingIncomeArray[$i]['building_name'];
        $arrayBuildingIncome[] = array("PaidAmount" => $PaidAmount, "building_name" => $building_name);
    }
}else{
    $building_name="-";
    $arrayBuildingIncome[]= array("PaidAmount" => 0, "building_name" => $building_name);
}
$BuildingIncomeMonthlyArray = $DB_analyze->getBuildingsMonthlyIncome($company_id,$building_ids);
foreach ($BuildingIncomeMonthlyArray as $BmA){
            $BnewArr[$BmA["building_id"]][$BmA["month_due_date"]]=array($BmA["paid"],$BmA["building_name"]);
}

    $array['Payment']=$arrayPayment;
    $array['Buildings']=$arrayBuildingIncome;
    $array['Monthly']=$BnewArr;
    $json=  json_encode($array);

//    echo "<pre>";
    echo $json;
//    var_dump($array);

    ?>