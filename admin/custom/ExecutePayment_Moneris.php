<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
}
else {
    $path = "../../pdo/";
}
include_once($path . 'dbconfig.php');
include_once($path . '/Class.Payment.php');
$DB_payment=new Payment($DB_con);
// foreach ($_POST as $key => $value){
//    echo $key."=".$value."<br>\n";
// }
// die();

$response_order_id = $_POST['response_order_id'];    //invoice number
$date_stamp = $_POST['date_stamp'];
$time_stamp = $_POST['time_stamp'];
$convenience_fee = isset($_POST['convenience_fee']) ? $_POST['convenience_fee'] : 0;
$cf_success = isset($_POST['cf_success']) ? $_POST['cf_success'] : null;
$cf_fee_rate = isset($_POST['cf_fee_rate']) ? $_POST['cf_fee_rate'] : 0;
$cf_fee_type = isset($_POST['cf_fee_type']) ? $_POST['cf_fee_type'] : null;
$charge_total = $_POST['charge_total'];
$bank_transaction_id = $_POST['bank_transaction_id'];
$bank_approval_code = $_POST['bank_approval_code'];
$issname = isset($_POST['ISSNAME']) ? $_POST['ISSNAME'] : null;
$issconf = isset($_POST['ISSCONF']) ? $_POST['ISSCONF'] : null;
$response_code = $_POST['response_code'];
$iso_code = $_POST['iso_code'];
$trans_name = $_POST['trans_name'];
$cardholder = $_POST['cardholder'];
$f4l4 = $_POST['f4l4'];
$card = $_POST['card'];
$expiry_date = $_POST['expiry_date'];
$result = $_POST['result'];
$message = $_POST['message'];
$response_order_id_explode = explode('-', $response_order_id);
$payment_record_id = $cust_id = $response_order_id_explode[sizeof($response_order_id_explode)-1]; //end of array is payment record id

//create datetime
$create_date_time = $date_stamp . " " . $time_stamp;


if (isset($_POST['ISSCONF']) || isset($_POST['ISSNAME'])) {   //interac
    $payment_method = 1;
    $gateway = 'm2';
} else {    //credit card
    $payment_method = 2;
    $gateway = 'm1';
}

if (isset($response_code) && $response_code < 50 && !is_null($response_code) && (!is_null($bank_transaction_id) || strtoupper($bank_transaction_id) != 'NULL') && (!is_null($bank_approval_code) || strtoupper($bank_approval_code) != 'NULL') ) {

    #--------------------------- Services Payment -------------------------------

    if (strpos($response_order_id, 'SEVS') !== false) {
        $services_payment_id = $cust_id;
        $payment_date_time = $create_date_time;

        $services_payment_info = $DB_payment->get_services_payment_info($services_payment_id);
        $c_f = $services_payment_info['convenience_fee'];

        //add one record into transactions_moneris table
        $moneris_transaction_id = $DB_payment->add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issname, $issconf, $iso_code, $trans_name, $cardholder, $f4l4, $card, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code);

        //change services payment record
        $DB_payment->update_services_payment_info_moneris($payment_date_time, $response_order_id, 1, $moneris_transaction_id, $payment_method, 5, $services_payment_id);
    }

    #--------------------------- Kijiji Payment -------------------------------

    if (strpos($response_order_id, 'KJJ') !== false) {
        $kijiji_payment_id = $cust_id;
        $payment_date_time = $create_date_time;

        $kijiji_payment_info = $DB_payment->get_kijiji_payment_info_by_id($kijiji_payment_id);
        $buy_slots_price = $kijiji_payment_info['buy_slots_price'];
        $buy_slots_count = $kijiji_payment_info['buy_slots_count'];
        $total_amount = $kijiji_payment_info['payment_amount'];
        $employee_id = $kijiji_payment_info['employee_id'];
        $c_f = $kijiji_payment_info['C_F_amount'];

        $slots_period = $total_amount / $buy_slots_price / $buy_slots_count;
        $due_time = date('Y-m-d H:i:s', strtotime($payment_date_time . ' + ' . $slots_period . ' month '));

        //add one record into transactions_moneris table
        $moneris_transaction_id = $DB_payment->add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issname, $issconf, $iso_code, $trans_name, $cardholder, $f4l4, $card, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code);

        //update kijiji payment detail record
        $DB_payment->update_kijiji_payment_info_moneris($kijiji_payment_id, $response_order_id, $payment_date_time, $due_time, 1, $moneris_transaction_id, 5, $payment_method);
    }

    #---------------------------- Lease Payment -------------------------------

    if (strpos($response_order_id, 'INV') !== false) {

        $lease_payment_detail_id = $cust_id;

        $lease_payment_detail_info = $DB_payment->get_lease_payment_detail_info_by_id($lease_payment_detail_id);
        $comment = $lease_payment_detail_info['comments'];


        //add one record into transactions_moneris table
        $moneris_transaction_id = $DB_payment->add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issname, $issconf, $iso_code, $trans_name, $cardholder, $f4l4, $card, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code);

        //update lease payment detail record
        if (empty($comment)) {    //means only one lease payment detail passed
            $DB_payment->update_lease_payment_details_moneris($lease_payment_detail_id, $response_order_id, 5, $payment_method, $create_date_time, $moneris_transaction_id);
            $c_f = $lease_payment_detail_info['cf_amount'];
        }
        else {
            $lease_payment_detail_ids_array = explode(',', $comment);
            $c_f = 0.0;   //accumulator

            foreach ($lease_payment_detail_ids_array as $lease_payment_detail_id) {
                $DB_payment->update_lease_payment_details_moneris($lease_payment_detail_id, $response_order_id, 5, $payment_method, $create_date_time, $moneris_transaction_id);

                $this_c_f = $DB_payment->get_lease_payment_detail_info_by_id($lease_payment_detail_id)['cf_amount'];
                $c_f += floatval($this_c_f);
            }

            $payment_record_id = $comment;
        }
    }

    #---------------------------- Sale Payment --------------------------------

    if (strpos($response_order_id, 'SALE') !== false) {
        $sale_record_id = $cust_id;

        if(strpos($sale_record_id, 'SALE') == false){
            $sale_record_id = "SALE-".$sale_record_id;
        }

        $payment_date_time = $create_date_time;

        //add one record into transactions_moneris table
        $moneris_transaction_id = $DB_payment->add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issname, $issconf, $iso_code, $trans_name, $cardholder, $f4l4, $card, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code);

        // Update the standalone sale payment record
        $DB_payment->update_sale_payment_info_moneris($sale_record_id,$payment_date_time,1,$moneris_transaction_id, 5, $payment_method);
    }



    # ------------------------ All kinds of business logic deal done --------------------------

    if ($gateway == 'm1') {
        ?>
      <form name="success_form" id="success_form" action='payment_success.php' method='POST'>
        <input type='hidden' name='gateway' value='m1'>
        <input type='hidden' name='amount' value='<?= $charge_total ?>'>
        <input type='hidden' name='convenience_fee' value='<?= $c_f ?>'>
        <input type="hidden" name="card_holder" value="<?= $cardholder ?>">
        <input type="hidden" name="card_number" value="<?= $f4l4 ?>">
        <input type="hidden" name="bank_approve_code" value="<?= $bank_approval_code ?>">
        <input type="hidden" name="bank_trans_id" value="<?= $bank_transaction_id ?>">
        <input type="hidden" name="timestamp" value="<?= $create_date_time ?>">
        <input type="hidden" name="response_order_id" value="<?= $response_order_id ?>">
        <input type="hidden" name="payment_record_id" value="<?= $payment_record_id ?>">
      </form>
      <script>success_form.submit();</script>
        <?php
    } elseif ($gateway == 'm2') {
        ?>
      <form name="success_form" id="success_form" action='payment_success.php' method='POST'>
        <input type='hidden' name='gateway' value='m2'>
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
      </form>
      <script>success_form.submit();</script>
        <?php
    }

} else {      //failed payment
    ?>
  <form name="fail_form" id="fail_form" action='payment_fail.php' method='POST'>
    <input type='hidden' name='gateway' value='m'>
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
