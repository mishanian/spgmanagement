<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

<?php include("../pdo/dbconfig.php");
include_once('../pdo/Class.Snapshot.php');
$DB_snapshot  = new Snapshot($DB_con);
?>
<!--Begin Content-->
<div class="container">

    <!--    <div class="row form-group">
            <div class="col-md-12">
                <button class="btn btn-success" id="generateReportAllBldgs">Generate Report</button>
            </div>
        </div>-->

    <div class="row form-group">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table id="eftTable" class="table table-fixed table-condensed" style="background:white;">
                    <thead>
                        <tr>
                            <td><strong><?php echo $DB_snapshot->echot("TRANSACTIONS FROM"); ?> </strong></td>
                            <td><strong><?php echo $DB_snapshot->echot("TRANSACTIONS TO"); ?></strong></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $index = 9;
                        while ($index >= 1) {
                            $previousDayModifier = 10 - $index;
                            $modifyPreviousday = "-$previousDayModifier day";

                            $selectedDayModifier = 10 - $index - 1;
                            $modifySelectedday = "-$selectedDayModifier day";

                            $date = new DateTime($modifySelectedday);
                            $selectedDay = $date->format('Y-m-d 11:00:00');

                            // Previous Date format
                            $datePrevious = new DateTime($modifyPreviousday);
                            $previousDay = $datePrevious->format('Y-m-d 11:00:00');

                            $formattedSelecteddate = new DateTime($selectedDay);
                            $formattedSelectedDay = $formattedSelecteddate->format('Y-m-d h:m:s');

                            $formattedPreviousdate = new DateTime($previousDay);
                            $formattedPreviousDay = $formattedPreviousdate->format('Y-m-d h:m:s');
                        ?>
                            <tr>
                                <td> <?php echo $formattedPreviousDay; ?></td>
                                <td> <?php echo $formattedSelectedDay; ?></td>
                                <td>
                                    <button data-sdate="<?php echo $selectedDay; ?>" type="button" class="btn btn-primary downloadallEftReport" data-pdate="<?php echo $previousDay; ?>">
                                        <span class="glyphicon glyphicon-download-alt"></span>
                                        <?php echo $DB_snapshot->echot("Download PDF"); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php
                            $index--;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--REPORT - PDF FOR ALL EFT-->
<div id="all_eft_building_report" class="container" style="display:none;">
    <div class="row form-group">
        <div class="col-sm-12" style="text-align:left;">
            <img src="files/logos/logo.png" />
        </div>
    </div>

    <div class="table-responsive">

        <table class="table table-condensed table-striped">
            <tbody>
                <tr>
                    <td style="border:none;">Transactions Date Range :</td>
                    <td style="border:none;text-align:left;" class="transRangeReport">-</td>
                </tr>
                <tr>
                    <td style="border:none;">All Buildings Transaction Total:</td>
                    <td style="border:none;text-align:left;" class="allEfttotalBuildingPaymentAmtReport"></td>
                </tr>

            </tbody>

        </table>
    </div>

    <div class="table-responsive">
        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th>Building</th>
                    <th>Unit</th>
                    <th>Transaction Date</th>
                    <th>Total Paid</th>
                </tr>
            </thead>
            <tbody id="alleft_building_report_tbody">

            </tbody>
        </table>
    </div>
</div>

<!-- Script -->
<script>
    loadjs.ready('head', function() {
        $('document').ready(function() {

            $('.downloadallEftReport').on('click', function() {

                var selectedDate = $(this).data("sdate");
                var previousDate = $(this).data("pdate");

                // AJAX call to retrieve Payments between a certain date range
                $.ajax({
                    url: 'custom/report/report_payment_controller.php',
                    type: 'POST',
                    data: {
                        request: 'allEftReports',
                        pDate: previousDate,
                        sDate: selectedDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        var _tbody = $('#all_eft_building_report').find(
                            '#alleft_building_report_tbody');

                        _tbody.empty();

                        var totalPaymentsAmt = 0;
                        if (response.data) {
                            for (payment in response.value) {
                                var paymentData = response.value[payment];

                                var _tr = "<tr>\n\
                                    <td>" + paymentData.buildingName + "</td>\n\
                                    <td>" + paymentData.unit + "</td>\n\
                                    <td>" + paymentData.paymentData.payment_date + "</td>\n\
                                    <td> $" + paymentData.paymentData.paid_amount + "</td>\n\
                                    </tr>";
                                _tbody.append(_tr);

                                totalPaymentsAmt += paymentData.paymentData.paid_amount;
                            }
                            $('.allEfttotalBuildingPaymentAmtReport').html("$" + Math
                                .floor(totalPaymentsAmt));

                            $("#all_eft_building_report").show();
                            printJS('all_eft_building_report', 'html');
                            $("#all_eft_building_report").hide();
                        } else {
                            alert("No Payments found for the date range.");
                        }

                    }
                });

            });

        });
    });
</script>