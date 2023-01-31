<?
session_start();

if (isset($_SESSION["company_id"])) {
    $company_id = $_SESSION["company_id"];
} else {
    $company_id = 9;
}
if (isset($_SESSION["employee_id"])) {
    $employee_id = $_SESSION["employee_id"];
} else {
    $employee_id = 145;
}
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
include_once $path . 'Class.LeasePayment.php';
$DB_ls_payment = new LeasePayment($DB_con);
if (empty($_GET['id'])) {
    echo "Lease ID is not set!";
    exit();
}
$lease_id = $_GET['id'];
$DB_ls_payment->addLeaseInvoices($lease_id, $employee_id, $company_id);
echo "Lease invoices generated successfully!";