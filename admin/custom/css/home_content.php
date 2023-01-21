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
}

if (CurrentUserLevel() == -1) {
  $role = "admin";
  $id = 0;
}

//$owner_num = $DB_snapshot->getOwnerNum($id, $role);
$building_num = $DB_snapshot->getBuildingNum($id, $role);
//$manager_num = $DB_snapshot->getManagerNum($id, $role);
$tenant_num = $DB_snapshot->getTenantNum($id, $role);

//$unpaid_payment_num = $DB_snapshot->getUnpaidPaymentNum($id, $role);
//$late_payment_num = $DB_snapshot->getLatePaymentNum($id, $role);

$unread_request_num = $DB_snapshot->getUnreadRequestNum($id, $role);
//$open_request_num = $DB_snapshot->getOpenRequestNum($id, $role);

//$pending_renewal_num = $DB_snapshot->getPendingRenewalNum($id, $role);
//$potential_tenant_num = $DB_snapshot->getPotentialTenantNum($id, $role);

?>

<link href="custom/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="custom/css/home-style.css" rel="stylesheet" type="text/css">

<div id="page-wrapper">
  <main>
    <section id="content">
      <div class="container">
        <div class="row">
          <!-- left -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <div class="buildings">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Buildings</li>
                  <li class="pull-right"><span><?= $building_num ?><img src="custom/images/home_icons/buildeing_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>

              </div>
              <div class="points">
                <ul>
                  <li><a href="building_infoslist.php"><img src="custom/images/home_icons/building_icon_1.png" alt="building_1">
                      <p>Building Management</p>
                    </a></li>
                  <li><a href="apartmentinfoslist"><img src="custom/images/home_icons/building_icon_2.png" alt="building_1">
                      <p>Unit Management</p>
                    </a></li>
                  <li><a href="lock_infoslist.php"><img src="custom/images/home_icons/building_icon_3.png" alt="building_1">
                      <p>Locker Management</p>
                    </a></li>
                  <li><a href="#"><img src="custom/images/home_icons/building_icon_4.png" alt="building_1">
                      <p>Parking Management</p>
                    </a></li>
                </ul>
              </div>
            </div>

            <div class="payment">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Payments & Deposits</li>
                  <li class="pull-right"><span><img src="custom/images/home_icons/payment_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>
              </div>
              <div class="points">
                <ul>
                  <li><a href="depositslist.php"><img src="custom/images/home_icons/payment_icon_1.png" alt="building_1">
                      <p>Deposits</p>
                    </a></li>
                  <li><a href="rental_paymentslist.php"><img src="custom/images/home_icons/payment_icon_2.png" alt="building_1">
                      <p>Rental Payment</p>
                    </a></li>
                </ul>
              </div>
            </div>
          </div>

          <!-- middle -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <div class="tenants">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Tenants</li>
                  <li class="pull-right"><span><?= $tenant_num ?><img src="custom/images/home_icons/Tenants_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>
              </div>
              <div class="points">
                <ul>
                  <li><a href="viewtenantinfoslist"><img src="custom/images/home_icons/tenants_1.png" alt="tenants_1">
                      <p>Tenants Management</p>
                    </a></li>
                  <li><a href="leaseinfoslist"><img src="custom/images/home_icons/tenants_2.png" alt="tenants_2">
                      <p>Lease Management</p>
                    </a></li>
                  <li><a href="view_tenant_statementlist.php"><img src="custom/images/home_icons/tenants_3.png" alt="building_1">
                      <p>Tenants Statement</p>
                    </a></li>
                </ul>
              </div>
            </div>

            <div class="p_tenants">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Potential Tenant</li>
                  <li class="pull-right"><span><img src="custom/images/home_icons/potential_tenant.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>
              </div>
              <div class="points">
                <ul>
                  <li><a href="view_questions_and_visitslist.php"><img src="custom/images/home_icons/potential_tenant_1.png" alt="tenants_1">
                      <p>Potential Tenant List</p>
                    </a></li>
                  <li><a href="#"><img src="custom/images/home_icons/potential_tenant_2.png" alt="tenants_2">
                      <p>Credit Checking</p>
                    </a></li>
                  <li><a href="#"><img src="custom/images/home_icons/potential_tenant_3.png" alt="building_1">
                      <p>Lease<br>Signing</p>
                    </a></li>
                </ul>
              </div>
            </div>

            <div class="maintenance">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Maintenance</li>
                  <li class="pull-right"><span><img src="custom/images/home_icons/maintenance_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>
              </div>
              <div class="points">
                <ul>
                  <li><a href="equipment_infoslist.php"><img src="custom/images/home_icons/maintenance_icon_1.png" alt="building_1">
                      <p>Equipment Information</p>
                    </a></li>
                  <li><a href="appliance_infoslist.php"><img src="custom/images/home_icons/maintenance_icon_2.png" alt="building_1">
                      <p>Appliances</p>
                    </a></li>
                </ul>
              </div>
            </div>
          </div>

          <!-- right -->
          <div class="col-xs-12 col-sm-4 col-md-4">
            <div class="scroll_menu">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Request</li>
                  <li class="pull-right"><span><?= $unread_request_num ?><img src="custom/images/home_icons/Request_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>
              </div>

              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#unread_request">Unread Requests</a></li>
                <li><a data-toggle="tab" href="#bulletins">Bulletins</a></li>
              </ul>

              <div class="tab-content" style="height: 200px;">
                <div id="unread_request" class="tab-pane fade in active">
                  <ul class="text-left">
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">12 new users registered</li>
                          <li class="col-xs-4">Just now</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System shutdown</li>
                          <li class="col-xs-4">14 mins</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New invoice received</li>
                          <li class="col-xs-4">20 Min</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">DB overioaded 80%</li>
                          <li class="col-xs-4">1 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System error - Check</li>
                          <li class="col-xs-4">2 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New order received</li>
                          <li class="col-xs-4">7 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">12 new users registered</li>
                          <li class="col-xs-4">Just now</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System shutdown</li>
                          <li class="col-xs-4">14 mins</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New invoice received</li>
                          <li class="col-xs-4">20 Min</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">DB overioaded 80%</li>
                          <li class="col-xs-4">1 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System error - Check</li>
                          <li class="col-xs-4">2 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New order received</li>
                          <li class="col-xs-4">7 hr</li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>

                <div id="bulletins" class="tab-pane fade">
                  <ul class="text-left">
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">12 new users registered</li>
                          <li class="col-xs-4">Just now</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System shutdown</li>
                          <li class="col-xs-4">14 mins</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New invoice received</li>
                          <li class="col-xs-4">20 Min</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">DB overioaded 80%</li>
                          <li class="col-xs-4">1 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System error - Check</li>
                          <li class="col-xs-4">2 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New order received</li>
                          <li class="col-xs-4">7 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">12 new users registered</li>
                          <li class="col-xs-4">Just now</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System shutdown</li>
                          <li class="col-xs-4">14 mins</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New invoice received</li>
                          <li class="col-xs-4">20 Min</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">DB overioaded 80%</li>
                          <li class="col-xs-4">1 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">System error - Check</li>
                          <li class="col-xs-4">2 hr</li>
                        </ul>
                      </div>
                    </li>
                    <li class="bg_li">
                      <div class="container">
                        <ul class="row text-left">
                          <li class="col-xs-8">New order received</li>
                          <li class="col-xs-4">7 hr</li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="profile">
              <div class="headings_box">
                <ul>
                  <li class="pull-left">Profile</li>
                  <li class="pull-right"><span><img src="custom/images/home_icons/profile_icon.png" alt="building_icon"></span></li>
                  <div class="clearfix"></div>
                </ul>

              </div>
              <div class="points">
                <ul>
                  <li><a href="company_infoslist.php"><img src="custom/images/home_icons/profile_icon_1.png" alt="tenants_1">
                      <p>Company</p>
                    </a></li>
                  <li><a href="employee_infoslist.php"><img src="custom/images/home_icons/profile_icon_2.png" alt="tenants_2">
                      <p>Employees</p>
                    </a></li>
                  <li><a href="kijiji_listing.php"><img src="custom/images/home_icons/profile_icon_3.png" alt="building_1">
                      <p> Online Listing</p>
                    </a></li>
                </ul>
              </div>
            </div>
          </div>
        </div><!-- end row-->
      </div><!-- end container-->
    </section>
  </main>

</div>
<!--page-wrapper-->