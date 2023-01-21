<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$financialShow = new FinancialShow($_GET['fid'], $DB_con);

$leaseIDs = $financialShow->getFinancialDistributionByFDId();

$leasePaymentIds = $financialShow->getPaymentDetailByLeasePaymentId($leaseIDs);
//var_dump($leasePaymentIds);
foreach ($leasePaymentIds as $key => $value) {
    $leaseInfo[$key] = $DB_ls_payment->get_lease_info_by_lease_payment_id($value);
    $leaseInfo[$key]['unit_number'] = $DB_apt->getUnitNumber($leaseInfo[$key]['unit']);
    $leaseInfo[$key]['building_name'] = $DB_building->getBdName($leaseInfo[$key]['building_id']);
    $buildingInfo = $DB_building->getBdInfo($leaseInfo[$key]['building_id']);
    $ownerInfo = $DB_owner->getOwnerInfo($buildingInfo['owner_id']);
    $leaseInfo[$key]['owner_name'] = $ownerInfo['full_name'];
    $fullLeaseInformation = $DB_lease->getLeaseInfoByLeaseId($leaseInfo[$key]['lease_id']);
    $leaseInfo[$key]['tenant_ids'] = explode(',', $fullLeaseInformation['tenant_ids']);

    foreach ($leaseInfo[$key]['tenant_ids'] as $tKey => $tValue) {
        $tenantName = $DB_tenant->getTenantName($tValue);
        if ($tValue != null) {
            if (end($leaseInfo[$key]['tenant_ids'])) {
                $tenantNames = $DB_tenant->getTenantName($tValue);
            } else {
                $tenantNames = $DB_tenant->getTenantName($tValue) . ',';
            }
        }
    }

    $leaseInfo[$key]['tenant_names'] = $tenantNames;
}

class FinancialShow
{
    private $FdId;
    private $db;

    function __construct($FD_ID, $DB_con)
    {
        $this->FdId = $FD_ID;
        $this->db = $DB_con;
    }

    public function getFinancialDistributionByFDId()
    {
        $SelectSql = "select * from financial_distributes where FD_ID = $this->FdId";
        $statement = $this->db->prepare($SelectSql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $leaseIDs = array_map('intval', explode(',', $result['FD_leasePD_ids']));
        return $leaseIDs;
    }

    public function getPaymentDetailByLeasePaymentId($leaseIDs)
    {
        foreach ($leaseIDs as $key => $value) {
            $SelectSql = "select * from lease_payment_details where id = $value";
            $statement = $this->db->prepare($SelectSql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $leasePaymentIds[$key] = $result['lease_payment_id'];
        }
        return $leasePaymentIds;
    }

}

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered">
                <thead class="table-info">
                <tr>
                    <th scope="col">Row</th>
                    <th scope="col">Building Owner</th>
                    <th scope="col">Building name</th>
                    <th scope="col">Tenant name</th>
                    <th scope="col">Unit</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($leaseInfo as $key => $value) {

                    if ($value['lease_id'] != null) { ?>
                        <tr>
                            <th scope="row"><?php echo $key ?></th>
                            <td><?php echo $value['owner_name']; ?></td>
                            <td><?php echo $value['building_name']; ?></td>
                            <td><?php echo $value['tenant_names']; ?></td>
                            <td><?php echo $value['unit_number']; ?></td>
                        </tr>
                    <?php }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>