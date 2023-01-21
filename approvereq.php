<?php
session_start();
include "subdomain_check.php";
include_once("pdo/dbconfig.php");

if (isset($_GET['c'])) {
	$_SESSION['company_id'] = $_GET['c'];
} ?>
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
                    <div class="page-title-left">
                        <h2>Request Completion</h2>
                    </div>
                </div>
            </div>
        </div>

		<?php
		$request_id    = $_GET['rid'];
		$requestInfo   = $DB_request->get_one_request_info($request_id);
		$apartmentInfo = $DB_request->get_apartment_info($request_id);

		/* Display Values*/
		$request_status    = $requestInfo["request_status"];
		$request_status_id = $requestInfo["status_id"];
		$request_type      = $requestInfo["request_type"];
		$created_time      = $requestInfo["entry_datetime"];
		$message           = $requestInfo["message"];

		$buildingName     = $apartmentInfo["building_name"];
		$building_address = $apartmentInfo["building_address"];
		$unitDetail       = $apartmentInfo["specific_area"];
		?>

        <input type="hidden" name="request_id_value" id="request_id_value" value="<?php echo $request_id; ?>">

        <div class="row">
            <div class="col-md-6">
                <form class="panel panel-primary" id="create-event-oneonone-form"
                      action="controller_event.php" method="post">
                    <div class="panel-heading"> Request Information</div>
                    <div class="panel-body">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Request ID #</strong>
                                            <span><?php echo $request_id; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Created Time</strong>
                                            <span><?php echo $created_time; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-6">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Message</strong>
                                            <span><?php echo $message; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-12">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Request Type</strong>
                                            <span><?php echo $request_type; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-12">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Address</strong>
                                            <span><?php echo $building_address; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group create-event-form-col-1 col-md-12">
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-12">
                                            <strong>Apartment</strong>
                                            <span><?php echo $unitDetail . " - " . $buildingName; ?></span>
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
						if ($request_status_id != 4) { ?>
                            <div class="row form-group well">
                                <div id="tenant-signature-pad" class="signature-pad col-md-12 form-group">
                                    <legend><h2><kbd>Tenant Signature </kbd></h2></legend>
                                    <div class="tenant-signature-pad-body">
                                        <canvas id="tenant_canvas" style="border:1px dashed;"></canvas>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <button id="clear-signature-tenant" class="btn btn-danger btn-md removeapresubmit">
                                        Clear Tenant Signature
                                    </button>
                                </div>
                            </div>

                            <div class="row form-group well">
                                <div id="vendor-signature-pad" class="signature-pad col-md-12 form-group">
                                    <legend><h2><kbd>Vendor Signature </kbd></h2></legend>
                                    <div class="vendor-signature-pad-body">
                                        <canvas id="vendor_canvas" style="border:1px dashed;"></canvas>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <button id="clear-signature-vendor" class="btn btn-danger btn-md removeapresubmit">
                                        Clear Vendor Signature
                                    </button>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-12">
                                    <button id="tenant_signature_submit"
                                            class="btn btn-success btn-md removeapresubmit col-md-8">
                                        Submit
                                    </button>
                                </div>
                            </div>

						<?php } else { ?>

                            <div class="alert alert-info"> This request has already been marked as <i>Complete</i></div>

						<?php } ?>

                        <span class="row form-group showapressubmitsuccess" style="display:none;">
                            <div class="col-md-12 form-group alert alert-success">
                                <span>Tenant signatured is successfully captured and request status is modified to Complete.</span>
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
    // jQuery(document).ready(function () {
    var wrapperTenant = document.getElementById("tenant-signature-pad");
    var wrapperVendor = document.getElementById("vendor-signature-pad");

    var canvasTenant = wrapperTenant.querySelector("canvas");
    var signaturePadTenant = new SignaturePad(canvasTenant, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    var canvasVendor = wrapperVendor.querySelector("canvas");
    var signaturePadVendor = new SignaturePad(canvasVendor, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);

        canvasTenant.width = canvasTenant.offsetWidth * ratio;
        canvasTenant.height = canvasTenant.offsetHeight * ratio;
        canvasTenant.getContext("2d").scale(ratio, ratio);

        canvasVendor.width = canvasVendor.offsetWidth * ratio;
        canvasVendor.height = canvasVendor.offsetHeight * ratio;
        canvasVendor.getContext("2d").scale(ratio, ratio);

        signaturePadTenant.clear();
        signaturePadVendor.clear();
    }

    jQuery("#clear-signature-tenant").on("click", function () {
        signaturePadTenant.clear();
    });

    jQuery("#clear-signature-vendor").on("click", function () {
        signaturePadVendor.clear();
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

    jQuery("#tenant_signature_submit").on("click", function () {
        tenant_signature_image_b64 = signaturePadTenant.toDataURL("image/jpeg");
        vendor_signature_image_b64 = signaturePadVendor.toDataURL("image/jpeg");
        request_id = jQuery("#request_id_value").val();

        /* Tenant signature conversion to blob */
        var tenantImageURL = tenant_signature_image_b64;
        var tenantBlock = tenantImageURL.split(";");
        var tenantcontentType = tenantBlock[0].split(":")[1];
        var tenantRealData = tenantBlock[1].split(",")[1];
        var tenantBlob = b64toBlob(tenantRealData, tenantcontentType);

        /* Vendor signature conversion to blob */
        var vendorImageURL = vendor_signature_image_b64;
        var vendorBlock = vendorImageURL.split(";");
        var vendorcontentType = vendorBlock[0].split(":")[1];
        var vendorRealData = vendorBlock[1].split(",")[1];
        var vendorBlob = b64toBlob(vendorRealData, vendorcontentType);

        // Create a FormData and append the file
        var fd = new FormData();
        fd.append("tenant_image", tenantBlob);
        fd.append("vendor_image", vendorBlob);
        fd.append("action", "tenantSignatureUpload");
        fd.append("request_id", request_id);

        jQuery.ajax({
            url: "admin/custom/request/request_info_controller.php",
            data: fd,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            error: function (err) {
                console.error(err);
            },
            success: function (data) {
                if (data && data.files.tenant_image.length > 0 && data.files.vendor_image.length > 0) {
                    vendorCanvasheight = jQuery("vendor_canvas").attr("height");
                    vendorCanvaswidth = jQuery("vendor_canvas").attr("width");

                    tenantCanvasheight = jQuery("tenant_canvas").attr("height");
                    tenantCanvaswidth = jQuery("tenant_canvas").attr("width");

                    jQuery("#vendor_canvas").remove();
                    jQuery("#tenant_canvas").remove();
                    jQuery(".removeapresubmit").remove();

                    jQuery(".vendor-signature-pad-body").append("<img width='" + vendorCanvaswidth + "' height='" + vendorCanvasheight + "' src='" + vendor_signature_image_b64 + "'>");
                    jQuery(".tenant-signature-pad-body").append("<img width='" + tenantCanvaswidth + "' height='" + tenantCanvasheight + "' src='" + tenant_signature_image_b64 + "'>");

                    jQuery(".showapressubmitsuccess").fadeIn();
                }
            }
        });

    });
    // });

</script>
</body>
</html>
