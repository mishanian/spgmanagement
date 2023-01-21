<?php
include_once("../../pdo/dbconfig.php");
$invoice_no = $_POST['invoice_no'];
$payment_amount = $_POST['payment_amount'];
$convenience_fee = $_POST['convenience_fee'];
$cust_id = $_POST['cust_id'];       //payment_record_id: id for every payments table,expect lease payment, it is lease_payment_id

session_start();
$_SESSION['dealing_payment_amount'] = round(($convenience_fee + $payment_amount), 2);
?>

<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/interac.css">

<div class="container">
  <div class="col-md-offset-2 col-md-8  col-sm-offset-2 col-sm-8">

    <div class="col-sm-9 col-md-9 pad-top">
      <h3>INTERAC<sup>®</sup> Online Payment</h3>
      <p>
        ® Trade-mark of Interac Inc. Used under licence.
        <a href="http://interac.ca/en/interac-online-consumer" target="_blank">Learn more</a>
      </p>
    </div>

    <div class="col-sm-2 col-md-2 pad-top">
      <div class="interac-logo">
        <img src="../phpimages/Interac_Online_Eng_vert_small_RGB.png" height="72">
      </div>
    </div>
    <?php

    if (strpos($invoice_no, 'SEVS') !== false) {

      # ----------------------- SERVICES PAYMENT ---------------------------

      $service_payment_id = $cust_id;
      $total_amount = floatval(number_format(($convenience_fee + $payment_amount), 2, '.', ''));
      $service_payment_info = $DB_payment->get_services_payment_info($service_payment_id);

      $service_type = $service_payment_info['service_type'];
      $service_price = $service_payment_info['service_price'];
      $service_times = $service_payment_info['services_count'];

    ?>
      <div class="col-md-12 col-sm-12 payment-content text-style" style="padding: 0px; margin: 0px;">
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
            <dl style="margin-bottom: 5px"><?php echo ucwords($service_times); ?></dl>
          </div>
        </div>
      </div>
    <?php
    }

    if (strpos($invoice_no, 'INV') !== false) {

      # ----------------------- LEASE PAYMENT PAYMENT ---------------------------

      $lease_payment_id = $cust_id;
      $total_amount = floatval(number_format(($convenience_fee + $payment_amount), 2, '.', ''));
      $lease_payment_info = $DB_ls_payment->get_lease_info_by_lease_payment_id($lease_payment_id);
      $unit_number = $lease_payment_info['unit'];
      $total_discount = $lease_payment_info['total_discount'];
      $outstanding = $lease_payment_info['outstanding'];
      $due_date = $lease_payment_info['payment_due'];

      $first_day = date('Y-m-01', strtotime($due_date));
      $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
    ?>

      <div class="col-md-12 col-sm-12 payment-content text-style" style="padding: 0px; margin: 0px;">
        <h4 class="payment-content-title">Current Balance Details</h4>
        <div class="col-md-12 col-sm-12 payment-content-body">
          <div class="col-sm-6 col-md-6" style="text-align: left;">
            <dl style="margin-bottom: 5px">PAYMENT DUE DATE :</dl>
            <dl style="margin-bottom: 5px">UNIT # :</dl>
            <dl style="margin-bottom: 5px">PERIOD :</dl>
            <dl style="margin-bottom: 5px">RENT :</dl>
            <dl style="margin-bottom: 5px">OUTSTANDING BALANCE :</dl>
          </div>

          <div class="col-sm-6 col-md-6" style="text-align: right;">
            <dl style="margin-bottom: 5px"><?php echo $due_date; ?></dl>
            <dl style="margin-bottom: 5px"><?php echo $unit_number; ?></dl>
            <dl style="margin-bottom: 5px"><?php echo $first_day . ' - ' . $last_day ?></dl>
            <dl style="margin-bottom: 5px">
              <?php echo '$' . number_format(round($total_discount, 2), 2) . ' (CAD)' ?></dl>
            <dl style="margin-bottom: 5px">
              <?php echo '$' . number_format(round($outstanding, 2), 2) . ' (CAD)' ?></dl>
          </div>
        </div>
      </div>
    <?php
    }

    if (strpos($invoice_no, 'KJJ') !== false) {

      # --------------------------- KIJIJI PAYMENT -------------------------------

      $record_id = $cust_id;
      $kjj_payment_info = $DB_payment->get_kijiji_payment_info_by_id($record_id);
      $slots_price = $kjj_payment_info['buy_slots_price'];
      $slots_count = $kjj_payment_info['buy_slots_count'];
      $employee_id = $kjj_payment_info['employee_id'];
      $slots_period = $payment_amount / $slots_price / $slots_count;

      $start_time = date('Y-m-d', strtotime($kjj_payment_info['created_time']));
      $end_time = date('Y-m-d', strtotime($start_time . ' + ' . $slots_period . ' months'));

    ?>

      <div class="col-md-12 col-sm-12 payment-content text-style" style="padding: 0px; margin: 0px;">
        <h4 class="payment-content-title">Advertisement Slots Details</h4>
        <div class="col-md-12 col-sm-12 payment-content-body">
          <div class="col-sm-6 col-md-6" style="text-align: left;">
            <dl style="margin-bottom: 5px">SLOTS PRICE :</dl>
            <dl style="margin-bottom: 5px">SLOTS COUNT :</dl>
            <dl style="margin-bottom: 5px">SLOTS PERIOD :</dl>
          </div>

          <div class="col-sm-6 col-md-6" style="text-align: right;">
            <dl style="margin-bottom: 5px">
              <?php echo '$' . number_format(round($slots_price, 2), 2) . '(CAD)'; ?>
            </dl>
            <dl style="margin-bottom: 5px"><?php echo $slots_count; ?></dl>
            <dl style="margin-bottom: 5px"><?php echo $start_time . ' - ' . $end_time ?></dl>
          </div>
        </div>
      </div>

    <?php
    }

    //=============================  Payment Details & Interac Gateway ==========================

    ?>
    <!-- SHARED PART : Payment Details -->
    <div class="col-md-12 col-sm-12 pad-top payment-content text-style" style="padding: 0px; margin: 30px 0px 0px 0px;">
      <h4 class="payment-content-title">Payment Details</h4>
      <div class="col-md-12 col-sm-12 payment-content-body">
        <div class="col-sm-6 col-md-6" style="text-align: left;">
          <dl style="margin-bottom: 5px">PAYMENT AMOUNT :</dl>
          <dl style="margin-bottom: 5px">ONLINE CONVENIENCE FEE :</dl>
          <dl style="margin-bottom: 5px">TOTAL :</dl>
        </div>

        <div class="col-sm-6 col-md-6" style="text-align: right;">
          <dl style="margin-bottom: 5px">
            <?php echo '$' . number_format(round($payment_amount, 2), 2) . ' (CAD)' ?></dl>
          <dl style="margin-bottom: 5px">
            <?php echo '$' . number_format(round($convenience_fee, 2), 2) . ' (CAD)'; ?></dl>
          <dl style="margin-bottom: 5px">
            <?php echo '$' . number_format(round($convenience_fee + $payment_amount, 2), 2) . ' (CAD)' ?>
          </dl>
        </div>

      </div>
    </div>

    <?php

    $ps_store_id = "D2LYPtore3";       //TB5Q4tore3               //D2LYPtore3
    $hpp_key = "hp1XQB7ZSOCN";         //hp47323PG74J             //hp1XQB7ZSOCN


    $total_amount = round($convenience_fee + $payment_amount, 2);

    ?>
    <div class="col-sm-12 col-md-12 margin-top-bottom" style="margin: 30px 0px 0px 0px;padding: 0px;">
      <!--      <form action="https://merchant-test.interacidebit.ca/gateway/merchant_test_processor.do" method="post">-->
      <!--        <input type='hidden' name='IDEBIT_INVOICE' value='-->
      <? //= $invoice_no
      ?>
      <!--'>-->
      <!--        <input type='hidden' name='IDEBIT_AMOUNT' value='-->
      <? //= (($convenience_free + $price) * 100)
      ?>
      <!--'>-->
      <!--        <input type='hidden' name='IDEBIT_MERCHNUM' value=''>-->
      <!--        <input type='hidden' name='IDEBIT_CURRENCY' value='CAD'>-->
      <!--        <input type='hidden' name='IDEBIT_FUNDEDURL' value=''>-->
      <!--        <input type='hidden' name='IDEBIT_NOTFUNDEDURL' value=''>-->
      <!--        <input type='hidden' name='IDEBIT_ISSLANG' value='en'>-->
      <!--        <input type='hidden' name='IDEBIT_VERSION' value='1'>-->
      <!--        <button type="button" class="btn btn-info process_interac_btn">Process to Interac<sup>®</sup> Online Service-->
      <!--        </button>-->
      <!--      </form>-->

      <form METHOD="POST" name="moneris_form" id="moneris_form" ACTION="https://esqa.moneris.com/HPPDP/index.php">
        <input TYPE="hidden" NAME="ps_store_id" VALUE="<?= $ps_store_id ?>">
        <input TYPE="hidden" NAME="hpp_key" VALUE="<?= $hpp_key ?>">
        <input type="hidden" name="order_id" VALUE="<?= $invoice_no ?>">
        <input TYPE="hidden" NAME="charge_total" VALUE="<?= $total_amount ?>">
        <button type="submit" class="btn btn-info process_interac_btn">Process to Interac<sup>®</sup> Online
          Service</button>
      </form>

    </div>

  </div>
</div>