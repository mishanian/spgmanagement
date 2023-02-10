<?php
error_reporting(-1);
ini_set('display_errors', 'On');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pdf = 1;
$email = 0;
include('./renewal_notice_content.php')
?>
<!DOCTYPE html>
<html>


<head>
    <title>Renewal Notice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    table {
        width: 100%;
    }

    #signature {
        width: 300px;
        height: 200px;
        border: 1px solid black;
    }
    </style>
    <style media="print">
    p {
        line-height: 150%;
    }
    </style>
</head>



<body>
    <div class="container"><br /><br />
        <?php
        include '../../../pdo/dbconfig.php';
        include '../../../pdo/Class.TenantLease.php';

        $Crud = new CRUD($DB_con);
        $lease_id = (!empty($_POST['lease_id']) ? $_POST['lease_id'] : $_GET['lease_id']);
        $tenant_id = (!empty($_POST['tenant_id']) ? $_POST['tenant_id'] : $_GET['tenant_id']);

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
            "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "next_length_of_lease" => $next_length_of_lease, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
            "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
            "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 0, "renewal_letter_date" => $renewal_letter_date,
            "terms_en" => $terms_en, "terms_fr" => $terms_fr
        );
        // die(var_dump($params));
        $text = render_renewal($params);


        //     $sql = "select *, MIN(open_datetime) AS first_open_dt, HIS.id as history_id from history HIS left join history_types HT on HIS.history_type_id=HT.id
        // where user_id=$tenant_id and table_id=$lease_id and history_type_id in (9,10,11,12,13)
        // GROUP BY DATE(open_datetime),HIS.history_type_id  order by HIS.open_datetime DESC";
        $sql = "select *, HIS.id as history_id from history HIS left join history_types HT on HIS.history_type_id=HT.id
        where user_id=$tenant_id and table_id=$lease_id and history_type_id in (9,10,11,12,13)
        order by HIS.open_datetime DESC";
        // echo ($sql);
        $Crud->query($sql);
        $rowSigns = $Crud->resultSet();
        // echo "Count=" . count($rowSigns);
        $text .= "<table cellpadding=5 cellspacing=1 border=1><tr><th>Ref</th><th>Date</th><th>Description</th><th>Sign</th><th>IP</th></tr>";
        if (empty($rowSigns)) {
            $text .= "<tr><td colspan=5>No Record found</td></tr>";
        }
        // die(var_dump($rows));
        foreach ($rowSigns as $rowSign) {
            $client_data = json_decode($rowSign['client_data']);
            $ip = $client_data[0];
            if ($rowSign['history_type_id'] == 12) {
                $tenant_sign_file_counter = "_c" . $rowSign['history_id'];
            } else {
                $tenant_sign_file_counter = "";
            }
            $text .= "<tr><td>" . $rowSign['history_id'] . "</td><td>" . $rowSign['open_datetime'] . "</td><td>" . $rowSign['name'] . "</td><td>";
            if (in_array($rowSign['history_type_id'], [9, 10, 12])) {
                $text .= "<img src='../../files/tenant_signatures/renew_signature_l" . $lease_id . "_t" . $tenant_id  . $tenant_sign_file_counter . ".png'>";
            } else {
                $text .= " No Sign ";
            }
            $text .= "</td><td>$ip</td></tr>\n";
        }
        $text .= "</table><br>";
        echo $text;
        // generatePDF($PDF_file_name, $text);


        function generatePDF($PDF_file_name, $text)
        {
            // Generate PDF
            require_once '../mpdf/vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($text);
            $mpdf->Output($PDF_file_name);
        }

        function console_log($output, $with_script_tags = true)
        {
            $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
                ');';
            if ($with_script_tags) {
                $js_code = '<script>' . $js_code . '</script>';
            }
            //  echo $js_code;
        }
        ?>