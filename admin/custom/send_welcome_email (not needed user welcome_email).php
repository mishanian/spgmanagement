<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (strpos(getcwd(), "custom") == false) {
  $path = "../";
} else {
  $path = "../../";
}
include("$path/pdo/dbconfig.php");
include("$path/pdo/Class.Template.php");
include("sendSMSEmail.php");

$TemplateObj = new Template();

include_once("$path/pdo/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);


if (!isset($_GET['t'])) {
  die("No tenant id");
} else {
  $tenant_id = $_GET['t'];
}
$tenant_infos = $DB_tenant->getTenantInfo($tenant_id);
// die(var_dump($tenant_infos));
$tenant_email = $tenant_infos['email'];
$tenant_name = $tenant_infos['full_name'];
$subject = "Home rental website credential";
$title = "Credentials of Home Rental Website";
$bodyTenant1 = "Your lease is submitted. You can check the status of lease, report the issues online. <br>Your username: <b>" . $tenant_infos['username'] . "</b><br>Your Password: <b>" . $tenant_infos['userpass'] . "</b>";

if (!empty($_GET['company_id'])) {
  $company_id = $_GET['company_id'];
} else {
  $company_id = 9;
} //SPG Canada

// $email_template = $TemplateObj->emailTemplate($title, '', 'Staff', $bodyStaff1, '', "https://www.spgmanagement.com/admin/", 'Login', $company_id, 0);
// $smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', 'apply@spg-canada.com', 'Apply', $subject, $email_template); // Send to Manager

$email_template = $TemplateObj->emailTemplate($title, '', $tenant_name, $bodyTenant1, '', 'https://www.spgmanagement.com/admin/home', 'Login', $company_id, 0);
$smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $tenant_email, $tenant_name, $subject, $email_template); // Send to Tenant
// var_dump($smtp_log);
echo "email sent to $tenant_email";