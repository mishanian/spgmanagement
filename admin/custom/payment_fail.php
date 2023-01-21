<?php
session_start();
$user_id             = $_SESSION['UserID'];
$pay_convenience_fee = $_SESSION['pay_convenience_fee'];
$pay_payment_amount  = $_SESSION['pay_payment_amount'];

if (isset($_POST['gateway']) && $_POST['gateway'] == 'm') {
	$fail_source = 'moneris';

	$charge_amount = number_format(round($pay_convenience_fee + $pay_payment_amount, 2), 2);

	$response_order_id = $_POST['response_order_id'];

	//reference number is payment type(like"INV_") + payment record id in checkout(record id in DB)
	$reference_number = explode('-', $response_order_id)[0] . '-' . strval($_SESSION["pay_record_id"]);

	$response_code = $_POST['response_code'];

	$trans_name_arr = explode('_', $_POST['trans_name']);
	$card_type      = '';
	if ($trans_name_arr[0] == 'idebit')
		$card_type = 'Debit';
	$trans_type = ucwords($trans_name_arr[1]);

	$response_message = $_POST['response_message'];
	$timestamp        = date("Y-m-d H:i:s");

	$download_receipt_url = 'invoice_receipt/invoice_receipt_controller.php?download_failed_payment&id=' . $response_order_id . '&response_code=' . $response_code . '&message=' . $response_message . '&amount=' . $charge_amount . '&ref=' . $reference_number;
}

elseif (isset($_POST['gateway']) && $_POST['gateway'] == 'p') {
	$fail_source = 'paypal';

	$reason = $_POST['reason'];
	if ($reason == 'payment_processing_error') {
		$prompt = 'We received a exception from Paypal about this transaction.';
	}

}
else {
	$fail_source = 'paypal';
	$prompt      = 'There were some exceptions on loading Paypal gateway.';
}


$redirect_url = '../';

if ($user_id > 100000 && $user_id < 200000) {   //from tenant
	$redirect_url = '../tenant_portal.php';
}
elseif ($user_id < 100000) {    //from employee

	$redirect_url = '../kijiji_listing.php';

	$response_order_id = $_POST['response_order_id'];

	/* if the payment is a standalone payment */
	if (strpos($response_order_id, "SALE") !== false) {
		$redirect_url = '../payment_stand.php';
	}
	else {
		$redirect_url = '../../';
	}
}


# kill related filed in session, ready to handle next payment from same browser
unset($_SESSION['pay_record_id']);
unset($_SESSION['pay_convenience_fee']);
unset($_SESSION['pay_payment_amount']);
?>

<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<style>
    .non-display {
        display: none;
    }
</style>
<body>
<div class="container" style="margin: 30px 30px">
    <div class="col-md-12 col-sm-12" style="margin:30px 30px">
        <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
            <img src="images/payment_fail.png" style="max-height: 150px; max-width: 150px;">
        </div>
        <div class="col-md-8 col-sm-8" style="margin-top: 38px;">
            <h2 style="color: #5bc0de">Fail to Process Payment</h2>
        </div>
    </div>

	<?php if ($fail_source == 'moneris') { ?>

        <div class="col-md-10 col-sm-10 col-sm-offset-1 col-md-offset-1" style="font-size: 16px;margin-top: 10px;">
            <p>There were some error during processing your payment. This error may be due to incorrect online banking
               login or exceeding
               your limit. Please try the payment again, If you still fail to pay, please contact with us.</p>
        </div>

        <div class="col-sm-10 col-md-10 col-sm-offset-1 col-md-offset-1"
             style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
            <div class="col-sm-6 col-md-6" style="text-align: left;">
                <dl style="margin-bottom: 5px">Order ID :</dl>
                <dl style="margin-bottom: 5px">Transaction Amount :</dl>
                <dl style="margin-bottom: 5px">Card Type :</dl>
                <dl style="margin-bottom: 5px">Response Code :</dl>
                <dl style="margin-bottom: 5px">Message :</dl>
                <dl style="margin-bottom: 5px">Comments :</dl>
            </div>

            <div class="col-sm-6 col" style="text-align: right;">
                <dl style="margin-bottom: 5px"><?= $response_order_id ?></dl>
                <dl style="margin-bottom: 5px"><?= '$' . $charge_amount . ' CAD' ?></dl>
                <dl style="margin-bottom: 5px"><?= $card_type ?></dl>
                <dl style="margin-bottom: 5px"><?= $response_code ?></dl>
                <dl style="margin-bottom: 5px"><?= $response_message ?></dl>
                <dl style="margin-bottom: 5px">The account was not charged, because the transaction failed.</dl>
                <dl style="margin-bottom: 5px">(If it was charged, please contact with us)</dl>
            </div>
        </div>

	<?php } else { ?>

        <div class="col-md-10 col-sm-10 col-sm-offset-1 col-md-offset-1" style="font-size: 16px;margin-top: 10px;">
            <p><?php echo $prompt; ?></p>
            <p>Please try the payment again later, If you still fail to pay, please contact with us.</p>
        </div>

	<?php } ?>

    <div class="col-sm-7 col-md-7 col-sm-offset-1 col-md-offset-1" style="margin-top: 20px;">
        <span>The page will be automatically redirected in&nbsp;&nbsp;<span id="countdown_timer"
                                                                            style="font-size: 16px; color: red;">10</span>&nbsp;&nbsp;seconds</span>
        <span>( <a href="<?php echo $redirect_url; ?>">Redirect Now !</a> )</span>
    </div>

    <div class="col-sm-1 col-md-1 col-sm-offset-2 col-md-offset-2 <?php if ($fail_source == 'paypal') echo 'non-display'; ?>"
         style="margin-top: 20px;">
        <a class="btn btn-success" type="button" href="<?php echo $download_receipt_url; ?>">Print</a>
    </div>


</div>
</body>

<script src="../jquery/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
    var timer = 10;

    $(document).ready(function () {
        countdown();
    });

    function countdown() {
        if (timer === 0) {
            window.location.replace('<?php echo $redirect_url;?>');
            return;
        }
        else {
            $('#countdown_timer').text(timer);
            timer--;
        }

        setTimeout(function () {
            countdown()
        }, 1000)
    }

</script>
</html>
