<?php
session_start();
include "subdomain_check.php";
include_once("pdo/dbconfig.php");
$dbClass = 'pdo/Class.Bill.php';
include_once($dbClass);
$DB_bill = new Bill($DB_con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>
		<?php
		$company_id = $_SESSION['company_id'];
		echo $DB_company->getWebTitle($company_id);
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
include_once("search-bar.php");
?>
<section id="section-body">
    <div class="container">
        <div class="page-title breadcrumb-top">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                        <!--            <li><a href="event-list.php">Event List</a></li>-->
                        <li><a href="map-listing.php">Property</a></li>
                    </ol>
                </div>
            </div>
        </div>

		<?php
		$paymentId    = $_GET['id'];
		$paymentPrint = $DB_bill->getPaymentById($paymentId);
		$is_signed    = $paymentPrint["is_signed"];
		$billPaid     = $DB_bill->getBillByID($paymentPrint['bill_id']);

		$resultVendor = $DB_vendor->getVendorInfo($billPaid['vendor_id']);
		$vendorName   = $resultVendor['company_name'];
		?>

        <input type="hidden" value="<?php echo $_GET['id']; ?>" id="paymentIdvalue">

        <div class="row">
            <div class="col-md-6">
                <form class="panel panel-primary" id="create-event-oneonone-form"
                      action="controller_event.php" method="post">
                    <div class="panel-heading"> Bill</div>
                    <div class="panel-body">
                        <div class="col-sm-12 col-xs-12">


                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Vendor Name </strong>
                                            <span><?php echo $vendorName; ?></span>
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
                                            class="btn btn-danger btn-md removeapresubmit col-md-12">
                                        <strong>Submit</strong>
                                    </button>
                                </div>
                            </div>

						<?php } else { ?>
                            <div class="alert alert-danger"> You have already made the signature for this Bill.</div>
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
<script type="text/javascript" src="js/masonry.pkgd.min.html"></script>
<script type="text/javascript" src="js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="js/infobox.js"></script>
<script type="text/javascript" src="js/markerclusterer.js"></script>
<script type="text/javascript" src="js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

<script type="text/javascript">
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
            paymentId = jQuery("#paymentIdvalue").val();
            buttonClassName = jQuery(this).attr("class");

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

            /* Send pdf to the email ID specified*/
            // if (jQuery(this).hasClass("generatePdfPaymentSend")) {
            //     fd.append("action", "printPaymentSend");
            //     emailAddressToSend = jQuery("#paymentSendEmailValue").val();
            //
            //     if (emailAddressToSend.length < 1) {
            //         return;
            //     }
            //
            //     fd.append("action", "printPaymentSend");
            //     fd.append("email", emailAddressToSend);
            // }
            // else {
            //     /* Download the pdf without sending to any email address */
            //     fd.append("action", "printPaymentDownload");
            // }



            jQuery.ajax({
                url: "admin/custom/billing/printPaymentPdf.php",
                data: fd,
                type: "POST",
                contentType: false,
                processData: false,
                cache: false,
                dataType: "json",
                complete: function (response) {
                    jQuery(".showapressubmitsuccess").fadeIn();
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
