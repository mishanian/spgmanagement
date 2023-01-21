<?php
include_once("../../../pdo/dbconfig.php");
include_once('../../../pdo/Class.Bill.php');
$DB_bill = new Bill($DB_con);
$Crud = new CRUD($DB_con);

if (!array_key_exists("id", $_POST)) {
    echo json_encode(array("value" => false));
    exit;
}

$paymentPrint = $DB_bill->getPaymentById($_POST['id']);
$billPaid = $DB_bill->getBillByID($paymentPrint['bill_id']);
$signatureDoneBy = 0; // 0 is owner ; 1 is vendor
$resetAction = 0;

// who signed - vendor / owner
if (array_key_exists('who-signed', $_POST) && !is_null($_POST["who-signed"])) {
    $signatureDoneBy = intval($_POST["who-signed"]);
}

if (array_key_exists('reset-action', $_POST) && !is_null($_POST["reset-action"])) {
    $resetAction = intval($_POST["reset-action"]);

    if ($resetAction == 1) {
        $resetUpdateSql = "UPDATE payment_infos SET payment_action_id=1, is_signed=0,is_sent=0,is_printed=0,is_send_vendor=0,is_vendor_signed=0,is_pickup=0 WHERE id=" . $_POST['id'];
        $Crud->query($resetUpdateSql);
        $Crud->execute();
    }
}

/* Get the request data from the Print page AJAX request */
if (isset($_POST) && !empty($_POST)) {

    if (array_key_exists("action", $_POST)) {

        /* Upload the Signature Image */
        if (isset($_FILES)) {
            $error = false;
            $files = array();

            $uploaddir = "../../files/attachments/";
            foreach ($_FILES as $key => $file) {
                $fileName = "reset_signature_payment_" . $_POST["id"] . "_signedby_" . $signatureDoneBy . ".jpeg";

                if (move_uploaded_file($file['tmp_name'], $uploaddir . $fileName)) {
                    $files[$key] = $fileName;
                } else {
                    $error = true;
                }
            }

            if (!$error) {
                echo json_encode(array("value" => true));
            } else {
                echo json_encode(array("value" => false));
            }
        }
    }

}

?>
