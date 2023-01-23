<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_GET['pdf'])) {
    $pdf = 0;
} else {
    $pdf = 1;
}
include('./renewal_notice_content.php')
?>
<!DOCTYPE html>
<html>


<head>
    <title>Renewal Notice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php if (empty($pdf)) { ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <?php } ?>
    <style>
    #signature {
        width: 300px;
        height: 200px;
        border: 1px solid black;
    }

    .tbl {
        width: 550pt;
    }
    </style>
    <style media="print">
    p {
        line-height: 150%;
    }
    </style>
</head>


<body>
    <?php
    //die(var_dump($_GET));
    include '../../../pdo/dbconfig.php';
    include '../../../pdo/Class.TenantLease.php';
    $Crud = new CRUD($DB_con);
    $DB_tenantLease = new TenantLease($DB_con);
    include '../../../pdo/Class.Request.php';
    $DB_Request = new Request($DB_con);
    require_once("../GetClientData.php");
    $data = new GetDataPlugin();
    $client_data = array(
        $data->ip(), $data->os(), $data->browser(), $data->geo('country'),
        $data->geo('region'), $data->geo('continent'), $data->geo('city'), $data->agent(), $data->referer(),
        $data->height(), $data->width(), $data->javaenabled(), $data->cookieenabled(), $data->language(), $data->architecture(), $data->geo('logitude'), $data->geo('latitude'), $data->provetor(), $data->getdate()
    );
    $client_data = json_encode($client_data);
    $email = 0;
    $sign = "mgmgmt_sign.jpg";


    // $logo = "";
    $tenant_id = (!empty($_POST['tenant_id']) ? $_POST['tenant_id'] : $_GET['tenant_id']);
    $lease_id = (!empty($_POST['lease_id']) ? $_POST['lease_id'] : $_GET['lease_id']);
    $is_signed = false;
    $PDF_file_name = "../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf";

    if (empty($_POST['submit'])) { // NOT Submitted -> 1-1.Already in history 2. New Page
        console_log("Not Submitted");
        // Check if already signed
        $results_history = $DB_tenantLease->getHistoryDetails($tenant_id, $lease_id, "9,10"); // Not (Approved , Not Approved, Opened)
        // die(var_dump($results_history));
        if (!empty($results_history)) { // 1-1.Already in history
            console_log("1-1.Already in history");
            $is_signed = true;
            $results = $results_history;
            $renewal_notice_date = $results['open_datetime'];
            extract($results); ?>
    <div class="container"><br /><br /><br />
        <p>You have already signed the lease.
            <?php if (file_exists("../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf")) {
                        console_log("<h1>1.1 File Exist</h1>"); ?>
            <a target='_blank' href=<?= $PDF_file_name ?>>Download the sign PDF</a>
            <br><a href='../../home?skip=1'>Home Page</a>
            <?php
                        die();
                    } else {
                        console_log("<h1>1.1 File Not Exist</h1>");
                        //show renewal contents
                        // require_once 'renewal_notice_content.php';
                        $sqlCompany = "select renewal_notification_day, logo, renewal_gap_day from company_infos where id=$company_id";
                        $Crud->query($sqlCompany);
                        $resultSql = $Crud->resultSingle();
                        $renewal_notification_day = $resultSql['renewal_notification_day'];
                        $logo = $resultSql['logo'];
                        $renewal_gap_day = $resultSql['renewal_gap_day'];
                        $end_date_onj = date_create($end_date);
                        $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
                        $last_day_renewal = NULL;
                        if (!empty($renewal_notice_date)) {
                            $last_day_renewal = date_format(date_add(date_create($renewal_notice_date), date_interval_create_from_date_string("+" . $renewal_gap_day . " days")), "Y-m-d");
                        }

                        $is_signed = true;
                        $pdf = 1;
                        $params = array(
                            "lease_id" => $lease_id, "tenant_id" => $tenant_id,
                            "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
                            "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
                            "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date
                        );


                        $text = render_renewal($params);
                        generatePDF($PDF_file_name, $text); ?>
            <a target='_blank' href=<?= $PDF_file_name ?>>Download the generated sign PDF</a><br>
            <a href='../../home?skip=1'>Home Page</a>
        </p>
    </div>
    <?php
                    }
                    die();
                } else { // not find already 2. New Page
                    console_log("not find already 2. New Page ");
                    /*
                    $results_renew = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
                    $results = $results_renew;
                    extract($results);
                    //if (empty($renewal_notice_date) || $renewal_notice_date == "0000-00-00") {
                    $end_date_onj = date_create($end_date);
                    console_log("end date=" . $end_date);
                    $sqlCompany = "select renewal_notification_day, logo, renewal_gap_day from company_infos where id=$company_id";
                    $Crud->query($sqlCompany);
                    list($renewal_notification_day, $logo) = $Crud->resultArray();
                    console_log("New renewal_notification_day=$renewal_notification_day");
                    $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
                    // echo "renewal_notice_date=".$renewal_notice_date;
                    // NOT insert the renewal date
                    //}
                    */
                    $sql = "SELECT LRN.*, LI.lease_status_id, LI.total_amount  from lease_renewal_notice LRN LEFT JOIN lease_infos LI ON LRN.lease_id=LI.id WHERE LRN.lease_id=$lease_id and LRN.tenant_id=$tenant_id";
                    $Crud = new CRUD($DB_con);
                    $Crud->query($sql);
                    $row = $Crud->resultSingle();
                    if (empty($row)) {
                        die("No record found");
                    }
                    $company_id = $row['company_id'];
                    $employee_id = $row['employee_id'];
                    $lease_renewal_notice_id = $row['lease_renewal_notice_id'];
                    $total_amount = $row['total_amount'];


                    console_log("start to add to history view");
                    $sql = "insert into history (table_id, user_id, history_type_id, open_datetime, subject, comments, client_data, employee_id,
    company_id) VALUES ($lease_id, $tenant_id, 11, '" . date("Y-m-d H:i:s") . "', 'Open Renewal Notification',
    '$comment',:client_data,$employee_id,$company_id)"; // Tenant Open Notification
                    // console_log("sql=$sql");
                    $Crud->query($sql);
                    $Crud->bind("client_data", $client_data);
                    $Crud->execute();
                    $sqlupdate = "UPDATE lease_infos SET renewal_notice_date=CURDATE(), renewal_notice_tenant_id=$tenant_id, comments=CONCAT(comments,'- Renewal Notice Date automatically added when it is viewed')
                    WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND id=$lease_id";
                    // echo ($sqlupdate . "<br>");
                    $Crud->query($sqlupdate);
                    $Crud->execute();
                    $sqlupdate = "UPDATE lease_renewal_notice SET renewal_notice_date=CURDATE()
                    WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND lease_renewal_notice_id=$lease_renewal_notice_id";
                    // echo ($sqlupdate . "<br>");
                    $Crud->query($sqlupdate);
                    $Crud->execute();
                    console_log("end of adding to history view");
                }

                // var_dump($results);
            } else { // it is submitted
                console_log("it is submitted");
                // var_dump($_POST);
                // $results_renew = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
                // $results = $results_renew;
                // extract($results);
                $phone = $_POST['phone'];

                $sql = "SELECT LRN.*, LI.lease_status_id, LI.total_amount  from lease_renewal_notice LRN LEFT JOIN lease_infos LI ON LRN.lease_id=LI.id WHERE LRN.lease_id=$lease_id and LRN.tenant_id=$tenant_id";
                $Crud = new CRUD($DB_con);
                $Crud->query($sql);
                $row = $Crud->resultSingle();
                // die(var_dump($row));
                if (empty($row)) {
                    die("No record found");
                }
                $company_id = $row['company_id'];
                $employee_id = $row['employee_id'];
                $tenant_id = $row['tenant_id'];
                $lease_id = $row['lease_id'];
                $end_date = $row['end_date'];
                $renewal_notice_date = $row['renewal_notice_date'];
                $unit_number = $row['unit_number'];
                $address = $row['address'];
                $city = $row['city'];
                $province_name = $row['province_name'];
                $postal_code = $row['postal_code'];
                $tenant_name = $row['tenant_name'];
                $monthly_amount = $row['monthly_amount'];
                $total_amount = $row['total_amount'];

                $lease_renewal_notice_id = $row['lease_renewal_notice_id'];
                // extract($_POST);
                $accept = $_POST['accept'];
                $signed_date = date("Y-m-d H:i:s");
                $sign_update = "";
                switch ($accept) {
                    case 0:
                        $comment = "NOT Accept Renewal";
                        $lease_status_id = 9;
                        $sign_update = ", is_signed=1, signed_date='$signed_date'";
                        break;
                    case 1:
                        $comment = "Accept Renewal";
                        $lease_status_id = 10;
                        $sign_update = ", is_signed=1, signed_date='$signed_date'";
                        break;
                    case 2:
                        $comment = "Know Someone interested: " . $phone;
                        $lease_status_id = 9;
                        $sign_update = ", is_signed=1, signed_date='$signed_date'";
                        break;
                    case 3:
                        $comment = "Skip";
                        $lease_status_id = 12;
                        $sign_update = ", is_signed=1, signed_date='$signed_date'";
                        break;
                }
                $sql = "insert into history (table_id, user_id, history_type_id, open_datetime, subject, comments, client_data, employee_id,
    company_id) VALUES ($lease_id, $tenant_id, $lease_status_id, '" . $signed_date . "', 'Renewal Request',
    '$comment',:client_data,$employee_id,$company_id)";
                $Crud->query($sql);
                $Crud->bind("client_data", $client_data);
                $Crud->execute();
                $lastInsertId = $Crud->lastInsertId();

                $sqlupdate = "UPDATE lease_infos SET renewal_notice_date=CURDATE(), renewal_notice_tenant_id=$tenant_id, comments=CONCAT(comments,'- Renewal Notice Date automatically added when it is viewed')
                WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND id=$lease_id";
                $Crud->query($sqlupdate);
                $Crud->execute();

                $sqlupdate = "UPDATE lease_renewal_notice SET renewal_notice_date=CURDATE(), lease_status_id=$lease_status_id $sign_update
                WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND lease_id=$lease_id"; // and tenant_id=$tenant_id ; update for all tenants
                // echo ($sqlupdate);
                $Crud->query($sqlupdate);
                $Crud->execute();
                if ($accept == 3) {
                    $tenant_sign_file_counter = "_c" . $lastInsertId;
                } else {
                    $tenant_sign_file_counter = "";
                }
                // $DB_Request->insertHistory($tenant_id,$lease_status_id,$lease_id,'','Renewal Request','',$comment,$client_data,'');
                $signatureData = $_POST['signatureData'];
                $encoded_image = explode(",", $signatureData)[1];
                $decoded_image = base64_decode($encoded_image);
                file_put_contents(
                    "../../files/tenant_signatures/renew_signature_l" . $lease_id . "_t" . $tenant_id . $tenant_sign_file_counter . ".png",
                    $decoded_image
                );
                $is_signed = true;
                if ($accept == 3) {
                    die("Thanks for informing us. <a href='../../home?skip=1'>Home Page</a>");
                }
                $sql = "update lease_infos set lease_status_id=$lease_status_id where id=$lease_id";
                $Crud->query($sql);
                $Crud->execute();
                $sqlCompany = "select renewal_notification_day, logo, renewal_gap_day from company_infos where id=$company_id";
                $Crud->query($sqlCompany);
                $resultSql = $Crud->resultSingle();
                $renewal_notification_day = $resultSql['renewal_notification_day'];
                $logo = $resultSql['logo'];
                $renewal_gap_day = $resultSql['renewal_gap_day'];
                console_log("New renewal_notification_day=$renewal_notification_day");
                $end_date_onj = date_create($end_date);
                console_log("end date=" . $end_date);
                $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
                $last_day_renewal = NULL;
                if (!empty($renewal_notice_date)) {
                    $last_day_renewal = date_format(date_add(date_create($renewal_notice_date), date_interval_create_from_date_string("+" . $renewal_gap_day . " days")), "Y-m-d");
                }
                console_log("renewal_notice_date=" . $renewal_notice_date);
                $params = array(
                    "lease_id" => $lease_id, "tenant_id" => $tenant_id,
                    "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
                    "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
                    "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date
                );
                // echo "params=";
                // die(var_dump($params));

                // echo ("start\n");
                $text = render_renewal($params);
                // echo ($text);

                //    require_once 'renewal_notice_content.php';
                generatePDF($PDF_file_name, $text);
    ?>
    <div class="container"><br /><br /><br />Thanks for your collaboration. <a href='../../home?skip=1'>Home Page</a>
        <br />
        <a target="_blank"
            href=<?= "../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf" ?>>Download
            the sign PDF</a>
        <!-- <button class="btn btn-primary" onclick="self.close()">Close</button> -->
    </div>
    <?php if ($accept == 1 || $accept == 2) {
                    $next_start_date = date('Y-m-d', strtotime($end_date . ' + 1 days'));
                    $next_end_date = date('Y-m-d', strtotime($end_date . ' + 365 days'));
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <form id="renewForm" action="../renew_lease.php" method="post">
        <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
        <input type="hidden" name="length_of_lease" value="12">
        <input type="hidden" name="lease_amount" value="<?= $total_amount ?>">
        <input type="hidden" name="start_date_new" value="<?= date("Y-m-d", strtotime($next_start_date)) ?>">
        <input type="hidden" name="end_date_new" value="<?= date("Y-m-d", strtotime($next_end_date)) ?>">
        <input type="hidden" name="move_in_date" value="<?= date("Y-m-d", strtotime($next_start_date)) ?>">
        <input type="hidden" name="move_out_date_new_datepicker"
            value="<?= date("Y-m-d", strtotime($next_end_date)) ?>">
        <input type="hidden" name="comments" value="">
        <!-- This lease is renewed by signed on tenant portal <?= date("Y-m-d") ?> -->
        <input type="hidden" name="renew_parking_lease_choice" value="1">
        <input type="hidden" name="submitted" value="submitted">
        <input type="hidden" name="signed_on_portal" value="1">
    </form>
    <script>
    var data = $('#renewForm').serializeArray();
    $.post("../renew_lease.php", data, function(result) {
        alert(result);
    });
    </script>
    <?php
                    console_log("Renew ajax submitted");
                } ?>
</body>
<?php
            }

?>
<?php if (empty($pdf)) {
    console_log("No PDF -> Generate Form for renewal_notice.php"); ?>

<form action="renewal_notice.php" method="post" enctype="multipart/form-data" name="rform" id="rform">
    <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">
    <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
    <input type="hidden" name="length_of_lease" value="12">
    <input type="hidden" name="lease_amount" value="<?= $total_amount ?>">
    <input type="hidden" name="company_id" value="<?= $company_id ?>">
    <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
    <div class="container">
        <?php
    }
    //show renewal contents
    /*
    $sqlCompany = "select renewal_notification_day, logo, renewal_gap_day from company_infos where id=$company_id";
    $Crud->query($sqlCompany);
    $resultSql = $Crud->resultSingle();
    $renewal_notification_day = $resultSql['renewal_notification_day'];
    $logo = $resultSql['logo'];
    $renewal_gap_day = $resultSql['renewal_gap_day'];
    $end_date_onj = date_create($end_date);
    console_log("end date= $end_date, renewal_gap_day=$renewal_gap_day");
    $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
    $last_day_renewal = date_format(date_add(date_create($renewal_notice_date ? $renewal_notice_date : date("Y-m-d")), date_interval_create_from_date_string("+" . $renewal_gap_day . " days")), "Y-m-d");
    */
    $sql = "SELECT LRN.*, LI.lease_status_id  from lease_renewal_notice LRN LEFT JOIN lease_infos LI ON LRN.lease_id=LI.id WHERE LRN.lease_id=$lease_id and LRN.tenant_id=$tenant_id";
    // echo $sql . "<br>";
    $Crud = new CRUD($DB_con);
    $Crud->query($sql);
    $row = $Crud->resultSingle();
    if (empty($row)) {
        die("No record found");
    }
    extract($row);
    $params = array(
        "lease_id" => $lease_id, "tenant_id" => $tenant_id,
        "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
        "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
        "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date
    );
    // var_dump($params);
    $text = render_renewal($params);
    echo $text;
    if (empty($pdf)) { ?>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
        </script> -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.10/dist/signature_pad.umd.min.js"></script>
        <?php
            console_log("Generate Signature lease_status_id=$lease_status_id");
            if (empty($pdf) && in_array($lease_status_id, array(1, 7))) { ?>
        <script>
        // $(document).ready(function() {
        $(document).ready(function() {

            window.signaturePad = new SignaturePad(document.getElementById('signature-pad'));

            $('#rform').submit(function() {
                var data = window.signaturePad.toDataURL('image/png');
                $('#signatureData').val(data);

                if (!$("input[name='accept']:checked").val()) {
                    alert('Please select any options of renewal or not renewal');
                    return false;
                }
                //alert($('#signatureData').val());
                if (window.signaturePad.isEmpty()) {
                    alert('Please Sign below of the page');
                    return false;
                }


            });
        })
        </script>
        <?php } // if(empty($pdf) && in_array($lease_status_id,array(1,7))){


        } //if(empty($pdf)){
        ?>
        </body>


</html>

<?php
function generatePDF($PDF_file_name, $text)
{
    // Generate PDF
    require_once '../mpdf/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($text);
    $mpdf->Output($PDF_file_name);
    // die("<br>pdf generating2 PDF_file_name=$PDF_file_name");
}

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
?>