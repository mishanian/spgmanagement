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
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Property"); ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?><img src="custom/images/home_icons/buildeing_icon.png" alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>
                                    <li <?php echo validateDisplay($listAccessPermissions, "building_infos"); ?>><a href="building_infoslist.php"><img src="custom/images/home_icons/building_icon_1.png" alt="building_1">
                                            <div class="num_pad"><?= $building_num ?></div>
                                            <p> <?php echo $DB_snapshot->echot("Properties"); ?> </p>
                                        </a></li>
                                    <li <?php echo validateDisplay($listAccessPermissions, "apartment_infos"); ?>><a href="apartmentinfoslist"><img src="custom/images/home_icons/building_icon_2.png" alt="building_1">
                                            <div class="num_pad"><?= $unit_num ?></div>
                                            <p><?php echo $DB_snapshot->echot("Units"); ?> </p>
                                        </a></li>
                                    <li><a href="floor_infoslist.php">
                                            <p><i class="fa fa-building fa-3x"></i><?php echo $DB_snapshot->echot("Floors"); ?>
                                            </p>
                                        </a></li>
                                    <li><a href="apartmentinfoslist?cmd=search&t=apartment_infos&z_size_type_id=%3D&x_size_type_id=9"><img src="custom/images/home_icons/parking unit.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Parking Units"); ?></p>
                                        </a></li>
                                    <li><a href="apartmentinfoslist?cmd=search&t=apartment_infos&z_size_type_id=%3D&x_size_type_id=10"><img src="custom/images/home_icons/Storag Units.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Storage Units"); ?></p>
                                        </a></li>
                                    <li><a href="lock_infoslist.php"><img src="custom/images/home_icons/building_icon_3.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Security Codes"); ?></p>
                                        </a></li>

                                </ul>
                            </div>
                        </div>




                    </div>

                    <!-- middle -->
                    <div class="col-xs-12 col-sm-3 col-md-3">
                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Tenants"); ?></li>
                                    <li class="pull-right"><span><?= $tenant_num ?><img src="custom/images/home_icons/Tenants_icon.png" alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>
                            <div class="points">
                                <ul>
                                    <li <?php echo validateDisplay($listAccessPermissions, "tenant_infos"); ?>><a href="viewtenantinfoslist"><img src="custom/images/home_icons/tenants_1.png" alt="tenants_1">
                                            <div class="num_pad"><?= $tenant_num ?></div>
                                            <p> <?php echo $DB_snapshot->echot("Tenants"); ?></p>
                                        </a></li>
                                    <li <?php echo validateDisplay($listAccessPermissions, "lease_infos"); ?>><a href="leaseinfoslist?cmd=reset"><img src="custom/images/home_icons/tenants_2.png" alt="tenants_2">
                                            <div class="num_pad"><?= $lease_num ?></div>
                                            <p><?php echo $DB_snapshot->echot("Leases"); ?></p>
                                        </a></li>

                                    <li <?php echo validateDisplay($listAccessPermissions, "potential_tenant_infos"); ?>>
                                        <a href="view_questions_and_visitslist.php"><img src="custom/images/home_icons/potential_tenant_1.png" alt="tenants_1">
                                            <div class="num_pad"><?= $potential_tenant_num ?></div>
                                            <p><?php echo $DB_snapshot->echot("Potential Tenant List"); ?></p>
                                        </a>
                                    </li>
                                    <li <?php echo validateDisplay($listAccessPermissions, "building_infos"); ?>><a href="apartmentinfoslist?cmd=search&t=apartment_infos&z_apartment_status=%3D&x_apartment_status=5"><img src="custom/images/home_icons/building_icon_1.png" alt="building_1">
                                            <p> <?php echo $DB_snapshot->echot("Vacances"); ?> </p>
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Material & Maintenance"); ?>
                                    </li>
                                    <li class="pull-right"><span><img width="80%" src="custom/images/home_icons/maintenance_icon.png" alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>
                            <div class="points">
                                <ul>
                                    <li><a href="equipment_infoslist.php"><img src="custom/images/home_icons/maintenance_icon_1.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Equipment Infos"); ?></p>
                                        </a></li>
                                    <li><a href="appliance_infoslist.php"><img src="custom/images/home_icons/maintenance_icon_2.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Appliances Infos"); ?></p>
                                        </a></li>
                                    <li><a href="paintcode_infoslist.php"><img src="custom/images/home_icons/paintCode.png" alt="building_1">
                                            <p><?php echo $DB_snapshot->echot("Paint Code"); ?></p>
                                        </a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- right -->
                    <div class="col-xs-12 col-sm-3 col-md-3">
                        <div class="scroll_menu white-bkg" id="request">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Requests"); ?></li>
                                    <li class="pull-right"><span style="font-size: 10px;">unread:</span><span><?= $unread_request_num ?><img src="custom/images/home_icons/Request_icon.png" alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#unread_request"><?php echo $DB_snapshot->echot("Recent Requests"); ?></a>
                                </li>
                                <li><a data-toggle="tab" href="#bulletins"><?php echo $DB_snapshot->echot("Bulletins"); ?></a></li>
                            </ul>

                            <div class="tab-content" style="height: 177px;overflow-x: auto">
                                <div id="unread_request" class="tab-pane fade in active">
                                    <ul class="text-left">
                                        <?php
                                        $unread_requests = $DB_snapshot->get_recent_request($id);
                                        foreach ($unread_requests as $row) {
                                            $message = $row['message'];
                                            $request_id = $row['id'];
                                            //Yuhong
                                            $unit_addr = $row['building_name'];

                                            $last_update_timestamp = strtotime($row['last_update_time']);
                                            $current_timestamp = strtotime(date('Y-m-d H:i:s'));
                                            $timediff = timediff($last_update_timestamp, $current_timestamp);    // array

                                            $diff_day = $timediff['day'];
                                            $diff_hour = $timediff['hour'];
                                            $diff_min = $timediff['min'];
                                            $diff_sec = $timediff['sec'];

                                            $time_interval = '- ';

                                            if ($diff_day == 1) {
                                                $time_interval .= $diff_day . ' day';
                                            } elseif ($diff_day > 1) {
                                                $time_interval .= $diff_day . ' days';
                                            } elseif ($diff_hour == 1) {
                                                $time_interval .= $diff_hour . ' hour';
                                            } elseif ($diff_hour > 1) {
                                                $time_interval .= $diff_hour . ' hours';
                                            } elseif ($diff_min == 1) {
                                                $time_interval .= $diff_min . ' min';
                                            } elseif ($diff_min > 1) {
                                                $time_interval .= $diff_min . ' mins';
                                            } elseif ($diff_sec == 1) {
                                                $time_interval .= $diff_sec . ' sec';
                                            } elseif ($diff_sec > 1) {
                                                $time_interval .= $diff_sec . ' secs';
                                            }
                                        ?>
                                            <li class="bg_li">
                                                <div class="container">
                                                    <ul class="row text-left">
                                                        <li class="col-xs-12">
                                                            <span style="text-decoration: underline"> <a href="<?php echo "requestadd?action=rview&rid=$request_id"; ?>">
                                                                    <?php echo $unit_addr; ?> </a></span>
                                                            <span style="text-align:left"> [<?php echo $time_interval; ?>]
                                                            </span>
                                                        </li>
                                                        <li class="col-xs-12"><?php echo $message; ?></li>
                                                        <!--li class="col-xs-4"><!--?php echo $time_interval; ?-->
                                                        <!--/li-->
                                                    </ul>
                                                </div>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </div>

                                <div id="bulletins" class="tab-pane fade">
                                    <ul class="text-left">
                                        <?php
                                        $bulletins = $DB_snapshot->get_current_bulletins($id);
                                        foreach ($bulletins as $r) {
                                            $title = $r['title'];
                                        ?>
                                            <li class="bg_li">
                                                <div class="container">
                                                    <ul class="row text-left">
                                                        <li class="col-xs-12"><?php echo $title; ?></li>
                                                    </ul>
                                                </div>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
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