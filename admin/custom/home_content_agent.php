<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
if (isset($_SESSION['employee_id'])) {
    $id = $_SESSION['employee_id'];
    $role = "employee";
} else if (isset($_SESSION['owner_id'])) {
    $id = $_SESSION['owner_id'];
    $role = "owner";
} else if (isset($_SESSION['UserID'])) {
    $id = $_SESSION['UserID'];
    $role = "employee";
} else {
    $role = "admin";
    $id = 0;
}

/**
 *  Below code to calculate which detail to show in the Home page to the logged in user based on the permission
 */
$userLevelList = $_SESSION["spgmanagement_arUserLevelPriv"];

$listAccessPermissions = array();
$accessPermittedValues = [96, 104, 20, 40, 41, 42, 43, 44, 45, 46, 47, 72, 73, 74, 75, 76, 77, 78, 79, 111, 108, 109, 110];

foreach ($userLevelList as $accessList) {
    $accessListCategory = explode("}", $accessList[0]);
    switch ($accessListCategory[1]) {
        case "building_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["building_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["building_infos"] = 1;
            }
            break;
        case "apartment_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["apartment_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["apartment_infos"] = 1;
            }
            break;
        case "lease_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["lease_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["lease_infos"] = 1;
            }
            break;
        case "tenant_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["tenant_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["tenant_infos"] = 1;
            }
            break;

        case "view_tenant_statement":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["view_tenant_statement"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["view_tenant_statement"] = 1;
            }
            break;

        case "view_questions_and_visits":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["potential_tenant_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["potential_tenant_infos"] = 1;
            }
            break;

        case "credit_check_records":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["credit_check_records"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["credit_check_records"] = 1;
            }
            break;

        case "company_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["company_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["company_infos"] = 1;
            }
            break;

        case "employee_infos":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["employee_infos"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["employee_infos"] = 1;
            }
            break;

        case "calendar.php":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["Calendar"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["Calendar"] = 1;
            }
            break;

        case "online_listing":
            $permissionValue = intval($accessList["permission"]);
            $listAccessPermissions["online_listing"] = 0;
            if (in_array($permissionValue, $accessPermittedValues) || ($permissionValue > 8 && $permissionValue < 20) || ($permissionValue == 8)) {
                $listAccessPermissions["online_listing"] = 1;
            }
            break;
    }
}

function validateDisplay($listAccessPermissions, $type)
{
    if (isset($listAccessPermissions[$type])) {
        return $listAccessPermissions[$type] === 0 ? "style='display:none;'" : "";
    }
    return "style='display:none;'";
}

$manager_num = $DB_snapshot->getManagerNum($id, $role);
$unpaid_payment_num = $DB_snapshot->getUnpaidPaymentNum($id, $role);
$late_payment_num = $DB_snapshot->getLatePaymentNum($id, $role);
$open_request_num = $DB_snapshot->getOpenRequestNum($id, $role);
$pending_renewal_num = $DB_snapshot->getPendingRenewalNum($id, $role);
$owner_num = $DB_snapshot->getOwnerNum($id, $role);
//Yuhong

$tenant_num = $DB_snapshot->getTenantNum($id, $role);
$unread_request_num = $DB_snapshot->getUnreadRequestNum($id, $role);
$potential_tenant_num = $DB_snapshot->getPotentialTenantNum($id, $role);
$employee_num = $DB_snapshot->getManagerNum($id, "employee");
$building_num = $DB_snapshot->getBuildingNum($id, "employee");
$unit_num = $DB_snapshot->getUnitNum($id, "employee");
$lease_num = $DB_snapshot->getLeaseNum($id, "employee");


if (isset($_SESSION['company_id'])) {
    $companyId = $_SESSION['company_id'];
    $building_num = $DB_snapshot->getBuildingNum($companyId, "company");
    $lease_num = $DB_snapshot->getLeaseNum($companyId, "company");
    $unit_num = $DB_snapshot->getUnitNum($companyId, "company");
    $potential_tenant_num = $DB_snapshot->getPotentialTenantNum($companyId, "company");
    $tenant_num = $DB_snapshot->getTenantNum($companyId, "company");
}

if (PHPMaker2023\spgmanagement\CurrentUserLevel() == -1) {
    $role = "admin";
    $id = 0;

    foreach ($listAccessPermissions as $key => $permission) {
        $listAccessPermissions[$key] = 1;
    }

    $building_num = $DB_snapshot->getBuildingNum($id, $role);
    $lease_num = $DB_snapshot->getLeaseNum($id, $role);
    $unit_num = $DB_snapshot->getUnitNum($id, $role);
    $potential_tenant_num = $DB_snapshot->getPotentialTenantNum($id, $role);
    $tenant_num = $DB_snapshot->getTenantNum($id, $role);
}
?>

<link href="custom/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="custom/css/home-style.css" rel="stylesheet" type="text/css">

<div id="page-wrapper">
    <main>
        <section id="content">

            <div class="container">

                <div class="row">
                    <!-- left -->

                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Quick Access"); ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>
                                    <li <?php echo validateDisplay($listAccessPermissions, "building_infos"); ?>><a href="apartmentinfoslist?cmd=search&t=apartment_infos&z_apartment_status=%3D&x_apartment_status=5"><img src="custom/images/home_icons/building_icon_1.png" alt="building_1">
                                            <p> <?php echo $DB_snapshot->echot("Vacances"); ?> </p>
                                        </a></li>
                                    <li <?php echo validateDisplay($listAccessPermissions, "potential_tenant_infos"); ?>><a href="view_questions_and_visitslist.php"><img src="custom/images/home_icons/potential_tenant_1.png" alt="tenants_1">
                                            <div class="num_pad"><?= $potential_tenant_num ?></div>
                                            <p><?php echo $DB_snapshot->echot("Potential Tenant List"); ?></p>
                                        </a></li>
                            </div>
                        </div>



                    </div>
                </div><!-- end row-->
            </div><!-- end container-->
        </section>
    </main>

</div>
<!--page-wrapper-->

<?php

function timediff($begin_time, $end_time)
{
    $timediff = $end_time - $begin_time;

    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}
?>