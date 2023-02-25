<br><br>

<?php
include_once('../pdo/Class.Payment.php');
$DB_payment = new Payment($DB_con);
include_once('../pdo/Class.Request.php');
$DB_request = new Request($DB_con);
$user_id = $_SESSION['UserID'];
$user_level = $_SESSION['UserLevel'];

//convenience fee
$convenience_fee_rate = $DB_payment->get_convenience_fee_rate();
$paypalCharge      = $convenience_fee_rate['CF_PP_Balance_F'];
$creditcardCharge           = $convenience_fee_rate['CF_PP_CC_P'];
$creditcardCharge            = $convenience_fee_rate['CF_M_CC_P'];
$interactCharge       = $convenience_fee_rate['CF_M_Interac_F'];


//global $apartment_id;

include_once("../pdo/dbconfig.php");
$DB_tenant->record_login_time($user_id);

// get the issues list

## get_issues_for_tenant_unit($user_id,$apartment_id)
$all_issues = $DB_tenant->get_issues_for_tenant_unit($user_id, $apartment_id);

$issues = array();
// print_r($all_issues);
//to remove the past issues,past issues is hidden for tenants
// foreach ($all_issues as $one) {   ////////////////////////// Should work like this $all_issues[$building_id][$apartment_id]
//     $issue_status = $one['issue_status'];
//     $issue_past_after_days = $one['issue_past_after_days'];
//     $last_update_time = date('Y-m-d', strtotime($one['last_update_time']));


//     $time_flag = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list

//     if ($issue_status == 'closed' && strtotime(date('Y-m-d')) > $time_flag) {
//         continue;
//     }

//     array_push($issues, $one);
// }
// die(var_dump($issues));
$issues = $DB_request->get_tenant_requests($tenant_id, $apartment_id);

//echo "<pre>";
//die(var_dump($all_issues));
?>
<?php
$buildinginfo = $DB_tenant->get_building_info_tenant($apartment_id);  ##get_building_info_tenant($apartment_id);
$building_name = $buildinginfo['building_name'];
$building_id = $buildinginfo['building_id'];

$schedules = $buildinginfo['schedules'];
$washingroom_info = $buildinginfo['washingroom_info'];
$workhour_info = $buildinginfo['workhour_info'];
$emergency_info = $buildinginfo['emergency_info'];
$tenant_id = $user_id;
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
foreach ($payments as $payment) { //$apartment_id
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
            <div class="container-fluid">
                <div class="row" style="margin-top:20px;">
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                        <div class="l_payment">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left">Payment</li>
                                    <li class="pull-right"><span><img
                                                src="custom/tenant_portal/images/list_payment_icon.png"
                                                alt="building_icon"></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points" id="div_pay<?= $apartment_id . "-" . $lease_id ?>"
                                style="overflow:scroll!important;">
                                <div class="table-responsive">
                                    <?php if ($payments) { ?>
                                    <form action="custom/checkout.php" method="post"
                                        id="form_<?= $apartment_id . "_" . $lease_id ?>" class="form_<?= $lease_id ?>">
                                        <input type="hidden" name="apartment_id" value="<?= $apartment_id ?>">
                                        <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
                                        <input type="hidden" name="product" value="Lease">
                                        <!--add april 10 -->

                                        <table class="table table-bordered"
                                            id="table_pay<?= $apartment_id . "-" . $lease_id ?>">
                                            <thead>
                                                <?php $deposits = $DB_tenant->get_deposit_amount($lease_id);
                                                    $depositRowNo = 0;
                                                    if (!empty($deposits)) {
                                                    ?>
                                                <tr>
                                                    <th class="text_clr">Deposits</th>
                                                </tr>

                                                <?php

                                                        $depositRowNo++;
                                                        foreach ($deposits as $deposit) {
                                                            $depositRowNo++;
                                                            if (!empty($deposit['amount'])) {
                                                                echo "<tr><td>" . $deposit['paid_date'] . "</td><td>" . $deposit['amount'] . "</td><td colspan='5'>" . $deposit['comments'] . "</td></tr>";
                                                            }
                                                        }
                                                        //
                                                    }
                                                    ?>

                                                <tr>
                                                    <th class="text_clr">Due Date</th>
                                                    <th class="text_clr">Paid</th>
                                                    <th class="text_clr">Balance</th>
                                                    <th class="text_clr">Inv.</th>
                                                    <th class="text_clr">Pay</th>
                                                    <!-- Hide Payment
                                                    <th class="text_clr"
                                                        <?php echo $payment['outstanding'] == 0 ? 'style="display:none;" ' : '' ?>>
                                                        Select
                                                    </th> -->
                                                    <th class="text_clr">Comment</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                    // $sql = "SELECT * FROM payment LIMIT $record_index, $20";

                                                    foreach ($payments as $payment) {
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
                                                        <a
                                                            href="custom/invoice_receipt/invoice_receipt_controller.php?download_invoice&lease_payment_id=<?php echo $payment['lease_payment_id'] ?>">
                                                            <i class="fa-solid fa-file-pdf fa-2x"
                                                                aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text_clr">Paid</td>
                                                    <?php
                                                            } else {
                                                                if ($first_not_paid_lease_id == 0) {
                                                                    $first_not_paid_lease_id = $payment['lease_payment_id'];
                                                                }
                                                                //                                                $ScrollPay++;
                                                            ?>
                                                    <td> N/A</td>
                                                    <td class="text_clr">
                                                        <!--                                                        <a href="pay.php?id=-->
                                                        <? // echo $payment['lease_payment_id'];
                                                                    ?>
                                                        <!--">pay</a>-->
                                                        Unpaid
                                                    </td>
                                                    <?php
                                                            } ?>
                                                    <!-- Hide Payment
                                                        <td>
                                                        <input class="selectPaymentrec"
                                                            data-amt="<?php echo $payment['outstanding'] ?>"
                                                            data-pid="<?php echo $payment['lease_payment_id']; ?>"
                                                            data-aptid="<?php echo $apartment_id; ?>" type="checkbox"
                                                            name="selected_lease_payment_ids[]"
                                                            value="<?= $payment['lease_payment_id'] ?>"
                                                            id="select<?= $payment['lease_payment_id'] ?>"
                                                            <?php echo $payment['outstanding'] <= 0 ? 'style="display:none;" ' : '' ?>
                                                            onclick="ew.vars.calc<?= $apartment_id . "_" . $lease_id ?>(<?= $payment['outstanding'] ?>, this.checked,this);">
                                                    </td> -->
                                                    <td><?php
                                                                if ($payment['invoice_type_id'] == '3' && !empty($payment['tenant_comments'])) {
                                                                ?>
                                                        <div class="customComment" data-bs-trigger="hover"
                                                            title="Comment"
                                                            data-content="<?php echo $payment['tenant_comments']; ?>"
                                                            data-placement="top">
                                                            <?php echo substr($payment['tenant_comments'], 0, 10) . "..."; ?>
                                                        </div>

                                </div>
                            </div>
                            <?php
                                                                } ?>
                            </td>
                            </tr>
                            <?php
                                                    } ?>
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
                <div class="bldig_info">
                    <div class="headings_box">
                        <ul>
                            <li class="pull-left b_u_info">Building & Unit Information</li>
                            <li class="pull-right"><span><b></b> <img
                                        src="custom/tenant_portal/images/building_info_icon.png"
                                        alt="building_icon"></span></li>
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
                            <div class="accordion" id="accordion">
                                <?php if (!empty($rules_content)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header" style="align-self: left">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Building Rules</button>
                                        </h5>
                                    </div>
                                    <div id="collapseOne<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse ">
                                        <div class="customComment text-align-left" data-bs-trigger="hover" title="Rules"
                                            data-content="<?= strip_tags($rules_content) ?>" data-placement="bottom">
                                            <?php
                                                $rules_content_len = strlen($rules_content);
                                                //if ($rules_content_len >= 50) {
                                                //    echo substr(strip_tags($rules_content), 0, 50) . " ...";
                                                //} else {
                                                echo $rules_content;
                                                // }
                                                ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php $docsArray = array(8, 10, 11, 4);
                                for ($j = 0; $j < count($docsArray); $j++) {
                                    $document_category_id = $docsArray[$j];
                                    $table_field_array = $DB_tenant->get_document_type($document_category_id);
                                    $table_field = $table_field_array[0]['table_field'] . "_id";
                                    if ($table_field == "view_tenant_id") {
                                        $table_field = "tenant_id";
                                    }
                                    $doc_name = $table_field_array[0]['name'];
                                    $documents = $DB_tenant->get_documents($document_category_id, $table_field, $$table_field);
                                ?>
                                <?php if (!empty($documents) && false) { // Disable all of documents
                                    ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseThree<?= $j ?>-<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i><?= $doc_name ?>
                                                Document</button>
                                        </h5>
                                    </div>
                                    <div id="collapseThree<?= $j ?>-<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <?php
                                                    if (!empty($documents)) {
                                                        foreach ($documents as $document) {
                                                            if (!empty($document['file'])) {
                                                    ?>
                                            <p>
                                                <a target="_blank"
                                                    href="<?php echo "files/attachments/" . $document['file']; ?>">download<?= $document['file']; ?></a>
                                            </p>
                                            <?php
                                                            }
                                                        }
                                                    } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php
                                } ?>
                                <?php if (!empty($fire_escape_plan)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseSeven<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Fire Escape
                                                plan</button>
                                        </h5>
                                    </div>
                                    <div id="collapseSeven<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <!--                                            is_null-->
                                            <?php $fire_escape_plan = $DB_tenant->get_fire_escape_plan($apartment_id);
                                                if (!empty($fire_escape_plan)) {
                                                ?>

                                            <a target="_blank"
                                                href="<?php echo "files/" . $fire_escape_plan; ?>">download</a>

                                            <?php
                                                }
                                                ?>
                                        </div>
                                    </div>
                                </div>
                                <? } ?>
                                <?php if (!empty($schedules)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseEight<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Public Garbage and
                                                Recycle Information</button>
                                        </h5>
                                    </div>
                                    <div id="collapseEight<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">

                                            <div class="customComment text-align-left" data-bs-trigger="hover"
                                                title="Garbage" data-content="<?= strip_tags($schedules) ?>"
                                                data-placement="bottom">
                                                <?php
                                                    $schedules_len = strlen($schedules);
                                                    // if ($schedules_len >= 50) {
                                                    //     echo substr(strip_tags($schedules), 0, 50) . " ...";
                                                    // } else {
                                                    echo strip_tags($schedules);
                                                    // }
                                                    ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (!empty($washingroom_info)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseNine<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Public Washing-room
                                                Information</button>
                                        </h5>
                                    </div>
                                    <div id="collapseNine<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div class="customComment text-align-left" data-bs-trigger="hover"
                                                title="Washingroom_info"
                                                data-content="<?= strip_tags($washingroom_info) ?>"
                                                data-placement="bottom">
                                                <?php
                                                    $washingroom_info_len = strlen($washingroom_info);
                                                    // if ($washingroom_info_len >= 50) {
                                                    //     echo substr(strip_tags($washingroom_info), 0, 50) . " ...";
                                                    // } else {
                                                    echo strip_tags($washingroom_info);
                                                    // }
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (!empty($workhour_info)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseTen<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Administration Work hour
                                                Information</button>
                                        </h5>
                                    </div>
                                    <div id="collapseTen<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div class="customComment text-align-left" data-bs-trigger="hover"
                                                title="Work hour Information"
                                                data-content="<?= strip_tags($workhour_info) ?>"
                                                data-placement="bottom">
                                                <?php
                                                    $workhour_info_len = strlen($workhour_info);
                                                    // if ($workhour_info_len >= 50) {
                                                    //     echo substr(strip_tags($workhour_info), 0, 50) . " ...";
                                                    // } else {
                                                    echo strip_tags($workhour_info);
                                                    // }
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (!empty($emergency_info)) { ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseTwelve<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle" aria-hidden="true"></i>Emergency
                                                Information</button>
                                        </h5>
                                    </div>
                                    <div id="collapseTwelve<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">

                                            <div class="customComment text-align-left" data-bs-trigger="hover"
                                                title="Emergency Information"
                                                data-content="<?= strip_tags($emergency_info) ?>"
                                                data-placement="bottom">
                                                <?php
                                                    $emergency_info_len = strlen($emergency_info);
                                                    // if ($emergency_info_len >= 50) {
                                                    //     echo substr(strip_tags($emergency_info), 0, 50) . " ...";
                                                    // } else {
                                                    echo strip_tags($emergency_info);
                                                    // }
                                                    ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <? $applianceArray = array(26, 27, 28, 6, 9, 2, 3, 18, 29, 30, 31, 21);
                                for ($j = 0; $j < count($applianceArray); $j++) {
                                    $appliance = $DB_tenant->get_appliances($apartment_id, $applianceArray[$i]);

                                    if (!empty($appliance[0]['name']) && !empty($appliance[0]['information'])) {
                                ?>

                                <div class="accordion-item">

                                    <div class="accordion-header">
                                        <h5 class="accordion-title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?= $j ?>-<?= $apartment_id . "-" . $lease_id ?>"><i
                                                    class="fa fa-circle"
                                                    aria-hidden="true"></i><?= $appliance[0]['name'] ?></button>
                                        </h5>
                                    </div>
                                    <div id="collapse<?= $j ?>-<?= $apartment_id . "-" . $lease_id ?>"
                                        class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <?= $appliance[0]['information'] ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                } ?>




                            </div>

                        </div>

                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <!-- Hide Payment -->
                <div class="l_payment" style="visibility:hidden; display:none">

                    <div class="headings_box">
                        <ul>
                            <li class="pull-left">Total Payment</li>
                            <li class="pull-right"><span><img src="custom/tenant_portal/images/list_payment_icon.png"
                                        alt="building_icon"></span></li>
                            <div class="clearfix"></div>
                        </ul>
                    </div>

                    <div class="points" id="div_total">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><input type="radio"
                                            name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>" checked
                                            value="0"></td>
                                    <td><b>Total Amount</b></td>
                                    <td>
                                        <span id="span_total_amount_<?= $apartment_id . "_" . $lease_id ?>">0</span>
                                        <input type="hidden" data-aptid="<?php echo $apartment_id; ?>" size="5"
                                            id="total_amount_<?= $apartment_id . "_" . $lease_id ?>"
                                            class="classtotal_payment_<?php echo $apartment_id; ?>" name="total_payment"
                                            value="0">
                                        <input type="hidden" data-aptid="<?php echo $apartment_id; ?>" size="5"
                                            id="pay_amount_<?= $apartment_id . "_" . $lease_id ?>" name="payment_amount"
                                            value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="radio"
                                            name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>" value="1">
                                    </td>
                                    <td><b>Partial Payment<b></td>
                                    <td>
                                        <input data-aptid="<?php echo $apartment_id; ?>" type="text" size="5"
                                            class="partial_payment_entry_amt"
                                            id="partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>"
                                            name="partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>" disabled> $
                                        CAD
                                    </td>
                                </tr>
                            </table>
                        </div>


                        <hr>
                        <h5>Payment Options</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="margin: 0px">
                                <tr>
                                    <td><input type="radio" name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"
                                            id="payment_when_<?= $apartment_id . "_" . $lease_id ?>_0" value="0"
                                            checked></td>
                                    <td><b> Pay By Myself</b></td>
                                </tr>
                                <tr>
                                    <td><input type="radio" name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"
                                            id="payment_when_<?= $apartment_id . "_" . $lease_id ?>_1" value="1"></td>
                                    <td><b>Have someone pay for you</b></td>
                                </tr>
                            </table>
                        </div>
                        <div class="wrapper-tabscontent-main">
                            <!-- style="display: none"-->
                            <table class="table table-bordered" style="margin: 0px; padding: 2px">
                                <tr>
                                    <td colspan="3" style="color: red">Please click on the icon below to pay</td>
                                </tr>
                                <tr>
                                    <!--                                            <th  class="text_clr">Pay Now</th>-->
                                    <th class="text_clr">Convenience Fee</th>
                                    <th class="text_clr">Total Amount</th>
                                    <th class="text_clr">Pay Now</th>
                                </tr>
                                <br>

                                <input id="paymentType<?= $apartment_id . "_" . $lease_id ?>" type="hidden"
                                    name="paymentType<?= $apartment_id . "_" . $lease_id ?>">
                                <?php
                                //    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false
                                //    || strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== false) {
                                //        echo "<h3 style='color:red'> Please use Firefox, Microsoft Edge or other browser. Please DO NOT use chrome for payment.</h3>";
                                //    }
                                ?>
                                <tr>
                                    <!-- <td> <input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>" checked  onclick="ew.vars.calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="m2"></td> -->
                                    <td><span
                                            id="span_interac_<?= $apartment_id . "-" . $lease_id ?>"><?= $interactCharge ?>
                                            $</span>
                                    </td>
                                    <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="m2" data-cf="0"
                                            class="totalAmtDisplayWCf"
                                            id="total_<?= $apartment_id . "-" . $lease_id ?>">0</span></td>
                                    <td><img src="custom/tenant_portal/images/interac3.png" alt="interac" height="30"
                                            onclick="ew.vars.submitPayForm(<?= $apartment_id ?>,<?= $lease_id ?>,'m2')"
                                            style="cursor: pointer"></button></td>
                                </tr>
                                <!-- <tr> -->
                                <!-- It was commented before <td><input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>" onclick="ew.vars.calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="p1"></td>-->
                                <!-- <td>
                                        <span id="span_paypal_<?= $apartment_id . "-" . $lease_id ?>"><?= $paypalCharge ?> %</span>
                                    </td>
                                    <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="p1"
                                              data-cf="2.90" class="totalAmtDisplayWCf"
                                              id="total_<?= $apartment_id . "-" . $lease_id ?>">0</span></td>
                                    <td><img align="center" src="custom/tenant_portal/images/paypal3.png"
                                             alt="pay_icon" height="30"
                                             onclick="ew.vars.submitPayForm(<?php echo $apartment_id; ?>,'p1')"
                                             style="cursor: pointer"></td>
                                </tr> -->
                                <tr>
                                    <!-- <td><input type="radio" name="paymentType<?= $apartment_id . "_" . $lease_id ?>"  onclick="ew.vars.calc<?= $apartment_id . "_" . $lease_id ?>(0)" size="10" value="m1"></td>-->
                                    <td>
                                        <span
                                            id="span_mastercard_<?= $apartment_id . "-" . $lease_id ?>"><?= $creditcardCharge ?>
                                            %</span>
                                    </td>
                                    <td><span data-aptid="<?php echo $apartment_id; ?>" data-ptype="m1" data-cf="2.9"
                                            class="totalAmtDisplayWCf"
                                            id="span_mastercard_<?= $apartment_id . "-" . $lease_id ?>">0</span>
                                    </td>
                                    <td><img align="left" src="custom/tenant_portal/images/moneris3.png"
                                            alt="Credit Card" height="30"
                                            onclick="ew.vars.submitPayForm(<?= $apartment_id ?>,<?= $lease_id ?>,'m1')"
                                            style="cursor: pointer"></td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" name="gateway" id="gateway" value="">
                        </form>

                    </div>
                </div>
                <div class="l_payment">
                    <?php
                    $tenant_accessbility = $DB_tenant->get_tenant_settings_about_request($user_id);
                    // die(print_r($tenant_accessbility));
                    $allow_create_request = $tenant_accessbility["allow_create_request"];
                    $ActiveStatus = array(1, 7, 8, 9, 10);

                    if (!in_array($lease_status_id, $ActiveStatus)) {
                        $allow_create_request = 0;
                        //    echo " it is inactive";
                    }
                    ?>
                    <div class="headings_box">

                        <div class="row">
                            <!-- <div class="col-md-5">
                                <a href="requestinfoslist" style="color: #7C7C7C">
                                    <button class="btn btn-primary"> <strong> Request List </strong> </button>
                                </a>
                            </div> -->
                            <div class="col-md-5">
                                <a href="requestinfosadd?lease_id=<?= $lease_id ?>&showdetail=">
                                    <button class="btn btn-primary"
                                        <?php echo $allow_create_request == 0 ? 'style="display:none;" ' : '' ?>>
                                        <strong>Report a Request</strong>
                                    </button>
                                </a>
                            </div>
                        </div>

                        <ul>
                            <!-- <li class="pull-left"><a   href="requests?unit_id=<?php echo $apartment_id; ?>"
                                                     style="color: #7C7C7C">
                                                       <button class="btn btn-primary">

                                                     Request List

                                                    </a>
                                                   </li> -->
                            <!-- <li class="pull-right"><a
                                        href="requests?direct=report&unit_id=<?php echo $apartment_id; ?>">
                                    <button class="btn btn-primary" <?php echo $allow_create_request == 0 ? 'style="display:none;" ' : '' ?>>
                                        Report a Request
                                    </button>
                                </a></li> -->
                            <!-- <div class="clearfix"></div> -->
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
                                        <!-- <th class="text_clr">Type</th> -->
                                        <th class="text_clr">Message</th>
                                        <th class="text_clr">Status</th>
                                        <th class="text_clr">View</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($issues as $row) {
                                            $message = $row['message'];
                                            $type = $row['request_type'];
                                            $status = $row['request_status'];
                                            // if ($row['last_access_time'] == '2001-01-01 00:00:00' || $row['last_update_time'] > $row['last_access_time']) {
                                            //     $readColor = " LightCoral";
                                            // } else {
                                            //     $readColor = " white";
                                            // }
                                        ?>
                                    <tr style="border-bottom:1px solid #D0D0D0; background-color: <? // echo $readColor
                                                                                                            ?> ">
                                        <!-- <td><?php echo $type; ?></td> -->
                                        <td title="<?php echo $message; ?>">
                                            <?php
                                                    $msglen = strlen($message);
                                                    // if ($msglen >= 50) {
                                                    //     echo substr($message, 0, 50) . " ...";
                                                    // } else {
                                                    echo $message;
                                                    // }
                                                    ?>
                                            <? //=$row['last_access_time'].">".$row['last_access_time']
                                                    ?>
                                        </td>
                                        <td><?php echo $status; ?></td>
                                        <td><a
                                                href="<?php echo 'requestcommunicationslist?showmaster=request_infos&fk_id=' . $row["request_id"] ?>"><button
                                                    class="btn btn-warning">View</button></a></td>
                                    </tr>
                                    <?php
                                        } ?>
                                </tbody>

                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                <div class="bulletins">
                    <div class="headings_box">
                        <ul>
                            <li class="pull-left">Notifications</li>
                            <li class="pull-right"><span><b></b> <img
                                        src="custom/tenant_portal/images/bulletins_icon.png" alt="building_icon"></span>
                            </li>
                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="points">
                        <ul>
                            <?php
                            $results = $DB_tenantLease->getRenewDetails($tenant_id, $lease_id);
                            if($results===false){die("No lease found for this tenant");}
                            $date_diff = round((strtotime($lease_end_date) - strtotime(date("Y-m-d"))) / 60 / 60 / 24);
                            if (!empty($results['renewal_notice_date']) && $results['renewal_notice_date'] != "0000-00-00") {
                                $renewal_due_date = date('Y-m-d', strtotime($results['renewal_notice_date'] . "+30 days"));
                            } else {
                                $renewal_due_date = "";
                            }
                            if (!in_array($results['lease_status_id'], [9]) && $date_diff <= $renewal_notification_day && !empty($results) && $results['renewal'] == 0) {

                            ?>
                            <li>
                                <h3 style="color:red">Renewal Notice</h3>
                                <h3><a href="custom/tenant_portal/renewal_notice.php?tenant_id=<?= $tenant_id ?>&lease_id=<?= $lease_id ?>"
                                        target="_blank">Please click here to see the detail</a></h3>
                                <hr>

                            </li>
                            <? if (!empty($renewal_due_date)) { ?>

                            <li>
                                <h6 style="color:red">Due Date to Accept/Not Accept of renewal of the lease:
                                    <b><?= $renewal_due_date ?></b>
                                </h6>
                            </li>
                            <?php }
                            } ?>



                            <?php
                            $bulletins = $DB_tenant->get_tenant_bulletin($user_id, $apartment_id);
                            //  die(var_dump($bulletins));
                            $secret = 'Bulletin';
                            foreach ($bulletins as $bulletin) {
                                $message_body = $bulletin['message_body'];
                                $file_id = $bulletin['id'];
                                $subject = $bulletin['message_title'];
                                $slug = md5($file_id . $secret);
                                $user_tracker_id = $tenant_id;
                                $history_type_id = 7;
                                $user_tracker_email = $tenant_infos['email'];
                                $id = $file_id; ?>
                            <li>
                                <?php echo $bulletin['create_time']; ?><br>
                                <b><?php echo $bulletin['message_title']; ?></b><br>
                            </li>
                            <div class="customComment" data-bs-trigger="hover"
                                title="<?php echo $bulletin['message_title']; ?>"
                                data-content="<?php echo strip_tags($message_body) ?>" data-placement="bottom">
                                <?php $msgLen = strlen($message_body);
                                    echo substr(strip_tags($message_body), 0, 70) . ($msgLen > 70 ? "..." : ""); ?>
                                <p>
                                    <?php if (!empty($bulletin['attachment'])) {
                                            echo "<a style='font-size:12pt' target='_blank'
                                    href='https://www.spgmanagement.com/admin/custom/download_file.php?fid=$slug&u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject'>attachment</a>";
                                        } ?>
                                </p>
                                <?php
                                    $history_type_id = 6;
                                    echo "<img border='0' src='https://www.spgmanagement.com/admin/custom/email_tracker.php?u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject' width='1' height='1' alt='' >"; ?>
                                <hr>
                            </div>
                            <?php
                            } ?>

                        </ul>

                    </div>
                </div>
            </div>

        </section>
    </main>
    <!--page-wrapper-->
</div>
<style>
.customComment:hover {
    cursor: pointer;
}

#prop_managment .bulletins .points {
    height: auto !important;
}

#prop_managment .bulletins {
    height: auto !important;
}

.text-align-left {
    text-align: left;
}
</style>
<script>
/*
loadjs.ready("jquery", function() {
    $(document).ready(function() {
        var
            payments_checked = {}; // To keep track of the payments selected in a building - Building id is an array key
        var total_payment = 0;

        ew.vars.pushIntoPaymentsChecked = function(aptId, paymentId) {
            if (!payments_checked.hasOwnProperty(aptId)) {
                payments_checked[aptId] = []; // Add apt id to the object
                payments_checked[aptId].push(paymentId);
            } else {
                if (payments_checked[aptId].indexOf(paymentId) == -1) {
                    payments_checked[aptId].push(paymentId);
                }
            }
        }

        ew.vars.popFromPaymentsChecked = function(aptId, paymentId) {
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

        ew.vars.calc<?= $apartment_id . "_" . $lease_id ?> = function(os,
            isChecked, thisObject) {
            console.log("calc" + <?= $apartment_id . "_" . $lease_id ?>)
            var aptId = thisObject.getAttribute('data-aptid');
            var paymentId = thisObject.getAttribute('data-pid');
            var total_amount = 0;
            var totalAmtWoCf = 0;

            m2 = <?= $interactCharge ?>; //0
            p1 = <?= $paypalCharge ?>; //3.10;
            m1 = <?= $creditcardCharge ?> //2.90;
            pay_amount = document.getElementById(
                    "pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                .value;
            console.log("pay_amount_<?= $apartment_id . "_" . $lease_id ?>",
                pay_amount)
            if (isChecked == 1) {
                ew.vars.pushIntoPaymentsChecked(aptId, paymentId);
                // total_amount = parseFloat(pay_amount) + parseFloat(os);
            } else {
                ew.vars.popFromPaymentsChecked(aptId, paymentId);
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
            console.log("total_amount", total_amount);
            totalAmtWoCf = total_amount;
            ew.vars.total_payment = total_amount;


            //for (var i = 0, length = radios.length; i < length; i++) {
            //    if (radios[i].checked) {
            //        radioValue = radios[i].value;
            //        break;
            //    }
            //}

            $(".totalAmtDisplayWCf").each(function(i, v) {
                if ($(v).attr("data-aptid") != aptId) {
                    return;
                }

                typeOfPayment = $(v).attr("data-ptype");
                switch (typeOfPayment) {
                    case "m1":
                        total_amount = ew.vars.total_payment + (
                            parseFloat(
                                ew.vars.total_payment) *
                            parseFloat(
                                m1) / 100);
                        total_amount = Math.round(100 *
                            total_amount) / 100;
                        $(v).html(total_amount);
                        break;
                    case "m2":
                        //total_amount = ew.vars.total_payment + (parseFloat(ew.vars.total_payment) * parseFloat(m2) / 100);
                        total_amount = ew.vars.total_payment +
                            parseFloat(
                                m2);
                        total_amount = Math.round(100 *
                            total_amount) / 100;
                        $(v).html(total_amount);
                        break;
                    case "p1":
                        total_amount = ew.vars.total_payment + (
                            parseFloat(
                                ew.vars.total_payment) *
                            parseFloat(
                                p1) / 100);
                        total_amount = Math.round(100 *
                            total_amount) / 100;
                        $(v).html(total_amount);
                        break;
                }
            });

            // Calculating the total + Convenience fees just to show to the user
            // For processing don't add the CF in the content page :
            // It is taken care of by Tianen in the checkout page

            document.getElementById(
                "span_total_amount_<?= $apartment_id . "_" . $lease_id ?>"
            ).innerHTML = totalAmtWoCf;
            document.getElementById(
                    "total_amount_<?= $apartment_id . "_" . $lease_id ?>")
                .value = totalAmtWoCf;
            $("#total_amount_<?= $apartment_id . "_" . $lease_id ?>").attr(
                "value", totalAmtWoCf);
            $("#pay_amount_<?= $apartment_id . "_" . $lease_id ?>").attr(
                "value", totalAmtWoCf);
            $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                .val(totalAmtWoCf);
        }

        $("document").ready(function() {
            $("body").find(
                'input[type="radio"][name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"]'
            ).on('change', function() {
                var checked_value = $(
                    'input:radio[name="payment_when_<?= $apartment_id . "_" . $lease_id ?>"]:checked'
                ).val();
                var totalPayment = $(".classtotal_payment_" +
                    <?php echo $apartment_id; ?>).val();

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


                    var leasePaymentIds = payments_checked[
                        <?php echo $apartment_id; ?>];
                    var inputElements = "";

                    for (var leasePaymentIndex in
                            leasePaymentIds) {
                        var leasePaymentIdSingle =
                            leasePaymentIds[leasePaymentIndex];
                        inputElements +=
                            "<input type='hidden' name='lease_payment_id[]' value='" +
                            leasePaymentIdSingle + "'> ";
                    }

                    $(".fwd_lease_payment_ids_div").empty()
                        .html(inputElements);

                    $(".fwd_payment_amount").val(totalPayment);
                    $(".fwd_if_partial_payment").val(ifpartial);
                }
            });
        });

        $('[name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>"]')
            .click(function() {
                var checked_value = $(
                    'input:radio[name="if_partial_payment_<?= $apartment_id . "_" . $lease_id ?>"]:checked'
                ).val();
                if (checked_value == 1) { //partial payment
                    $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                        .removeAttr("disabled");
                    document.getElementById(
                        "partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>"
                    ).value = document.getElementById(
                        "total_amount_<?= $apartment_id . "_" . $lease_id ?>"
                    ).value;
                } else { // full payment
                    $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                        .val("");
                    $("#partial_pay_amount_<?= $apartment_id . "_" . $lease_id ?>")
                        .attr("disabled", true);
                }
            });

        $("body").on("blur", ".partial_payment_entry_amt", function(e) {
            e.stopImmediatePropagation();
            var apt_id = $(this).attr("data-aptid");
            var amountEntered = parseFloat($(this).val().trim());

            var apt_id_current_tab = $(".aptsnavpills").children()
                .filter(".active").attr("data-aptid");

            if (apt_id_current_tab = apt_id) {
                if (isNaN($(this).val())) {
                    $(this).val(ew.vars.total_payment);
                    return;
                }

                if (parseInt($(this).val()) < 1) {
                    $(this).val(ew.vars.total_payment);
                    return;
                }

                $(".classtotal_payment_" + apt_id).val(amountEntered);
                $(".classtotal_payment_" + apt_id).next().attr("value",
                    amountEntered);

                // Check if the amount entered in the partial payment is greater than the total amount which is calculated
                if (amountEntered > parseFloat(ew.vars.total_payment)) {
                    $(this).val(ew.vars.total_payment);
                    $(".classtotal_payment_" + apt_id).val(
                        ew.vars.total_payment);
                    $(".classtotal_payment_" + apt_id).next().attr(
                        "value", ew.vars.total_payment);
                }
            }
        });

        ew.vars.submitPayForm = function(aptId, lease_id, paymentMethod) {
            if (payments_checked[aptId] && payments_checked[aptId].length <
                1) {
                alert("Please select the payment");
                return false;
            }
            if (ew.vars.total_payment < 1) {
                alert("There is no total to pay");
                return false;
            }
            $("#paymentType" + aptId + "_" + lease_id).attr("value",
                paymentMethod);
            $("#gateway").val(paymentMethod);


            // Strat Disable temproray the payment process//
            //   $("#form_" + aptId + "_" + lease_id).submit();
            alert(
                "Sorry for any inconvenience. Payment is temporarily disabled. Please contact management office for payment."
            )
            // End of  Disable temproray the payment process //

        }
    });
});
*/
</script>
<script>
loadjs.ready(["jquery", "head"], function() {
    $(document).ready(function() {
        $('[]').popover();
        $('form').trigger("reset");


        var rowId = <?= $scrollrowpos + $depositRowNo ?>;

        var Tablepos = $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr:eq(' + rowId + ')')
            .offset()
            .top;
        var divPayPos = $('#div_pay<?= $apartment_id . "-" . $lease_id ?>').offset().top;
        var goPos = Tablepos - divPayPos;
        $('#div_pay<?= $apartment_id . "-" . $lease_id ?>').scrollTop(goPos);
        $('#div_pay<?= $apartment_id . "-" . $lease_id ?>').animate({
            scrollTop: goPos
        }, 500);
        $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr').css({
            'background-color': 'none'
        });
        $('#table_pay<?= $apartment_id . "-" . $lease_id ?> tr:eq(' + rowId + ')').css({
            'background-color': '#ccc'
        });

        /* Hide Payment
                document.getElementById("select" + "<?= $first_not_paid_lease_id ?>").click();
                document.getElementById("payment_when_" + "<?= $apartment_id . "_" . $lease_id ?>_0").click();
        */


    });
});
</script>
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->