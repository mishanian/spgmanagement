<? session_start();
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])) {
    //    print_r($_POST);
    $return_date = implode('-', array_reverse(explode('/', $_POST['return_date'])));
    $comments = " - Refund Comments: " . $_POST['comments'];
    $deposit_id = $_POST['deposit_id'];
    $SelectSql = "update deposits set deposit_status=1, comments=CONCAT(IFNULL(`comments`,''),'$comments') , return_employee_id=" . $_SESSION['employee_id'] . ", return_date='$return_date" . date(" H:i:s") . "' where id=$deposit_id";
    //    echo $SelectSql;
    $statement = $DB_con->prepare($SelectSql);
    $result = $statement->execute();
    echo "Refunded.";
    die();
} else {
    $deposit_id = $_GET['id'];
    $SelectSql = "SELECT building_name,unit_number, DP.tenant_id, DP.amount FROM deposits DP LEFT JOIN lease_infos LI ON LI.id=DP.lease_id LEFT JOIN  building_infos BI ON BI.building_id=LI.building_id LEFT JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id WHERE DP.id=$deposit_id";
    //echo $SelectSql;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row = $result[0];
    foreach ($row as $key => $value) {
        $$key = $value;
    }


    $SelectSql = "SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_id)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

    <form method="post" class="form-horizontal" id="modalform" name="modalform">
        <input type="hidden" name="deposit_id" id="deposit_id" value="<?= $deposit_id ?>">
        <input type="hidden" name="submitted" id="submitted" value="1">
        <input type="hidden" name="UserID" id="UserID" value="<?= $_SESSION['UserID'] ?>">
        <div class="container">



            <div class="row gy-4" style="padding: 10px;">
                <div class="col">Deposit Amount:</div>
                <div class="col"><strong><?= $amount ?></strong></div>
            </div>

            <div class="row gy-4" style="padding: 10px;">
                <div class="col">Apartment:</div>
                <div class="col"><strong><?= $building_name . " - " . $unit_number ?></strong></div>
            </div>
            <div class="row gy-4" style="padding: 10px;">
                <div class="col">Refund Date:</div>
                <div class="col"><strong><input type="text" name="return_date" id="return_date" value="<?= date("Y-m-d") ?>" size="10"></strong></div>
            </div>
            <div class="row gy-4" style="padding: 10px;">
                <div class="col">Comments:</div>
                <div class="col"><textarea name="comments" id="comments" class="form-control"></textarea></div>
            </div>

            <div class="row gy-4" style="padding: 10px;">
                <div class="col"><button id="form-submit" class="btn btn-primary">Refund</button>
                </div>
            </div>

        </div>
    </form>
    <script>
        $(function() {
            $("#form-submit").click(function() {
                if ($("#return_date").val() <= 0) {
                    alert("Please enter the discount");
                    return false;
                }
                if ($("#comments").val() == "") {
                    alert("Please enter the comments");
                    return false;
                }

                $.ajax({
                    type: "POST",
                    //  data: {discount: $('#discount').val(), comments: $('#comments').val(), lease_payment_id: $('#lease_payment_id').val(), submitted: $('#submitted').val()},
                    data: $('#modalform').serialize(),
                    url: "custom/deposit_refund.php",
                    async: true,
                    cache: true,
                    timeout: 20000,
                    success: function(response) {
                        alert(response);
                        $('#MyModal').modal('hide');
                        window.location.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Status" + textStatus);
                    }
                });
                return false;
            });
        });
    </script>
<? } ?>