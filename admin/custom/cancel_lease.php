<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])) {
    //   print_r($_POST);
    $comments = $_POST['comments'];
    $lease_id = $_POST['lease_id'];

    $SelectSql = "update lease_infos set lease_status_id=4, comments=:comments where id=" . $lease_id;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);

    if (!empty($_POST['cancel_all']) && $_POST['cancel_all'][0] == 1) {
        $SelectSql = "update lease_payments set payment_status_id=4, invoice_type_id=3, comments=:comments where lease_id=" . $lease_id;
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute([':comments' => $comments]);
    }
    echo "Canceled.";
} else {
    $lease_id = $_GET['id'];
?>

<form method="post" class="form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_id" id="lease_id" value="<?= $lease_id ?>">
    <input type="hidden" name="submitted" id="submitted" value="1">
    <div class="container">

        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">Noted: <i>By cancelling a lease all unpaid rent payments will be
                    marked as cancelled. We advise users only to cancel leases which have been created by mistake. If
                    you wish to end a lease early please use the Terminate button located in lease information page.</i>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12"><label><input type="checkbox" name="cancel_all" value="1">
                    <strong>CANCEL ALL MANUAL PAYMENTS ASSOCIATED WITH THIS LEASE</strong></label></div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Please type CANCEL to cancel this payment:</div>
            <div class="col-sm-2 col-md-2 col-lg-2"><input type="text" name="cancel" id="cancel" size="6" placeholder=""
                    value="" class="form-control" align="left"></div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Reason for cancellation:</div>
            <div class="col-sm-2 col-md-2 col-lg-2"><textarea name="comments" id="comments"
                    class="form-control"></textarea>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <p style="color: red"><strong> Warning: Once a lease is cancelled it cannot be undone!</strong></p>
            </div>
        </div>













        <div class="row">
            <div class="col-sm-8 col-md-8 col-lg-8">
                <hr>
            </div>
        </div>



        <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-4"><button id="form-submit" class="btn btn-primary">Cancel the
                    lease</button> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button></div>
        </div>

    </div>
</form>
<script>
$(function() {
    //     event.preventDefault();
    $("#form-submit").click(function() {

        if ($("#comments").val() == "") {
            alert("Please enter the reason to cancellation.");
            return false;
        }

        if ($("#cancel").val() == "CANCEL") {
            var cancel_alls = [];
            $.each($("input[name='cancel_all']:checked"), function() {
                cancel_alls.push($(this).val());
            });

            $.ajax({
                type: "POST",
                data: {
                    comments: $('#comments').val(),
                    lease_id: $('#lease_id').val(),
                    submitted: $('#submitted').val(),
                    cancel_all: cancel_alls
                },
                url: "../custom/cancel_lease.php",
                success: function(data) {
                    console.log(data);
                },
                error: function(data) {
                    console.log(data); //error message
                }
            });

            $('#MyModal').modal('hide');
        } else {
            alert("Please type CANCEL to cancellation");
            return false;
        }


    });



});
</script>
<? } ?>