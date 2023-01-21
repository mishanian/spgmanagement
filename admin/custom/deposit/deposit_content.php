<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$loggedInUserId = 0;
if (PHPMaker2023\spgmanagement\Security()->isLoggedIn()) {
    $loggedInUserId = PHPMaker2023\spgmanagement\CurrentUserInfo("user_id");
}

// Logged In User ID for use in JS
echo "<input type='hidden' value='" . $loggedInUserId . "' id='loggedInUserId' >";

include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('../pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('../pdo/Class.LeasePayment.php');
$DB_ls_payment = new LeasePayment($DB_con);
include_once('../pdo/Class.Lease.php');
$DB_lease  = new Lease($DB_con);
include_once("../pdo/Class.Apt.php");
$DB_apt = new Apt($DB_con);



$companyId = false;

if (isset($_SESSION["company_id"])) {
    $companyId = $_SESSION["company_id"];
}
?>
<style>
.page-link {
    color: #0056b3 !important;
}

.alert a {
    color: #0056b3 !important;
}
</style>

<div class="container-fluid" id="body">
    <div class="row form-group ">
        <div class="col-lg-6">
            <select id="building_filterdeposit" class="form-control">
                <option value="0">All Building</option>
                <?php
                $allBuildings = $DB_building->getAllBdRows();
                foreach ($allBuildings as $singleBuilding) {
                    if ($singleBuilding["company_id"] != $companyId) {
                        continue;
                    } ?>
                <option <?php
                            if (isset($_GET["bid"])) {
                                if ($_GET["bid"] == $singleBuilding["building_id"]) {
                                    echo "selected";
                                }
                            } ?> value="<?php echo $singleBuilding["building_id"]; ?>">
                    <?php echo $singleBuilding["building_name"]; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <?php
    $bidDisplayConstraint = "";
    $bid = null;
    if (!isset($_GET["bid"])) {
        // $bidDisplayConstraint = "noDisplay";
    } else {
        $bid = $_GET["bid"];
    }
    ?>

    <?php
    $buildingInfo = $DB_building->getBdInfo($bid);
    if (!empty($buildingInfo)) {
        $bankAccount = $buildingInfo["bank_account"];
    } else {
        $bankAccount = "";
    }
    $ownerName = $DB_company->getName($companyId);
    ?>

    <div class="row form-group  <?php echo $bidDisplayConstraint; ?>">
        <div class="col-lg-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation"><a class="nav-link active" data-bs-toggle="tab"
                        href="#chequeTab">Transactions</a>
                </li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"
                        href="#cashDetailsTab">Cash Details</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab"
                        href="#depositSlipReportTab" id="ahref_depositslip">Deposit Slip Reports</a></li>
            </ul>
        </div>
    </div>

    <div class="row form-group ">
        <div class="col-lg-8">
            <td>
                <div class="input-group">
                    <span class="input-group-prepend input-group-text"> <strong>Name of the Account </strong></span>
                    <input id="buildingNameofAccount" type="text" class="form-control" style="background: white;"
                        name="" value="<?php echo $ownerName; ?>">
                </div>
            </td>
        </div>
    </div>

    <div class="row form-group ">
        <div class="col-lg-4">
            <td>
                <div class="input-group">
                    <span class="input-group-prepend input-group-text"> <strong>Date</strong></span>
                    <input id="buildingDate" type="text" class="form-control" value="<?php echo date("Y-m-d"); ?>"
                        style="background: white;" name="" readonly>
                </div>
            </td>
        </div>
        <div class="col-lg-4">
            <td>
                <div class="input-group">
                    <span class="input-group-prepend input-group-text"> <strong>Branch No</strong></span>
                    <input id="buildingBranchNo" type="text" class="form-control" value="<?php ?>"
                        style="background: white;" name="">
                </div>
            </td>
        </div>
        <div class="col-lg-4">
            <td>
                <div class="input-group">
                    <span class="input-group-prepend input-group-text"> <strong>Account No</strong></span>
                    <input id="buildingAccountNo" type="text" class="form-control" value="<?php echo $bankAccount; ?>"
                        style="background: white;" name="">
                </div>
            </td>
        </div>
    </div>

    <div class="row form-group">

        <div class="<?php echo $bidDisplayConstraint; ?> tab-content col-md-12">
            <div id="chequeTab" class="tab-pane active">

                <div class="table-responsive whitebg alert">
                    <table class="table table-bordered table-hover table-condensed" style="width:100%" id="chequeTable">
                        <thead>
                            <tr>
                                <th class="col-md-1 td-center"><input class="transactionSelectAll" type="checkbox" />
                                </th>
                                <th class="col-md-2 text-center">Building</th>
                                <th class="col-md-1 text-center">Paid</th>
                                <th class="col-md-1 text-center">Date</th>
                                <th class="col-md-1 text-center">Type</th>
                                <th class="col-md-2 text-center">Cheque Details</th>
                                <th class="col-md-1 text-center">Cheque Verified?</th>
                                <th class="col-md-2 text-center">Cheque Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $payments = null;
                            // if (!$bid) {
                            //     echo "Building data not set.";
                            // } else {
                            $payments = $DB_ls_payment->getAllPaymentsByCompanyIdDeposit($bid, $companyId);

                            $cashTransactions = 0;
                            // }


                            if ($payments) {
                                foreach ($payments as $singlePayment) {
                                    $leaseInfo = $DB_lease->getLeaseInfoByLeaseId($singlePayment["lease_id"]);
                                    $buildingId = $leaseInfo["building_id"];
                                    $apartmentId = $leaseInfo["apartment_id"];
                                    $buildingName = $DB_building->getBdName($buildingId);
                                    $apartmentNo = $DB_apt->getAptInfo($apartmentId)["unit_number"];
                                    $paymentMethod = $singlePayment["payment_method"];

                                    if (!$singlePayment["amount"]) {
                                        $paidAmt = intval($singlePayment["subtotalamount"]) + intval($singlePayment["cfamount"]);
                                    } else {
                                        $paidAmt = $singlePayment["amount"];
                                    }

                                    $paidAmt = sprintf("%.2f", $paidAmt);

                                    $disabledLabel = "";
                                    $paymentMethodTypeForDiv = 0;

                                    $paymentMethodName = "Cheque";
                                    if ($paymentMethod == 3) {
                                        $cashTransactions++;
                                        $paymentMethodName = "Cash";
                                        $disabledLabel = "disabled";
                                        $paymentMethodTypeForDiv = 1;
                                    }

                            ?>
                            <tr data-bid="<?php echo $buildingId; ?>"
                                class="<?php echo 'transrow_' . $singlePayment["payment_details_id"]; ?>"
                                data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                data-amt="<?php echo $paidAmt; ?>" data-ptype="<?php echo $paymentMethodTypeForDiv; ?>">
                                <td data-ptype="<?php echo $paymentMethodTypeForDiv; ?>" class="td-center"><input
                                        data-ptype="<?php echo $paymentMethodTypeForDiv; ?>"
                                        data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                        class="transactionSelect <?php echo 'transrowselect_' . $singlePayment["payment_details_id"]; ?>"
                                        type="checkbox" /></td>
                                <td nowrap><?php echo $buildingName . ' - ' . $apartmentNo; ?></td>
                                <td class="text-center">$<?php echo $paidAmt; ?></td>
                                <td class="text-center">
                                    <?php echo date_format(date_create($singlePayment["payment_date"]), "d-m-y"); ?>
                                </td>
                                <td class="text-center">
                                    <div name="transaction_paytype"
                                        data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                        class="form-control transaction_paytype"
                                        id="<?php echo 'transaction_paytype_' . $singlePayment["payment_details_id"] ?>">
                                        <?php echo $paymentMethodName; ?> </div>
                                </td>
                                <td nowrap>
                                    <input <?php echo $disabledLabel; ?> type="text"
                                        data-pamount="<?php echo $paidAmt; ?>"
                                        data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                        class="form-control cheque_id_entry" placeholder="Cheque No/Details"
                                        value="<?php echo $paymentMethodName == 'Cheque' ? $singlePayment['payment_detail'] : '' ?>"
                                        id="<?php echo 'chequedetailentry_' . $singlePayment["payment_details_id"]; ?>">
                                </td>
                                <td class="td-center">
                                    <input <?php echo $disabledLabel; ?>
                                        data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                        class="text-center check_cheque"
                                        id="<?php echo 'chequeChecked_' . $singlePayment["payment_details_id"]; ?>"
                                        type="checkbox" />
                                </td>
                                <td nowrap>
                                    <div class="input-group mb-12">
                                        <div class="input-group-prepend"><span class="input-group-text"
                                                id="dol_pre">$</span></div>
                                        <input <?php echo $disabledLabel; ?> type="text"
                                            data-pamount="<?php echo $paidAmt; ?>"
                                            data-pid="<?php echo $singlePayment["payment_details_id"]; ?>"
                                            class="form-control cheque_amount_entry"
                                            id="<?php echo 'chequeamountentry_' . $singlePayment["payment_details_id"]; ?>"
                                            placeholder="Amount" aria-describedby="dol_pre">
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>


            </div>

            <div id="cashDetailsTab" class="tab-pane fade">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive whitebg alert">
                                    <table class="table table-bordered table-hover table-condensed" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1">Cash Count</th>
                                                <th class="col-md-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            /***
                                             * Make object for cash denomination count and  iterate to generate the table below for cash.
                                             */
                                            $cashDeposit_Denomination = array(
                                                array("cashCount5_div", "cashCount5", "cashCount5_amt", "5", 5),
                                                array("cashCount10_div", "cashCount10", "cashCount10_amt", "10", 10),
                                                array("cashCount20_div", "cashCount20", "cashCount20_amt", "20", 20),
                                                array("cashCount50_div", "cashCount50", "cashCount50_amt", "50", 50),
                                                array("cashCount100_div", "cashCount100", "cashCount100_amt", "100", 100),
                                                array("cashCount100_div", "cashCount1", "cashCount1_amt", "$ 1 COIN", 1),
                                                array("cashCount100_div", "cashCount2", "cashCount2_amt", "$ 2 COIN", 2),
                                                array("cashCountcoinmisc_div", "cashCountcoinmisc", "cashCountcoinmisc_amt", "COINS", 0)
                                            );

                                            foreach ($cashDeposit_Denomination as $denomination) { ?>

                                            <tr id="<?php echo $denomination[0]; ?>">
                                                <td>
                                                    <div class="input-group">
                                                        <input data-denom="<?php echo $denomination[4]; ?>"
                                                            id="<?php echo $denomination[1]; ?>" type="text"
                                                            class="form-control cash_count_entry" name="count_5"
                                                            placeholder="Count">
                                                        <span class="input-group-prepend  input-group-text"><small><strong>X
                                                                </strong></small> <?php echo $denomination[3]; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        id="<?php echo $denomination[2]; ?>" placeholder="Amount"
                                                        readonly style="background: white;">
                                                </td>
                                            </tr>

                                            <?php } ?>

                                            <!--  End of generating the Denomination rows -->
                                            <tr>
                                                <td class="text-right">CASH SUBTOTAL</td>
                                                <td>
                                                    <input type="text" class="form-control" id="cash_subtotal_display">
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="depositSlipReportTab" class="tab-pane fade">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive whitebg alert">
                            <table class="table table-bordered table-condensed" style="width:100%"
                                id="depositSlipReportTable">
                                <thead>
                                    <tr>
                                        <!-- <th class="col-md-2 text-center">Id</th> -->
                                        <!-- <th class="col-md-2 text-center">Type</th> -->
                                        <th class="col-md-2 text-center">Deposit Date</th>
                                        <th class="col-md-1 text-center">Building</th>
                                        <th class="col-md-1 text-center">Deposit Amount</th>
                                        <th class="col-md-1 text-center">Deposited by</th>
                                        <th class="col-md-2 text-center">Report</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div style="display:none;" id="totalswarning" class="alert alert-danger">
        <strong>Note!</strong>
        <span></span>
    </div>

    <div style="display:none;" id="depositsuccessmessage" class="alert alert-success">
        <strong>Success!</strong>
        <span>Selected transactions are deposited.</span>
    </div>


    <div class="row form-group well  <?php echo $bidDisplayConstraint; ?>">
        <div class="col-lg-12">
            <p>Please <strong>Click </strong> on Cash Details if you have selected any cash transaction.</p>
            <ul class="nav nav-tabs">
                <li class="nav-item active"><a class="nav-link" data-bs-toggle="tab" href="#chequeTab">Transactions</a>
                </li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#cashDetailsTab">Cash Details</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Cheque Subtotal and the number of cheques -->
    <div class="<?php echo $bidDisplayConstraint; ?> table-responsive col-md-7 whitebg alert">
        <table class="table table-bordered table-condensed" style="width:100%">
            <tbody>
                <tr>
                    <td colspan="1" style="text-align: right;"><strong>Paid Total</strong></td>
                    <td>
                        <input id="paidtotaldisplay" value="$0" type="text" class="form-control"
                            style="background: white;" name="" readonly>
                    </td>
                </tr>

                <tr>
                    <td colspan="1" style="text-align: right;"><strong>Cheque Subtotal </strong></td>
                    <td>
                        <input id="chequesubtotaldisplay" value="$0" type="text" class="form-control"
                            style="background: white;" name="" readonly>
                    </td>
                </tr>

                <tr>
                    <td colspan="1" style="text-align: right;"><strong>Cash Subtotal </strong></td>
                    <td>
                        <input id="cashsubtotaldisplay2" value="$0" type="text" class="form-control"
                            style="background: white;" name="" readonly>
                    </td>
                </tr>

                <tr>
                    <td colspan="1" style="text-align: right;"><strong>Total # of Cheques</strong></td>
                    <td>
                        <input id="chequecountdisplay" type="text" class="form-control" style="background: white;"
                            name="" readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <!--    Deposit button and print -->
    <div class="<?php echo $bidDisplayConstraint; ?> row form-group">
        <div class="col-sm-offset-2 col-md-5">
            <button class="btn btn-danger btn-block" id="depositfinalbtn">
                <strong>Deposit</strong>
            </button>
        </div>

        <div class="col-sm-offset-2 col-md-3">
            <form action="custom/deposit/ds_print.php" method="post" id="printformdeposit" target="_blank">
                <input type="hidden" name="printchequedata" id="printchequedata">
                <input type="hidden" name="printcashdata" id="printcashdata">
                <input type="hidden" name="printchequesubtotal" id="printchequesubtotal">
                <input type="hidden" name="printcashsubtotal" id="printcashsubtotal">
                <input type="hidden" name="printpaidtotal" id="printpaidtotal">
                <input type="hidden" name="printDate" id="printDate">
                <input type="hidden" name="printAccountNo" id="printAccountNo">
                <input type="hidden" name="printBranchNo" id="printBranchNo">
                <input type="hidden" name="printNameOfAccount" id="printNameOfAccount">
                <input type="hidden" name="building_id" id="building_id">
                <button style="display:none;" type="submit" class="btn btn-primary btn-block" id="printdeposit">
                    <strong>Print</strong>
                </button>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="chequesubtotal" value="0" />
<input type="hidden" id="cashtransactions" value="<?php echo $cashTransactions; ?>" />
<input type="hidden" id="paidtotal" value="0" />
<input type="hidden" id="cashsubtotal" value="0" />

<!-- Style and scripts includes -->

<style>
.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.highlight {
    background: #b9e7ef;
}

.whitebg {
    background: white;
}

.column {
    float: left;
    width: 50%;
}

.myrow:after {
    content: "";
    display: table;
    clear: both;
}

.noDisplay {
    display: none;
}

.td-center {
    text-align: center;
    /* center checkbox horizontally */
    vertical-align: middle;
    /* center checkbox vertically */
}
</style>

<script>
loadjs.ready(["jquery", "head"], function() {
    loadjs([
        "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css",
        "https://code.jquery.com/ui/1.13.2/jquery-ui.min.js",
        "https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css",
        "https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js",
    ], 'datatable');
});


loadjs.ready(["datatable"], function() {
    loadjs([
        "https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js",
    ], 'jsloaded');
});
</script>
<script>
loadjs.ready('jsloaded', function() {
    $("document").ready(function() {
        var transactionSelectAllPageWise = [];
        var checkedRowsPid = {};
        var depositError = false;
        var chequeChecked = [];
        var paymentTypesCash = 0;
        var buildingIdSelected = 0;
        var buildingNameSelected = "";

        var chequeTable = $('#chequeTable').DataTable({
            "stateSave": true,
            "columns": [{
                    "orderable": false,
                    "width": "5%"
                },
                {
                    "width": "25%"
                },
                {
                    "width": "10%"
                },
                {
                    "width": "10%"
                },
                {
                    "width": "10%"
                },
                {
                    "width": "10%"
                },
                {
                    "width": "10%"
                },
                {
                    "width": "10%"
                },
            ]
        });


        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // If the date filter is to be applied
                if (buildingIdSelected != 0) {
                    var rowBuildingValue = $.parseHTML(data[1]);
                    // if(rowBuildingValue.indexOf(buildingNameSelected) != -1){
                    //   return true;
                    // }
                    // return false;
                }
                return true;
            }
        );

        jQuery.fn.dataTableExt.oSort['us_date-asc'] = function(a, b) {
            var x = new Date(a),
                y = new Date(b);
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        };

        jQuery.fn.dataTableExt.oSort['us_date-desc'] = function(a, b) {
            var x = new Date(a),
                y = new Date(b);
            return ((x < y) ? 1 : ((x > y) ? -1 : 0));
        };

        $("#building_filterdeposit").on("change", function() {
            buildingIdSelected = $(this).val();
            $("#building_id").val(buildingIdSelected);

            // alert($("#building_id").val());
            buildingNameSelected = $(this).text();
            console.log(buildingIdSelected);
            window.location.href = location.protocol + "//" + location.host + location
                .pathname + "?bid=" + buildingIdSelected;
        });

        // Functions below to calculate the Paid Subtotal Automatically and when any rows are checked/unchecked

        /**
         * Calculate the Cheque subtotals for the selected rows in the page
         * @param row
         */
        var calculatePaidSubtotal = function(row_amount, op) {
            var row_amount = parseFloat(row_amount); // Amount for the selected transaction row
            var subtotalValue = parseFloat($("#paidtotal").val());

            if (op == "add") { // Add the row amount to the existing subtotal value if the op is add
                var newSubtotal = row_amount + subtotalValue;
            } else { // Subtract the row amount from the existing subtotal value if the op is sub
                var newSubtotal = subtotalValue - row_amount;
            }

            newSubtotal = newSubtotal.toFixed(2); // Round of to nearest two decimal places

            if (newSubtotal < 0) {
                newSubtotal = 0;
            }

            $("#paidtotal").val(newSubtotal); // Assign the new value to the input field
            $("#paidtotaldisplay").val("$" +
                newSubtotal); // Assign the new value to the input field
        };

        var unsetPaidSubtotal = function() {
            $("#paidtotal").val(0); // Assign the new value to the input field
            $("#paidtotaldisplay").val("$0"); // Assign the new value to the input field
        };

        /**
         * Function to check if all the rows in the page are checked.
         * Check If the number of rows present in the page and the number number of checked rows match
         */
        var checkIfAllRowsChecked = function() {
            // iterate over all the rows in the table
            // Count the number of rows present in the table
            var numberOfRowsDisplayedCurrently = $("#chequeTable").find("tbody").children().length;
            var checkedCount = 0;

            $.each($("#chequeTable").find("tbody").children(), function(i, v) {
                if ($(v).find(".transactionSelect").prop("checked")) {
                    checkedCount++; // Add to the number of rows checked if an individual row is found checked
                }
            });

            // If the checkedCount and number of rows currently displayed in the page are equal -
            // check the selectAllCheckbox field in the thead of the table
            // Also if all the rows are checked - push the current page index to the array transactionSelectAllPageWise
            // to keep track of which page has all the rows checked
            if (checkedCount == numberOfRowsDisplayedCurrently) {
                $(".transactionSelectAll").prop("checked", true); // Checking the selectAllCheckbox
                transactionSelectAllPageWise.push(chequeTable.page.info()
                    .page); // Pushing the current page number to array
            }
        };

        // Cheque Checked checkbox
        $("body").on("click", ".check_cheque", function() {
            var pid = $(this).attr("data-pid");
            var checkedOrUncheck = $(this).prop("checked");
            if (checkedOrUncheck) {
                chequeChecked.push(pid);
            } else {
                var indexOfPid = chequeChecked.indexOf(pid);
                if (indexOfPid !== -1) {
                    chequeChecked.splice(indexOfPid, 1);
                }
            }
        });

        // Checkbox select - highlight and do the calculation
        // Check if the SelectAllCheckbox is checked : If it's checked - Uncheck it
        $("body").on("click", ".transactionSelect", function() {
            // Highlight the row
            $(this).closest("tr").toggleClass("highlight");
            var pid = $(this).attr("data-pid");
            var ptype = parseInt($(this).attr("data-ptype"));

            // Calculate the sum of the selected rows and show in the subtotal
            if ($(this).prop("checked")) {
                calculatePaidSubtotal($(this).closest("tr").attr("data-amt"), "add");

                pushThePaymentIdRowFromTheArray($(this).closest("tr").attr("data-pid"), $(this)
                    .closest("tr").attr("data-amt"));

                // Check if all the rows in the page are checked : if they are all checked individually
                // Check the SelectAllCheckbox too
                checkIfAllRowsChecked();

                if (ptype == "1" || ptype == 1) {
                    paymentTypesCash++;
                }
            } else {
                calculatePaidSubtotal($(this).closest("tr").attr("data-amt"), "sub");
                popThePaymentIdRowFromTheArray($(this).closest("tr").attr("data-pid"));
                // Unset the SelectAllCheckbox when a individual row checkbox is checked and the SelectAllCheckbox is checked
                if ($(".transactionSelectAll").prop("checked")) {
                    $(".transactionSelectAll").prop("checked", false);
                    removePageIndexFromTheSelectedAllArray();
                }

                if (ptype == 1) {
                    paymentTypesCash--;
                }
            }

            // Number of cheques selected - to be deposited
            $("#chequecountdisplay").val(Object.keys(checkedRowsPid).length);
            compareChequeCashSubtotals();

            // console.log(paymentTypesCash);
        });

        function pushThePaymentIdRowFromTheArray(pid, amt) {
            pid = pid.toString();
            if (!checkedRowsPid.hasOwnProperty(pid)) {
                checkedRowsPid[pid] = {}; // Push the checked row Payment_Id to the Array
                checkedRowsPid[pid].amt = amt;
            }

            // Check if the Cheque ID and cheque amount are not empty
            if (checkIfChequeDataFilled(pid, "chequedetailentry_")) {
                var chequeIdValue = $("#chequedetailentry_" + pid).val();
                checkedRowsPid[pid].chequeid = chequeIdValue;
            }
            if (checkIfChequeDataFilled(pid, "chequeamountentry_")) {
                var chequeIdValue = $("#chequeamountentry_" + pid).val();
                checkedRowsPid[pid].chequeamt = chequeIdValue;
            }

            var ptype = $(".transrow_" + pid).attr("data-ptype");
            checkedRowsPid[pid].ptype = ptype;
        }

        /**
         * Delete the PaymentIDRow from the array when it's unchecked.
         */
        var popThePaymentIdRowFromTheArray = function(pid) {
            pid = pid.toString();
            if (checkedRowsPid.hasOwnProperty(pid)) {
                delete checkedRowsPid[pid];
            }

        };

        var removePageIndexFromTheSelectedAllArray = function() {
            var indexOfPage = transactionSelectAllPageWise.indexOf(chequeTable.page.info().page);
            if (indexOfPage !== -1) {
                transactionSelectAllPageWise.splice(indexOfPage, 1);
            }
        };

        // Uncheck all the individual rows in the page.
        var uncheckAllRows = function() {
            $.each($("#chequeTable").find("tbody").children(), function(i, v) {
                $(v).find(".transactionSelect").prop("checked", false);
                calculatePaidSubtotal($(v).attr("data-amt"));
            });
        };

        // Checkbox select - highlight all the rows in the current page
        $(".transactionSelectAll").on("click", function() {
            var checkBoxAllVal = $(this).prop("checked");

            // Storing the Page number when SelectAllCheckbox is checked on a page
            // This array is used to retain the selectAllChecbox checked value when the user comes back that page
            // This is done to maintain consistency
            if (checkBoxAllVal) {
                // uncheckAllRows();
                transactionSelectAllPageWise.push(chequeTable.page.info().page);
            } else {
                removePageIndexFromTheSelectedAllArray();
            }

            $("#paidtotal").val(
                "0"
            ); // If this is not set to 0 :- the value will be wrong ; Reset the Paid total calculated value to 0 since we will calculate all the values in the checkedRowsPid object again which will included the already existing values sum

            // Check all the checkboxes in the children rows
            $.each($("#chequeTable").find("tbody").children(), function(i, v) {
                var ptype = parseInt($(v).attr(
                    "data-ptype")); // payment type for the selected transaction

                if (checkBoxAllVal) {
                    $(v).addClass("highlight");
                    pushThePaymentIdRowFromTheArray($(v).attr('data-pid'), $(v).attr(
                        'data-amt'));
                    if (ptype == "1" || ptype == 1) {
                        paymentTypesCash++;
                    }
                    // calculatePaidSubtotal(v,"add");
                } else {
                    $(v).removeClass("highlight");
                    popThePaymentIdRowFromTheArray($(v).attr('data-pid'));
                    if (ptype == 1) {
                        paymentTypesCash--;
                    }
                    // calculatePaidSubtotal(v,"sub");
                }

                // Set the individual checkboxes checked or unchecked based on the value of the header  checkAll checkbox
                $(v).find(".transactionSelect").prop("checked", checkBoxAllVal);
            });

            if (!checkBoxAllVal) {
                unsetPaidSubtotal();
            } else {
                $.each(checkedRowsPid, function(i, v) {
                    if (checkBoxAllVal) {
                        calculatePaidSubtotal(v.amt, "add");
                    }
                });
            }

            $("#chequecountdisplay").val(Object.keys(checkedRowsPid).length);
            compareChequeCashSubtotals();

            console.log("Cash transactions in this :" + paymentTypesCash);

        });

        $('#chequeTable').on('page.dt', function() {
            $(".transactionSelectAll").prop("checked", false);
            var pageInfo = chequeTable.page.info();

            // Cheque if the index of the new changed page exists in the array transactionSelectAllPageWise
            // If the index exists : check the selectAllCheckbox to maintain consistency
            var pageIndex = transactionSelectAllPageWise.indexOf(pageInfo.page);

            if (pageIndex != -1) {
                // Check the Header checkbox selectAllCheckbox
                // Reason to do this : When shifting between pages : the datatables is retaining the individual row checkboxes and
                // it doesnt retain the header checkbox : selectAllCheckbox  ; In order to maintain data correctness - manually checking the
                // SelectAllCheckbox by knowing if the page index exists in the array in which the page numbers of selectAllCheckbox is checked
                $(".transactionSelectAll").prop("checked", true);
            }

            _isWindowBlurEvent = false;
        });

        /***************************************************************************************************************
         *  Functions below to calculate the Cheques Subtotal when any data is entered into the Cheque Amount field    *
         ***************************************************************************************************************
         ***/

        /* variable to track what kind of blur event is fired */
        var _isWindowBlurEvent = false;

        window.onblur = function(e) {
            _isWindowBlurEvent = true;
        };

        window.onfocus = function(e) {
            _isWindowBlurEvent = false;
        };

        $("body").on("blur", ".cheque_id_entry", function(e) {
            if (!_isWindowBlurEvent) //if the event is Element.onBlur
            {
                var chequeId = $(this).val();

                var pid = $(this).attr("data-pid");
                var paidAmt = $(this).attr("data-pamount");

                if (chequeId.length < 1) {
                    return;
                }

                var chequeAmount = $("#chequeamountentry_" + pid).val();

                if (!chequeAmount || chequeAmount.length < 1) {
                    chequeAmount = 0;
                }
                addChequeDataToCheckedRowsPid(pid, paidAmt, chequeAmount, chequeId)
            }
        });


        // When the Cheque amount is entered and field is focused out
        $("body").on("blur", ".cheque_amount_entry", function(e) {
            if (!_isWindowBlurEvent) //if the event is Element.onBlur
            {
                var amountValue = $(this).val();

                if (isNaN(amountValue)) {
                    // Amount entered is not a valid number
                    alert("Enter a valid amount.");
                    return;
                }

                // If the Amount value is empty - could be a case of : new data entry or removal of existing amount value
                if (amountValue.length == 0) {
                    var amountValueRemoved = $(this).attr("data-value-entered");
                    if (!amountValueRemoved || amountValueRemoved.length < 1) {
                        return;
                    }
                    $(this).attr("data-value-entered", ""); // Unset the data attribute value
                    calculateChequeSubtotal(amountValueRemoved,
                        "sub"); // Calculate the Cheque Subtotals and show in the display field
                } else {
                    // Check if there is a value already in the field - dont add it again
                    // Subtract the existing value and then add the new value to the sub total
                    if ($(this).attr("data-value-entered") && ($(this).attr(
                            "data-value-entered").length > 0)) {
                        calculateChequeSubtotal($(this).attr("data-value-entered"), "sub");
                    }

                    // Store the value entered in a data attribute in the element
                    var amountValueEntered = $(this).attr("data-value-entered", amountValue);
                    calculateChequeSubtotal(amountValue, "add");
                }

                var chequeId = $("#chequedetailentry_" + $(this).attr("data-pid")).val();
                var paidAmt = $(this).attr("data-pamount");

                if (!chequeId && chequeId.length < 1) {
                    chequeId = "";
                }

                addChequeDataToCheckedRowsPid($(this).attr("data-pid"), paidAmt, amountValue,
                    chequeId);

                // Compare the subtotals
                compareChequeCashSubtotals();
            }
        });

        var calculateChequeSubtotal = function(amountValue, op) {
            var chequeSubtotalValue = parseFloat($("#chequesubtotal").val());
            row_amount = parseFloat(amountValue);

            if (op == "add") { // Add the row amount to the existing subtotal value if the op is add
                var newSubtotal = row_amount + chequeSubtotalValue;
            } else { // Subtract the row amount from the existing subtotal value if the op is sub
                var newSubtotal = chequeSubtotalValue - row_amount;
            }

            newSubtotal = newSubtotal.toFixed(2); // Round of to nearest two decimal places

            if (newSubtotal < 0) {
                newSubtotal = 0;
            }

            $("#chequesubtotal").val(newSubtotal); // Assign the new value to the input field
            $("#chequesubtotaldisplay").val("$" +
                newSubtotal); // Assign the new value to the input field
        };

        // When the cheque data is entered - put it into the checkedRowsPid object for later use in print page
        var addChequeDataToCheckedRowsPid = function(pid, paidAmt, chequeAmount, chequeId) {
            pushThePaymentIdRowFromTheArray(pid, paidAmt); // If the row doesn't exist - push it

            var ptype = $(".transrow_" + pid).attr("data-ptype");

            checkedRowsPid[pid].chequeid = chequeId;
            checkedRowsPid[pid].chequeamt = chequeAmount;
            checkedRowsPid[pid].ptype = ptype;

            // Everytime the cheque data is changed, it's better to recalculate the paid subtotal from the object checkedRowsPid to avoid error
            unsetPaidSubtotal();
            $.each(checkedRowsPid, function(i, v) {
                calculatePaidSubtotal(v.amt, "add");
            });

            // Because the Cheque amount and the cheque id are added - highlight this transaction row
            $("#chequedetailentry_" + pid).closest("tr").addClass("highlight");
            $(".transrowselect_" + pid).prop("checked", true);
        }

        var checkIfChequeDataFilled = function(pid, field) {
            var fieldValue = $("#" + field + pid).val();
            if (fieldValue.length > 0 || fieldValue != "") {
                return true;
            }
            return false;
        }

        /***************************************************************************************************************
         *  Functions below to calculate the Cash Subtotal when any data is entered into the Cash Amount field    *
         ***************************************************************************************************************
         */

        // Compare the cheque+cash subtotals with the paid total
        function compareChequeCashSubtotals() {
            // Compare cheque subtotal and cash subtotal
            var chequeSubtotal = parseFloat(parseFloat($("#chequesubtotal").val()).toFixed(2));
            var cashSubtotal = parseFloat(parseFloat($("#cashsubtotal").val()).toFixed(2));
            var total = parseFloat(chequeSubtotal + cashSubtotal);
            var paidtotal = parseFloat(parseFloat($("#paidtotal").val()).toFixed(2));
            var balancepaid = parseFloat((paidtotal - (chequeSubtotal + cashSubtotal)).toFixed(2));
            // Totals are equal
            if (total === paidtotal) {
                // Hide the Warning alert if shown
                $("#totalswarning").hide();
                depositError = false;
            } else {
                // Show the alert warning
                $("#totalswarning").find("span").html(
                    "Cash and Cheque Subtotals do not match. Difference is : <strong>" +
                    balancepaid + "</strong>");
                $("#totalswarning").fadeIn();
                depositError = true;
            }
        }

        // When a cash count is entered
        $("body").on("change", ".cash_count_entry", function(e) {
            if (!_isWindowBlurEvent) //if the event is Element.onBlur
            {
                var denomination_count = parseInt($(this)
                    .val()); // Count of the cash for a denomination
                var denomination = parseInt($(this).attr(
                    "data-denom"
                )); // Denomination = Cash Amount Value ; eg: 5 | 10 | 20 | 100 | $1 | $2

                if (denomination_count.length < 1) {
                    return;
                }

                if (denomination_count.length > 0 && isNaN(denomination_count)) {
                    alert("Please enter a valid number");
                    $(this).focus();
                    return;
                }

                amount_target_id = $(this).attr("id") + "_amt";

                if (isNaN(denomination_count)) {
                    $("#" + amount_target_id).val("");
                    var cashSubtotal = 0;
                    $.each($(".cash_count_entry"), function(i, v) {
                        var count_entered = parseInt($(v).val());
                        var id = $(v).attr("id");

                        if (count_entered != '' || count_entered.length > 0 || !isNaN(
                                count_entered)) {
                            if ($("#" + id + "_amt").val().length > 0) {
                                cashSubtotal += parseFloat(parseFloat($("#" + id +
                                    "_amt").val()).toFixed(2));
                            }
                        }
                    });
                    console.log("Cash sub " + cashSubtotal);
                    $("#cashsubtotal").val(cashSubtotal);
                    $("#cash_subtotal_display,#cashsubtotaldisplay2").val("$" + cashSubtotal);
                    compareChequeCashSubtotals();
                    return;
                }

                var cashCount_denomination = denomination_count * denomination;

                if (denomination == 0) {
                    cashCount_denomination = (denomination_count * 1) / 100;
                }

                $("#" + amount_target_id).val(cashCount_denomination);

                var cashSubtotal = 0;

                $.each($(".cash_count_entry"), function(i, v) {
                    var count_entered = parseInt($(v).val());
                    var id = $(v).attr("id");

                    if (count_entered != '' || count_entered.length > 0 || !isNaN(
                            count_entered)) {
                        if ($("#" + id + "_amt").val().length > 0) {
                            cashSubtotal += parseFloat($("#" + id + "_amt").val());
                        }
                    }
                });

                $("#cashsubtotal").val(cashSubtotal);
                $("#cash_subtotal_display,#cashsubtotaldisplay2").val("$" + cashSubtotal);

                compareChequeCashSubtotals();
            }
        });

        /***************************************************************************************************************
         *  Functions below to Submit the transactions and make an AJAX call to the deposit controller
         ***************************************************************************************************************
         */
        /*
                $("body").on("click", "#lclrepo,.reportLink", function(e) {

                    e.preventDefault();
                    var id = $(this).closest("tr").attr("data-id");
                    console.log("click report", id)
                    window.location.href = $(this).attr("href") + "?fromPid=" + id;
                });
        */
        var depositSlipReportTable = $('#depositSlipReportTable').DataTable({
            "ajax": {
                "url": "custom/deposit/deposit_controller.php",
                "type": "POST",
                "data": {
                    "action": "reportData"
                }
            },
            "columns": [
                // {
                //     "data": "recordId"
                // },
                // {
                //     "data": "typeName"
                // },
                {
                    "data": "depositDate"
                },
                {
                    "data": "building"
                },
                {
                    "data": "paidTotal"
                },
                {
                    "data": "depositBy"
                },
                {
                    "data": "link"
                }
            ],
            "order": [
                [0, "desc"]
            ],
            "rowCallback": function(row, data) {
                // Custom data attributes for the row items
                $(row).attr('data-id', data.recordId);
                $(row).addClass('rowLink');
            }
        });

        $("#depositfinalbtn").on("click", function(e) {
            // Check if the number of transactions checked are not 0
            var numberOfTransactionsSelected = Object.keys(checkedRowsPid).length;

            if (numberOfTransactionsSelected < 1) {
                alert("Choose atleast 1 transaction to deposit.");
                return;
            }

            if (depositError) {
                return;
            }

            var numberOfCashTransactions = paymentTypesCash;
            var numberOfChequesChecked = chequeChecked.length;

            if (numberOfTransactionsSelected - numberOfCashTransactions !=
                numberOfChequesChecked) {
                console.log(numberOfTransactionsSelected);
                console.log(numberOfCashTransactions);
                console.log(numberOfChequesChecked);
                alert(
                    "Please make sure to verify the Cheques before depositing for the selected transaction records."
                );
                return;
            }

            // Get all the PID's of the selected transactions and mark them as deposited
            $.ajax({
                url: "custom/deposit/deposit_controller.php",
                type: "POST",
                dataType: "json",
                data: {
                    action: "deposit",
                    data: checkedRowsPid,
                    userId: $("#loggedInUserId").val(),
                    depositDate: $("#buildingDate").val()
                },
                success: function(response) {
                    if (response) {
                        if (numberOfTransactionsSelected == parseInt(response)) {
                            $("#depositsuccessmessage").fadeIn();

                            // Store the page data into the print form for it to take to the ds_print.php page as POST parameter
                            $("#printchequedata").val(JSON.stringify(
                                checkedRowsPid));
                            $("#printcashsubtotal").val($("#cashsubtotal").val());
                            $("#printchequesubtotal").val($("#chequesubtotal")
                                .val());
                            $("#printpaidtotal").val($("#paidtotal").val());
                            $("#printAccountNo").val($("#buildingAccountNo").val());
                            $("#printBranchNo").val($("#buildingBranchNo").val());
                            $("#printNameOfAccount").val($("#buildingNameofAccount")
                                .val());
                            $("#printDate").val($("#buildingDate").val());

                            var cashObj = {};
                            // Make a cash data object to send to print page
                            $.each($(".cash_count_entry"), function(i, v) {
                                var id = $(v).attr("id");
                                var denomination = $(v).attr("data-denom");
                                var value = $(v).val();
                                var amount = $("#" + id + "_amt").val();

                                cashObj[id] = {};
                                cashObj[id]["count"] = value;
                                cashObj[id]["amount"] = amount;
                                cashObj[id]["denom"] = denomination;
                            });

                            $("#printcashdata").val(JSON.stringify(cashObj));

                            $("#printdeposit").fadeIn();

                            var depositRecordObj = {
                                chequeData: checkedRowsPid,
                                cashData: cashObj,
                                cashSubtotal: $("#cashsubtotal").val(),
                                chequeSubtotal: $("#chequesubtotal").val(),
                                paidTotal: $("#paidtotal").val(),
                                accountNo: $("#buildingAccountNo").val(),
                                branchNo: $("#buildingBranchNo").val(),
                                nameOfAccount: $("#buildingNameofAccount")
                                    .val(),
                                date: $("#buildingDate").val(),
                                userId: $("#loggedInUserId").val(),
                                buildingId: $("#building_filterdeposit").val()
                            };

                            // Create deposit record slip record for report generation later
                            $.ajax({
                                url: "custom/deposit/deposit_controller.php",
                                type: "POST",
                                dataType: "json",
                                data: {
                                    action: "createDepositRec",
                                    data: depositRecordObj
                                },
                                success: function(response) {
                                    // console.log("success");
                                    depositSlipReportTable.ajax
                                        .reload();
                                    // location.reload();
                                    $("#ahref_depositslip").click();
                                }
                            });

                        }
                    }
                }
            });

        });
    });
});
</script>