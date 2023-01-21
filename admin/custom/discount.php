<? session_start();
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])){
//    print_r($_POST);
    $discount=$_POST['discount'];
    $comments=$_POST['comments'];
    $lease_payment_id=$_POST['lease_payment_id'];
    $SelectSql ="insert into lease_payment_details (lease_payment_id,payment_type_id,payment_method_id,amount,comments,payment_date,entry_datetime,entry_user_id)
    value ($lease_payment_id,2,10,$discount,:comments,'".date("Y-m-d")."','".date("Y-m-d H:i:s")."',".$_SESSION['UserID'].")";
//    echo $SelectSql;
    $statement=$DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);
//    echo "\nPDOStatement::errorInfo():\n";
//    $arr = $statement->errorInfo();
//   print_r($arr);
    echo "Saved";
    die();
}else{
    $lease_payment_id = $_GET['id'];
    $SelectSql = "SELECT due_date,total,outstanding,LP.lease_amount, APP.building_id, building_name, APP.unit_number,tenant_ids 
FROM lease_payments LP 
LEFT JOIN lease_infos LI ON LI.id=LP.lease_id
LEFT JOIN lease_payment_details LPD ON LPD.lease_payment_id=LP.id
LEFT JOIN apartment_infos APP ON APP.apartment_id=LI.apartment_id
LEFT JOIN building_infos BLD ON BLD.building_id=APP.building_id 
";
    $SelectSql .= "where LP.id=$lease_payment_id";
//    echo "$SelectSql<br>";
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
        <input type="hidden" name="lease_payment_id" id="lease_payment_id" value="<?= $lease_payment_id ?>">
        <input type="hidden" name="submitted" id="submitted" value="1">
        <input type="hidden" name="UserID" id="UserID" value="<?=$_SESSION['UserID']?>">
        <div class="container">


            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Payment Due Date:</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><strong><?= $due_date ?></strong></div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Rent Total</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><strong>$<?= $total ?> (CAD)</strong></div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Tenant(s):</div>
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <? foreach ($tenants as $key) { ?>
                        <strong><a href="tenant_infosview.php?showdetail=&id=<?= $key['id'] ?>"
                                   target="_blank"><?= $key['full_name'] ?></a></strong><br>
                    <? } ?>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Building:</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><strong><?= $building_name ?></strong></div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Appartment:</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><strong><?= $unit_number ?></strong></div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Discount Amount:</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><input type="text" name="discount" id="discount" size="3"
                                                               placeholder="0" value="0" class="form-control"
                                                               align="left">$ (CAD)
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2">Comments:</div>
                <div class="col-sm-2 col-md-2 col-lg-2"><textarea name="comments" id="comments"
                                                                  class="form-control"></textarea></div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <hr>
                </div>
            </div>

            <div class="row"><div class="col-sm-8 col-md-8 col-lg-8">Transaction Logs:<br><br></div></div>

                <?
                try {
                $SelectSql = "select entry_datetime,amount,payment_date, LPD.comments, EMP.full_name as employee_name, PM.name as payment_method, PT.name as payment_type from lease_payment_details LPD            
                LEFT JOIN employee_infos EMP ON EMP.employee_id=LPD.entry_user_id
                LEFT JOIN payment_methods PM ON PM.id=LPD.payment_method_id
                LEFT JOIN payment_types PT ON PT.id=LPD.payment_type_id
                where lease_payment_id=" . $lease_payment_id;
                $statement = $DB_con->prepare($SelectSql);
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    print "Error!: " . $e->getMessage() . "<br/>";
                    die();
                }

                //$rowNumbers=$result->rowCount();
                if ( $result ) {
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
                <?

                foreach ($result as $row){

                    foreach ($row as $key => $value) { $$key = $value; }
                    ?>

                    <tr style="font-size: 8pt">
                        <td><?=$entry_datetime?></td>
                        <td align="right"><?=$amount?></td>
                        <td><?=$payment_date?></td>
                        <td><?=$employee_name?></td>
                        <td><?=$payment_method?></td>
                        <td><?=$payment_type?></td>
                        <td><?=$comments?></td>
                    </tr>

                    <?
                } // for each rows
                }else { // if rowsNumber
                echo "No transaction yet";

                }
                ?>
            </table>









            <div class="row"><div class="col-sm-8 col-md-8 col-lg-8"><hr></div></div>

    <div class="row"><div class="col-sm-4 col-md-4 col-lg-4"><button id="form-submit" class="btn btn-primary">Save changes</button>  <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button></div></div>

</div>
</form>
    <script>
        $("input[name='discount']").TouchSpin({
            min: 0,
            max: 5000,
            step: 0.01,
            decimals: 2
        });


        $(function () {
                $("#form-submit").click(function(){
                    if ($("#discount").val()<=0){alert("Please enter the discount");return false;}
                    if ($("#comments").val()==""){alert("Please enter the comments");return false;}

                    $.ajax({
                        type: "POST",
                      //  data: {discount: $('#discount').val(), comments: $('#comments').val(), lease_payment_id: $('#lease_payment_id').val(), submitted: $('#submitted').val()},
                        data: $('#modalform').serialize(),
                        url: "custom/discount.php",
                        async: true,
                        cache: true,
                        timeout: 20000,
                        success: function(response) { alert(response); $('#MyModal').modal('hide');  window.location.reload();},
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert("Status"+textStatus);
                        }
                    });
                    return false;
                });
        });

    </script>
<?}?>

