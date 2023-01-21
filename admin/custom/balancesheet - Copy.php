<div class="container">
    <table class="table table-striped" width="300">
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
        $where=" true";
        $lastT = $t - 1;
        if(!empty($id)) {
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
            $sql = "select sum(amount) as total from payment_infos WHERE payment_action_id=7 and $where=" . $account_sub_type['id'] . " and paid_owner_id=" . $owner_id;
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
                <td><?= $total ?></td>
            </tr>

            <?
        }
        ?>
    </table>
</div>