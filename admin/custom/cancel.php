<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
if (!empty($_POST['submitted'])){
 //   print_r($_POST);
    $comments=$_POST['comments'];
    $lease_payment_id=$_POST['lease_payment_id'];
    $SelectSql ="update lease_payments set payment_status_id=4, invoice_type_id=3, comments=:comments where id=".$lease_payment_id;
    $statement=$DB_con->prepare($SelectSql);
    $statement->execute([':comments' => $comments]);
    echo "Canceled.";
}else{
$lease_payment_id=$_GET['id'];
$SelectSql = "SELECT * from rental_payments ";
$SelectSql .= "where lease_payment_id=$lease_payment_id";
$statement=$DB_con->prepare($SelectSql);
$statement->execute();
$result=$statement->fetchAll(PDO::FETCH_ASSOC);
$row=$result[0];
foreach ($row as $key=>$value){$$key=$value;}
$due_date=date_format(date_create($due_date),"M d, Y");

$SelectSql ="SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_ids)";
$statement=$DB_con->prepare($SelectSql);
$statement->execute();
$tenants=$statement->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" class="form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_payment_id" id="lease_payment_id"  value="<?=$lease_payment_id?>">
    <input type="hidden" name="submitted" id="submitted"  value="1">
<div class="container">

    <div class="row"><div class="col-12">Please cancel a payment if it was entered by mistake.</div></div>
    <div class="row"><div class="col-12"><hr></div></div>

    <div class="row">
        <div class="col-6">Payment Due Date:</div>
        <div class="col-6"><strong><?=$due_date?></strong></div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>

    <div class="row">
        <div class="col-6">Rent Total</div>
        <div class="col-6"><strong>$<?=$total?> (CAD)</strong></div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>


    <div class="row">
        <div class="col-6">Tenant(s):</div>
        <div class="col-6">
                <? foreach ($tenants as $tenant){?>
                    <strong><a href="tenant_infosview.php?showdetail=&id=<?=$tenant['tenant_id']?>" target="_blank"><?=$tenant['full_name']?></a></strong><br>
            <? }?>
        </div>
    </div>


    <div class="row"><div class="col-12"><hr></div></div>


    <div class="row">
        <div class="col-6">Building:</div>
        <div class="col-6"><strong><?=$building_name?></strong></div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>

    <div class="row">
        <div class="col-6">Appartment:</div>
        <div class="col-6"><strong><?=$unit_number?></strong></div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>





    <div class="row">
        <div class="col-6">Please type CANCEL to cancel this payment:</div>
        <div class="col-6"><input type="text" name="cancel" id="cancel" size="6" placeholder="" value="" class="form-control" align="left"></div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>


    <div class="row">
        <div class="col-6">Reason for cancellation:</div>
        <div class="col-6"><textarea name="comments"  id="comments" class="form-control"></textarea> </div>
    </div>
    <div class="row"><div class="col-12"><hr></div></div>




    <div class="row"><div class="col-12">Transaction Logs:<br><br></div></div>

    <?
    $SelectSql = "select entry_datetime,amount,payment_date, LPD.comments, EMP.full_name as employee_name, PM.name as payment_method, PT.name as payment_type from lease_payment_details LPD            
                LEFT JOIN employee_infos EMP ON EMP.employee_id=LPD.entry_user_id
                LEFT JOIN payment_methods PM ON PM.id=LPD.payment_method_id
                LEFT JOIN payment_types PT ON PT.id=LPD.payment_type_id
                where lease_payment_id=" . $lease_payment_id;
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$rowNumbers=$result->rowCount();
    if ( $result ) {
    ?>
				<div class="row"><div class="col-12">
				<div class="table-responsive">
            <table class="table">
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
		?>
		    </table></div>
</div></div>		
			<?
        }else { // if rowsNumber
            echo "No transaction yet";

        }
        ?>










    <div class="row"><div class="col-12"><hr></div></div>



    <div class="row"><div class="col-12"><button id="form-submit" class="btn btn-primary">Cancel the payment</button>  <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button></div></div>

</div>
</form>
    <script>


        $(function () {
            $("#form-submit").click(function(){

                if ($("#comments").val()==""){ alert("Please enter the reason to cancellation.");return false;}
                if ($("#cancel").val()!="CANCEL"){ alert("Please type CANCEL to cancellation.");return false;}



                    $.ajax({
                        type: "POST",
                        data: {discount: $('#discount').val(), comments: $('#comments').val(), lease_payment_id: $('#lease_payment_id').val(), submitted: $('#submitted').val()},
                        url: "custom/cancel.php",
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

