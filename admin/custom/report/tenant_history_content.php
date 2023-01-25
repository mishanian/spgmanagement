<?php
if (!array_key_exists("lid", $_GET)) {
	echo "Lease Id detail missing";
	exit;
}
include_once("../pdo/dbconfig.php");
include_once('../pdo/Class.Lease.php');
$DB_lease = new Lease($DB_con);
include_once("../pdo/Class.Employee.php");
$DB_employee = new Employee($DB_con);
include_once("../pdo/Class.Building.php");
$DB_building = new Building($DB_con);
include_once("../pdo/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);
include_once("../pdo/Class.LeasePayment.php");
$DB_ls_payment = new LeasePayment($DB_con);


$lease_apartment_id = $_GET["aid"];

function nameAndTypeOfId($id, $DB_tenant, $DB_employee)
{
	$response = array();
	if (numberOfDigits($id) > 5) { // Tenant
		$response["name"] = $DB_tenant->getTenantName($id);
		$response["type"] = 1;
	} else { // Employee
		$response["name"] = $DB_employee->getEmployeeName($id);
		$response["type"] = 0;
	}
	return $response;
}

function numberOfDigits($number)
{
	return strlen(strval($number));
}

function get_words($sentence, $count = 10)
{
	preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
	return $matches[0];
}

function getRequestCategoryName($categoryId)
{
	switch (intval($categoryId)) {
		case 0:
			return "System Generated";
			break;
		case 1:
			return "Internal Issue";
			break;
		case 2:
			return "Tenant Issue";
			break;
	}
}

$tenantRequestDetails = $DB_tenant->getTenantRequestDetailsByAptId($lease_apartment_id);
$paymentDetails       = $DB_ls_payment->getAllPaymentsByLeaseId($_GET["lid"]);
// die(var_dump($tenantRequestDetails));
?>
<div class="container">
    <fieldset>
        <form action="" method="post">

            <div class="form-group row">
                <!--                <div class="col-sm-6">-->
                <!--                    <div class="form-group row">-->
                <!--                        <div class="col-sm-12">-->
                <!--                            <label for="tenantSelect" class="col-2 col-form-label">Tenant</label>-->
                <!--                            <div class="input-group">-->
                <!--                                <select class="form-control" id="tenantSelect">-->
                <!--                                    <option value="#">Select Tenant</option>-->
                <!--									--><?php
															//									foreach ($tenants as $tenant) {
															//										if (isset($_GET["tid"]) && !empty($_GET["tid"])) {
															//											$tid = intval($_GET["tid"]);
															//											if ($tid == intval($tenant["id"])) {
															//												echo "<option selected='selected' value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
															//											}
															//											else {
															//												echo "<option value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
															//											}
															//										}
															//										else {
															//											echo "<option value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
															//										}
															//									}
															//
															?>
                <!--                                </select>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label for="paymentDateFilter" class="col-2 col-form-label">Date</label>
                                <div class="form-group mb-10">
                                    <div class="input-group-prepend date" data-provide="datepicker" style="float:left;">
                                        <input type="text" class="datepicker form-control" id="tenanthistoryDate"
                                            placeholder="Select a date">
                                    </div>
                                    <button id="clearPaymentDateFilter" type="button" class="btn btn-default btn-sm"
                                        style="float:left;margin-left:5px">
                                        <span class="glyphicon glyphicon-remove"></span> Clear Date
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">

                <table id="tenantBehaviourTable" class="table table-striped table-bordered" cellspacing="0" width="100%"
                    style="background: aliceblue;">

                    <!-- <colgroup>
                        <col span="1" style="width: 10%;">
                        <col span="1" style="width: 20%;">
                        <col span="1" style="width: 20%;">
                        <col span="1" style="width: 30%;">
                    </colgroup> -->

                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Date and Time</th>
                            <th>Information</th>
                            <th>Comments</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
						//$tenantRequestDetails=array_slice($tenantRequestDetails, 0, 0);
						foreach ($tenantRequestDetails as $request) {
							$statement      = "";
							$createId = intval($request["employee_id"]);
							// it was $createId = intval($request["created_user_id"]);

							$nameAndTypeOfCreated = nameAndTypeOfId($createId, $DB_tenant, $DB_employee);
							$requestTimeStamp          = new DateTime($request["request_timestamp"]);
							$requestFormattedTimeStamp = $requestTimeStamp->format("Y-m-d h:m:s");

							if ($request["category"] == 0) {
								continue; // System generated Issue for the tenant
							}

							$category = getRequestCategoryName($request["category"]);

							$type     = $request["type"];
							$typeName = $DB_tenant->getRequestType($type);

							if (isset($_GET["tid"]) && !empty($_GET["tid"])) {
								$tid = intval($_GET["tid"]);
								//							if ($tid != intval($createId)) {
								//								continue;
								//							}
							}
						?>
                        <tr>
                            <td> Request / Issue </td>
                            <td> <?php echo $requestFormattedTimeStamp; ?></td>
                            <td>
                                <span><strong>Issue #: </strong> <?php echo $request["request_info_table_id"]; ?>
                                </span>
                                <br>
                                <span><strong>Category: </strong> <?php echo $typeName["name"]; ?></span><br>
                                <span><strong>Type: </strong> <?php echo $category; ?> </span>
                            </td>
                            <td style="height:10px;overflow-y: auto;">
                                <?php
									$issueDetail = addslashes($request["issue_detail"]);
									echo $issueDetail;
									?>
                            </td>
                        </tr>
                        <?php
						}
						?>

                        <?php
						foreach ($paymentDetails as $payment) {
							$paymentMethod = $DB_ls_payment->getPaymentMethod($payment["method"])["name"];
							$paymentType   = $DB_ls_payment->getPaymentType($payment["type"])["name"];

							$paymentTimeStamp          = new DateTime($payment["timestamp"]);
							$paymentFormattedTimeStamp = $requestTimeStamp->format("Y-m-d h:m:s");

							$dueDateTimeStamp          = new DateTime($payment["due_date"]);
							$dueDateFormattedTimeStamp = $dueDateTimeStamp->format("Y-m-d h:m:s");

							$paidAmt = $payment["amount"];

							if (!isset($paidAmt) || !is_null($paidAmt)) {
								$paidAmt = $payment["ls_paid_amount"];
							}
						?>
                        <tr>
                            <td> Payment </td>
                            <td> <?php echo $paymentFormattedTimeStamp; ?></td>
                            <td>
                                <span><strong>Due Date:</strong> <?php echo $dueDateFormattedTimeStamp; ?> </span>
                                <br>
                                <span><strong>Payment Type: </strong> <?php echo $paymentType; ?></span><br>
                                <span><strong>Payment Method: </strong> <?php echo $paymentMethod; ?> </span>
                                <br>
                                <span><strong>Amount: </strong> <?php echo "$" . $paidAmt; ?> </span>
                            </td>
                            <td>
                                <?php echo $payment["comments"]; ?>
                            </td>
                        </tr>
                        <?php }
						?>
                    <tfoot>
                        <tr>
                        <tr>
                            <th>Type</th>
                            <th>Date and Time</th>
                            <th>Information</th>
                            <th>Comments</th>
                        </tr>
                        </tr>
                    </tfoot>
                    </tbody>

                </table>
            </div>
        </form>

    </fieldset>
</div>
<!-- *** Scripts that are not present in the header - Do not Remove *** -->
<script>
loadjs.ready(["jquery", "head"], function() {
    loadjs([
        "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css",
        "https://code.jquery.com/ui/1.12.1/jquery-ui.js",
        "https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css",
        "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js",
    ], 'datatable');
});


loadjs.ready(["datatable"], function() {
    loadjs([
        "https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js",
        "custom/report/js/tenant_history.js"
    ], 'jsloaded');
});
</script>
<!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css" />
 <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>
 <script type="text/javascript" src="custom/report/js/tenant_history.js"></script> -->