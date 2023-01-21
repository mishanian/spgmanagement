<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); ?>
<?
if (empty($_GET['hc'])) {
    die("Wrong ID");
} else {
    $hc = $_GET['hc'];
}
include('../../../pdo/dbconfig.php');
$query = "SELECT tenant_id, full_name, email FROM tenant_infos WHERE FIND_IN_SET(tenant_id,(SELECT tenant_ids FROM lease_infos WHERE hash_code='$hc'))";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
// var_dump($rows);

$query = "SELECT LI.id as lease_id FROM lease_infos LI WHERE hash_code='$hc'";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$row = $stmt->fetch(\PDO::FETCH_ASSOC);
$lease_id = $row['lease_id'];
// die(var_dump($row));

include('../../../pdo/Class.Template.php');
include('../sendSMSEmail.php');
$company_id = $_SESSION['company_id'];
$sign_type_id = $_GET['stid'];


if ($sign_type_id == 1) {
    $subject = "Tenant Lease to sign";
    $title = "Sign the Tenant Lease";
    $file_sign = "lease_sign.php";
}

if ($sign_type_id == 2) {
    $subject = "Pre-Authorized Debit (PAD) Agreement to sign";
    $title = "Sign the Pre-Authorized Debit (PAD) Agreement";
    $file_sign = "lease_pad.php";
}

if ($sign_type_id == 3) {
    $subject = "Mandantary Agreement to sign";
    $title = "Mandantary Agreement";
    $file_sign = "lease_agr.php";
}

echo "<b>$subject</b><br>";

foreach ($rows as $row) {
    $tenant_id = $row['tenant_id'];
    $full_name = $row['full_name'];
    $email = $row['email'];
    $link = "https://www.spgmanagement.com/admin/custom/lease/$file_sign?hc=$hc&tid=$tenant_id&ws=t";
    echo "Email Send for Sign to $full_name ($email)<br>Link: <a href='" . $link . "'>$link</a><br>";


    $body = "Your file is ready. Please <a href='$link'> Sign it</a>.";
    $TemplateObj = new Template();
    $email_template = $TemplateObj->emailTemplate($title, '', $full_name, $body, '', $link, 'Sign here', $company_id, 0);
    $smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email, 'Apply', $subject, $email_template, "", "leasing@mgmgmt.ca"); // Send to Tenant


    $IP = $_SERVER['REMOTE_ADDR'];
    $user_info = $_SERVER['HTTP_USER_AGENT'];

    $query = "insert into tenant_signs (tenant_id, lease_id, sign_type_id, is_signed, sign_DT, sign_IP, sign_user_info) VALUES ('$tenant_id', $lease_id, '5', '0', '" . date("Y-m-d H:i:s") . "', '$IP', :smtp_log)";
    //echo $query;
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(':smtp_log', $smtp_log);
    $stmt->execute();
}

?>