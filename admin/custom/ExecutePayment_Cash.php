<?php
error_reporting(E_ALL);
include_once("../../pdo/dbconfig.php");

//foreach ($_POST as $key => $value) {
//	echo $key . "=" . $value . "<br>\n";
//}

$response_order_id         = $_POST['response_order_id'];    //invoice number
$date_stamp                = date("Y-m-d");
$time_stamp                = date("h:i:s");
$convenience_fee           = 0;
$cf_success                = 0;
$cf_fee_rate               = 0;
$cf_fee_type               = null;
$charge_total              = $_POST['charge_total'];
$bank_transaction_id       = 0;
$bank_approval_code        = 0;
$issname                   = null;
$issconf                   = null;
$response_code             = 200;
$iso_code                  = 0;
$trans_name                = 0;
$cardholder                = null;
$f4l4                      = null;
$card                      = null;
$c_f                       = 0;
$expiry_date               = null;
$result                    = null;
$message                   = null;
$response_order_id_explode = explode('-', $response_order_id);
$payment_record_id         = $cust_id = $response_order_id_explode[sizeof($response_order_id_explode) - 1]; //end of array is payment record id

//create datetime
$create_date_time = $date_stamp . " " . $time_stamp;

$payment_method = 3;
$gateway        = 'm3';

if (strpos($response_order_id, 'SALE') !== false) {
	$sale_record_id = $cust_id;

	if (strpos($sale_record_id, 'SALE') == false) {
		$sale_record_id = "SALE-" . $sale_record_id;
	}

	$payment_date_time = $create_date_time;

	//add one record into transactions_moneris table
//	$moneris_transaction_id = $DB_payment->add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issname, $issconf, $iso_code, $trans_name, $cardholder, $f4l4, $card, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code);

	// Update the standalone sale payment record
	$DB_payment->update_sale_payment_info_moneris($sale_record_id, $payment_date_time, 1, "", 1, $payment_method);
}


# ------------------------ All kinds of business logic deal done --------------------------

if ($gateway == 'm3') {
	?>
    <form name="success_form" id="success_form" action='payment_success.php' method='POST'>
        <input type='hidden' name='gateway' value='m3'>
        <input type='hidden' name='amount' value='<?= $charge_total ?>'>
        <input type='hidden' name='convenience_fee' value='<?= $c_f ?>'>
        <input type="hidden" name="iss_name" value="<?= $issname ?>">
        <input type="hidden" name="iss_conf" value="<?= $issconf ?>">
        <input type="hidden" name="bank_trans_id" value="<?= $bank_transaction_id ?>">
        <input type="hidden" name="bank_approval_code" value="<?= $bank_approval_code ?>">
        <input type="hidden" name="response_code" value="<?= $response_code ?>">
        <input type="hidden" name="trans_name" value="<?= $trans_name ?>">
        <input type="hidden" name="timestamp" value="<?= $create_date_time ?>">
        <input type="hidden" name="response_order_id" value="<?= $response_order_id ?>">
        <input type="hidden" name="payment_record_id" value="<?= $payment_record_id ?>">
        <input type="hidden" name="payment_method" value="3">
    </form>
        <script>success_form.submit();</script>
	<?php
}


else {      //failed payment
	?>
    <form name="fail_form" id="fail_form" action='payment_fail.php' method='POST'>
        <input type='hidden' name='gateway' value=m3'>
        <input type="hidden" name="response_order_id" value="<?= $response_order_id ?>">
        <input type="hidden" name="response_code" value="<?= $response_code ?>">
        <input type="hidden" name="trans_name" value="<?= $trans_name ?>">
        <input type="hidden" name="response_message" value="<?= $message ?>">
    </form>
    <script>fail_form.submit();</script>
	<?php
	exit;
}

?>

