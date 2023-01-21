<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (empty($_GET['pdf'])) {
    $pdf = 0;
    session_start();
} else {
    $pdf = 1;
} ?>
<!DOCTYPE html>
<html>


<head>
    <title>Renewal Notice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <? if (empty($pdf)) { ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <? } ?>
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
    <?
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

    $tenant_id = (!empty($_POST['tenant_id']) ? $_POST['tenant_id'] : $_GET['tenant_id']);
    $lease_id = (!empty($_POST['lease_id']) ? $_POST['lease_id'] : $_GET['lease_id']);
    $is_signed = false;
    $PDF_file_name = "../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf";

    if (empty($_POST['submit'])) { // NOT Submitted -> 1-1.Already in history 2. New Page
        console_log("Not Submitted");
        // Check if already signed
        $results_history = $DB_tenantLease->getHistoryDetails($tenant_id, $lease_id, 1);
        if (!empty($results_history)) { // 1-1.Already in history
            console_log("1-1.Already in history");
            $is_signed = true;
            $results = $results_history;
            $sign_date = $results['open_datetime'];
            extract($results);
    ?>
            <div class="container"><br /><br /><br />
                <p>You have already signed the lease.
                    <? if (file_exists("../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf")) {
                        console_log("<h1>1.1 File Exist</h1>"); ?>
                        <a target='_blank' href=<?= $PDF_file_name ?>>Download the sign PDF</a>
                    <? } else {
                        console_log("<h1>1.1 File Not Exist</h1>");
                        //show renewal contents
                        require_once 'renewal_notice_content.php';
                        generatePDF($PDF_file_name, $text); ?>
                        <a target='_blank' href=<?= $PDF_file_name ?>>Download the sign PDF</a>
                </p>
            </div>
    <?
                    }
                    die();
                } else { // not find already 2. New Page
                    console_log("not find already 2. New Page ");
                    $sign_date = date("Y-m-d");
                    $results_renew = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
                    $results = $results_renew;
                    extract($results);

                    $sql = "insert into history (table_id, user_id, history_type_id, open_datetime, subject, comments, client_data, employee_id,
    company_id) VALUES ($lease_id, $tenant_id, 11, '" . $sign_date . "', 'Open Renewal Notification',
    '$comment',:client_data,$employee_id,$company_id)"; // Tenant Open Notification
                    console_log($sql);
                    $Crud->query($sql);
                    $Crud->bind("client_data", $client_data);
                    $Crud->execute();
                    console . log("add to history view");

                    if (empty($results_renew)) {
                        die("Wrong Data");
                    }
                }

                // var_dump($results);



            } else { // it is submitted
                console_log("it is submitted");
                //var_dump($_POST);
                $results_renew = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
                $results = $results_renew;
                extract($results);
                extract($_POST);
                $sign_date = date("Y-m-d H:i:s");
                switch ($accept) {
                    case 0:
                        $comment = "NOT Accept Renewal";
                        $lease_status_id = 9;
                        break;
                    case 1:
                        $comment = "Accept Renewal";
                        $lease_status_id = 10;
                        break;
                    case 2:
                        $comment = "Know Someone interested: " . $phone;
                        $lease_status_id = 9;
                        break;
                }
                $sql = "insert into history (table_id, user_id, history_type_id, open_datetime, subject, comments, client_data, employee_id,
    company_id) VALUES ($lease_id, $tenant_id, $lease_status_id, '" . $sign_date . "', 'Renewal Request',
    '$comment','$client_data',$employee_id,$company_id)";
                $Crud->query($sql);
                $Crud->execute();
                // $DB_Request->insertHistory($tenant_id,$lease_status_id,$lease_id,'','Renewal Request','',$comment,$client_data,'');
                // $lastInsertId=$Crud->lastInsertId();
                $signatureData = $_POST['signatureData'];
                $encoded_image = explode(",", $signatureData)[1];
                $decoded_image = base64_decode($encoded_image);
                file_put_contents(
                    "../../files/tenant_signatures/renew_signature_l" . $lease_id . "_t" . $tenant_id . ".png",
                    $decoded_image
                );

                $sql = "update lease_infos set lease_status_id=$lease_status_id where id=$lease_id";
                $Crud->query($sql);
                $Crud->execute();
                require_once 'renewal_notice_content.php';
                generatePDF($PDF_file_name, $text);

    ?>
    <div class="container"><br /><br /><br />Thanks for your collabortion. You can close this window <br />
        <a target="_blank" href=<?= "../../files/renewal_notice/renewal_notice_l" . $lease_id . "_t" . $tenant_id . ".pdf" ?>>Download
            the sign PDF</a>
        <!-- <button class="btn btn-primary" onclick="self.close()">Close</button> -->
    </div>
    <? if ($accept == 1 || $accept == 2) { ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <form id="renewForm" action="../renew_lease.php" method="post">
            <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
            <input type="hidden" name="length_of_lease" value="12">
            <input type="hidden" name="lease_amount" value="<?= $total_amount ?>">
            <input type="hidden" name="parking_amount" value="<?= $parking_amount ?>">
            <input type="hidden" name="storage_amount" value="<?= $storage_amount ?>">
            <input type="hidden" name="start_date_new" value="<?= date("Y-m-d", strtotime($next_start_date)) ?>">
            <input type="hidden" name="end_date_new" value="<?= date("Y-m-d", strtotime($next_end_date)) ?>">
            <input type="hidden" name="move_in_date" value="<?= date("Y-m-d", strtotime($next_start_date)) ?>">
            <input type="hidden" name="move_out_date_new_datepicker" value="<?= date("Y-m-d", strtotime($next_end_date)) ?>">
            <input type="hidden" name="comments" value="Renewed by signed on tenant portal">
            <input type="hidden" name="renew_parking_lease_choice" value="1">
            <input type="hidden" name="submitted" value="submitted">
        </form>
        <script>
            var data = $('#renewForm').serializeArray();
            $.post("../renew_lease.php", data, function(result) {
                alert(result);
            });
        </script>
    <?
                    console_log("Renew ajax submitted");
                } ?>
</body>
<?
            }

?>
<? if (empty($pdf)) {
    console_log("No PDF -> Generate Form for renewal_notice.php");
?>

    <form action="renewal_notice.php" method="post" enctype="multipart/form-data" name="rform" id="rform">
        <input type="hidden" name="tenant_id" value="<?= $tenant_id ?>">
        <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
        <input type="hidden" name="company_id" value="<?= $company_id ?>">
        <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
        <div class="container">
        <? }
    //show renewal contents
    require_once 'renewal_notice_content.php';
    echo $text;
    if (empty($pdf)) { ?>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
            <?
            console_log("Generate Signature lease_status_id=$lease_status_id");
            if (empty($pdf) && in_array($lease_status_id, array(1, 7))) { ?>
                <script>
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
        <? } // if(empty($pdf) && in_array($lease_status_id,array(1,7))){


        } //if(empty($pdf)){
        ?>
        </body>


</html>

<?
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
    echo $js_code;
}
?>