<?php
session_start();
$building_id = $_GET['building_id'];
$event_id = $_GET['event_id'];
$employee_id = $_SESSION['employee_id'];
?>
<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css"/>

<style>
  label {
    font-weight: normal;
  }
</style>

<section id="section-body">
  <div class="container" style="background: white;">
    <div class="page-title breadcrumb-top">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href="event-list.php?building_id=<?php echo $building_id; ?>&direct=inner_events">Office & Maintenance Events</a></li>
            <li class="active">Event Details</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div id="content-area" class="contact-area">
          <div class="white-block">
            <div class="row">
                <?php
                include_once("../pdo/dbconfig.php");
                $row = $DB_calendar->get_office_maintenance_event_detail($event_id);
                $notification_preferences = $DB_calendar->getNotificationPreference($event_id);
                $event_name = $row['event_name'];
                $contact_person = $row['person_in_contact'];
                $contact_number = $row['contact_number'];
                $event_date = $row['event_date'];
                $event_frequency_type = $row['event_frequency_type'];
                $event_type = $row['event_type'];
                $event_info = $row['event_info'];
                ?>

              <form id="create-event-oneonone-form" action="custom/calendar_visit/office-controller-event.php" method="post">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
                <input type="hidden" name="building_id" value="<?php echo $building_id; ?>"/>
                <div class="col-sm-12 col-xs-12 contact-block-inner">

                  <div class="form-group col-md-3">
                    <label class="control-label" for="event_name">Event Name</label>
                    <input class="form-control" name="event_name" id="event_name" value="<?php echo $event_name ?>">
                  </div>

                  <div class="form-group col-md-3">
                    <label class="control-label" for="contact_person">Person in Contact</label>
                    <input class="form-control" name="contact_person" id="contact_person"
                           value="<?php echo $contact_person ?>">
                  </div>

                  <div class="form-group col-md-3">
                    <label class="control-label" for="contact_number">Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" id="contact_number"
                           value="<?php echo $contact_number; ?>">
                  </div>

                    <?php if ($event_type == "regular") { ?>
                      <div class="form-group col-md-3">
                        <label class="control-label" for="event_date">Specific Date</label>
                        <input type="date" class="form-control" name="event_date" id="event_date"
                               placeholder="dd-mm-yyyy" pattern="\d{1,2}-\d{1,2}-\d{4}">
                      </div>

                      <div class="checkbox form-group col-md-12">
                        <label>
                          <input type="checkbox" id="is_regular" name="is_regular" onchange="is_regular_event()" data-toggle="collapse" data-target="#regular_events">Regular
                          events
                        </label>
                      </div>

                      <div class="collapse col-md-12" id="regular_events">
                        <div class="form-group col-md-3">
                          <label class="control-label" for="regular_start_date">Start Date</label>
                          <input type="date" class="form-control" name="regular_start_date" id="regular_start_date"
                                 placeholder="dd-mm-yyyy" pattern="\d{1,2}-\d{1,2}-\d{4}"
                                 value="<?php echo $event_date; ?>">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label" for="regular_frequency">Frequency</label>
                          <select class="form-control" name="regular_frequency" id="regular_frequency">
                            <option value="day" <?php if ($event_frequency_type == "day") {
                                echo("selected");
                            } ?>>Daily
                            </option>
                            <option value="week" <?php if ($event_frequency_type == "week") {
                                echo("selected");
                            } ?>>Weekly
                            </option>
                            <option value="month" <?php if ($event_frequency_type == "month") {
                                echo("selected");
                            } ?>>Monthly
                            </option>
                            <option value="year" <?php if ($event_frequency_type == "year") {
                                echo("selected");
                            } ?>>Yearly
                            </option>
                          </select>
                        </div>
                      </div>
                    <?php } else { ?>
                      <div class="form-group col-md-3">
                        <label class="control-label" for="event_date">Specific Date</label>
                        <input type="date" class="form-control" name="event_date" id="event_date"
                               placeholder="dd-mm-yyyy" pattern="\d{1,2}-\d{1,2}-\d{4}"
                               value="<?php echo $event_date; ?>">
                      </div>
                      <div class="checkbox form-group col-md-12">
                        <label>
                          <input type="checkbox" id="is_regular" name="is_regular" onchange="is_regular_event()" data-toggle="collapse" data-target="#regular_events">Regular
                          events
                        </label>
                      </div>
                      <div class="collapse col-md-12" id="regular_events">
                        <div class="form-group col-md-3">
                          <label class="control-label" for="regular_start_date">Start Date</label>
                          <input type="text" class="form-control" name="regular_start_date" id="regular_start_date" placeholder="dd-mm-yyyy" pattern="\d{1,2}-\d{1,2}-\d{4}">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label" for="regular_frequency">Frequency</label>
                          <select class="form-control" name="regular_frequency" id="regular_frequency">
                            <option value="day" <?php if ($event_frequency_type == "day") {
                                echo("selected");
                            } ?>>Daily
                            </option>
                            <option value="week" <?php if ($event_frequency_type == "week") {
                                echo("selected");
                            } ?>>Weekly
                            </option>
                            <option value="month" <?php if ($event_frequency_type == "month") {
                                echo("selected");
                            } ?>>Monthly
                            </option>
                            <option value="year" <?php if ($event_frequency_type == "year") {
                                echo("selected");
                            } ?>>Yearly
                            </option>
                          </select>
                        </div>
                      </div>
                    <?php } ?>

                    <div class="col-md-12" style="padding-left: 0px;">
                        <div class="form-group col-md-4">
                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="sms_notif" <?php echo $notification_preferences["sms_notify"] == 1 ? 'checked' : '';?> > SMS </label>
                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="email_notif" <?php echo $notification_preferences["email_notify"] == 1 ? 'checked' : '';?> > Email </label>
                            <label class="checkbox-inline"><input type="checkbox" name="notification[]" value="voice_notif" <?php echo $notification_preferences["voice_notify"] == 1 ? 'checked' : '';?> > Voice </label>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="notification_when_type">When to Notify?</label>
                            <select class="form-control" id="notification_when_type" name="notification_when_type" >
                                <?php
                                $notificationWhenTypeValues = array("default" => "When to receive notification?",
                                    "day" => "Day",
                                    "month" => "Month",
                                    "week" => "Week",
                                    "year" => "Year"
                                );
                                foreach($notificationWhenTypeValues as $value => $text){ ?>
                                    <option <?php if($value == $notification_preferences["notify_when_type"]){ echo "selected"; }?> value="<?php echo $value; ?>"><?php echo $text; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="notification_when">Notification arrives</label>
                            <div class="input-group">
                                <span class="input-group-addon"> <strong> Before </strong></span>
                                <input type="text" class="form-control" id="notification_when" name="notification_when" value="<?php echo $notification_preferences["notify_when"];?>" >
                                <span class="input-group-addon" id="notification_when_type_val"> <strong> <?php echo $notificationWhenTypeValues[$notification_preferences["notify_when_type"]];?> </strong></span>
                            </div>
                        </div>
                    </div>

                  <div class="form-group col-md-6">
                    <label class="control-label" for="event_info">Event Information</label>
                    <textarea class="form-control" name="event_info" rows="5"
                              id="event_info"><?php echo $event_info ?></textarea>
                  </div>
                    <?php
                    $assigned_staff = $DB_calendar->get_event_assgintos_info($event_id);
                    $assigned_arr = array();
                    $other_principal_id = 0;
                    $index_arr = 0;
                    foreach ($assigned_staff as $r) {
                        if ($r['assigned_user_id'] != null)
                            $assigned_arr[$index_arr] = $r['assigned_user_id'];
                        else
                            $other_principal_id = $r['assigned_other_principal_id'];
                        $index_arr++;
                    }
                    ?>
                  <div class="form-group col-md-6">
                    <label class="control-label" for="principals_assigned">Principal Assigned</label>
                    <select name="principals_assigned[]" multiple class="form-control" style="height: 110px;">
                        <?php
                        $staff = $DB_calendar->get_same_company_staff($employee_id);
                        foreach ($staff as $row) {
                            $id = $row['employee_id'];
                            $name = $row['full_name'] . '   ';
                            $email = $row['email'] . '   ';
                            $phone_number = $row['mobile'];
                            if (in_array($id, $assigned_arr))
                                echo "<option value=\"$id\" selected>$name &nbsp;&nbsp; $email &nbsp;&nbsp; $phone_number</option>";
                            else
                                echo "<option value=\"$id\">$name &nbsp;&nbsp; $email &nbsp;&nbsp; $phone_number</option>";
                        }
                        ?>
                      <option onclick="disselection()"><--- Disselect all selections ---></option>
                    </select>
                  </div>

                    <?php if ($other_principal_id == 0) { ?>
                      <div class="checkbox form-group col-md-12">
                        <label>
                          <input type="checkbox" id="is_other_principal" name="is_other_principal" data-toggle="collapse" data-target="#other_principal">Other Principal
                        </label>
                      </div>

                      <div class="collapse col-md-12" id="other_principal">
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_name">Principal Name</label>
                          <input type="text" class="form-control" name="other_principal_name" id="other_principal_name">
                        </div>
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_telephone">Principal Telephone</label>
                          <input type="text" class="form-control" name="other_principal_telephone"
                                 id="other_principal_telephone">
                        </div>
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_email">Principal Email</label>
                          <input type="text" class="form-control" name="other_principal_email"
                                 id="other_principal_email">
                        </div>
                      </div>

                      <div class="form-group create-event-form-col-2 col-md-12">
                        <button type="submit" class="btn btn-primary btn-long" name="update_event" style="width: 115px">
                          Update
                        </button>
                      </div>
                    <?php } else {
                        $result = $DB_calendar->get_principal_info($other_principal_id);
                        $other_principal_name = $result['principal_name'];
                        $other_principal_telephone = $result['principal_telephone'];
                        $other_principal_email = $result['principal_email'];
                        ?>
                      <div class="checkbox form-group col-md-12">
                        <label>
                          <input type="checkbox" id="is_other_principal" name="is_other_principal" data-toggle="collapse" data-target="#other_principal">Other Principal
                        </label>
                      </div>

                      <div class="collapse col-md-12" id="other_principal">
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_name">Principal Name</label>
                          <input type="text" class="form-control" name="other_principal_name" id="other_principal_name"
                                 value="<?php echo $other_principal_name; ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_telephone">Principal Telephone</label>
                          <input type="text" class="form-control" name="other_principal_telephone"
                                 id="other_principal_telephone" value="<?php echo $other_principal_telephone; ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label class="control-label" for="other_principal_email">Principal Email</label>
                          <input type="text" class="form-control" name="other_principal_email"
                                 id="other_principal_email" value="<?php echo $other_principal_email; ?>">
                        </div>
                      </div>

                      <div class="form-group create-event-form-col-2 col-md-12">
                        <button type="submit" class="btn btn-primary btn-long" name="update_event" style="width: 115px;">Update
                        </button>
                      </div>
                    <?php } ?>
                </div>
              </form>



              <div class="col-md-12">
                <legend>Uploads</legend>
              </div>
              <div class="form-group create-event-form-col-2 col-md-12">
                <form action="custom/calendar_visit/office-controller-event.php" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
                  <input type="hidden" name="building_id" value="<?php echo $building_id; ?>"/>
                  <div class="form-group col-md-6">
                    <label class="control-label" for="file_to_upload">Choose a File to Upload</label>
                    <input class="form-control" type="file" name="file_to_upload" id="file_to_upload" required>
                  </div>
                  <div class="form-group col-md-6 form-item-top-padding-1">
                    <button type="submit" class="btn btn-primary btn-long" name="upload_file" style="margin-top: 22px; width: 115px;">Upload</button>
                  </div>
                </form>
              </div>

              <table class="table">
                <thead>
                <tr>
                  <th class="col-xs-3 text-center">Upload ID</th>
                  <th class="col-xs-3 text-center">Upload Date</th>
                  <th class="col-xs-3 text-center">Upload Name</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $results = $DB_calendar->get_event_uploads($event_id);
                foreach ($results as $row) {
                    $upload_id = $row['id'];
                    $upload_date = $row['upload_date'];
                    $upload_name = $row['upload_name'];
                    $upload_url = "./custom/calendar_visit/uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name;
                    ?>
                  <tr>
                    <td class="text-center"><?php echo $upload_id; ?></td>
                    <td class="text-center"><?php echo $upload_date; ?></td>
                    <td class="text-center"><a href="<?php echo $upload_url; ?>" target="_blank"><?php echo $upload_name; ?></a></td>

                    <form action="custom/calendar_visit/office-controller-event.php" method="post">
                      <td class="text-center">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
                        <input type="hidden" name="building_id" value="<?php echo $building_id; ?>"/>
                        <input type="hidden" name="upload_id" value="<?php echo $upload_id; ?>"/>
                        <input type="hidden" name="upload_name" value="<?php echo $upload_name; ?>"/>
                        <button type="submit" class="btn btn-secondary event-table-button" name="delete_upload" onclick="return confirm('Are you sure to delete this upload?')">Delete</button>
                      </td>
                    </form>
                  </tr>
                    <?php
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--end section page body-->

<!--Start Scripts-->
<script type="text/javascript">

    function is_regular_event() {
        if ($('#is_regular').get(0).checked) {
            $('#event_date').attr('disabled', true);
            $('#regular_start_date').attr('require', true);
        }
        else {
            $('#event_date').attr('disabled', false);
            $('#regular_start_date').attr('require', false);
        }
    }

    $(document).ready(function () {

        // Change of the notification when type event
        $("#notification_when_type").change(function () {
            // If default - hide the display value of the notification when type
            if($(this).val() == "default"){
                $("#notification_when_type_val").addClass("hidden");
                return;
            }
            // Change the display value of the notification when type in the notification arrival time
            var text = $("#notification_when_type option:selected").text();
            $("#notification_when_type_val").removeClass("hidden").find("strong").html(text);
        });

    });


    <?php if($event_type == "regular"){?>$('#is_regular').click();<?php } ?>
    <?php if($other_principal_id != 0) {?>$('#is_other_principal').click();<?php }?>


    function disselection() {
        $("option").attr('selected', false);
    }

</script>