<?
include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Vendor.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$DB_Vendor = new Vendor($DB_con);
?>
<script>
    loadjs.ready("jquery", function () {
        loadjs(["vendor/snapappointments/bootstrap-select/dist/css/bootstrap-select.min.css", "vendor/snapappointments/bootstrap-select/dist/js/bootstrap-select.min.js"]);
    });
</script>
<?

if(isset($_POST['submit'])) {
    extract($_POST);
//    var_dump($_POST);
$sql="select tax1+tax2+tax3 as tax from provinces where id=1";
$statement = $DB_con->query($sql, PDO::FETCH_ASSOC);
$tax=number_format($statement->fetch()['tax'],3);
// die(var_dump($tax));
if(empty($_POST['amount_wo_tax'])){$amount_wo_tax=number_format($amount/$tax,2);}
if(empty($project_id)){$project_id='NULL';}
if(empty($contract_id)){$contract_id='NULL';}
if(empty($invoice_id)){$invoice_id='NULL';}
$sql="insert into payment_infos (payment_date,is_ready_print, payment_action_id,payment_inex_id,payment_inex_type_id, payment_method_id, vendor_id, paid_owner_id, project_id, contract_id, invoice_id, invoice_category_id, amount_wo_tax, amount, applied_amount, cheque_no, material_by_owner, income, expence, approved_by_employee_id, employee_id, company_id, memo) values ('".date("Y-m-d")."',1,5,2,4,4,$vendor_id, $paid_owner_id, $project_id, $contract_id, $invoice_id, 1, $amount_wo_tax, $amount, $amount, $cheque_no, 1, 0, $amount, ".$_SESSION['employee_id'].", ".$_SESSION['employee_id'].",".$_SESSION['company_id'].", '$memo')";
//echo $sql; 
$statement = $DB_con->prepare($sql);
    $statement->execute();
    // $DB_con->commit();
$payment_id=$DB_con->lastInsertId();
?>    
Your cheque is submitted. 
<form method="post" action="custom/billing/printPaymentPdf.php" target="_blank">
<input type="hidden" name="action_type_id" value="5">
<input type="hidden" name="id" value="<?=$payment_id?>">
<input type="submit" class='btn btn-primary' value="Print">
</form>
<?
}else{
?>
<form method="post" action="issue_cheque.php">
<div class="container">
    <div class="card">
        <div class="card-header">
            Cheque Issue
        </div>
        <div class="card-body">
            <div class="form-row align-items-center">
                <div class="form-group col-6">
                    <?
                    $vendors = $DB_Vendor->getVendorsListByCompany($_SESSION['company_id']);
                    // echo(var_dump($vendors));
                    ?>
                    <label>Pay to: </label> <select name="vendor_id" id="vendor_id" class="selectpicker" data-live-search="true"
                                                    title="Choose Pay to?" required >
                        <? foreach ($vendors as $vendor) { ?>
                            <option value="<?= $vendor['vendor_id'] ?>"><?= $vendor['company_name'] ?></option>
                        <? } ?>
                    </select><a class="btn btn-primary" target="_blank" href="vendor_infosadd.php?showdetail=">Add Vendor</a>

                </div>

                <div class="form-group col-6">
                    <?
                    $ownervendors = $DB_Vendor->getVendorOwnerListByCompany($_SESSION['company_id'],1);
                    // echo(var_dump($vendors));
                    ?>
                    <label>Pay From Owner: </label> <select name="paid_owner_id" id="paid_owner_id" class="selectpicker" data-live-search="true"
                                                    title="Pay From Company?" required >
                        <? foreach ($ownervendors as $ownervendor) { ?>
                            <option value="<?= $ownervendor['owner_vendor_id'] ?>"><?= $ownervendor['owner_vendor_name'] ?></option>
                        <? } ?>
                    </select><a class="btn btn-primary" target="_blank" href="owner_infosadd.php?showdetail=">Add Owner</a>

                </div>

                <div class="form-group col-6">


                    <label>Project:</label> <select name="project_id" id="project_id" class="selectpicker"
                                                              data-live-search="true"
                                                              title="Which Project?" >
                    </select><a class="btn btn-primary" target="_blank" href="project_infosadd.php?showdetail=">Add Project</a>


                </div>

                <div class="form-group col-6">

                    <label>Contract:</label> <select name="contract_id" id="contract_id" class="selectpicker"
                                                               data-live-search="true"
                                                               title="Which Contract?" >
                    </select><a class="btn btn-primary" target="_blank" href="contract_infosadd.php?showdetail=">Add Contract</a>
                </div>
                <div class="form-group col-6">

                    <label>Invoice:</label> <select name="invoice_id" id="invoice_id" class="selectpicker"
                                                              data-live-search="true"
                                                              title="Which Invoice?">

                    </select><a class="btn btn-primary" target="_blank" href="invoice_infosadd.php?showdetail=">Add Invoice</a>

                </div>
                <!--<div class="form-group col-6">
                    <label>Approved Amount: </label> <input type="text" class="form-control" name="amount_wo_tax" id="amount_wo_tax"
                                                            placeholder="amount before tax" required >
                </div>-->
                <div class="form-group col-6">
                    <label>Amount After Tax: </label> <input type="text" name="amount" id="amount" class="form-control"
                                                             placeholder="amount" required >
                </div>
                <div class="form-group col-6">
                    <label>Cheque No.: </label> <input type="text" name="cheque_no" class="form-control"
                                                       placeholder="Cheque No." required >
                </div>
                <div class="form-group col-6">
                    <label>Cheque Memo: </label> <input type="text" name="memo" id="memo" class="form-control"
                                                       placeholder="Memo writen on cheque"  >
                </div>
                <div class="form-group col-6">
                    <label>Which Bank: </label>
                    <select id="bank_id" name="bank_id" class="selectpicker" data-live-search="true" title="Which Bank?" required >
                        <option value="1" data-content="<img src='images/TDBankLogo.png' width=25px> TD Bank" selected>TD Bank</option>
                        <option value="2" data-content="<img src='images/RBCBankLogo.jpg' width=25px> RBC Bank" >RBC Bank</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <button type="submit" name="submit" class="btn btn-primary mb-2">Submit</button>
                </div>
            </div>
        </div>

        <div id="chequeImage" style='background: url("images/td_cheque.jpg") no-repeat'>
            <table style=" border: 1px solid black;" width="600">
                <tr>
                    <td width="460" height="75">&nbsp;</td>
                    <td valign="bottom" style="font-size: 18px; color: maroon"><?= date("y") ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?= date("m") ?>&nbsp;&nbsp;&nbsp;<?= date("d") ?></td>
                </tr>
                <tr>
                    <td width="450" height="50" style="padding-left: 100px; font-size: 18px; color: maroon"><span
                                id="vendor_name">&nbsp;</span></td>
                    <td style="padding-left: 30px; font-size: 18px; color: maroon"><span id="spanAmount">&nbsp;</span></td>
                </tr>
                <tr>
                    <td height="45">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top" height="100" style="padding-left: 60px; font-size: 12px; color: maroon">
                        <span id="spanMemo">&nbsp;</span>&nbsp;
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>

        </div>


    </div>
</div>
</form>
    <script>
        function fillSelect(fillField) {
            vendor_id = $("#vendor_id").val();
            project_id = $("#project_id").val();
            contract_id = $("#contract_id").val();
            $.getJSON("custom/getProjectByVendor.php", {
                    'v': vendor_id,
                    'p': project_id,
                    'c': contract_id
                },
                function (data) {
                    //      console.log(data);

                    projects = data.p;
                    contracts = data.c;
                    invoices = data.i;
                    //alert(projects[0].project_name);
                    var sel = $("#" + fillField + "_id");
                    sel.empty();
                    if (fillField == "project") {
                        for (var i = 0; i < projects.length; i++) {
                            sel.append('<option value="' + projects[i].project_id + '">' + projects[i].project_name + '</option>');
                        }
                    } else if (fillField == "contract") {
                        for (var i = 0; i < contracts.length; i++) {
                            sel.append('<option value="' + contracts[i].contract_id + '">' + contracts[i].contract_name + '</option>');
                        }
                    } else if (fillField == "invoice") {
                        for (var i = 0; i < invoices.length; i++) {
                            sel.append('<option value="' + invoices[i].invoice_id + '">' + invoices[i].invoice_no + '</option>');
                        }
                    }
                    sel.selectpicker('refresh');
                });
        }

        loadjs.ready("jquery", function () {
            $("#vendor_id").change(function () {
                fillSelect("project");
                $("#vendor_name").text($("#vendor_id option:selected").text());
                $("#memo").text("");
            });
            $("#project_id").change(function () {
                fillSelect("contract");
                $("#spanMemo").text($("#spanMemo").text()+$("#project_id option:selected").text());
            });
            $("#contract_id").change(function () {
                fillSelect("invoice");
                $("#spanMemo").text($("#spanMemo").text()+", "+$("#contract_id option:selected").text());
            });
            $("#contract_id").change(function () {
                fillSelect("invoice");
                $("#spanMemo").text($("#spanMemo").text()+", "+$("#contract_id option:selected").text());
            });
            $("#invoice_id").change(function () {
                $("#spanMemo").text($("#spanMemo").text()+", "+$("#invoice_id option:selected").text());
            });
            $("#memo").change(function () {
                $("#spanMemo").text($("#spanMemo").text()+", "+$("#memo").val());
            });
            $("#amount").change(function () {
                $("#spanAmount").text($("#amount").val());
            });
            $("#bank_id").change(function () {
                if($("#bank_id").val()==1){
                imageSrc="images/td_cheque.jpg";}else{
                imageSrc="images/rbc_cheque.jpg";}
                $("#chequeImage").css("background-image", "url("+imageSrc+")");
            });
        });
    </script>
<? }?>