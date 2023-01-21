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

$ExpenseArray = $DB_analyze->getExpenseDetailParams($company_id, $building_ids , $apartment_id, $online_shopping_id, $vendor_id, $vendor_type_id, $request_type_id);

    //die(var_dump($ExpenseArray));
    $TotalAmount = 0;
    $arrayExpense=array();
    for ($i = 0; $i < count($ExpenseArray); $i++) {
        $BillDate = $ExpenseArray[$i]['invoice_date'];
        $description = $ExpenseArray[$i]['memo'];
        $request_id = $ExpenseArray[$i]['request_id'];
        if (!empty($ExpenseArray[$i]['building_id'])){$building_name = $DB_analyze->getBdName($ExpenseArray[$i]['building_id']);}else{$building_name = "";}
        if (!empty($ExpenseArray[$i]['apartment_id'])){$UnitNumber = $DB_analyze->getUnitNumber($ExpenseArray[$i]['apartment_id']);
        $apartment_infos = $DB_analyze->getApartmentInfo($ExpenseArray[$i]['apartment_id']);
        if (!empty($apartment_infos[0]['size_type_id'])){   $size_type_id = $apartment_infos[0]['size_type_id'];$SizeTypeName = $DB_analyze->getSizeTypeName($size_type_id);}else{$SizeTypeName="";}

        }else{$UnitNumber="-";$SizeTypeName = "";}

        $amount = $ExpenseArray[$i]['amount'];
       // $total = $ExpenseArray[$i]['total'];
        $paid_amount = round($ExpenseArray[$i]['paid_amount'],2);
if (!empty($request_id)) {
    $Store = $DB_analyze->getOnlineShipping($request_id);
    $request_type_id = $DB_analyze->getRequestInfos($request_id);
    if (!empty($request_type_id)){$request_type_name = $DB_analyze->getRequestTypeName($request_type_id);}else{$request_type_name="";}
}else{
    $Store ="";
    $request_type_name="";
}
        $vendor_id = $ExpenseArray[$i]['vendor_id'];
        $vendor_infos = $DB_analyze->getVendorInfos($vendor_id);

        if (!empty($vendor_infos)) {
            $vendor_name = $vendor_infos[0]['company_name'];
            $vendor_type_name = $DB_analyze->getVendorTypeName($vendor_infos[0]['vendor_type_id']);
        }else{
            $vendor_name = "";
            $vendor_type_name = "";
        }

       // $amount = $ExpenseArray[$i]['amount'];


        $TotalAmount = $TotalAmount + $amount;
        if(!empty($export)){$Store= mb_convert_encoding($Store, 'UTF-16LE', 'UTF-8');}
        $arrayExpense[]=array("invoice_date"=>$ExpenseArray[$i]['invoice_date'],"request_type_name"=>$request_type_name,"amount"=>$amount,"description"=>$description,"vendor_name"=>$vendor_name,"vendor_type_name"=>$vendor_type_name,"Store"=>$Store,"building_name"=>$building_name,"UnitNumber"=>$UnitNumber,"paid_amount"=>$paid_amount);

    }
    $arrayBuildingExpense=array();
    $BuildingExpenseArray = $DB_analyze->getBuildingsExpense($company_id,$building_ids);



    for ($i = 0; $i < count($BuildingExpenseArray); $i++) {
//        $amount = $BuildingExpenseArray[$i]['amount'];
        $amount = $BuildingExpenseArray[$i]['amount'];
        $paid_amount = $BuildingExpenseArray[$i]['paid_amount'];
        if (!empty($BuildingExpenseArray[$i]['building_id'])){$building_name = $DB_analyze->getBdName($BuildingExpenseArray[$i]['building_id']);}else{$building_name = "";}
        $arrayBuildingExpense[]=array("paid_amount"=>$paid_amount,"building_name"=>$building_name);
    }

$BuildingExMonthlyArray = $DB_analyze->getBuildingsMonthlyExpense($company_id,$building_ids);
foreach ($BuildingExMonthlyArray as $BmA){
    $BnewArr[$BmA["building_id"]][$BmA["month_invoice_date"]]=array($BmA["amount"]);
}
    $array['Payment']=$arrayExpense;
    $array['Buildings']=$arrayBuildingExpense;
    $array['Monthly']=$BnewArr;
    $json=  json_encode($array);

  //      echo "<pre>";
  //  var_dump($array);
        echo $json;


?>