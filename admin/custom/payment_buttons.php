<?

namespace PHPMaker2023\spgmanagement;

use \Company;
use \Vendor;
?>
<div class="modal fade" id="paymentprintpasswordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Enter your password to print the bill</h4>
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
                <h4 class="modal-title">Recipient's Email Address </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-12"> &nbsp;
                        <input type="hidden" id="action_type" name="action_type">
                        <div id="input_manager">Email:
                            <select id="paymentPrintEmailValueInput">
                                <option value="0" data-select-vendor_id="0">Select Email</option>
                                <?
                                $root = dirname(__DIR__);
                                include($root . '/../pdo/dbconfig.php');
                                include_once($root . '/../pdo/Class.Vendor.php');
                                include_once($root . '/../pdo/Class.Company.php');
                                $sql = "SELECT vendor_id, company_name,  CONCAT(IFNULL(email,''),',',IFNULL(sign_email,'')) AS email  FROM vendor_infos WHERE email<>'' OR sign_email<>'' ORDER BY company_name";
                                $vendor_emails = ExecuteRows($sql);
                                //$vendor_emails=array_unique ($vendor_emails);
                                //var_dump($vendor_emails);
                                foreach ($vendor_emails as $vendor_email) {
                                    $vendor_id = $vendor_email['vendor_id'];
                                    $emails = array_unique(explode(",", $vendor_email['email']));
                                    if (!empty($emails)) {
                                        foreach ($emails as $email) {
                                            if (!empty($email)) {
                                ?>
                                <option value="<?= $email ?>" data-select-vendor_id="<?= $vendor_id ?>"><?= $email ?>
                                </option>
                                <? }
                                        }
                                    }
                                } ?>
                                <option value="<?= $_SESSION['UserEmail'] ?>" data-select-vendor_id="0">
                                    <?= $_SESSION['UserEmail'] ?></option>
                            </select>
                            <!--input type="text" class="form-control form-group" id="paymentPrintEmailValueInput"  placeholder="Enter the email address of the recipient"/-->

                            <br>

                        </div>
                        &nbsp;
                        <div id="select_manager">Company: <select id="paymentPrintEmailValueSelect">
                                <?


                                //                        $SelectSql = "select name from company_infos where id=".$_SESSION['company_id'];
                                //                        $statement = $DB_con->prepare($SelectSql);
                                //                        $statement->execute();
                                //                        list($company_name) = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
                                $DB_company = new Company($DB_con);


                                $DB_vendor = new Vendor($DB_con);

                                if (CurrentUserLevel() == 14) {
                                    $vendor_id = $_SESSION['employee_id'];
                                    $company_id = $DB_vendor->getVendorCompany($vendor_id);
                                } else {
                                    $company_id = $_SESSION['company_id'];
                                }

                                $company_name = $DB_company->getName($company_id);
                                ?>

                                <option value="<?= $_SESSION['company_id'] ?>"><?= $company_name ?></option>

                            </select><br><br></div>
                        <div id="reasontr">Reason: <input type="text" class="form-control form-group" id="reason"
                                name="reason" placeholder="Reason" /></div>
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

<form id="printNavigationForm" action="custom/billing/printPaymentPdf.php" method="post" target="_blank"
    style="display: none;">
    <input type="hidden" id="printNavigationForm_pid" name="id" value="" />
    <input type="hidden" id="action_type_id" name="action_type_id" value="" />
    <input type="submit" />
</form>

<script>
loadjs.ready("load", function() {
    function resetPrint(printId) {
        $.ajax({
            url: "custom/resetPrint.php?id=" + printId
        }).done(function() {
            return true;
        });
    }

    function donePrint(printId) {
        $.ajax({
            url: "custom/resetPrint.php?fid=" + printId
        }).done(function() {
            return true;
        });
    }


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

    $(document).ready(function() {


        // $(".ew-list-other-options").append("<button onclick='javascript:ew.vars.refreshTable(\"true\",\"tbl_payment_infoslist\");' class='btn btn-danger'>Refresh</button>");
        $(".ew-list-other-options").append(
            "<button onclick='javascript:location.reload();' class='btn btn-danger'>Refresh</button>"
        )

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
                $("#printNavigationForm").find("#action_type_id").val(action_type_id);
                $("#printNavigationForm").submit();
                $("#paymentprintpasswordModal").modal("hide");
                $(".printstatus_" + pid).html("Printed");
                //  location.reload();
            } else {
                /* Show wrong password alert */
            }
        });

        /* CLick print button after the signature is done - ask for password of the user */
        $(".printSignedPwd").on("click", function(e) {
            pid = $(this).attr("data-pid");
            /* Fetch the pid of the clicked payment */
            action_type_id = $(this).attr("data-action-type-id");
            //         alert(action_type_id);
            $("#paymentprintpasswordsubmit").attr("data-pid", pid);
            /* Set pid value to the Password modal submit button */
            $("#paymentprintpasswordModal").modal("show");
            /* SHow the modal */
        });

        /* Send button clicked in the modal for the print payment email */
        $("#paymentPrintEmailSend").on("click", function(e) {


            var resetAction = false;
            if ($(this).attr("data-reset") != null && $(this).attr("data-reset") == "true") {
                resetAction = true;
            }
            //  action_type_id=$(this).attr("data-action-type-id");
            //  alert(action_type_id);
            $("action_type").val(action_type_id);
            if (action_type_id == 1 || action_type_id == 2 || resetAction == true) {
                emailAddress = $("#paymentPrintEmailValueSelect").val();
            } else {
                emailAddress = $("#paymentPrintEmailValueInput").val();
            }

            reason = $("#reason").val();

            // alert(action_type_id);





            if (emailAddress.length < 1) {
                return;
            }

            //  alert(emailAddress);

            var re =
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            /*
            if (!re.test(String(emailAddress).toLowerCase())) {
                return;
            }
*/
            $.ajax({
                type: "GET",
                url: "custom/billing/payment_controller.php?action=sendmail",
                dataType: 'json',
                data: {
                    pid: pid,
                    email: emailAddress,
                    reason: reason,
                    action_type_id: action_type_id,
                    reset: resetAction
                },
                complete: function(json) {
                    $(".printstatus_" + pid).html(
                        "Yet to be signed. Email sent for signature.");
                    $("#paymentPrintEmailAlert").slideDown();
                    /* Show email sent alert */
                    setTimeout(function() {
                        $("#paymentPrintEmailModal").modal("hide");
                        $("#paymentPrintEmailAlert").hide();
                        location.reload();
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
            vendor_id = $(this).attr("data-vendor_id");
            var resetAction = false;
            if ($(this).attr("data-reset") != null && $(this).attr("data-reset") == "true") {
                resetAction = true;
            }

            // Hide Other Vendor Emails
            $('#paymentPrintEmailValueInput').children().hide();
            // $('#paymentPrintEmailValueInput').children().hide();
            $('*[data-select-vendor_id="0"]').show();
            //$('*[data-select-vendor_id="0"]').val(0);
            $('*[data-select-vendor_id="' + vendor_id + '"]').show();
            //$('*[data-select-vendor_id="'+vendor_id+'"]').select();


            action_type_id = $(this).attr("data-action-type-id");
            $("action_type").val(action_type_id);
            if (action_type_id == 1 || action_type_id == 2 || resetAction == true) {
                $("#select_manager").show();
                $("#input_manager").hide();
                //    alert(resetAction);
            } else {
                $("#select_manager").hide();
                $("#input_manager").show();
            }
            //   alert(action_type_id);
            $("#paymentPrintEmailSend").removeAttr("data-reset");
            $('#reasontr').hide();
            /* Check if the Button is a Reset Button */
            if ($(this).attr("data-reset") != null && $(this).attr("data-reset") == "true") {
                $("#paymentPrintEmailSend").attr("data-reset", true);
                $('#reasontr').show();
            }

            $("#paymentPrintEmailSend").attr("data-pid", pid);
            $("#paymentPrintEmailModal").modal("show");
        });
        /*
                // DataTable
                let table = $('#paymentTable').DataTable({
                    "autoWidth": false
                });

                $('#search_current').click(function () {
                    updateTable();
                });

                $('#clear_search').click(function () {
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
                        function (settings, data, dataIndex) {
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
        */
        $('#paymentDate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    });
});
</script>