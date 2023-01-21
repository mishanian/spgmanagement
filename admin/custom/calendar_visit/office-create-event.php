<?php
$building_id = $_GET['building_id'];
$employee_id = $_SESSION['employee_id'];
?>

<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<script src="custom/calendar_visit/js/bootstrap-datepicker.js"></script>

<style>
label {
    font-weight: normal
}
</style>

<section id="section-body">
    <div class="container">
        <div class="page-title breadcrumb-top">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="event-list.php?building_id=<?php echo $building_id; ?>&direct=inner_events">Office
                                & Maintenance Events</a>
                        </li>
                        <li class="active">Create Office & Maintenance Event</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div id="content-area" class="contact-area">
                    <div class="white-block">
                        <div class="row">
                            <form id="create-regular-event-form"
                                action="custom/calendar_visit/office-controller-event.php" method="post">
                                <input type="hidden" name="building_id" value="<?php echo $building_id ?>" />
                                <input type="hidden" name="employee_id" value="<?php echo $employee_id ?>" />
                                <div class="col-sm-12 col-xs-12 contact-block-inner well">
                                    <div class="col-md-12" style="padding-left: 0px;">
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="event_name">Event Name</label>
                                            <input class="form-control" name="event_name" id="event_name" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="event_category">Event Category</label>
                                            <select class="form-control" name="event_category" id="event_category"
                                                required>
                                                <option value="office" selected>Office</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding-left: 0px;">
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="event_contact">Person in Contact</label>
                                            <input class="form-control" name="event_contact" id="event_contact">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="contact_number">Contact Number</label>
                                            <input class="form-control" name="contact_number" id="contact_number"
                                                pattern="[\+]\d{11}" placeholder="+01231234567">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="event_date">Specific Date</label>
                                            <input type="text" class="form-control date_input" name="event_date"
                                                id="event_date" placeholder="YYYY-MM-DD" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding-left: 0px;">
                                        <div class="checkbox form-group col-md-12">
                                            <label>
                                                <input type="checkbox" id="is_regular" name="is_regular"
                                                    data-toggle="collapse" data-target="#regular_events"
                                                    onchange="is_regular_event()">Fixed events
                                            </label>
                                        </div>
                                    </div>
                                    <div class="collapse col-md-12" id="regular_events">
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="regular_start_date">Start Date</label>
                                            <input type="text" class="form-control date_input" name="regular_start_date"
                                                id="regular_start_date" placeholder="YYYY-MM-DD">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="control-label" for="regular_frequency">Frequency</label>
                                            <select class="form-control" name="regular_frequency"
                                                id="regular_frequency">
                                                <option value="day">Daily</option>
                                                <option value="week">Weekly</option>
                                                <option value="month">Monthly</option>
                                                <option value="year">Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="margin-top: 8px;padding-left: 0px;">
                                        <div class="form-group col-md-6">
                                            <label class="control-label" for="event_info">Event Information</label>
                                            <textarea class="form-control" name="event_info" rows="5" id="event_info"
                                                placeholder="Event location, Event description, Event purpose, Preparing works"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label" for="principals_assigned">Principal
                                                Assigned</label>
                                            <select name="principals_assigned[]" multiple class="form-control"
                                                style="height: 115px;">
                                                <?php
                        include_once("../pdo/dbconfig.php");
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

                                    <div class="col-md-12" style="padding-left: 0px;">
                                        <div class="checkbox form-group col-md-2">
                                            <label>
                                                <input type="checkbox" id="is_other_principal" name="is_other_principal"
                                                    data-toggle="collapse" data-target="#other_principal">Other
                                                Principal
                                            </label>
                                        </div>
                                    </div>

                                    <div class="collapse col-md-12" id="other_principal">
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="other_principal_name">Principal
                                                Name</label>
                                            <input type="text" class="form-control" name="other_principal_name"
                                                id="other_principal_name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="other_principal_telephone">Principal
                                                Telephone</label>
                                            <input type="text" class="form-control" name="other_principal_telephone"
                                                pattern="[\+]\d{11}" placeholder="+01231234567"
                                                id="other_principal_telephone">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" for="other_principal_email">Principal
                                                Email</label>
                                            <input type="text" class="form-control" name="other_principal_email"
                                                id="other_principal_email"
                                                pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="padding-left: 0px;">
                                        <legend>Notification Preference</legend>
                                        <div class="form-group col-md-4">
                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]"
                                                    value="sms_notif">SMS</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]"
                                                    value="email_notif">Email</label>
                                            <label class="checkbox-inline"><input type="checkbox" name="notification[]"
                                                    value="voice_notif">Voice</label>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="notification_when_type">When to Notify?</label>
                                            <select class="form-control" id="notification_when_type"
                                                name="notification_when_type">
                                                <option value="default">When to receive notification?</option>
                                                <option value="day">Day</option>
                                                <option value="month">Month</option>
                                                <option value="week">Week</option>
                                                <option value="year">Year</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="notification_when">Notification arrives</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"> <strong> Before </strong></span>
                                                <input type="text" class="form-control" id="notification_when"
                                                    name="notification_when">
                                                <span class="hidden input-group-addon" id="notification_when_type_val">
                                                    <strong> Days </strong></span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="form-group col-md-12" style="margin-top: 10px;">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-long" name="create_event"
                                            style="width: 115px;">Create</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--end section page body-->

<script type="text/javascript">
function is_regular_event() {
    if ($('#is_regular').get(0).checked) {
        $('#event_date').attr('disabled', true);
        $('#regular_start_date').attr('require', true);
    } else {
        $('#event_date').attr('disabled', false);
        $('#regular_start_date').attr('require', false);
    }
}

function disselection() {
    $("option").attr('selected', false);
}
</script>
<script>
$('.date_input').datepicker({
    format: 'y-MM-dd'
});

$(document).ready(function() {

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

});
</script>

<style>
.hidden {
    display: none;
}
</style>