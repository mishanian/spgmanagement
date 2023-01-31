<?php session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    a {
        color: black
    }

    i {
        color: darkblue;
    }
    </style>
</head>

<body>
    <p></p>
    <div class="container">

        <div class="row">
            <div class="col-md-6" style="border: solid 1px gray; padding:50px">

                <?php
                if (empty($_GET['lid']) || empty($_SESSION['company_id'])) {
                    die("Wrong ID. Close this page");
                }
                $lease_id = $_GET['lid'];
                include('../../../pdo/dbconfig.php');
                $query = "select  *, PPT.name as period_name from lease_infos LI left join payment_period_types PPT on LI.payment_period=PPT.id where LI.id=" . $lease_id;
                // die($query);
                $stmt = $DB_con->prepare($query);
                $stmt->execute();
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                // $row = $rows[0];
                extract($row);
                //  die(print_r($row));

                $query = "select tenant_id, full_name from tenant_infos where tenant_id in ($tenant_ids)";
                $stmt = $DB_con->prepare($query);
                $stmt->execute();
                $tenants = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                // die(print_r($tenants));


                $query = "select  BI.building_name, BI.address as building_address , BI.postal_code as building_postal_code, AI.unit_number from apartment_infos AI left join building_infos BI on BI.building_id=AI.building_id where apartment_id=" . $apartment_id;
                $stmt = $DB_con->prepare($query);
                $stmt->execute();
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                // $row = $rows[0];
                extract($row);

                ?>
                <h2><?= $building_address . " #" . $unit_number ?>
                </h2>
                <hr>
                <h3>Generate Blank Renewal Notice</h3>
                <?php
                foreach ($tenants as $tenant) {
                    echo "<i class='fa fa-file-pdf-o fa-lg' aria-hidden='true'></i> <a target=_blank href='../tenant_portal/renewal_notice_empty.php?tenant_id=" . $tenant['tenant_id'] . "&lease_id=" . $lease_id . "'>Generate Renewal Notice for <b>" . $tenant['full_name'] . "</b></a><br>\n";
                }

                ?>

                <hr>

                <?php if (!$is_signed) {
                    echo "<a target='_blank' href='lease_sign.php?ws=e&hc=$hash_code&stid=1&prv=1'><i class='fa fa-pencil-square-o fa-lg' aria-hidden='true'></i>Manager Sign the lease</a>";
                } else {
                    if (!empty($sign_employee_id) && !empty($sign_DT)) {
                        $query = "select full_name from employee_infos where employee_id=$sign_employee_id";
                        $stmt = $DB_con->prepare($query);
                        $stmt->execute();
                        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                        echo $row['full_name'] . " Signed on : " . $signDT . "<br>";
                        echo "<a target='_blank' href='lease_pdf.php?hc=$hash_code&stid=1'><i class='fa fa-file-pdf-o fa-lg' aria-hidden='true'></i> Download PDF</a><br>";
                    }
                } ?>

                <?php
                $query = "select  * from tenant_sign_types where sign_type_id<5";
                $stmt = $DB_con->prepare($query);
                $stmt->execute();
                $sign_types = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($sign_types as $sign_type) { ?>
                <hr>
                <h3><?= $sign_type['name'] ?></h3>

                <!-- <?php foreach ($tenants as $tenant) { ?>
                <a target='_blank'
                    href='lease_pdf.php?hc=<?= $hash_code ?>&stid=<?= $sign_type['sign_type_id'] ?>&rid=<?= $tenant['tenant_id'] ?>&prv=1'><i
                        class='fa fa-eye fa-lg' aria-hidden='true'></i> Preview</a><br>
                <?php } ?> -->


                <?php if ($sign_type['sign_type_id'] != 4) { ?><a target='_blank'
                    href='lease_send_sign.php?hc=<?= $hash_code ?>&stid=<?= $sign_type['sign_type_id'] ?>'><i
                        class='fa fa-envelope fa-lg' aria-hidden='true'></i> Send to Tenant to sign</a><br>
                <? } ?>
                <?php
                    $query = "select TI.tenant_id, TI.full_name, is_signed, sign_DT, sign_IP, sign_user_info from tenant_infos TI LEFT JOIN tenant_signs TS ON TI.tenant_id=TS.tenant_id where TS.lease_id=$lease_id and TS.sign_type_id=" . $sign_type['sign_type_id'] . " and TS.is_signed=1 and TI.tenant_id IN ($tenant_ids)";
                    // echo $query;
                    $stmt = $DB_con->prepare($query);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    // print_r($rows);
                    if ($rows) {
                        foreach ($rows as $row) {
                            echo $row['full_name'] . "  Signed on " . $row['sign_DT'] . "<br>";
                            echo "<a target='_blank' href='lease_pdf.php?hc=$hash_code&stid=" . $sign_type['sign_type_id'] . "&rid=" . $row['tenant_id'] . "'><i class='fa fa-file-pdf-o fa-lg' aria-hidden='true'></i> Download PDF</a><br>";
                        }
                    }



                    /*
foreach ($tenants as $tenant) {
?>
                <a target='_blank'
                    href='lease_pdf.php?lid=<?=$lease_id?>&stid=<?=$sign_type['sign_type_id']?>&rid=<?=$tenant['tenant_id']?>'><i
                        class='fa fa-file-pdf-o fa-lg' aria-hidden='true'></i> Download PDF</a><br>
                <?
}
*/
                }
                ?>


                <br>
            </div>
        </div>

    </div>
</body>

</html>