<?php
$employeeId = null;
$companyId = null;
if (isset($_SESSION["employee_id"])) {
    $employeeId = $_SESSION["employee_id"];
}
if (isset($_SESSION["company_id"])) {
    $companyId = $_SESSION["company_id"];
}

include_once("../pdo/dbconfig.php");
include_once('../pdo/Class.Employee.php');
$DB_employee  = new Employee($DB_con);
include_once('../pdo/Class.Building.php');
$DB_building  = new Building($DB_con);
include_once('../pdo/Class.Snapshot.php');
$DB_snapshot  = new Snapshot($DB_con);
include_once('../pdo/Class.LeasePayment.php');
$DB_ls_payment = new LeasePayment($DB_con);


$employeeInfo = $DB_employee->getEmployeeInfo($employeeId);

// Fetch all rows of the building data and filter by Employee ID and Company ID
$allBuildingsFromTable = $DB_building->getAllBdRowsByCompany($companyId);
$allBuildings = array();

if (intval($employeeInfo["admin_id"]) == 1) { // This user is an admin -  show all the buildings
    $allBuildings = $allBuildingsFromTable;
} else {
    $employeeData = $DB_employee->getEmployeeInfo($employeeId);
    $buildingIds = $employeeData["building_ids"];
    $allBuildingsArray = explode(",", $buildingIds);
    foreach ($allBuildingsArray as $bId) {
        array_push($allBuildings, $DB_building->getBdInfo($bId));
    }
}
// die(print_r($allBuildingsFromTable ));
// Selected Date format
//$date = new DateTime('2018-01-06');

$date = new DateTime();
$dateToday = $date->format('m/d/Y');

$selectedDay = $date->format('Y-m-d 11:00:00');

// Previous Date format
$date->modify('-1 day');
$previousDay = $date->format('Y-m-d 11:00:00');

if (isset($_GET['datereq']) && !empty($_GET['datereq'])) {
    $dateFilter = explode("/", trim($_GET['datereq']));
    $dateSelectedFilter = $dateFilter[2] . '-' . $dateFilter[1] . '-' . $dateFilter[0];
    $date = new DateTime($dateSelectedFilter);
    $selectedDay = $date->format('Y-m-d 11:00:00');

    // Previous Date format
    $date->modify('-1 day');
    $previousDay = $date->format('Y-m-d 11:00:00');
}
?>

<input type="hidden" id="val_selectedDate" value="<?php echo $selectedDay; ?>" />
<input type="hidden" id="val_previousDate" value="<?php echo $previousDay; ?>" />

<!--Begin Content-->
<div class="container">
    <div class="row form-group">
        <div class="col-md-12">
            <button class="btn btn-success"
                id="generateReportAllBldgs"><?php echo $DB_snapshot->echot("Generate Report"); ?></button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="col-md-12">
                <div class="form-group row">
                    <label for="paymentDateFilter"
                        class="col-2 col-form-label"><?php echo $DB_snapshot->echot("Date"); ?></label>
                    <div class="col-10">

                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="datepicker" class="form-control"
                                placeholder="<?php echo $DB_snapshot->echot("Select a date"); ?>" value=<? if
                                (!empty($_GET['datereq'])) { echo $_GET['datereq']; } ?>>
                            <div class="input-group-addon" style="display:none;">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>

                        <!--                        <input class="form-control" type="text" id="paymentDateFilter" value="<?php echo $selectedDay; ?>" />-->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="col-md-12">
                <div class="form-group row">
                    <label for="buildingFilter"
                        class="col-2 col-form-label"><?php echo $DB_snapshot->echot("Building"); ?></label>
                    <div class="col-10">
                        <select class="form-control" id="buildingFilter">
                            <option value="#"><?php echo $DB_snapshot->echot("Select Building"); ?></option>
                            <?php foreach ($allBuildings as $index => $building) { ?>
                            <option value="<?php echo $building['building_id']; ?>">
                                <?php echo $building['building_name']; ?> </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="col-md-12">
                <div class="form-group row">
                    <label for="resetFilter"
                        class="col-2 col-form-label"><?php echo $DB_snapshot->echot("Filter"); ?></label>
                    <div class="col-10">
                        <button id="resetFilter"
                            class="form-control btn-info btn"><?php echo $DB_snapshot->echot("Reset Filter"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--BODY CONTENT FOR TABLE-->
    <div class="row form-group">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table id="eftTable" class="table table-fixed table-condensed" style="background:white;">
                    <thead>
                        <tr>
                            <th><?php echo $DB_snapshot->echot("Building"); ?></th>
                            <th><?php echo $DB_snapshot->echot("Transaction Date"); ?></th>
                            <th><?php echo $DB_snapshot->echot("EFT Total"); ?></th>
                            <th><?php echo $DB_snapshot->echot("Transaction Status"); ?></th>
                            <th><?php echo $DB_snapshot->echot("Number of Payments"); ?></th>
                            <th><?php echo $DB_snapshot->echot("Details"); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // If bulding request filter is selected
                        if (isset($_GET['brequest_id'])) {
                            $buildingName = $DB_building->getBdName($_GET['brequest_id']);
                            echo get_payment_row($DB_snapshot, $DB_ls_payment, $buildingName, $selectedDay, $previousDay, $_GET['brequest_id']);
                        } else {
                            foreach ($allBuildings as $index => $building) {
                                echo get_payment_row($DB_snapshot, $DB_ls_payment, $building['building_name'], $selectedDay, $previousDay, $building['building_id']);
                            }
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payments Details modal  -->
    <div class="modal fade" id="paymentsDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $DB_snapshot->echot("Details"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table  table-fixed table-condensed">
                                    <tbody>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Building"); ?></td>
                                            <td style="text-align:right;" class="td_bname">{building_name}</td>
                                        </tr>

                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Transaction Date"); ?></td>
                                            <td class="td_tdate" style="text-align:right;">{transaction_date}</td>
                                        </tr>

                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("EFT Total"); ?></td>
                                            <td class="td_total" style="text-align:right;">{eft_total}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--                    <div class="row form-group">
                                            <div class="col-md-12">
                                                <button class="btn btn-success pull-right">Export CSV</button>
                                            </div>
                                        </div>-->

                    <div class="row form-group payments-details">
                        <div class="col-md-12">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $DB_snapshot->echot("Payment Date"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Order ID"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Building"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Payment Method"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Entered By"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Total"); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="paymentViewBody">
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo $DB_snapshot->echot("Close"); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rent Invoice Details modal  -->
    <div class="modal fade" id="rentInvoiceDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $DB_snapshot->echot("Payment Invoice Details"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;margin:7px;">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table  table-fixed table-condensed">
                                    <tbody>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Tenant"); ?></td>
                                            <td style="text-align:right;" class="td_itname"></td>
                                        </tr>

                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Building"); ?></td>
                                            <td class="td_ibname" style="text-align:right;"></td>
                                        </tr>

                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Unit"); ?></td>
                                            <td class="td_iunit" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Due Date"); ?></td>
                                            <td class="td_idue" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Rent"); ?></td>
                                            <td class="td_irent" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Parking"); ?></td>
                                            <td class="td_iparking" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Storage"); ?></td>
                                            <td class="td_istorage" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Late Fee"); ?></td>
                                            <td class="td_ilatefee" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Total"); ?></td>
                                            <td class="td_itotal" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Paid"); ?></td>
                                            <td class="td_ipaid" style="text-align:right;"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $DB_snapshot->echot("Status"); ?></td>
                                            <td class="td_istatus" style="text-align:right;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group rentInvoice-details">
                        <div class="col-md-12">
                            <h4><strong><?php echo $DB_snapshot->echot("Transactions"); ?></strong></h4>
                            <div class="table-responsive">
                                <table class="table  table-fixed table-condensed">
                                    <thead>
                                        <tr>
                                            <th><?php echo $DB_snapshot->echot("Timestamp"); ?>Timestamp</th>
                                            <th><?php echo $DB_snapshot->echot("Date Paid"); ?></th>
                                            <th><?php echo $DB_snapshot->echot("Rent"); ?></th>
                                            <th><?php echo $DB_snapshot->echot("Entered By"); ?></th>
                                            <th><?php echo $DB_snapshot->echot("Payment Method"); ?></th>
                                            <th><?php echo $DB_snapshot->echot("Payment Description"); ?></th>
                                            <th><?php echo $DB_snapshot->echot("Comments"); ?></th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rentalInvoiceBody">
                                        <tr>
                                            <td class="rentalInvoice-tstamp"></td>
                                            <td class="rentalInvoice-date"></td>
                                            <td class="rentalInvoice-Rent"></td>
                                            <td class="rentalInvoice-EnteredBy"></td>
                                            <td class="rentalInvoice-pmethod"></td>
                                            <td class="rentalInvoice-pdesc"></td>
                                            <td class="rentalInvoice-comments"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo $DB_snapshot->echot("Close"); ?></button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
/*
 *  Frame a payment row to show in the table
 *  @param $DB_ls_payment - Lease Payment Object
 *  @param $name - Name of the building
 *  @param $previousDay - Previous date formatted
 *  @param $selectedDay -  Current date formatted
 *  @param $buildingID  -  Building ID from the building_infos table
 */

function get_payment_row($DB_snapshot, $DB_ls_payment, $name, $selectedDay, $previousDay, $buildingId, $type = 0)
{

    $paymentData = $DB_ls_payment->getPaymentsData($buildingId, $selectedDay, $previousDay);

    // echo "<pre>";
    // print_r($paymentData);

    $paymentSum = 0;
    if (count($paymentData) > 0) {
        foreach ($paymentData as $paymentIndex => $payment) {
            $paymentSum += ($payment['amount'] + $payment['cf_amount']);
        }
    }

    $paymentCount = count($paymentData);

    $formattedSelecteddate = new DateTime($selectedDay);
    $formattedSelectedDay = $formattedSelecteddate->format('Y-m-d h:m:s');

    $formattedPreviousdate = new DateTime($previousDay);
    $formattedPreviousDay = $formattedPreviousdate->format('Y-m-d h:m:s');

    /*
     * Framing HTML for TD for the table
     */
    return <<<HTML
        <tr>
        <td> $name </td>
        <td>$formattedPreviousDay - $formattedSelectedDay </td>
        <td>$$paymentSum</td>
        <td>&nbsp;</td>
        <td> $paymentCount </td>
        <td>
            <button data-bname="$name" data-sdate="$selectedDay" data-bid="$buildingId" data-bdate="$formattedPreviousDay - $formattedSelectedDay" data-tot="$paymentSum" class="btn btn-primary paymentView" role="button" data-toggle="modal" >
                {$DB_snapshot->echot('Payments')} <span class="badge">$paymentCount</span>
             </button>
        <td>
            <button type="button" class="btn btn-default" id="downloadBuildingReport"  data-sdate="$selectedDay" data-bid="$buildingId">
                <span class="glyphicon glyphicon-download-alt"></span>
                {$DB_snapshot->echot('Download')} <span class="badge">$paymentCount</span>
            </button>
        </td>
    </tr>
HTML;
}
?>

<!-- <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />  -->
<!--Script for Filters-->
<script>
loadjs.ready(["jquery", "head"], function() {
    loadjs([
        "https://printjs-4de6.kxcdn.com/print.min.js",
        "https://printjs-4de6.kxcdn.com/print.min.css",
        "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js",
        "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css",
    ], 'jsloaded');
});


loadjs.ready(['head', 'jsloaded'], function() {
    $('document').ready(function() {

        $('#paymentDateFilter').datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $.fn.datepicker.defaults.format = "y-MM-dd";
        $('.datepicker').datepicker({
            startDate: new Date()
        }).change(function(e) {
            location.href = location.protocol + '//' + location.host + location.pathname +
                "?datereq=" + $(this).val();
        });

        //        Building Filter Select Input - Change Event
        $('#buildingFilter').change(function() {
            location.href = location.protocol + '//' + location.host + location.pathname +
                "?brequest_id=" + this.value;
        });


        $('#resetFilter').on("click", function() {
            location.href = location.protocol + '//' + location.host + location.pathname;
        });

        //       Date Filter - Change Event
        $('#paymentDateFilter').change(function(value) {
            var _date = $('#paymentDateFilter').val();
            location.href = location.protocol + '//' + location.host + location.pathname +
                "?datereq=" + this.value;
        });

        $('#generateReportAllBldgs').on('click', function() {
            var _totalPaymentAmt = $('#totalPaymentAmtReport').html();
            var _transactionDateRange = $('#val_selectedDate').val() + " - " + $(
                '#val_previousDate').val();

            $('.totalPaymentAmtReport').html(_totalPaymentAmt);
            $('.transRangeReport').html(_transactionDateRange)
            $("#eft_allbuildings_report").show();
            printJS('eft_allbuildings_report', 'html');
            $("#eft_allbuildings_report").hide();
        });

        //       Payments View Button click Event
        $('.paymentView').on('click', function() {

            var _urlParams = getAllUrlParams();
            //            var _dateFilter = null;
            //            if (_urlParams.datereq != null) {
            //                _dateFilter = _urlParams.datereq;
            //            }

            _dateFilter = $(this).data(
                'sdate'); // Send the properly formatted Date to Controller

            // Remove Dynamic TD elements when the Payments button is cliced again
            $('#paymentViewBody').empty();

            var _bName = $(this).data('bname');
            var _tDate = $(this).data('bdate');
            var _eftTotal = $(this).data('tot');
            var _bId = $(this).data('bid');

            var modalBody = $('#paymentsDetailsModal').find(".modal-body");

            modalBody.find('.td_bname').html(_bName);
            modalBody.find('.td_tdate').html(_tDate);
            modalBody.find('.td_total').html("$" + _eftTotal);

            $.ajax({
                url: 'custom/report/report_payment_controller.php',
                type: 'POST',
                data: {
                    request: 'paymentData',
                    b_id: _bId,
                    date: _dateFilter
                },
                dataType: 'json',
                success: function(response) {
                    if (response.data == true) {
                        for (payment in response.value) {
                            var _paymentData = response.value[payment];

                            var totalProper = _paymentData.total == null ? "-" :
                                "$" + _paymentData.total;

                            var rowHTML = "<tr> \n\
                                    <td>" + _paymentData.payment_date + "</td> \n\
                                    <td>" + _paymentData.orderID + "</td> \n\
                                    <td>" + _paymentData.bname + "</td> \n\
                                    <td>" + _paymentData.method + "</td> \n\
                                    <td>" + _paymentData.enteredBy + "</td> \n\
                                    <td>$" + _paymentData.total + "</td> \n\
                                    <td> <button data-pmethod='" + _paymentData.method + "' data-enteredBy='" +
                                _paymentData.enteredBy + "' data-tdate='" +
                                _paymentData.payment_date + "' data-paidAmt='" +
                                _paymentData.total + "' data-bname='" + _paymentData
                                .bname + "' data-oid='" + _paymentData.orderID +
                                "' data-payid='" + _paymentData.paymentID +
                                "' class='btn btn-info rentInvoiceViewBtn'> Rent Invoice Details </button> </td> </tr>";

                            $('#paymentViewBody').append(rowHTML);
                        }
                        // Open the Payments Modal
                        $('#paymentsDetailsModal').modal('show');
                    }
                }
            });


        });

        $("body").on('click', '.rentInvoiceViewBtn', function() {
            // Hide the Payment details modal and show the rent invoice details modal
            $('#paymentsDetailsModal').modal('hide');
            $('#rentInvoiceDetailsModal').modal('show');

            var _paymentId = $(this).data('payid');
            var _bName = $(this).data('bname');
            var _oid = $(this).data('oid');
            var _paidAmt = $(this).data('paidamt');
            var _paymentDate = $(this).data('tdate');
            var _enteredBy = $(this).data('enteredBy');
            var _pmethod = $(this).data('pmethod');
            var paymentDateSplit = _paymentDate.split(/[ ,]+/);

            $.ajax({
                url: 'custom/report/report_payment_controller.php',
                type: 'POST',
                data: {
                    request: 'invoiceData',
                    payId: _paymentId,
                    paymentDetId: _oid
                },
                dataType: 'json',
                success: function(response) {

                    var modalBody = $('#rentInvoiceDetailsModal').find(
                        ".modal-body");

                    //                  Appending data to the existing table rows
                    modalBody.find('.td_itname').html(response.tenant);
                    modalBody.find('.td_ibname').html(_bName);
                    modalBody.find('.td_iunit').html(response.unit);
                    modalBody.find('.td_idue').html(response.due);
                    modalBody.find('.td_irent').html("$" + response.total);
                    modalBody.find('.td_istorage').html("$" + response
                        .storage_amount);
                    modalBody.find('.td_ilatefee').html("$0");
                    modalBody.find('.td_iparking').html("$" + response
                        .parking_amount);
                    modalBody.find('.td_itotal').html("$" + response.total);
                    modalBody.find('.td_ipaid').html("$" + _paidAmt);
                    modalBody.find('.td_istatus').html("Paid");

                    var tableBody = modalBody.find("#rentalInvoiceBody");
                    tableBody.find('.rentalInvoice-tstamp').html(_paymentDate);
                    tableBody.find('.rentalInvoice-date').html(paymentDateSplit[0]);
                    tableBody.find('.rentalInvoice-Rent').html("$" + _paidAmt);
                    tableBody.find('.rentalInvoice-EnteredBy').html(_enteredBy);
                    tableBody.find('.rentalInvoice-pmethod').html(_pmethod);
                    tableBody.find('.rentalInvoice-pdesc').html("-");
                    tableBody.find('.rentalInvoice-comments').html("-");
                }
            });

        });

        $("body").on('click', '#downloadBuildingReport', function() {
            // Hit the Controller to fetch single building report data and fill in the HTML for pdf report
            var _buildingId = $(this).data('bid');

            var _urlParams = getAllUrlParams();
            var _dateFilter = null;
            //            if (_urlParams.datereq != null) {
            //                _dateFilter = _urlParams.datereq;
            //            }

            _dateFilter = $(this).data(
                'sdate'); // Send the properly formatted Date to Controller



            $.ajax({
                url: 'custom/report/report_payment_controller.php',
                type: 'POST',
                data: {
                    request: 'buildingReportData',
                    b_id: _buildingId,
                    date: _dateFilter
                },
                dataType: 'json',
                success: function(response) {
                    // Data found for the requested buildng ID
                    if (response.data) {
                        var _tbody = $('#eft_building_report').find(
                            '#eft_building_report_tbody');

                        var totalPaymentsAmt = 0;
                        var totalPaymentsCount = response.value.length;
                        $('#buildingReportTotalNumPayments').html(
                            totalPaymentsCount);
                        var buildingName = response.value[0].bName;
                        var buildingAddr = response.value[0].bAddress;
                        $('#buildingReportbName').html(buildingName);
                        $('#buildingReportbAddr').html(buildingAddr);

                        for (payment in response.value) {
                            var tenantIds = null;
                            var paymentData = response.value[payment];
                            paymentData.date_paid = paymentData.date_paid == null ?
                                '-' : paymentData.date_paid;

                            if (paymentData.tenantIds.length < 2) {
                                tenantIds = paymentData.tenantIds[0];
                            } else {
                                tenantIds = paymentData.tenantIds.join();
                            }

                            var _tr = "<tr>\n\
                                    <td>" + paymentData.unit + "</td>\n\
                                    <td>" + paymentData.tenant + "</td>\n\
                                    <td>" + tenantIds + "</td>\n\
                                    <td>" + paymentData.due + "</td>\n\
                                    <td>" + paymentData.date_paid + "</td>\n\
                                    <td>" + paymentData.date_paid + "</td>\n\
                                    <td>Paid </td>\n\
                                    <td>" + paymentData.paidAmt + "</td>\n\
                                    </tr>";
                            _tbody.append(_tr);
                            totalPaymentsAmt += paymentData.paidAmt;
                        }
                        $('#buildingReportTotal,.totalBuildingPaymentAmtReport')
                            .html(totalPaymentsAmt);
                        $("#eft_building_report").show();
                        printJS('eft_building_report', 'html');
                        $("#eft_building_report").hide();
                    }
                }
            });




        });

        function getAllUrlParams(url) {

            // get query string from url (optional) or window
            var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

            // we'll store the parameters here
            var obj = {};

            // if query string exists
            if (queryString) {

                // stuff after # is not part of query string, so get rid of it
                queryString = queryString.split('#')[0];

                // split our query string into its component parts
                var arr = queryString.split('&');

                for (var i = 0; i < arr.length; i++) {
                    // separate the keys and the values
                    var a = arr[i].split('=');

                    // in case params look like: list[]=thing1&list[]=thing2
                    var paramNum = undefined;
                    var paramName = a[0].replace(/\[\d*\]/, function(v) {
                        paramNum = v.slice(1, -1);
                        return '';
                    });

                    // set parameter value (use 'true' if empty)
                    var paramValue = typeof(a[1]) === 'undefined' ? true : a[1];

                    // (optional) keep case consistent
                    paramName = paramName.toLowerCase();
                    paramValue = paramValue.toLowerCase();

                    // if parameter name already exists
                    if (obj[paramName]) {
                        // convert value to array (if still string)
                        if (typeof obj[paramName] === 'string') {
                            obj[paramName] = [obj[paramName]];
                        }
                        // if no array index number specified...
                        if (typeof paramNum === 'undefined') {
                            // put the value on the end of the array
                            obj[paramName].push(paramValue);
                        }
                        // if array index number specified...
                        else {
                            // put the value at that index number
                            obj[paramName][paramNum] = paramValue;
                        }
                    }
                    // if param name doesn't exist yet, set it
                    else {
                        obj[paramName] = paramValue;
                    }
                }
            }

            return obj;
        }

    });
});
</script>

<!--REPORT HTML FOR ALL BUILDINGS PDF GENERATION-->
<div id="eft_allbuildings_report" class="container" style="display:none;">
    <div class="row form-group">
        <div class="col-sm-6">
            <img src="files/logos/spg.png" />
        </div>
    </div>

    <div class="table-responsive">

        <table class="table table-condensed table-striped">
            <tbody>
                <tr>
                    <td style="border:none;">Transactions Date Range :</td>
                    <td style="border:none;text-align:left;" class="transRangeReport">Data</td>
                </tr>
                <tr>
                    <td style="border:none;">All Buildings Transaction Total:</td>
                    <td style="border:none;text-align:left;" class="totalPaymentAmtReport"></td>
                </tr>

            </tbody>

        </table>
    </div>


    <div class="table-responsive">

        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th>Building</th>
                    <th>Bank Account Nickname</th>
                    <th>Number of Payments</th>
                    <th>Transaction Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Variables for aggregate of all the buildings
                $totalPaymentsAllBuildings = 0;
                $totalPaymentCountAllBuildings = 0;

                foreach ($allBuildings as $index => $building) {

                    $paymentData = $DB_ls_payment->getPaymentsData($building['building_id'], $selectedDay, $previousDay);
                    $paymentSum = 0;
                    if (count($paymentData) > 1) {
                        foreach ($paymentData as $paymentIndex => $payment) {
                            $paymentSum += $payment['paid_amount'];
                        }
                    }
                    $paymentCount = count($paymentData);

                    // Payment Count for all the buildings that are shown in the report
                    $totalPaymentsAllBuildings += $paymentSum;
                    $totalPaymentCountAllBuildings += $paymentCount;

                    // If the payment count for a building is less than 1, do not show it in the PDF report
                    if ($paymentCount < 1) {
                        continue;
                    }
                ?>

                <tr>
                    <td> <?php echo $building['building_name']; ?> </td>
                    <td>Nick Name</td>
                    <td> <?php echo $paymentCount ?></td>
                    <td> <?php echo '$' . $paymentSum ?></td>
                </tr>

                <?php
                } // end of foreach
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Number of Payments : <?php echo $totalPaymentCountAllBuildings; ?></td>
                    <td id="totalPaymentAmtReport"><?php echo '$' . $totalPaymentsAllBuildings; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!--REPORT HTML FOR SPECIFIC BUILDING PDF GENERATION-->
<div id="eft_building_report" class="container" style="display:none;">
    <div class="row form-group">
        <div class="col-sm-4">
            <p>
                <img class="img-responsive img-thumbnail" id="eft_building_report_bldgimg" />
            </p>
        </div>
        <div class="col-sm-4" style="text-align:right;">
            <span id="buildingReportbName"></span> <br>
            <span id="buildingReportbAddr"></span>
        </div>
        <div class="col-sm-4" style="text-align:right;">
            <h6>BUIDLING REPORT </h6><br>
            <img src="files/logos/spg.png" />
        </div>
    </div>

    <div class="table-responsive">

        <table class="table table-condensed table-striped">
            <tbody>
                <tr>
                    <td style="border:none;">Transactions Date Range :</td>
                    <td style="border:none;text-align:left;" class="transRangeReport">Data</td>
                </tr>
                <tr>
                    <td style="border:none;">All Buildings Transaction Total:</td>
                    <td style="border:none;text-align:left;" class="totalBuildingPaymentAmtReport"></td>
                </tr>

            </tbody>

        </table>
    </div>


    <div class="table-responsive">
        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Tenants</th>
                    <th>Tenant ID Number</th>
                    <th>Due Date</th>
                    <th>Date Paid</th>
                    <th>Time Paid</th>
                    <th>Payment Status</th>
                    <th>Total Paid</th>
                </tr>
            </thead>
            <tbody id="eft_building_report_tbody">

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">Number of Payments : <span id="buildingReportTotalNumPayments"></span></td>
                    <td>TOTAL: <span id="buildingReportTotal"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>