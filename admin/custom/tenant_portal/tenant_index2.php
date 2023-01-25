<?php
$user_id = $_SESSION['UserID'];
$user_level = $_SESSION['UserLevel'];

include("../pdo/dbconfig.php");
$DB_tenant->record_login_time($user_id);

// get the issues list
$all_issues = $DB_tenant->get_issues_for_tenant($user_id);
$issues = array();

//to remove the past issues,past issues is hidden for tenants
foreach ($all_issues as $one) {
    $issue_status = $one['issue_status'];
    $issue_past_after_days = $one['issue_past_after_days'];
    $last_update_time = date('Y-m-d', strtotime($one['last_update_time']));

    $time_flag = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list

    if ($issue_status == 'closed' && strtotime(date('Y-m-d')) > $time_flag)
        continue;

    array_push($issues, $one);
}
?>

<link rel="stylesheet" href="custom/tenant_portal/css/style.css">
<div id="container">
    <div id="content">

        <div class="col-md-4 col-sm-12 vertical-box">
            <div class="box">
                <div class="box-title">
                    <h3>Rent Payment</h3>
                </div>
                <div class="box-content">
                    <?php
                    $payments = $DB_tenant->get_lease_payments($user_id);
                    $today = date("Y-m-d");
                    $next_due_date = date("Y-m-d", strtotime("+2 month"));
                    $next_due_exist = 0;
                    foreach ($payments as $payment) {
                        if (strtotime($payment['due_date']) > strtotime($today) && strtotime($payment['due_date']) < strtotime($next_due_date)) {
                            $next_due_date = $payment['due_date'];
                            $next_due = $payment['outstanding'];
                            $next_due_payment_id = $payment['lease_payment_id'];
                            $next_due_exist = 1;
                        }
                    }
                    if ($next_due_exist == 1) {

                        $pay_now_btn = '';
                        if ($next_due <= 0)
                            $pay_now_btn = 'disabled';

                    ?>
                        <div class="col-sm-12">
                            <div class="box-content-note">
                                <h3>Here is your next due and due date.</h3>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <div class="due"><span>Next Due</span>
                                    <h3><?php echo "$" . $next_due; ?></h3>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="due"><span>Due Date</span>
                                    <h3><?php echo $next_due_date; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <a href="pay.php?id=<?php echo $next_due_payment_id; ?>"><button class="btn btn-primary" <?php echo $pay_now_btn; ?>>Pay Now!</button></a>
                        </div>
                    <?php } else { ?>
                        <div class="col-sm-12">
                            <div class="box-content-note">
                                <h3>You do not have any coming due.</h3>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="box">
                <div class="box-title">
                    <h3>List of Payments</h3>
                </div>
                <div class="box-content">
                    <?php if ($payments) { ?>
                        <div class="box-table-head">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <!--                <th scope="col">Amount</th>-->
                                        <th scope="col" class="col-sm-3">Due Date</th>
                                        <th scope="col" class="col-sm-2">Paid</th>
                                        <th scope="col" class="col-sm-2">Outstanding</th>
                                        <th scope="col" class="col-sm-1">Inv.</th>
                                        <th scope="col" class="col-sm-2">Pay</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="box-table">
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    foreach ($payments as $payment) { ?>
                                        <tr>
                                            <!--                <th scope="row">--><?php //echo "$".$payment['total_discount'];
                                                                                    ?>
                                            <!--</th>-->
                                            <td class="col-sm-3"><?php echo $payment['due_date']; ?></td>
                                            <td class="col-sm-2"><?php echo $payment['paid']; ?></td>
                                            <td class="col-sm-2"><?php echo $payment['outstanding']; ?></td>
                                            <td class="col-sm-1">
                                                <?php if ($payment['outstanding'] <= 0) { ?>
                                                    <a href="custom/invoice_receipt/invoice_receipt_controller.php?download_invoice&lease_payment_id=<?php echo $payment['lease_payment_id'] ?>">
                                                        <i class="fa-solid fa-file-pdf fa-2x" aria-hidden="true"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    N/A
                                                <?php } ?>
                                            </td>
                                            <td class="col-sm-2">
                                                <?php if ($payment['outstanding'] > 0) { ?>
                                                    <a href="pay.php?id=<?php echo $payment['lease_payment_id'] ?>">
                                                        <button class="btn btn-primary">Pay</button>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="col-sm-12">
                            <div class="box-content-note">
                                <h3>You do not have any payments.</h3>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 vertical-box">

            <div class="box" style="overflow-y: scroll;">
                <div class="box-title">
                    <h3>Building & Unit Information</h3>
                </div>
                <div class="box-content building-info">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                        <?php $row = $DB_tenant->get_building_info_tenant($user_id); ?>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Building
                                        Schedule</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body"><?php echo $row['rules']; ?></div>
                            </div>
                        </div>


                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Building
                                        Document</a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <?php
                                    $building_documents = $DB_tenant->get_building_documents($user_id);
                                    foreach ($building_documents as $building_document) {
                                        if ($building_document['building_file']) { ?>
                                            <p><a href="<?php echo "files" . $building_document['building_file']; ?>" download><?php echo $building_document['building_file_type']; ?></a></p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>


                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Apartment Document</a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                <div class="panel-body">
                                    <?php $apartment_documents = $DB_tenant->get_apartment_documents($user_id);
                                    foreach ($apartment_documents as $apartment_document) {
                                        if ($apartment_document['apartment_file']) {
                                    ?>
                                            <p><a href="<?php echo "files" . $apartment_document['apartment_file']; ?>" download><?php echo $apartment_document['apartment_file_type']; ?></a></p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFour">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">Lease
                                        Document</a>
                                </h4>
                            </div>
                            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                                <div class="panel-body">
                                    <?php $lease_documents = $DB_tenant->get_lease_documents($user_id);
                                    foreach ($lease_documents as $lease_document) {
                                        if ($lease_document['lease_file']) {
                                    ?>
                                            <p><a href="<?php echo "files" . $lease_document['lease_file']; ?>" download><?php echo $lease_document['lease_file_type']; ?></a></p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFive">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">Tenant
                                        Document</a>
                                </h4>
                            </div>
                            <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
                                <div class="panel-body">
                                    <?php $tenant_documents = $DB_tenant->get_tenant_documents($user_id);
                                    foreach ($tenant_documents as $tenant_document) {
                                        if ($tenant_document['tenant_file']) {
                                    ?>
                                            <p><a href="<?php echo "files" . $tenant_document['tenant_file']; ?>" download><?php echo $tenant_document['tenant_file_type']; ?></a></p>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <!-- ... -->

                    </div>
                </div>
            </div>

            <div class="box">
                <div class="box-title">
                    <h3>Request List</h3>
                    <div class="box-content-button">
                        <a href="requests?action=report"><button class="btn btn-primary">Report a
                                Request</button></a>
                    </div>
                </div>
                <div class="box-content">
                    <?php if ($issues == null) { ?>
                        <div class="col-sm-12">
                            <div class="box-content-note">
                                <h3>You do not have any requests currently.</h3>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="box-table-head">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="col-sm-4">Type</th>
                                        <th scope="col" class="col-sm-4">Status</th>
                                        <th scope="col" class="col-sm-4">Message</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="box-table">
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    foreach ($issues as $row) {
                                        $message = $row['message'];
                                        $type = $row['request_type'];
                                        $status = $row['request_status'];
                                    ?>
                                        <tr>
                                            <td class="col-sm-4"><?php echo $type; ?></td>
                                            <td class="col-sm-4"><?php echo $status; ?></td>
                                            <td class="col-sm-4 text-center non-overflow" data-toggle="tooltip" data-placement="top" data-container="body" title="<?php echo $message; ?>">
                                                <?php echo $message; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>

        <div class="col-md-4 col-sm-12 vertical-box vertical-box-notification">
            <div class="box">
                <div class="box-title">
                    <h3>Bulletins</h3>
                </div>
                <div class="box-content">
                    <div class="box-notification">
                        <?php
                        $bulletins = $DB_tenant->get_tenant_bulletin($user_id);
                        foreach ($bulletins as $bulletin) {
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?php echo $bulletin['message_title']; ?></h3>
                                </div>
                                <div class="panel-body">
                                    <?php echo $bulletin['message_body']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>