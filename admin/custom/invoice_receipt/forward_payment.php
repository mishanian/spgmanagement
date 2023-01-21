<?php

/**
 * This file is to handle the recipient's payment request, after recipient click the payment link.
 */

error_reporting(E_ALL);

include_once('../../pdo/dbconfig.php');
$check_token = $_GET['token'];
$payment_forward_info = $DB_payment->get_payment_forwards_info_by_token($check_token);

if (empty($payment_forward_info['id'])) {
  echo 'sorry!the token is invalid!';
} elseif ($payment_forward_info['is_active'] == 0) {
  echo 'soory!the token is expired!';
} else {
  $created_user_id = $payment_forward_info['created_user_id'];
  $created_time = $payment_forward_info['created_time'];
  $product = $payment_forward_info['product'];
  $payment_amount = $payment_forward_info['payment_amount'];

  //parse data param
  $data = $payment_forward_info['data'];

  $data_params = array();


  $params_arr = explode('&', $data);
  foreach ($params_arr as $param_pair) {
    $temp = explode('=', $param_pair);
    $data_params[$temp[0]] = $temp[1];
  }

  $requester_info = $DB_payment->get_user_info($created_user_id);
  $requester_name = $requester_info['full_name'];
  $requester_email = $requester_info['email'];
  $requester_mobile = $requester_info['mobile'];

  session_start();
  $_SESSION['UserID'] = $created_user_id;
  $_SESSION['ForwardPaymentToken'] = $check_token;
?>

<html>
<link rel="stylesheet" type="text/css" href="../bootstrap3/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/forward_payment.css">

<body>
    <header class="main-header ewHorizontal">
        <nav class="navbar navbar-static-top">
            <div class="container-fluid">
                <div class="navbar-header"><a href="#" class="navbar-brand" style="padding: 10px 5px 20px 3px;"><img
                            src="../../images/logo.png" height="60"></a></div>
            </div>
        </nav>
    </header>

    <div class="container">
        <form method="post" action="external_pay_content.php">
            <div class="col-md-offset-2 col-md-8  col-sm-offset-2 col-sm-8">
                <div class="col-md-12 col-sm-12 payment-content text-style" style="margin-bottom: 20px">
                    <h4 class="payment-content-title">Payment Requester Details</h4>
                    <div class="col-md-12 col-sm-12 payment-content-body">
                        <div class="col-sm-6 col-md-6" style="text-align: left;">
                            <dl style="margin-bottom: 5px">Requester Name :</dl>
                            <dl style="margin-bottom: 5px">Requester Email :</dl>
                            <dl style="margin-bottom: 5px">Requester Mobile :</dl>
                        </div>

                        <div class="col-sm-6 col-md-6" style="text-align: right;">
                            <dl style="margin-bottom: 5px"><?php echo $requester_name; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $requester_email; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $requester_mobile; ?></dl>
                        </div>
                    </div>
                </div>
                <?php
          if ($product == 'services') {
            $service_type = $data_params['service_type'];
            $service_price = $data_params['service_price'];
            $service_times = $data_params['service_buy_count'];
          ?>
                <div class="col-md-12 col-sm-12 payment-content text-style" style="margin-bottom: 20px;">
                    <h4 class="payment-content-title">Services Details</h4>
                    <div class="col-md-12 col-sm-12 payment-content-body">
                        <div class="col-sm-6 col-md-6" style="text-align: left;">
                            <dl style="margin-bottom: 5px">SERVICE TYPE :</dl>
                            <dl style="margin-bottom: 5px">SERVICE PRICE :</dl>
                            <dl style="margin-bottom: 5px">SERVICE TIME(S) :</dl>
                        </div>

                        <div class="col-sm-6 col-md-6" style="text-align: right;">
                            <dl style="margin-bottom: 5px"><?php echo ucwords($service_type); ?></dl>
                            <dl style="margin-bottom: 5px">
                                <?php echo '$' . number_format(round($service_price, 2), 2) . '(CAD)'; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $service_times; ?></dl>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="services_buy" value="btn">
                <input type="hidden" name="service_type" value="<?php echo $service_type; ?>">
                <input type="hidden" name="service_buy_count" value="<?php echo $service_times; ?>">
                <?php
          }

          if ($product == 'lease') {
            $lease_payment_id = $data_params['lease_payment_id'];
            $lease_payment_info = $DB_payment->get_invoice_info($lease_payment_id);
            $building_name = $lease_payment_info['building'];
            $unit_number = $lease_payment_info['unit'];

            $due_date = $lease_payment_info['due_date'];
            $first_day = date('Y-m-01', strtotime($due_date));
            $first_day_format = date('Y-m-01', strtotime($due_date));
            $last_day = date('Y-m-d', strtotime("$first_day_format +1 month -1 day"));
          ?>

                <div class="col-md-12 col-sm-12 payment-content text-style"
                    style="padding: 0px; margin: 20px 0px 0px 0px;">
                    <h4 class="payment-content-title">Payment Details</h4>
                    <div class="col-md-12 col-sm-12 payment-content-body">
                        <div class="col-sm-6 col-md-6" style="text-align: left;">
                            <dl style="margin-bottom: 5px">BUILDING :</dl>
                            <dl style="margin-bottom: 5px">UNIT # :</dl>
                            <dl style="margin-bottom: 5px">RENT DUE DATE :</dl>
                            <dl style="margin-bottom: 5px">PERIOD PAID FOR:</dl>
                        </div>

                        <div class="col-sm-6 col-md-6" style="text-align: right;">
                            <dl style="margin-bottom: 5px"><?php echo $building_name; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $unit_number; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $due_date; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $first_day . ' - ' . $last_day ?></dl>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id" value="<?php echo $lease_payment_id; ?>">
                <input type="hidden" name="payment_amount" value="<?php echo $payment_amount; ?>">
                <?php
          }

          if ($product == 'kijiji') {
            // $data = 'slot_count='.$slot_count.'&slots_period='.$slots_period.'&slots_price'.$slots_price;
            $slots_count = $data_params['slot_count'];
            $slots_period = $data_params['slots_period'];
            $slots_price = $data_params['slots_price'];
          ?>

                <div class="col-md-12 col-sm-12 payment-content text-style" style="margin-bottom: 20px;">
                    <h4 class="payment-content-title">Advertisement Slots Details</h4>
                    <div class="col-md-12 col-sm-12 payment-content-body">
                        <div class="col-sm-6 col-md-6" style="text-align: left;">
                            <dl style="margin-bottom: 5px">SLOTS PRICE :</dl>
                            <dl style="margin-bottom: 5px">SLOTS COUNT :</dl>
                            <dl style="margin-bottom: 5px">SLOTS PERIOD :</dl>
                        </div>

                        <div class="col-sm-6 col-md-6" style="text-align: right;">
                            <dl style="margin-bottom: 5px">
                                <?php echo '$' . number_format(round($slots_price, 2), 2) . '(CAD)'; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $slots_count; ?></dl>
                            <dl style="margin-bottom: 5px"><?php echo $slots_period . ' month(s)' ?></dl>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="slot_number" value="<?php echo $slots_count; ?>">
                <input type="hidden" name="slots_period" value="<?php echo $slots_period; ?>">
                <input type="hidden" name="price" value="<?php echo $slots_price; ?>">
                <input type="hidden" name="employee_id" value="<?php echo $created_user_id; ?>">
                <input type="hidden" name="employee_name" value="<?php echo $requester_name; ?>">
                <?php
          }

          ?>
                <!-- SHARED PART : Payment Details -->
                <div class="col-md-12 col-sm-12 pad-top payment-content text-style" style="padding: 0px;">
                    <h4 class="payment-content-title">Payment Details</h4>
                    <div class="col-md-12 col-sm-12 payment-content-body">
                        <div class="col-sm-6 col-md-6" style="text-align: left;">
                            <dl style="margin-bottom: 5px">REQUEST PAYMENT AMOUNT :</dl>
                            <dl style="margin-bottom: 5px">( Not include tax & convenience fee )</dl>
                        </div>

                        <div class="col-sm-6 col-md-6" style="text-align: right;">
                            <dl style="margin-bottom: 5px">
                                <?php echo '$' . number_format(round($payment_amount, 2), 2) . ' (CAD)' ?></dl>
                        </div>

                    </div>
                </div>

                <div class="col-md-12 col-sm-12 pad-top text-style">
                    <button type="submit" class="btn btn-info process_btn">continue to payment</button>
                </div>

            </div>
        </form>
    </div>

    <footer class="main-footer" style="border-top:1px solid #eeeeee; margin-top: 90px; min-height: 40px;">
        <div class="col-md-12 col-sm-12" style="margin-top: 10px;">
            <div class="pull-right hidden-xs"></div>
            <div class="ewFooterText">©2019 SPGManagement. A Product of <a href="http://beaverait.com"
                    target="_blank">Beaver AIT Canada</a>, All rights reserved. - <a href="../contact_us.php"
                    target="_blank">Contact Us</a> - <a href="../terms.php" target="_blank">Terms of Service</a></div>
        </div>
    </footer>
</body>

</html>

<?php
}
?>