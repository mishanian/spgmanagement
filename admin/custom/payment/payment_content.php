<?php
// namespace PHPMaker2023\spgmanagement;
// use \PDO, \Building, \Payment;
$root = dirname(__DIR__);
include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('../pdo/Class.Payment.php');
$DB_payment = new Payment($DB_con);
$companyId = false;
$employeeId  = false;

if (isset($_SESSION["company_id"])) {
    $companyId = $_SESSION["company_id"];
}
if (isset($_SESSION["employee_id"])) {
    $employeeId = $_SESSION["employee_id"];
}
$employeeId = 54;
$companyId = 9;
?>

<input type="hidden" value="<?php echo $companyId; ?>" id="companyIdValue" />
<div class="container">

    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#home">Item</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#menu1" id="payment_tab">Payment</a></li>
    </ul>

    <form method="post" class="form-horizontal" action="custom/checkout.php" id="process_form">
        <div class="tab-content">

            <div id="home" class="tab-pane active">
                <h3>Item Details</h3>
                <div class="row custom_well">
                    <div class="col-sm-10">
                        <div class="form-group row">
                            <label class="control-label col-sm-2" for="item_name">Item Name:</label>
                            <div class="col-sm-4">
                                <input type="text" name="item_name" class="form-control" id="item_name" placeholder="Item Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-2" for="item_detail">Item Details:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="item_detail" id="item_detail" placeholder="Enter additional information about the item to be sold.">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-2" for="item_detail">Date of Sale</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="item_sale_date" name="item_sale_date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-2" for="item_building_id">Building</label>
                            <div class="col-sm-4">
                                <!--                                <input type="text" class="form-control" id="item_building_id" >-->
                                <select title="Building Select" name="building_filterpayment" id="building_filterpayment" class="form-control">
                                    <option value="0">Select a Building</option>
                                    <?php
                                    $allBuildings = $DB_building->getAllBdRowsByCompany($companyId);
                                    foreach ($allBuildings as $singleBuilding) {
                                        if ($singleBuilding["company_id"] != $companyId) {
                                            continue;
                                        } ?>
                                        <option <?php
                                                if (isset($_GET["bid"])) {
                                                    if ($_GET["bid"] == $singleBuilding["building_id"]) {
                                                        echo "selected";
                                                    }
                                                } ?> value="<?php echo $singleBuilding["building_id"]; ?>"> <?php echo $singleBuilding["building_name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="item_unit_id_display" style="display: none;">
                            <label class="control-label col-sm-2" for="item_unit_id">Unit</label>
                            <div class="col-sm-4">
                                <select title="Building Select" name="item_unit_id" id="item_unit_id" class="form-control"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-2" for="item_sale_amt">Amount</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="item_sale_amt" id="item_sale_amt" placeholder="Sale Amount">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label><input id="customer_tenant_toggle" name="customer_tenant_toggle" type="checkbox"> Is Customer a Tenant ?</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group hidden" id="item_sale_tenant_toggle">
                            <label class="control-label col-sm-2" for="item_sale_tenant">Select Tenant</label>
                            <div class="col-sm-4">
                                <input type="text" class="white-bg" id="item_sale_tenant" name="item_sale_tenant" placeholder="Select a Tenant to attach to this sale." readonly>
                                <input type="hidden" class="form-control" id="item_sale_tenant_id" name="item_sale_tenant_id">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary" id="browsetenant_select"> Browse </button>
                            </div>
                        </div>

                        <div class="form-group row" id="item_sale_customer_toggle">
                            <label class="control-label col-sm-2" for="item_sale_customer">Customer Name</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="item_sale_customer" id="item_sale_customer" placeholder="Customer's Full Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="submit_payment_item" name="submit_payment_item" type="button" class="btn btn-default">Next <i class="fas fa-arrow-right"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="menu1" class="tab-pane fade">
                <h3>Payment Details</h3>
                <hr>

                <h4> You are paying $<span id="payment_amount">_</span> for the item : <span id="payment_item_name">_</span></h4>
                <hr>
                <input type="hidden" id="pay_payment_amount_form" name="pay_payment_amount" value="0" /> <!-- Payment amount in the form tosend to the checkout.php page -->
                <input type="hidden" id="payment_amount_form" name="payment_amount" value="0" /> <!-- Payment amount in the form tosend to the checkout.php page -->
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employeeId; ?>" />

                <?php
                //obtain the convenience fee from settings
                $convenience_rules = $DB_payment->get_convenience_fee_rate();
                $CF_PP_Balance_P = $convenience_rules['CF_PP_Balance_F'];
                $CF_PP_CC_P = $convenience_rules['CF_PP_CC_P'];
                $CF_M_CC_P = $convenience_rules['CF_M_CC_P'];
                $CF_M_Interac_F = $convenience_rules['CF_M_Interac_F'];
                $outstanding = 20.00;
                ?>

                <div class="hidden_cf_values">
                    <input type="hidden" id="CF_PP_Balance_F" name="CF_PP_Balance_F" value="<?php echo $CF_PP_Balance_P; ?>" />
                    <input type="hidden" id="CF_PP_CC_P" name="CF_PP_CC_P" value="<?php echo $CF_PP_CC_P; ?>" />
                    <input type="hidden" id="CF_M_CC_P" name="CF_M_CC_P" value="<?php echo $CF_M_CC_P; ?>" />
                    <input type="hidden" id="CF_M_Interac_F" name="CF_M_Interac_F" value="<?php echo $CF_M_Interac_F; ?>" />
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist" id="nav_tabs_payment">
                            <li id="paynow_tag" role="presentation" class="nav-item"><a class="nav-link active" href="#pay_now" aria-controls="pay_now" role="tab" data-toggle="tab">Pay Now</a></li>
                            <li id="fwd_pay_tag" role="presentation" class="nav-item"><a class="nav-link" href="#pay_forward" aria-controls="pay_forward" role="tab" data-toggle="tab">Forward this Payment</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- pay now panel -->
                            <div role="tabpanel" class="tab-pane active row" id="pay_now">

                                <div class="table-responsive whitebg alert ">
                                    <table class="table table-bordered table-hover table-striped" id="paymentoptionsTable">
                                        <thead>
                                            <tr>
                                                <th class="col-md-2 text-center">Pay By</th>
                                                <th class="col-md-1 text-center">Service Charge</th>
                                                <th class="col-md-1 text-center">Total</th>
                                                <th class="col-md-1 text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="gateway" class="payment_method_radio needs_dataamount_value" data-method="CF_PP_Balance_F" id="gateway1" value="p1" required> Paypal Balance
                                                    </label>
                                                </td>
                                                <td class="text-center"><?php echo number_format($CF_PP_Balance_P, 2); ?>%</td>
                                                <td class="text-center"><b id="o_1" data-method="CF_PP_Balance_F" class="needs_html_value"> </b> $ CAD <input type="hidden" class="needs_input_value" data-method="CF_PP_Balance_F" name="outstanding1" value="0"> </td>
                                                <td class="text-center"><img src="images/paypal64.png"></td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <label><input type="radio" class="payment_method_radio needs_dataamount_value" data-method="CF_PP_CC_P" name="gateway" id="gateway2" value="p2" required> Credit Card via Paypal</label>
                                                </td>
                                                <td class="text-center"><?php echo number_format($CF_PP_CC_P, 2); ?>%</td>
                                                <td class="text-center"><b data-method="CF_PP_CC_P" class="needs_html_value" id="o_2"></b> $ CAD <input class="needs_input_value" data-method="CF_PP_CC_P" type="hidden" name="outstanding2" value="0"> </td>
                                                <td class="text-center">
                                                    <img src="images/paypal64.png"> <img src="images/visa64.png"> <img src="images/mastercard64.png">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <label>
                                                        <input data-method="CF_M_CC_P" type="radio" name="gateway" class="payment_method_radio needs_dataamount_value" id="gateway3" value="m1" required> Credit Card via Moneris
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo number_format($CF_M_CC_P, 2); ?>%
                                                </td>
                                                <td class="text-center">
                                                    <b id="o_3" data-method="CF_M_CC_P" class="needs_html_value"></b> $ CAD <input data-method="CF_M_CC_P" class="needs_input_value" type="hidden" name="outstanding3" value="0">
                                                </td>
                                                <td class="text-center">
                                                    <img src="images/visa64.png"> <img src="images/mastercard64.png">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <label> <input data-method="CF_M_Interac_F" type="radio" name="gateway" class="payment_method_radio needs_dataamount_value" id="gateway4" value="m2" required> <b>INTERAC<sup>®</sup></b> Online Service</label>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo number_format($CF_M_Interac_F, 2); ?>$
                                                </td>
                                                <td class="text-center">
                                                    <b id="o_4" data-method="CF_M_Interac_F" class="needs_html_value"></b> $ CAD <input data-method="CF_M_Interac_F" class="needs_input_value" type="hidden" name="outstanding4" value="0">
                                                </td>
                                                <td class="text-center">
                                                    <img src="images/interac_online_log.png" height="40">
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <!-- forward payment panel -->
                            <div role="tabpanel" class="tab-pane row" id="pay_forward">
                                <div class="col-sm-12">
                                    <br>
                                    <div class="row form-group">
                                        <div class="col-sm-2 col-md-2 col-lg-2 text-left">Recipient Name :</div>
                                        <div class="col-sm-5 col-md-5 col-lg-5">
                                            <input type="text" id="forward_recipient_name" name="forward_recipient_name" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-2 col-md-2 col-lg-2 text-left">Recipient Email :</div>
                                        <div class="col-sm-5 col-md-5 col-lg-5">
                                            <input type="email" id="forward_recipient_email" name="forward_recipient_email" class="form-control input-width">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-2 col-md-2 col-lg-2 text-left">Recipient Mobile :</div>
                                        <div class="col-sm-5 col-md-5 col-lg-5">
                                            <input type="text" id="forward_recipient_mobile" name="forward_recipient_mobile" class="form-control input-width">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-2 col-md-2 col-lg-2 text-left">Message to Recipient:</div>
                                        <div class="col-sm-5 col-md-5 col-lg-5">
                                            <textarea id="forward_message" name="forward_message" class="form-control" rows="7" cols="56" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <button id="sale_form_submit" class="btn btn-primary">Process Now!</button>
                </div>
                <input type="hidden" name="product" value="saleItem" />
            </div>

        </div>
    </form>

</div>


<!--Scripts needed for this page -- not included by PHPMaker-->
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="custom/payment/css/style.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="custom/payment/js/payments.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> -->
<script>
    loadjs.ready(["jquery", "head"], function() {
        loadjs([
            "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css",
            "https://code.jquery.com/ui/1.12.1/jquery-ui.js",
            "https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css",
            "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js",
        ], 'datatable');
    });


    loadjs.ready(["datatable"], function() {
        loadjs([
            "https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js",
            "custom/payment/css/style.css",
            "custom/payment/js/payments.js"
        ], 'jsloaded');
    });
</script>




<div class="modal fade" id="tenantSelectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Select a tenant!</h4>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="table-responsive whitebg alert">
                            <table class="table table-bordered" id="tenantSelecttable">
                                <thead>
                                    <tr>
                                        <th class="col-md-2 text-center">Name</th>
                                        <th class="col-md-1 text-center">Email</th>
                                        <th class="col-md-1 text-center">Building</th>
                                        <th class="col-md-1 text-center">Select</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>