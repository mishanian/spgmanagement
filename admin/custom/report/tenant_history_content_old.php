<!-- *** Scripts that are not present in the header - Do not Remove *** -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tenantBehaviourTable').DataTable();

    var selectedChanged = false;
    var dateChanged = false;
    /*
     * Data table - tenant name select on change event listener
     */
    $('#tenantSelect').change(function() {
        selectedChanged = true;
        table.draw();
        selectedChanged = false;
    });

    /*
     * Date Picker for the tenant history date filter - and change event listener
     */
    $('#tenanthistoryDate').datepicker({
        clearBtn: true,
        showButtonPanel: true,
        autoclose: true,
        numberOfMonths: 3,
        dateFormat: "y-MM-dd"
    }).change(function(e) {
        dateChanged = true;
        table.draw();
        dateChanged = false;
    });

    $('#clearPaymentDateFilter').on("click", function() {
        $('#tenanthistoryDate').val('').datepicker("refresh");
        table.draw();
    });

    /*
     * Data table filter and search
     */
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {

            // If the tenant name filter is to be applied
            if (selectedChanged) {
                var _valueHTML = $.parseHTML(data[1]);
                var _text = _valueHTML[0].data; // Value from the Action column in the table
                var tenantId = _text.split('{').pop().split('}').shift();
                tenantId = parseInt(tenantId);

                var filterByTenantId = parseInt($('#tenantSelect').val());

                if (tenantId == filterByTenantId) {
                    return true;
                }
                return false;
            }

            // If the date filter is to be applied
            if (dateChanged) {
                var _valueHTML = $.parseHTML(data[0]);
                var _dateAndTime = _valueHTML[0].data;
                var dateAndTimeArray = _dateAndTime.split(/(\s+)/);

                var dateArray = dateAndTimeArray[0].split("/");

                var filterSelectedDate = $("#tenanthistoryDate").val();

                if (filterSelectedDate === dateAndTimeArray[0]) {
                    return true;
                }
                return false;
            }

            return true;
        }
    );

});
</script>

<?php
/**
 * Page Content : Shows the tenants activity and his transactions in the system
 */
$employeeId = null;
$companyId = null;
if (isset($_SESSION["employee_id"])) {
	$employeeId = $_SESSION["employee_id"];
}
if (isset($_SESSION["company_id"])) {
	$companyId = $_SESSION["company_id"];
}

/**
 * Handling the Inclusion of database config file for different directories
 */
$cwd = getcwd();
$cwdArray = explode("/", $cwd);
if ($cwdArray[count($cwdArray) - 1] == "report") {
	include("../../../pdo/dbconfig.php");
} else {
	include("../pdo/dbconfig.php");
}

/**
 * Check if Employee is an Admin in the company and fetch the building ID's under them
 */
$isEmployeeAdmin = isEmployeeAdmin($employeeId, $DB_employee) ? "0000" : $employeeId;
$employeeName = $DB_employee->getEmployeeName($employeeId);

if (isEmployeeAdmin($employeeId, $DB_employee)) {
	$allBuildingsIds = $DB_building->getAllBdIdsByCompany($companyId)["GROUP_CONCAT(building_id)"];
	$allBuildingsIds = explode(',', $allBuildingsIds);
} else {
	$allBuildingsIds = getEmployeeBuildings($DB_employee, $employeeId);
}

$tenants = array();

foreach ($allBuildingsIds as $bId) {
	$tenantData = getTenantsByBid($DB_tenant, $bId);
	if ($tenantData & count($tenantData) > 0) {
		foreach ($tenantData as $tenant) {
			$singleTenantData["id"] = $tenant["tenant_id"];
			$singleTenantData["name"] = $tenant["full_name"];
			array_push($tenants, $singleTenantData);
		}
	}
}

function getTenantsByBid($DB_tenant, $bId)
{
	return $DB_tenant->getTenantViewByBid($bId);
}

function isEmployeeAdmin($employeeId, $DB_employee)
{
	$employeeData = $DB_employee->getEmployeeInfo($employeeId);
	if ($employeeData["admin_id"] == 1) {
		return true;
	}
	return false;
}

/**
 * Return the number of digits in a number
 * @param int $number
 * @return int
 */
function numberOfDigits($number)
{
	return strlen(strval($number));
}

/**
 * $response["type"] : 1 = Tenant ; 0 = Employee
 * @param type $id
 */
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

function getEmployeeBuildings($DB_employee, $employeeId)
{
	$employeeData = $DB_employee->getEmployeeInfo($employeeId);
	$buildingIds = $employeeData["building_ids"];

	$buildingIdArray = explode(",", $buildingIds);
	return $buildingIdArray;
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

/*
 * Get all the request assignee details based on the current employee
 * Check if the current loggedin employee is an admin
 * If Admin : Show all the requests for all the buildings in the company
 */

if ($isEmployeeAdmin) {  // If employee is an admin : get all the requests in the company
	$tenantRequestDetails = $DB_tenant->getTenantRequestDetails($companyId);
	$paymentDetails = $DB_ls_payment->getAllPaymentsByCompanyId($companyId);
} else { // If employee is not an admin : get requests of the buildings that the employee has control over
	$tenantRequestDetails = $DB_tenant->getTenantRequestDetails($companyId, $employeeId);
	$paymentDetails = $DB_ls_payment->getAllPaymentsByCompanyId($companyId, $employeeId);
}
?>
<div class="container">
    <fieldset>
        <form action="" method="post">

            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="tenantSelect" class="col-2 col-form-label">Tenant</label>
                            <div class="input-group">
                                <select class="form-control" id="tenantSelect">
                                    <option value="#">Select Tenant</option>
                                    <?php
									foreach ($tenants as $tenant) {
										if (isset($_GET["tid"]) && !empty($_GET["tid"])) {
											$tid = intval($_GET["tid"]);
											if ($tid == intval($tenant["id"])) {
												echo "<option selected='selected' value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
											} else {
												echo "<option value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
											}
										} else {
											echo "<option value='" . $tenant["id"] . "'>" . $tenant["name"] . "</option>";
										}
									}
									?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label for="paymentDateFilter" class="col-2 col-form-label">Date</label>
                                <div class="col-10">
                                    <div class="input-group date" data-provide="datepicker" style="float:left;">
                                        <input type="text" class="datepicker form-control" class="form-control"
                                            id="tenanthistoryDate" placeholder="Select a date">
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

                    <colgroup>
                        <col span="1" style="width: 10%;">
                        <col span="1" style="width: 20%;">
                        <col span="1" style="width: 20%;">
                        <col span="1" style="width: 30%;">
                    </colgroup>

                    <thead>
                        <tr>
                            <th><strong> Date and Time </strong></th>
                            <th><strong> Action </strong></th>
                            <th><strong> Information </strong></th>
                            <th><strong> Comments </strong></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
						foreach ($tenantRequestDetails as $request) {
							$statement = "";
							$assignedToId = $request["assigned_to"];
							$nameAndType = nameAndTypeOfId($assignedToId, $DB_tenant, $DB_employee);
							$nameOfAssigned = $nameAndType['name'];
							$typeOfAssigned = $nameAndType['type'];

							$createId = $request["created_user_id"];

							$nameAndTypeOfCreated = nameAndTypeOfId($createId, $DB_tenant, $DB_employee);

							if ($nameAndTypeOfCreated["type"] == 0) { // Issue created by employee
								if ($nameAndTypeOfCreated["type"] != $typeOfAssigned) { // 'Assigned' is the tenant
									$apt_id = $request["apt_id"];
									$statement = "<span style='display:none;'>{" . $assignedToId . "}</span> <span data-tenantid='" . $assignedToId . "' > Issue created by <strong> " . $nameAndTypeOfCreated["name"] . " </strong>  </span>";
								} else {
									continue;
								}
							} else { // Issue created by Tenant
								if ($nameAndTypeOfCreated["type"] != $typeOfAssigned) { // 'Assigned' is the Employee
									$statement = "<span style='display:none;'>{" . $createId . "}</span> <span data-tenantid='" . $createId . "' > Issue created by <strong>" . $nameAndTypeOfCreated["name"] . "</strong> and can be viewed by $nameOfAssigned.</span>";
								} else {
									continue;
								}
							}

							$requestTimeStamp = new DateTime($request["request_timestamp"]);
							$requestFormattedTimeStamp = $requestTimeStamp->format("Y-m-d h:m:s");

							if ($request["category"] == 0) {
								continue; // System generated Issue for the tenant
							}

							$category = getRequestCategoryName($request["category"]);

							$type = $request["type"];
							$typeName = $DB_tenant->getRequestType($type);

							if (isset($_GET["tid"]) && !empty($_GET["tid"])) {
								$tid = intval($_GET["tid"]);
								if ($tid != intval($createId)) {
									continue;
								}
							}
						?>
                        <tr>
                            <td> <?php echo $requestFormattedTimeStamp; ?></td>
                            <td> <?php echo $statement; ?></td>
                            <td>
                                <span><strong><i>Issue #:</i> </strong> <?php echo $request["request_info_table_id"]; ?>
                                </span> <br>
                                <span><strong><i>Category: </i></strong> <?php echo $typeName["name"]; ?></span><br>
                                <span><strong><i>Type: </i></strong> <?php echo $category; ?> </span>
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
							$paymentType = $DB_ls_payment->getPaymentType($payment["type"])["name"];

							$paymentTimeStamp = new DateTime($payment["timestamp"]);
							$paymentFormattedTimeStamp = $requestTimeStamp->format("Y-m-d h:m:s");

							$dueDateTimeStamp = new DateTime($payment["due_date"]);
							$dueDateFormattedTimeStamp = $dueDateTimeStamp->format("Y-m-d h:m:s");

							$tenantIds = $payment["tenant_ids"];

							if (!empty($tenantIds)) {
								if (isset($_GET["tid"]) && !empty($_GET["tid"])) {
									$tid = intval($_GET["tid"]);
									$tenantIdArray = explode(",", $tenantIds);
									if (count($tenantIdArray) > 0) {
										if (!in_array($tid, $tenantIdArray)) {
											continue;
										}
									}
								}
							}
						?>
                        <tr>
                            <td> <?php echo $paymentFormattedTimeStamp; ?></td>
                            <td> <?php echo "Payment registered by the Tenant"; ?></td>
                            <td>
                                <span><strong><i>Due Date:</i> </strong> <?php echo $dueDateFormattedTimeStamp; ?>
                                </span> <br>
                                <span><strong><i>Payment Type: </i></strong> <?php echo $paymentType; ?></span><br>
                                <span><strong><i>Payment Method: </i></strong> <?php echo $paymentMethod; ?> </span>
                                <br>
                                <span><strong><i>Amount: </i></strong> <?php echo "$" . $payment["amount"]; ?> </span>
                            </td>
                            <td>
                                <?php echo $payment["comments"]; ?>
                            </td>
                        </tr>
                        <?php }
						?>

                    </tbody>

                </table>
            </div>
        </form>
        <!--END OF FORM ^^-->
    </fieldset>
</div>