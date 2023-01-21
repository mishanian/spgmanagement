<div class="container">
<?
include '../pdo/dbconfig.php';
include '../pdo/Class.Owner.php';
$Crud = new CRUD($DB_con);
$db_owner = new Owner($DB_con);

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}
if (!empty($_GET['oid'])) {
    $owner_id = $_GET['oid'];
}
if (!empty($_GET['t'])) {
    $t = $_GET['t'];
}else{
    $t=1;
}
$OwnerName = $db_owner->getOwnerName($owner_id);
?>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" href="#Income" role="tab" data-toggle="tab">Income</a>
        </li>
        <li>
            <a class="nav-link" href="#Expences" role="tab" data-toggle="tab">Expences</a>
        </li>

    </ul>
    <div class="tab-content">


        <div class="tab-pane active in" id="Income">
            <?
            $sql = "SELECT YEAR(LP.due_date) as due_year, LP.due_date, sum(LP.total-LP.outstanding) AS amount, LI.building_id, BI.building_name, BI.owner_id, OC.owner_account_id AS acccount_id, OC.bank_id, OC.financial_accountNo
    FROM lease_payments LP
    LEFT JOIN lease_infos LI ON LP.lease_id=LI.id
    LEFT JOIN building_infos BI ON BI.building_id=LI.building_id
    LEFT JOIN owner_accounts OC ON BI.owner_id=OC.owner_id
    WHERE payment_status_id IN(2,3,5,6) and BI.owner_id=$owner_id
    group by YEAR(LP.due_date), OC.owner_account_id, LI.building_id
    order by LP.due_date DESC ";
    // die($sql);
            $result = $Crud->query($sql);
            $rents = $Crud->resultSet();
            ?>
            <table class="table table-striped table-hover" width="300">
                <tr>
                    <td>Year</td>
                    <td>Property</td>
                    <td align="right">Amount</td>
                    <td align="right">Account No.</td>
                </tr>
                <?
                foreach ($rents as $rent) {?>
                    <tr>
                        <td><?=$rent['due_year']?></td>
                        <td><?=$rent['building_name']?></td>
                        <td align="right"><?=number_format($rent['amount'],2 , '.', ',');?></td>
                        <td align="right"><?=$rent['financial_accountNo']?></td>
                    </tr>

                <? } ?>
            </table>

        </div>
<!--- Expences --->

        <div class="tab-pane fade" id="Expences">

        <table class="table table-striped table-hover" width="300">
            <?

            $where=" true";
            $lastT = $t - 1;
            $nextT =$t+1;
            if(!empty($id) && $lastT>0) {
                $where= " account_type".$lastT."_id=" . $id;
            }
            $sql = "select * from account_types".$t." where $where" ;

            //echo $sql;
            $result = $Crud->query($sql);
            $account_sub_types = $Crud->resultSet();
            //var_dump($account_sub_types);
            ?>
            <tr>
                <th colspan="2"><?= $OwnerName ?></th>
            </tr>
            <?
            foreach ($account_sub_types as $account_sub_type) {
                $total = 0;
                $sql = "select sum(amount) as total from payment_infos WHERE payment_action_id=7 and account_type".$t."_id=" . $account_sub_type['id'] . " and paid_owner_id=" . $owner_id;
                //  echo $sql."<br>";
                $result = $Crud->query($sql);
                $total = $Crud->resultField();
                if (empty($total)) {
                    $total = 0;
                }

                ?>
                <tr>
                    <td>
                        <? if ($t <= 1){ ?>
                        <a href="balancesheet.php?t=<?= $nextT ?>&oid=<?= $owner_id ?>&id=<?= $account_sub_type['id'] ?>">
                            <? }else{ ?>
                            <a href="balancesheet.php?t=<?= $nextT ?>&oid=<?= $owner_id ?>&id=<?= $account_sub_type['id'] ?>">
                                <? }?>
                                <?= $account_sub_type['name'] ?>

                            </a></td>
                    <td align="right"><?=number_format($total,2 , '.', ','); ?></td>
                </tr>

                <?
            }
            ?>
        </table>











        </div>
    </div>








</div>