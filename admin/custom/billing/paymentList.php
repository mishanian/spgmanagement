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

/* Custom scrollbar for the messages select in pdf generate */
/* width */
::-webkit-scrollbar {
    width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
<link rel="stylesheet" type="text/css" media="screen"
    href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<?php

$payments = $DB_bill->getAllPayments();
$paymentMethods = $DB_bill->getPaymentMethods();
$accountTypes = $DB_bill->getAccountTypes();
$vendors = $DB_vendor->getVendorsList();
if (!empty($_SESSION['company_id'])) {
    $contracts = $DB_request->getAllContractsByCompanyId($_SESSION['company_id']);
}
if (!empty($_SESSION['company_id'])) {
    $projects = $DB_request->getAllProjectsByCompanyId($_SESSION['company_id']);
}
?>
<input type="hidden" value="<?php echo $_SESSION['employee_id']; ?>" id="sessionEmployeeId">

<div class="container-fluid" style="overflow: auto;margin-top:10px;">
    <div id="filter-part-current" class="col-sm-12 col-md-12 well" style="padding: 15px 0;">
        <div class="row">
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="vendor"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Project"); ?></label>
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
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Contract"); ?></label>
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
                    <label for="invoiceNo"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Invoice No"); ?>
                    </label>
                    <div class="col-sm-8 col-md-9">
                        <input type="text" class="form-control" id="invoiceNo" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="vendor"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Payment Methods"); ?>
                    </label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="paymentMethod">
                            <option></option>
                            <?php foreach ($paymentMethods as $pKey => $pValue) {
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
                    <label for="accountType"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Account Type"); ?>
                    </label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="accountType">
                            <option></option>
                            <?php foreach ($accountTypes as $cKey => $cValue) {
                            ?>
                            <option value="<?php echo $cValue['name']; ?>"> <?php echo $cValue['name']; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="checkNo"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Check Number"); ?>
                    </label>
                    <div class="col-sm-8 col-md-9">
                        <input type="text" class="form-control" id="checkNo" value="">
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-md-3 filter-block">
                <div class="form-group">
                    <label for="paymentDate"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Payment Date"); ?></label>
                    <div class="col-sm-8 col-md-9">
                        <input type="text" class="form-control date_input" id="paymentDate" style="min-width: 0;"
                            placeholder="YYYY-MM-DD" value="">
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-sm-4 col-md-3 filter-block">
            </div>
            <div class="col-sm-4 col-md-3 filter-block">
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

    <div class="row">
        <div class="col-md-12">
            <table id="paymentTable" class="table table-striped table-bordered table-responsive">
                <thead class="table-info">
                    <tr>
                        <th scope="col">Row</th>
                        <th scope="col">Project</th>
                        <th scope="col">Contract</th>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Payment No</th>
                        <th scope="col">Method</th>
                        <th scope="col">Check Number</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Account Type</th>
                        <th scope="col">Account Sub Type</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Memo</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Registered By</th>
                        <th scope="col">Print Status</th>
                        <th scope="col">Print</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (!empty($payments)) {
                        foreach ($payments as $key => $value) {
                            $billInfo = $DB_bill->getBillByID($value['bill_id']);
                    ?>
                    <tr>
                        <td class="billCheckBox"><?php echo $key + 1 ?></td>
                        <td class="billCheckBox"><?php $projectInfo = $DB_request->getProjectInfo($billInfo['project_id']);
                                                            echo $projectInfo['name'] ?></td>
                        <td class="billCheckBox"><?php $contractInfo = $DB_request->getContractDataByContractId($billInfo['contract_id']);
                                                            echo $contractInfo['contract_desc']; ?></td>
                        <td><?php echo $billInfo['invoice_no'] ?></td>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php foreach ($paymentMethods as $pKey => $pValue) {
                                        if ($pValue['id'] == $value['method']) {
                                            echo $pValue['name'];
                                        }
                                    } ?></td>
                        <td><?php echo $value['check_no']; ?></td>
                        <td><?php echo $value['amount']; ?></td>
                        <td><?php foreach ($accountTypes as $tKey => $tValue) {
                                        if ($tValue['id'] == $value['account_type']) {
                                            echo $tValue['name'];
                                        }
                                    } ?></td>
                        <td><?php $accountSubTypes = $DB_bill->getAccountSubTypesByAccountTypeID($value['account_type']);
                                    foreach ($accountSubTypes as $sKey => $sValue) {
                                        if ($sValue['id'] == $value['account_sub_type']) {
                                            echo $sValue['name'];
                                        }
                                    }
                                    ?></td>
                        <td><?php echo date("d-m-Y", strtotime($value['payment_date'])); ?></td>
                        <td><?php echo $value['memo']; ?></td>
                        <td> <?php
                                        foreach ($vendors as $vKey => $vValue) {
                                            if ($vValue['vendor_id'] == $value['vendor_id']) {
                                                echo $vValue['company_name'];
                                            }
                                        }
                                        ?></td>
                        <td><?php echo $DB_employee->getEmployeeName($value['employee_id']); ?></td>
                        <td><?php
                                    $printStatus = "";
                                    $printStatusCode = 0;
                                    if (intval($value['is_signed']) == 0) {
                                        $printStatus = "Not signed";
                                        $printStatusCode = 1;
                                    }
                                    if (intval($value['is_signed']) == 1) {
                                        $printStatus = "Signed. Ready for print";
                                        $printStatusCode = 2;
                                    }
                                    if (intval($value['is_printed']) == 1) {
                                        $printStatus = "Printed";
                                        $printStatusCode = 3;
                                    }
                                    ?>
                            <span class="<?php echo "printstatus_" . $value['id'] ?>"
                                data-code="<?php echo $printStatusCode; ?>"> <?php echo $printStatus; ?></span>
                        </td>
                        <td>
                            <?php if ($value['is_signed'] == 0) { ?>
                            <input class="printButtonUnsigned btn btn-primary" value="Print" type="button"
                                id="printButton" name="<?php echo $value['id'] ?>" />
                            <?php } else { ?>
                            <!-- <form action="custom/billing/paymentPrint.php" method="post" target="_blank"> -->
                            <!-- action="custom/billing/printPaymentPdf.php"                                    -->
                            <form method="post" target="_blank">
                                <input type="hidden" name="id" value="<?php echo $value['id'] ?>" />
                                <input type="hidden" name="billId" value="<?php echo $value['bill_id'] ?>" />
                                <input data-pid="<?php echo $value['id'] ?>" type="button" value="Print"
                                    class="btn btn-primary printSignedPwd" />
                            </form>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentprintpasswordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Enter your password to print the Bill</h4>
            </div>
            <div class="modal-body">
                <!-- fieldsets -->
                <fieldset>
                    <div class="form-group">
                        <label class="font-weight-bold" for="password">Password</label>
                        <input type="password" class="form-control" id="paymentprintpasswordvalue" name="password" />
                        <input type="hidden" class="form-control" id="sessionPassword" name="sessionPassword"
                            value="<?php echo $_SESSION['userpass'] ?>">
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" id="paymentprintpasswordsubmit" class="btn btn-primary">Print</button>
            </div>
        </div>
    </div>
</div>

<div id="paymentPrintEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Recipient's Email Address </h4>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-group" id="paymentPrintEmailValue"
                            placeholder="Enter the email address of the recipient" />
                    </div>
                </div>

                <div class="row form-group" id="paymentPrintEmailAlert" style="display: none;">
                    <div class="col-md-12">
                        <span class="alert"> Recipient has been notified by email.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="paymentPrintEmailSend">Send</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<form id="printNavigationForm" action="custom/billing/printPaymentPdf.php" method="post" target="_blank"
    style="display: none;">
    <input type="hidden" id="printNavigationForm_pid" name="id" value="" />
    <input type="submit" />
</form>

<script>
$(document).ready(function() {

    /* change the print status */
    function changePrintStatus(next_status, pid) {
        switch (parseInt(next_status)) {
            case 1:
                $(".printstatus_" + pid).html("Email sent for signature.");
                break;
            case 2:
                break;
            case 3:
                break;
        }
    }

    /* Password modal submit -  navigate to print pdf page if correct password */
    $("#paymentprintpasswordsubmit").on("click", function(e) {
        pid = $(this).attr("data-pid");
        password_entered = $("#paymentprintpasswordvalue").val();
        session_password = $("#sessionPassword").val();

        if (password_entered.length < 1 || session_password.length < 1) {
            return;
        }

        /* Passwords match - navigate to the print page */
        if (password_entered.trim() == session_password.trim()) {
            $("#printNavigationForm").find("#printNavigationForm_pid").val(pid);
            $("#printNavigationForm").submit();
            $("#paymentprintpasswordModal").modal("hide");
            $(".printstatus_" + pid).html("Printed");
        } else {
            /* Show wrong password alert */
        }
    });

    /* CLick print button after the signature is done - ask for password of the user */
    $(".printSignedPwd").on("click", function(e) {
        pid = $(this).attr("data-pid");
        /* Fetch the pid of the clicked payment */

        $("#paymentprintpasswordsubmit").attr("data-pid", pid);
        /* Set pid value to the Password modal submit button */
        $("#paymentprintpasswordModal").modal("show");
        /* SHow the modal */
    });

    /* Send button clicked in the modal for the print payment email */
    $("#paymentPrintEmailSend").on("click", function(e) {
        emailAddress = $("#paymentPrintEmailValue").val();

        if (emailAddress.length < 1) {
            return;
        }

        var re =
            /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!re.test(String(emailAddress).toLowerCase())) {
            return;
        }

        $.ajax({
            type: "GET",
            url: "custom/billing/payment_controller.php?action=sendmail",
            dataType: 'json',
            data: {
                pid: pid,
                email: emailAddress
            },
            complete: function(json) {
                $(".printstatus_" + pid).html(
                    "Yet to be signed. Email sent for signature.");
                $("#paymentPrintEmailAlert").slideDown();
                /* Show email sent alert */
                setTimeout(function() {
                    $("#paymentPrintEmailModal").modal("hide");
                    $("#paymentPrintEmailAlert").hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                // alert(xhr.responseText);
            }
        });
    });

    /* On click - print button - show print email send modal */
    $(".printButtonUnsigned").on("click", function(e) {
        pid = $(this).attr("name");

        $("#paymentPrintEmailSend").attr("data-pid", pid);
        $("#paymentPrintEmailModal").modal("show");
    });

    // DataTable
    let table = $('#paymentTable').DataTable({
        "autoWidth": false
    });

    $('#search_current').click(function() {
        updateTable();
    });

    $('#clear_search').click(function() {
        $('#paymentMethod').val("");
        $('#checkNo').val("");
        $('#paymentDate').val("");
        $('#accountType').val("");
        $('#contract').val("");
        $('#project').val("");
        $('#invoiceNo').val("");
        updateTable();
    });

    function updateTable() {
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let paymentMethod = $('#paymentMethod').val();
                let checkNo = $('#checkNo').val();
                let paymentDate = $('#paymentDate').val();
                let accountType = $('#accountType').val();
                let contract = $('#contract').val();
                let project = $('#project').val();
                let invoiceNo = $('#invoiceNo').val();

                let projectValue = data[1];
                let contractValue = data[2];
                let invoiceNoValue = data[3];
                let paymentMethodValue = data[5];
                let checkNoValue = data[6];
                let accountTypeValue = data[8];
                let paymentDateValue = data[10];

                let paymentMethodCondition = true;
                if (paymentMethod) {
                    paymentMethodCondition = (paymentMethod === paymentMethodValue);
                }

                let checkNoCondition = true;
                if (checkNo) {
                    checkNoCondition = (checkNo === checkNoValue);
                }

                let paymentDateCondition = true;
                if (paymentDate) {
                    paymentDateCondition = (paymentDate === paymentDateValue);
                }

                let accountTypeCondition = true;

                if (accountType) {
                    accountTypeCondition = (accountType.trim() === accountTypeValue.trim());
                }

                let contractCondition = true;
                if (contract) {
                    contractCondition = (contract === contractValue);
                }

                let projectCondition = true;
                if (project) {
                    projectCondition = (project === projectValue);
                }

                let invoiceNoCondition = true;
                if (invoiceNo) {
                    invoiceNoCondition = (invoiceNo === invoiceNoValue);
                }

                return (paymentMethodCondition &&
                    checkNoCondition &&
                    paymentDateCondition &&
                    contractCondition &&
                    projectCondition &&
                    invoiceNoCondition &&
                    accountTypeCondition);
            }
        );
        table.draw();
    }

    $('#paymentMethod').select2();
    $('#contract').select2();
    $('#project').select2();
    $('#accountType').select2();

    $('#paymentDate').datetimepicker({
        format: 'y-MM-dd'
    });
});
</script>