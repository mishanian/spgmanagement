<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../pdo/dbconfig.php';

include_once('../pdo/Class.Company.php');
$DB_company = new Company($DB_con);

include '../pdo/Class.TenantLease.php';
$DB_tenantLease = new TenantLease($DB_con);

if (isset($_SESSION['company_id'])) {
    $company_id = $_SESSION['company_id'];
} else {
    die("You may not login now. please contact support for this issue. <a href='mailto:techsupport@spgmanagement.com'>techsupport@spgmanagement.com</a>");
}

$company_id = $_SESSION['company_id'];
$tenant_id = $_SESSION['tenant_id'];
$LeaseInfos = $DB_tenantLease->getTenantLeaseInfo($tenant_id);
if (empty($LeaseInfos)) {
    echo ("The tenant is not activated or there is no lease linked to the tenant yet.");
} else {
    $lease_id = $LeaseInfos[0]['id'];
    $lease_end_date = $LeaseInfos[0]['end_date'];
    // die(var_dump($LeaseInfos));

    $renewal_notification_day = $DB_company->getRenewalNotificationDay($company_id);

    $results = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
    if (empty($result)) {
        die("There is no lease linked to the tenant yet. you may inform technical support <a href='mailto:techsupport@spgmanagement.com'>techsupport@spgmanagement.com</a> by sending this error : home page, no lease linked tid: $tenant_id, lid: $lease_id");
    }
    // die(var_dump($results));
    $date_diff = round((strtotime($lease_end_date) - strtotime(date("Y-m-d"))) / 60 / 60 / 24);
    if (!in_array($results['lease_status_id'], [8, 9]) && $date_diff <= $renewal_notification_day && !empty($results) && $results['renewal'] == 0 && empty($_GET['skip'])) {
        // include_once("custom/tenant_portal/renewal_notice.php");
        header("Location: custom/tenant_portal/renewal_notice.php?tenant_id=$tenant_id&lease_id=$lease_id");
        echo "<a href='custom/tenant_portal/renewal_notice.php?tenant_id=$tenant_id&lease_id=$lease_id'>Click here to renew your lease</a>";
    } else {
        include_once("custom/tenant_portal/tenant_index.php");
    }
}
