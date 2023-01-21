<?php
//namespace PHPMaker2020\spgmanagement;
//use \Crud, \Bill, \Snapshot, \Company, \Employee, \Project, \Request, \Vendor;
// use PHPMaker2020\spgmanagement\Snapshot;
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once("pdo/dbconfig.php");
include_once('pdo/Class.Bill.php');
$DB_bill = new Bill($DB_con);
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('pdo/Class.Employee.php');
$DB_employee = new Employee($DB_con);
include_once('pdo/Class.Snapshot.php');
$DB_snapshot = new Snapshot($DB_con);
include_once ('pdo/Class.Project.php');
$DB_project = new Project($DB_con);
include_once ('pdo/Class.Request.php');
$DB_request = new Request($DB_con);
include_once ('pdo/Class.Vendor.php');
$DB_vendor = new Vendor($DB_con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
    <title>
        <?php

      //  die($company_id);
     //   if(!empty($_SESSION['company_id'])){echo $DB_company->getWebTitle($company_id);$company_id = $_SESSION['company_id'];}else{"SPG Management";}
        ?>
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once "links-for-html.php"; ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<?php
include_once("header.php");
?>
<section id="section-body">
    <div class="container">

            <!-- <div class="row justify-content-md-center">
                <div class="col-md-12">
                    <img src="images\logo_big.jpg">
                </div>
            </div> -->


        <?php
        $resetAction = 0;

        if (isset($_GET["resetact"]) && !is_null($_GET["resetact"])) {
            $resetAction = intval($_GET["resetact"]);
        }
        if(!empty($_GET['action_type_id'])){$action_type_id=$_GET['action_type_id'];}
        $paymentId = $_GET['id'];
        $paymentPrint = $DB_bill->getPaymentById($paymentId);

        $is_signed = $paymentPrint["is_signed"];
        $is_printed = $paymentPrint["is_printed"];
        $is_vendor_signed = $paymentPrint["is_vendor_signed"];
        $is_picked_up = $paymentPrint["is_pickup"];
        $reason = $paymentPrint["reason"];
        $invoice_id=$paymentPrint["invoice_id"];
        $email_manager_sent = $paymentPrint["email_manager_sent"];
        $email_vendor_sent = $paymentPrint["email_vendor_sent"];
        $email_pickup = $paymentPrint["email_pickup"];
        if(!empty($paymentPrint["sender_id"])){
        $employee_id = $paymentPrint["sender_id"];
        }else{
        $employee_id = $paymentPrint["employee_id"];
        }
//die($employee_id);


        $project_name = $DB_project->getProjectName($paymentPrint["project_id"]);
        $contract_name = $DB_project->getContactName($paymentPrint["contract_id"]);
        $invoice_no = $paymentPrint["invoice_no"];
        $company_name = $DB_company->getName($paymentPrint["company_id"]);
        $memo = $paymentPrint["memo"];
        $comments = $paymentPrint["comments"];

        if ($employee_id>300000) {
            $employee_FullName = $DB_vendor->getVendorInfo($employee_id)['company_name'];
            $employee_info = $DB_vendor->getVendorInfo($employee_id);
        }else{
            $employee_FullName=$DB_employee->getEmployeeName($employee_id);
            $employee_info=$DB_employee->getEmployeeInfo($employee_id);
        }

        $employee_level_id=$employee_info['user_level'];
        //die("pp=".$paymentPrint["user_level"]);
        $employee_level_name = $DB_employee->getLevelName($employee_level_id);


        $billPaid = $DB_bill->getBillByID($paymentPrint['invoice_id']);
        $contractId = $paymentPrint['contract_id'];

        $contractDetails = $DB_request->getContractDataByContractId($contractId);
        $vendorId = $paymentPrint["vendor_id"];
        $vendorDetails = $DB_vendor->getVendorInfo($vendorId);
        $vendorName = $vendorDetails['company_name'];

        //die(var_dump($vendorDetails));
        //	$resultVendor = $DB_vendor->getVendorInfo($billPaid['vendor_id']);
        ?>

        <input type="hidden" value="<?php echo $_GET['id']; ?>" id="paymentIdvalue">
        <input type="hidden" value="<?php echo $resetAction; ?>" id="resetActionValue">
        <input type="hidden" value="<?php echo $email_manager_sent; ?>" id="email_manager_sent">
        <input type="hidden" value="<?php echo $email_vendor_sent; ?>" id="email_vendor_sent">
        <input type="hidden" value="<?php echo $email_pickup; ?>" id="email_pickup">

        <div class="row">
            <div class="col-md-6">
                <form class="panel panel-primary" id="create-event-oneonone-form"
                      action="controller_event.php" method="post">
                    <div class="panel-heading"> Bill</div>
                    <div class="panel-body">





                        <div class="col-sm-12 col-xs-12">

                            <div class="col-lg-12">
                                Sender : <b><?="$employee_FullName</b> - Position: <b>$employee_level_name</b> - ID: <b>$employee_id</b>"?></b><br>
                                <? echo "Project : <b>$project_name</b>  <br>Contract : <b>$contract_name</b> <br>Invoice No. : <b>";
if(!empty($attach)) {
echo "<br><span><a target='_blank' href='https://spgmanagement.com/admin/files/$attach'>";
}
echo $invoice_no."</a>";

echo "</b><br>Memo : <b>$memo</b>";
?>
                            <? if(!empty($reason)){?>Reason to reset : <b><?=$reason?></b><? }?>
                                <? if ($action_type_id==1 || $action_type_id==2){ echo "<br>Comment: <b>$comments</b>";}?>
                                <?
                                if(!empty($invoice_id)) {
                                    $attach = $DB_bill->getPaymentInvoiceAttachment($invoice_id);
                                    //	var_dump($attach);
                                    if(!empty($attach)) {
                                        echo "<br><span><a target='_blank' href='https://spgmanagement.com/admin/files/$attach'>Click to download invoice Attachment</a></span>";
                                    }
                                }
                                ?>
                            </div>


                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Vendor Name </strong>
                                            <span><?=$vendorDetails["company_name"]?><?php // echo strlen($vendorDetails["full_name"]) < 1 ? $vendorDetails["company_name"] : $vendorDetails["full_name"] . " - " . $vendorDetails["company_name"]; ?> </span>





                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Bill Amount</strong>
                                            <span>$<?php echo $paymentPrint['amount']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($is_signed == 1 && $is_printed == 1 && $is_vendor_signed == 1 && $is_picked_up == 0 && $resetAction != 1) { ?>

                                <input type="hidden" value="true" id="pickupDetailCapture">

                                <div class="form-group create-event-form-col-1 col-md-12">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Pickup Person Name </strong>
                                                <input type="text" class="form-control" name="pickup_name"
                                                       id="pickup_name"
                                                       placeholder="Enter the name of the pickup person" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group create-event-form-col-1 col-md-6">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Pickup ID Type </strong>
                                                <input type="text" class="form-control" name="pickup_id_type"
                                                       id="pickup_id_type" placeholder="Enter the type of Pickup ID"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group create-event-form-col-1 col-md-6">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Pickup ID Number</strong>
                                                <input type="text" class="form-control" name="pickup_id_number"
                                                       id="pickup_id_number" placeholder="Enter the Pickup ID Detail"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">Signature</div>
                    <div class="panel-body">
                        <?php
                        /* check if the request status is already 4 */
                        if (intval($is_signed) != 1) { ?>
                            <div class="row form-group removeapresubmit">
                                <div id="signature-pad" class="signature-pad col-md-12 form-group">
                                    <div class="signature-pad-body">
                                        <canvas id="canvas" style="border:1px dashed;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-12">
                                    <button id="generatePdfPaymentSend"
                                            data-whosigned="0"
                                            class="btn btn-danger btn-md removeapresubmit col-md-4">
                                        <strong>Submit</strong>
                                    </button>
                                    <button id="clearButton"
                                            class="btn btn-success btn-md col-md-3 padding-right-md " onclick="clearCanvas();">
                                        <strong>Clear <i class="fa fa-eraser" aria-hidden="true"></i></strong>
                                    </button>
                                    <button id="closeButton"
                                            class="btn  btn-info btn-md col-md-3 padding-right-md " onclick="javascript:window.close()">
                                        <strong>Close X</strong>
                                    </button>
                                </div>
                            </div>
                        <?php } else {
                            // Check if the vendor has signed this
                            if (intval($is_vendor_signed) != 1) { ?>

                                <div class="row form-group removeapresubmit">
                                    <div id="signature-pad" class="signature-pad col-md-12 form-group">
                                        <div class="signature-pad-body">
                                            <canvas id="canvas" style="border:1px dashed;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-md-12">
                                        <button id="generatePdfPaymentSend"
                                                data-whosigned="1"
                                                class="btn btn-danger btn-md removeapresubmit col-md-3 padding-right-md ">
                                            <strong>Submit <i class="fa fa-sign-in" aria-hidden="true"></i></strong>
                                        </button> &nbsp;
                                        <button id="clearButton"
                                                class="btn btn-success btn-md col-md-3 padding-right-md " onclick="clearCanvas();">
                                            <strong>Clear <i class="fa fa-eraser" aria-hidden="true"></i></strong>
                                        </button>
                                        <button id="closeButton"
                                                class="btn  btn-info btn-md col-md-3 padding-right-md " onclick="javascript:window.close()">
                                            <strong>Close X</strong>
                                        </button>
                                    </div>
                                </div>

                                <?php
                            } else {
                                // Both vendor and the owner have signed
                                // Check if the Pickup is done

                                if ($is_signed == 1 && $is_printed == 1 && $is_vendor_signed == 1 && ($is_picked_up == 0 || $resetAction == 1) || $resetAction == 1) { ?>

                                    <div class="row form-group removeapresubmit">
                                        <div id="signature-pad" class="signature-pad col-md-12 form-group">
                                            <div class="signature-pad-body">
                                                <canvas id="canvas" style="border:1px dashed;"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-12">
                                            <button id="generatePdfPaymentSend"
                                                    data-whosigned="<?php echo $resetAction == 1 ? 1 : 2; ?>"
                                                    class="btn btn-danger btn-md removeapresubmit col-md-3 padding-right-md">
                                                <strong>Submit</strong>
                                            </button>
                                            <button id="clearButton"
                                                    class="btn btn-success btn-md col-md-3 padding-right-md " onclick="clearCanvas();">
                                                <strong>Clear <i class="fa fa-eraser" aria-hidden="true"></i></strong>
                                            </button>
                                            <button id="closeButton"
                                                    class="btn  btn-info btn-md col-md-3 padding-right-md " onclick="javascript:window.close()">
                                                <strong>Close X</strong>
                                            </button>
                                        </div>
                                    </div>

                                <?php } else { ?>

                                    <div class="alert alert-danger"> You have already made the signature for this
                                        Bill.
                                    </div><button id="closeButton"
                                                  class="btn  btn-info btn-md col-md-3 padding-right-md " onclick="javascript:window.close()">
                                        <strong>Close X</strong>
                                    </button>

                                <?php }

                                ?>

                            <?php } ?>

                        <?php } ?>


                        <span class="row form-group showapressubmitsuccess" style="display:none;">
                            <div class="col-md-12 form-group alert alert-success">
                                <span>Your signatured is successfully captured and the accountant is notified by email.</span>
                            </div>
                        </span>

                    </div>
                </div>
            </div>
        </div>


    </div>
</section>
<!--end section page body-->

<!--start footer section-->
<?php include_once("footer.php"); ?>
<!--end footer section-->

<!--Start Scripts-->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
<script type="text/javascript" src="js/bootstrap-select.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<!--<script type="text/javascript" src="js/masonry.pkgd.min.html"></script>-->
<script type="text/javascript" src="js/jquery.nicescroll.js"></script>
<!--<script type="text/javascript" src="js/infobox.js"></script>-->
<script type="text/javascript" src="js/markerclusterer.js"></script>
<script type="text/javascript" src="js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

<script type="text/javascript">

    function clearCanvas() {
        const context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
    }
    jQuery(document).ready(function () {

       var wrapperSignature = document.getElementById("signature-pad");

        var canvasSignature = wrapperSignature.querySelector("canvas");
        var signaturePad = new SignaturePad(canvasSignature, {
            backgroundColor: 'rgb(255, 255, 255)'
        });





        function b64toBlob(b64Data, contentType, sliceSize) {
            contentType = contentType || '';
            sliceSize = sliceSize || 512;

            var byteCharacters = atob(b64Data);
            var byteArrays = [];

            for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                var slice = byteCharacters.slice(offset, offset + sliceSize);

                var byteNumbers = new Array(slice.length);
                for (var i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                var byteArray = new Uint8Array(byteNumbers);

                byteArrays.push(byteArray);
            }

            var blob = new Blob(byteArrays, {type: contentType});
            return blob;
        }

        /* Submit click */
        jQuery("#generatePdfPaymentSend").on("click", function () {
            var resetActionValue = jQuery("#resetActionValue").val();
            var pickupDetailCapture = jQuery("#pickupDetailCapture").length;

            paymentId = jQuery("#paymentIdvalue").val();
            buttonClassName = jQuery(this).attr("class");
            signedBy = parseInt(jQuery(this).attr("data-whosigned"));

            signature_image_b64 = signaturePad.toDataURL("image/jpeg");

            /* Signature conversion to BLOB */
            var ImageURL = signature_image_b64;
            var signatureBlock = ImageURL.split(";");
            var signaturecontentType = signatureBlock[0].split(":")[1];
            var signatureRealData = signatureBlock[1].split(",")[1];
            var signatureBlob = b64toBlob(signatureRealData, signaturecontentType);

            // Create a FormData and append the file
            var fd = new FormData();
            fd.append("image", signatureBlob);
            fd.append("id", paymentId);
            fd.append("action", "printPaymentSave");
            fd.append("who-signed", signedBy);
            fd.append("reset-action", resetActionValue);
            fd.append("action_type_id", <?=$action_type_id?>);
            <? if(!empty($email_manager_sent)){?>fd.append("email_manager_sent",'<?=$email_manager_sent?>');<?}?>
            <? if(!empty($email_vendor_sent)){?>fd.append("email_vendor_sent",'<?=$email_vendor_sent?>');<?}?>
            <? if(!empty($email_pickup)){?>fd.append("email_pickup",'<?=$email_pickup?>');<?}?>

            /* Check if the signature is by the pickup person - get the details of pickup person */
            if (signedBy == "2" && pickupDetailCapture == 1) {
                fd.append("pickup_name", jQuery("#pickup_name").val());
                fd.append("pickup_type", jQuery("#pickup_id_type").val());
                fd.append("pickup_id", jQuery("#pickup_id_number").val());
            }

            /* Send pdf to the email ID specified*/
            if (jQuery(this).hasClass("generatePdfPaymentSend")) {
                fd.append("action", "printPaymentSend");
                emailAddressToSend = jQuery("#paymentSendEmailValue").val();
            
                if (emailAddressToSend.length < 1) {
                    return;
                }
            
                fd.append("action", "printPaymentSend");
                fd.append("email", emailAddressToSend);
            }
            else {
                /* Download the pdf without sending to any email address */
                fd.append("action", "printPaymentDownload");
            }

            var url = "admin/custom/billing/printPaymentPdf.php";

            if (resetActionValue == "1") {
                var url = "admin/custom/billing/resetPayment.php"
            }

            jQuery.ajax({
                url: url,
                data: fd,
                type: "POST",
                contentType: false,
                processData: false,
                cache: false,
                dataType: "json",
                complete: function (response) {
                    jQuery(".showapressubmitsuccess").fadeIn();
                    jQuery("#clearButton").remove();
                    jQuery(".removeapresubmit").remove();

                    /* Send email to the accountant */
                    // jQuery.ajax({
                    //     url: "admin/custom/billing/printPaymentPdf.php",
                    // });

                }
            });

        });
    });

</script>

</body>
</html>
