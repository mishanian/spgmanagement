<?php
$paymentMethods = $DB_bill->getPaymentMethods();
$accountTypes = $DB_bill->getAccountTypes();
$result = $DB_bill->getLastCheckNumber();
$lastCheckNumber = $result['cheque_no'];

?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <label class="font-weight-bold" for="paymentDate">Date</label>
                    <div class="form-group">
                        <div class='input-group date' id='paymentDate'>
                            <input type='text' name="paymentDate" class="form-control"
                                value="<?php echo date('d-m-Y') ?>" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold" for="paymentMethod">Method</label>
                        <select class="form-control" id="paymentMethod" name="paymentMethod">
                            <?php
                            foreach ($paymentMethods as $key => $value) { ?>
                            <option name="paymentMethod" value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?>
                            </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <br />
                        <br />
                        <label class="font-weight-bold" for="printChequeNo">To be Printed:</label>
                        <input disabled id="printChequeNo" name="printChequeNo" type="checkbox"
                            value="<?php echo $lastCheckNumber ?>" />

                        <!--     <label class="font-weight-bold" for="assignChequeNo">Assign cheque no:</label>
                             <input disabled id="assignChequeNo" name="assignChequeNo" type="checkbox"/>-->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold" for="checkNumber">Check Number:</label>
                        <input disabled class="form-control pull-right float-right" type="text" name="checkNumber"
                            id="checkNumber" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label class="font-weight-bold" for="accountType">Account type</label>
                    <select class="form-control" id="accountType" name="accountType">
                        <?php
                        foreach ($accountTypes as $key => $value) { ?>
                        <option name="vendor_id" value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?>
                        </option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold" for="accountSubType">Account Sub Type</label>
                        <select class="form-control" id="accountSubType" name="accountSubType">
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold" for="totalAmountToPay">Amount to Pay:</label>
                        <input disabled class="form-control pull-right float-right" type="text" name="totalAmountToPay"
                            id="totalAmountToPay" />
                        <input type="hidden" name="grandTotalToPay" id="grandTotalToPay" value="" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="font-weight-bold" for="memo">Memo:</label>
                    <input class="form-control pull-right float-right" type="text" name="memo" id="memo" />
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" id="submitPayment" name="submitPayment" class="btn btn-primary pull-right">Pay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {

        $('#paymentDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $("#accountType").click(function(event) {
            $.ajax({
                type: "GET",
                url: "custom/billing/bill_data.php",
                dataType: 'json',
                data: {
                    account_type_id: event.target.value
                },
                async: true,
                success: function(json) {
                    $('#accountSubType').empty();
                    $.each(json, function(key, value) {
                        $('#accountSubType').append('<option value="' + value.id +
                            '">' + value.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });

        $("#paymentMethod").change(function(e) {
            if (e.target.value === "4") {
                $("#printChequeNo").prop("disabled", false);
                $("#assignChequeNo").prop("disabled", false);
                $("#checkNumber").prop("disabled", false);
            } else {
                $("#printChequeNo").prop("disabled", true);
                $("#assignChequeNo").prop("disabled", true);
                $("#checkNumber").prop("disabled", true);
            }
        });

        $('#printChequeNo').on('change', function(e) {
            let checkNumber = parseInt(e.target.value);
            let checkNumberSelector = $("#checkNumber");

            if ($("#printChequeNo").is(':checked')) {
                if (e.target.value) {
                    checkNumber = checkNumber + 1;
                    checkNumberSelector.val('000' + checkNumber.toString());
                } else {
                    checkNumberSelector.val('0001');
                }

            } else {
                checkNumberSelector.val("");
            }
        });
    });
    </script>