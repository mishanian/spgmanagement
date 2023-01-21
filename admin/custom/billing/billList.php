<?php
include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Bill.php');
$DB_bill = new Bill($DB_con);
include_once('../pdo/Class.Vendor.php');
$DB_vendor = new Vendor($DB_con);
include_once('../pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('../pdo/Class.Request.php');
$DB_request = new Request($DB_con);
include_once('../pdo/Class.Snapshot.php');
$DB_snapshot = new Snapshot($DB_con);
?>
<style>
.form-control {
    width: 100% !important;
}

input[type=text]:not([size]):not([name=pageno]):not(.cke_dialog_ui_input_text),
input[type=password]:not([size]) {
    min-width: 100% !important;
}

.input-group>.form-control,
.input-group>.input-group-btn {
    width: auto !important;
}
</style>

<?php


$vendors = $DB_vendor->getVendorsList();

if (isset($_POST['bill_id']) && $_POST['randCheck'] == $_SESSION['rand']) {
    $DB_bill->updateBill($_POST);
    $DB_bill->updateBillDetails($_POST);
} else if (isset($_POST['submitBill']) && $_POST['randCheck'] == $_SESSION['rand']) {
    $DB_bill->insertBill($_POST);
    $DB_bill->insertBillDetails($_POST);
} else if ((isset($_POST['submitPayment']) || isset($_POST['printPayment'])) && $_POST['randCheck'] == $_SESSION['rand'] && !empty($_POST['selectedCheckBox'])) {
    foreach ($_POST['selectedCheckBox'] as $key => $value) {
        $index = 0;
        foreach ($_POST['grandTotal'] as $keyT => $valueT) {
            if ($valueT != null && $index == $key) {
                $bills[$key]['id'] = $value;
                $bills[$key]['amount'] = $valueT;
            }
            if ($valueT != null) {
                $index++;
            }
        }
    }

    foreach ($bills as $key => $value) {
        $billID = $value['id'];
        $billData = $DB_bill->getBillByID($billID);
        $totalPaidAmount = $value['amount'] + $billData['paid_amount'];

        $DB_bill->updatePaidAmount($billID, $totalPaidAmount);
        if ($totalPaidAmount == $billData['grand_total']) {
            $status = 1;
        } else if ($totalPaidAmount < $billData['grand_total']) {
            $status = 2;
        } else {
            $status = 0;
        }
        $DB_bill->updateBillStatus($billID, $status);
        $DB_bill->insertPayment($billID, $_POST);
    }
    unset($_POST);
}

try {
    $billInfo = $DB_bill->getAllBills();
} catch (PDOException $e) {
    echo $e->getMessage();
}


if (!empty($_SESSION['admin_id'])) {
    $buildings = $DB_building->getAllBdRowsByCompany($_SESSION['company_id']);
} else if (!empty($_SESSION['building_ids'])) {
    $buildings = $DB_building->getBuildingsByIds($_SESSION['building_ids']);
} else {
    $buildings = $DB_building->getAllBdRowsByEmployee($_SESSION['employee_id']);
}

if (!empty($_SESSION['company_id'])) {
    $contracts = $DB_request->getAllContractsByCompanyId($_SESSION['company_id']);
}
if (!empty($_SESSION['company_id'])) {
    $projects = $DB_request->getAllProjectsByCompanyId($_SESSION['company_id']);
}

?>
<div class="container-fluid">
    <div id="filter-part-current" class="col-sm-12 col-md-12 well" style="padding: 15px 0;">
        <div class="row">
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="vendor"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Project"); ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="project">
                            <option></option>
                            <?php foreach ($projects as $pKey => $pValue) {
                            ?>
                            <option value="<?php echo $pValue['name']; ?>"> <?php echo $pValue['name']; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="vendor"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Contract"); ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="contract">
                            <option></option>
                            <?php foreach ($contracts as $cKey => $cValue) {
                            ?>
                            <option value="<?php echo $cValue['contract_desc']; ?>">
                                <?php echo $cValue['contract_desc']; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="vendor"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Vendor"); ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="vendor">
                            <option></option>
                            <?php foreach ($vendors as $vKey => $vValue) {
                            ?>
                            <option value="<?php echo $vValue['company_name']; ?>">
                                <?php echo $vValue['company_name']; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="building"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Building"); ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="building">
                            <option></option>
                            <?php foreach ($buildings as $vKey => $vValue) {
                            ?>
                            <option value="<?php echo $vValue['building_name']; ?>">
                                <?php echo $vValue['building_name']; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="billDate"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Invoice Due"); ?>
                        :</label>
                    <div class="col-sm-8 col-md-9">
                        <input type="text" class="form-control date_input" id="billDate" style="min-width: 0;"
                            placeholder="YYYY-MM-DD" value="">
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="status"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Status"); ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="status">
                            <option value=""></option>
                            <option value="Paid">Paid</option>
                            <option value="Not Paid">Not Paid</option>
                            <option value="Partially Paid">Partially Paid</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 filter-block">

            </div>

            <div class="col-sm-4 col-md-3 filter-block" style="padding-top: 5px;">
                <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"
                    id="clear_search"><?php echo $DB_snapshot->echot("Clear"); ?></button>
                <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-1"
                    id="search_current"><?php echo $DB_snapshot->echot("Search"); ?></button>
            </div>
        </div>

    </div>
    <!-- filter end-->
    <form action="billList.php" method="post">
        <input type="hidden" value="<?php $rand = rand();
                                    $_SESSION['rand'] = $rand;
                                    echo $rand; ?>" name="randCheck" />
        <div class="row">
            <div class="col-md-12">
                <table id="billTable" class="table table-striped table-bordered">
                    <thead class="table-info">
                        <tr>
                            <th scope="col">Select</th>
                            <th scope="col">Ref No</th>
                            <th scope="col">Request No</th>
                            <th scope="col">Invoice No</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Contract</th>
                            <th scope="col">Project</th>
                            <th scope="col">Building</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Date</th>
                            <th scope="col">Invoice Due</th>
                            <th scope="col">Memo</th>
                            <th scope="col">Detail</th>
                            <th scope="col">Status</th>
                            <th scope="col">Contract Price</th>
                            <th scope="col">Invoice Price</th>
                            <th scope="col">Approved to pay</th>
                            <th scope="col">Paid Amount</th>
                            <th scope="col">Outstanding</th>
                            <th scope="col">Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($billInfo)) {
                            foreach ($billInfo as $key => $value) { ?>
                        <tr>
                            <td class="billCheckBox"><input class="selectedCheckBox" type="checkbox"
                                    id="selectedCheckBox-<?php echo $key ?>" name="selectedCheckBox[]"
                                    value="<?php echo $value['id']; ?>" />
                            </td>
                            <td><?php echo $value['ref_no']; ?></td>
                            <td><a href="requestadd?action=rview&rid=<?php echo $value['request_id'] ?>"
                                    target="_blank"><?php echo $value['request_id']; ?></a></td>
                            <td><?php echo $value['invoice_no']; ?></td>
                            <td>
                                <?php
                                        foreach ($vendors as $vKey => $vValue) {
                                            if ($vValue['vendor_id'] == $value['vendor_id']) {
                                                echo $vValue['company_name'];
                                            }
                                        }
                                        ?>
                            </td>
                            <td>
                                <?php foreach ($contracts as $cKey => $cValue) {
                                            echo $cValue['contract_id'] == $value['contract_id'] ? $cValue['contract_desc'] : ''; ?>
                                <?php
                                        } ?>
                            </td>
                            <td>
                                <?php foreach ($projects as $pKey => $pValue) {
                                            echo $pValue['project_id'] == $value['project_id'] ? $pValue['name'] : ''; ?>
                                <?php
                                        } ?>
                            </td>
                            <td>
                                <?php
                                        if (!empty($value['building_id'])) {
                                            $buildingName = $DB_building->getBdName($value['building_id']);
                                            echo $buildingName;
                                        } ?>
                            </td>
                            <td>
                                <?php
                                        if (!empty($value['apartment_id'])) {
                                            $unitNumber = $DB_apt->getUnitNumber($value['apartment_id']);
                                            echo $unitNumber;
                                        }
                                        ?>
                            </td>
                            <td><?php echo date("d-m-Y", strtotime($value['bill_date'])); ?></td>
                            <td><?php echo date("d-m-Y", strtotime($value['due_date'])); ?></td>
                            <td><?php echo $value['memo']; ?></td>
                            <td><a href="addEditBill.php?bill_id=<?php echo $value['id'] ?>">Detail</a>
                            </td>
                            <td><?php
                                        if ($value['bill_status'] == 0) {
                                            echo 'Not Paid';
                                        }
                                        if ($value['bill_status'] == 1) {
                                            echo 'Paid';
                                        }
                                        if ($value['bill_status'] == 2) {
                                            echo 'Partially Paid';
                                        } ?>
                            </td>
                            <td>
                                <?php $contractInfo = $DB_request->getContractDataByContractId($value['contract_id']);
                                        ?>
                                <input type="text" class="form-control amount" disabled
                                    id="contractPrice-<?php echo $key ?>"
                                    value="<?php $floatValue = (float)$contractInfo['vendor_contract_price'];
                                                                                                                                                echo round($floatValue, 2); ?>" />
                            </td>
                            <td>
                                <input type="text" class="form-control amount" disabled id="amount-<?php echo $key ?>"
                                    value="<?php $floatValue = (float)$value['grand_total'];
                                                                                                                                        echo round($floatValue, 2); ?>" />
                            </td>
                            <td><input type="text" class="form-control approvedToPay" disabled name="approvedToPay[]"
                                    id="approvedToPay-<?php echo $key ?>"
                                    value="<?php echo !empty($value['approved_to_pay']) ? $value['approved_to_pay'] : '' ?>" />
                            </td>
                            <td><input type="text" class="form-control paidAmount" disabled
                                    id="paidAmount-<?php echo $key ?>"
                                    value="<?php $floatValue = (float)$value['paid_amount'];
                                                                                                                                                echo round($floatValue, 2); ?>" />
                            </td>
                            <td><input class="form-control grandTotal" type="text" name="grandTotal[]"
                                    id="grandTotal-<?php echo $key ?>" /></td>
                            <td>
                                <?php if (!empty($value['file_name'])) { ?>
                                <a target="_blank"
                                    href="custom/billing/uploads/<?php echo $value['id'] ?>/<?php echo $value['file_name'] ?>">
                                    <?php echo $value['file_name'] ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <?php include "payment.php" ?>
            </div>
        </div>
        <br />
    </form>
</div>
<script>
loadjs(["https://cdn.datatables.net/v/dt/jq-3.6.0/datatables.min.css",
    "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"
]);
ew.ready("head", ["https://cdn.datatables.net/v/dt/jq-3.6.0/datatables.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"
], "datatables");
</script>
<script>
$(document).ready(function() {
    $('.grandTotal').on("change", function(e) {
        let id = e.target.id.split('-')[1];
        let remainingAmount = $('#amount-' + id).val() - $('#paidAmount-' + id).val();
        let amountToBePaid = $('#grandTotal-' + id);
        if (remainingAmount < amountToBePaid.val()) {
            alert("The amount should be less than the remaining amount to pay.");
            amountToBePaid.val(0);
        }
        let totalSum = calculateTotalAmount();
        $('#totalAmountToPay').val(Math.round(totalSum * 100) / 100);
        $('#grandTotalToPay').val(Math.round(totalSum * 100) / 100);
    });

    $('.selectedCheckBox').on("click", function(e) {
        if (!$(this).is(':checked')) {
            let id = e.target.id.split('-')[1];
            $('#grandTotal-' + id).val("");
            //   $("#approvedToPay-" + id).prop("disabled", true);
        } else {
            let id = e.target.id.split('-')[1];
            // $("#approvedToPay-" + id).prop("disabled", false);
            let value = $('#amount-' + id).val() - $('#paidAmount-' + id).val();
            if (value < 0) {
                alert("The amount should not be less than 0.");
                value = 0;
            }
            $('#grandTotal-' + id).val(Math.round(value * 100) / 100);
        }
        let totalSum = calculateTotalAmount();
        $('#totalAmountToPay').val(Math.round(totalSum * 100) / 100);
        $('#grandTotalToPay').val(Math.round(totalSum * 100) / 100);
    });

    function calculateTotalAmount() {
        let totalElements = document.querySelectorAll('.grandTotal');
        let sum = 0;
        for (let i = 0; i < totalElements.length; i++) {
            if (totalElements[i].value != null) {
                sum = +sum + +totalElements[i].value;
            }
        }
        return sum;
    }

    // DataTable
    let table = $('#billTable').DataTable({
        "scrollX": true
    });

    $('#date').datetimepicker({
        format: 'y-MM-dd'
    });
    $('#billDate').datetimepicker({
        format: 'y-MM-dd'
    });

    $('#search_current').click(function() {
        updateTable();
    });

    $('#clear_search').click(function() {
        $('#vendor').val("");
        $('#project').val("");
        $('#contract').val("");
        $('#status').val("");
        $('#building').val("");
        $('#billDate').val("");
        updateTable();

    });

    function updateTable() {
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let vendor = $('#vendor').val();
                let contract = $('#contract').val();
                let project = $('#project').val();
                let status = $('#status').val();
                let building = $('#building').val();
                let billDate = $('#billDate').val();
                let vendorValue = data[4];
                let contractValue = data[5];
                let projectValue = data[6];
                let buildingValue = data[7];
                let billDateValue = data[10];
                let statusValue = data[13];

                let vendorCondition = true;
                if (vendor) {
                    vendorCondition = (vendor === vendorValue);
                }

                let contractCondition = true;
                if (contract) {
                    contractCondition = (contract === contractValue);
                }

                let projectCondition = true;
                if (project) {
                    projectCondition = (project === projectValue);
                }

                let buildingCondition = true;
                if (building) {
                    buildingCondition = (building === buildingValue);
                }

                let statusCondition = true;
                if (status) {
                    statusCondition = (status === statusValue);
                }

                let billDateCondition = true;
                if (billDate) {
                    billDateCondition = (billDate === billDateValue);
                }

                return (vendorCondition &&
                    buildingCondition &&
                    statusCondition &&
                    contractCondition &&
                    projectCondition &&
                    billDateCondition);
            }
        );
        table.draw();
    }

    $('#vendor').select2();
    $('#project').select2();
    $('#contract').select2();
    $('#building').select2();
    $('#status').select2();
});
</script>