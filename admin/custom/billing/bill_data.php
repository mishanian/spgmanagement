<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$dbClass = $path . 'Class.Bill.php';
include_once($dbClass);
$DB_bill = new Bill($DB_con);
include_once ($path .'Class.Vendor.php');
$DB_vendor = new Vendor($DB_con);
include_once ($path .'Class.Request.php');
$DB_request = new Request($DB_con);

if (isset($_GET['project_id'])) {
    $contracts = $DB_request->getContractsByProjectId($_GET['project_id']);
    $data['contracts'] = $contracts;

    $vendors = $DB_vendor->getVendorsList();
    $data['vendors'] = $vendors;

    if(!empty($contracts)){
        $contractID = $contracts[0]['contract_id'];
        $contractInfo = $DB_request->getContractDataByContractId($contractID);
        $billsByContract = $DB_bill->getBillsByContractID($contractID);
        $sum = 0;
        foreach ($billsByContract as $key => $value) {
            if ($value['material_by_owner'] != 1) {
                $sum = $value['amount'] + $sum;
            }
        }
        if (!empty($contractInfo['contract_price'])) {
            $outstanding = $contractInfo['contract_price'] - $sum;
        }
        $data['contract'] = $contractInfo;
        $floatValue = (float)$outstanding;
        $data['outstanding'] = round($floatValue, 2);
    }

    echo json_encode($data);
} else if (isset($_GET['contract_id'])) {
    $contractID = $_GET['contract_id'];
    $contractInfo = $DB_request->getContractDataByContractId($contractID);
    $billsByContract = $DB_bill->getBillsByContractID($contractID);
    $sum = 0;
    foreach ($billsByContract as $key => $value) {
        if ($value['material_by_owner'] != 1) {
            $sum = $value['amount'] + $sum;
        }
    }
    if (!empty($contractInfo['contract_price'])) {
        $outstanding = $contractInfo['contract_price'] - $sum;
    }
    $contractData['contract'] = $contractInfo;
    $floatValue = (float)$outstanding;
    $contractData['outstanding'] = round($floatValue, 2);

    echo json_encode($contractData);
} else if (isset($_GET['building_id'])) {
    $apartment = $DB_apt->getAptInfoInBuilding($_GET['building_id']);
    $unitNumber = array();
    foreach ($apartment as $key => $row) {
        $unitNumber[$key] = $row['unit_number'];
    }
    array_multisort($unitNumber, SORT_ASC, $apartment);
    echo json_encode($apartment);
} else if (isset($_GET['account_type_id'])) {
    $accountSubTypes = $DB_bill->getAccountSubTypesByAccountTypeID($_GET['account_type_id']);
    echo json_encode($accountSubTypes);
} else if (isset($_GET['vendor_id'])) {
    $vendors = $DB_vendor->getVendorsList();
    $vendor = $DB_vendor->getVendorInfo($_GET['vendor_id']);
    $data['vendor'] = $vendor;

    for ($i = 1; $i <= 3; $i++) {
        $id = $vendor['account_type' . $i];
        if (!empty($id)) {
            $result = $DB_bill->getAccountTypeByID($id);
            $vendorTypes[$i] = $result['name'];
        }
        if ($i == 3 && !empty($vendorTypes)) {
            $data['vendorTypes'] = $vendorTypes;
        }
    }

    $province_id = $vendor['province_id'];
    $SelectSql = "select * from provinces where id=1";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $tax['province'] = $result['name'];
    $tax['GST'] = $result['tax1'];
    $tax['QST'] = $result['tax2'];
    $tax['HST'] = $result['tax3'];

    $taxes = ($result['tax1'] + $result['tax2'] + $result['tax3']);

    if ($result['tax1'] != 0 && $result['tax1'] != null) {
        $tax['type'] = 'GST';
    }
    if ($result['tax2'] != 0 && $result['tax1'] != null) {
        $tax['type'] = $tax['type'] . ',Qst';
    }
    if ($result['tax3'] != 0 && $result['tax1'] != null) {
        $tax['type'] = $tax['type'] . ',HST';
    }
    $data['tax'] = $tax;
    echo json_encode($data);
}