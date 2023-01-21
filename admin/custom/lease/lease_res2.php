<? if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); ?>

<html>

<head>
    <title>Renewal Notice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">


</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <br>
                <p>Thanks for signing the lease. We will contact you shortly.</p>
                <?
                if (!empty($_GET['company_id'])) {
                    $company_id = $_GET['company_id'];
                } else {
                    $company_id = 9;
                } //SPG Canada

                include('../../../pdo/dbconfig.php');
                // die(print_r($_POST));
                $hc = $_POST['hc'];
                $rid = $_POST['rid'];
                $sign_type_id = $_POST['stid'];
                $IP = $_SERVER['REMOTE_ADDR'];
                $user_info = $_SERVER['HTTP_USER_AGENT'];
                $who_sign = $_POST['who_sign'];

                $query = "select id as lease_id from lease_infos LI where LI.hash_code='$hc'";
                $stmt = $DB_con->prepare($query);
                $stmt->execute();
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                $lease_id = $row['lease_id'];

                if ($who_sign == "t") {
                    $query = "INSERT INTO tenant_signs (tenant_id, lease_id, sign_type_id, is_signed, sign_DT, sign_IP, sign_user_info) VALUES ($rid, $lease_id, $sign_type_id, 1,'" . date("Y-m-d H:i:s") . "', '$IP', :user_info)";
                    // die($query);
                    $stmt = $DB_con->prepare($query);
                    $stmt->bindParam(':user_info', $user_info);
                    $stmt->execute();

                    /*
$query = "update lease_infos set lease_status_id=1 where id=".$lease_id;
$stmt =$DB_con->prepare($query);
$stmt->execute();
*/

                    $query = "select full_name, email from tenant_infos where tenant_id=" . $rid;
                    $stmt = $DB_con->prepare($query);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $row = $rows[0];
                    $tenant_name = $row['full_name'];

                    include('../../../pdo/Class.Template.php');
                    include('../sendSMSEmail.php');
                    $TemplateObj = new Template();

                    $subject = "The lease is signed";
                    $title = "The lease is signed by " . $tenant_name;

                    $bodyStaff1 = "A new <a href='https://www.spgmanagement.com/admin/custom/tenant_portal/lease_res_pdf.php?hc=$hc'>Signed Lease</a> is added.";

                    $email_template = $TemplateObj->emailTemplate($title, '', 'Staff', $bodyStaff1, '', 'https://www.spgmanagement.com/admin/custom/tenant_portal/lease_res_pdf.php?id=$lease_id', 'Lease is signed', $company_id, 0);
                    $smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', 'apply@spg-canada.com', 'Apply', $subject, $email_template, "", "leasing@mgmgmt.ca"); // Send to Manager

                    $IP = $_SERVER['REMOTE_ADDR'];
                    $user_info = $_SERVER['HTTP_USER_AGENT'];

                    $query = "insert into tenant_signs (tenant_id, lease_id, sign_type_id, is_signed, sign_DT, sign_IP, sign_user_info) VALUES ('$rid', '$lease_id', '$sign_type_id', '0', '" . date("Y-m-d H:i:s") . "', '$IP', :smtp_log)";
                    //echo $query;
                    $stmt = $DB_con->prepare($query);
                    $stmt->bindParam(':smtp_log', $smtp_log);
                    $stmt->execute();
                }

                if ($who_sign == "e") {
                    if (!empty($lease_id)) {
                        $query = "update lease_infos set is_signed=1, signDT='" . date("Y-m-d H:i:s") . "', signIP='$IP', sign_user_info='$user_info', sign_employee_id=" . $_SESSION['employee_id'] . " where id=$lease_id";
                        $stmt = $DB_con->prepare($query);
                        $stmt->execute();
                    }


                    $query = "select * from lease_infos where id=$lease_id";
                    $stmt = $DB_con->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $lease_amount = $row['lease_amount'];
                    $start_date = date_create($row['start_date']);
                    $length_of_lease = $row['length_of_lease'];

                    $start_date = new \DateTime($row['start_date']);
                    $end_date = new \DateTime($row['end_date']);
                    $diff = date_diff($start_date, $end_date);
                    $diff = $diff->format("%y") * 12 + $diff->format("%m") + 1;
                    $payment_per_month = $lease_amount; ///$length_of_lease;
                    for ($i = 0; $i < $diff; $i++) {
                        $original_start_date = new \DateTime($row['start_date']);
                        $du_date = $original_start_date->add(new \DateInterval('P' . $i . 'M'));
                        $du_date = $du_date->format('Y-m-d');
                        if ($i == 0) {
                            $invoice_type_id = 1;
                        } else {
                            $invoice_type_id = 2;
                        }
                        $queryInsert = "insert IGNORE into lease_payments (lease_id,invoice_type_id,due_date,lease_amount,total,outstanding) values ($lease_id,$invoice_type_id,'$du_date',$payment_per_month,$payment_per_month,$payment_per_month)";
                        $stmt = $DB_con->prepare($queryInsert);
                        $stmt->execute();
                    }
                    echo "Dear " . $_SESSION['UserFullName'] . ",<br>
    Thanks for siging the lease. <br><a href='lease_page.php?lid=$lease_id'>Click here to come back to lease
    page</a>";
                }

                ?>
            </div>
        </div>
    </div>
</body>

</html>