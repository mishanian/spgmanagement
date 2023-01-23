<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pdf = 1;
$email = 0;
$empty = 1;
$logo = "";
$is_signed = 0;
$sign = "";
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
        $lease_id = (!empty($_POST['lease_id']) ? $_POST['lease_id'] : $_GET['lease_id']);
        $tenant_id = (!empty($_POST['tenant_id']) ? $_POST['tenant_id'] : $_GET['tenant_id']);

        $sql = "SELECT LRN.*, LI.lease_status_id  from lease_renewal_notice LRN LEFT JOIN lease_infos LI ON LRN.lease_id=LI.id WHERE LRN.lease_id=$lease_id and LRN.tenant_id=$tenant_id";
        // echo ($sql . "<br>");
        $Crud = new CRUD($DB_con);
        $Crud->query($sql);
        $row = $Crud->resultSingle();
        // die(var_dump($row));

        if (empty($row)) {
            $where = "LI.id=$lease_id";
            $sqlSend = "SELECT LI.id as lease_id, LI.tenant_ids, LI.renewal_notice_date, LI.lease_status_id, LI.employee_id, LI.company_id,
                CI.renewal_notification_day, CI.send_renewal_notification_after_notice, CI.renewal_gap_day,
                CI.send_renewal_notification_after_notice2, building_name, unit_number, `start_date`, end_date,
                BI.address, BI.city, BI.building_id, AI.apartment_id, PV.name as province_name , BI.postal_code ,
                AI.monthly_price as monthly_amount,
                DATEDIFF(end_date, CURDATE())AS datediffrenew ,
                DATEDIFF(renewal_notice_date, CURDATE())AS datediffnotif
                FROM lease_infos LI
                LEFT JOIN company_infos CI ON LI.company_id=CI.id
                LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
                LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
                LEFT JOIN provinces PV ON BI.province_id=PV.id
                WHERE $where
                ";
            $Crud->query($sqlSend);
            $row = $Crud->resultSingle();
            $end_date = $row['end_date'];
            $renewal_notice_date = $row['renewal_notice_date'];
            $unit_number = $row['unit_number'];
            $address = $row['address'];
            $city = $row['city'];
            $province_name = $row['province_name'];
            $postal_code = $row['postal_code'];
            $lease_status_id = $row['lease_status_id'];
            $employee_id = $row['employee_id'];
            $company_id = $row['company_id'];
            $renewal_notification_day = $row['renewal_notification_day'];
            $send_renewal_notification_after_notice = $row['send_renewal_notification_after_notice'];
            $renewal_gap_day = $row['renewal_gap_day'];
            $send_renewal_notification_after_notice2 = $row['send_renewal_notification_after_notice2'];
            $building_name = $row['building_name'];
            $start_date = $row['start_date'];
            $building_id = $row['building_id'];
            $apartment_id = $row['apartment_id'];
            $monthly_amount = $row['monthly_amount'];
            $end_date_onj = date_create($end_date);
            $renewal_letter_date = date_format(date_add($end_date_onj, date_interval_create_from_date_string("-" . $renewal_notification_day . " days")), "Y-m-d");
            $last_day_renewal = NULL;
            if (!empty($renewal_notice_date)) {
                $last_day_renewal = date_format(date_add(date_create($renewal_notice_date), date_interval_create_from_date_string("+" . $renewal_gap_day . " days")), "Y-m-d");
            }


            $sqlTenant = "select full_name as tenant_name, email as tenant_email from tenant_infos where tenant_id=$tenant_id";
            $Crud->query($sqlTenant);
            $rowTenant = $Crud->resultSingle();
            $tenant_name = $rowTenant['tenant_name'];
            $tenant_email = $rowTenant['tenant_email'];
            $params = array(
                "lease_id" => $lease_id, "tenant_id" => $tenant_id,
                "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
                "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
                "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 1, "renewal_letter_date" => $renewal_letter_date
            );
            $text = render_renewal($params);

            // die(var_dump($params));
            $params['building_id'] = $building_id;
            $params['apartment_id'] = $apartment_id;
            $params['employee_id'] = $employee_id;
            $params['company_id'] = $company_id;
            $params['tenant_name'] = str_replace("'", "\'", $params['tenant_name']);
            unset($params['lease_status_id']); //  lease_status_id should get from lease_infos not lease_renewal_notice (to have current lease status )
            unset($params['email']); //  email should get from caller of the renewal_notice_content.php
            unset($params['pdf']); //  email should get from caller of the renewal_notice_content.php
            $sqlInsertInto = "INSERT IGNORE INTO lease_renewal_notice (" . implode(", ", array_keys($params)) . ") VALUES ('" . implode("','", array_values($params)) . "')";
            // echo $sqlInsertInto . "<br>";
            $insertIntoStmt = $DB_con->prepare($sqlInsertInto);
            $insertIntoStmt->execute();
        } else {
            extract($row);
            // $params['building_id'] = $building_id;
            // $params['apartment_id'] = $apartment_id;
            // $params['employee_id'] = $employee_id;
            // $params['company_id'] = $company_id;
            // $params['tenant_name'] = str_replace("'", "\'", $params['tenant_name']);
            $params = array(
                "lease_id" => $lease_id, "tenant_id" => $tenant_id,
                "sign" => $sign, "logo" => $logo, "end_date" => $end_date, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
                "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal" => $last_day_renewal,
                "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed" => $is_signed, "email" => $email, "empty" => 1, "renewal_letter_date" => $renewal_letter_date
            );
            $text = render_renewal($params);
        }




        die($text); // No need to generate PDF, print from chrome is enough
        // generatePDF($PDF_file_name, $text);
        generatePDF("", $text);
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