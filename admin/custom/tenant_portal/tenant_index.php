<!-- <script src="https://cdn.rawgit.com/muicss/loadjs/4.2.0/dist/loadjs.min.js"></script><script>var loadjs = require('loadjs');</script> -->
<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!---------------genral---------------------->

<link href="custom/tenant_portal/css/style.css" rel="stylesheet" type="text/css">

<link href="custom/tenant_portal/css/responsive.css" rel="stylesheet" type="text/css">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

<!--[if lt IE 9]>

<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

<![endif]-->

<?php

$user_id = $_SESSION['UserID'];

$user_level = $_SESSION['UserLevel'];
//echo("UD=".$user_id);
include_once('../pdo/Class.Tenant.php');
$DB_tenant = new Tenant($DB_con);
$tenant_infos = $DB_tenant->getTenantInfo($user_id);
include_once('../pdo/Class.Company.php');
$DB_company = new Company($DB_con);
$renewal_notification_day = $DB_company->getRenewalNotificationDay($tenant_infos['company_id']);


//$user_id=100144;

include_once("../pdo/dbconfig.php"); //  connect to database and select the correct database

include_once("../pdo/Class.TenantLease.php"); // To get the new written class

$DB_tenantLease = new TenantLease($DB_con); // create new object


$lease_status_array = $DB_tenantLease->getTableToArray("lease_status");

//var_dump($lease_status_array[3]['name']);


$LeaseInfos = $DB_tenantLease->getTenantLeaseInfo($user_id);

//var_dump($LeaseInfos);


echo "<ul class='aptsnavpills nav nav-tabs '>\n";


//foreach ($LeaseInfos as $key) {

//    $building_name = $key['building_name'];


for ($i = 0; $i < count($LeaseInfos); $i++) {
    $building_name   = $LeaseInfos[$i]['building_name'];
    $unit_number     = $LeaseInfos[$i]['unit_number'];
    $tenant_ids      = $LeaseInfos[$i]['tenant_ids'];
    $apartment_id    = $LeaseInfos[$i]['apartment_id'];
    $lease_status_id = $LeaseInfos[$i]['lease_status_id'];
    $lease_id        = $LeaseInfos[$i]['id'];
    $lease_start_date        = $LeaseInfos[$i]['start_date'];
    $lease_end_date        = $LeaseInfos[$i]['end_date'];

    for ($j = 0; $j < count($lease_status_array); $j++) {
        if ($lease_status_array[$j]['id'] == $lease_status_id) {
            $lease_status_name = $lease_status_array[$j]['name'];
        }
    }

    if ($i == 0) {
        echo "<li  data-aptid='" . $apartment_id . "' class='nav-item'> <a class='nav-link active' style='font-size: 14px' data-aptid='" . $apartment_id . "' data-toggle=\"tab\" href='#b$i'>$building_name - $unit_number - $lease_status_name </a></li>\n"; //- ".$tenant_infos['full_name']."
    } else
        echo "<li  data-aptid='" . $apartment_id . "' class='nav-item' ><a class='nav-link' data-aptid='" . $apartment_id . "' style='font-size: 14px'  data-toggle=\"tab\" href='#b$i'>$building_name- $unit_number - $lease_status_name </a></li>\n"; //- ".$tenant_infos['full_name']."
}


echo "</ul>";


//$unit=$buildinginfo['unit'];

echo " <div class=\"tab-content\">";

$first_not_paid_lease_id = 0;
// die("cpou=".count($LeaseInfos));
for ($i = 0; $i < count($LeaseInfos); $i++) {

    $apartment_id = $LeaseInfos[$i]['apartment_id'];
    $lease_id     = $LeaseInfos[$i]['id'];
?>
<!--                // id = unit\-id-->
<div id='<?= "b$i"; ?>' class="tab-pane <? if ($i == 0) {
                                                echo 'in active';
                                            } ?> <?php echo "tabcontent_" . $apartment_id; ?>"
    data-aptleaseid="<?php echo $apartment_id . '_' . $lease_id; ?>">
    <!-- Start of Content of Each <?= $i ?> Building-->
    <?php
        $lease_status_id = $LeaseInfos[$i]['lease_status_id'];
        include('tenant_content.php');
        ?>
    <!-- End of Content of Each<?= $i ?> Building-->
</div>
<?php
}
?>
</div>
<script>
loadjs.ready("jquery", function() {
    $(document).ready(function() {
        $('.forwardPaymentModal').on('hidden.bs.modal', function() {
            var apt_id_current_tab = $(".aptsnavpills").children().filter(".active").attr(
                "data-aptid");
            var aptleaseid = $(".tabcontent_" + apt_id_current_tab).attr("data-aptleaseid");
            var radioNameTotalPayment = "payment_when_" + aptleaseid;
            $("input[name=" + radioNameTotalPayment + "][value='0']").prop('checked', true)
                .trigger("change"); // Select the radio button of total Payment
        });
        $("#form-submit-forward").on("click", function() {
            $("#form-")
            $("#process_form_fwd").submit();
        });


    });
});
</script>

<div class="modal fade forwardPaymentModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Have someone else pay for you</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <form method="post" class="form-horizontal" id="process_form_fwd"
                    action="custom/forward_payment_request.php">
                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Name :</div>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <input type="text" id="forward_recipient_name" name="forward_recipient_name"
                                class="form-control"><br><br>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Email :</div>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <input type="email" id="forward_recipient_email" name="forward_recipient_email"
                                class="form-control input-width"><br><br>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 text-center">Recipient Mobile :</div>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <input type="text" id="forward_recipient_mobile" name="forward_recipient_mobile"
                                class="form-control input-width"><br><br>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 text-center">Message to Recipient:</div>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <textarea id="forward_message" name="forward_message" class="form-control" rows="7"
                                cols="56" style="resize: none;"></textarea><br><br>
                        </div>
                    </div>

                    <input type="hidden" name="product" value="Lease">
                    <input type="hidden" name="lease_payment_id" value="">
                    <div class="fwd_lease_payment_ids_div"></div>
                    <input type="hidden" class="fwd_payment_amount" name="payment_amount" value="">
                    <input type="hidden" class="fwd_if_partial_payment" name="if_partial_payment" value="">
                    <input type="hidden" class="fwd_outstanding1" name="outstanding1" value="">
                    <input type="hidden" class="fwd_outstanding2" name="outstanding2" value="">
                    <input type="hidden" class="fwd_outstanding3" name="outstanding3" value="">
                    <input type="hidden" class="fwd_outstanding4" name="outstanding4" value="">

                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <button id="form-submit-forward" class="btn btn-primary">Process Now!</button>
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>