<?php
// #Execute Payment Sample
// This is the second step required to complete
// PayPal checkout. Once user completes the payment, paypal
// redirects the browser to "redirectUrl" provided in the request
// This sample will show you how to execute the payment
// that has been approved by
// the buyer by logging into paypal site
// You can optionally update transaction
// information by passing in one or more transactions.
// API used: POST '/v1/payments/payment/<payment-id>/execute'.

require 'paypal/paypal/rest-api-sdk-php/sample/bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

error_reporting(E_ALL);

if (isset($_GET['success']) && $_GET['success'] == 'true') {

    #-------------------------  Do Not Change, Paypal Core ----------------------

    // Get the payment Object by passing paymentId
    // payment id was previously stored in session in
    // CreatePaymentUsingPayPal.php
    $paymentId = $_GET['paymentId'];
    $payment = Payment::get($paymentId, $apiContext);
    // ### Payment Execute
    // PaymentExecution object includes information necessary
    // to execute a PayPal account payment.
    // The payer_id is added to the request query parameters
    // when the user is redirected from paypal back to your site
    $execution = new PaymentExecution();
    $execution->setPayerId($_GET['PayerID']);
    // Add the above transaction object inside our Execution object.
    //  $execution->addTransaction($transaction);
    try {
        // Execute the payment
        // (See bootstrap.php for more on `ApiContext`)
        $result = $payment->execute($execution, $apiContext);
        $p_response = json_decode($payment);
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        // ResultPrinter::printResult("Executed Payment", "Payment", $payment->getId(), $execution, $result);
        $payment = Payment::get($paymentId, $apiContext);

    } catch (Exception $ex) {
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //if (!empty($ex->getData())){
        //    $data = json_decode($ex->getData());
        //    echo "Error: " . $data->message;
        //}else {
        //    echo "Error:" . $ex;
        //}
        // ResultPrinter::printError("Executed Payment", "Payment", null, null, $ex);
        header("Location: payment_fail.php");
        exit(1);
    }
    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    // ResultPrinter::printResult("Get Payment", "Payment", $payment->getId(), null, $payment);


    #---------------------------- Business Logic -------------------------------

    $p_id = $p_response->id;
    $p_state = $p_response->state;
    $p_create_time = date('Y-m-d H:i:s', strtotime($p_response->create_time));

    $p_invoice_number = $p_response->transactions[0]->invoice_number;
    $p_description = $p_response->transactions[0]->description;
    $p_payer_id = $p_response->payer->payer_info->payer_id;
    $p_merchant_id = $p_response->transactions[0]->payee->merchant_id;

    $p_price = $p_response->transactions[0]->item_list->items[0]->price;
    $p_quantity = $p_response->transactions[0]->item_list->items[0]->quantity;
    $p_total = $p_price * $p_quantity;

    $payment_record_id = $p_sku = $p_response->transactions[0]->item_list->items[0]->sku;
    $p_sku_2 = $p_response->transactions[0]->item_list->items[1]->sku;
    $p_c_f = $p_response->transactions[0]->item_list->items[1]->price;
    $p_paid_amount = $p_response->transactions[0]->amount->total;   //convenience fee + payment amount


    if (trim($p_sku_2) == 'balance') {
        $p_payment_method = 9;
        $payment_method = 'Paypal/Balance';
    } else {
        $p_payment_method = 2;    //credit card in db
        $payment_method = 'Paypal/Credit card';
    }

    include_once("../../pdo/dbconfig.php");


    # --------------------------- SERVICES PAYMENT ----------------------------

    if (strpos($p_invoice_number, 'SEVS') !== false) {

        $services_payment_id = $p_sku;
        $services_payment_info = $DB_payment->get_services_payment_info($services_payment_id);
        $user_id = $services_payment_info['user_id'];
        $service_type = $services_payment_info['service_type'];
        $services_price = $services_payment_info['service_price'];
        $services_count = $services_payment_info['services_count'];

        //add record in paypal tables
        $paypal_transaction_id = $DB_payment->add_paypal_transaction_record($p_id, $p_state, $p_create_time, $p_invoice_number, $p_payer_id, $p_merchant_id, $p_total, $p_description);

        //change services payment record
        $DB_payment->update_services_payment_info_paypal($p_create_time, $p_invoice_number, 1, $paypal_transaction_id, $p_payment_method, 4, $services_payment_id);
    }

    #---------------------------  Kijiji payment ------------------------------

    if (strpos($p_invoice_number, 'KJJ') !== false) {

        $kijiji_payment_id = $p_sku;
        $kjj_payment_info = $DB_payment->get_kijiji_payment_info_by_id($kijiji_payment_id);
        $buy_slots_price = $kjj_payment_info['buy_slots_price'];
        $buy_slots_count = $kjj_payment_info['buy_slots_count'];
        $total_amount = $kjj_payment_info['payment_amount'];
        $employee_id = $kjj_payment_info['employee_id'];
        $slots_period = $total_amount / $buy_slots_price / $buy_slots_count;
        $due_time = date('Y-m-d H:i:s', strtotime($p_create_time . ' + ' . $slots_period . ' month '));

        //add record in paypal tables
        $paypal_transaction_id = $DB_payment->add_paypal_transaction_record($p_id, $p_state, $p_create_time, $p_invoice_number, $p_payer_id, $p_merchant_id, $p_total, $p_description);

        //change kijiji payment record
        $DB_payment->update_kijiji_payment_info_paypal($kijiji_payment_id, $p_create_time, $due_time, $total_amount, 1, $paypal_transaction_id, 4, $p_payment_method);
    }

    #---------------------------  Lease Payment  ------------------------------

    if (strpos($p_invoice_number, 'INV') !== false) {

        $lease_payment_detail_id = $p_sku;

        //add record in paypal tables
        $paypal_transaction_id = $DB_payment->add_paypal_transaction_record($p_id, $p_state, $p_create_time, $p_invoice_number, $p_payer_id, $p_merchant_id, $p_total, $p_description);

        /*
         * Get the comment field value from the first lease_payment_detail record
         */
        $lease_payment_detail_info = $DB_payment->get_lease_payment_detail_info_by_id($lease_payment_detail_id);
        $comment = $lease_payment_detail_info['comments'];

        //update lease payment detail record
        if (empty($comment)) {    //means only one lease payment detail passed
            $DB_payment->update_lease_payment_details_paypal($lease_payment_detail_id, $p_invoice_number, 4, $p_payment_method, $p_create_time, $paypal_transaction_id);
        }
        else {
            $lease_payment_detail_ids_array = explode(',', $comment);
            foreach ($lease_payment_detail_ids_array as $lease_payment_detail_id) {
                $DB_payment->update_lease_payment_details_paypal($lease_payment_detail_id, $p_invoice_number, 4, $p_payment_method, $p_create_time, $paypal_transaction_id);
            }

            //to generate the payment receipt,several lease_payment_details_id should be passed
            $payment_record_id = $comment;
        }
    }

    //--------------------------  Standalone payment page  --------------------
    if (strpos($p_invoice_number, 'SALE') !== false) {

        $sale_record_id = $p_sku;

        //add record in paypal tables
        $paypal_transaction_id = $DB_payment->add_paypal_transaction_record($p_id, $p_state, $p_create_time, $p_invoice_number, $p_payer_id, $p_merchant_id, $p_total, $p_description);

        $DB_payment->update_sale_payment_info_paypal($sale_record_id,$p_create_time,1,$paypal_transaction_id,4,$p_payment_method);
        $payment_record_id = $sale_record_id;
    }

    ?>
  <!--  switch to success page  -->
  <form name="success_form" id="success_form" action='payment_success.php' method='POST'>
    <input type='hidden' name='gateway' value='p'>
    <input type='hidden' name='amount' value='<?= $p_paid_amount ?>'>
    <input type='hidden' name='convenience_fee' value='<?= $p_c_f ?>'>
    <input type="hidden" name="payment_method" value="<?= $payment_method ?>">
    <input type="hidden" name="timestamp" value="<?= $p_create_time ?>">
    <input type="hidden" name="response_order_id" value="<?= $p_invoice_number ?>">
    <input type="hidden" name="p_id" value="<?= $p_id ?>">
    <input type="hidden" name="p_state" value="<?= $p_state ?>">
    <input type="hidden" name="p_payer_id" value="<?= $p_payer_id ?>">
    <input type="hidden" name="payment_record_id" value="<?= $payment_record_id ?>">
  </form>
  <script>success_form.submit();</script>
    <?php
}
else {
    ?>
  <form name="fail_form" id="fail_form" action='payment_fail.php' method='POST'>
    <input type='hidden' name='gateway' value='p'>
    <input type="hidden" name="reason" value="payment_processing_error">
  </form>
  <script>fail_form.submit();</script>
    <?php
    exit;
}
