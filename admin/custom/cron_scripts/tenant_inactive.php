<?php
/* * *
 *  After 20 days of move out , tenants should be inactive
 * Author : Sharan
 * Date : 2018-08-13
 * * */
if (strpos(getcwd(), "cron_scripts") == false) {
	$path = "../../pdo/";
} else {
	$path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
include_once($path . "Class.Lease.php");
$DB_lease   = new Lease($DB_con);
$allLeaseDetails   = $DB_lease->getTenantIdsMoveOut20Days(); // Has lease details and move out date,move out date + 20 days
$eligibleTenantIds = array();

foreach ($allLeaseDetails as $leaseDetail) {
	$moveOutDate20Date = $leaseDetail["dateafter20"];
	$id                = $leaseDetail["id"];
	$todayDate         = date("Y-m-d");

	if ($moveOutDate20Date == $todayDate) {
		array_push($eligibleTenantIds, $leaseDetail["tenant_ids"]);
	}
}

/* Deactivate the tenants accounts */
foreach ($eligibleTenantIds as $tenantIdsDetail) {
	$tenantIdArray = explode(",", $tenantIdsDetail);

	foreach ($tenantIdArray as $tenantId) {
		/* Deactivate tenant id */
		$DB_tenant->deactivateTenant($tenantId);
	}
}