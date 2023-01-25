<br><br>
<script>
    $(function() {
        $('.example-popover').popover({
            container: 'body'
        })
    });
</script><?php
            $user_id = $_SESSION['UserID'];
            $user_level = $_SESSION['UserLevel'];

            global $apartment_id;

            include_once("../pdo/dbconfig.php");
            $DB_tenant->record_login_time($user_id);

            // get the issues list

            ## get_issues_for_tenant_unit($user_id,$apartment_id)
            $all_issues = $DB_tenant->get_issues_for_tenant_unit($user_id, $apartment_id);
            $issues = array();

            //to remove the past issues,past issues is hidden for tenants
            foreach ($all_issues as $one) {   ////////////////////////// Should work like this $all_issues[$building_id][$apartment_id]
                $issue_status = $one['issue_status'];
                $issue_past_after_days = $one['issue_past_after_days'];
                $last_update_time = date('Y-m-d', strtotime($one['last_update_time']));

                $time_flag = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list

                if ($issue_status == 'closed' && strtotime(date('Y-m-d')) > $time_flag)
                    continue;

                array_push($issues, $one);
            }

            $buildinginfo = $DB_tenant->get_building_info_tenant($apartment_id);  ##get_building_info_tenant($apartment_id);
            $building_name = $buildinginfo['building_name'];
            //  $address=$buildinginfo['address'];
            if (!empty($_GET["start"])) {
                $start = $_GET["start"];
            } else {
                $start = 0;
            }
            $recNo = 20;
            $payments = $DB_tenant->get_lease_payments_wid($user_id, $apartment_id, $lease_id, $start, $recNo);    ##get_lease_payments($user_id,$apartment_id)

            //die(var_dump($payments));
            $today = date("Y-m-d");
            $next_due_date = date("Y-m-d", strtotime("+1 month"));

            //die($next_due_date);
            $next_due_exist = 0;
            $next_due = 0.00;
            $first_next_due_date = $today;
            $first_next_due_payment = 0;
            //var_dump($payments);
            $rowpos = 1;
            $scrollrowpos = 1;
            //                                    foreach ($apartment_id as) {
            foreach ($payments as $payment) //$apartment_id
            {

                //if (strtotime($payment['due_date'])>strtotime($today) && strtotime($payment['due_date'])<strtotime($next_due_date)){
                if ($payment['outstanding'] > 0) {
                    if ($first_next_due_payment == 0) {
                        $first_next_due_payment = number_format($payment['outstanding'], 2);
                        $first_next_due_date = $payment['due_date'];
                        $first_due_payment_id = $payment['lease_payment_id'];
                        $scrollrowpos = $rowpos;
                    }
                    $next_due_date = $first_next_due_date;
                    $next_due = $first_next_due_payment;
                    $next_due_payment_id = $first_due_payment_id;
                    $next_due_exist = 1;
                }
                //                                        echo "scrollrowpos=$scrollrowpos<br>";
                $rowpos++;
            }


            $pay_now_btn = '';
            if ($next_due <= 0) {
                $pay_now_btn = 'disabled';
            }
            ?>

<div id="page-wrapper">
    <main>
        <header id="header">
        </header>
        <section id="prop_managment">
            <div class="container">
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        <div class="l_payment">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left">Partial Payment</li>
                                    <li class="pull-right"><span><img src="custom/tenant_portal/images/list_payment_icon.png" alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>
                            <div class="points" id="div_pay" style="overflow:scroll!important;">
                                <div class="table-responsive">
                                    <?php if ($payments) { ?>
                                        <form action="custom/checkout.php" method="post" id="payform" class="form_<?= $apartment_id ?>">
                                            <input type="hidden" name="apartment_id" value="<?= $apartment_id ?>">
                                            <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
                                            <input type="hidden" name="product" value="Lease">
                                            <!--add april 10 -->

                                            <table class="table table-bordered " id="table_pay<?= $apartment_id ?>">
                                                <thead>
                                                    <tr>
                                                        <th class="text_clr">Due Date</th>
                                                        <th class="text_clr">Paid</th>
                                                        <th class="text_clr">Balance</th>
                                                        <th class="text_clr">Inv.</th>
                                                        <th class="text_clr">Pay</th>
                                                        <th class="text_clr" <?php echo $payment['outstanding'] == 0 ? 'style="display:none;" ' : '' ?>>
                                                            Select
                                                        </th>
                                                        <th class="text_clr">Comment</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?
                                                    ?>
                                                    <?php
                                                    // $sql = "SELECT * FROM payment LIMIT $record_index, $20";
                                                    foreach ($payments

                                                        as $payment) {
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $payment['due_date']; ?>
                                                            </td>

                                                            <td><?php echo number_format($payment['paid'], 2); ?></td>
                                                            <td><?php echo number_format($payment['outstanding'], 2); ?></td>


                                                            <?php if ($payment['outstanding'] <= 0) {
                                                            ?>
                                                                <td>
                                                                    <a href="custom/invoice_receipt/invoice_receipt_controller.php?download_invoice&lease_payment_id=<?php echo $payment['lease_payment_id'] ?>">
                                                                        <i class="fa-solid fa-file-pdf fa-2x" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>

                                                                <td class="text_clr">paid</td>
                                                            <?php } else {
                                                                //                                                $ScrollPay++;
                                                            ?>
                                                                <td> N/A</td>
                                                                <td class="text_clr"><a href="pay.php?id=<? echo $payment['lease_payment_id']; ?>">pay</a>
                                                                </td>
                                                            <?php } ?>


                                                            <td>
                                                                <input class="selectPaymentrec" data-amt="<?php echo $payment['outstanding'] ?>" data-pid="<?php echo $payment['lease_payment_id']; ?>" data-aptid="<?php echo $apartment_id; ?>" type="checkbox" name="selected_lease_payment_ids[]" value="<?= $payment['lease_payment_id'] ?>" id="select<?= $payment['lease_payment_id'] ?>" <?php echo $payment['outstanding'] <= 0 ? 'style="display:none;" ' : '' ?> onclick="calc<?= $apartment_id . "_" . $lease_id ?>(<?= $payment['outstanding'] ?>, this.checked,this);">
                                                            </td>
                                                            <td><?php
                                                                if ($payment['invoice_type_id'] == '3') {
                                                                ?>
                                                                    <div class="customComment" data-toggle="popover" title="Popover title" data-content="<?php echo $payment['tenant_comments']; ?>" data-placement="top">
                                                                        <?php echo substr($payment['tenant_comments'], 0, 10) . "..."; ?>
                                                                    </div>

                                </div>
                            </div>
                        <?php } ?>
                        </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <div class="col-sm-12">
                        <div class="box-content-note">
                            <h5>You do not have any payments.</h5>
                        </div>
                    </div>
                <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="l_payment col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="headings_box">
                    <ul>
                        <li class="pull-left">Total Payment</li>
                        <li class="pull-right"><span><img src="custom/tenant_portal/images/list_payment_icon.png" alt="building_icon"></span></li>
                        <div class="clearfix"></div>
                    </ul>
                </div>

                <div class="points" id="div_total">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td><input type="radio" name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>" checked value="0"></td>
                                <td><b>Total Amount</b></td>
                                <td>
                                    <span id="span_total_amount_<?= $apartment_id . "_" . $lease_id ?>">0</span>
                                    <input type="hidden" data-aptid="<?php echo $apartment_id; ?>" size="5" id="total_amount_<?= $apartment_id . "_" . $lease_id ?>" class="classtotal_payment_<?php echo $apartment_id; ?>" name="total_payment" value="0">
                                    <input type="hidden" data-aptid="<?php echo $apartment_id; ?>" size="5" id="pay_amount_<?= $apartment_id . "_" . $lease_id ?>" name="payment_amount" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>" value="1">
                                </td>
                                <td><b>Partial Payment<b></td>
                                <td>
                                    <input data-aptid="<?php echo $apartment_id; ?>" type="text" size="5" class="partial_payment_entry_amt" id="partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>" name="partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>" disabled> $ CAD
                                </td>
                            </tr>
                        </table>
                    </div>
                    <script>
                        var
                            payments_checked = {}; // To keep track of the payments selected in a building - Building id is an array key
                        var total_payment = 0;

                        function pushIntoPaymentsChecked(aptId, paymentId) {
                            if (!payments_checked.hasOwnProperty(aptId)) {
                                payments_checked[aptId] = []; // Add apt id to the object
                                payments_checked[aptId].push(paymentId);
                            } else {
                                if (payments_checked[aptId].indexOf(paymentId) == -1) {
                                    payments_checked[aptId].push(paymentId);
                                }
                            }
                        }

                        function popFromPaymentsChecked(aptId, paymentId) {
                            if (payments_checked.hasOwnProperty(aptId)) {
                                index = payments_checked[aptId].indexOf(paymentId);
                                if (index !== -1) {
                                    payments_checked[aptId].splice(index, 1);
                                }
                                if (payments_checked[aptId].length < 1) {
                                    // remove the apartmentID as key to avoid error
                                    delete payments_checked[aptId];
                                }
                            }
                        }

                        function calc<?= $apartment_id . "_" . $lease_id ?>(os, isChecked, thisObject) {
                            var aptId = thisObject.getAttribute('data-aptid');
                            var paymentId = thisObject.getAttribute('data-pid');
                            var total_amount = 0;
                            var totalAmtWoCf = 0;

                            m2 = 0;
                            p1 = 2.99;
                            m1 = 2.9;
                            pay_amount = document.getElementById("pay_amount_<?= $apartment_id . "_" . $lease_id ?>").value;
                            if (isChecked == 1) {
                                pushIntoPaymentsChecked(aptId, paymentId);
                                // total_amount = parseFloat(pay_amount) + parseFloat(os);
                            } else {
                                popFromPaymentsChecked(aptId, paymentId);
                                // total_amount = parseFloat(pay_amount) - parseFloat(os);
                            }

                            $(".selectPaymentrec").each(function(i, v) {
                                if ($(v).attr("data-aptid") != aptId) {
                                    return;
                                }
                                if (!$(v).prop("checked")) {
                                    return;
                                }
                                var amount = parseFloat($(v).attr("data-amt"));
                                total_amount += amount;
                            });

                            totalAmtWoCf = total_amount;
                            total_payment = total_amount;

                            $(".totalAmtDisplayWCf").each(function(i, v) {
                                if ($(v).attr("data-aptid") != aptId) {
                                    return;
                                }

                                typeOfPayment = $(v).attr("data-ptype");
                                switch (typeOfPayment) {
                                    case "m1":
                                        total_amount = total_amount + (parseFloat(total_amount) * parseFloat(m1) /
                                            100);
                                        total_amount = Math.round(100 * total_amount) / 100;
                                        $(v).html(total_amount);
                                        break;
                                    case "m2":
                                        total_amount = total_amount + (parseFloat(total_amount) * parseFloat(m2) /
                                            100);
                                        total_amount = Math.round(100 * total_amount) / 100;
                                        $(v).html(total_amount);
                                        break;
                                    case "p1":
                                        total_amount = total_amount + (parseFloat(total_amount) * parseFloat(p1) /
                                            100);
                                        total_amount = Math.round(100 * total_amount) / 100;
                                        $(v).html(total_amount);
                                        break;
                                }
                            });

                            // Calculating the total + Convenience fees just to show to the user
                            // For processing don't add the CF in the content page :
                            // It is taken care of by Tianen in the checkout page

                            document.getElementById("span_total_amount_<?= $apartment_id . "_" . $lease_id ?>").innerHTML =
                                totalAmtWoCf;
                            document.getElementById("total_amount_<?= $apartment_id . "_" . $lease_id ?>").value =
                                totalAmtWoCf;
                            $("#total_amount_<?= $apartment_id . "_" . $lease_id ?>").attr("value", totalAmtWoCf);
                            $("#pay_amount_<?= $apartment_id . "_" . $lease_id ?>").attr("value", totalAmtWoCf);
                            $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>").val(totalAmtWoCf);
                        }

                        $("document").ready(function() {
                            $("body").find(
                                    'input[type="radio"][name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"]'
                                )
                                .on('change', function() {
                                    var checked_value = $(
                                        'input:radio[name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"]:checked'
                                    ).val();
                                    var totalPayment = $(".classtotal_payment_" + <?php echo $apartment_id; ?>)
                                        .val();

                                    if (checked_value == 0) {
                                        // Pay now
                                        $(".wrapper-tabscontent-main").show();
                                        $(".forwardPaymentModal").modal("hide");
                                    }
                                    if (checked_value == 1) {
                                        // Forward Payment
                                        if (totalPayment < 1) {
                                            $('input:radio[name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"]:checked')
                                                .prop("checked", false);
                                            return;
                                        }
                                        $(".wrapper-tabscontent-main").hide();
                                        $(".forwardPaymentModal").modal("show");

                                        var ifpartial = $(
                                            'input:radio[name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>"]:checked'
                                        ).val();


                                        var leasePaymentIds = payments_checked[<?php echo $apartment_id; ?>];
                                        var inputElements = "";

                                        for (var leasePaymentIndex in leasePaymentIds) {
                                            var leasePaymentIdSingle = leasePaymentIds[leasePaymentIndex];
                                            inputElements +=
                                                "<input type='hidden' name='lease_payment_id[]' value='" +
                                                leasePaymentIdSingle + "'> ";
                                        }

                                        $(".fwd_lease_payment_ids_div").empty().html(inputElements);

                                        $(".fwd_payment_amount").val(totalPayment);
                                        $(".fwd_if_partial_payment").val(ifpartial);
                                    }
                                });
                        });

                        $('[name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>"]').click(function() {
                            var checked_value = $(
                                'input:radio[name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>"]:checked'
                            ).val();
                            if (checked_value == 1) { //partial payment
                                $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>").removeAttr(
                                    "disabled");
                                document.getElementById("partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                                    .value = document.getElementById(
                                        "total_amount_<?= $apartment_id . "_" . $lease_id ?>").value;
                            } else { // full payment
                                $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>").val("");
                                $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>").attr("disabled",
                                    true);
                            }
                        });

                        $("body").on("blur", ".partial_payment_entry_amt", function(e) {
                            e.stopImmediatePropagation();
                            var apt_id = $(this).attr("data-aptid");
                            var amountEntered = parseFloat($(this).val().trim());

                            var apt_id_current_tab = $(".aptsnavpills").children().filter(".active").attr(
                                "data-aptid");

                            if (apt_id_current_tab = apt_id) {
                                if (isNaN($(this).val())) {
                                    $(this).val(total_payment);
                                    return;
                                }

                                if (parseInt($(this).val()) < 1) {
                                    $(this).val(total_payment);
                                    return;
                                }

                                $(".classtotal_payment_" + apt_id).val(amountEntered);
                                $(".classtotal_payment_" + apt_id).next().attr("value", amountEntered);

                                // Check if the amount entered in the partial payment is greater than the total amount which is calculated
                                if (amountEntered > parseFloat(total_payment)) {
                                    $(this).val(total_payment);
                                    $(".classtotal_payment_" + apt_id).val(total_payment);
                                    $(".classtotal_payment_" + apt_id).next().attr("value", total_payment);
                                }
                            }
                        });

                        function submitPayForm(aptId, paymentMethod) {
                            // Get the current Apartment ID
                            if (payments_checked[aptId] && payments_checked[aptId].length < 1) {
                                return;
                            }
                            if (total_payment < 1) {
                                return;
                            }
                            $("#paymentMethodIdForm_" + aptId).attr("value", paymentMethod);
                            $(".form_" + aptId).submit();
                        }
                    </script>

                    <hr>
                    <h5>Payment Options</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td><input type="radio" name="payment_when_<?= $apartment_id . "_" . $lease_id ?>" value="0"></td>
                                <td><b>Pay Now</b></td>
                            </tr>
                            <tr>
                                <td><input type="radio" name="payment_when_<?= $apartment_id . "_" . $lease_id ?>" value="1"></td>
                                <td><b>Have someone pay for you</b></td>
                            </tr>
                        </table>
                    </div>

                    <div class="wrapper-tabscontent-main" style="display: none;">
                        <table class="table table-bordered">
                            <tr>
                                <!--                                            <th  class="text_clr">Pay Now</th>-->
                                <th class="text_clr">Convenience Fee</th>
                                <th class="text_clr">Total Amount</th>
                                <th class="text_clr">Pay Now</th>
                            </tr>
                            <br>

                            <input id="<?php echo "paymentMethodIdForm_" . $apartment_id; ?>" type="hidden" name="paymentType<?= $apartment_id . "_" . $lease_id ?>">

                            <tr>
                                <!-- <td> <input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>" checked  onclick="calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="m2"></td> -->
                                <td><span id="span_interac_<?= $apartment_id . "-" . $lease_id ?>">0</span>
                                </td>
                                <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="m2" data-cf="0" class="totalAmtDisplayWCf" id="total_<?= $apartment_id . "-" . $lease_id ?>">0</span></td>
                                <td><img align="center" src="custom/tenant_portal/images/interac3.png" alt="pay_icon" height="30" onclick="submitPayForm(<?php echo $apartment_id; ?>,'m2')" style="cursor: pointer"></td>
                            </tr>
                            <tr>
                                <!-- <td><input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>" onclick="calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="p1"></td>-->
                                <td>
                                    <span id="span_paypal_<?= $apartment_id . "-" . $lease_id ?>">2.99 %</span>
                                </td>
                                <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="p1" data-cf="2.99" class="totalAmtDisplayWCf" id="total_<?= $apartment_id . "-" . $lease_id ?>">0</span></td>
                                <td><img align="center" src="custom/tenant_portal/images/paypal3.png" alt="pay_icon" height="30" onclick="submitPayForm(<?php echo $apartment_id; ?>,'p1')" style="cursor: pointer"></td>
                            </tr>
                            <tr>
                                <!-- <td><input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>"  onclick="calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="m1"></td>-->
                                <td>
                                    <span id="span_mastercard_<?= $apartment_id . "-" . $lease_id ?>">2.9 %</span>
                                </td>
                                <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="m1" data-cf="2.9" class="totalAmtDisplayWCf" id="span_mastercard_<?= $apartment_id . "-" . $lease_id ?>">0</span>
                                </td>
                                <td><img align="left" src="custom/tenant_portal/images/moneris3.png" alt="pay_icon" height="30" onclick="submitPayForm(<?php echo $apartment_id; ?>,'m1')" style="cursor: pointer"></td>
                            </tr>
                        </table>
                    </div>
                    </form>

                </div>
            </div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('form').trigger("reset");
        var rowId = <?= $scrollrowpos ?>; //$(this).attr('href');
        var Tablepos = $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr:eq(' + rowId + ')').offset().top;
        var divPayPos = $('#div_pay<?= $apartment_id . "-" . $lease_id ?>').offset().top;
        var goPos = Tablepos - divPayPos;
        //$('#div_pay<?= $apartment_id . "-" . $lease_id ?>').scrollTop(goPos);
        $('#div_pay<?= $apartment_id . "-" . $lease_id ?>').animate({
            scrollTop: goPos
        }, 500);
        $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr').css({
            'background-color': 'none'
        });
        $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr:eq(' + rowId + ')').css({
            'background-color': '#ccc'
        });

        $("#form-submit-forward").on("click", function() {
            $("#process_form_fwd").submit();
        });

    });
</script>
<div class="row">
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <div class="bldig_info">
            <div class="headings_box">
                <ul>
                    <li class="pull-left b_u_info">Building & Unit Information</li>
                    <li class="pull-right"><span><b></b> <img src="custom/tenant_portal/images/building_info_icon.png" alt="building_icon"></span></li>
                    <div class="clearfix"></div>
                </ul>
            </div>
            <?php
            ## changed, based on above
            $row = $DB_tenant->get_building_info_tenant($apartment_id);
            $rules_content = $row['rules'];
            $schedules_content = $row['schedules'];
            ?>
            <div class="points">
                <div class="bs-example">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading" style="align-self: left">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Building
                                        Rules</a>
                                </h5>
                            </div>
                            <div id="collapseOne<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse ">
                                <div class="panel-body" title="<?= strip_tags($rules_content) ?>">
                                    <?
                                    $rules_content_len = strlen($rules_content);
                                    if ($rules_content_len >= 50) {
                                        echo substr(strip_tags($rules_content), 0, 50) . " ...";
                                    } else {
                                        echo strip_tags($rules_content);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" style="align-self: left">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Building
                                        Schedule</a>
                                </h5>
                            </div>
                            <div id="collapseTwo<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse ">
                                <div class="panel-body " data-toggle="popover" data-trigger="hover" title="<?= strip_tags($schedules_content) ?>">
                                    <?
                                    $schedules_content_len = strlen($schedules_content);
                                    if ($schedules_content_len >= 50) {
                                        echo substr(strip_tags($schedules_content), 0, 50) . " ...";
                                    } else {
                                        echo strip_tags($schedules_content);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Building
                                        Document</a>
                                </h5>
                            </div>
                            <div id="collapseThree<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php

                                    $building_documents = $DB_tenant->get_building_documents($apartment_id);
                                    foreach ($building_documents as $building_document) {
                                        if ($building_document['building_file']) {
                                    ?>
                                            <p>
                                                <a href="<?php echo "files/" . $building_document['building_file']; ?>" download><?php echo $building_document['building_file_type']; ?></a>
                                            </p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Apartment
                                        Document</a>
                                </h5>
                            </div>
                            <div id="collapseFour<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php $apartment_documents = $DB_tenant->get_apartment_documents($apartment_id);
                                    foreach ($apartment_documents as $apartment_document) {
                                        if ($apartment_document['apartment_file']) {
                                    ?>
                                            <p>
                                                <a href="<?php echo "files/" . $apartment_document['apartment_file']; ?>" download><?php echo $apartment_document['apartment_file_type']; ?></a>
                                            </p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Lease
                                        Document</a>
                                    </h4>
                            </div>
                            <div id="collapseFive<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php $lease_documents = $DB_tenant->get_lease_documents($user_id, $apartment_id);
                                    foreach ($lease_documents as $lease_document) {
                                        if ($lease_document['lease_file']) {
                                    ?>
                                            <p>
                                                <a href="<?php echo "files/" . $lease_document['lease_file']; ?>" download><?php echo $lease_document['lease_file_type']; ?></a>
                                            </p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix<?= $apartment_id . "-" . $lease_id ?>"><i class="fa fa-circle" aria-hidden="true"></i>Tenant
                                        Document</a>
                                </h5>
                            </div>
                            <div id="collapseSix<?= $apartment_id . "-" . $lease_id ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php $tenant_documents = $DB_tenant->get_tenant_documents($user_id);   ##no change
                                    foreach ($tenant_documents as $tenant_document) {
                                        if ($tenant_document['tenant_file']) {
                                    ?>
                                            <p>
                                                <a href="<?php echo "files/" . $tenant_document['tenant_file']; ?>" download><?php echo $tenant_document['tenant_file_type']; ?></a>
                                            </p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('[data-toggle="popover"]').popover();
            });
        </script>
        <div class="l_payment">
            <?
            $tenant_accessbility = $DB_tenant->get_tenant_settings_about_request($user_id);
            $allow_create_request = $tenant_accessbility["allow_create_request"];
            $ActiveStatus = array(1, 7, 8);

            if (!in_array($lease_status_id, $ActiveStatus)) {
                $allow_create_request = 0;
                //    echo " it is inactive";
            }
            ?>
            <div class="headings_box">
                <ul>
                    <li class="pull-left"><a href="requests?unit_id=<?php echo $apartment_id; ?>" style="color: #7C7C7C">Request List</li>
                    <li class="pull-right"><a href="requests?direct=report&unit_id=<?php echo $apartment_id; ?>">
                            <button class="btn btn-primary" <?php echo $allow_create_request == 0 ? 'style="display:none;" ' : '' ?>>
                                Report a Request
                            </button>
                        </a></li>
                    <div class="clearfix"></div>
                </ul>
            </div>
            <div class="points">
                <?php if ($issues == null) { ?>
                    <div class="col-sm-12">
                        <div class="box-content-note">
                            <h5>You do not have any requests currently.</h5>
                        </div>
                    </div>
                <?php } else { ?>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text_clr">Type</th>
                                    <th class="text_clr">Status</th>
                                    <th class="text_clr">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($issues as $row) {
                                    $message = $row['message'];
                                    $type = $row['request_type'];
                                    $status = $row['request_status'];
                                ?>
                                    <tr>
                                        <td><?php echo $type; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td title="<?php echo $message; ?>">
                                            <?php
                                            $msglen = strlen($message);
                                            if ($msglen >= 50) {
                                                echo substr($message, 0, 50) . " ...";
                                            } else {
                                                echo $message;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <div class="bulletins">
            <div class="headings_box">
                <ul>
                    <li class="pull-left">Bulletins</li>
                    <li class="pull-right"><span><b></b> <img src="custom/tenant_portal/images/bulletins_icon.png" alt="building_icon"></span></li>
                    <div class="clearfix"></div>
                </ul>
            </div>
            <div class="points">
                <ul>
                    <?php
                    $bulletins = $DB_tenant->get_tenant_bulletin($user_id, $apartment_id);

                    foreach ($bulletins as $bulletin) {
                        $message_body = $bulletin['message_body'];
                    ?>
                        <li title="<?= $message_body ?>">
                            <?php echo $bulletin['create_time'];

                            ?><br>
                            <?php echo $bulletin['message_title']; ?><br>

                            <?php

                            $msglen = strlen($message_body);
                            if ($msglen >= 50) {
                                echo substr($message_body, 0, 50) . " ...";
                            } else {
                                echo $message_body;
                            }
                            ?>
                            <br><br>

                        </li>
                    <?php } ?>

                </ul>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</section>
</main>
</div>
<!--page-wrapper-->

<style>
    .customComment:hover {
        cursor: pointer;
    }
</style>