<!-- *** Scripts that are not present in the header - Do not Remove *** -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

/* * *
 * Check if the building ID is set as a GET param
 */

if (isset($_GET["bid"]) && !empty($_GET["bid"])) {
    // Building ID is set 
    ?>

    <div class="container">
        <form class="buildingTimeForm">
            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="">List Units - Number of Days before Vacant :</label>
                        <input type="hidden" name="bid" id="bid" value="<?php echo $_GET["bid"]; ?>">
                        <input type="text" class="form-control input-sm" id="listDaysBefore" name="listDaysBefore">
                    </div>            
                </div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Suggest Lease Start On</label>
                        <input type="text" class="form-control input-sm" id="leaseStartOn" name="leaseStartOn">
                    </div>            
                </div>
            </div>
            <div class="row form-group"> 
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Suggest Weekly Lease Start On:</label>
                        <input type="text" class="form-control input-sm" id="weeklyLeaseStartOn" name="weeklyLeaseStartOn">
                    </div>            
                </div>
            </div>

            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="">Upcoming Vacancy Warning  - Number of Days before :</label>
                        <input type="text" class="form-control input-sm" id="vacancyWarningBefore" name="vacancyWarningBefore">
                    </div>            
                </div>
            </div>


            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="">Place the resolved issue - Number of Days after the issue is marked Resolved:</label>
                        <input type="text" class="form-control input-sm" placeholder="Number of days" id="resolvedIssueMarking" name="resolvedIssueMarking">
                    </div>            
                </div>
            </div>

            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Notifying the Tenant </div>
                        <div class="panel-body">                    
                            <div class="form-group">
                                <label for="">Invoice is available - Number of days before it is Due:</label>
                                <input type="text" class="form-control input-sm" id="notifyTenantInvoice" name="notifyTenantInvoice">
                            </div>  
                            <div class="form-group">
                                <label for="">Rent is Late after - Number of days after Due Date:</label>
                                <input type="text" class="form-control input-sm" id="notifyTenantRentLate" name="notifyTenantRentLate">
                            </div>  
                        </div>
                    </div>
                </div>
            </div>


            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Impose Late Payment Charge</div>
                        <div class="panel-body">                    
                            <div class="form-group">
                                <label for="">Number of days after Due Date:</label>
                                <input type="text" class="form-control input-sm" id="LatePayChargeDays" name="LatePayChargeDays">
                            </div>  
                            <div class="form-group">
                                <label for="">$ Amount Charge for Each Late Invoice :</label>
                                <input type="text" class="form-control input-sm" id="latePaymentChargeAmt" name="latePaymentChargeAmt">
                            </div>  
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Send SMS to Tenant</div>
                        <div class="panel-body">                    
                            <div class="form-group">
                                <label for="">To Remind the Unpaid Rent Invoice is available - Number of days after Due Date:</label>
                                <input type="text" class="form-control input-sm" id="smsUnpaidInvoice" name="smsUnpaidInvoice">
                            </div>  
                            <div class="form-group">
                                <label for="">To Remind Unpaid Rent - Frequency:</label>
                                <input type="text" class="form-control input-sm" id="smsUnpaidRentFreq" name="smsUnpaidRentFreq">
                            </div>  
                        </div>
                    </div>
                </div>
            </div>


            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Send Landlord Invoice for Payment Reminder </div>
                        <div class="panel-body">                    
                            <div class="form-group">                            
                                <label for="">When Credit Of </label>
                                <input type="text" placeholder="Credit Value" class="form-control input-sm" id="landlordPayRemindCreditValue" name="landlordPayRemindCreditValue">
                            </div>  
                            <div class="form-group">
                                <label for="">service is Lower than Credit : </label>
                                <input placeholder="Credit Value" type="text" class="form-control input-sm" id="landlordPayRemindCreditValueLowerThan" name="landlordPayRemindCreditValueLowerThan">
                            </div>  
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-group"> 
                <div class="col-sm-5">
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="saveBtnTimes"> Save </button>
                    </div>            
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>

<script>
    $(function () {
        // On page load - fill the form 
        $.ajax({
            url: 'buildingtime_controller.php',
            type: 'GET',
            dataType: 'json',
            data: {
                bid: <?php echo $_GET["bid"]; ?>,
                action: "fillBuildingTimelines"
            },
            success: function (response) {
                if (response) {
                    if (response.result) {
                        var formValues = $.parseJSON(response.value.building_timelines);
                        for (var value in formValues) {
                            $("#" + value).val(formValues[value]);
                        }
                    }
                }
            }
        });

        // On Save button click - Update all the timeline values to the building infos table 
        $("#saveBtnTimes").on("click", function () {
            // Serialize the form and send to the ajax controller
            var serializedForm = $(".buildingTimeForm").serialize();

            $.ajax({
                url: 'buildingtime_controller.php',
                type: 'POST',
                dataType: 'json',
                data: serializedForm,
                success: function (response) {
                    if (response) {
                        if (response.value == "true") {
                            alert("Building Timeline values are updated.");
                        }
                    }
                }
            });

        });
    });// End of document ready
</script>
