<?php if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$dbClass = $path . 'Class.Bill.php';
include_once($dbClass);
$DB_bill = new Bill($DB_con);
include_once("../pdo/Class.Building.php");
$DB_building = new Building($DB_con);
include_once("../pdo/Class.Apt.php");
$DB_apt = new Apt($DB_con);
include_once("../pdo/Class.Vendor.php");
$DB_vendor = new Vendor($DB_con);
include_once("../pdo/Class.Province.php");
$DB_province  = new Province($DB_con);
include_once("../pdo/Class.Request.php");
$DB_request = new Request($DB_con);
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />


<?php
if (!empty($_SESSION['admin_id'])) {
    $buildings = $DB_building->getAllBdRowsByCompany($_SESSION['company_id']);
    $apartments = $DB_apt->getAptInfoInBuilding($buildings[0]['building_id']);
} else if (!empty($_SESSION['building_ids'])) {
    $buildings = $DB_building->getBuildingsByIds($_SESSION['building_ids']);
    $apartments = $DB_apt->getAptInfoInBuilding($buildings[0]['building_id']);
} else {
    $buildings = $DB_building->getAllBdRowsByEmployee($_SESSION['employee_id']);
    $apartments = $DB_apt->getAptInfoInBuilding($buildings[0]['building_id']);
}
sortUnits($apartments);

$vendors = $DB_vendor->getVendorsList();

$province_id = $vendors[0]['province_id'];
$result = $DB_province->getProvinceInfo($province_id);
$tax['province'] = $result['name'];
$tax['GST'] = $result['tax1'];
$tax['QST'] = $result['tax2'];
$tax['HST'] = $result['tax3'];

if ($result['tax1'] != 0 && $result['tax1'] != null) {
    $tax['type'] = 'GST';
}
if ($result['tax2'] != 0 && $result['tax1'] != null) {
    $tax['type'] = $tax['type'] . ',Qst';
}
if ($result['tax3'] != 0 && $result['tax1'] != null) {
    $tax['type'] = $tax['type'] . ',HST';
}

for ($i = 1; $i <= 3; $i++) {
    $id = $vendors[0]['account_type' . $i];
    if (!empty($id)) {
        $result = $DB_bill->getAccountTypeByID($id);
        $vendorTypes[$i] = $result['name'];
    }
}

if (isset($_GET['request_id'])) {
    $attachments = $DB_bill->getAttachmentsByRequestID($_GET['request_id']);
    $requestInfo = $DB_request->get_request_info($_GET['request_id']);
    $buildingID = $requestInfo['building_id'];
    $apartmentID = $requestInfo['apartment_id'];
    $apartments = $DB_apt->getAptInfoInBuilding($buildingID);
    sortUnits($apartments);
} else if (isset($_GET['bill_id'])) {
    $billId = $_GET['bill_id'];
    $billInfoDetail = $DB_bill->getBillByID($billId);
    $billDetails = $DB_bill->getBillDetailsByID($billId);
    $vendorID = $billInfoDetail['vendor_id'];
    $apartmentID = $billInfoDetail['apartment_id'];
    $buildingID = $billInfoDetail['building_id'];
    $apartments = $DB_apt->getAptInfoInBuilding($buildingID);
    sortUnits($apartments);

    if (!empty($billInfoDetail['request_id'])) {
        $attachments = $DB_bill->getAttachmentsByRequestID($billInfoDetail['request_id']);
        $paymentDetails = $DB_vendor->getPaymentDetails($billInfoDetail['request_id']);
        $requestInfo = $DB_request->get_request_info($billInfoDetail['request_id']);
    }
    foreach ($billDetails as $key => $value) {
        $billInfoDetail['detail'][$key]['accountType'] = $value['account_type_id'];
        $billInfoDetail['detail'][$key]['amount'] = $value['amount'];
        $billInfoDetail['detail'][$key]['taxType'] = $value['tax_type'];
        $billInfoDetail['detail'][$key]['taxAmount'] = $value['tax'];
        $billInfoDetail['detail'][$key]['description'] = $value['description'];
        $billInfoDetail['detail'][$key]['apartment_id'] = $value['apartment_id'];
        $billInfoDetail['detail'][$key]['building_id'] = $value['building_id'];
        $billInfoDetail['detail'][$key]['totalAmount'] = $value['total'];
    }
}

if (!empty($attachments['invoices_attached'])) {
    $attachments = json_decode($attachments['invoices_attached']);
    $files = array();
    $dir = opendir('files');
    foreach ($attachments as $key => $value) {
        while (false != ($file = readdir($dir))) {
            if (($file == $value)) {
                $files[] = $file;
            }
        }
    }
}

if (!empty($_SESSION['company_id'])) {
    $contracts = $DB_request->getAllContractsByCompanyId($_SESSION['company_id']);
}
if (!empty($_SESSION['company_id'])) {
    $projects = $DB_request->getAllProjectsByCompanyId($_SESSION['company_id']);
}


function sortUnits($apartments)
{
    $unitNumber = array();
    foreach ($apartments as $key => $row) {
        $unitNumber[$key] = $row['unit_number'];
    }
    array_multisort($unitNumber, SORT_ASC, $apartments);
}


?>

<div class="container-fluid">
    <form action="billList.php" method="post" id="billForm" enctype="multipart/form-data">
        <input type="hidden" value="<?php $rand = rand();
                                    $_SESSION['rand'] = $rand;
                                    echo $rand; ?>" name="randCheck" />
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="vendors">Vendor</label>
                                    <select class="form-control" id="vendors" name="vendor_id">
                                        <!-- onchange="updateVendorInfo(event)"> -->
                                        <?php
                                        foreach ($vendors as $key => $value) { ?>
                                            <option name="vendor_id" <?php echo $value['vendor_id'] == (!empty($vendorID) ? $vendorID : '') ? 'selected' : '' ?> value="<?php echo $value['vendor_id'] ?>">
                                                <?php echo $value['company_name'] ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold" for="address">Address</label>
                                    <textarea autocomplete='street-address' class="form-control" id="address" name="address" rows="5">   <? if (!empty($vendorID)) {
                                                                                                                                                foreach ($vendors as $key => $value) {
                                                                                                                                                    if ($value['vendor_id'] == $vendorID) {
                                                                                                                                                        echo $value['address'];
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                echo $vendors[0]['address'];
                                                                                                                                            } ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold" for="date">Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='date'>
                                        <input type='text' name="date" class="form-control" value="<?php echo isset($_GET['bill_id']) ? date("d-m-Y", strtotime($billInfoDetail['bill_date'])) : date('d-m-y') ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold" for="amountDue">Total Balance / Contract</label>
                                    <input type="text" class="form-control" id="amountDue" name="amountDue" value="<?php echo !empty($billInfoDetail['grand_total']) ? $billInfoDetail['grand_total'] : '' ?>">
                                </div>
                                <label class="font-weight-bold" for="billDate">Bill Due</label>
                                <div class="form-group">
                                    <div class='input-group date' id='billDate'>
                                        <input type='text' name="billDate" class="form-control" value="<?php echo !empty($billInfoDetail['due_date']) ? date("d-m-Y", strtotime($billInfoDetail['due_date'])) : date('d-m-y') ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="memo">Memo</label>
                                    <input type="text" class="form-control" id="memo" name="memo" value="<?php echo !empty($billInfoDetail['memo']) ? $billInfoDetail['memo'] : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="project">Project
                                        :</label>
                                    <select class="form-control" id="project" name="projectID">
                                        <!-- onchange="getContracts(event)"> -->
                                        <?php
                                        foreach ($projects as $key => $value) { ?>
                                            <option name="projectID" <?php echo $value['project_id'] == (!empty($billInfoDetail['project_id']) ? $billInfoDetail['project_id'] : '') ? 'selected' : '' ?> value="<?php echo $value['project_id'] ?>"><?php echo $value['name'] ?>
                                            </option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="contract">Contract
                                        : </label>
                                    <select class="form-control" id="contract" name="contractID">
                                        <!-- onchange="updateContractInfo(event)"> -->
                                        <!--     <?php
                                                    /*                                        foreach ($contracts as $key => $value) { */ ?>
                                            <option name="contractID" <?php /*echo $value['contract_id'] == (!empty($billInfoDetail['contract_id']) ? $billInfoDetail['contract_id'] : '') ? 'selected' : '' */ ?>
                                                    value="<?php /*echo $value['contract_id'] */ ?>"><?php /*echo $value['contract_desc'] */ ?></option>
                                        --><?php /*}
                                        */ ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="discount">Discount percentage</label>
                                    <input type="text" class="form-control" id="discountPercentage" name="discountPercentage" value="" placeholder="%">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="labor">Labor:</label>
                                    <input checked id="labor" name="labor" type="radio" value="labor" />
                                    <label class="font-weight-bold" for="material">Material:</label>
                                    <input id="material" name="labor" type="radio" value="not" />
                                </div>
                                <div class="form-group hidden" id="materialType">
                                    <label class="font-weight-bold" for="MaterialByOwner">Material By Owner:</label>
                                    <input id="MaterialByOwner" name="material" type="radio" value="owner" />
                                    <label class="font-weight-bold" for="MaterialByVendor">Material By Vendor:</label>
                                    <input id="MaterialByVendor" name="material" type="radio" value="vendor" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="estimatedPrice">Estimated Price</label>
                                    <input type="text" disabled class="form-control" id="estimatedPrice" name="estimatePrice" value="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="contractPrice">Contract Price:</label>
                                    <input type="text" disabled class="form-control" id="contractPrice" name="ContractPrice" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="approvedToPay">Approved To pay</label>
                                    <input type="text" class="form-control" name="approvedToPay" id="approvedToPay-0" value="<?php echo !empty($billInfoDetail['approved_to_pay']) ? $billInfoDetail['approved_to_pay'] : '' ?>">
                                    <!-- onchange="updateTotalAmount(event)"/> -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="invoiceDetail">Invoice No:</label>
                                    <input type="text" class="form-control" id="invoiceDetail" name="invoiceDetail" value="<?php
                                                                                                                            if (!empty($requestInfo['invoice_id'])) {
                                                                                                                                echo $requestInfo['invoice_id'];
                                                                                                                            } else if (!empty($billInfoDetail['invoice_no'])) {
                                                                                                                                echo $billInfoDetail['invoice_no'];
                                                                                                                            } else {
                                                                                                                                echo '';
                                                                                                                            }
                                                                                                                            ?>" />
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="requestID">Request Number
                                        <a href="requestadd?action=rview&rid=<?php if (!empty($billInfoDetail['request_id'])) {
                                                                                    echo $billInfoDetail['request_id'];
                                                                                }
                                                                                if (!empty($_GET['request_id'])) {
                                                                                    echo $_GET['request_id'];
                                                                                } ?>" target="_blank">
                                            : <?php if (!empty($billInfoDetail['request_id'])) {
                                                    echo $billInfoDetail['request_id'];
                                                }
                                                if (!empty($_GET['request_id'])) {
                                                    echo $_GET['request_id'];
                                                } ?>
                                        </a></label>
                                    <input type="hidden" id="requestID" name="requestID" value="<?php if (!empty($billInfoDetail['request_id'])) {
                                                                                                    echo $billInfoDetail['request_id'];
                                                                                                }
                                                                                                if (!empty($_GET['request_id'])) {
                                                                                                    echo $_GET['request_id'];
                                                                                                } ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="refNumber">Reference Number
                                        :
                                        <?php echo !empty($billInfoDetail['ref_no']) ? $billInfoDetail['ref_no'] : '' ?></label>
                                    <input type="hidden" class="form-control" id="refNumber" name="refNumber" value="<?php echo !empty($billInfoDetail['ref_no']) ? $billInfoDetail['ref_no'] : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="border: 3px solid #000">
            <div class="container-fluid">
                <div class="form-group" style="margin-bottom: 25px !important; margin-top: 25px !important;">
                    <input type="file" name="file01" id="file01">
                    <br />

                    <label class="font-weight-bold" for="attachments">Attachments : <?php ?></label>
                    <?php if (!empty($billInfoDetail['file_name'])) { ?>
                        <a target="_blank" href="custom/billing/uploads/<?php echo $billInfoDetail['id'] ?>/<?php echo $billInfoDetail['file_name'] ?>">
                            <?php echo $billInfoDetail['file_name'] ?></a>
                        <br />
                    <?php }
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            echo ("<a href='files/attachments/$file'>$file</a> <br />\n");
                        }
                    } ?>
                </div>
                <h3>Invoice Detail</h3>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="billTable">
                            <thead class="table-info">
                                <tr>
                                    <th scope="col">Account type</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Tax Type</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Tax Amount</th>
                                    <th scope="col">Total amount</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (isset($_GET['bill_id'])) {
                                    if (!empty($billInfoDetail['detail'])) {
                                        foreach ($billInfoDetail['detail'] as $key => $value) { ?>
                                            <tr>
                                                <td width="15%">
                                                    <select class="form-control accountType" id="accountType" name="accountType[]">
                                                        <?php
                                                        if (!empty($vendorTypes)) {
                                                            foreach ($vendorTypes as $keyType => $valueType) { ?>
                                                                <option <?php echo $keyType == $value['accountType'] ? 'selected' : '' ?> value="<?php echo $keyType ?>"><?php echo $valueType ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <select class="form-control building buildingclass" id="building-<?php echo $key ?>" name="building[]">
                                                                    <!-- onchange="updateApartmentInfo(event)" -->
                                                                    <!-- onload="updateApartmentInfo(event)"> -->
                                                                    <?php
                                                                    foreach ($buildings as $keyB => $valueB) { ?>
                                                                        <option <?php echo $valueB['building_id'] == (!empty($value['building_id']) ? $value['building_id'] : '') ? "selected" : '' ?> value="<?php echo $valueB['building_id'] ?>">
                                                                            <?php echo !empty($valueB['building_name']) ? $valueB['building_name'] : '' ?>
                                                                        </option>
                                                                    <?php
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <select class="form-control unit" id="unit-<?php echo $key ?>" name="unit[]">
                                                                    <?php
                                                                    $apartments = $DB_apt->getAptInfoInBuilding($value['building_id']);
                                                                    foreach ($apartments as $keyA => $valueA) {
                                                                    ?>
                                                                        <option <?php echo $valueA['apartment_id'] == (!empty($value['apartment_id']) ? $value['apartment_id'] : '') ? "selected" : '' ?> value="<?php echo $valueA['apartment_id'] ?>">
                                                                            <?php echo !empty($valueA['unit_number']) ? $valueA['unit_number'] : '' ?>
                                                                        </option>
                                                                    <?php
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td width="25%"><input type="text" class="form-control" name="description[]" value="<?php echo $value['description'] ?>">
                                                </td>
                                                <td width="10%"><input type="text" class="form-control taxType" name="taxType[]" id="taxType-<?php echo $key ?>" value="<?php echo $value['taxType'] ?>">
                                                </td>

                                                <td width="10%"><input type="text" class="form-control amount amountclass" id="amount-<?php echo $key ?>" name="amount[]" value="<?php $floatValue = (float)$value['amount'];
                                                                                                                                                                                    echo round($floatValue, 2) ?>">
                                                    <!-- onchange="calculateTax(event)" -->
                                                </td>
                                                <td width="10%"><input type="text" class="form-control taxAmount taxAmountclass" name="taxAmount[]" id="taxAmount-<?php echo $key ?>" value="<?php $floatValue = (float)$value['taxAmount'];
                                                                                                                                                                                                echo round($floatValue, 2) ?>" />
                                                    <!-- onchange="calculateTax(event)" -->

                                                    <input type="hidden" name="tax1" id="tax1" value="<?php echo $tax['GST'] ?>" />
                                                    <input type="hidden" name="tax2" id="tax2" value="<?php echo $tax['QST'] ?>" />
                                                    <input type="hidden" name="tax3" id="tax3" value="<?php echo $tax['HST'] ?>" />
                                                </td>
                                                <td width="10%"><input type="text" class="form-control totalAmount totalAmountclass" name="totalAmount[]" id="totalAmount-<?php echo $key ?>" value="<?php echo $value['totalAmount'] ?>">
                                                    <!-- onchange="calculateReversedTax(event)" -->
                                                </td>
                                            </tr>
                                    <?php }
                                    }
                                } else { ?>
                                    <tr>
                                        <td width="15%">
                                            <select class="form-control accountType" name="accountType[]">
                                                <?php
                                                if (!empty($vendorTypes)) {
                                                    foreach ($vendorTypes as $keyType => $valueType) { ?>
                                                        <option value="<?php echo $keyType ?>"><?php echo $valueType ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <select class="form-control building" id="building-0" name="building[]">
                                                            <!-- onchange="updateApartmentInfo(event)"> -->
                                                            <?php
                                                            foreach ($buildings as $keyB => $valueB) { ?>
                                                                <option <?php echo $valueB['building_id'] == (!empty($buildingID) ? $buildingID : '') ? "selected" : '' ?> value="<?php echo $valueB['building_id'] ?>">
                                                                    <?php echo !empty($valueB['building_name']) ? $valueB['building_name'] : '' ?>
                                                                </option>
                                                            <?php
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <select class="form-control unit" id="unit-0" name="unit[]">
                                                            <?php
                                                            foreach ($apartments as $keyA => $valueA) {
                                                            ?>
                                                                <option <?php echo $valueA['apartment_id'] == (!empty($apartmentID) ? $apartmentID : '') ? "selected" : '' ?> value="<?php echo $valueA['apartment_id'] ?>">
                                                                    <?php echo !empty($valueA['unit_number']) ? $valueA['unit_number'] : '' ?>
                                                                </option>
                                                            <?php
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="25%"><input type="text" class="form-control" id="description" name="description[]">
                                        </td>
                                        <td width="10%"><input type="text" class="form-control taxType" name="taxType[]" value="<?php echo $tax['type'] ?>"></td>
                                        <td width="10%"><input type="text" class="form-control amount" id="amount-0" name="amount[]">
                                            <!-- onchange="calculateTax(event)"> -->
                                        </td>
                                        <td width="10%"><input type="text" class="form-control taxAmount" name="taxAmount[]" id="taxAmount-0">
                                            <!-- onchange="updateTax(event)"> -->

                                            <input type="hidden" name="tax1" id="tax1" value="<?php echo $tax['GST'] ?>" />
                                            <input type="hidden" name="tax2" id="tax2" value="<?php echo $tax['QST'] ?>" />
                                            <input type="hidden" name="tax3" id="tax3" value="<?php echo $tax['HST'] ?>" />
                                        </td>
                                        <td width="10%"><input type="text" class="form-control totalAmount totalAmount-0class" name="totalAmount[]" id="totalAmount-0">
                                            <!-- onchange="calculateReversedTax(event)"/> -->
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table style="margin-bottom: 25px !important">
                            <tbody>
                                <tr>
                                    <td width="15%"></td>
                                    <td></td>
                                    <td width="25%"></td>
                                    <td width="10%"></td>
                                    <td width="10%">
                                        <br />
                                        <br />
                                        <br />
                                        <label>Total Amount</label>
                                        <input type="text" class="form-control" name="grandTotalWithoutTax" id="grandTotalWithoutTax" placeholder="Total amount" />
                                    </td>
                                    <td width="10%"></td>
                                    <td width="10%">
                                        <label>Discount Amount</label>
                                        <input type="text" class="form-control" name="grandTotal" value="0" id="discountAmount">
                                        <label>Grand Total</label>
                                        <input type="text" class="form-control" name="grandTotal" id="grandTotal" value="<?php echo !empty($billInfoDetail['grand_total']) ? $billInfoDetail['grand_total'] : '' ?>" placeholder="Grand Total" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 25px !important;">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3">
                                <button id="addRow" type="button" class="btn btn-success float-left">Add Row</button>
                            </div>
                            <div class="col-md-3">
                                <button id="deleteRow" type="button" class="btn btn-danger float-left">Delete Row
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?Php if (!empty($_GET['bill_id'])) { ?>
                            <input type="hidden" name="bill_id" value="<?php echo $billInfoDetail['id'] ?>">
                            <button type="submit" id="updateBill" name="updateBill" class="btn btn-success float-right pull-right" <?php echo (!empty($_GET['request_id'])) ? 'disabled' : '' ?>>
                                Update
                            </button>
                        <?php } else { ?>
                            <button type="submit" id="submitBill" name="submitBill" class="btn btn-primary float-right pull-right" <?php echo (!empty($_GET['bill_id'])) ? 'disabled' : '' ?>>Save
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Enter your password to change the approved
                        amount</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- fieldsets -->
                    <fieldset>
                        <div class="form-group">
                            <label class="font-weight-bold" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                            <input type="hidden" class="form-control" id="sessionPassword" name="sessionPassword" value="<?php echo $_SESSION['userpass'] ?>">

                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    loadjs.ready("jquery", function() {
        loadjs([
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js',
            "custom/request/js/bootstrap-datetimepicker.min.js",
        ], 'jsloaded');
    });
</script>
<script>
    loadjs.ready(['jsloaded'], function() {
        // alert("loaded");
        $("#vendors").change(function() {
            updateVendorInfo(this);
        });

        $("#project").change(function() {
            getContracts(this);
        });

        $("#contract").change(function() {
            updateContractInfo(this);
        });

        $("#approvedToPay-0").change(function() {
            updateTotalAmount(this);
        });

        $(".buildingclass").load(function() {
            updateApartmentInfo(this);
        });

        $(".buildingclass").change(function() {
            updateApartmentInfo(this);
        });




        $(".amountclass").change(function() {
            calculateTax(this);
        });

        $(".taxAmountclass").change(function() {
            calculateTax(this);
        });

        $(".totalAmountclass").change(function() {
            calculateReversedTax(this);
        });


        $("building-0").change(function() {
            updateApartmentInfo(this);
        });



        $("amount-0").change(function() {
            calculateTax(this);
        });

        $("taxAmount-0").change(function() {
            updateTax(this);
        });

        $(".totalAmount-0class").change(function() {
            calculateReversedTax(this);
        });



        function updateTotalAmount(thisObj) {
            let amount = thisObj.value;
            $('#totalAmount-0').val(amount);
            calculateReversedTax(thisObj);
        }

        function updateContractInfo(thisObj) {
            $.ajax({
                type: "GET",
                url: "custom/billing/bill_data.php",
                dataType: 'json',
                data: {
                    contract_id: thisObj.value
                },
                async: true,
                success: function(json) {
                    console.log(json);
                    $('#estimatedPrice').val(json.contract.vendor_estimated_price);
                    $('#contractPrice').val(json.contract.vendor_contract_price);
                    $('#amountDue').val(json.outstanding);
                    /* $('.accountType').empty();
                    $('.taxType').val(json.tax.type);
                    $.each(json.vendorTypes, function (key, value) {
                        $('.accountType').append('<option value="' + key + '">' + value + '</option>');
                    });*/
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function getContracts(thisObj) {

            $('#estimatedPrice').val('');
            $('#contractPrice').val('');
            $('#amountDue').val('');
            $('#contract').empty();
            $.ajax({
                type: "GET",
                url: "custom/billing/bill_data.php",
                dataType: 'json',
                data: {
                    project_id: thisObj.value
                },
                async: true,
                success: function(json) {
                    if (json.contract) {
                        $('#estimatedPrice').val(json.contract.vendor_estimated_price);
                        $('#contractPrice').val(json.contract.vendor_contract_price);
                        $('#amountDue').val(json.outstanding);
                        $('#vendors').empty();
                    }
                    $.each(json.contracts, function(key, value) {
                        $('#contract').append('<option value="' + value.contract_id + '">' +
                            value.contract_desc + '</option>');
                    });

                    $.each(json.vendors, function(key, value) {
                        if (value.vendor_id === json.contract.vendor_id) {
                            $('#vendors').append('<option selected value="' + value.vendor_id +
                                '">' + value.company_name + '</option>');
                        } else {
                            $('#vendors').append('<option value="' + value.vendor_id + '">' +
                                value.company_name + '</option>');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        var updateVendorInfo = function(thisObj) {
            $.ajax({
                type: "GET",
                url: "custom/billing/bill_data.php",
                dataType: 'json',
                data: {
                    vendor_id: thisObj.value
                },
                async: true,
                success: function(json) {
                    $('#address').val(json.vendor.address);
                    $('.accountType').empty();
                    $('.taxType').val(json.tax.type);
                    $.each(json.vendorTypes, function(key, value) {
                        $('.accountType').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function updateApartmentInfo(thisObj) {
            let id = thisObjt.id;
            let idNumber = id.split('-')[1];
            $.ajax({
                type: "GET",
                url: "custom/billing/bill_data.php",
                dataType: 'json',
                data: {
                    building_id: thisObj.value
                },
                async: true,
                success: function(json) {
                    $('#unit-' + idNumber).empty();
                    $.each(json, function(key, value) {
                        $('#unit-' + idNumber).append('<option value="' + value.apartment_id +
                            '">' + value.unit_number + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function calculateTax(thisObj) {
            let amount = thisObj.value;
            let id = thisObj.id;

            let tax1 = $("#tax1").val();
            let tax2 = $("#tax2").val();
            let tax3 = $("#tax3").val();

            let taxAmount =
                ((amount * tax1) / 100) +
                ((amount * tax2) / 100) +
                ((amount * tax3) / 100);

            let idNumber = id.split('-')[1];
            $('#taxAmount-' + idNumber).val(Math.round(taxAmount * 100) / 100);
            let amountWithTax = +taxAmount + +amount;
            $('#totalAmount-' + idNumber).val(Math.round(amountWithTax * 100) / 100);

            updateGrandTotal();
            updateGrandTotalWithoutTax();

        }

        function calculateReversedTax(thisObj) {
            let amount = thisObj.value;
            let id = thisObj.id;

            let tax1 = $("#tax1").val();
            let tax2 = $("#tax2").val();
            let tax3 = $("#tax3").val();

            let tax = (+tax1 + +tax2 + +tax3) / 100;
            let amountWithoutTax = +amount / (+tax + 1);
            let taxAmount = +amountWithoutTax * +tax;
            let idNumber = id.split('-')[1];

            $('#taxAmount-' + idNumber).val(Math.round(taxAmount * 100) / 100);
            $('#amount-' + idNumber).val(Math.round(amountWithoutTax * 100) / 100);

            updateGrandTotal();
            updateGrandTotalWithoutTax();
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            let totalAmountElements = document.querySelectorAll('.totalAmount');
            for (let i = 0; i < totalAmountElements.length; i++) {
                grandTotal = (+totalAmountElements[i].value + +grandTotal);
            }

            let finalDiscountValue = $('#discountAmount').val();
            let finalAmount = grandTotal - finalDiscountValue;

            $('#grandTotal').val(Math.round(finalAmount * 100) / 100);
            //$('#approvedToPay').val(Math.round(finalAmount * 100) / 100);
        }

        function updateGrandTotalWithoutTax() {
            let grandTotalWithoutTax = 0;
            let grandTotalWithoutTaxElements = document.querySelectorAll('.amount');
            for (let i = 0; i < grandTotalWithoutTaxElements.length; i++) {
                grandTotalWithoutTax = (+grandTotalWithoutTaxElements[i].value + +grandTotalWithoutTax);
            }
            $('#grandTotalWithoutTax').val(Math.round(grandTotalWithoutTax * 100) / 100);
        }

        function updateTax(thisObj) {
            let id = thisObj.id;
            let idNumber = id.split('-')[1];
            let totalAmount = $('#amount-' + idNumber).val() - thisObj.value;
            $('#totalAmount-' + idNumber).val(Math.round(totalAmount * 100) / 100);
            updateGrandTotal();
        }

        $(document).ready(function() {
            updateGrandTotalWithoutTax();

            $('#date').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#billDate').datetimepicker({
                format: 'DD-MM-YYYY'
            });

            $("#submitBill").click(function(event) {
                let grandTotal = $("#grandTotal");
                let amountDue = $("#amountDue");
                if (!$(".amount").val()) {
                    event.preventDefault();
                    alert("Amount in detail is empty.");
                }
                /* if (grandTotal.val() !== amountDue.val()) {
                     event.preventDefault();
                     alert("Grand total amount and amount due are not equal.");
                     if (!amountDue.val()) {
                         amountDue.val(grandTotal.val());
                     }
                 }*/
            });

            $("#updateBill").click(function(event) {
                let grandTotal = $("#grandTotal");
                let amountDue = $("#amountDue");
                if (!$(".amount").val()) {
                    event.preventDefault();
                    alert("Amount in detail is empty.");
                }
                /*  if (grandTotal.val() !== amountDue.val()) {
                      event.preventDefault();
                      alert("Grand total amount and amount due are not equal.");
                      if (!amountDue.val()) {
                          amountDue.val(grandTotal.val());
                      }
                  }*/
            });

            $("#addRow").click(function() {
                $("#billTable").each(function() {
                    let tds = '<tr id=rowCount>';
                    jQuery.each($('tr:last td', this), function() {
                        tds += '<td>' + $(this).html() + '</td>';
                    });
                    tds += '</tr>';
                    if ($('tbody', this).length > 0) {
                        $('tbody', this).append(tds);
                    } else {
                        $(this).append(tds);
                    }
                });

                setIDs('taxAmount');
                setIDs('amount');
                setIDs('totalAmount');
                setIDs('building');
                setIDs('unit');
            });

            function setIDs(element) {
                let elementName = document.querySelectorAll('.' + element);

                for (let i = 0; i < elementName.length; i++) {
                    if (i === elementName.length - 1) {
                        elementName[i].value = '';
                    }
                    elementName[i].id = element + '-' + i;
                }
            }

            $('#deleteRow').on("click", function() {
                let rowCount = $('#billTable tr').length;
                if (rowCount > 2) {
                    $('#billTable tr:last').remove();
                    updateGrandTotalWithoutTax();
                    updateGrandTotal();
                }
            });

            $('#discountAmount').on("change", function() {
                $('#discountPercentage').val(0);
                if ($('#grandTotal').val()) {
                    updateGrandTotal();
                }
            });

            $('#approvedToPay').one("focus", function() {
                $('#passwordModal').modal('show');
            });


            $('#passwordModal').on('hidden.bs.modal', function() {
                if ($('#password').val() !== $('#sessionPassword').val()) {
                    $("#approvedToPay").prop('disabled', true);
                }
            });

            $('#discountPercentage').on("change", function() {
                let grandTotalValue = $('#grandTotal').val();
                let discountPercentage = $('#discountPercentage').val();
                if (discountPercentage !== null && discountPercentage !== 0) {
                    let discountValue = (grandTotalValue * discountPercentage) / 100;
                    $('#discountAmount').val(discountValue);
                }
                if (grandTotalValue) {
                    updateGrandTotal();
                }
            });

            $('#material').on("change", function() {
                let materialType = $('#materialType');
                if (materialType.hasClass('hidden')) {
                    materialType.removeClass('hidden')
                }
            });

            $('#labor').on("change", function() {
                let materialType = $('#materialType');
                if (!materialType.hasClass('hidden')) {
                    materialType.addClass('hidden')
                }
            });

            $('#vendors').select2();
            $('#contract').select2();
            $('#project').select2();
        });

    }); //loadjs
</script>