<?php
// Request Handler to handle the requests
include "Class.RequestHandler.php";
$requestHandler = new RequestHandler();
$requestHandler->makeRepoConn();
$requestHandler->makePdoObject("Request", "request");
$requestHandler->makePdoObject("Vendor", "vendor");

// Local variables
$user_id = $_SESSION['UserID'];
$user_level = $_SESSION['UserLevel'];
$employee_id = $_SESSION['employee_id'];
$companyId = $_SESSION['company_id'];

$user_id = 54;
$user_level = 1;
$employee_id = 54;
$companyId = 9;
?>
<input type="hidden" name="userIdValue" id="userIdValue" value="<?php echo $user_id; ?>" />
<input type="hidden" name="employeeIdValue" id="employeeIdValue" value="<?php echo $employee_id; ?>" />
<input type="hidden" name="companyIdValue" id="companyIdValue" value="<?php echo $companyId; ?>" />
<?php
include("../../../pdo/dbconfig.php");

$vendorId = null;
if (isset($_GET["vid"])) {
    $vendorId = $_GET["vid"];
}

if ($user_level == 5) {     //tenant
    $user_unit_id = $_GET['unit_id'];
} else {      //employee
    $user_unit_id = 0;
}

/* If the page parameters are set for viewing a request - action and rid */
$request_id = "";
if (isset($_GET["action"]) && $_GET["action"] == "rview") {
    $request_id = $_GET["rid"];
    $reportReadyData = $requestHandler->get_report_ready($user_level, $user_id, $user_unit_id);
    $viewRequestData = $requestHandler->get_modal_info($request_id, $user_id, $user_unit_id);
    $recipientsData = $requestHandler->get_recipients($request_id);
    $orderData = $requestHandler->get_editing($request_id, $user_id);
    $materialsData = $requestHandler->request->getMaterialProvided($request_id);
    $invoicesData = $requestHandler->get_attached_invoices($request_id);
    $reportLocationForRequest = $orderData["location"];
}

$locationReportArea = array("default" => "None", "common area" => "Common Area", "apartment" => "Apartment");

$filter_building_list = array();
$building_lst = $DB_request->get_building_list($user_id);
if ($user_level == 5) {
    $temp = array();
    $temp['building_id'] = $building_lst['building_id'];
    $temp['building_name'] = $building_lst['building_name'];
    array_push($filter_building_list, $temp);
} else {
    foreach ($building_lst as $r) {
        $temp = array();
        $temp['building_id'] = $r['building_id'];
        $temp['building_name'] = $r['building_name'];
        array_push($filter_building_list, $temp);
    }
}
$filter_employee_lst = $DB_request->get_employees_lst($user_id);
?>

<div class="container-fluid">
    <div class="panel panel-success">
        <div class="panel-heading">Requests</div>
        <div class="panel-body">

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#request_add">Add New Request</a></li>
                <?php if (isset($_GET["action"]) && $_GET["action"] == "rview") { ?> <li><a id="viewrequest_tag" data-toggle="tab" href="#request_view">View Request <span id="request-num-val"></span></a></li>
                <?php } ?>
            </ul>

            <div class="tab-content">

                <div id="request_add" class="tab-pane fade in active">
                    <div id="reportModal" style="margin-top: 10px;">
                        <div class="panel panel-primary">

                            <div class="panel-heading">Create a new Request</div>

                            <div class="panel-body">

                                <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                                    <li role="presentation" class="active"><a id="report_details_tag" href="#report_details" aria-controls="report_details" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Task Details"); ?></a>
                                    </li>
                                    <li role="presentation"><a class="remove-for-tenant" id="recipient_report_tag" href="#recipient_report" aria-controls="recipient_report" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Recipients / Vendors"); ?></a>
                                    </li>
                                    <li role="presentation"><a id="pictures_new_report_tag" class="removeForFixedEventType" href="#pictures_new_report" aria-controls="pictures_new_report" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Pictures"); ?></a></li>
                                    <li role="presentation"><a id="additional_info_new_report_tag" href="#additional_info_new_report" aria-controls="additional_info_new_report" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Additional Details"); ?></a>
                                    </li>
                                </ul>

                                <div class="tab-content" style="margin-top: 10px;">

                                    <div role="tabpanel" class="tab-pane active" id="report_details">
                                        <div class="reportAnIssue">
                                            <div class="form">
                                                <form id="reportIssue" enctype="multipart/form-data">
                                                    <div class="row remove-for-tenant form-group">
                                                        <div class="col-sm-12" id="newTaskType">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Type of Task"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <label class="radio-inline">
                                                                        <input id="report-tasktypeFixed" class="edit-input request-newreportTasktype" type="radio" name="newreportTasktype" value="1"> Fixed Event
                                                                        <input type="checkbox" id="is_regular" name="is_regular" checked style="display:none;">
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <input id="report-tasktypeRequest" class="edit-input request-newreportTasktype" type="radio" name="newreportTasktype" value="0" checked> Request
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <input id="report-tasktypeProject" class="edit-input request-newreportTasktype" type="radio" name="newreportTasktype" value="2"> Project
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group removeForRequestTaskType">
                                                        <div class="col-sm-12">
                                                            <div>
                                                                <label class="edit-label col-sm-4 col-md-3" for="event_category"><?php echo $DB_snapshot->echot("Building"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <select class="form-control" name="building_id" id="building_id" required>
                                                                        <option value="0">Select a Building</option>
                                                                        <?php
                                                                        $allBuildings = $DB_building->getAllBdRows();
                                                                        foreach ($allBuildings as $singleBuilding) {
                                                                            if ($singleBuilding["company_id"] != $companyId) {
                                                                                continue;
                                                                            } ?>
                                                                            <option <?php
                                                                                    if (isset($_GET["building_id"])) {
                                                                                        if ($_GET["building_id"] == $singleBuilding["building_id"]) {
                                                                                            echo "selected";
                                                                                        }
                                                                                    } ?> value="<?php echo $singleBuilding["building_id"]; ?>">
                                                                                <?php echo $singleBuilding["building_name"]; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="projectTaskTypeOnlyShow" style="display: none;">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3" for="request-type"><?php echo $DB_snapshot->echot("Project Name"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <input type="text" name="project_name_newrequest" id="project_name_newrequest" class="form-control" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row removeForFixedEventType form-group">
                                                        <div class="col-sm-12 request-location-wrap" id="reportLocationWrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3" for="location"><?php echo $DB_snapshot->echot("Location"); ?></label>
                                                                <div id="reportLocation" class="col-sm-8 col-md-8 request-location-div">
                                                                    <select class="edit-input form-control report-location" id="reportBuilding" name="building">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3" for="request-type"><?php echo $DB_snapshot->echot("Task Type"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <select class="edit-select form-control request-type" id="report-request-type" name="reportRequestType" title="Type">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <div class="col-sm-8 col-sm-offset-4">
                                                                    <button data-target-tenant="pictures_new_report_tag" data-target="recipient_report_tag" href="#recipient_report" aria-controls="recipient_report" role="tab" data-toggle="tab" type="button" class="btn btn-warning reportNewTaskNextBtn" id="reportNewTaskDetailNext"><?php echo $DB_snapshot->echot("Next"); ?>
                                                                        <i class="fas fa-arrow-right"></i></button>
                                                                    <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12">
                                                            <div class="alert alert-success" id="create_task_success" style="display: none;"> </div>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="create_event" />
                                                    <input type="hidden" name="ajax_create" />
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane remove-for-tenant" id="recipient_report">

                                        <div class="recipientReport-div panel panel-default">
                                            <div class="panel-heading">Add Vendors to this Task</div>
                                            <div class="panel-body">

                                                <form id="editRecipientReport">
                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-vendor-speciality-wrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Speciality"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <?php $specialityTypes = $DB_request->get_request_types_all(); ?>
                                                                    <select class="form-control" id="recipient-report-vendor-speciality" name="recipient-report-vendor-speciality">
                                                                        <option value="default">Select Speciality
                                                                        </option>
                                                                        <?php
                                                                        foreach ($specialityTypes as $speciality) {
                                                                            echo "<option value='" . $speciality["id"] . "'> $speciality[name] </option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-vendor-speciality-level-wrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Speciality Level"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <?php $specialityLevels = $DB_vendor->getVendorSpecialityLevels(); ?>
                                                                    <select class="form-control" id="recipient-report-vendor-speciality-level" name="recipient-report-vendor-speciality-level">
                                                                        <option value="default">Select Speciality Level
                                                                        </option>
                                                                        <?php
                                                                        foreach ($specialityLevels as $specialityLevel) {
                                                                            echo "<option value='" . $specialityLevel["id"] . "'> $specialityLevel[name] </option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-vendor-type-wrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Type of Vendor"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <?php $vendorTypes = $DB_vendor->getVendorTypes(); ?>
                                                                    <select class="form-control" id="recipient-report-vendor-type" name="recipient-report-vendor-type">
                                                                        <option value="default">Select Type</option>
                                                                        <?php
                                                                        foreach ($vendorTypes as $vendor) {
                                                                            echo "<option value='" . $vendor["id"] . "'> $vendor[name] </option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-vendor-license-wrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Licenses"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <?php $licenseTypes = $DB_vendor->getLicenseTypes(); ?>
                                                                    <select class="form-control" id="recipient-report-license-type" name="recipient-report-license-type">
                                                                        <option value="default">Select Licenses</option>
                                                                        <?php
                                                                        foreach ($licenseTypes as $licenses) {
                                                                            echo "<option value='" . $licenses["id"] . "'> $licenses[name] </option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-vendor-wrap" style="display:none;">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Vendors"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <select class="form-control" id="recipient-report-vendor" name="recipient-report-vendor"></select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="recipient-report-estimatedprice-wrap" style="display:none;">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Estimated Price (CAD)"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <input type="text" name="recipient-vendor-estimatedprice" id="recipient-vendor-estimatedprice" class="form-control" value="0" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div class="recipientReport-alert alert alert-info" style="display: none;"></div>

                                            </div>
                                        </div>

                                        <div class="handymanReport-div panel panel-default">
                                            <div class="panel-heading">Add Handyman to this Task</div>
                                            <div class="panel-body">

                                                <form id="editHandymanReportForm">

                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="handyman-report-wrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Handyman"); ?></label>
                                                                <div class="col-sm-7">
                                                                    <?php $handymen = $DB_employee->getHandyman(); ?>
                                                                    <select class="form-control" id="handyman-report-select" name="handyman-report-select">
                                                                        <option value="default">Select Handyman</option>
                                                                        <?php
                                                                        foreach ($handymen as $handymanSingle) {
                                                                            echo "<option value='" . $handymanSingle["employee_id"] . "'> $handymanSingle[full_name] </option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>

                                        <div class="panel panel-default">
                                            <div class="panel-heading"> Attach Invoices / Quotation </div>
                                            <div class="panel-body">
                                                <form id="newRequest_invoicefilesForm" method="POST" enctype="multipart/form-data">
                                                    <div class="form-group row fileupload-buttonbar">
                                                        <div class="col-lg-7">
                                                            <span class="btn btn-info fileinput-button">
                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                <span>Add files...</span>
                                                                <input type="file" name="newRequest_invoicefiles[]" id="newRequest_invoicefiles" multiple>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <span id="newRequest_invoicefiles_list" class="panel" style="display: none;">
                                                        <h4>Files to Upload:</h4>
                                                        <ol></ol>

                                                        <button type="submit" class="btn btn-primary start">
                                                            <i class="glyphicon glyphicon-upload"></i>
                                                            <span>Start upload</span>
                                                        </button>
                                                        <span class="fileupload-process"></span>
                                                    </span>
                                                </form>

                                                <div style="display:none" class="form-group" id="newRequest_invoicefiles_alert">
                                                    <span class="alert alert-success">Invoices have been successfully
                                                        uploaded!</span>
                                                </div>


                                            </div>
                                        </div>

                                        <div class="materialReport-div panel panel-default">
                                            <div class="panel-heading">Material for this Task</div>
                                            <div class="panel-body">
                                                <form id="editMaterialReport" action="#" method="POST">
                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="materialProviderWrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Provided By?"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <!--  Radio Values according the database table 'material_provider' -->
                                                                    <label class="radio-inline">
                                                                        <input id="report-materialprovidervendor" class="edit-input request-material-provider" type="radio" name="reportMaterialprovider" value="2">Vendor
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <input id="report-materialproviderowner" class="edit-input request-material-provider" type="radio" name="reportMaterialprovider" value="1" checked> Owner
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="material_outer_wrapper">
                                                        <div id="material_detail_wrap">
                                                            <div class="row form-group">
                                                                <div class="col-md-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <input type="text" class="form-control" name="request_material[]" placeholder="Material Detail" />
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <select class="form-control" name="request_material_purchase_shop[]">
                                                                                <option value="0"> Select a Shop
                                                                                </option>
                                                                                <?php
                                                                                $allStores  = $DB_request->getOnlineStores();
                                                                                foreach ($allStores as $store) { ?>
                                                                                    <option value="<?php echo $store["id"]; ?>">
                                                                                        <?php echo $store["name"]; ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input type="text" class="form-control" name="request_material_purchase_url[]" placeholder="Material Online URL" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Material detail boxes will be appended dynamically below this line -->
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-8 col-md-8" id="reportMaterialWrap">
                                                                        <button type="button" id="addMoreMaterial" class="btn btn-primary "> <i class="fas fa-plus"></i>
                                                                            Material</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div id="add_more_material_proto" style="display:none;">
                                                    <div class="form-group material-wrap-main">
                                                        <div class="row form-group">
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" name="request_material[]" placeholder="Material Detail" />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select class="form-control" name="request_material_purchase_shop[]">
                                                                    <option value="0"> Select a Shop</option>
                                                                    <?php
                                                                    $allStores  = $DB_request->getOnlineStores();
                                                                    foreach ($allStores as $store) { ?>
                                                                        <option value="<?php echo $store["id"]; ?>">
                                                                            <?php echo $store["name"]; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" name="request_material_purchase_url[]" placeholder="Material Online URL" />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button class="btn btn-danger remove-material-detail">
                                                                    <i class="fa fa-remove"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-4">
                                                        <button data-target="pictures_new_report_tag" type="button" class="btn btn-warning reportNewTaskNextBtn"><?php echo $DB_snapshot->echot("Next"); ?>
                                                            <i class="fas fa-arrow-right"></i></button>
                                                        <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="invoices_report">

                                        <div class="panel panel-default">
                                            <div class="panel-heading"> Attach Invoices </div>
                                            <div class="panel-body">
                                                <form id="newRequest_invoicefilesForm" method="POST" enctype="multipart/form-data">
                                                    <div class="form-group row fileupload-buttonbar">
                                                        <div class="col-lg-7">
                                                            <span class="btn btn-info fileinput-button">
                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                <span>Add files...</span>
                                                                <input type="file" name="newRequest_invoicefiles[]" id="newRequest_invoicefiles" multiple>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <span id="newRequest_invoicefiles_list" class="panel" style="display: none;">
                                                        <h4>Files to Upload:</h4>
                                                        <ol></ol>

                                                        <button type="submit" class="btn btn-primary start">
                                                            <i class="glyphicon glyphicon-upload"></i>
                                                            <span>Start upload</span>
                                                        </button>
                                                        <span class="fileupload-process"></span>
                                                    </span>
                                                </form>
                                            </div>
                                        </div>

                                        <div style="display:none" class="form-group" id="newRequest_invoicefiles_alert">
                                            <span class="alert alert-success">Invoices have been successfully
                                                uploaded!</span>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-4">
                                                        <button data-target="additional_info_new_report_tag" type="button" class="btn btn-warning reportNewTaskNextBtn"><?php echo $DB_snapshot->echot("Next"); ?>
                                                            <i class="fas fa-arrow-right"></i></button>
                                                        <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="additional_info_new_report">

                                        <form id="report_modaladditional_info_form">
                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12 form-group">
                                                    <label class="edit-label col-sm-4 col-md-3" for="event_name"><?php echo $DB_snapshot->echot("Event Name"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <input class="form-control" name="event_name" id="event_name" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12 form-group">
                                                    <label class="edit-label col-sm-4 col-md-3" for="event_category"><?php echo $DB_snapshot->echot("Event Category"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <select class="form-control" name="event_category" id="event_category" required>
                                                            <option value="office" selected>Office</option>
                                                            <option value="maintenance">Maintenance</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12 form-group">
                                                    <label class="edit-label col-sm-4 col-md-3" for="regular_start_date"><?php echo $DB_snapshot->echot("Date and Time for this Event"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <input type="text" class="form-control date_input" name="regular_start_date" id="regular_start_date" placeholder="YYYY-MM-DD">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12 form-group">
                                                    <label class="edit-label col-sm-4 col-md-3" for="regular_frequency"><?php echo $DB_snapshot->echot("Frequency"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <select class="form-control" name="regular_frequency" id="regular_frequency">
                                                            <option value="day">Daily</option>
                                                            <option value="week">Weekly</option>
                                                            <option value="month">Monthly</option>
                                                            <option value="3months">Every 3 Months</option>
                                                            <option value="6months">Every 6 Months</option>
                                                            <option value="year">Yearly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12">
                                                    <label class="edit-label col-sm-4 col-md-3" for="contact_number"><?php echo $DB_snapshot->echot("Contact Number"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <input class="form-control" name="contact_number" id="contact_number" pattern="[\+]\d{11}" placeholder="999-999-9999" maxlength="15">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row remove-for-tenant removeForFixedEventType">
                                                <div class="col-sm-12 col-sm-12 form-group mt-20" id="setTaskDateTimeWrap">
                                                    <div class="form-group">
                                                        <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Set Task Date/Time ?"); ?></label>
                                                        <div class="col-sm-8 col-md-8">
                                                            <label class="radio-inline">
                                                                <input id="report-settaskdatetime" class="edit-input request-settask-datetime" type="radio" name="isRequestSetTaskDateTime" value="1">Yes
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input id="report-donotsettaskdatetime" class="edit-input request-settask-datetime" type="radio" name="isRequestSetTaskDateTime" value="0" checked>No
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row remove-for-tenant taskdatetimeFormInput removeForFixedEventType" style="display: none;">
                                                <div class="col-sm-12 col-sm-12 form-group">
                                                    <div class="form-group">
                                                        <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Date and Time for this Task"); ?></label>
                                                        <div class="col-sm-4 col-md-4">
                                                            <input id="requestSetTaskDateTimePickerFrom" type="text" class="edit-input form-control" name="requestSetTaskDateTimeFrom" placeholder="Select a start time for the task" />
                                                        </div>
                                                        <div class="col-sm-4 col-md-4">
                                                            <input id="requestSetTaskDateTimePickerTo" type="text" class="edit-input form-control" name="requestSetTaskDateTimeTo" placeholder="Select an end time for the task" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row removeForFixedEventType">
                                                <div class="col-sm-12 form-group">
                                                    <div class="form-group">
                                                        <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Message"); ?></label>
                                                        <div class="col-sm-8 col-md-8">
                                                            <textarea id="reportMessage" class="edit-input form-control" name="reportMessage" rows="4" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12">
                                                    <label class="edit-label col-sm-4 col-md-3" for="event_info"><?php echo $DB_snapshot->echot("Event Information"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <textarea class="form-control" name="event_info" rows="5" id="event_info" placeholder="Event location, Event description, Event purpose, Preparing works"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group removeForRequestTaskType">
                                                <div class="col-sm-12">
                                                    <label class="edit-label col-sm-4 col-md-3" for="principals_assigned"><?php echo $DB_snapshot->echot("Principal Assigned"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <select name="principals_assigned[]" multiple class="form-control" style="height: 115px;">
                                                            <?php
                                                            $staff = $DB_calendar->get_same_company_staff($employee_id);
                                                            foreach ($staff as $row) {
                                                                $id = $row['employee_id'];
                                                                $name = $row['full_name'] . '   ';
                                                                $email = $row['email'] . '   ';
                                                                $phone_number = $row['mobile'];
                                                                echo "<option value=\"$id\">$name &nbsp;&nbsp; $email &nbsp;&nbsp; $phone_number</option>";
                                                            }
                                                            ?>
                                                            <option onclick="disselection()">
                                                                <--- Disselect all selections --->
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel panel-default removeForRequestTaskType">
                                                <div class="panel-heading">Notification</div>
                                                <div class="panel-body">
                                                    <div class="row form-group ">
                                                        <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Notification Preference"); ?></label>
                                                        <div class="col-sm-8 col-md-8">
                                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="sms_notif">SMS</label>
                                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="email_notif">Email</label>
                                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="voice_notif">Voice</label>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <label class="edit-label col-md-3"><?php echo $DB_snapshot->echot("Receive Notification : "); ?></label>
                                                        <div class="col-md-5">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"> <strong> Before
                                                                    </strong></span>
                                                                <input type="text" class="form-control" id="notification_when" name="notification_when">
                                                                <!--                                               <span class="hidden input-group-addon" id="notification_when_type_val"> <strong> Days </strong></span>-->
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" id="notification_when_type" name="notification_when_type">
                                                                <option value="day">Days</option>
                                                                <option value="month">Months</option>
                                                                <option value="week">Weeks</option>
                                                                <option value="year">Years</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row removeForFixedEventType">
                                                <div class="col-sm-12 request-notify-me-wrap form-group">
                                                    <div class="form-group">
                                                        <label class="edit-label col-sm-4 col-md-3 request-notify-label" id="notifyLabel"><?php echo $DB_snapshot->echot("Notify Me by"); ?></label>
                                                        <div class="col-sm-8 col-md-8">
                                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeByEmail" value="0"><input class="edit-input notify-me-by-email" type="checkbox" name="notifyMeByEmail" id="reportNotifyMeEmail" value="1"><?php echo $DB_snapshot->echot("Email"); ?></label>
                                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeBySms" value="0"><input class="edit-input notify-me-by-sms" type="checkbox" name="notifyMeBySms" id="reportNotifyMeSms" value="1"><?php echo $DB_snapshot->echot("SMS"); ?></label>
                                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeByVoice" value="0"><input class="edit-input notify-me-by-voice" type="checkbox" name="notifyMeByVoice" id="reportNotifyMeVoice" value="1"><?php echo $DB_snapshot->echot("Voice"); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group showOnlyIfInvoicesAttached" style="display:none;">
                                                <div class="col-sm-12">
                                                    <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Uploaded Invoices"); ?></label>
                                                    <div class="col-sm-8 col-md-8">
                                                        <ol id="invoicesAttachedNewRequest"></ol>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row removeForFixedEventType">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-8 col-sm-offset-4">
                                                            <button type="button" class="btn btn-warning" id="submitReport"><?php echo $DB_snapshot->echot("Report this request"); ?></button>
                                                            <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row removeForRequestTaskType">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-8 col-sm-offset-4">
                                                            <button id="create_fixed_event" type="button" class="btn btn-primary btn-long">Create Fixed
                                                                Event</button>
                                                            <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>

                                        <div class="row form-group" id="new_request_loader" style="display: none;">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-3">
                                                        <span class="alert alert-warning">
                                                            <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
                                                            Working on creating your request! Please wait.
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="pictures_new_report">
                                        <form id="reportNew_uploadImagesForm" action="#" method="POST" enctype="multipart/form-data">
                                            <div class="removeForFixedEventType panel panel-default">
                                                <div class="panel-heading">Add Pictures of the Task</div>
                                                <div class="panel-body">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">

                                                            <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Pictures"); ?></label>
                                                            <div class="col-sm-8 col-md-8" id="reportPicturesWrap">
                                                                <label class="btn btn-primary btn-file"><?php echo $DB_snapshot->echot("Select Images"); ?><input class="request-pic-upload" id="reportButton" type="file" name="file[]" style="display: none" accept="image/*" multiple></label>
                                                                <div class="report-location-margin-top" id="report_preview_imgs"></div>
                                                                <div id="reportNew_uploadImages" style="display: none;margin-top: 15px;">
                                                                    <button type="button" id="reportNew_uploadImages_btn" class="btn btn-primary start">
                                                                        <i class="glyphicon glyphicon-upload"></i>
                                                                        <span>Upload Picture</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-4">
                                                        <button data-target="additional_info_new_report_tag" data-target-tenant="additional_info_new_report_tag" type="button" class="btn btn-warning reportNewTaskNextBtn"><?php echo $DB_snapshot->echot("Next"); ?>
                                                            <i class="fas fa-arrow-right"></i></button>
                                                        <button class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Show the request view tab only if there are appropriate params-->
                <?php if (isset($_GET["action"]) && $_GET["action"] == "rview") { ?>
                    <div id="request_view" class="tab-pane fade">
                        <div id="iModal">
                            <div>
                                <div style="padding: 10px 16px">
                                    <div class="modal-apart-info">
                                        <img class="image modal-img" id="modal_building_img" src="files/requests/request_building_mask.jpeg">
                                        <div class="text">
                                            <span class="text-item-title box-text-bigger" id="modal_building">
                                                <?php echo $viewRequestData["building_name"]; ?></span>
                                            <span class="text-item box-text-bigger" id="modal_apart">
                                                <?php echo $viewRequestData["specific_area"]; ?> </span>
                                            <span class="text-item box-text-bigger" id="modal_address">
                                                <?php echo $viewRequestData["building_address"]; ?> </span>
                                        </div>
                                    </div>

                                    <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                                        <li role="presentation" class="active"><a id="communication_tag" href="#communication" aria-controls="communication" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Communications"); ?></a>
                                        </li>
                                        <!--      <li role="presentation"><a id="attach_tag" href="#attach" aria-controls="attach" role="tab" data-toggle="tab">--><?php //echo $DB_snapshot->echot("Attachments");
                                                                                                                                                                    ?>
                                        <!--</a></li>-->
                                        <li role="presentation"><a id="recipient_tag" href="#recipient" aria-controls="recipient" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Recipient"); ?></a></li>
                                        <li role="presentation"><a id="edit_tag" href="#edit" aria-controls="edit" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Order"); ?></a>
                                        </li>
                                        <li role="presentation"><a id="payment_tag" href="#payment_tab" aria-controls="payment_tab" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Payment"); ?></a></li>
                                        <li role="presentation"><a class="remove-for-tenant" id="materials_tag" href="#materialedit_tab" aria-controls="materialedit_tab" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Materials"); ?></a></li>
                                        <li role="presentation"><a class="remove-for-tenant" id="invoices_report_tag" href="#invoices_report_tab" aria-controls="invoices_report_tab" role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Attached Invoices"); ?></a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">

                                        <div role="tabpanel" class="tab-pane active" id="communication">
                                            <div class="communication-div">
                                                <div class="row">
                                                    <div id="communications" class="col-sm-12 col-md-12" style="overflow: auto; white-space: nowrap">
                                                        <!-- communications -->
                                                    </div>
                                                </div>
                                                <form enctype="multipart/form-data" id="post_communication_form">
                                                    <div class="row">
                                                        <div id="new_message" class="col-sm-12 form-group">
                                                            <div id="communiation_input" class="communication-input" contenteditable="true"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 form-group" id="opeartions_new_message">
                                                            <button type="button" class="btn btn-primary btn-post" id="post_communication" onclick="add_communication()"><?php echo $DB_snapshot->echot("Post"); ?></button>
                                                            <label class="btn btn-primary btn-post">Add Image<input class="request-pic-upload" type="file" name="upload_img" style="display: none" accept="image/*" onchange="add_communication_image()"></label>
                                                            <div class="checkbox remove-for-tenant modal-communication-selection">
                                                                <label><input type="checkbox" id="hide_for_tenant"><?php echo $DB_snapshot->echot("Hidden for tenants"); ?></label>
                                                            </div>
                                                            <div class="checkbox remove-for-tenant modal-communication-selection">
                                                                <label><input type="checkbox" id="force_ntf"><?php echo $DB_snapshot->echot("Notify recipients"); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="recipient" data-rid="<?php echo $request_id; ?>">
                                            <div class="recipient-div mt-20">
                                                <div class="form">
                                                    <form id="editRecipient">
                                                        <div class="row">

                                                            <div class="col-sm-12" id="recipient-employee-wrap">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Notice Sent to"); ?></label>
                                                                    <div class="col-sm-7">

                                                                        <div class="dropdown" id="recipient_dropdown_list">
                                                                            <button style="width:50%;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> List
                                                                                <span class="caret"></span></button>
                                                                            <ul id="recipient-employee" class="dropdown-menu" style="overflow:auto;max-height:260px;width:30%;">
                                                                                <?php
                                                                                $employees = $recipientsData["employees"];
                                                                                foreach ($employees as $employee) {
                                                                                    //if the employee is already assigned with this request, then check the box
                                                                                    if ($employee["assigned"] === 1) {
                                                                                        $checked = 'checked';
                                                                                    } else {
                                                                                        $checked = '';
                                                                                    }
                                                                                ?>
                                                                                    <li>
                                                                                        <a href="#" class="recipient_employee_a">
                                                                                            <label class="checkbox">
                                                                                                <input class="edit-input" type="checkbox" name="employee" value="<?php echo $employee["employee_id"]; ?> " <?php echo  $checked; ?> />
                                                                                                <?php echo $employee["full_name"]; ?>
                                                                                            </label>
                                                                                        </a>
                                                                                    </li>

                                                                                <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <?php $tenants = $recipientsData["tenants"];
                                                            if ($tenants) {  ?>
                                                                <div class="col-sm-12 tenant-recipient-wraps">
                                                                    <div class="form-group">
                                                                        <label class="edit-label col-sm-4">Tenants</label>
                                                                        <div class="col-sm-7">
                                                                            <table id="recipient-tenant">
                                                                                <?php

                                                                                if ($tenants) {
                                                                                    foreach ($tenants as $tenant) {
                                                                                        //if the tenant is already assigned with this request, then check the box
                                                                                        if ($tenant["assigned"] === 1) {
                                                                                            $checked = 'checked';
                                                                                        } else {
                                                                                            $checked = '';
                                                                                        }
                                                                                ?>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <label class="checkbox">
                                                                                                    <input class="edit-input" type="checkbox" name="tenant" value="<?php echo $tenant["tenant_id"]; ?>" <?php echo $checked; ?>>
                                                                                                    <?php echo $tenant["full_name"]; ?>
                                                                                                </label>
                                                                                            </td>
                                                                                        </tr>
                                                                                <?php }
                                                                                }
                                                                                ?>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>

                                                            <div class="col-sm-12 vendor-recipient-wraps">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4">Vendors</label>
                                                                    <div class="col-sm-7">
                                                                        <select id="recipient-vendor" class="selectizeThis">
                                                                            <option class="edit-input" name="vendor">
                                                                            </option>
                                                                            <?php
                                                                            $vendors = $recipientsData["vendors"];
                                                                            foreach ($vendors as $vendor) {
                                                                                //if the vendor is already assigned with this request, then check the box
                                                                                if ($vendor["assigned"] === 1) {
                                                                                    $vendor_selected = 'selected';
                                                                                } else {
                                                                                    $vendor_selected = '';
                                                                                }
                                                                                $vendorName = $vendor["full_name"];
                                                                                if (!$vendorName) {
                                                                                    $vendorName = $vendor["company_name"];
                                                                                }
                                                                                if ($vendor["vendor_type_id"] == 1) {
                                                                                    $vendorName = $vendor["company_name"];
                                                                                }
                                                                            ?>
                                                                                <option data-rid="<?php echo $request_id; ?>" class="edit-input" name="vendor" value="<?php echo $vendor["vendor_id"]; ?>" <?php echo $vendor_selected; ?>>
                                                                                    <?php echo $vendorName; ?> </option>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-8 col-sm-offset-4">
                                                                        <button id="saveRecipients" type="submit" class="btn btn-primary"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                                                        <button id="cancelRecipients" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="edit">
                                            <div class="edit-div">
                                                <div class="form">
                                                    <form id="edit-request">
                                                        <div class="row remove-for-tenant">
                                                            <div class="col-sm-12 request-location-wrap">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3" for="location"><?php echo $DB_snapshot->echot("Location"); ?></label>
                                                                    <div class="col-sm-8 col-md-8">
                                                                        <select class="edit-input form-control" id="editRequestLocationReportArea" name="editRequestLocationReportArea" required>
                                                                            <?php
                                                                            foreach ($locationReportArea as $value => $displayValue) {
                                                                                $locationReportAreaSelected = "";
                                                                                if ($reportLocationForRequest == $value) {
                                                                                    $locationReportAreaSelected = "selected";
                                                                                }
                                                                            ?>
                                                                                <option value="<?php echo $value; ?>" <?php echo $locationReportAreaSelected; ?>>
                                                                                    <?php echo $displayValue; ?></option>
                                                                            <?php }
                                                                            ?>
                                                                        </select>

                                                                        <select class="form-control form-group" id="editRequestLocationBuilding" name="editRequestLocationBuilding">
                                                                            <option value="all">All Buildings</option>
                                                                            <?php
                                                                            foreach ($filter_building_list as $r) {
                                                                                $building_selected = "";
                                                                                if ($r['building_id'] == $orderData["building_id"]) {
                                                                                    $building_selected = "selected";
                                                                                } ?>
                                                                                <option value="<?php echo $r['building_id']; ?>" <?php echo $building_selected; ?>>
                                                                                    <?php echo $r['building_name']; ?></option>
                                                                            <?php }
                                                                            ?>
                                                                        </select>

                                                                        <?php
                                                                        $commonAreaDisplay = "display:none;";
                                                                        if (isset($orderData["location"]) && $orderData["location"] == "common area") {
                                                                            $commonAreaDisplay = "";
                                                                        }
                                                                        ?>
                                                                        <textarea style="<?php echo $commonAreaDisplay; ?>" class="form-control" id="editRequestLocationCommonArea" name="editRequestLocationCommonArea" rows="1" placeholder="Specific Common Area (e.g. Elevator No.2)">
                                                                            <?php
                                                                            echo (isset($orderData["common_area_detail"])) ? ltrim($orderData["common_area_detail"]) : "";
                                                                            ?>
                                                                        </textarea>

                                                                        <?php
                                                                        $floorDisplayInit = "display:none;";
                                                                        if (isset($orderData["floor_id"]) && !empty($orderData["floor_id"])) {
                                                                            $floorDisplayInit = "";
                                                                            $floorData = $requestHandler->get_floors($orderData["building_id"]);
                                                                        } ?>
                                                                        <select style="<?php echo $floorDisplayInit; ?>" class="form-control" id="editRequestLocationFloor" name="editRequestLocationFloor">
                                                                            <?php
                                                                            if (isset($floorData)) {
                                                                                foreach ($floorData as $floor) {
                                                                                    $floorSelected = "";
                                                                                    if ($floor["floor_id"] == $orderData["floor_id"]) {
                                                                                        $floorSelected = "selected";
                                                                                    }
                                                                            ?>
                                                                                    <option value="<?php echo $floor["floor_id"]; ?>" <?php echo $floorSelected; ?>>
                                                                                        <?php echo $floor["floor_name"]; ?></option>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>

                                                                        <?php
                                                                        $aptDisplayInit = "display:none;";
                                                                        if (isset($orderData["floor_id"]) && !empty($orderData["floor_id"])) {
                                                                            $aptDisplayInit = "";
                                                                            $aptData = $requestHandler->get_apartments($orderData["floor_id"]);
                                                                        } ?>
                                                                        <select style="<?php echo $floorDisplayInit; ?>" class="form-control form-group" id="editRequestLocationApt" name="editRequestLocationApt">
                                                                            <?php
                                                                            if (isset($aptData)) {
                                                                                foreach ($aptData as $apt) {
                                                                                    $aptSelected = "";
                                                                                    if ($apt["apartment_id"] == $orderData["apartment_id"]) {
                                                                                        $aptSelected = "selected";
                                                                                    }
                                                                            ?>
                                                                                    <option value="<?php echo $apt["apartment_id"]; ?>" <?php echo $aptSelected; ?>>
                                                                                        <?php echo $apt["unit_number"]; ?></option>
                                                                            <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row remove-for-tenant">
                                                            <div class="col-sm-12 request-datetimeevent-wrap">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Date and Time"); ?></label>
                                                                    <div class="col-sm-4 col-md-4">
                                                                        <input value="<?php echo (isset($orderData["datetime_from"])) ? $orderData["datetime_from"] : ""; ?>" id="reportEditDateTimeFrom" type="text" class="edit-input form-control" name="reportEditDateTimeFrom" placeholder="Select a start time for the task" />
                                                                    </div>
                                                                    <div class="col-sm-4 col-md-4">
                                                                        <input value="<?php echo (isset($orderData["datetime_to"])) ? $orderData["datetime_to"] : ""; ?>" id="reportEditDateTimeTo" type="text" class="edit-input form-control" name="reportEditDateTimeTo" placeholder="Select a end time for the task" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3" for="request-type"><?php echo $DB_snapshot->echot("Request Type"); ?></label>
                                                                    <div class="col-sm-8 col-md-8 request-type-div">
                                                                        <?php
                                                                        $requestTypes = $reportReadyData["request_types"];
                                                                        ?>
                                                                        <select class="edit-select form-control request-type" id="request-type" name="request_type" title="Type">
                                                                            <?php
                                                                            foreach ($requestTypes as $type) {
                                                                                $requestTypeSelected = "";
                                                                                if ($type["id"] == $orderData["request_type_id"]) {
                                                                                    $requestTypeSelected = "selected";
                                                                                }
                                                                            ?>
                                                                                <option value="<?php echo $type["id"]; ?>" <?php echo $requestTypeSelected; ?>>
                                                                                    <?php echo $type["name"]; ?> </option>
                                                                            <?php }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Message"); ?></label>
                                                                    <div class="col-sm-8 col-md-8 request-message-div">
                                                                        <textarea id="editMessage" class="edit-input form-control request-message" name="message" rows="4" required>
                                                                            <?php echo (isset($orderData["message"])) ? $orderData["message"] : ""; ?>
                                                                        </textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12 request-status-wrap">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Status"); ?></label>
                                                                    <div class="col-sm-8 col-md-8 request-status-div">
                                                                        <select class="edit-select form-control request-status" id="request-status" name="request_status" title="Status">
                                                                            <?php
                                                                            $request_status_values = $reportReadyData["request_status"];
                                                                            foreach ($request_status_values as $status_value) {
                                                                                $status_selected = "";
                                                                                if ($status_value["id"] == $orderData["status_id"]) {
                                                                                    $status_selected = "selected";
                                                                                }
                                                                            ?>
                                                                                <option value="<?php echo $status_value["id"] ?>" <?php echo $status_selected; ?>>
                                                                                    <?php echo $status_value["name"]; ?>
                                                                                </option>
                                                                            <?php }  ?>
                                                                            <option disabled="">
                                                                                ------------------------------------------------------
                                                                            </option>
                                                                            <?php
                                                                            if ($user_level != 5) {
                                                                                $request_status_values2 = $reportReadyData["request_status_2"];
                                                                                foreach ($request_status_values2 as $status_value) {
                                                                                    $status_selected2 = "";
                                                                                    if ($status_value["id"] == $orderData["status_id"]) {
                                                                                        $status_selected2 = "selected";
                                                                                    }
                                                                            ?>
                                                                                    <option value="<?php echo $status_value["id"] ?>" <?php echo $status_selected2; ?>>
                                                                                        <?php echo $status_value["name"]; ?>
                                                                                    </option>
                                                                            <?php }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12 request-notify-me-wrap">
                                                                <div class="form-group">
                                                                    <label class="edit-label col-sm-4 col-md-3 request-notify-label" id="notifyLabel"><?php echo $DB_snapshot->echot("Notify Me by"); ?></label>
                                                                    <div class="col-sm-8 col-md-8">
                                                                        <label class="checkbox-inline">
                                                                            <!--                                                                            <input type="hidden" name="notifyMeByEmail" value="0">-->
                                                                            <input <?php echo (isset($orderData["notify_by_email"]) && intval($orderData["notify_by_email"]) == 1) ? "checked" : ""; ?> class="edit-input notify-me-by-email" type="checkbox" name="notifyMeByEmail" id="editNotifyMeEmail" value="1"><?php echo $DB_snapshot->echot("Email"); ?>
                                                                        </label>
                                                                        <label class="checkbox-inline">
                                                                            <!--                                                                            <input type="hidden" name="notifyMeBySms" value="0">-->
                                                                            <input <?php echo (isset($orderData["notify_by_sms"]) && intval($orderData["notify_by_sms"]) == 1) ? "checked" : ""; ?> class="edit-input notify-me-by-sms" type="checkbox" name="notifyMeBySms" id="editNotifyMeSms" value="1"><?php echo $DB_snapshot->echot("SMS"); ?>
                                                                        </label>
                                                                        <label class="checkbox-inline">
                                                                            <!--                                                                            <input type="hidden" name="notifyMeByVoice" value="0">-->
                                                                            <input <?php echo (isset($orderData["notify_by_voice"]) && intval($orderData["notify_by_voice"]) == 1) ? "checked" : ""; ?> class="edit-input notify-me-by-voice" type="checkbox" name="notifyMeByVoice" id="editNotifyMeVoice" value="1"><?php echo $DB_snapshot->echot("Voice"); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-8 col-sm-offset-4">
                                                                        <button type="button" class="btn btn-primary" id="saveEdit"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelEdit"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="request_op_success_closed" class="row" style="display: none;">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-8 col-sm-offset-2 alert alert-success alert-dismissible fade in">
                                                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                                        <span>Request is saved! Please update your Payment
                                                                            details.</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="payment_tab">
                                            <div class="row form-group mt-20">
                                                <div class="col-sm-12">
                                                    <div id="payment_tab_alert"></div>
                                                </div>
                                            </div>
                                            <div id="payment_tab_content">
                                                <form id="edit-payment">
                                                    <legend>Payment Details</legend>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12 request-payestimatedprice-wrap">
                                                            <div class="form-group">
                                                                <label class="col-sm-4 col-md-3" for="request_pay_perhr"><?php echo $DB_snapshot->echot("Estimated Price (CAD)"); ?></label>
                                                                <div class="col-sm-4 col-md-4  request-paymentestimatedprice-div">
                                                                    <input name="request_pay_estimatedprice" class="form-control" id="request_pay_estimatedprice" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12 request-payperhr-wrap">
                                                            <div class="form-group">
                                                                <label class="col-sm-4 col-md-3" for="request_pay_perhr"><?php echo $DB_snapshot->echot("Per Hour Wage"); ?></label>
                                                                <div class="col-sm-4 col-md-4 request-paymentperhr-div">
                                                                    <input name="request_pay_perhr" class="form-control" id="request_pay_perhr" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12 request-payinfo-wrap">
                                                            <div class="form-group">
                                                                <label class="col-sm-4 col-md-3" for="request_payinfo"><?php echo $DB_snapshot->echot("Job Detail"); ?></label>
                                                                <div class="col-sm-4 col-md-4  request-paymentinfo-div">
                                                                    <input class="form-control" id="request_payinfo" name="request_payinfo">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12 request-payhours-wrap">
                                                            <div class="form-group">
                                                                <label class="col-sm-4 col-md-3" for="request_payhours"><?php echo $DB_snapshot->echot("Job Total Hours"); ?></label>
                                                                <div class="col-sm-4 col-md-4 request-paymenthours-div">
                                                                    <input class="form-control" id="request_payhours" name="request_payhours">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-sm-12 request-payexpenses-wrap">
                                                            <div class="form-group">
                                                                <label class="col-sm-4 col-md-3" for="request_pay_expenses"><?php echo $DB_snapshot->echot("Other Expenses Amount"); ?></label>
                                                                <div class="col-sm-4 col-md-4 request-paymentexpenses-div">
                                                                    <input class="form-control" id="request_pay_expenses" name="request_pay_expenses">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="file-holder-payments"></div>
                                                </form>
                                                <div class="row form-group">
                                                    <div class="col-sm-12 request-payinvoice-wrap">
                                                        <div class="form-group">
                                                            <label class="col-sm-4 col-md-3" for="request_pay_invoice"><?php echo $DB_snapshot->echot("Attach Invoice"); ?></label>
                                                            <div class="col-sm-4 col-md-4 request-paymentinvoice-div">
                                                                <form id="paymentinv_form" enctype="multipart/form-data">
                                                                    <input name="paymentinvoicenum" id="paymentinvoicenum" placeholder="Invoice #" type="text" class="form-control" />
                                                                    <input name="paymentinvoicefile" id="paymentinvoicefile" type="file" style="margin-top:10px;" />
                                                                </form>
                                                                <progress style="display: none;" id="paymentinv_form_progress"></progress>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <div class="col-sm-8 col-sm-offset-3">
                                                                <button type="button" class="btn btn-primary" id="savePaymentdetails"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  Admin Approval -->
                                                <form id="payment_approval">
                                                    <legend>Payment Detail Validation</legend>
                                                    <h5 id="request_approve_alert"></h5>
                                                    <div id="request-payapprove-form-div">
                                                        <div class="row form-group">
                                                            <div class="col-sm-12 request-payapprove-wrap">
                                                                <div class="form-group">
                                                                    <label class="col-sm-4 col-md-3" for="request_is_payapprove"><?php echo $DB_snapshot->echot("Approve the Payment"); ?></label>
                                                                    <div class="col-sm-8 col-md-8 request_is_payapprove-div">
                                                                        <input type="checkbox" name="request_is_payapprove" id="request_is_payapprove" value="1"> Yes
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-sm-12 request-payfinalamt-wrap">
                                                                <div class="form-group">
                                                                    <label class="col-sm-4 col-md-3" for="request_approve_finalamt"><?php echo $DB_snapshot->echot("Final Amount"); ?></label>
                                                                    <div class="col-sm-4 col-md-4 request_approve_finalamt-div">
                                                                        <input type="text" class="form-control" name="request_approve_finalamt" id="request_approve_finalamt" placeholder="Enter the final amount to Pay">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-sm-12 request-payapprovecomments-wrap">
                                                                <div class="form-group">
                                                                    <label class="col-sm-4 col-md-3" for="request_approve_comments"><?php echo $DB_snapshot->echot("Comments"); ?></label>
                                                                    <div class="col-sm-4 col-md-4  request_approve_finalamt-div">
                                                                        <textarea class="form-control" name="request_approve_comments" id="request_approve_comments" placeholder=""></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-8 col-sm-offset-3">
                                                                        <button type="button" class="btn btn-primary" id="savePaymentApproval"><?php echo $DB_snapshot->echot("Confirm Approval"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="materialedit_tab">
                                            <div class="editRequestmaterialReport-div mt-20">
                                                <form id="editRequestMaterialReport" action="#" method="POST">
                                                    <div class="row form-group">
                                                        <div class="col-sm-12" id="editRequestmaterialProviderWrap">
                                                            <div class="form-group">
                                                                <label class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Provided By?"); ?></label>
                                                                <div class="col-sm-8 col-md-8">
                                                                    <!--  Radio Values according the database table 'material_provider' -->
                                                                    <?php
                                                                    $material_provider_selected = null;
                                                                    if (!empty($materialsData)) {
                                                                        $material_provider_selected = $materialsData["material_provider"];
                                                                    } ?>

                                                                    <label class="radio-inline">
                                                                        <input <?php echo (isset($material_provider_selected) && $material_provider_selected == 2) ? "checked" : ""; ?> id="editRequest-materialprovidervendor" class="edit-input editRequest-material-provider" type="radio" name="editRequestMaterialprovider" value="2">Vendor
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <input <?php echo (isset($material_provider_selected) && $material_provider_selected == 1) ? "checked" : ""; ?> id="editRequest-materialproviderowner" class="edit-input editRequest-material-provider" type="radio" name="editRequestMaterialprovider" value="1" checked> Owner
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="editRequestmaterial_detail_wrap_outer">
                                                        <div id="editRequestmaterial_detail_wrap">
                                                            <?php
                                                            if (empty($materialsData)) { ?>
                                                                <div id="editRequestmaterial_existing_row_input" class="row form-group">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-4">
                                                                                <input type="text" class="form-control" name="editRequest_material[]" placeholder="Material Detail" />
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <select class="form-control" name="editRequest_material_purchase_shop[]">
                                                                                    <option value="0"> Select a Shop</option>
                                                                                    <?php
                                                                                    $allStores  = $DB_request->getOnlineStores();
                                                                                    foreach ($allStores as $store) { ?>
                                                                                        <option value="<?php echo $store["id"]; ?>">
                                                                                            <?php echo $store["name"]; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input type="text" class="form-control" name="editRequest_material_purchase_url[]" placeholder="Material Online URL" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>

                                                            <div id="editRequestmaterial_detail_wrap_inner">
                                                                <?php
                                                                if (!empty($materialsData)) {
                                                                    $materialDataArray = json_decode($materialsData["material_detail"], true);

                                                                    foreach ($materialDataArray as $material) { ?>

                                                                        <div class="form-group material-wrap-main">
                                                                            <div class="row form-group">
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control" name="editRequest_material[]" placeholder="Material Detail" value="<?php echo $material["material_name"]; ?>" />
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <select class="form-control" name="editRequest_material_purchase_shop[]">
                                                                                        <option value="0"> Select a Shop</option>
                                                                                        <?php
                                                                                        $allStores  = $DB_request->getOnlineStores();
                                                                                        foreach ($allStores as $store) {
                                                                                            $material_online_store_id_selected = "";
                                                                                            if ($material["material_online_store_id"] == $store["id"]) {
                                                                                                $material_online_store_id_selected = "selected";
                                                                                            }
                                                                                        ?>
                                                                                            <option value="<?php echo $store["id"]; ?>" <?php echo $material_online_store_id_selected; ?>>
                                                                                                <?php echo $store["name"]; ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <input type="text" class="form-control" name="editRequest_material_purchase_url[]" placeholder="Material Online URL" value="<?php echo $material["material_url"]; ?>" />
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <button class="btn btn-danger remove-material-detail">
                                                                                        <i class="fa fa-remove"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                <?php }
                                                                } ?>
                                                            </div>

                                                            <!-- Material detail boxes will be appended dynamically below this line -->

                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-sm-6">
                                                                        <button type="button" id="editRequestaddMoreMaterial" class="btn btn-primary "> <i class="fas fa-plus"></i> Material</button>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <button type="button" id="editRequestmaterialDetailsSave" class="btn btn-info "> <i class="far fa-arrow-from-left"></i> Save
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div id="editRequestadd_more_material_proto" style="display:none;">
                                                    <div class="form-group material-wrap-main">
                                                        <div class="row form-group">
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" name="editRequest_material[]" placeholder="Material Detail" />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select class="form-control" name="editRequest_material_purchase_shop[]">
                                                                    <option value="0"> Select a Shop</option>
                                                                    <?php
                                                                    $allStores  = $DB_request->getOnlineStores();
                                                                    foreach ($allStores as $store) { ?>
                                                                        <option value="<?php echo $store["id"]; ?>">
                                                                            <?php echo $store["name"]; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" name="editRequest_material_purchase_url[]" placeholder="Material Online URL" />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button class="btn btn-danger remove-material-detail"> <i class="fa fa-remove"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="invoices_report_tab">
                                            <?php
                                            if (!empty($invoicesData)) {
                                                $invoicesDataArray = json_decode($invoicesData, true);
                                            ?>
                                                <div class="panel panel-primary mt-20">
                                                    <div class="panel-heading">Attached Invoices List</div>
                                                    <div class="panel-body">
                                                        <ol id="report_edit_attachedInvoices_ol">
                                                            <?php if (count($invoicesDataArray) > 0) {
                                                                foreach ($invoicesDataArray as $invoice) { ?>
                                                                    <li> <?php echo $invoice; ?> <a target="_blank" href="<?php echo "files/requests/$invoice"; ?>" class="btn-xs btn-primary">View</a> </li>
                                                            <?php }
                                                            } ?>
                                                        </ol>
                                                    </div>
                                                </div>

                                            <?php } else {
                                                echo " <div class='mt-20'> No invoices attached to this request </div>";
                                            }
                                            ?>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div>
    </div>
</div>

<input type="hidden" name="request_id" id="request_id_val" value="<?php echo (isset($_GET["rid"])) ? $_GET["rid"] : 0; ?>" />
<button id="startReport" style="display:none;"></button>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/table_style.css">
<link rel="stylesheet" href="css/request_info.css">
<link rel="stylesheet" href="css/lightbox.min.css">
<link href="css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/lightbox.min.js"></script>

<script>
    lightbox.option({
        'alwaysShowNavOnTouchDevices': true
    });

    $('.date_input').datepicker({
        format: 'y-MM-dd',
        autoclose: true
    });

    $(function() {

        $(document).on('click', '#recipient_dropdown_list .dropdown-menu', function(e) {
            e.stopPropagation();
        });

        $('.selectizeThis').selectize({
            sort: 'text'
        });

        $('#startReport').trigger('click');

        setTimeout(function() {
            if (location.search.indexOf('vid=') >= 0) {
                console.log("vendor id ");
                // Vendor ID exists - Show the Projects content straight away
                // vendor_id = (location.search.split('vid=')[1]||'').split('&')[0];
                $("#category-projects").trigger("click");
            }
        }, 10);


        $('[data-toggle="tooltip"]').tooltip();

        $(".reportNewTaskNextBtn").on("click", function() {
            let target = $(this).attr("data-target");
            if (user_level == 5 || user_level == "5") {
                // Get the target for the tenant
                target = $(this).attr("data-target-tenant");
            }
            $("#" + target).trigger("click");
        });

        $("#requestSetTaskDateTimePickerFrom,#requestSetTaskDateTimePickerTo,#reportEditDateTimeFrom,#reportEditDateTimeTo")
            .datetimepicker({
                format: 'dd-mm-yyyy hh:ii',
                autoclose: true
            }).on('show.bs.modal', function(event) {
                // prevent datepicker from firing bootstrap modal "show.bs.modal"
                event.stopPropagation();
            });

        // Show fixed events tab on Fixed event button click
        $("#category-fixed").on("click", function() {
            $("#fixed_events_tabli > a").toggleClass("hidden").trigger("click");
        });

        // Show projects tab on projects button click in the filters dropdown
        $("#category-projects").on("click", function() {
            $("#projects_tabli > a").toggleClass("hidden").trigger("click");
        });

        $(".removeForRequestTaskType").hide();

        $("#materialDetailsContinue").on("click", function() {
            $("#reportModal").find("#report_details_tag").trigger("click");
        });

        // Change of the notification when type event
        $("#notification_when_type").change(function() {
            // If default - hide the display value of the notification when type
            if ($(this).val() == "default") {
                $("#notification_when_type_val").addClass("hidden");
                return;
            }
            // Change the display value of the notification when type in the notification arrival time
            var text = $("#notification_when_type option:selected").text();
            $("#notification_when_type_val").removeClass("hidden").find("strong").html(text);
        });

        // Submit the fixed event data
        $("#create_fixed_event").on("click", function() {
            $.ajax({
                url: "custom/calendar_visit/office-controller-event.php",
                type: "post",
                data: $("#reportIssue").serialize(),
                success: function(data) {
                    if (data) {
                        $("#reportIssue")[0]
                            .reset(); // reset the form fields to create a new event if neededs
                        $("#create_task_success").html("Fixed event created successfully!")
                            .fadeIn();
                    } else {
                        console.log("error in creating the event!");
                    }
                }
            });
        });

        //------------------------ Fixed Events filter operations below -----------------------------
        // Init Datatable for the fixed event table
        var fixedEventsTable = $('#fixed_events_table').DataTable();

        fixed_event_category_changed = false;
        fixed_event_createdby_changed = false;
        fixed_event_date_changed = false;
        fixed_event_building_changed = false;

        // Fixed events category filter
        $("#filter_category_fixed").on("change", function() {
            fixed_event_category_changed = $(this).val().toLowerCase();
            fixedEventsTable.draw();
            fixed_event_category_changed =
                false; // change back the value of the flag to false for next filter
        });

        // Date filter in  the fixed events
        $("#filter_date_event_fixed").change(function() {
            fixed_event_date_changed = $(this).val();
            fixedEventsTable.draw();
            fixed_event_date_changed = false;
        });

        $("#filter_building_fixed_event").change(function() {
            fixed_event_building_changed = $("#filter_building_fixed_event :selected").attr("data-name");
            fixedEventsTable.draw();
            fixed_event_building_changed = false;
        });

        // Clear button in the fixed events page
        $("#default_fixed_event").on("click", function() {
            fixed_event_category_changed = false;
            fixed_event_createdby_changed = false;
            fixed_event_date_changed = false;
            fixed_event_building_changed = false;

            $("#filter_createdby_fixed").val("default");
            $("#filter_category_fixed").val("default");
            fixedEventsTable.draw();
        });

        $("#filter_createdby_fixed").on("change", function() {
            let value_selected = $(this).val();
            let name_value = $("#filter_createdby_fixed :selected").attr("data-name");
            fixed_event_createdby_changed = name_value;
            fixedEventsTable.draw();
            fixed_event_createdby_changed = false;
        });

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var _valueHTML = $.parseHTML(data[1]);
                var _text = _valueHTML[0].data;

                if (fixed_event_category_changed) {
                    if (fixed_event_category_changed == "default") {
                        return true;
                    }

                    let category_value = data[2].toLowerCase(); // category value from the table of each row
                    if (fixed_event_category_changed == category_value) {
                        return true;
                    }
                    return false;
                }

                if (fixed_event_date_changed) {
                    let date_value = data[3];
                    if (date_value == fixed_event_date_changed) {
                        return true;
                    }
                    return false;
                }

                if (fixed_event_createdby_changed) {
                    if (fixed_event_createdby_changed == "default") {
                        return true;
                    }
                    let name_value = data[5];
                    if (name_value == fixed_event_createdby_changed) {
                        return true;
                    }
                    return false;
                }

                if (fixed_event_building_changed) {
                    let building_name = data[1];
                    console.log(fixed_event_createdby_changed);
                    if (fixed_event_createdby_changed == "all") {
                        return true;
                    }

                    if (building_name == fixed_event_building_changed) {
                        return true;
                    }
                    return false;
                }

                return true;
            }
        );

    });

    // Materials - when provided by is Vendor : do not show/add any Material rows
    $(".request-material-provider").on("click", function() {

        if ($(this).val() == 2) {
            // Do not show the material rows
            $(".material_outer_wrapper").hide();
        } else {
            // Show the material rows if hidden
            $(".material_outer_wrapper").show();
        }

    });


    $(".editRequest-material-provider").on("click", function() {

        if ($(this).val() == 2) {
            // Do not show the material rows
            $("#editRequestmaterial_detail_wrap_outer").fadeOut();
        } else {
            // Show the material rows if hidden
            $("#editRequestmaterial_detail_wrap_outer").fadeIn();
        }

    });


    $("#contact_number").on("blur", function(e) {
        e.stopImmediatePropagation();
        let value_entered = $(this).val();
        if (/[a-zA-Z]/.test(value_entered) || value_entered.length < 1) {
            $(this).val("");
            return;
        }
    });

    var fullContactNumber = false;
    $("#contact_number").on("keypress", function(e) {
        let value_entered = $(this).val();
        let phoneLen = value_entered.length;

        if (phoneLen == 0) {
            fullContactNumber = false;
        }
        if (phoneLen == 3) {
            if (fullContactNumber) {
                return;
            }
            let reformatted = "+1 " + value_entered + "-";
            $(this).val(reformatted)
        }
        if (phoneLen == 10) {
            if (fullContactNumber) {
                return;
            }
            let reformatted = value_entered + "-";
            $(this).val(reformatted);
            fullContactNumber = true;
        }
    });

    // Open the fixed event modal - pull the page from the office-create-event.php file
    $("#newAgendaFixedEvent").on("click", function(e) {
        data = {
            userId: $("#userIdValue").val(),
            employeeId: $("#employeeIdValue").val(),
            companyId: $("#companyIdValue").val()
        };

        $.get("custom/calendar_visit/office-create-event-modal.php", data, function(data) {
            $("#fixedEventModal").find(".modal-body").html(data);
            $("#fixedEventModal").modal("show");
        });
    });

    $('#iModal').on('show.bs.modal', function(event) {
        var tr = $(event.relatedTarget);
        var request_id = tr.data('request');
        var modal = $(this);
        modal.find('.modal-header input').val(request_id);
        $('#communication_tag').trigger('click');
        init_modal(request_id);
    });

    $('#iModal_bulletin_details').on('show.bs.modal', function(event) {
        var tr = $(event.relatedTarget);
        var bulletin_id = tr.data('request');
        var modal = $(this);
        modal.find('.modal-header input').val(bulletin_id);
        init_bulletin_modal(bulletin_id);
    });

    $('#iModal').on('hidden.bs.modal', function(e) {
        $('#communications').empty();
        //$('#attach_images').empty();
    });

    $('#reportModal').on('hidden.bs.modal', function(e) {
        console.log("close");
        $("#reportNew_uploadImagesForm .request-pic-upload").unbind("change");
    });

    $('#communication_tag').on('click', function(e) {
        var request_id = $('#request_id_val').val();
        init_communications(request_id);
    });

    $('#recipient_tag').on('click', function(e) {
        var request_id = $('#request_id_val').val();
        // init_recipient(request_id,true);
    });

    $('#payment_tag').on('click', function(e) {
        var request_id = $('#request_id_val').val();
        init_payment(request_id);
    });

    $('#edit_tag').on('click', function(e) {
        // init_request('#edit-request');
        // var request_id = $('#request_id_val').val();
        // init_editing('#edit-request', request_id);
    });

    // Load the invoices if any attached to the report on creation
    $("#invoices_report_tag").on("click", function() {
        // init_invoices_attached();
    });

    function init_invoices_attached() {
        let request_id = $("#request_id_val").val();
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_attached_invoices",
                request_id: request_id
            },
            success: function(result) {
                if (result && result != null) {
                    let resultJson = $.parseJSON(result);
                    for (var invoiceIndex in resultJson) {
                        let fileName = resultJson[invoiceIndex];
                        let filePath = "files/requests/" + fileName;
                        $("#report_edit_attachedInvoices_ol").append("<li>" + fileName +
                            " <a target='_blank' href='" + filePath +
                            "' class='btn-xs btn-primary'>View</a> </li>");
                    }
                }
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    // New request report -recipients tab click
    $('#recipient_report_tag').on('click', function(e) {
        let selectedRequestType = $("#report-request-type").val();
        if (selectedRequestType == "0") {
            // Default type - ask the user to select a request type to see the recipients / vendors list
            // $('#editRecipientReport').hide();
            // $(".recipientReport-alert").html("No vendors found for the selected request type.").show();
        }
    });


    //-----------save button-----------
    $('#saveRecipients').on('click', function(e) {
        e.preventDefault();
        var request_id = $('#request_id_val').val();
        set_recipients(request_id);
    });

    $('#saveEdit').on('click', function(e) {
        var request_id = $('#request_id_val').val();
        submit_edit(request_id);
    });

    //-----------report button------------
    $('#startReport').on('click', function(e) {
        init_request('#reportIssue');
    });

    $('#submitReport').on('click', function(e) {

        let location = $('#reportIssue .request-area').val();
        let requestType = $('#reportIssue .request-type').val();

        // if the values in the form are default - don't submit the form
        if (location == "default" || requestType == "0") {
            alert("Missing Location or Request Type. Please check all the required fields");
            return;
        }

        // check if the task date and time radio is "YES"
        let isTaskDateAndTimeSelected = $("input[name='isRequestSetTaskDateTime']:checked").val();
        if (isTaskDateAndTimeSelected && isTaskDateAndTimeSelected == "1") {
            // Task date and time is selected to YES : the start time for the task must not be empty
            if ($("#requestSetTaskDateTimePickerFrom").val().length < 1) {
                return;
            }
        }

        submit_report();
    });

    //------------bulletin button-----------
    $('#startBulletin').on('click', function(e) {
        init_new_bulletin();
    });

    $('#submitNewBulletin').on('click', function() {
        submit_new_bulletin();
    });

    $('#bullentin_read_status_tag').on('click', function(e) {
        var bulletin_id = $('#bulletin_id').val();
        set_bulletin_reading_status(bulletin_id);
    });

    $('#bulletins_tbody').find('.bulletin-close').on('click', function() {
        var btn_id = $(this).attr('id');
        var bulletin_id = btn_id.substring(15);
        close_bulletin(bulletin_id);
    });


    // --------------- global params  ---------------

    var user_id = <?php echo $user_id; ?>;
    var user_level = <?php echo $user_level; ?>;
    var user_unit_id = <?php echo $user_unit_id ?>;
    var relative_path = "";

    //-----------------  communications -------------------

    function init_communications(request_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_communication_controller.php",
            data: {
                action: "get_communications",
                request_id: request_id,
                user_id: user_id
            },
            dataType: "json",
            success: function(result) {
                set_communications_view(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }

    function add_communication() {
        var request_id = $('#request_id_val').val();
        var message = $('#communiation_input').html();
        console.log(message);
        var post_communication_form = new FormData(document.getElementById("post_communication_form"));
        var if_seen_by_tenant = 1;
        var if_notify = false;

        if ($('#hide_for_tenant').is(':checked')) {
            if_seen_by_tenant = 0;
        }
        if ($('#force_ntf').is(':checked')) {
            if_notify = true;
        }
        post_communication_form.append('action', 'add_communication');
        post_communication_form.append('user_id', user_id);
        post_communication_form.append('request_id', request_id);
        post_communication_form.append('message', message);
        post_communication_form.append('if_seen_by_tenant', if_seen_by_tenant);
        post_communication_form.append('if_notify', if_notify);

        if (message.length > 0) {
            message = message.replace(/<(.|\n)*?>/g, '');

            $.ajax({
                type: "post",
                url: relative_path + "request_communication_controller.php",
                data: post_communication_form,
                dataType: "json",
                processData: false,
                contentType: false,
                // async: false,
                success: function(result) {
                    set_communications_view(result);
                },
                error: function(result) {
                    console.log("error:" + result);
                }
            });
        }
        $('#communiation_input').empty();
        $('#hide_for_tenant').prop('checked', true);
        $('#force_ntf').prop('checked', false);
    }


    function set_communications_view(data) {
        $('#communications').empty();

        for (var i in data) {
            var bg_color = 'bg-info';
            if (data[i].creator_id == user_id) {
                bg_color = 'bg-success';
            }
            if (data[i].is_system_msg == 1) {
                bg_color = 'bg-warning';
            }

            var creator_info = '';
            if (data[i].creator_id != 0) {
                creator_info = data[i].creator_name + '  [' + data[i].creator_role + ']';
            } else {
                creator_info = data[i].creator_role;
            }

            var string = '<div class="message-container ' + bg_color + '">';

            var communication_id = data[i].communication_id;

            if (data[i].is_image == 1) {
                string += '<div class="message-text"><a class="attach-a" href="' + data[i].remark +
                    '" data-lightbox="attach-img" data-title="Picture Title"><img src="' + data[i].remark +
                    '" style="max-width: 125px;max-height: 125px;"></a></div>';
            } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 0) {
                string += '<div class="message-text">' + data[i].remark + '</div>';
            } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 1) {
                string +=
                    '<div class="message-text"><span>Do you confirm this repair event ? </span><button id="repair_event_force_confirm" type="button" class = "btn btn-danger repair-event-confirm-btn" name="' +
                    communication_id +
                    '" onclick="change_repair_event_confirm_status.call(this,2)">Force Confirm</button><button id="repair_event_confirm" type="button" class = "btn btn-success repair-event-confirm-btn" name="' +
                    communication_id +
                    '" onclick="change_repair_event_confirm_status.call(this,1)">Confirm</button><button id="repair_event_cancel" type="button" class = "btn btn-primary repair-event-confirm-btn" name="' +
                    communication_id + '" onclick="change_repair_event_confirm_status.call(this,0)">Cancel</button></div>';
            } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 2) {
                string +=
                    '<div class="message-text"><span>Do you confirm this repair event ? </span><button id="repair_event_confirm" type="button" class = "btn btn-success repair-event-confirm-btn" name="' +
                    communication_id +
                    '" onclick="change_repair_event_confirm_status.call(this,1)">Confirm</button><button id="repair_event_cancel" type="button" class = "btn btn-primary repair-event-confirm-btn" name="' +
                    communication_id + '" onclick="change_repair_event_confirm_status.call(this,0)">Cancel</button></div>';
            } else {
                string += '<div class="message-text">' + data[i].remark + '</div>';
            }

            string += '<div class="message-info"><span class="message-info-name">' + creator_info + '</span>';
            if (data[i].if_seen_by_tenant == 0) {
                string += '<i class="fa fa-eye-slash hidden-for-tenant-msg"></i>';
            }
            string += '               <a id="read_status_' + i +
                '" class="message-info-read-status" tabindex="0" role="button" data-toggle="popover" data-container="body" data-trigger="focus">Reading status</a>\n' +
                '                    <span class="message-info-date">' + data[i].created_time + '</span>\n' +
                '                  </div>\n' +
                '                </div>';

            $('#communications').append(string);

            var employee_text = '';
            var tenant_text = '';
            var assignees_status = data[i].assignees_status;
            for (r in assignees_status) {
                if (assignees_status[r].read_status == 'read')
                    var temp =
                        '<button type="button" class="btn btn-primary read-status-bk" data-toggle="tooltip" data-placement="right" title="last access: ' +
                        assignees_status[r].last_access_time + '">' + assignees_status[r].user_name + '</button>';
                else if (assignees_status[r].read_status == 'unread')
                    var temp =
                        '<button type="button" class="btn btn-warning read-status-bk" data-toggle="tooltip"  data-placement="right" title="last access: ' +
                        assignees_status[r].last_access_time + '">' + assignees_status[r].user_name + '</button>';

                if (assignees_status[r].user_role == 'Employee')
                    employee_text += temp;
                else if (assignees_status[r].user_role == 'Tenant')
                    tenant_text += temp;
            }

            var text = '<div><h6>Employees:</h6>' + employee_text + '</span> <h6>Tenants:</h6>' + tenant_text + '<div>';
            //yuhong: add legend to show the color coding
            text = text + '<div><h6> Legend: </h6><button type="button" class = "btn btn-primary">Read</button> ' +
                '<button type="button" class = "btn btn-warning">Unread</button></div>';

            $('#read_status_' + i).popover({
                html: true,
                content: text
            });
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $(function() {
            $('[data-toggle="popover"]').popover();
        });

        //set textare and file input
        $('#post_communication_form')[0].reset();
    }


    function add_communication_image() {
        var ele_communiaction_form = $('#post_communication_form');
        var img = ele_communiaction_form.find('.request-pic-upload')[0].files[0];
        var communication_input = ele_communiaction_form.find('#communiation_input');
        communication_input.html(communication_input.html().replace(/<(.|\n)*?>/g, ''));
        var fileReader = new FileReader();
        fileReader.readAsDataURL(img);
        fileReader.onloadend = function(oFRevent) {
            var src = oFRevent.target.result;
            communication_input.append('<img id="reportPic_' + img.name +
                '" class="request-pic-preview img-thumbnail " style="max-width: 150px;max-height: 150px;" src="' +
                src + '">');
        };
    }

    //user_action[0:cancel, 1:confirm, 2:force_confirm]
    function change_repair_event_confirm_status(user_action) {
        var request_communication_id = this.name;
        var post_action = null;
        if (user_action === 0) {
            confirm("Are you sure to cancel this repair reservation ?");
            post_action = 'cancel';
        } else if (user_action === 1) {
            confirm("Are you sure to conform this repair reservation ?");
            post_action = 'confirm';
        } else if (user_action === 2) {
            confirm("Are you sure to confirm this repair reservation direacly without the confirmation from handyman ?");
            post_action = 'force_confirm';
        }

        var request_id = $('#request_id_val').val();

        $.ajax({
            type: "post",
            url: relative_path + "request_communication_controller.php",
            data: {
                action: 'change_repair_event_status',
                request_communication_id: request_communication_id,
                user_action: post_action,
                user_id: user_id,
                request_id: request_id
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_communications_view(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }

    // ------------------- attachments -------------------------

    function init_attachments(request_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_attachments",
                request_id: request_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_attachments_view(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }

    function init_recipient(request_id, update_recipients) {
        if (!update_recipients) {
            alert("Recipients updated!");
            return;
        }
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_recipients",
                request_id: request_id
            },
            // async: false,
            dataType: "json",
            success: function(result) {
                set_recipients_view(result);
            },
            error: function(result) {
                console.log("Error: " + result);
            }
        });
    };

    // Check if any vendor is assigned to this request
    // if no vendor is assigned - ask the user to assignt a vendor first and then add the payment details
    function init_payment(request_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_payment_controller.php",
            data: {
                action: "check_assigned",
                request_id: request_id
            },
            // async: false,
            dataType: "json",
            success: function(response) {
                set_payment_view(response, request_id);
            },
            error: function(result) {
                console.log("Error: " + result);
            }
        });
    }

    function set_payment_view(response, request_id) {
        $("#payment_tab_alert").hide();
        $("#payment_tab_content").show();
        if (response.result) {
            /***
             * Get the vendor details
             * Per Hour wage, vendor id
             */
            $("#request-payapprove-form-div").show();
            $("#request_approve_alert").hide();
            let vendor = response.value[0];

            if (!vendor.wage) {
                $("#edit-payment").find("#request_pay_perhr").attr("readonly", false);
            } else {
                $("#edit-payment").find("#request_pay_perhr").val(vendor.wage).attr("data-wage", vendor.wage);
            }

            $("#savePaymentdetails").attr("data-vendorid", vendor.user_id).attr("data-wage", vendor.wage).attr("data-rid",
                request_id);
            $("#edit-payment").find("#file-holder-payments").empty();

            $.ajax({
                type: "post",
                url: relative_path + "request_payment_controller.php",
                data: {
                    action: "getPaymentDetails",
                    request_id: request_id
                },
                async: false,
                dataType: "json",
                success: function(response) {
                    let estimated = response.estimated;
                    let value = response.value;

                    $("#edit-payment").find("#request_pay_estimatedprice").val(
                        estimated); // estimated price for the job - request
                    $("#edit-payment").find("#request_pay_perhr").val(value.payment_amount);
                    $("#edit-payment").find("#request_payinfo").val(value.repair_detail);
                    $("#edit-payment").find("#request_payhours").val(value.job_hours);
                    $("#edit-payment").find("#request_pay_expenses").val(value.other_expenses);
                    $("#paymentinvoicenum").val(value.invoice_detail);
                    if (value.invoice_attachment != null && value.invoice_detail != null) {
                        let invoiceFullName = value.invoice_detail + "=" + value.invoice_attachment;
                        let hiddenFileName =
                            "<input type='hidden' style='display:none;' name='invoice_attached[]' value='" +
                            invoiceFullName + "' >";
                        $("#edit-payment").find("#file-holder-payments").append(hiddenFileName);
                    }

                    $("#savePaymentdetails").attr("disabled", false);

                    if (value.is_approved == 1) {
                        $("#request-payapprove-form-div").hide();
                        $("#request_approve_alert").html("Payment is approved for this request").show();
                        $("#savePaymentdetails").attr("disabled", true);
                    }

                },
                error: function(result) {
                    console.log("Error: " + result);
                }
            });
        } else {
            $("#payment_tab_alert").html(
                    "<span class='alert alert-warning'>Assign a vendor to this request to view the payment details.</span>")
                .show();
            $("#payment_tab_content").hide();
        }
    }

    function init_modal(request_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_modal_info",
                request_id: request_id,
                user_id: user_id,
                user_unit_id: user_unit_id
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_modal_view(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    };

    function set_recipients(request_id) {
        var recipientEmployee = [];
        $('#recipient-employee input').each(function(i, item) {
            var assigned = '0';
            if (item.checked) {
                assigned = '1';
            }
            var employee = {
                employee_id: item.value,
                assigned: assigned
            };
            recipientEmployee.push(employee);
        });

        var recipientTenant = [];
        $('#recipient-tenant input').each(function(i, item) {
            var assigned = '0';
            if (item.checked) {
                assigned = '1';
            }
            var tenant = {
                tenant_id: item.value,
                assigned: assigned
            };
            recipientTenant.push(tenant);
        });

        var recipientVendors = [];
        // $('#recipient-vendor input').each(function (i,item) {
        //     var assigned = '0';
        //     if (item.checked) {
        //         assigned = '1';
        //     }
        //     var vendor = {
        //         vendor_id: item.value,
        //         assigned: assigned
        //     };
        //     recipientVendors.push(vendor);
        // });
        //

        let recipientVendorSelected = $("body").find('#recipient-vendor').val();

        if ($("body").find('#recipient-vendor').length > 0) {
            let recipientsList = $("body").find('#recipient-vendor')[0].selectize.options;

            for (var rec in recipientsList) {
                var assigned = '0';
                let value = rec;
                if (recipientVendorSelected == value) {
                    assigned = '1';
                }
                var vendor = {
                    vendor_id: value,
                    assigned: assigned
                };
                recipientVendors.push(vendor);
            }
        }


        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "set_recipients",
                request_id: request_id,
                employees: recipientEmployee,
                tenants: recipientTenant,
                vendors: recipientVendors,
            },
            success: function(result) {
                init_recipient(result, false)
            },
            error: function(result) {
                console.log("error:" + result);
            }
        });
    }


    function set_notify_methods() {
        var is_ntf_email = false;
        var is_ntf_sms = false;
        var is_ntf_voice = false;
        var request_id = $('#request_id_val').val();

        if ($('#notify_email').is(':checked'))
            is_ntf_email = true;
        if ($('#notify_sms').is(':checked'))
            is_ntf_sms = true;
        if ($('#notify_voice').is(':checked'))
            is_ntf_voice = true;

        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "set_notify_methods",
                user_id: user_id,
                request_id: request_id,
                notify_email: is_ntf_email,
                notify_sms: is_ntf_sms,
                notify_voice: is_ntf_voice
            },
            dataType: "json",
            success: function(result) {},
            error: function(result) {
                console.log('ERROR' + result);
            }
        });
    };


    //--------------------  update view methods -----------------------

    function set_modal_view(data) {
        var request_id = data.request_id;
        var request_category = data.request_category;
        var request_status = data.request_status;
        var request_type = data.request_type;
        var open_or_close = data.open_or_close;
        var created_user_name = data.created_user_name;
        var created_user_telephone = data.created_user_telephone;
        var created_date = data.created_date;
        var created_time = data.created_time;
        var building_name = data.building_name;
        var apart = data.specific_area;
        var building_address = data.building_address;
        var building_picture = data.building_picture;


        $('#issue_id').text(request_id);
        $('#modal_user_name').text(created_user_name);
        $('#modal_user_telephone').text(created_user_telephone);
        $('#modal_date').text(created_date);
        $('#modal_time').text(created_time);
        // $('#modal_building').text(building_name);
        // $('#modal_apart').text(apart);
        // $('#modal_address').text(building_address);
        $('#modal_building_img').attr('src', building_picture);

        //decarate the request type
        var issue_h_type = $('#issue_h_type');
        issue_h_type.text(request_type);

        if (user_level != 5) {
            if (request_category == 0)
                issue_h_type.attr('class', 'issue-h-type-system');
            else if (request_category == 1)
                issue_h_type.attr('class', 'issue-h-type-internal');
            else
                issue_h_type.attr('class', 'issue-h-type-tenant');
        }

        if (user_level == 5) {
            // If the user is tenant
            $('#recipient_tag,#payment_tag').remove();
            $('#recipient,#payment_tab,#payment_approval').remove();
            $('#payment_approval').remove();
        }

        if (user_level == 11) {
            // If the user is handyman - show the payment approval div
            $('#payment_approval').remove();
        }

        // Hide the payment tab when the issue is not closed
        if (open_or_close != "closed") {
            $("#payment_tag").hide();
        }

        // change the issue bkg
        var issue_tr_id = '#issue_row_' + data.request_id;
        var issue_line_class = 'issue-line';
        if (request_category == 2 || request_category == 0) {
            issue_line_class += ' warning ';
        } else {
            issue_line_class += ' success ';
        }
        if (open_or_close == 'open') {
            issue_line_class += ' txt-black ';
        } else {
            issue_line_class += ' txt-grey ';
        }

        $(issue_tr_id).attr('class', issue_line_class);

        var status_selected_line = $(issue_tr_id).children().first();
        if (user_level != 5 && status_selected_line.text() == 'PENDING') {
            status_selected_line.text("READ BY MANAGER");
        }

        //change unread count
        $('#unread_issue_count').text(data.open_issue_count);
        $('#unread_issue_count').text(data.unread_issue_count);

        //open or close
        if (open_or_close == 'open') {
            $('#open_or_closed_text').text(request_status);
            $('#open_or_closed').attr("class", "modal-status modal-status-open");

            //allow communication
            $('#new_message').attr('hidden', false);
            $('#opeartions_new_message').attr('hidden', false);
        } else {
            $('#open_or_closed_text').text(request_status);
            $('#open_or_closed').attr("class", "modal-status modal-status-close");

            //disable communication
            $('#new_message').attr('hidden', true);
            $('#opeartions_new_message').attr('hidden', true);
        }

        $('#hide_for_tenant').prop('checked', true);
        $('#force_ntf').prop('checked', false);
    }

    // When the material tab is clicked in the edit reuqest modal
    $("#materials_tag").on("click", function() {
        let request_id = $("#iModal").find("#request_id").val();
        // Open the material Provided tab and show the materials provided earlier during the creation of the request -
        // if no material was provided - show the same material provided tab content - nothing new here!
        // setEditRequestmaterialTabContent(request_id);
    });

    function set_recipients_view(data) {
        $('#recipient-employee').empty();
        var employees = data.employees;
        let requestId = data.request_id;
        $("#recipient").attr("data-rid", requestId); // Set the request ID value to the div for later use

        // for (var x in employees) {
        //     //if the employee is already assigned with this request, then check the box
        //     if (employees[x].assigned === 1) {
        //         var checked = 'checked';
        //         if (employees[x].notifyEmployeesByEmail === 1) {
        //             $('#editRecipient .notify-employees-by-email').prop('checked', true);
        //         }
        //         if (employees[x].notifyEmployeesBySms === 1) {
        //             $('#editRecipient .notify-employees-by-sms').prop('checked', true);
        //         }
        //         if (employees[x].notifyEmployeesByVoice === 1) {
        //             $('#editRecipient .notify-employees-by-voice').prop('checked', true);
        //         }
        //     } else {
        //         var checked = '';
        //     }
        //     $('#recipient-employee').append('<tr><td><label class="checkbox"><input class="edit-input" type="checkbox" name="employee" value="' + employees[x].employee_id + '" ' + checked + '>' + employees[x].full_name + '</label></td></tr>');
        // }

        // $('#editRecipient .tenant-recipient-wraps').remove();
        // if (data.tenants) {
        //     $('#recipient-employee-wrap').after('<div class="col-sm-12 tenant-recipient-wraps">\n' +
        //         '                    <div class="form-group">\n' +
        //         '                      <label class="edit-label col-sm-4">Tenants</label>\n' +
        //         '                      <div class="col-sm-7">\n' +
        //         '                        <table id="recipient-tenant">\n' +
        //         '                        </table>\n' +
        //         '                      </div>\n' +
        //         '                    </div>\n' +
        //         '                  </div>\n'
        //     );
        //     var tenants = data.tenants;
        //     for (var y in tenants) {
        //         //if the tenant is already assigned with this request, then check the box
        //         if (tenants[y].assigned === 1) {
        //             var checked = 'checked';
        //         } else {
        //             var checked = '';
        //         }
        //         $('#recipient-tenant').append('<tr><td><label class="checkbox"><input class="edit-input" type="checkbox" name="tenant" value="' + tenants[y].tenant_id + '" ' + checked + '>' + tenants[y].full_name + '</label></td></tr>')
        //     }
        // }


        $('#editRecipient .vendor-recipient-wraps').remove();
        let vendorData = data.vendors;
        if (vendorData.length > 0) {
            if (data.vendors) {
                $('#recipient-employee-wrap').after('<div class="col-sm-12 vendor-recipient-wraps">\n' +
                    '                    <div class="form-group">\n' +
                    '                      <label class="edit-label col-sm-4">Vendors</label>\n' +
                    '                      <div class="col-sm-7">\n' +
                    '                        <select id="recipient-vendor">\n' +
                    '                        </select>\n' +
                    '                      </div>\n' +
                    '                    </div>\n' +
                    '                  </div>\n'
                );
                var vendors = data.vendors;
                $('#recipient-vendor').append('<option class="edit-input" name="vendor" ></option>');
                for (var y in vendors) {
                    //if the vendor is already assigned with this request, then check the box
                    if (vendors[y].assigned === 1) {
                        var vendor_selected = 'selected';
                    } else {
                        var vendor_selected = '';
                    }
                    let vendorName = vendors[y].full_name;
                    if (!vendorName || vendorName.length < 1 || vendors[y].vendor_type_id == 1) {
                        vendorName = vendors[y].company_name;
                    }
                    $('#recipient-vendor').append('<option data-rid="' + requestId +
                        '" class="edit-input" name="vendor" value="' + vendors[y].vendor_id + '" ' + vendor_selected +
                        ' >' + vendorName + '</option>');
                }
            }
        } else {
            $('#recipient-employee-wrap').after('<div class="col-sm-12 vendor-recipient-wraps">\n' +
                '                    <div class="form-group">\n' +
                '                      <label class="edit-label col-sm-4">Vendors</label>\n' +
                '                      <div class="col-sm-7">\n' +
                '                        <h5>*No vendors found for the selected request type.*</h5>\n' +
                '                      </div>\n' +
                '                    </div>\n' +
                '                  </div>\n'
            );
        }

        //if closed request, disable operations
        if (data.open_or_close == 'closed') {
            console.log("closed");
            $('#saveRecipients').hide();
            $('#cancelRecipients').hide();
        } else {
            console.log("open");
            $('#saveRecipients').show();
            $('#cancelRecipients').show();
        }
    }

    // Edit request  - material provided - update the material provided value if exists
    $("#editRequestmaterialDetailsSave").on("click", function() {
        // check if there is atleast one materia row to save
        let count_of_material_rows = $("#editRequestmaterial_detail_wrap_inner").children().length;

        if (count_of_material_rows < 1) {
            $("#editRequestaddMoreMaterial").trigger("click");
            return;
        }

        // get the material provided data
        let materialProvidedFormData = $("#editRequestMaterialReport").serialize();
        let request_id = $("#request_id_val").val();

        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                request_id: request_id,
                action: 'updateMaterialProvided',
                data: materialProvidedFormData
            },
            success: function(response) {
                if (response) {
                    // material updated for the selected request id
                    alert("Material details updated for the request.");
                }
            },
            error: function() {
                console.log("Error");
            }
        });
    });

    // In the edit request modal - set the material tab content
    // This will be when the user changes the vendor or the first time a new vendor is assigned to the request
    function setEditRequestmaterialTabContent(request_id) {
        // get the materials already defined for the request from request_infos table and show
        $("#editRequestmaterial_detail_wrap_inner").empty();
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "getMaterialProvided",
                request_id: request_id
            },
            dataType: "json",
            success: function(result) {
                if (result.material_provider) {
                    // set the material provider value which is already set in the database
                    $('input[name=editRequestMaterialprovider][value=' + result.material_provider + ']').prop(
                        'checked', true);
                }
                if (result.material_detail) {
                    let materialDetail = $.parseJSON(result
                        .material_detail); // this is already a JSON value which is stored in the db - so parse the json to object
                    if (!materialDetail) {
                        return;
                    }
                    let materialCount = materialDetail.length;
                    if (materialCount > 0) {
                        $("#editRequestmaterial_existing_row_input")
                            .remove(); // remove the existing default input row if there are already material rows in the db for the selected request ID
                        for (var index in materialDetail) {
                            let materialValue = materialDetail[
                                index]; // add this material value to the material tab as a form - the prototype for the form input element exists in the form
                            let form_input_prototype = $("#editRequestadd_more_material_proto").html();
                            let newElement = $.parseHTML(form_input_prototype);
                            $(newElement).children().children().first().children(":first").val(materialValue
                                .material_name) // Material Detail value
                            $(newElement).children().children().first().next().children(":first").val(
                                materialValue.material_online_store_id); // Material Shop detail
                            $(newElement).children().children().first().next().next().children(":first").val(
                                materialValue.material_url); // Material Shop detail
                            $("#editRequestmaterial_detail_wrap_inner").append(
                                newElement); // Add the existing material data as a new row
                        }
                    }
                }
            },
            error: function(result) {
                console.log("error:" + result);
            }
        });
    }


    //---------------init_request start--------------

    //init-request is the entry to initialize a form. All the functions after that is called directly or indirectly
    function init_request(formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_report_ready",
                user_level: user_level,
                user_id: user_id,
                user_unit_id: user_unit_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_report_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    //set view for common are or apartment list
    function set_report_view(data, formId) {
        //set location section
        $(formId + ' .request-location-div').empty();
        $(formId + ' .request-tenant-wrap').remove();
        $(formId + ' .request-notify-tenants-wrap').remove();
        $(formId + ' .report-location-margin-top').empty();

        if (user_level === 5) {
            $(formId + ' .request-location-div').append(data.building_name + " " + data.unit_number);
            $(formId + ' .request-location-div').append(
                '<input id="preUnit" type="hidden" name="reportApartmentId" value="' + data.apartment_id + '">' +
                '<input id="preBuilding" type="hidden" name="reportBuildingId" value="' + data.building_id + '">' +
                '<input id="preArea" type="hidden" name="reportArea" value="apartment">');
            //if it is a tenant, then no access to choosing status when reporting an issue.
            if (formId === "#reportIssue") {
                $(formId + ' .request-status-wrap').remove();
            }

        } else {
            var content =
                '<select class="edit-input form-control request-area" id="reportArea" name="reportArea" required>' +
                '<option value="default">None</option>' +
                '<option value="common area">Common Area</option>' +
                '<option value="apartment">Apartment</option>' +
                '<option value="request-area-other">Other</option>' +
                '</select>';

            $(formId + ' .request-location-div').append(content);
            $(formId + ' .request-area').on('change', function() {
                var area = $(this).children('option:selected').val();
                set_building_view(data.buildings, area, formId);
            });
        }

        set_request_type_view(data.request_types, formId + ' .request-type');

        if (formId != '#reportIssue') {
            set_request_status_view(data, formId + ' .request-status');
        }

        //by defalut, hiden handyman time slot
        $(formId + ' .request-visit-time-wraps').hide();

        //for enabling/disabling the visit from to time
        $(formId + ' .request-visit-approved').on('click', function() {
            $(formId + ' .request-visit-time-wraps').show();

        });
        $(formId + ' .request-visit-not-approved').on('click', function() {
            $(formId + ' .request-visit-time-wraps').hide();
        });

        //when the request type is changed, the time slot will be changed
        // Sharan code : in the recipients tab : add the vendors list according to the request type selected
        $(formId + ' .request-type').change(function(e) {
            e.stopImmediatePropagation();
            $('.recipientReport-alert').hide();
            $("#recipient-report-vendor-wrap,#recipient-report-estimatedprice-wrap").hide();
            $("#recipient-report-vendor-type").val("default");
            // var selected_request_type = $(formId + ' .request-type').val();
            // var building_id = $(formId).find('#preBuilding').val();
            // update_handyman_date_slot(building_id,selected_request_type); // Not required as suggested by Mr.Frank
        });

        // When the vendor type changes - load the vendors list again accoridng to the type
        $("#recipient-report-vendor-type,#recipient-report-vendor-speciality-level,#recipient-report-vendor-speciality,#recipient-report-license-type")
            .change(function(e) {
                e.stopImmediatePropagation();
                $(".recipientReport-alert").fadeOut();
                if ($(this).val() != "default") {
                    updateVendorsNewRequest();
                }
            });

        // Upload pictures from the pictures tab in the reportModal - add new task
        $("#reportNew_uploadImages_btn").on("click", function(e) {
            let pictureformData = new FormData($("#reportNew_uploadImagesForm")[0]);
            pictureformData.append("action", "add_request_upload_pictures");

            $.ajax({
                type: "post",
                url: relative_path + "request_info_controller.php",
                data: pictureformData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    if (response.result) {
                        let pictureFilesInput = "";
                        for (var pictureIndex in response.values) {
                            pictureFilesInput += "<input type='hidden' name='pictureImages[]' value='" +
                                response.values[pictureIndex] + "' > ";
                        }
                        $("#report_modaladditional_info_form").append(pictureFilesInput);
                        // pictures are uploaded
                        alert("Pictures uploaded successfully!");
                    }
                },
                error: function() {
                    console.log("File upload error");
                }
            });
        });

        //uploading pictures ---- preview the picture
        $('#reportNew_uploadImagesForm .request-pic-upload').on('change', function() {
            let formId = "#reportNew_uploadImagesForm";
            $(formId).find('#report_preview_imgs').empty();
            var files = $(this)[0].files;

            console.log(files.length);
            var fileReader = new FileReader();
            var img_div = $(formId).find('#report_preview_imgs');

            for (var i = 0; i < files.length; i++) {
                var file_one = files[i];
                fileReader.readAsDataURL(file_one);
                fileReader.onloadend = function(oFRevent) {
                    var src = oFRevent.target.result;
                    img_div.append(
                        '<img id="reportPic" class="request-pic-preview img-thumbnail" style="max-width: 150px;max-height: 150px; margin-right:7px; padding:2px;" src="' +
                        src + '">');
                };

                fileReader = new FileReader();
            }

            // Show the upload pictures button
            $("#reportNew_uploadImages").fadeIn();
        });
    }


    // check only numbers are entered
    $("#recipient-vendor-estimatedprice").on("blur", function() {
        let price_value = $(this).val();
        if (isNaN(price_value)) {
            let strippedValue = price_value.replace(/\D/g, ''); // Remove all the non digits from the entered value
            $(this).val(strippedValue);
        }
    });

    // Update the vendors list in the recipients tab in the new report request modal
    function updateVendorsNewRequest() {
        // Get all the vendors in the system for the given type of the request (job type)
        let vendorTypeSelected = $("#recipient-report-vendor-type").val();
        let vendorSpecialityLevel = $("#recipient-report-vendor-speciality-level").val();
        let vendorSpeciality = $("#recipient-report-vendor-speciality").val();
        let vendorLicenses = $("#recipient-report-license-type").val();

        if (vendorSpecialityLevel == 7) {
            vendorSpeciality = true;
            $("#recipient-report-vendor-speciality-wrap").hide();
        } else {
            $("#recipient-report-vendor-speciality-wrap").show();
        }

        $('#editRecipientReport').show();
        $('#recipient-report-vendor').empty();
        $('.recipientReport-alert').hide();
        $('#recipient-report-vendor').append("<option value='0'>Select a Vendor</option>");
        $("#recipient-report-vendor-wrap,#recipient-report-estimatedprice-wrap").hide();

        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_vendors",
                vendorSpeciality: vendorSpeciality,
                vendor_type: vendorTypeSelected,
                vendorSpecialityLevel: vendorSpecialityLevel,
                vendorLicenses: vendorLicenses
            },
            dataType: "json",
            success: function(result) {
                if (result.value) {
                    $("#recipient-report-vendor-wrap,#recipient-report-estimatedprice-wrap").fadeIn();
                    // vendors exist
                    var vendors = result.value;
                    for (var y in vendors) {
                        let vendorName = vendors[y].full_name;
                        if (!vendorName || vendorName.length < 1 || vendors[y].vendor_type_id == 1) {
                            vendorName = vendors[y].company_name;
                        }

                        starCount = 1;
                        starsCount = "";

                        while (starCount <= parseInt(vendors[y].stars)) {
                            starsCount += '&#xf005; ';
                            starCount++;
                        }

                        $('#recipient-report-vendor').append(
                            '<option class="edit-input" name="vendor" value="' + vendors[y].vendor_id +
                            '" > ' + starsCount + vendorName + '</option>'
                        );
                    }
                } else {
                    $("#recipient-report-vendor-wrap,#recipient-report-estimatedprice-wrap").fadeOut();
                    // $('#editRecipientReport').hide();
                    $(".recipientReport-alert").html("No vendors found.").fadeIn();
                }
            },
            error: function(result) {
                console.log("Error: " + result);
            }
        });
    }

    //set the list of request types
    function set_request_type_view(data, element) {
        $(element).empty();
        var content = '';
        content += '<option value="0">Select task type</option>';
        for (var i in data) {
            content += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        }
        $(element).append(content);
    }

    //set the list of request status
    function set_request_status_view(data, element) {
        var status = data.request_status;

        $(element).empty();

        var content = '';
        for (var i in status) {
            content += '<option value="' + status[i].id + '">' + status[i].name + '</option>';
        }

        if (user_level != 5) {
            content += '<option disabled>------------------------------------------------------</option>';
            console.log(data);
            var status_2 = data.request_status_2;
            for (var i in status_2) {
                content += '<option value="' + status_2[i].id + '">' + status_2[i].name + '</option>';
            }
        }

        $(element).append(content);
    }

    //set view for building list
    function set_building_view(data, area, formId) {
        $(formId + ' .request-area').nextAll().remove();
        $(formId + ' .request-tenant-wrap').remove();
        $(formId + ' .request-notify-wrap').remove();
        if (area == 'common area') {
            var content =
                '<select class="edit-input form-control request-location-building report-location-margin-top hideForOtherRequestArea" id="reportBuilding" name="reportBuildingId" required><option value="">Select Building ...</option>';
            for (var i in data) {
                content += '<option value="' + data[i].building_id + '">' + data[i].building_name + '</option>'
            }
            content += '</select>';
            $(formId + ' .request-location-div').append(content);
            $(formId + ' .request-location-building').on('change', function() {
                $(this).nextAll().remove();
                $(formId + ' .request-location-div').append(
                    '<textarea class="form-control report-location-margin-top hideForOtherRequestArea" id="reportAreaDetails" name="reportLocationDetails" rows="1" placeholder="Specific Common Area (e.g. Elevator No.2)" ></textarea>'
                );
            });
        } else if (area == 'apartment') {
            var content =
                '<select class="edit-input form-control request-location-building report-location-margin-top hideForOtherRequestArea" id="reportBuilding" name="reportBuildingId" required><option value="">Select Building ...</option>';
            for (var i in data) {
                content += '<option value="' + data[i].building_id + '">' + data[i].building_name + '</option>'
            }
            content += '</select>';
            $(formId + ' .request-location-div').append(content);
            $(formId + ' .request-location-building').on('change', function() {
                var building_id = $(this).children('option:selected').val();
                init_floor(building_id, formId);
            });
        }
    }

    //set view for floors list
    function init_floor(building_id, formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_floors",
                building_id: building_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_report_floor_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    $("#editRequestLocationBuilding,#editRequestLocationReportArea").on("change", function() {
        let edit_view_report_area = $("#editRequestLocationReportArea").val();

        if (edit_view_report_area == "apartment") {
            $("#editRequestLocationCommonArea").hide();
            let building_id = $("#editRequestLocationBuilding").val();

            let floor_id = $("#editRequestLocationFloor").val();
            if (floor_id != "default") {
                $("#editRequestLocationApt").hide();
            }

            init_floor_editing_view(building_id, "edit-request");
        }

        if (edit_view_report_area == "common area") {
            $("#editRequestLocationCommonArea").show();
            $("#editRequestLocationFloor,#editRequestLocationApt").hide();
        }

    });

    function init_floor_editing_view(building_id, formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_floors",
                building_id: building_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_report_floor_view_editing_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    function set_report_floor_view_editing_view(data, formId) {
        $("#editRequestLocationFloor").empty();
        var content = '<option value="default">Select Floor ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].floor_id + '">' + data[i].floor_name + '</option>'
        }

        $("#editRequestLocationFloor").append(content).show();

        $("#editRequestLocationFloor").on('change', function() {
            var floor_id = $(this).children('option:selected').val();
            init_apartment_editing_view(floor_id, "edit-request");
        });
    }

    function init_apartment_editing_view(floor_id, formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_apartments",
                floor_id: floor_id
            },
            dataType: "json",
            success: function(result) {
                set_report_apartment_view_editing_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    function set_report_apartment_view_editing_view(data, formId) {
        $("#editRequestLocationApt").empty();
        var content = '<option value="default">Select Unit Number ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].apartment_id + '">' + data[i].unit_number + '</option>'
        }
        console.log(content);
        $("#editRequestLocationApt").append(content).show();
    }

    function set_report_floor_view(data, formId) {
        $(formId + ' .request-location-building ').nextAll().remove();
        $(formId + ' .request-tenant-wrap').remove();
        $(formId + ' .request-notify-wrap').remove();
        var content =
            '<select class="edit-input form-control request-location-floor report-location-margin-top hideForOtherRequestArea" id="reportFloorEdit" name="reportFloorId" required><option value="">Select Floor ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].floor_id + '">' + data[i].floor_name + '</option>'
        }
        content += '</select>';
        $(formId + ' .request-location-div').append(content);
        $(formId + ' .request-location-floor').on('change', function() {
            var floor_id = $(this).children('option:selected').val();
            init_apartment(floor_id, formId);
        });
    }

    //set view for apartments list
    function init_apartment(floor_id, formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_apartments",
                floor_id: floor_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_report_apartment_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    function set_report_apartment_view(data, formId) {
        $(formId + ' .request-location-floor').nextAll().remove();
        $(formId + ' .request-tenant-wrap').remove();
        $(formId + ' .request-notify-wrap').remove();
        var content =
            '<select class="hideForOtherRequestArea edit-input form-control request-location-apartment report-location-margin-top" id="reportApartment" name="reportApartmentId" required><option value="">Select Unit Number ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].apartment_id + '">' + data[i].unit_number + '</option>'
        }
        content += '</select>';
        $(formId + ' .request-location-div').append(content);
        $(formId + ' .request-location-apartment').on('change', function() {
            var apartment_id = $(this).children('option:selected').val();
            init_tenant(apartment_id, formId);
        });
    }

    function init_tenant(apartment_id, formId) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_tenants",
                apartment_id: apartment_id
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_report_tenant_view(result, formId);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    function set_report_tenant_view(data, formId) {
        $(formId + ' .request-location-apartment').nextAll().remove();
        var content =
            '<div class="col-sm-12 request-tenant-wrap hideForOtherRequestArea" id="reportTenantsWrap"><div class="form-group"><label class="edit-label col-sm-4" for="reportTenants">Include Tenants as Recipients</label><div id="reportTenants" class="col-sm-8"><table>';
        for (var i in data) {
            content +=
                '<tr><td><label class="checkbox-inline"><input class="edit-input" type="checkbox" name="reportTenantIds[]" value="' +
                data[i].tenant_id + '" checked>' + data[i].tenant_name + '</label></td></tr>';
        }
        content += '</table></div></div></div>';
        content += '<div class="hideForOtherRequestArea col-sm-12 request-notify-tenants-wrap">\n' +
            '              <div class="form-group">\n' +
            '                <label class="edit-label col-sm-4 request-notify-label" id="notifyLabel">Notify the Tenants by</label>\n' +
            '                <div class="col-sm-8">\n' +
            '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsByEmail" value="0"><input class="edit-input notify-by-email" type="checkbox" name="notifyTenantsByEmail" id="reportNotifyTenantsEmail" value="1">Email</label>\n' +
            '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsBySms" value="0"><input class="edit-input notify-by-sms" type="checkbox" name="notifyTenantsBySms" id="reportNotifyTenantsSms" value="1">SMS</label>\n' +
            '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsByVoice" value="0"><input class="edit-input notify-by-voice" type="checkbox" name="notifyTenantsByVoice" id="reportNotifyTenantsVoice" value="1">Voice</label>\n' +
            '                </div>\n' +
            '              </div>\n' +
            '            </div>'
        $(formId + ' .request-tenant-wrap').remove();
        $(formId + ' .request-notify-tenants-wrap').remove();
        $(formId + ' .request-location-wrap').after(content);
    }

    //--------------init_request functions end----------------


    //---------------init_editing function start----------------

    function init_editing(formId, request_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_editing",
                request_id: request_id,
                user_id: user_id,
                user_level: user_level
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_editing_view(formId, result);
            },
            error: function(result) {
                console.log("error: " + result);
            }
        });
    }

    //to pre set the info of the request to be edited.
    function set_editing_view(formId, data) {
        if (user_level == 5) {
            $(formId + ' .request-type-div').empty();
            $(formId + ' .request-type-div').append(data.request_type_name);
            $(formId + ' .request-status').val(data.status_id);
            $(formId + ' .request-message-div').empty();
            $(formId + ' .request-message-div').append(data.message);
        }

        // else if (user_level == 1) {
        else {
            // For the editing view - set the report area
            $("#editRequestLocationReportArea").val(data.location);

            // For the editing view - set the Building
            $("#editRequestLocationBuilding").val(data.building_id);

            if (data.location === 'apartment') {
                $(formId + ' .request-location-div').empty();
                $(formId + ' .request-location-div').append('The unit of ' + data.unit_number);

                // For the editing view - if the location report area is apartment - hide the common area field
                $("#editRequestLocationCommonArea").hide();

                init_floor_editing_view(data.building_id, "edit-request");
                init_apartment_editing_view(data.floor_id, "edit-request");

                // For editing view  - in this case show the Floor and other details - if they are selected previously - select them and show
                $("#editRequestLocationFloor").val(data.floor_id).show();

                // For editing view - show the apartment select and preselect if there's a value
                $("#editRequestLocationApt").val(data.apartment_id).show();

            } else if (data.location === 'common area') {
                $(formId + ' .request-location-div').empty();
                $(formId + ' .request-location-div').append('The common area(' + data.common_area_detail + ') in ' + data
                    .building_name);

                // For the editing view - if the location report area is common area - show the common area field
                $("#editRequestLocationCommonArea").val(data.common_area_detail).show();
            }

            $(formId + ' .request-type').val(data.request_type_id);
            $(formId + ' .request-status').val(data.status_id);
            $(formId + ' .request-message').val(data.message);
        }

        if (data.datetime_from != null && data.datetime_from.length > 0) {
            $(formId + '  #reportEditDateTimeFrom').val(data.datetime_from);
        }
        if (data.datetime_to != null && data.datetime_to.length > 0) {
            $(formId + '  #reportEditDateTimeTo').val(data.datetime_to);
        }

        //set the notify me methods
        if (Number(data.notify_by_email) === 1) {
            $(formId + ' .notify-me-by-email').prop('checked', true);
        }
        if (Number(data.notify_by_sms) === 1) {
            $(formId + ' .notify-me-by-sms').prop('checked', true);
        }
        if (Number(data.notify_by_voice) === 1) {
            $(formId + ' .notify-me-by-voice').prop('checked', true);
        }

        //disable the operations for past requests
        if (data.forbid_editing == 1) {
            $('#saveEdit').hide();
            $('#cancelEdit').hide();
        } else {
            $('#saveEdit').show();
            $('#cancelEdit').show();
        }
    }

    //submitting the report with FormData.
    function submit_report() {
        // join the form data from 2 forms
        let additionalData = $("#report_modaladditional_info_form").serialize();

        // Vendor detail
        let vendor_id = $("#recipient-report-vendor").val();
        let vendor_estimated_price = $("#recipient-vendor-estimatedprice")
            .val(); // estimated price for the job for the vendor

        // get the material provided data
        let materialProvidedFormData = $("#editMaterialReport").serialize();

        // Handyman detail
        let handyman_id = $("#handyman-report-select").val();

        var reportFormData = new FormData(document.getElementById("reportIssue"));
        reportFormData.append('reportUserId', user_id);
        reportFormData.append('action', 'add_request');
        reportFormData.append('vendor_id', vendor_id);
        reportFormData.append('handyman_id', handyman_id);
        reportFormData.append('material_provided', materialProvidedFormData);
        reportFormData.append('vendor_estimated_price', vendor_estimated_price);
        reportFormData.append('task_detail_form', additionalData);

        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: reportFormData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#new_request_loader").show(); // show the loader
                $('#submitReport').attr("disabled", true);
            },
            success: function() {
                $("#new_request_loader").fadeOut();
                // window.location.replace('requests?unit_id=' + user_unit_id);
            },
            error: function() {
                console.log("submit_report() error");
                $('#submitReport').attr("disabled", false);
            }
        });

    }

    //submitting the edit with FormData
    function submit_edit(request_id) {
        var closedStatusIdValues = [4, 17, 21]; // These are possible values that correspond to Close status of the request
        var editFormData = new FormData(document.getElementById("edit-request"));
        editFormData.append('action', 'edit_request');
        editFormData.append('request_id', request_id);
        editFormData.append('user_id', user_id);
        editFormData.append('user_level', user_level);
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: editFormData,
            processData: false,
            contentType: false,
            // async: false,
            success: function() {
                // Show the Payment Tab when the status is changed to closed ( possible status ID values : 4,17,21 )
                var request_status_updated = parseInt($("#edit-request").find("#request-status :selected")
                    .val());
                var requestStatusIsClosed = $.inArray(request_status_updated, closedStatusIdValues);
                if (requestStatusIsClosed != -1) {
                    // Status is changed to closed
                    $("#payment_tag").show();
                    $("#request_op_success_closed").show();
                } else {
                    alert("Status is updated.");
                    // request status is not changed to closed - no need to show the payment tab - so refresh the page
                    // window.location.replace('requests?unit_id=' + user_unit_id);
                }
            },
            error: function(result) {
                console.log("error in submitting the edit" + result);
            }
        });
    };


    //--------------------  paging  -----------------

    function curent_issue_paging_event_binding() {
        //previous/next events for current isuses
        $('#current_issues_previous_page a').click(function() {
            var current_page_id = $('#current_issues_paging .active').children().first().attr('id');
            var current_page_number = parseInt(current_page_id.substr(5));
            if (current_page_number > 1) {
                get_current_issue_list(current_page_number - 1);
            }
        });

        $('#current_issues_next_page a').click(function() {
            var current_page_id = $('#current_issues_paging .active').children().first().attr('id');
            var current_page_number = parseInt(current_page_id.substr(5));
            var total_page_number = $('#current_page_number').val();
            if (current_page_number < total_page_number) {
                get_current_issue_list(current_page_number + 1);
            }
        });
    }

    //call it after first loading
    curent_issue_paging_event_binding();


    function past_issue_paging_event_binding() {
        //previous/next events for past isuses
        $('#past_issues_previous_page a').click(function() {
            var past_page_id = $('#past_issues_paing .active').children().first().attr('id');
            var past_page_number = parseInt(past_page_id.substr(5));
            if (past_page_number > 1) {
                get_past_issue_page(past_page_number - 1);
            }
        });

        $('#past_issues_next_page a').click(function() {
            var past_page_id = $('#past_issues_paing .active').children().first().attr('id');
            var past_page_number = parseInt(past_page_id.substr(5));
            var total_page_number = $('#past_page_number').val();
            if (past_page_number < total_page_number) {
                get_past_issue_page(past_page_number + 1);
            }
        });
    }

    //call it after first loading
    past_issue_paging_event_binding();


    function update_current_pagination(page_number) {
        $('#current_page_number').val(page_number);
        var current_paging_ul = $('#current_issues_paging');
        current_paging_ul.empty();

        var string =
            '<li class="disabled" id="current_issues_previous_page"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>' +
            '<li class="active"><a href="#" id="page_1" onclick="get_current_issue_list(1)" >1</a></li>';
        for (var n = 2; n <= page_number; n++) {
            string += '<li><a href="#" id="page_' + n + '" onclick="get_current_issue_list(' + n + ')">' + n + '</a></li>';
        }
        string +=
            '<li id="current_issues_next_page"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        current_paging_ul.append(string);

        //event rebinding
        curent_issue_paging_event_binding();
    }


    function update_past_pagination(page_number) {
        $('#past_page_number').val(page_number);
        var past_paging_ul = $('#past_issues_paing');
        past_paging_ul.empty();

        var string =
            '<li class="disabled" id="past_issues_previous_page"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>' +
            '<li class="active"><a href="#" id="page_1" onclick="get_past_issue_page(1)" >1</a></li>';
        for (var n = 2; n <= page_number; n++) {
            string += '<li><a href="#" id="page_' + n + '" onclick="get_past_issue_page(' + n + ')">' + n + '</a></li>';
        }
        string +=
            '<li id="past_issues_next_page"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        past_paging_ul.append(string);

        //event rebinding
        past_issue_paging_event_binding();
    }

    function get_current_issue_list(page_number) {
        var filter_building_id = $('#filter_building_current').val();
        var filter_category = $('#filter_category_current').val();
        var filter_status = $('#filter_status_current').val();
        var filter_unit = $('#filter_units_current').val();
        var filter_from = $('#filter_created_from_current').val();
        var filter_to = $('#filter_created_to_current').val();
        var filter_employee_id = $('#filter_employee_current').val();
        var filter_order = $('#order_by_current').val();
        var filter_read_category = $('#filter_read_category').val();

        $.ajax({
            type: "post",
            url: relative_path + "request_info_paging_controller.php",
            data: {
                action: "get_current_issue_page",
                user_id: user_id,
                page_number: page_number,
                user_unit_id: user_unit_id,
                filter_building_id: filter_building_id,
                filter_category: filter_category,
                filter_status: filter_status,
                filter_unit: filter_unit,
                filter_from: filter_from,
                filter_to: filter_to,
                filter_employee_id: filter_employee_id,
                filter_order: filter_order,
                filter_read_category: filter_read_category
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                update_current_issue_page(result, page_number);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }


    function update_current_issue_page(data, page_number) {
        var data_arr = data.data_content;

        $('#current_issue_tbody').empty();

        for (i in data_arr) {
            var id = 'issue_row_' + data_arr[i].request_id;
            var persson_info = 'Telephone : ' + data_arr[i].creator_mobile + ' Email :' + data_arr[i].creator_email;

            //request level highlight
            var level_label_class = '';
            if (data_arr[i].request_level == 'SERIOUS') {
                level_label_class = 'level-label-serious';
            } else if (data_arr[i].request_level == 'URGENT') {
                level_label_class = 'level-label-urgent';
            }

            //open or closed text color
            var txt_class = 'txt-black';
            if (data_arr[i].issue_status == 'CLOSED') {
                txt_class = 'txt-grey';
            }

            $('#current_issue_tbody').append('<tr class="' + data_arr[i].style_class + ' issue-line ' + txt_class +
                ' " id="' + id + '" data-request="' + data_arr[i].request_id + '">\n' +
                '              <td class="col-md-1 text-center">' + data_arr[i].detailed_status + '</td>\n' +
                '              <td class="col-md-2 text-center">' + data_arr[i].request_type + '</td>\n' +
                '              <td class="col-md-1 text-center ' + level_label_class + '">' + data_arr[i]
                .request_level + '</td>\n' +
                '              <td class="col-md-2 text-center">' + data_arr[i].created_time + '</td>\n' +
                '              <td class="col-md-1 text-center">' + data_arr[i].interval + '</td>\n' +
                '              <td class="col-md-1 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                persson_info + '">' + data_arr[i].creator_full_name + '</td>\n' +
                '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                data_arr[i].closed_by + '">' + data_arr[i].closed_by + '</td>\n' +
                '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                data_arr[i].address + '">' + data_arr[i].address + '</td>\n' +
                '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                data_arr[i].message + '">' + data_arr[i].message + '</td>\n' +
                '            </tr>');
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // change active class for a tags
        $('#current_issues_paging li').removeClass('active');
        var page_id_seletor = '#current_issues_paging #page_' + page_number;
        $(page_id_seletor).parent().addClass('active');

        //change the previous/next buttons status
        var total_page_number = $('#current_page_number').val();
        $('#current_issues_previous_page').removeClass('disabled');
        $('#current_issues_next_page').removeClass('disabled');

        if (page_number == 1) {
            $('#current_issues_previous_page').addClass('disabled');
        }

        if (page_number == total_page_number) {
            $('#current_issues_next_page').addClass('disabled');
        }

    };

    function get_past_issue_page(page_number) {
        var filter_building_id = $('#filter_building_past').val();
        var filter_category = $('#filter_category_past').val();
        var filter_status = $('#filter_status_past').val();
        var filter_unit = $('#filter_units_past').val();
        var filter_from = $('#filter_created_from_past').val();
        var filter_to = $('#filter_created_to_past').val();
        var filter_employee_id = $('#filter_employee_past').val();
        var filter_order = $('#order_by_past').val();

        $.ajax({
            type: "post",
            url: relative_path + "request_info_paging_controller.php",
            data: {
                action: "get_past_issue_page",
                user_id: user_id,
                page_number: page_number,
                user_unit_id: user_unit_id,
                filter_building_id: filter_building_id,
                filter_category: filter_category,
                filter_status: filter_status,
                filter_unit: filter_unit,
                filter_from: filter_from,
                filter_to: filter_to,
                filter_employee_id: filter_employee_id,
                filter_order: filter_order
            },
            dataType: "json",
            success: function(result) {
                update_past_issue_page(result, page_number);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }

    function update_past_issue_page(data, page_number) {
        var data_arr = data.data_content;
        $('#past_issues_tbody').empty();
        for (i in data_arr) {
            var id = 'issue_row_' + data_arr[i].request_id;
            var persson_info = 'Telephone : ' + data_arr[i].creator_mobile + ' Email :' + data_arr[i].creator_email;

            //request level highlight
            var level_label_class = '';
            if (data_arr[i].request_level == 'SERIOUS') {
                level_label_class = 'level-label-serious';
            } else if (data_arr[i].request_level == 'URGENT') {
                level_label_class = 'level-label-urgent';
            }

            $('#past_issues_tbody').append('<tr class="' + data_arr[i].style_class + ' issue-line" id="' + id +
                '" data-request="' + data_arr[i].request_id + '">\n' +
                '              <td class="col-md-1 text-center">' + data_arr[i].last_update_time + '</td>\n' +
                '              <td class="col-md-2 text-center">' + data_arr[i].request_type + '</td>\n' +
                '              <td class="col-md-1 text-center ' + level_label_class + '">' + data_arr[i]
                .request_level + '</td>\n' +
                '              <td class="col-md-1 text-center">' + data_arr[i].created_time + '</td>\n' +
                '              <td class="col-md-1 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                persson_info + '">' + data_arr[i].creator_full_name + '</td>\n' +
                '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                data_arr[i].address + '">' + data_arr[i].address + '</td>\n' +
                '              <td class="col-md-3 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
                data_arr[i].message + '">' + data_arr[i].message + '</td>\n' +
                '            </tr>');
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // change active class for a tags
        $('#past_issues_paing li').removeClass('active');
        var page_id_seletor = '#past_issues_paing #page_' + page_number;
        $(page_id_seletor).parent().addClass('active');

        //change the previous/next buttons status
        var total_page_number = $('#past_page_number').val();
        $('#past_issues_previous_page').removeClass('disabled');
        $('#past_issues_next_page').removeClass('disabled');

        if (page_number == 1) {
            $('#past_issues_previous_page').addClass('disabled');
        }

        if (page_number == total_page_number) {
            $('#past_issues_next_page').addClass('disabled');
        }
    };


    //------------------------ filter (current issues)-----------------

    $('#search_current').click(function() {
        current_issue_filtered();
    });

    $('#default_current').click(function() {
        $('#filter_building_current option').first().attr('selected', 'selected');
        $('#filter_category_current option').first().attr('selected', 'selected');
        $('#filter_status_current option').first().attr('selected', 'selected');
        $('#filter_units_current option').first().attr('selected', 'selected');
        $('#filter_units_current').attr('disabled', true);
        $('#filter_created_from_current').val('');
        $('#filter_created_to_current').val('');
        $('#filter_employee_current option').first().attr('selected', 'selected');
        $('#order_by_current option').first().attr('selected', 'selected');
        $('#filter_tenant_current').val('');
        $('#filter_read_category option').first().attr('selected', 'selected');
        $("#request_type_detail").val("all").trigger("change");

        current_issue_filtered();
    });


    $('#filter_building_current').change(function() {
        var selected_building_id = $('#filter_building_current').val();
        if (selected_building_id != 'all') {
            $('#filter_units_current').attr('disabled', false);
            $.ajax({
                type: "post",
                url: relative_path + "request_info_controller.php",
                data: {
                    action: "get_units",
                    building_id: selected_building_id
                },
                dataType: "json",
                success: function(result) {
                    set_filter_units_current(result);
                },
                error: function(result) {
                    console.log("Error:" + result);
                }
            });
        } else {
            $('#filter_units_current').attr('disabled', true);
            $('#filter_units_current').empty();
            $('#filter_units_current').append('<option value="all" selected>All Units</option>');
        }
    });

    function set_filter_units_current(data) {
        $('#filter_units_current').empty();
        $('#filter_units_current').append('<option value="all" selected>All Units</option>');
        var data_conent = data.data_content;
        for (i in data_conent) {
            $('#filter_units_current').append('<option id="' + data_conent[i].apartment_id + '">' + data_conent[i]
                .unit_number + '</option>');
        }
    }

    function current_issue_filtered() {
        var filter_building_id = $('#filter_building_current').val();
        var filter_category = $('#filter_category_current').val();
        var filter_status = $('#filter_status_current').val();
        var filter_unit = $('#filter_units_current').val();
        var filter_from = $('#filter_created_from_current').val();
        var filter_to = $('#filter_created_to_current').val();
        var filter_employee_id = $('#filter_employee_current').val();
        var filter_order = $('#order_by_current').val();
        var filter_tenant = $('#filter_tenant_current').val();
        var filter_read_category = $('#filter_read_category').val();
        var request_type_detail = $('#request_type_detail').val();
        var vendor_id = null;

        if (location.search.indexOf('vid=') >= 0) {
            vendor_id = (location.search.split('vid=')[1] || '').split('&')[0];
        }

        $.ajax({
            type: "post",
            url: relative_path + "request_info_paging_controller.php",
            data: {
                action: "get_current_issue_page",
                user_id: user_id,
                page_number: 1,
                user_unit_id: user_unit_id,
                filter_building_id: filter_building_id,
                filter_category: filter_category,
                filter_status: filter_status,
                filter_unit: filter_unit,
                filter_from: filter_from,
                filter_to: filter_to,
                filter_employee_id: filter_employee_id,
                filter_order: filter_order,
                filter_tenant: filter_tenant,
                filter_read_category: filter_read_category,
                request_type_detail: request_type_detail,
                vendor_id: vendor_id
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                update_current_issue_page(result, 1);
                update_current_pagination(result.total_pages);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });

    }


    //------------------------ filter (past issues)-----------------

    $('#search_past').click(function() {
        filtered_past();
    });

    $('#default_past').click(function() {
        $('#filter_building_past option').first().attr('selected', 'selected');
        $('#filter_category_past option').first().attr('selected', 'selected');
        $('#filter_status_past option').first().attr('selected', 'selected');
        $('#filter_units_past option').first().attr('selected', 'selected');
        $('#filter_units_past').attr('disabled', true);
        $('#filter_created_from_past').val('');
        $('#filter_created_to_past').val('');
        $('#filter_employee_past option').first().attr('selected', 'selected');
        $('#order_by_past option').first().attr('selected', 'selected');
        $('#filter_tenant_past').val('');
        filtered_past();
    });


    $('#filter_building_past').change(function() {
        var selected_building_id = $('#filter_building_past').val();
        if (selected_building_id != 'all') {
            $('#filter_units_past').attr('disabled', false);
            $.ajax({
                type: "post",
                url: relative_path + "request_info_controller.php",
                data: {
                    action: "get_units",
                    building_id: selected_building_id
                },
                dataType: "json",
                success: function(result) {
                    set_filter_units_past(result);
                },
                error: function(result) {
                    console.log("Error:" + result);
                }
            });
        } else {
            $('#filter_units_past').attr('disabled', true);
            $('#filter_units_past').empty();
            $('#filter_units_past').append('<option value="all" selected>All Units</option>');
        }

    });


    function set_filter_units_past(data) {
        $('#filter_units_past').empty();
        $('#filter_units_past').append('<option value="all" selected>All Units</option>');
        var data_conent = data.data_content;

        for (i in data_conent) {
            $('#filter_units_past').append('<option id="' + data_conent[i].apartment_id + '">' + data_conent[i]
                .unit_number + '</option>');
        }
    }


    function filtered_past() {
        var filter_building_id = $('#filter_building_past').val();
        var filter_category = $('#filter_category_past').val();
        var filter_status = $('#filter_status_past').val();
        var filter_unit = $('#filter_units_past').val();
        var filter_from = $('#filter_created_from_past').val();
        var filter_to = $('#filter_created_to_past').val();
        var filter_employee_id = $('#filter_employee_past').val();
        var filter_order = $('#order_by_past').val();
        var filter_tenant = $('#filter_tenant_past').val();

        $.ajax({
            type: "post",
            url: relative_path + "request_info_paging_controller.php",
            data: {
                action: "get_past_issue_page",
                user_id: user_id,
                page_number: 1,
                user_unit_id: user_unit_id,
                filter_building_id: filter_building_id,
                filter_category: filter_category,
                filter_status: filter_status,
                filter_unit: filter_unit,
                filter_from: filter_from,
                filter_to: filter_to,
                filter_employee_id: filter_employee_id,
                filter_order: filter_order,
                filter_tenant: filter_tenant
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                update_past_issue_page(result, 1);
                update_past_pagination(result.total_pages);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });

    }


    //------------------------------ bulletins --------------------------

    function init_new_bulletin() {
        $.ajax({
            type: "post",
            url: relative_path + "request_bulletins_controller.php",
            data: {
                action: "get_new_bulletin",
                user_id: user_id
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_new_bulletin(result);
            },
            error: function(result) {
                console.log("error in inti_new_bulletin()" + result);
            }
        });
    }

    function set_new_bulletin(data) {
        $('#bulletinBuilding').empty();
        var building_arr = data;
        var content = '';
        for (var i in building_arr) {
            content += '<option value="' + building_arr[i].building_id + '">' + building_arr[i].building_name + '</option>';
        }
        $('#bulletinBuilding').append(content);
    }


    function init_bulletin_modal(bulletin_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_bulletins_controller.php",
            data: {
                action: "get_bulletin_info",
                bulletin_id: bulletin_id,
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_bulletin_modal_view(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }


    function submit_new_bulletin() {
        var bulletinFormData = new FormData(document.getElementById("bulletinForm"));
        bulletinFormData.append('reportUserId', user_id);
        bulletinFormData.append('action', 'add_bulletin');
        $.ajax({
            type: "post",
            url: relative_path + "request_bulletins_controller.php",
            data: bulletinFormData,
            processData: false,
            contentType: false,
            // async: false,
            success: function() {
                window.location.reload(true);
            },
            error: function() {
                console.log("submit_new_bulletin() error");
            }
        });
    }

    function set_bulletin_modal_view(data) {
        $('#bulletin_shown_id').text(data.bulletin_id);
        $('#bulletin_creator_name').text(data.issuer_name);
        $('#bulletin_creator_telephone').text(data.issuer_telephone);
        $('#issued_date').text(data.issue_date);
        $('#issued_time').text(data.issue_time);
        $('#bullent_modal_title').text(data.title);
        $('#bullent_modal_content').text(data.content);
    }

    function set_bulletin_reading_status(bulletin_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_bulletins_controller.php",
            data: {
                action: "get_bulletin_reading_status",
                bulletin_id: bulletin_id,
            },
            dataType: "json",
            // async: false,
            success: function(result) {
                set_bulletin_read_status_view(result)
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }


    function set_bulletin_read_status_view(data) {
        $('#bulletin_read_status_tbody').empty();
        var readed_tenant_lst = data.data_content;

        for (var i in readed_tenant_lst) {
            $('#bulletin_reading_status').append('<tr>\n' +
                '<td class="col-md-3 text-center">' + readed_tenant_lst[i].full_name + '</td>\n' +
                '<td class="col-md-3 text-center">' + readed_tenant_lst[i].username + '</td>\n' +
                '<td class="col-md-3 text-center">' + readed_tenant_lst[i].last_login_time + '</td>\n' +
                '</tr>');
        }
    }


    function close_bulletin(bulletin_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_bulletins_controller.php",
            data: {
                action: "close_bulletin",
                bulletin_id: bulletin_id,
            },
            dataType: "json",
            async: false,
            success: function(result) {
                if (result.status == 'success') {
                    $('#bulletin_close_' + bulletin_id).hide();
                    $('#bulletin_close_' + bulletin_id).parent().append('<span>CLOSED<span>');
                }
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    }

    //---------------------- handyman time slot ----------------
    function update_handyman_date_slot(building_id, request_type_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_handyman_controller.php",
            data: {
                action: "get_avail_date",
                building_id: building_id,
                request_type_id: request_type_id
            },
            dataType: "json",
            success: function(result) {
                if (result.status === 'success') {
                    $('#handyman_avail_date').unbind('change');
                    render_avail_time_view(result.content);
                } else {
                    console.log("error: no data");
                }
            },
            error: function(result) {
                console.error("error: " + result);
            }
        });
    }

    function render_avail_time_view(date) {
        var content = '';
        for (var i = 0; i < date.length; i++) {
            content += '<option value="' + date[i].slot_id + '">' + date[i].date + '</option>';
        }
        var handyman_avail_data = $('#handyman_avail_date');
        handyman_avail_data.empty();
        handyman_avail_data.append(content);
        handyman_avail_data.change(function() {
            var selected_slot_id = $(this).val();
            update_handyman_time_slot(selected_slot_id);
        });
    }

    function update_handyman_time_slot(slot_id) {
        $.ajax({
            type: "post",
            url: relative_path + "request_handyman_controller.php",
            data: {
                action: "get_avail_datetime",
                slot_id: slot_id
            },
            dataType: "json",
            success: function(result) {
                if (result.status === 'success') {
                    $('#reportVisitDuration').val(result.content.duration);
                    render_handyman_time_slot_view(result.content.slots);
                } else {
                    console.log("error");
                }
            },
            error: function(result) {
                console.error("error: " + result);
            }
        });
    }

    function render_handyman_time_slot_view(data) {
        var report_reserve_time = $('#report_reserve_time');
        report_reserve_time.empty();
        var content = '';
        for (var i = 0; i < data.length; i++) {
            content += '<option value="' + data[i] + '">' + data[i] + '</option>';
        }
        report_reserve_time.append(content);
    }

    //---------------------- document ready ---------------------

    // to avoid open model during loading page
    $(document).ready(function() {

        var request_id = null;

        setTimeout(function() {
            // check if rid and action=rview parameter is set
            if (location.search.indexOf('action=') >= 0) {
                let action = (location.search.split('action=')[1] || '').split('&')[0];

                if (action == "rview") {
                    if (location.search.indexOf('rid=') >= 0) {
                        let request_id = (location.search.split('rid=')[1] || '').split('&')[0];
                        $("#request-num-val").html("#" + request_id);
                        $("#viewrequest_tag").trigger("click");
                        init_modal(request_id);
                        $('#communication_tag').trigger("click");
                    }
                }
            }
        }, 10);

        $('[data-toggle="popover"]').popover();
        // $('.issue-line').attr('data-toggle', 'modal');

        $('.issue-line').on('click', function() {
            let request_id = $(this).attr("data-request");
            alert(request_id);
        });

        // Sharan's code for the payment tab file upload in request modal
        var paymentForm = $("#edit-payment");

        $('#paymentinvoicefile').on('change', function() {
            var file = this.files[0];
            let invoiceNumber = $("#paymentinvoicenum")
                .val(); // invoice number for this particular attached invoice file

            $('#paymentinv_form_progress').attr({
                value: 0,
                max: 0
            });

            var formdata = new FormData($('#paymentinv_form')[0]);
            formdata.append('action', 'upload_invoice');
            formdata.append('upload_inv', file);
            $("#paymentinv_form_progress").show();
            $.ajax({
                url: relative_path + "request_payment_controller.php",
                type: 'POST',
                data: formdata,
                cache: false,
                dataType: "json",
                contentType: false,
                processData: false,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        // For handling the progress of the upload
                        myXhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                $('#paymentinv_form_progress').attr({
                                    value: e.loaded,
                                    max: e.total,
                                });
                            }
                        }, false);
                    }
                    return myXhr;
                },
                success: function(response) {
                    if (response.result) {
                        let invoiceFullName = invoiceNumber + "=" + response.name;
                        let hiddenFileName =
                            "<input type='hidden' style='display:none;' name='invoice_attached[]' value='" +
                            invoiceFullName + "' >";
                        $("#edit-payment").find("#file-holder-payments").append(hiddenFileName);
                    }
                }
            });

            // Also see .name, .type
        });

        // Material provided - onclick
        $('.request-material-provided').on("click", function() {
            let valueSelected = $(this).val();
            if (valueSelected == 0) {
                $("#material_report_tag").hide();
            } else {
                $("#material_report_tag").show().trigger("click");
            }
        });

        // Task type - onclick - show the respective fields
        // Radio values - 1: fixed event , 0: Request
        $('.request-newreportTasktype').on("click", function() {
            let valueSelected = $(this).val();
            $("#projectTaskTypeOnlyShow").hide();

            if (valueSelected == 2) {
                $("#projectTaskTypeOnlyShow").show();
            }

            if (valueSelected == 0 || valueSelected == 2) {
                // Task type is request
                $("body").find("#reportModal").find(".removeForRequestTaskType").hide();
                $("body").find("#reportModal").find(".removeForFixedEventType").show();

                $("#report-donotsettaskdatetime").trigger("click");
                // $("body").find("#task-fixed-event-type-wrapper").hide();
                // $("body").find("#task-request-type-wrapper").show();
            } else {
                $("body").find("#reportModal").find(".removeForRequestTaskType").show();
                $("body").find("#reportModal").find(".removeForFixedEventType").hide();
            }
        });

        $('.request-settask-datetime').on("click", function() {
            let valueSelected = $(this).val();
            $("#requestSetTaskDateTimePicker").val("");
            if (valueSelected == 0) {
                $(".taskdatetimeFormInput").hide();
            } else {
                $(".taskdatetimeFormInput").show();
            }
        });

        // add new lines for the material detail
        $("#addMoreMaterial").on("click", function(e) {
            e.preventDefault();
            // copy the prototype and append into the material_detail_wrap div
            let prototype = $("#add_more_material_proto")
                .html(); // html elements of the prototype of the material detail
            $("#material_detail_wrap").append(prototype);
        });

        // add new lines for the material detail in the edit request modal
        $("#editRequestaddMoreMaterial").on("click", function(e) {
            e.preventDefault();
            // copy the prototype and append into the material_detail_wrap div
            let prototype = $("#editRequestadd_more_material_proto")
                .html(); // html elements of the prototype of the material detail
            $("#editRequestmaterial_detail_wrap").append(prototype);
        });

        // remove the appended material detail - removes the added material divs in both the new task and the edit request modals
        $("body").on("click", ".remove-material-detail", function() {
            // find the nearest div with the class : material-wrap-main and delete it
            let nearestWrap = $(this).closest(".material-wrap-main");
            nearestWrap.remove();
        });

        // Save Button click in the Payment tab in the request modal
        $("#savePaymentdetails").on("click", function() {
            let payment_job_detail = $("#request_payinfo").val();
            let payment_hours = $("#request_payhours").val();
            let payment_expenses = $("#request_pay_expenses").val();
            let request_id = $(this).attr("data-rid");
            let vendor_id = $(this).attr("data-vendorid");

            var form = $("#edit-payment").serialize();

            $.ajax({
                url: relative_path + "request_payment_controller.php",
                type: 'POST',
                data: {
                    data: form,
                    action: 'update_payment',
                    requestid: request_id,
                    vendorid: vendor_id
                },
                success: function(data) {
                    alert("Payment Data updated for this request.");
                }
            });
        });

        // Approval confirm button click
        $("#savePaymentApproval").on("click", function() {
            let payment_approval_checked = $("#request_is_payapprove").prop("checked");
            let payment_approval_finalamt = $("#request_approve_finalamt").val();
            let payment_approval_comments = $("#request_approve_comments").val();
            let request_id = $("#savePaymentdetails").attr("data-rid");

            if (!payment_approval_checked) {
                // do nothing if the approval checkbox is not checked
                return;
            }

            $.ajax({
                url: relative_path + "request_payment_controller.php",
                type: 'POST',
                data: {
                    action: 'approve_payment_details',
                    requestid: request_id,
                    comment: payment_approval_comments,
                    amount: payment_approval_finalamt
                },
                success: function(response) {
                    $("#request-payapprove-form-div").hide();
                    $("#savePaymentdetails").attr("disabled", true);
                    $("#request_approve_alert").html("Payment approved for this request.")
                        .show();
                }
            });
        });

        $('#category-internal').click(function(event) {
            $("#current_issues_a").trigger("click");
            console.log("x");
            $('#filter_category_current option[value=1]').attr("selected", true);
            $('#filter_category_current option[value=2]').attr("selected", false);
            $('#filter_category_current option[value=0]').attr("selected", false);
            current_issue_filtered();
        });

        $('#category-fixed').click(function(event) {
            $("#fixed_events_tabli > a").trigger("click");
            $("#fixed_events_tabli").toggleClass("hidden");
        });

        $('#category-tenant').click(function(event) {
            $("#current_issues_a").trigger("click");
            $('#filter_category_current option[value=1]').attr("selected", false);
            $('#filter_category_current option[value=2]').attr("selected", true);
            $('#filter_category_current option[value=0]').attr("selected", false);
            current_issue_filtered();
        });

        $('#unread_request_mark').click(function(event) {
            $("#current_issues_a").trigger("click");
            $('#filter_read_category option[value=all]').attr("selected", false);
            $('#filter_read_category').val(1).trigger("change");
            current_issue_filtered();
        });
    });
</script>
<?php
if ($user_id > 100000 && $user_id < 200000) {
?>
    <script>
        $(document).ready(function() {
            $('.remove-for-tenant').remove();
            $('#unread_request_mark').addClass('col-md-offset-8');
            $('#filter-part-current').hide();
            $("#new_request_modal_title").html("Add a New Request");
        });
    </script>
<?php
}

if (isset($_GET['direct']) && $_GET['direct'] == 'report') {
?>
    <script>
        $(document).ready(function() {
            $('#startReport').trigger('click');
        });
    </script>
<?php
}
?>

<div id="body-loader" style="display: none;"></div>
<script>
    $(function() {
        var $body_loading = $('#body-loader').hide();
        $(document)
            .ajaxStart(function() {
                $body_loading.show();
                $("body").find("container").addClass("loader-on");
            })
            .ajaxStop(function() {
                $body_loading.hide();
                $("body").find("container").removeClass("loader-on");
            });
    });
</script>
<style>
    #body-loader {
        position: fixed;
        background: url('http://beaveraittesting.site/admin/custom/body-loader.gif') no-repeat center center;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2000;
    }

    .loader-on {
        opacity: 0.4;
    }

    #recipient-report-vendor {
        font-family: 'FontAwesome', 'arial';
    }

    .hidden {
        display: none;
    }
</style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.min.css" />
<link rel="stylesheet" href="css/bootstrap-datetimepicker.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.css" />
<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<!-- FILE UPLOAD SCRIPT FILE -->
<script src="js/custom-fileupload.js"></script>

<?php
function timediff($begin_time, $end_time)
{
    $timediff = $end_time - $begin_time;

    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}
?>