<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bill</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
        integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css"
        rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
</head>

<body>
    <?php
    session_start();
    /**
     * Created by PhpStorm.
     * User: Beaver2
     * Date: 2018-05-15
     * Time: 13:56
     */
    error_reporting(0);
    @ini_set('display_errors', 0);

    if (strpos(getcwd(), "custom") == false) {
        $path = "../pdo/";
    } else {
        $path = "../../pdo/";
    }
    $file = $path . 'dbconfig.php';
    include_once($file);

    $paymentDetails = $DB_vendor->getPaymentDetails($_GET['request_id']);
    $buildings = $DB_building->getAllBdRowsByEmployee($_SESSION['employee_id']);
    $vendors = $DB_vendor->getVendorsList();
    $requestInfo = $DB_request->get_request_info($paymentDetails['request_id']);

    $buildingID = $requestInfo['building_id'];

    foreach ($vendors as $key => $value) {
        if ($paymentDetails['vendor_id'] == $value['vendor_id']) {
            $province_id = $value['province_id'];

            $SelectSql = "select * from provinces where id=$province_id";
            $statement = $DB_con->prepare($SelectSql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $tax['province'] = $result['name'];
            $tax['GST'] = $result['tax1'];
            $tax['QST'] = $result['tax2'];
            $tax['HST'] = $result['tax3'];

            $totalAmount = $paymentDetails['approval_amount'];
            $taxes = ($result['tax1'] + $result['tax2'] + $result['tax3']);
            $amount = ($totalAmount * 100) / ($taxes + 100);
            $taxAmount = $amount * ($taxes / 100);

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
                $id = $value['account_type' . $i];
                $SelectSql = "select * from account_types where id=$id";
                $statement = $DB_con->prepare($SelectSql);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $vendorTypes[$i] = $result['name'];
            }
        }
    }

    if ($_GET['bill_id'] != null) {
        $billId = $_GET['bill_id'];
        try {
            $SelectSql = "select * from bills where id= $billId";
            $statement = $DB_con->prepare($SelectSql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $billInfoDetail = $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        try {
            $SelectSql = "select * from bills_details where bill_id=$billId";
            $statement = $DB_con->prepare($SelectSql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $billDetails = $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    ?>
    <div class="container-fluid">
        <form action="billList.php" method="post" id="billForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="vendors">Vendor</label>
                                <select disabled class="form-control" id="vendors" name="vendor"
                                    onchange="updateVendorInfo(event)">
                                    <?php
                                    foreach ($vendors as $key => $value) { ?>
                                    <option
                                        <?php echo $value['vendor_id'] == $paymentDetails['vendor_id'] || $value['vendor_id'] == $billInfoDetail['vendor_id'] ? "selected" : "" ?>
                                        value="<?php echo $value['address'] ?>"><?php echo $value['full_name'] ?>
                                    </option>
                                    <?php }
                                    ?>
                                </select>
                                <input type="hidden" name="vendor_id"
                                    value="<?php echo $paymentDetails['vendor_id'] ?>" />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold" for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="5">  <?php foreach ($vendors as $key => $value) {
                                                                                                            if ($value['vendor_id'] == $paymentDetails['vendor_id']) {
                                                                                                                echo $value['address'];
                                                                                                            }
                                                                                                        } ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="<?php echo $_GET['bill_id'] !== null ? $billInfoDetail['due_date'] : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="amountDue">Amount due</label>
                                <input type="text" class="form-control" id="amountDue" name="amountDue"
                                    value="<?php echo $_GET['bill_id'] !== null ? $billInfoDetail['grand_total'] : $paymentDetails['approval_amount'] ?>">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="billDate">Bill due</label>
                                <input type="date" class="form-control" id="billDate" name="billDate" <input type="date"
                                    class="form-control" id="date" name="date"
                                    value="<?php echo $_GET['bill_id'] !== null ? $billInfoDetail['bill_date'] : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="memo">Memo</label>
                                <input type="text" class="form-control" id="memo" name="memo"
                                    value="<?php echo $_GET['bill_id'] !== null ? $billInfoDetail['memo'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="billTable">
                        <thead class="table-info">
                            <tr>
                                <th scope="col">Account type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Tax Type</th>
                                <th scope="col">Tax Amount</th>
                                <th scope="col">Description</th>
                                <th scope="col">Tenant</th>
                                <th scope="col">Total amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="15%">
                                    <select class="form-control" id="accountType" name="accountType[]">
                                        <?php foreach ($vendorTypes as $keyType => $valueType) { ?>
                                        <option value="<?php echo $keyType ?>"><?php echo $valueType ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td width="10%"><input type="text" class="form-control amount" id="amount-0"
                                        name="amount[]" value="<?php echo $amount ?>" onchange="calculateTax(event)">
                                </td>
                                <td width="10%"><input disabled type="text" class="form-control taxType"
                                        name="taxType[]" value="<?php echo $tax['type'] ?>">
                                    <input type="hidden" name="taxType[]" value="<?php echo $tax['type'] ?>" />
                                </td>

                                <td width="10%"><input type="text" class="form-control taxAmount" name="taxAmount[]"
                                        value="<?php echo $taxAmount ?>" id="taxAmount-0">

                                    <input type="hidden" name="tax1" id="tax1" value="<?php echo $tax['GST'] ?>" />
                                    <input type="hidden" name="tax2" id="tax2" value="<?php echo $tax['QST'] ?>" />
                                    <input type="hidden" name="tax3" id="tax3" value="<?php echo $tax['HST'] ?>" />
                                </td>
                                <td width="30%"><input type="text" class="form-control" id="description"
                                        name="description[]" value="<?php echo $paymentDetails['approval_comment'] ?>">
                                </td>
                                <td width="15%">
                                    <select class="form-control building" id="building-0" name="building[]"
                                        onchange="updateApartmentInfo(event)">
                                        <?php
                                        foreach ($buildings as $key => $value) { ?>
                                        <option <?php echo $value['building_id'] == $buildingID ? "selected" : '' ?>
                                            value="<?php echo $value['building_id'] ?>">
                                            <?php echo $value['building_name'] ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                    <br />
                                    <select class="form-control unit" id="unit-0" name="unit[]">

                                    </select>
                                </td>
                                <td width="10%"><input type="text" class="form-control totalAmount" name="totalAmount[]"
                                        id="totalAmount-0" value="<?php echo $totalAmount ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="grandTotal"
                                    value="<?php echo $totalAmount ?>" id="grandTotal">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
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
                    <?Php if ($_GET['request_id'] != null) { ?>
                    <button type="submit" name="submitBill" id="submitBill" class="btn btn-primary float-right"
                        <?php echo $_GET['bill_id'] != null ? 'disabled' : '' ?>>Save
                    </button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
    <script>
    function updateVendorInfo(e) {
        $("#address").val(e.target.value);
    }

    function updateApartmentInfo(e) {
        $.ajax({
            type: "GET",
            url: "bill_data.php",
            dataType: 'json',
            data: {
                building_id: e.target.value
            },
            async: true,
            success: function(json) {
                $('#unit-' + e.target.id.split('-')[1]).empty();
                $.each(json, function(key, value) {
                    $('#unit-' + e.target.id.split('-')[1]).append('<option value="' + value
                        .apartment_id + '">' + value.unit_number + '</option>');
                });
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function calculateTax(e) {
        var amount = e.target.value;
        var id = e.target.id;


        var tax1 = $("#tax1").val();
        var tax2 = $("#tax2").val();
        var tax3 = $("#tax3").val();

        var taxAmount =
            ((amount * tax1) / 100) +
            ((amount * tax2) / 100) +
            ((amount * tax3) / 100);


        var idNumber = id.split('-')[1];

        $('#taxAmount-' + idNumber).val(taxAmount);
        $('#totalAmount-' + idNumber).val(+taxAmount + +amount);

        var grandTotal = 0;
        var totalAmountElements = document.querySelectorAll('.totalAmount');
        for (var i = 0; i < totalAmountElements.length; i++) {
            grandTotal = (+totalAmountElements[i].value + +grandTotal);
        }
        $('#grandTotal').val(grandTotal);
    }
    $(document).ready(function() {
        $("#submitBill").click(function(event) {
            if ($("#grandTotal").val() !== $("#amountDue").val()) {
                event.preventDefault();
                alert("Grand total amount and amount due are not equal");
            }
        });

        $("#addRow").click(function() {
            $("#billTable").each(function() {
                var tds = '<tr id=rowCount>';
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

            var taxAmountElements = document.querySelectorAll('.taxAmount');
            var amountElements = document.querySelectorAll('.amount');
            var totalAmountElements = document.querySelectorAll('.totalAmount');
            var buildingElements = document.querySelectorAll('.building');
            var unitElements = document.querySelectorAll('.unit');

            // Set their ids
            for (var i = 0; i < buildingElements.length; i++) {
                if (i === buildingElements.length - 1) {
                    buildingElements[i].value = '';
                }
                buildingElements[i].id = 'building-' + i;
            }
            for (var i = 0; i < unitElements.length; i++) {
                if (i === unitElements.length - 1) {
                    unitElements[i].value = '';
                }
                unitElements[i].id = 'unit-' + i;
            }

            for (var i = 0; i < taxAmountElements.length; i++) {
                if (i === taxAmountElements.length - 1) {
                    taxAmountElements[i].value = '';
                }
                taxAmountElements[i].id = 'taxAmount-' + i;
            }

            for (var i = 0; i < amountElements.length; i++) {
                if (i === amountElements.length - 1) {
                    amountElements[i].value = '';
                }
                amountElements[i].id = 'amount-' + i;
            }

            for (var i = 0; i < totalAmountElements.length; i++) {
                if (i === totalAmountElements.length - 1) {
                    totalAmountElements[i].value = '';
                }
                totalAmountElements[i].id = 'totalAmount-' + i;
            }
        });

        $('#deleteRow').on("click", function() {
            var rowCount = $('#billTable tr').length;
            if (rowCount > 2) {
                $('#billTable tr:last').remove();
                var grandTotal = 0;
                var totalAmountElements = document.querySelectorAll('.totalAmount');
                for (var i = 0; i < totalAmountElements.length; i++) {
                    grandTotal = (+totalAmountElements[i].value + +grandTotal);
                }
                $('#grandTotal').val(grandTotal);
            }
        });
    });
    </script>