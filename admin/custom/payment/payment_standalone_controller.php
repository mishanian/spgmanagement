<?php
include_once ('../../../pdo/dbconfig.php');
include_once ('../../../pdo/Class.Tenant.php');
$DB_tenant = new Tenant($DB_con);
include_once ('../../../pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con);
include_once ('../../../pdo/Class.Payment.php');
$DB_payment = new Payment($DB_con);

// All the tenants filtered by the company
if(!empty($_GET["action"]) && $_GET["action"] == "getTenants"){
    // get all the tenants for the logged in user
    // The standalone payment can be atached to the tenant and made into a custom invoice
    $companyId = $_GET["company_id"]; // company id of the logged in session
    $allTenants = $DB_tenant->getTenantsByCompany($companyId);

    echo json_encode(array("data" => $allTenants ));
}

// All the tenants filtered by the company
if(!empty($_GET["action"]) && $_GET["action"] == "getApts"){
    $buildingId = $_GET["building_id"]; // building ID
    $allApts = $DB_apt->getAptInfoInBuilding($buildingId, 'unit_number');
    echo json_encode($allApts);
}

// respond with the outstanding values for all the payment methods
if(!empty($_GET["action"]) && $_GET["action"] == "getPaymentValue"){
    $outstanding = $_GET["outstanding"];
    $convenience_rules = $DB_payment->get_convenience_fee_rate();
    $CF_PP_Balance_P = $convenience_rules['CF_PP_Balance_F'];
    $CF_PP_CC_P = $convenience_rules['CF_PP_CC_P'];
    $CF_M_CC_P = $convenience_rules['CF_M_CC_P'];
    $CF_M_Interac_F = $convenience_rules['CF_M_Interac_F'];

    $values = array("CF_PP_Balance_F" => number_format($outstanding+round($outstanding*$CF_PP_Balance_P/100,2),2),
                    "CF_PP_CC_P" => number_format($outstanding+round($outstanding*$CF_PP_CC_P/100,2),2),
                    "CF_M_CC_P" => number_format($outstanding+round($outstanding*$CF_M_CC_P/100,2),2),
                    "CF_M_Interac_F" => number_format(round($outstanding+$CF_M_Interac_F,2),2)
                    );

    echo json_encode($values);
}





