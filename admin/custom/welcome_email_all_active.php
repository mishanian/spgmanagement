<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../pdo/dbconfig.php');
include_once('../../pdo/Class.WelcomeNtf.php');
include_once("../../pdo/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);

$sql = "SELECT * from lease_infos where lease_status_id in(1,2,7,8,9,10,11)";
// $sql .= "and id=2719";
// echo $sql . "<br>";
$Crud = new CRUD($DB_con);
$Crud->query($sql);
$rows = $Crud->resultSet();
// die(var_dump($row));
// $lease_id  = $_GET['lid'];

foreach ($rows as $row) {
    $lease_id = $row['id'];
    $tenants = $DB_tenant->getTenantForLeaseId($lease_id);
    $tenant_ids = explode(",", $tenants[0]);

    // echo (var_dump($tenant_ids));
    foreach ($tenant_ids as $tenant_id) {
        $welcome_lease = new WelcomeNtf($tenant_id);
        //var_dump($welcome_test);
        $welcome_lease->send_welcome_email();
    }
}