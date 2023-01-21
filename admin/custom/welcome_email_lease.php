<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../pdo/dbconfig.php');
$lease_id  = $_GET['lid'];
include_once('../../pdo/Class.WelcomeNtf.php');
include_once("../../pdo/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);

$tenants = $DB_tenant->getTenantForLeaseId($lease_id);
$tenant_ids = explode(",", $tenants[0]);

// echo (var_dump($tenant_ids));
foreach ($tenant_ids as $tenant_id) {
    $welcome_lease = new WelcomeNtf($tenant_id);
    //var_dump($welcome_test);
    $welcome_lease->send_welcome_email();
}