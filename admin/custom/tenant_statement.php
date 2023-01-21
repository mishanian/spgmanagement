<table cellpadding="5" cellspacing="5" border="1" align="center">
    <tr><th colspan="6">Statement </h1></th></tr>
    <tr><td colspan="3">Company Name&nbsp;</td><td colspan="3">Date: <?=date("Y-m-d")?></td></tr>
    <tr><td colspan="3">Address</td><td colspan="3">Statement#:</td></tr>
    <tr><td colspan="3">&nbsp;</td><td colspan="3">Customer ID:</td></tr>
    <tr><td colspan="3">Bill To:</td><td colspan="3">Comments</td></tr>
    <tr><td colspan="3">Address</td><td colspan="3">&nbsp;</td></tr>

    <tr><td>Date</td><td>Reference</td><td>Description</td><td>Charge</td><td>Payment</td><td>Balance</td></tr>
    <?php

    if (strpos(getcwd(), "custom") == false) {
        $path = "../pdo/";
    } else {
        $path = "../../pdo/";
    }
    $file = $path . 'dbconfig.php';
include_once($file);

$SelectSql = "SET @@session.group_concat_max_len = 1048576;";
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$tenant_id=100038;
$balance=0;

    $FromDate=date("Y-m-1");
    $ToDate = date("Y-m-t", strtotime($FromDate));
    $FromDateObj=date_create($FromDate);
    $ToDateObj=date_create($ToDate);
    $DateDiff=date_diff($FromDateObj,$ToDateObj );
    $DiffDay=$DateDiff->format("%a");
//echo ("$FromDate - $ToDate = $DiffDay<br>");

//for ($i=0;$i<=$DiffDay;$i++) {
//    $CurrentDateObj = date_add(date_create($FromDate), date_interval_create_from_date_string("$i days"));
//    $CurrentDate = date_format($CurrentDateObj, "Y-m-d");
// echo "$CurrentDate<br>";


    $Sql = "select * from view_tenant_statement where FIND_IN_SET(tenant_ids,$tenant_id)"; //and LPD.payment_date='$CurrentDate'
//$Sql = "SELECT
//id, DBCR, due_date, amount, description, tenant_ids, comments ,
//IF(DBCR='d',amount,'-') AS charge,
//IF(DBCR='c',amount,'-') AS payment,
//IF(DBCR='d',@a:=@a+amount,@a:=@a-amount) AS balance
//FROM view_tenant_statement_wo_balance
//JOIN (SELECT @a:=0) AS ATable  where FIND_IN_SET(tenant_ids,$tenant_id) ";
    //echo ("$Sql<br>");

    $statement = $DB_con->prepare($Sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $rows){

        foreach ($rows as $key => $value) {
            $$key = $value;
        }

    if ($DBCR=="c"){
    $reference="INV-".date_format(date_create($due_date),"Y")."-$id";
        $balance=$balance+$amount;
        }else{
    $reference="REC-".date_format(date_create($due_date),"Y")."-$id";
        $balance=$balance-$amount;
    }



        ?>


        <tr>
            <td><?= date_format(date_create($due_date),"Y/m/d");  ?></td>
            <td><?=$reference?></td>
            <td><?=$description?></td>
            <td align="right"><? if ($DBCR=="d"){echo $amount;}else{echo "-";}?></td>
            <td align="right"><? if ($DBCR=="c"){echo $amount;}else{echo "-";}?></td>
            <td align="right"><?=$balance?></td>
        </tr>


    <?
    //}
}
?>


<tr><td colspan="6" align="center"><br>Make all checks payable to  9221-3909 Quebec Inc.<br>
        Thank you for your business!<br>
        1650 Rene Levesque West, Montreal, H3H 2S1
    </td></tr>

</table>