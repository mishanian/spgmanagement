<? session_start();
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])) {
    // print_r($_POST);
    $lease_id = $_POST['lease_id'];
    $apartment_id = $_POST['apartment_id'];
    $available_date = $_POST['start_date_new'];
    $comments = $_POST['comments'];

    $SelectSql = "update lease_infos set lease_status_id=9, comments=:comments where id=$lease_id";
    //    echo "<br>$SelectSql<br>";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);

    $SelectSql = "update apartment_infos set apartment_status=8, available_date='$available_date' where apartment_id=$apartment_id";
    //    echo "<br>$SelectSql<br>";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();


    echo "Lease change to Non-Renewal.\r\n ";
    die();
} else {
    $lease_id = $_GET['id'];
    $SelectSql = "select * from lease_infos LI
LEFT JOIN apartment_infos APP ON APP.apartment_id=LI.apartment_id";
    $SelectSql .= " where LI.id=$lease_id ";
    //echo $SelectSql;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row = $result[0];
    foreach ($row as $key => $value) {
        $$key = $value;
    }
    //$due_date_show=date_format(date_create($due_date),"M d, Y");
    $start_date_show = date_format(date_create($start_date), "M d, Y");
    $end_date_show = date_format(date_create($end_date), "M d, Y");
    $move_in_date_show = date_format(date_create($move_in_date), "M d, Y");
    $start_date_new = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("1 day")), "Y-m-d");
    $start_date_new_show = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("1 day")), "M d, Y");
    $end_date_new = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("12 month")), "Y-m-d");
    $end_date_new_show = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("12 month")), "M d, Y");
    $move_out_date_new = $end_date_new;
    $SelectSql = "SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_ids)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" class="form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_id" id="lease_id" value="<?= $lease_id ?>">
    <input type="hidden" name="apartment_id" id="apartment_id" value="<?= $apartment_id ?>">
    <input type="hidden" name="start_date_new" id="start_date_new" value="<?= $start_date_new ?>">


    <input type="hidden" name="submitted" id="submitted" value="1">
    <div class="container">




        <div class="row">
            <div class="col-sm-8 col-md-8 col-lg-8"><strong>Current Lease:</strong>
                <hr>
                Total Lease Amount: <strong>$<?= $total_amount ?></strong> CAD <br>
                Unit : <strong><?= $unit_number ?></strong><br>
                Start Date : <strong><?= $start_date_show ?></strong><br>
                End Date : <strong><?= $end_date_show ?></strong><br>
                Move in Date: <strong><?= $move_in_date ?></strong><br>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-8 col-md-8 col-lg-8">
                <hr>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">Comments:</div>
            <div class="col-sm-2 col-md-2 col-lg-2"><textarea name="comments" id="comments"
                    class="form-control"></textarea> </div>
        </div>
        <div class="row">
            <div class="col-sm-8 col-md-8 col-lg-8">
                <hr>
            </div>
        </div>





        </table>




        <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-4"><button id="form-submit" class="btn btn-primary">Non-Renew</button>
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>

    </div>
</form>
<script>
$(function() {
    //     event.preventDefault();
    $("#form-submit").click(function() {

        $.ajax({
            type: "POST",
            //     data: {lease_id: $('#lease_id').val(), comments: $('#comments').val(), lease_amount: $('#lease_amount').val(), length_of_lease: $('#length_of_lease').val(), start_date_new: $('#start_date_new').val(),end_date_new: $('#end_date_new').val(), move_out_date_new: $('#move_out_date_new').val(),  submitted: $('#submitted').val()},
            data: $('#modalform').serialize(),
            url: "../custom/nonrenewal_lease.php",
            success: function(response) {
                alert(response);
                $('#MyModal').modal('hide');
                window.location.href =
                    "leaseinfoslist?cmd=search&t=lease_infos&x_building_id=" +
                    <?php echo $building_id; ?> + "&x_apartment_id=" +
                    <?php echo $apartment_id; ?>;
            },
            error: function(data) {
                alert(data);
            }
        });
        return false;
    });
});
</script>
<? } ?>