<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body style="background-color: white;">
    <?php
    include_once("../../../pdo/dbconfig.php");
    $event_id = $_GET['event_id'];
    $row = $DB_calendar->get_office_maintenance_event_detail($event_id);
    $notification_preferences = $DB_calendar->getNotificationPreference($event_id);
    $event_building = $row['building_name'];
    $event_building_address = $row['address'];
    $event_building_id = $row['building_id'];
    $event_name = $row['event_name'];
    $person_in_contact = $row['person_in_contact'];
    $contact_number = $row['contact_number'];
    $event_date = $row['event_date'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];
    $event_info = $row['event_info'];
    $event_category = $row['event_category'];
    $event_created_by_employee_id = $row['event_created_by_user_id'];
    $event_created_by_employee = $DB_calendar->get_employee_info($event_created_by_employee_id)['full_name'];
    if ($event_type == 'regular')
        $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
    else
        $event_date_value = date('Y-m-d', strtotime($event_date));
    ?>

    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
    <legend style="padding: 5px;" class="bg-primary">Event Information
        <span style="font-size: 15px;float: right; padding-top: 3px;">
            <i class="fa fa-building-o" style="font-size:20px;color:white;margin-right: 3px;"></i><a href="../../event-list.php?building_id=<?php echo $event_building_id; ?>" style="color: white;" target="_blank"><?php echo $event_building; ?></a>
        </span>
    </legend>

    <div class="col-sm-12 col-xs-12 contact-block-inner">
        <div class="form-group col-md-12">
            <label class="control-label"><b>Event Name:</b> </label> <?php echo $event_name ?>
        </div>
        <div class="form-group col-md-12">
            <label class="control-label"><b>Event Category:</b> </label> <?php echo $event_category ?>
        </div>
        <div class="form-group col-md-12">
            <label class="control-label"><b>Person in Contact:</b></label> <?php echo $person_in_contact ?>
        </div>
        <div class="form-group col-md-12">
            <label class="control-label"><b>Contact Number:</b> </label> <?php echo $contact_number ?>
        </div>
        <div class="form-group col-md-12">
            <label class="control-label"><b>Event Date:</b> </label> <?php echo $event_date_value ?>
        </div>
        <div class="form-group col-md-12" style="word-wrap: break-word;">
            <label class="control-label"><b>Event Location:</b> </label><?php echo $event_building_address ?>
        </div>
        <div class="form-group col-md-12" style="word-wrap: break-word;">
            <label class="control-label"><b>Event Description:</b> </label> <?php echo $event_info ?>
        </div>
        <div class="form-group col-md-12">
            <label class="control-label"><b>Created By:</b> </label> <?php echo $event_created_by_employee; ?>
        </div>

        <legend>Attachments</legend>

        <?php
        $results = $DB_calendar->get_event_uploads($event_id);
        foreach ($results as $row) {
            $upload_id = $row['id'];
            $upload_date = $row['upload_date'];
            $upload_name = $row['upload_name'];
            $upload_url = "./uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name;
        ?>
            <div class="form-group col-md-12">
                <label class="control-label"><a href="<?php echo $upload_url; ?>" target="_blank"><?php echo $upload_name; ?></a></label>
            </div>
        <?php }

        if (count($results) < 1) {
            echo "No attachments available.";
        }
        ?>
        <hr>
        <legend>Notification</legend>
        <div class="col-md-12" style="padding-left: 0px;">
            <div class="form-group col-md-12">
                <label class="checkbox-inline"><input type="checkbox" name="notification[]" id="sms_notif" value="sms_notif" <?php echo $notification_preferences["sms_notify"] == 1 ? 'checked' : ''; ?>>
                    SMS </label>
                <label class="checkbox-inline"><input type="checkbox" name="notification[]" id="email_notif" value="email_notif" <?php echo $notification_preferences["email_notify"] == 1 ? 'checked' : ''; ?>> Email </label>
                <label class="checkbox-inline"><input type="checkbox" name="notification[]" id="voice_notif" value="voice_notif" <?php echo $notification_preferences["voice_notify"] == 1 ? 'checked' : ''; ?>> Voice </label>
            </div>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <div class="form-group col-md-12">
                <label for="notification_when_type">When to Notify?</label>
                <select class="form-control" id="notification_when_type" name="notification_when_type">
                    <?php
                    $notificationWhenTypeValues = array(
                        "default" => "When to receive notification?",
                        "day" => "Day",
                        "month" => "Month",
                        "week" => "Week",
                        "year" => "Year"
                    );
                    foreach ($notificationWhenTypeValues as $value => $text) { ?>
                        <option <?php if ($value == $notification_preferences["notify_when_type"]) {
                                    echo "selected";
                                } ?> value="<?php echo $value; ?>"><?php echo $text; ?></option>
                    <?php }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <div class="form-group col-md-12">
                <label for="notification_when">Notification arrives</label>
                <div class="input-group">
                    <span class="input-group-addon"> <strong> Before </strong></span>
                    <input type="text" class="form-control" id="notification_when" name="notification_when" value="<?php echo $notification_preferences["notify_when"]; ?>">
                    <span class="input-group-addon" id="notification_when_type_val"> <strong>
                            <?php echo $notificationWhenTypeValues[$notification_preferences["notify_when_type"]]; ?>
                        </strong></span>
                </div>
            </div>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <div class="form-group col-md-12">
                <button data-eventid="<?php echo $event_id; ?>" class="btn btn-danger" id="notification_pref_save">Save</button>
            </div>
        </div>
    </div>


</body>

</html>

<?php
function cast_event_date_value($event_date, $event_frequency_type)
{
    if ($event_frequency_type == "day")
        return "workdays";
    else if ($event_frequency_type == "week") {
        $weekday = date('l', strtotime($event_date));
        return $weekday . 's';
    } else if ($event_frequency_type == "month") {
        $month_day = date('d', strtotime($event_frequency_type));
        return $month_day . ' per month';
    } else if ($event_frequency_type == "year") {
        $year_day = date('F,j', strtotime($event_date));
        return $year_day . ' every year';
    }
}
?>

<script>
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

        $("#notification_pref_save").on("click", function() {
            var eventId = $(this).attr("data-eventid");

            var data = {
                sms: $("#sms_notif").prop("checked") ? 1 : 0,
                email: $("#email_notif").prop("checked") ? 1 : 0,
                voice: $("#voice_notif").prop("checked") ? 1 : 0,
                notify_when_type: $("#notification_when_type option:selected").val(),
                notify_when: $("#notification_when").val()
            };

            // Send a ajax request to the controller to update the notification preferences for the selected event id
            $.ajax({
                url: "custom/calendar_visit/notification_controller.php",
                type: "POST",
                data: {
                    action: "save_notif_pref",
                    data: data,
                    event_id: eventId
                },
                success: function(response) {
                    if (response) {
                        alert("Notification preferences are saved.");
                    }
                }
            });
        });

    });
</script>