<?php
error_reporting(E_ALL);
session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['UserID'])) {
    $user_id          = $_SESSION['UserID'];
}
$is_admin_payment = isset($_SESSION['AdminPayment']) ? $_SESSION['AdminPayment'] : true;   //true : tenant portal

include("../../pdo/dbconfig.php");
include_once("../../pdo/Class.Payment.php");
$DB_payment = new Payment($DB_con);

if ($is_admin_payment) {
    if (!empty($user_id)) {
        $user_info           = $DB_payment->get_user_info($user_id);
        $purchaser           = $user_info['full_name'];
        $purchaser_telephone = $user_info['mobile'];
        $purchaser_email     = $user_info['email'];
    }
    if ((!isset($purchaser)) || strlen($purchaser) == 0) {
        $purchaser = ' --- ';
    }
} else {
    $forward_payment_token = $_SESSION['ForwardPaymentToken'];
    $payment_forward_info  = $DB_payment->get_payment_forwards_info_by_token($forward_payment_token);
    $DB_payment->deactivate_forward_payment_token($forward_payment_token);
    $purchaser           = $payment_forward_info['forward_to_name'];
    $purchaser_email     = $payment_forward_info['forward_to_email'];
    $purchaser_telephone = $payment_forward_info['forward_to_mobile'];
}

if (strpos($_POST["payment_record_id"], ',') == false) {
    $payment_record_id = $_POST['payment_record_id'];   //string
} else {
    $payment_record_id = explode(',', $_POST["payment_record_id"]); //array
}

//reference number is payment type(like"INV_") + payment record id in checkout(record id in DB)
if (is_array($payment_record_id)) {
    $payment_record_id_str = implode('|', $payment_record_id);
} else {
    $payment_record_id_str = $payment_record_id;
}
$reference_number = explode('-', $_POST['response_order_id'])[0] . '-' . strval($payment_record_id_str);


# kill related field in session, ready to handle next payment from same browser
unset($_SESSION['pay_record_id']);
unset($_SESSION['pay_convenience_fee']);
unset($_SESSION['pay_payment_amount']);
unset($_SESSION['AdminPayment']);
if (!$is_admin_payment) {
    unset($_SESSION['ForwardPaymentToken']);
}

?>
<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin: 30px 30px">
        <div class="col-md-12 col-sm-12" style="margin:40px 30px">
            <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
                <img src="images/payment_success.png" style="max-height: 150px; max-width: 150px;">
            </div>
            <div class="col-md-8 col-sm-8" style="vertical-align: center">
                <h2 style="color: #7CCD7C">Your payment has been approved !</h2>
            </div>
        </div>
        <?php
        if ($_POST['gateway'] == 'm1') {
            $transaction_amount = $_POST['amount'];
            $convenience_fee    = $_POST['convenience_fee'];
            $real_pay_amount    = $transaction_amount - $convenience_fee;
            $payment_method     = 'Moneris / Credit Card';
            $timestamp          = $_POST['timestamp'];
            $response_order_id  = $_POST['response_order_id'];   //invoice number
            $card_holder        = $_POST['card_holder'];
            $card_number        = $_POST['card_number'];
            $bank_approve_code  = $_POST['bank_approve_code'];
            $bank_trans_id      = $_POST['bank_trans_id'];
        ?>

            <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2" style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
                <div class="col-sm-6 col-md-6" style="text-align: left;">
                    <dl style="margin-bottom: 5px">Purchaser :</dl>
                    <dl style="margin-bottom: 5px">Invoice No :</dl>
                    <dl style="margin-bottom: 5px">Reference Number :</dl>
                    <dl style="margin-bottom: 5px">Amount:</dl>
                    <dl style="margin-bottom: 5px">Service Charge :</dl>
                    <dl style="margin-bottom: 5px">Total Paid Amount :</dl>
                    <dl style="margin-bottom: 5px">Payment Method :</dl>
                    <dl style="margin-bottom: 5px">Card Holder :</dl>
                    <dl style="margin-bottom: 5px">Card Number :</dl>
                    <dl style="margin-bottom: 5px">Bank Approval Code :</dl>
                    <dl style="margin-bottom: 5px">Bank Transaction No :</dl>
                    <dl style="margin-bottom: 5px">Date / Time :</dl>
                </div>

                <div class="col-sm-6 col" style="text-align: right;">
                    <dl style="margin-bottom: 5px"><?php echo $purchaser; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $response_order_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $reference_number; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($real_pay_amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($convenience_fee, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($transaction_amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $payment_method; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $card_holder; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $card_number; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $bank_approve_code; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $bank_trans_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $timestamp; ?></dl>
                </div>
            </div>
        <?php
        }

        if ($_POST['gateway'] == 'm2') {
            $transaction_amount = $_POST['amount'];
            $convenience_fee    = $_POST['convenience_fee'];
            $real_pay_amount    = $transaction_amount - $convenience_fee;
            $payment_method     = 'Moneris / Interac';
            $iss_name           = $_POST['iss_name'];
            $iss_conf           = $_POST['iss_conf'];
            $bank_trans_id      = $_POST['bank_trans_id'];
            $bank_approval_code = $_POST['bank_approval_code'];
            $response_code      = $_POST['response_code'];

            $response_field = 'FAILURE' . '  (' . $response_code . ')';

            if ($response_code < 50) {
                $response_field = 'SUCCESS' . '  (' . $response_code . ')';
            }

            $trans_name_arr = explode('_', $_POST['trans_name']);
            $card_type      = '';
            if ($trans_name_arr[0] == 'idebit')
                $card_type = 'Debit';
            $trans_type = ucwords($trans_name_arr[1]);

            $timestamp         = $_POST['timestamp'];
            $response_order_id = $_POST['response_order_id'];   //invoice number
        ?>

            <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2" style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
                <div class="col-sm-6 col-md-6" style="text-align: left;">
                    <dl style="margin-bottom: 5px">Purchaser :</dl>
                    <dl style="margin-bottom: 5px">Invoice No :</dl>
                    <dl style="margin-bottom: 5px">Reference Number :</dl>
                    <dl style="margin-bottom: 5px">Amount :</dl>
                    <dl style="margin-bottom: 5px">Service Charge :</dl>
                    <dl style="margin-bottom: 5px">Total Paid Amount :</dl>
                    <dl style="margin-bottom: 5px">Payment Method :</dl>
                    <dl style="margin-bottom: 5px">Card Type :</dl>
                    <dl style="margin-bottom: 5px">Issuer Name :</dl>
                    <dl style="margin-bottom: 5px">Issuer Confirmation :</dl>
                    <dl style="margin-bottom: 5px">Bank Transaction No :</dl>
                    <dl style="margin-bottom: 5px">Transaction Type :</dl>
                    <dl style="margin-bottom: 5px">Authorization Code :</dl>
                    <dl style="margin-bottom: 5px">Response Code / Result :</dl>
                    <dl style="margin-bottom: 5px">Date / Time :</dl>
                </div>

                <div class="col-sm-6 col" style="text-align: right;">
                    <dl style="margin-bottom: 5px"><?php echo $purchaser; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $response_order_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $reference_number; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($real_pay_amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($convenience_fee, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($transaction_amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $payment_method; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $card_type; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $iss_name; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $iss_conf; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $bank_trans_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $trans_type; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $bank_approval_code; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $response_field; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $timestamp; ?></dl>
                </div>
            </div>
        <?php
        }

        if ($_POST['gateway'] == 'p') {
            $amount            = $_POST['amount'];
            $convenience_fee   = $_POST['convenience_fee'];
            $real_pay_amount   = $amount - $convenience_fee;
            $payment_method    = $_POST['payment_method'];
            $timestamp         = $_POST['timestamp'];
            $response_order_id = $_POST['response_order_id'];  //invoice number
            $p_id              = $_POST['p_id'];
            $_payer_id         = $_POST['p_payer_id'];
            $p_state           = $_POST['p_state'];
        ?>

            <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2" style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
                <div class="col-sm-6 col-md-6" style="text-align: left;">
                    <dl style="margin-bottom: 5px">Purchaser :</dl>
                    <dl style="margin-bottom: 5px">Invoice No :</dl>
                    <dl style="margin-bottom: 5px">Reference Number :</dl>
                    <dl style="margin-bottom: 5px">Amount :</dl>
                    <dl style="margin-bottom: 5px">Service Charge :</dl>
                    <dl style="margin-bottom: 5px">Total Paid Amount :</dl>
                    <dl style="margin-bottom: 5px">Payment Method :</dl>
                    <dl style="margin-bottom: 5px">Paypal transaction No :</dl>
                    <dl style="margin-bottom: 5px">Paypal Transaction Status :</dl>
                    <dl style="margin-bottom: 5px">Paypal Payer ID :</dl>
                    <dl style="margin-bottom: 5px">Date / Time :</dl>
                </div>

                <div class="col-sm-6 col" style="text-align: right;">
                    <dl style="margin-bottom: 5px"><?php echo $purchaser; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $response_order_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $reference_number; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($real_pay_amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px">
                        <?php echo '$' . number_format(round($convenience_fee, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo '$' . number_format(round($amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $payment_method; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $p_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $p_state; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $_payer_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $timestamp; ?></dl>
                </div>
            </div>
        <?php }

        if ($_POST['gateway'] == 'm3') {
            $amount            = $_POST['amount'];
            $convenience_fee   = $_POST['convenience_fee'];
            $real_pay_amount   = $amount - $convenience_fee;
            $payment_method    = "Cash";
            $timestamp         = $_POST['timestamp'];
            $response_order_id = $_POST['response_order_id'];  //invoice number
        ?>

            <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2" style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
                <div class="col-sm-6 col-md-6" style="text-align: left;">
                    <dl style="margin-bottom: 5px">Purchaser :</dl>
                    <dl style="margin-bottom: 5px">Invoice No :</dl>
                    <dl style="margin-bottom: 5px">Reference Number :</dl>
                    <dl style="margin-bottom: 5px">Total Paid Amount :</dl>
                    <dl style="margin-bottom: 5px">Payment Method :</dl>
                    <dl style="margin-bottom: 5px">Date / Time :</dl>
                </div>

                <div class="col-sm-6 col" style="text-align: right;">
                    <dl style="margin-bottom: 5px"><?php echo $purchaser; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $response_order_id; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $reference_number; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo '$' . number_format(round($amount, 2), 2) . ' CAD'; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $payment_method; ?></dl>
                    <dl style="margin-bottom: 5px"><?php echo $timestamp; ?></dl>
                </div>
            </div>
        <?php }


        $redirect_url = '../';
        if (strpos($response_order_id, 'INV') !== false) {    // lease payment & from tenant
            $redirect_url         = '../home';
            $all                  = is_array($payment_record_id) ? '&all' : '';
            $download_receipt_url = 'invoice_receipt/invoice_receipt_controller.php?download_receipt' . $all . '&lease_payment_details_id=' . str_replace('|', ',', $payment_record_id_str);
        } elseif (strpos($response_order_id, 'KJJ') !== false) {
            $redirect_url         = '../kijiji_listing.php';
            $download_receipt_url = 'invoice_receipt/invoice_receipt_controller.php?download_kjj_receipt&kjj_payment_id=' . $payment_record_id;
        } elseif (strpos($response_order_id, 'SEVS') !== false) {
            $redirect_url         = '../services.php';
            $download_receipt_url = 'invoice_receipt/invoice_receipt_controller.php?download_services_receipt&services_payment_id=' . $payment_record_id;
        } elseif (strpos($response_order_id, 'SALE') !== false) {
            $redirect_url = '../payment_stand.php';
            $download_receipt_url = 'invoice_receipt/invoice_receipt_controller.php?download_sale_receipt&sale_payment_id=' . $payment_record_id;
        }

        if ($is_admin_payment) {
        ?>
            <div class="col-sm-7 col-md-7 col-sm-offset-2 col-md-offset-2" style="margin-top: 20px;">
                <span>The page will be automatically redirected in&nbsp;&nbsp;<span id="countdown_timer" style="font-size: 16px; color: red;">10</span>&nbsp;&nbsp;seconds</span>
                <span>( <a href="<?php echo $redirect_url; ?>">Redirect Now !</a> )</span>
            </div>
            <div class="col-sm-1 col-md-1" style="margin-top: 20px;">
                <a class="btn btn-success" type="button" href="<?php echo $download_receipt_url; ?>">Print</a>
            </div>
        <?php
        } else {
        ?>
            <div class="col-sm-7 col-md-7 col-sm-offset-2 col-md-offset-2" style="margin-top: 20px;">
                <a class="btn btn-success" type="button" href="<?php echo $download_receipt_url; ?>">Print</a>
            </div>
        <?php } ?>

    </div>
</body>
<script src="../jquery/jquery.min.js"></script>
<?php if ($is_admin_payment) { ?>
    <script type="text/javascript">
        var timer = 10;
        $(document).ready(function() {
            countdown();
        });

        function countdown() {
            if (timer === 0) {
                window.location.replace('<?php echo $redirect_url; ?>');
                return;
            } else {
                $('#countdown_timer').text(timer);
                timer--;
            }
            setTimeout(function() {
                countdown()
            }, 1000)
        }
    </script>
<?php } ?>

</html>
<?php

//---------------------------- Notification -----------------------------
if (strpos($response_order_id, 'KJJ') !== false) {
    include_once('invoice_receipt/Class.KijijiReceipt.php');
    $notification = new KijijiReceipt($payment_record_id);
    $notification->send_receipt_by_email();
}

if (strpos($response_order_id, 'SEVS') !== false) {
    include_once('invoice_receipt/Class.ServicesReceipt.php');
    $notification = new ServicesReceipt($payment_record_id);
    $notification->send_receipt_by_email();
}

if (strpos($response_order_id, 'INV') !== false && $is_admin_payment) {
    include_once('invoice_receipt/Class.Receipt.php');

    if (is_array($payment_record_id)) {
        $notification = new Receipt(implode(',', $payment_record_id), true);
        $notification->send_receipt_by_email();
        $notification->send_receipt_by_sms();
    } else {
        $notification = new Receipt($payment_record_id);
        $notification->send_receipt_by_email();
        $notification->send_receipt_by_sms();
    }
}

if (strpos($response_order_id, 'INV') !== false && !$is_admin_payment) {
    //todo: need to do for many lease_payment_detail_id
    include_once('invoice_receipt/Class.ExternalReceipt.php');
    $notification = new ExternalReceipt($payment_record_id, $purchaser_email, $purchaser);
    $notification->send_receipt_by_email();
}

?>