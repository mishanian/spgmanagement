<?php
session_start();

if (isset($_SESSION["company_id"])) {
    $company_id = $_SESSION["company_id"];
}
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
include_once $path . 'Class.Building.php';
$DB_building = new Building($DB_con);
include_once $path . 'Class.LeasePayment.php';
$DB_ls_payment = new LeasePayment($DB_con);

// Check if the renew form is submitted
if (!empty($_POST['submitted'])) {
    // const_dump($_POST);
    $lease_id  = $_POST['lease_id'];
    $SelectSql = "SELECT * from lease_infos where id=$lease_id";
    $DB_con->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row    = $result[0];
    foreach ($row as $key => $value) {
        $$key = $value;
    }

    // Apartment ID to check whether there's a lease which is upcoming or active-renewal or active-renewed for this apt
    $apartment_id               = $row["apartment_id"];
    $current_lease_status_id    = $row["lease_status_id"];
    $comments                   = $_POST['comments'];
    $lease_amount               = $_POST['lease_amount'];
    if (!empty($_POST['signed_on_portal']) && $_POST['signed_on_portal']) {
        $signed_on_portal = $_POST['signed_on_portal'];
    } else {
        $signed_on_portal = 0;
    }
    $storage_amount             = 0; //$_POST['storage_amount'];
    $parking_lease_renew_choice = 0; //$_POST["renew_parking_lease_choice"];
    // $parking_amount             = $_POST['parking_amount'];
    $start_date_new             = DateTime::createFromFormat('Y-m-d', $_POST['start_date_new'])->format('Y-m-d'); // from Y-m-d to Y-m-d
    $end_date_new               = $_POST['end_date_new'];
    $move_out_date_new          = DateTime::createFromFormat('Y-m-d', $_POST['move_out_date_new_datepicker'])->format('Y-m-d'); // from Y-m-d to Y-m-d
    $move_in_date               = $_POST['move_in_date'];
    $length_of_lease            = $_POST['length_of_lease'];
    // $total_lease_amount         = $lease_amount + $parking_amount + $storage_amount;
    $total_lease_amount         = $lease_amount; // + $parking_amount + $storage_amount;

    if (empty($move_in_date)) {
        $move_in_date = $start_date_new;
    }
    if (empty($move_out_date)) {
        $move_out_date = $end_date_new;
    }
    /*     * ****************** */
    $debit_credit   = 1;
    $move_out_debit = 0;

    if (empty($employee_id)) {
        $employee_id = "NULL";
    }
    if (empty($owner_id)) {
        $owner_id = "NULL";
    }
    if (empty($company_id)) {
        $company_id = "NULL";
    }
    /*     * *************** */
    if ($start_date_new > date("Y-m-d")) {
        $lease_status = 2;
    } else {
        $lease_status = 1;
    }

    //    $leaseUpcomingExist = "select count(id) as count from lease_infos where apartment_id = $apartment_id and ($start_date_new BETWEEN start_date AND end_date)  and id != $lease_id";
    // $leaseUpcomingExist       = "SELECT COUNT(id) FROM lease_infos WHERE apartment_id = $apartment_id AND ('$start_date_new' BETWEEN start_date AND end_date) and lease_status_id NOT IN (3,4,5,6)";
    $leaseUpcomingExist       = "SELECT COUNT(id) FROM lease_infos LI LEFT JOIN apartment_infos AI ON AI.apartment_id=LI.apartment_id
    WHERE AI.apartment_type_id<>5 AND LI.apartment_id = $apartment_id AND ('$start_date_new' BETWEEN start_date AND end_date) and lease_status_id NOT IN (3,4,5,6)";
    // echo $leaseUpcomingExist;
    $isLeaseUpcomingExistStmt = $DB_con->prepare($leaseUpcomingExist);
    $isLeaseUpcomingExistStmt->execute();
    $isLeaseUpcomingExistResult = intval($isLeaseUpcomingExistStmt->fetchColumn());

    if ($isLeaseUpcomingExistResult > 0) {
        echo "Lease can't be renewed, there is an existing lease for the same lease term.";
        die();
    }

    $isLeaseRenewed     = "select lease_renewed_id,renewal from lease_infos where id = $lease_id";
    $isLeaseRenewedStmt = $DB_con->prepare($isLeaseRenewed);
    $isLeaseRenewedStmt->execute();
    $isLeaseRenewedResult = $isLeaseRenewedStmt->fetchAll(PDO::FETCH_ASSOC);
    $isLeaseRenewedRow    = $isLeaseRenewedResult[0];

    //    if ((isset($isLeaseRenewedRow["renewal"]) && isset($isLeaseRenewedRow["lease_renewed_id"])) && $isLeaseRenewedRow["renewal"] == 1) {
    //        die("This lease is already renewed.");
    //    }
    if ($isLeaseRenewedRow["lease_renewed_id"] != null && $isLeaseRenewedRow["lease_renewed_id"] != "0" || $isLeaseRenewedRow["lease_renewed_id"] != 0) {
        die("This lease is already renewed.");
    }

    $SelectSql = "insert into lease_infos (building_id, apartment_id, tenant_ids, start_date, end_date, move_in_date, move_out_date, payment_period, length_of_lease, create_date, lease_amount, debit_credit, move_out_debit, lease_status_id, employee_id, company_id, owner_id, comments, parking_amount,storage_amount,total_amount, previous_lease_id, signed_on_portal) values ($building_id, $apartment_id, '$tenant_ids', '$start_date_new', '$end_date_new', '$move_in_date', '$move_out_date_new', $payment_period, $length_of_lease, '" . date("Y-m-d") . "', $lease_amount, '$debit_credit', '$move_out_debit', $lease_status, $employee_id, $company_id, $owner_id, :comments, $parking_amount, $storage_amount,$total_lease_amount,$lease_id, $signed_on_portal)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);
    $lease_id_new = $DB_con->lastInsertId();

    $SelectSql = "update lease_infos set lease_status_id=10,renewal=1,lease_renewed_id = $lease_id_new, comments=CONCAT(comments,'next lease is renewed by signing on tenant portal') where id=$lease_id";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();

    for ($i = 0; $i < $length_of_lease; $i++) {
        $original_start_date = new DateTime($start_date_new);
        $du_date             = $original_start_date->add(new DateInterval('P' . $i . 'M'));
        $du_date             = $du_date->format('Y-m-d');
        //        die("$start_date_new $du_date  i=$i");
        if ($i == 0) {
            $invoice_type_id = 1;
        } else {
            $invoice_type_id = 2;
        }

        $SelectSql = "insert into lease_payments (lease_id,invoice_type_id, due_date ,lease_amount,parking_amount,storage_amount, total,outstanding, employee_id, company_id) values ($lease_id_new,$invoice_type_id,'$du_date',$lease_amount,$parking_amount, $storage_amount, $total_lease_amount,$total_lease_amount, $employee_id, $company_id)";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //       $DB_con->commit();
    }

    // Update the apartment infos with the latest move out date
    $updateApartmentInfos = "update apartment_infos set available_date = '$move_out_date_new',apartment_status = 6 where apartment_id = $apartment_id";
    $statement            = $DB_con->prepare($updateApartmentInfos);
    $statement->execute();

    /* Check if there is any deposit for the old renewed lease */
    $leaseDeposits = $DB_ls_payment->getLeaseDeposit($lease_id);
    // error_log($leaseDeposits);
    // die(print_r($leaseDeposits));
    if (is_array($leaseDeposits) && count($leaseDeposits) > 0) {
        /* Insert the same $leaseDeposits with new lease ID */
        foreach ($leaseDeposits as $leaseDeposit) {
            $DB_ls_payment->createNewLeaseDeposit($lease_id_new, $leaseDeposit); /* Create new deposit record for the new lease */
        }
    }

    echo "The Lease is renewed.\r\n";
    die();
} else { // if it is NOT submit
    $lease_id  = $_GET['id'];
    $SelectSql = "select * from lease_infos LI LEFT JOIN apartment_infos APP ON APP.apartment_id=LI.apartment_id";
    $SelectSql .= " where LI.id=$lease_id ";

    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row    = $result[0];

    /*
	if (in_array($row["lease_status_id"], array(2, 3, 4, 5, 11))) {
		die("Lease can't be renewed.");
	}
    */

    foreach ($row as $key => $value) {
        $$key = $value;
    }
    $start_date_show     = date_format(date_create($start_date), "M d, Y");
    $end_date_show       = date_format(date_create($end_date), "M d, Y");
    $move_in_date_show   = date_format(date_create($move_in_date), "M d, Y");
    $start_date_new      = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("1 day")), "Y-m-d");
    $start_date_new_show = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("1 day")), "M d, Y");
    $end_date_new        = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("12 month")), "Y-m-d");
    $end_date_new_show   = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("12 month")), "M d, Y");
    $move_out_date_new   = date_format(date_add(date_create($end_date), date_interval_create_from_date_string("12 month")), "Y-m-d");

    $move_out_date_new_datepicker = new DateTime($end_date_new);
    $move_out_date_new_datepicker->modify('last day of this month');
    $move_out_date_new_datepicker = $move_out_date_new_datepicker->format('Y-m-d');

    $parking_amount_calculated = 0; // Will be calculated in the foreach loop while displaying the parking spots

    $SelectSql = "SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_ids)";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" class="ewForm ewEditForm form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_id" id="lease_id" value="<?= $lease_id ?>">
    <input type="hidden" name="end_date_new" id="end_date_new" value="<?= $end_date_new ?>">
    <input type="hidden" name="move_in_date" id="move_in_date" value="<?= $move_in_date ?>">
    <input type="hidden" name="move_out_date_new" id="move_out_date_new" value="<?= $move_out_date_new ?>">

    <input type="hidden" name="submitted" id="submitted" value="1">
    <div class="container">

        <div class="row">
            <div class="col-5">
                <h4>Lease Renewal Length :</h4>
            </div>
            <div class="col-7">
                <select name="length_of_lease" id="length_of_lease" class="form-control" onchange="renewal_change()">
                    <?php for ($i = 1; $i <= 24; $i++) { ?>
                    <option value="<?= $i ?>" <? if ($i==12) { echo "selected" ; } ?>><?= $i ?></option>
                    <? } ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card card-default">
                    <div class="card-header bg-light">Current Lease</div>
                    <div class="card-body">
                        <div class="col-*-12">
                            <div class="row form-group">
                                <div class="col-4"><strong> Total Lease Amount </strong></div>
                                <div class="col-8">$<?= $total_amount ?> CAD</div>
                            </div>

                            <div class="row form-group">
                                <div class="col-4"><strong>Unit </strong></div>
                                <div class="col-8"><?php echo $unit_number . " - $" . $total_amount . " CAD"; ?>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-4"><strong>Start Date</strong></div>
                                <div class="col-8"><?= $start_date_show ?></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-4"><strong>End Date</strong></div>
                                <div class="col-8"><?= $end_date_show ?></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-4"><strong>Move in Date </strong></div>
                                <div class="col-8"><?= $move_in_date ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header bg-light">Lease Renewal Terms</div>
                    <div class="card-body">
                        <div class="col">
                            <div class="row form-group mt-2">
                                <div class="col-4" style="margin-bottom:10px"><strong>Lease Amount </strong></div>
                                <div class="col-8">$ <input class="form-control" type="text" name="lease_amount"
                                        id="lease_amount" size="6" value="<?= $monthly_price ?>"> CAD
                                </div>
                            </div>

                            <div class="row mt-2 form-group">
                                <div class="col-4"><strong>Unit </strong></div>
                                <div class="col-8"><a
                                        href="apartment_infosview.php?showdetail=&apartment_id=<?= $apartment_id ?>&showmaster=floor_infos&fk_id=<?= $building_id ?>"
                                        target="_blank"><?php echo $unit_number; // . " - $" . $monthly_price . " CAD";
                                                                                                                                                                                                        ?></a>
                                </div>
                            </div>
                            <div class="row mt-2 form-group">
                                <div class="col-4"><strong>Start Date </strong></div>
                                <div class="col-8"><input type="text" name="start_date_new" id="start_date_new"
                                        size="10" maxlength="10" value="<?= $start_date_new ?>"
                                        onchange="renewal_change()" placeholder="Lease Start Date" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-2 form-group">
                                <div class="col-4"><strong>End Date </strong></div>
                                <div class="col-8"><span id="span_end_date_new"><?= $end_date_new_show ?></span>
                                </div>
                            </div>
                            <div class="row mt-2 form-group">
                                <div class="col-4"><strong>Move out Date</strong></div>
                                <div class="col-8"><input type="text" name="move_out_date_new_datepicker"
                                        id="move_out_date_new_datepicker" size="10" maxlength="10"
                                        value="<?php echo $move_out_date_new_datepicker; ?>"
                                        placeholder="Lease Start Date" class="form-control"> <br>(Please do not modify
                                    the
                                    Move Out Date unless
                                    required.)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <script>
        modalform = new ew.Form("modalform", "view");
        let format = "y-MM-dd",
            options = {
                localization: {
                    format,
                },
            }
        ew.createDateTimePicker("modalform", "start_date_new", ew.deepAssign({
            "useCurrent": false,
            "display": {
                "sideBySide": false
            }
        }, options));
        ew.createDateTimePicker("modalform", "move_out_date_new_datepicker", ew.deepAssign({
            "useCurrent": false,
            "display": {
                "sideBySide": false
            }
        }, options));
        </script>

        <script type="text/javascript">

        </script>
        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col">Number of Payment Periods: <span id="number_of_payment_periods">12</span></div>
        </div>
        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-2">Comments:</div>
            <div class="col-10"><textarea name="comments" id="comments" class="form-control"></textarea></div>
        </div>
        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>
        </table>
        <div class="row">
            <div class="col">
                <button id="form-submit" class="btn btn-primary">Renew</button>
            </div>
        </div>


</form>
<script>
$(function() {
    $("#form-submit").click(function() {
        $.ajax({
            type: "POST",
            //     data: {lease_id: $('#lease_id').val(), comments: $('#comments').val(), lease_amount: $('#lease_amount').val(), length_of_lease: $('#length_of_lease').val(), start_date_new: $('#start_date_new').val(),end_date_new: $('#end_date_new').val(), move_out_date_new: $('#move_out_date_new').val(),  submitted: $('#submitted').val()},
            data: $('#modalform').serialize(),
            url: "../custom/renew_lease.php",
            success: function(response) {
                alert(response);
                $('#MyModal').modal('hide');
                window.location.href =
                    "leaseinfoslist?cmd=search&t=lease_infos&x_building_id=" +
                    <?= $building_id ?> + "&x_apartment_id=" + <?= $apartment_id ?>;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Status" + textStatus);
            }
        });
        return false;
    });
});

function renewal_change() {
    const DateTime = luxon.DateTime;
    //   const end_date=$("#end_date").val();
    const length_of_lease = $('#length_of_lease').val();
    $("#number_of_payment_periods").html(length_of_lease);
    const start_date_obj = DateTime.fromFormat($("#start_date_new").val(), 'y-MM-dd');
    console.log("start_date_obj", start_date_obj.toFormat('y-MM-dd'))
    const end_date_obj = start_date_obj.plus({
        months: length_of_lease,
        days: -1
    });

    const end_date = end_date_obj.toFormat('y-MM-dd');
    $("#span_end_date_new").html(end_date); // Lease end date
    $("#span_move_in_date").html(end_date); // Move in  date
    const move_out_date = end_date;
    $("#move_out_date_new_datepicker").val(move_out_date); // Move out date
    $("#end_date_new").val(end_date);
}
</script>
<?php } ?>