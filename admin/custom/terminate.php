<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])) {
    //   print_r($_POST);
    //   die();
    $TerminateDate = $_POST['TerminateDate'];
    $TerminateDate = DateTime::createFromFormat('Y-m-d', $TerminateDate)->format('Y-m-d');
    $TerminateDatePlus1D = date('Y-m-d', strtotime($TerminateDate . ' + 1 days'));
    $comments = $_POST['comments'];
    $lease_id = $_POST['lease_id'];
    $apartment_id = $_POST['apartment_id'];
    $SelectSql = "update lease_payments set payment_status_id=7,  comments=:comments where due_date>='" . $TerminateDate . "' and lease_id=" . $lease_id; //invoice_type_id=3,
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);
    //    echo $SelectSql;
    $SelectSql = "update lease_infos set lease_status_id=6, create_date='" . date("Y-m-d H:i:s") . "', comments=:comments where id=" . $lease_id;
    //   die($SelectSql);
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);

    $SelectSql = "update lease_infos set terminate_date='$TerminateDate' where id=" . $lease_id;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();

    $SelectSql = "update apartment_infos set available_date='$TerminateDatePlus1D' where apartment_id=" . $apartment_id;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    /*
    // Check if the Cancel Payments after Move Out date is selected : and If Yes- Delete the payments from the lease_payments table for the remaining months in the lease
    if (isset($_POST["cancelPaymentsMoveOut"])) {
        $deletePayments = "DELETE FROM lease_payments where lease_id = $lease_id and due_date > '" . $TerminateDate . "'";
        $deletePaymentsStmt = $DB_con->prepare($deletePayments);
        $deletePaymentsStmt->execute();
    }
*/
    echo "Terminated.";
} else {
    $lease_id = $_GET['id'];
    $SelectSql = "SELECT * from rental_payments ";
    $SelectSql .= "where lease_id=$lease_id";
    // echo $SelectSql;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row = $result[0];
    foreach ($row as $key => $value) {
        $$key = $value;
    }
    $due_date = date_format(date_create($due_date), "M d, Y");

    $SelectSql = "SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_ids)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" class="form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_id" id="lease_id" value="<?= $lease_id ?>">
    <input type="hidden" name="apartment_id" id="apartment_id" value="<?= $apartment_id ?>">
    <input type="hidden" name="submitted" id="submitted" value="1">
    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="row form-group">
                    <div class="col-6"> Lease Day Remaining: </div>
                    <div class="col-6">
                        <?= date_diff(date_create($end_date), date_create(date("Y-m-d")))->format("%a"); ?> </div>
                </div>
                <div class="row form-group">
                    <div class="col-6"> Lease End: </div>
                    <div class="col-6"> <b><?= date_format(date_create($end_date), "Y-m-d") ?></b> </div>
                </div>
                <div class="row form-group">
                    <div class="col-6"> Move Out: </div>
                    <div class="col-6"> <b><?= date_format(date_create($move_out_date), "Y-m-d") ?></b> </div>
                </div>
                <div class="row form-group">
                    <div class="col-6"> Terminate Date: </div>
                    <div class="col-6"> <input type="text" id="TerminateDate" name="TerminateDate"
                            value="<?= date('Y-m-d') ?>" size="10" maxlength="10" placeholder="Terminate Date"
                            class="form-control" /> </div>
                </div>
                <div class="row form-group">
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="cancelPaymentsMoveOut"
                                id="cancelPaymentsMoveOut" checked>
                            <label class="form-check-label" for="cancelPaymentsMoveOut"> CANCEL PAYMENTS AFTER MOVE OUT
                                DATE </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-4">Rent Total</div>
            <div class="col-8"><strong>$<?= $total ?> (CAD)</strong></div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-4">Tenant(s):</div>
            <div class="col-8">
                <? foreach ($tenants as $key) { ?>
                <strong><a href="tenant_infosview.php?showdetail=&id=<?= $key['tenant_id'] ?>"
                        target="_blank"><?= $key['full_name'] ?></a></strong><br>
                <? } ?>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-4">Building:</div>
            <div class="col-8"><strong><?= $building_name ?></strong></div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-4">Apartment:</div>
            <div class="col-8"><strong><?= $unit_number ?></strong></div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-12">Note: You can manually edit remaining scheduled payments in the
                'Details' button.
                <br> Custom invoices and discounted payments will not be canceled when terminating a lease. They
                will
                need to be manually canceled.
                <br> The apartment will be considered vacant after the move out date.
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-4">Please type TERMINATE to terminate this lease:</div>
            <div class="col-8"><input type="text" name="terminate" id="terminate" size="10" placeholder="" value=""
                    class="form-control" align="left"></div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-4">Reason for Termination:</div>
            <div class="col-8"><textarea name="comments" id="comments" class="form-control"></textarea> </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-12">Transaction Logs:</div>
        </div>

        <?php
            $SelectSql = "select entry_datetime,amount,payment_date, LPD.comments, EMP.full_name as employee_name, PM.name as payment_method, PT.name as payment_type from lease_payment_details LPD
            LEFT JOIN employee_infos EMP ON EMP.employee_id=LPD.entry_user_id
            LEFT JOIN payment_methods PM ON PM.id=LPD.payment_method_id
            LEFT JOIN payment_types PT ON PT.id=LPD.payment_type_id
            where lease_payment_id=" . $lease_payment_id;
            $statement = $DB_con->prepare($SelectSql);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            //$rowNumbers=$result->rowCount();
            if ($result) {
            ?>
        <table class="table-condensed table-responsive stamp-table">
            <tr style="font-size: 8pt">
                <td>Time Stamp</td>
                <td>Amount</td>
                <td>Payment Date</td>
                <td>Entered By</td>
                <td>Payment Method</td>
                <td>Payment Type</td>
                <td>Comments</td>
            </tr>
            <?php
                    foreach ($result as $row) {

                        foreach ($row as $key => $value) {
                            $$key = $value;
                        }
                    ?>

            <tr style="font-size: 8pt">
                <td><?= $entry_datetime ?></td>
                <td align="right"><?= $amount ?></td>
                <td><?= $payment_date ?></td>
                <td><?= $employee_name ?></td>
                <td><?= $payment_method ?></td>
                <td><?= $payment_type ?></td>
                <td><?= $comments ?></td>
            </tr>

            <?php
                    } // for each rows
                } else { // if rowsNumber
                    echo "No transaction yet";
                }
                ?>
        </table>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-12"><button id="form-submit" class="btn btn-primary">Terminate the
                    Lease</button> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button></div>
        </div>
    </div>
</form>
<script>
let format = "y-MM-dd",
    options = {
        localization: {
            format,
        },
    }
ew.createDateTimePicker("modalform", "TerminateDate", ew.deepAssign({
    "useCurrent": false,
    "display": {
        "sideBySide": false
    }
}, options));
// ew.createDateTimePicker("modalform", "TerminateDate");

$(function() {
    $("#form-submit").click(function() {

        if ($("#comments").val() == "") {
            alert("Please enter the reason to terminate.");
            return false;
        }

        if ($("#terminate").val() == "TERMINATE") {

            $.ajax({
                type: "POST",
                data: {
                    submitted: $('#submitted').val(),
                    lease_id: $('#lease_id').val(),
                    apartment_id: $('#apartment_id').val(),
                    TerminateDate: $('#TerminateDate').val(),
                    comments: $('#comments').val(),
                },
                url: "../custom/terminate.php",
                success: function(response) {
                    alert(response);
                },
                error: function(data) {
                    console.log(data); //error message
                }
            });
            $('#MyModal').modal('hide');


        } else {
            alert("Please type TERMINATE to terminate.");
            return false;
        }

    });
});
</script>
<?php } ?>