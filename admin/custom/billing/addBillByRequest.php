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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<?php
$paymentDetails = $DB_vendor->getPaymentDetails($_GET['request_id']);

if (!empty($_SESSION['admin_id'])) {
    $buildings = $DB_building->getAllBdRowsByCompany($_SESSION['company_id']);
} else if (!empty($_SESSION['building_ids'])) {
    $buildings = $DB_building->getBuildingsByIds($_SESSION['building_ids']);
} else {
    $buildings = $DB_building->getAllBdRowsByEmployee($_SESSION['employee_id']);
}

$vendors = $DB_vendor->getVendorsList();
$requestInfo = $DB_request->get_request_info($_GET['request_id']);

$buildingID = $requestInfo['building_id'];
$apartmentID = $requestInfo['apartment_id'];
$apartments = $DB_apt->getAptInfoInBuilding($buildingID);
$unitNumber = array();
foreach ($apartments as $key => $row) {
    $unitNumber[$key] = $row['unit_number'];
}
array_multisort($unitNumber, SORT_ASC, $apartments);

foreach ($vendors as $key => $value) {
    if ($requestInfo['vendor_id'] == $value['vendor_id']) {
        $province_id = $value['province_id'];
        $result = $DB_province->getProvinceInfo($province_id);

        $tax['province'] = $result['name'];
        $tax['GST'] = $result['tax1'];
        $tax['QST'] = $result['tax2'];
        $tax['HST'] = $result['tax3'];

        $totalAmount = $requestInfo['invoice_amount'];
        $taxes = ($result['tax1'] + $result['tax2'] + $result['tax3']);
        $amount = ((float)$totalAmount * 100) / ((float)$taxes + 100);
        $taxAmount = $amount * ($taxes / 100);

        if (!empty($result['tax1']) != 0 && $result['tax1'] != null) {
            $tax['type'] = 'GST';
        }
        if ($result['tax2'] != 0 && $result['tax1'] != null) {
            $tax['type'] = $tax['type'] . ',Qst';
        }
        if ($result['tax3'] != 0 && $result['tax1'] != null) {
            $tax['type'] = $tax['type'] . ',HST';
        }

        for ($i = 1; $i <= 3; $i++) {
            $id = $value['account_type' . $i];
            if (!empty($id)) {
                $result = $DB_bill->getAccountTypeByID($id);
                $vendorTypes[$i] = $result['name'];
            }
        }
    }
}

if (!empty($requestInfo['invoices_attached'])) {
    $attachments = json_decode($requestInfo['invoices_attached']);
    $files = array();
    $dir = opendir('files');
    foreach ($attachments as $key => $value) {
        while (false != ($file = readdir($dir))) {
            if (($file == $value)) {
                $files[] = $file;
            }
        }
    }
    natsort($files);
}

if (!empty($requestInfo['project_id'])) {
    $projectInfo = $DB_request->getProjectInfo($requestInfo['project_id']);
}
if (!empty($requestInfo['contract_id'])) {
    $contractInfo = $DB_request->getContractDataByContractId($requestInfo['contract_id']);
    $outstanding = $contractInfo['vendor_contract_price'];
    $billsByContract = $DB_bill->getBillsByContractID($requestInfo['contract_id']);
    $sum = 0;
    foreach ($billsByContract as $key => $value) {
        if ($value['material_by_owner'] != 1) {
            $sum = $value['grand_total'] + $sum;
        }
    }

    if (!empty($contractInfo['vendor_contract_price'])) {
        $outstanding = $contractInfo['vendor_contract_price'] - $sum;
    }
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
                                    <select <?php echo !empty($requestInfo['vendor_id']) ? 'disabled' : '' ?> class="form-control" id="vendors" name="vendor">
                                        <?php
                                        if (!empty($vendors)) {
                                            foreach ($vendors as $key => $value) { ?>
                                                <option <?php echo $value['vendor_id'] == $requestInfo['vendor_id'] ? 'selected' : '' ?> value="<?php echo $value['vendor_id'] ?>">
                                                    <?php echo $value['company_name'] ?></option>
                                        <?php }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="vendor_id" value="<?php echo $requestInfo['vendor_id'] ?>" />
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="5">  <?php foreach ($vendors as $key => $value) {
                                                                                                                if ($value['vendor_id'] == $requestInfo['vendor_id']) {
                                                                                                                    echo $value['address'];
                                                                                                                }
                                                                                                            } ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold" for="date">Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='date'>
                                        <input type='text' name="date" class="form-control" value="<?php echo date('d-m-y') ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold" for="amountDue">Total Balance / Contract </label>
                                    <input type="text" class="form-control" id="amountDue" name="amountDue" value="<?php echo $outstanding; ?>" readonly="true" />
                                </div>
                                <label class="font-weight-bold" for="billDate">Bill Due</label>
                                <div class="form-group">
                                    <div class='input-group date' id='billDate'>
                                        <input type='text' name="billDate" class="form-control" value="<?php echo date('d-m-y') ?>" />
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
                                    <input type="text" class="form-control" id="memo" name="memo" value="">
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
                                    <input disabled type="text" class="form-control" id="project" name="project" value="<?php echo !empty($projectInfo['name']) ? $projectInfo['name'] : '' ?>">
                                    <input type="hidden" name="projectID" value="<?php echo !empty($requestInfo['project_id']) ? $requestInfo['project_id'] : '' ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="contract">Contract
                                        : </label>
                                    <input disabled type="text" class="form-control" id="contract" name="contract" value="<?php echo !empty($contractInfo['contract_desc']) ? $contractInfo['contract_desc'] : '' ?>">
                                    <input type="hidden" name="contractID" value="<?php echo !empty($requestInfo['contract_id']) ? $requestInfo['contract_id'] : '' ?>" />
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
                                    <input type="text" disabled class="form-control" id="estimatedPrice" name="estimatePrice" value="<?php echo !empty($contractInfo['vendor_estimated_price']) ? $contractInfo['vendor_estimated_price'] : '' ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="contractPrice">Contract Price:</label>
                                    <input type="text" disabled class="form-control" id="contractPrice" name="contractPrice" value="<?php echo !empty($contractInfo['vendor_contract_price']) ? $contractInfo['vendor_contract_price'] : '' ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="approvedToPay">Approved To pay</label>
                                    <input type="text" class="form-control" name="approvedToPay" id="approvedToPay-0" value="" onchange="updateTotalAmount(event)" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="invoiceDetail">Invoice No:</label>
                                    <input type="text" class="form-control" id="invoiceDetail" name="invoiceDetail" value="<?php echo !empty($requestInfo['invoice_id']) ? $requestInfo['invoice_id'] : '' ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="requestID">Request Number
                                        : <a href="requestadd?action=rview&rid=<?php echo $_GET['request_id'] ?>" target="_blank"> <?php echo $_GET['request_id'] ?> </a></label>
                                    <input type="hidden" id="requestID" name="requestID" value="<?php echo $_GET['request_id'] ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold" for="refNumber">Reference Number :</label>
                                    <input type="hidden" class="form-control" id="refNumber" name="refNumber" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="border: 3px solid #000">
            <div class="container-fluid">
                <div class="form-group" style="margin-bottom: 25px !important; margin-top:25px !important;">
                    <input type="file" name="file01" id="file01">
                    <br />
                    <label class="font-weight-bold" for="attachments">Attachments : <?php ?></label>
                    <?php
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            echo ("<a target='_blank' href='files/attachments/$file'>$file</a> <br />\n");
                        }
                    }
                    ?>
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
                                <tr>
                                    <td width="15%">
                                        <select class="form-control" name="accountType[]">
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
                                                    <select class="form-control building" id="building-0" name="building[]" onchange="updateApartmentInfo(event)">
                                                        <?php
                                                        foreach ($buildings as $key => $value) { ?>
                                                            <option <?php echo $value['building_id'] == $buildingID ? "selected" : '' ?> value="<?php echo $value['building_id'] ?>">
                                                                <?php echo $value['building_name'] ?></option>
                                                        <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control unit" id="unit-0" name="unit[]">
                                                        <?php
                                                        foreach ($apartments as $key => $value) {
                                                        ?>
                                                            <option <?php echo $value['apartment_id'] == $apartmentID ? "selected" : '' ?> value="<?php echo !empty($value['apartment_id']) ? $value['apartment_id'] : '' ?>">
                                                                <?php echo !empty($value['unit_number']) ? $value['unit_number'] : '' ?>
                                                            </option>
                                                        <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                    </td>
                                    <td width="25%"><input type="text" class="form-control" name="description[]" value="<?php echo $paymentDetails['approval_comment'] ?>">
                                    </td>
                                    <td width="10%"><input disabled type="text" class="form-control taxType" name="taxType[]" value="<?php echo !empty($tax['type']) ? $tax['type'] : '' ?>">
                                        <input type="hidden" name="taxType[]" value="<?php echo $tax['type'] ?>" />
                                    </td>
                                    <td width="10%"><input type="text" class="form-control amount" id="amount-0" name="amount[]" value="<?php
                                                                                                                                        if (!empty($amount)) {
                                                                                                                                            $floatValue = (float)$amount;
                                                                                                                                            echo round($floatValue, 2);
                                                                                                                                        } ?>" onchange="calculateTax(event)">
                                    </td>
                                    <td width="10%"><input type="text" class="form-control taxAmount" name="taxAmount[]" value="<?php
                                                                                                                                if (!empty($taxAmount)) {
                                                                                                                                    $floatValue = (float)$taxAmount;
                                                                                                                                    echo round($floatValue, 2);
                                                                                                                                } ?>" id="taxAmount-0" onchange="updateTax(event)">

                                        <input type="hidden" name="tax1" id="tax1" value="<?php echo $tax['GST'] ?>" />
                                        <input type="hidden" name="tax2" id="tax2" value="<?php echo $tax['QST'] ?>" />
                                        <input type="hidden" name="tax3" id="tax3" value="<?php echo $tax['HST'] ?>" />
                                    </td>
                                    <td width="10%"><input type="text" class="form-control totalAmount" name="totalAmount[]" id="totalAmount-0" value="<?php if (!empty($totalAmount)) {
                                                                                                                                                            echo $totalAmount;
                                                                                                                                                        } ?>" onchange="calculateReversedTax(event)" />
                                    </td>
                                </tr>
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
                                        <input type="text" class="form-control" name="grandTotalWithoutTax" id="grandTotalWithoutTax" placeholder="Total amount" value="<?php if (!empty($amount)) {
                                                                                                                                                                            $floatValue = (float)$amount;
                                                                                                                                                                            echo !empty($_GET['request_id']) ? round($floatValue, 2) : '';
                                                                                                                                                                        } ?>" />
                                    </td>
                                    <td width="10%"></td>
                                    <td width="10%">
                                        <label>Discount Amount</label>
                                        <input type="text" class="form-control" name="grandTotal" value="0" id="discountAmount">
                                        <label>Grand Total</label>
                                        <input type="text" class="form-control" name="grandTotal" value="<?php if (!empty($totalAmount)) {
                                                                                                                $floatValue = (float)$totalAmount;
                                                                                                                echo round($floatValue, 2);
                                                                                                            } ?>" id="grandTotal" placeholder="Grand Total">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" style="margin-bottom: 25px !important;">
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
                        <?Php if (!empty($_GET['request_id'])) { ?>
                            <button type="submit" name="submitBill" id="submitBill" class="btn btn-primary float-right pull-right" <?php echo !empty($_GET['bill_id']) ? 'disabled' : '' ?>>Save
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function updateTotalAmount(e) {
        let amount = e.target.value;
        $('#totalAmount-0').val(amount);
        calculateReversedTax(e);
    }

    function updateApartmentInfo(e) {
        let id = e.target.id;
        let idNumber = id.split('-')[1];
        $.ajax({
            type: "GET",
            url: "custom/billing/bill_data.php",
            dataType: 'json',
            data: {
                building_id: e.target.value
            },
            async: true,
            success: function(json) {
                $('#unit-' + idNumber).empty();
                $.each(json, function(key, value) {
                    $('#unit-' + idNumber).append('<option value="' + value.apartment_id + '">' + value
                        .unit_number + '</option>');
                });
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function calculateTax(e) {
        let amount = e.target.value;
        let id = e.target.id;


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
        updateGrandTotalWithoutTax()
    }

    function calculateReversedTax(e) {
        let amount = e.target.value;
        let id = e.target.id;

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
        updateGrandTotalWithoutTax()
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
        // $('#approvedToPay').val(Math.round(finalAmount * 100) / 100);
    }

    function updateGrandTotalWithoutTax() {
        let grandTotalWithoutTax = 0;
        let grandTotalWithoutTaxElements = document.querySelectorAll('.amount');
        for (let i = 0; i < grandTotalWithoutTaxElements.length; i++) {
            grandTotalWithoutTax = (+grandTotalWithoutTaxElements[i].value + +grandTotalWithoutTax);
        }
        $('#grandTotalWithoutTax').val(Math.round(grandTotalWithoutTax * 100) / 100);
    }

    function updateTax(e) {
        let id = e.target.id;
        let idNumber = id.split('-')[1];
        let totalAmount = $('#amount-' + idNumber).val() - e.target.value;
        $('#totalAmount-' + idNumber).val(Math.round(totalAmount * 100) / 100);
        updateGrandTotal();
    }

    $(document).ready(function() {
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

        $('#vendors').select2();

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
    });
</script>