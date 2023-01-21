<?
//error_reporting(E_ALL);

if (strpos(getcwd(), "custom") == false) {
  $path = "../pdo/";
} else {
  $path = "../../pdo/";
}
include_once($path . 'dbconfig.php');


//obtain the convenience fee from settings
$convenience_rules = $DB_payment->get_convenience_fee_rate();
$CF_PP_Balance_P = $convenience_rules['CF_PP_Balance_F'];
$CF_PP_CC_P = $convenience_rules['CF_PP_CC_P'];
$CF_M_CC_P = $convenience_rules['CF_M_CC_P'];
$CF_M_Interac_F = $convenience_rules['CF_M_Interac_F'];

//a value to flag whether the payment is processed inside the admin system
$_SESSION['AdminPayment'] = true;
?>
<style>
.vcenter {
    padding-top: 22px;
}

.input-width {
    min-width: 250px;
}
</style>

<br>
<br>
<div id="alert-window"></div>
<form method="post" class="form-horizontal" action="custom/checkout.php" id="process_form">
    <?php

  if (isset($_POST['services_buy'])) {

    # -------------------------  SERVICES PAYMENT  ----------------------------

    $user_id = $_SESSION['UserID'];
    $buy_service_type = $_POST['service_type'];
    $service_buy_count = $_POST['service_buy_count'];

    $services_price = $DB_payment->get_servies_price();
    if ($buy_service_type == 'lease_sign')
      $price = $services_price['service_price_leasign'];
    elseif ($buy_service_type == 'credit_check')
      $price = $services_price['service_price_crecheck'];

    $outstanding = round($price * $service_buy_count, 2);

    $cancel_btn_action = "location.href='" . "services.php'";
  ?>
    <input type="hidden" name="product" id="product" value="Services">
    <input type="hidden" name="user_id" value="<?= $user_id ?>">
    <input type="hidden" name="service_type" value="<?= $buy_service_type ?>">
    <input type="hidden" name="service_buy_count" value="<?= $service_buy_count ?>">
    <input type="hidden" name="service_price" value="<?= $price ?>">
    <input type="hidden" name="payment_amount" id="payment_amount" value="<?= $outstanding ?>">

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <p>You are going to pay service fees:</p><br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Service Type</div>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <strong><?php echo ucwords(str_replace('_', ' ', $buy_service_type)); ?></strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Service Price</div>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <strong><?php echo number_format($price, 2) . '  $CAD'; ?></strong>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Service Times</div>
            <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $service_buy_count ?></strong></div>
        </div>
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Total Amount</div>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <strong><?php echo number_format($outstanding, 2) . '  $CAD'; ?></strong><br><br>
            </div>
        </div>
        <?php
  } elseif (isset($_POST['kijiji_payment'])) {

    # -------------------------  KIJIJI PAYMENT  ----------------------------

    $slot_count = $_POST['slot_number'];
    $slots_period = $_POST['slots_period'];
    $employee_id = $_POST['employee_id'];
    $slots_price = $_POST['price'];
    $employee_name = $_POST['employee_name'];

    //outstanding means how much need to pay
    $outstanding = round($slot_count * $slots_period * $slots_price, 2);

    $end_url = $_POST['this_url'];
    $cancel_btn_action = "location.href='" . "$end_url'";
    ?>

        <input type="hidden" name="product" id="product" value="Kijiji">
        <input type="hidden" name="slots_period" id="slots_period" value="<?= $slots_period ?>">
        <input type="hidden" name="slot_count" id="slot_count" value="<?= $slot_count ?>">
        <input type="hidden" name="slots_price" id="slots_price" value="<?= $slots_price ?>">
        <input type="hidden" name="employee_id" id="employee_id" value="<?= $employee_id ?>">
        <input type="hidden" name="employee_name" value="<?= $employee_name ?>">
        <input type="hidden" name="payment_amount" id="payment_amount" value="<?= $outstanding ?>">

        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <p>You are going to pay kijiji ads fees:</p><br><br>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Slots Number</div>
                <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $slot_count ?></strong></div>
            </div>
            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Slots Period</div>
                <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $slots_period ?></strong></div>
            </div>
            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Total Amount</div>
                <div class="col-sm-10 col-md-10 col-lg-10"><strong><?php echo number_format($outstanding, 2); ?> $
                        CAD</strong><br><br></div>
            </div>
            <?php
    } else {

      # -------------------------  LEASE PAYMENT  --------------------------------

      $lease_payment_id = $_GET['id'];
      $rental_payment_info = $DB_payment->get_rental_payment_by_lease_payment_id($lease_payment_id);
      $due_date = $rental_payment_info['due_date'];
      $tenant_ids = $rental_payment_info['tenant_ids'];

      //outstanding means how much need to pay
      $outstanding = round($rental_payment_info['outstanding'], 2);

      $owner_name = $rental_payment_info['owner_name'];
      $building_name = $rental_payment_info['building_name'];
      $unit_number = $rental_payment_info['unit_number'];
      $due_date_show = date_format(date_create($due_date), "Y-m-d");


      $user_id = $_SESSION['UserID'];
      if ($user_id < 100000)   // from employees
        $cancel_btn_action = "location.href='home'";
      else                    // from tenants
        $cancel_btn_action = "location.href='tenant_portal'";

      ?>
            <input type="hidden" name="product" id="product" value="Lease">
            <input type="hidden" name="lease_payment_id" id="lease_payment_id" value="<?= $lease_payment_id ?>">
            <input type="hidden" name="payment_amount" id="payment_amount" value="<?= $outstanding ?>">

            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <p>You are going to pay your rent:</p><br><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">Owner:</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $owner_name ?></strong><br><br></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">Buidling:</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $building_name ?></strong><br><br></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">Unit:</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $unit_number ?></strong><br><br></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">Due Date:</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?= $due_date_show ?></strong><br><br></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">Rent Amount:</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?php echo number_format($outstanding, 2); ?> $
                            CAD</strong><br><br></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2"><input type="radio" name="if_partial_payment" value="0"
                            checked> Full Payment :</div>
                    <div class="col-sm-10 col-md-10 col-lg-10"><strong><?php echo number_format($outstanding, 2);  ?> $
                            CAD</strong><br><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2"><input type="radio" name="if_partial_payment" value="1">
                        Partial Payment:</div>
                    <div class="col-sm-8 col-md-8 col-lg-8 partial">
                        <input type="text" id="partial_pay_amount" name="partial_pay_amount" class="form-control"
                            disabled><label class="control-label"> $ CAD </label><br><br>
                    </div>
                </div>
                <?php
      }
        ?>
                <!--------------------------------------------------------------->
                <!------ PAYMENT METHOD CHOSE (FOR ALL PAYMENTS SOURCES) -------->
                <!--------------------------------------------------------------->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist" id="nav_tabs">
                            <li role="presentation" class="active"><a href="#pay_now" aria-controls="pay_now" role="tab"
                                    data-toggle="tab">Pay Now</a></li>
                            <li role="presentation"><a href="#pay_forward" aria-controls="pay_forward" role="tab"
                                    data-toggle="tab">Forward Payment</a></li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <!-- pay now panel -->
                    <div role="tabpanel" class="tab-pane active row" id="pay_now">
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3">Pay By</div>
                            <div class="col-sm-2 col-md-2 col-lg-2">Service Charge</div>
                            <div class="col-sm-2 col-md-2 col-lg-2">Total<br><br></div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway"
                                    id="gateway1" value="p1" required> Paypal Balance </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter">
                                <?php echo number_format($CF_PP_Balance_P, 2); ?>%</div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b
                                    id="o_1"><?= number_format($outstanding + round($outstanding * $CF_PP_Balance_P / 100, 2), 2) ?></b>
                                $ CAD <input type="hidden" name="outstanding1"
                                    value="<?= $outstanding + round($outstanding * $CF_PP_Balance_P / 100, 2) ?>">
                            </div>
                            <div class="col-sm-5 col-md-5 col-lg-5"><img src="phpimages/paypal64.png"> </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway"
                                    id="gateway2" value="p2" required> Credit Card via Paypal </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter">
                                <?php echo number_format($CF_PP_CC_P, 2); ?>%
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b
                                    id="o_2"><?= number_format($outstanding + round($outstanding * $CF_PP_CC_P / 100, 2), 2) ?></b>
                                $ CAD <input type="hidden" name="outstanding2"
                                    value="<?= $outstanding + round($outstanding * $CF_PP_CC_P / 100, 2) ?>"> </div>
                            <div class="col-sm-5 col-md-5 col-lg-5"><img src="phpimages/paypal64.png"> <img
                                    src="phpimages/visa64.png"> <img src="phpimages/mastercard64.png"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway"
                                    id="gateway3" value="m1" required> Credit Card via Moneris </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><?php echo number_format($CF_M_CC_P, 2); ?>%
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b
                                    id="o_3"><?= number_format($outstanding + round($outstanding * $CF_M_CC_P / 100, 2), 2) ?></b>
                                $ CAD</div> <input type="hidden" name="outstanding3"
                                value="<?= $outstanding + round($outstanding * $CF_M_CC_P / 100, 2) ?>">
                            <div class="col-sm-5 col-md-5 col-lg-5"> <img src="phpimages/visa64.png"> <img
                                    src="phpimages/mastercard64.png"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3 vcenter"><input type="radio" name="gateway"
                                    id="gateway4" value="m2" required> INTERAC<sup>®</sup> Online Service<br>®
                                Trade-mark of Interac Inc.<br> Used under licence.<a
                                    href="http://interac.ca/en/interac-online-consumer" target="_blank">Learn more</a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter">
                                <?php echo number_format($CF_M_Interac_F, 2); ?>$</div>
                            <div class="col-sm-2 col-md-2 col-lg-2 vcenter"><b
                                    id="o_4"><?= number_format(round($outstanding + $CF_M_Interac_F, 2), 2) ?></b> $ CAD
                                <input type="hidden" name="outstanding4"
                                    value="<?= round($outstanding + $CF_M_Interac_F, 2) ?>">
                            </div>
                            <div class="col-sm-5 col-md-5 col-lg-5"><img src="phpimages/interac_online_log.png"
                                    height="40"></div>
                        </div>
                    </div>

                    <!-- forward payment panel -->
                    <div role="tabpanel" class="tab-pane row" id="pay_forward">
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Name :</div>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                                <input type="text" id="forward_recipient_name" name="forward_recipient_name"
                                    class="form-control"><br><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Email :</div>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                                <input type="email" id="forward_recipient_email" name="forward_recipient_email"
                                    class="form-control input-width"><br><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Mobile :</div>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                                <input type="text" id="forward_recipient_mobile" name="forward_recipient_mobile"
                                    class="form-control input-width"><br><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 text-center">Message to Recipient:</div>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                                <textarea id="forward_message" name="forward_message" class="form-control" rows="7"
                                    cols="56" style="resize: none;"></textarea><br><br>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <button id="form-submit" class="btn btn-primary">Process Now!</button>
                    <button class="btn" aria-hidden="true" aria-label="Close"
                        onclick="<?= $cancel_btn_action ?>">Cancel</button>
                </div>
            </div>
</form>


<script src="kijiji/js/jquery.min.js"></script>
<script>
var original_value = <?php echo $outstanding; ?>;
var CF_PP_Balance_P = <?php echo $CF_PP_Balance_P; ?>;
var CF_PP_CC_P = <?php echo $CF_PP_CC_P; ?>;
var CF_M_CC_P = <?php echo $CF_M_CC_P; ?>;
var CF_M_Interac_F = <?php echo $CF_M_Interac_F; ?>;


$('[name="if_partial_payment"]').click(function() {
    var checked_value = $('input:radio[name="if_partial_payment"]:checked').val();

    if (checked_value == 1) { //partial payment
        $("#partial_pay_amount").removeAttr("disabled");
    } else { // full payment
        $("#partial_pay_amount").val("");
        $("#partial_pay_amount").attr("disabled", true);
        $("#o_1").text((original_value * CF_PP_Balance_P / 100 + original_value * 1).toFixed(2));
        $("#o_2").text((original_value * CF_PP_CC_P / 100 + original_value * 1).toFixed(2));
        $("#o_3").text((original_value * CF_M_CC_P / 100 + original_value * 1).toFixed(2));
        $("#o_4").text((original_value * 1 + CF_M_Interac_F).toFixed(2));

        $('#payment_amount').val(original_value * 1); //the amount passed to checkout.php
        $('[name="outstanding1"]').val((original_value * CF_PP_Balance_P / 100 + original_value * 1).toFixed(
            2));
        $('[name="outstanding2"]').val((original_value * CF_PP_CC_P / 100 + original_value * 1).toFixed(2));
        $('[name="outstanding3"]').val((original_value * CF_M_CC_P / 100 + original_value * 1).toFixed(2));
        $('[name="outstanding4"]').val((original_value * 1 + CF_M_Interac_F).toFixed(2));
    }
});

$("#partial_pay_amount").change(function() {

    var amount = $("#partial_pay_amount").val();

    if (amount < original_value && amount > 0) {
        var amount_1 = (amount * CF_PP_Balance_P / 100 + amount * 1).toFixed(2);
        var amount_2 = (amount * CF_PP_CC_P / 100 + amount * 1).toFixed(2);
        var amount_3 = (amount * CF_M_CC_P / 100 + amount * 1).toFixed(2);
        var amount_4 = (amount * 1 + CF_M_Interac_F).toFixed(2);

        $("#o_1").text(amount_1);
        $("#o_2").text(amount_2);
        $("#o_3").text(amount_3);
        $("#o_4").text(amount_4);
        $("#partial_pay_amount").val(parseFloat(amount).toFixed(2));

        $('#payment_amount').val(amount); //the amount passed to checkout.php
        $('[name="outstanding1"]').val(amount_1);
        $('[name="outstanding2"]').val(amount_2);
        $('[name="outstanding3"]').val(amount_3);
        $('[name="outstanding4"]').val(amount_4);
    } else {
        $("#partial_pay_amount").val(parseFloat(original_value).toFixed(2));
        $("#o_1").text((original_value * CF_PP_Balance_P / 100 + original_value * 1).toFixed(2));
        $("#o_2").text((original_value * CF_PP_CC_P / 100 + original_value * 1).toFixed(2));
        $("#o_3").text((original_value * CF_M_CC_P / 100 + original_value * 1).toFixed(2));
        $("#o_4").text((original_value * 1 + CF_M_Interac_F).toFixed(2));

        $('#payment_amount').val(original_value * 1); //the amount passed to checkout.php
        $('[name="outstanding1"]').val((original_value * CF_PP_Balance_P / 100 + original_value * 1).toFixed(
            2));
        $('[name="outstanding2"]').val((original_value * CF_PP_CC_P / 100 + original_value * 1).toFixed(2));
        $('[name="outstanding3"]').val((original_value * CF_M_CC_P / 100 + original_value * 1).toFixed(2));
        $('[name="outstanding4"]').val((original_value * 1 + CF_M_Interac_F).toFixed(2));
    }
});


$(document).ready(function() {
    if ($('#payment_amount').val() <= 0) {
        $('#alert-window').append(
            '<div class="alert alert-dismissible fade in" role="alert" id="alert" style="color: #a94442; background-color: #f2dede;border-color: #ebccd1;">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' +
            '<b>The lease amount is invalid, Please reopen your payment detail page to update the amount information.</b>' +
            '</div>');

        $('#form-submit').attr('disabled', true);
    }
});


var process_form = $('#process_form');

$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    var current_tab = $('#nav_tabs').find('.active').children().first().attr('href');
    if (current_tab === '#pay_forward') {
        process_form.attr('action', 'custom/forward_payment_request.php');
    } else if (current_tab === '#pay_now') {
        process_form.attr('action', 'custom/checkout.php');
        $('input[name="gateway"]').attr('required', true);
    }
});


// remove the required before hidden pay_now panel, avoid form control error
$('a[data-toggle="tab"]').on('hide.bs.tab', function(e) {
    var hidden_tab = $('#nav_tabs').find('.active').children().first().attr('href');
    if (hidden_tab === '#pay_now') {
        $('input[name="gateway"]').attr('required', false);
    }
});
</script>