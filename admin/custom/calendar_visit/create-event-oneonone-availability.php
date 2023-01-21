<?php
$building_id = $_GET['building_id'];
$event_id = $_GET['event_id'];
?>

<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/table_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
<link href="custom/calendar_visit/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script src="custom/calendar_visit/js/bootstrap-datepicker.js"></script>

<style>
label {
    font-weight: normal;
}

.width-full {
    width: 100% !important;
}
</style>

<section id="section-body">
    <div class="container">

        <div class="page-title breadcrumb-top">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="event-list.php?building_id=<?php echo $building_id; ?>&direct=visitor_events">Visitor
                                Events</a></li>
                        <li><a href="create-event-oneonone.php?building_id=<?php echo $building_id; ?>">Create Event</a>
                        </li>
                        <li class="active">Create Event Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div id="content-area" class="contact-area">
                    <div class="white-block">
                        <div class="row">
                            <form id="add-event-oneonone-availability-form"
                                action="custom/calendar_visit/controller_event.php" method="post">
                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                <input type="hidden" name="building_id" value="<?php echo $building_id ?>" />

                                <div class="col-md-12">
                                    <legend>Add Availability</legend>
                                </div>

                                <div class="form-group col-md-6">
                                    <div class="col-md-6">
                                        <label class="control-label" for="availability_start">From</label>
                                        <input type="time" class="form-control" name="availability_start"
                                            id="availability_start" placeholder="00:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label" for="availability_end">To</label>
                                        <input type="time" class="form-control" name="availability_end"
                                            id="availability_end" placeholder="00:00">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <div class="col-md-6">
                                        <label class="control-label" for="availability_specific_date">Choose Specific
                                            Date</label>
                                        <input type="text" class="form-control date_input"
                                            name="availability_specific_date" id="availability_specific_date"
                                            placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="col-md-3 col-md-offset-1" style="margin-top: 20px;">
                                        <button type="submit" class="btn btn-primary event-form-button"
                                            name="add_event_oneonone_availability" style=" width: 115px;">Add</button>
                                    </div>
                                </div>

                                <div class="checkbox form-group col-md-12">
                                    <input type="checkbox" id="is_regular" name="is_regular" value="checked"
                                        data-toggle="collapse" data-target="#regular_days"
                                        onchange="is_regular_event()">Regular events</label>
                                </div>

                                <div class="collapse" id="regular_days">
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="start_day">From</label>
                                            <input type="text" class="form-control date_input" name="start_day"
                                                id="start_day" placeholder="YYYY-MM-DD" data-date-start-date="0d">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_end_day">To</label>
                                            <input type="text" class="form-control date_input" name="end_day"
                                                id="end_day" placeholder="YYYY-MM-DD" data-date-end-date="+120d">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-6" style="margin-top: 22px;">
                                            <select name="availability_type" class="form-control" autocomplete="off"
                                                required>
                                                <option value="1" selected>Every Monday</option>
                                                <option value="2">Every Tuesday</option>
                                                <option value="3">Every Wednesday</option>
                                                <option value="4">Every Thursday</option>
                                                <option value="5">Every Friday</option>
                                                <option value="6">Every Saturday</option>
                                                <option value="7">Every Sunday</option>
                                                <option value="0">Every Workday</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </form>


                            <div class="col-md-12">
                                <legend style="padding-top: 20px;">Edit Availability</legend>
                            </div>
                            <table class="table table-striped table-condensed" id="availabilities_table"
                                style="background: white">
                                <thead>
                                    <tr>
                                        <th class="col-md-3 text-center">Slots Date</th>
                                        <th class="col-md-3 text-center">From</th>
                                        <th class="col-md-3 text-center">To</th>
                                        <th class="col-md-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                  include_once("../pdo/dbconfig.php");

                  if ($event_id != null) {
                    $result = $DB_calendar->get_event_availability($event_id);
                    foreach ($result as $row) {
                      $availability_id = $row['id'];
                      $availability_start = $row['availability_start'];
                      $availability_end = $row['availability_end'];
                      $availability_type = $row['availability_type'];
                      $availability_specific_date = $row['availability_specific_date'];

                      if ($availability_type == "specific") {
                        $availability_slot = date('Y-m-d', strtotime($availability_specific_date));
                      } else if ($availability_type == "monday") {
                        $availability_slot = "Every Monday";
                      } else if ($availability_type == "tuesday") {
                        $availability_slot = "Every Tuesday";
                      } else if ($availability_type == "wednesday") {
                        $availability_slot = "Every Wednesday";
                      } else if ($availability_type == "thursday") {
                        $availability_slot = "Every Thursday";
                      } else if ($availability_type == "friday") {
                        $availability_slot = "Every Friday";
                      }
                  ?>

                                    <tr>
                                        <form action="custom/calendar_visit/controller_event.php" method="post">
                                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                            <input type="hidden" name="availability_id"
                                                value="<?php echo $availability_id; ?>" />
                                            <input type="hidden" name="building_id"
                                                value="<?php echo $building_id ?>" />
                                            <td class="col-md-3 text-center" style="padding-top: 12px;">
                                                <?php echo $availability_slot ?></td>
                                            <td class="col-md-3 text-center"><input type="time"
                                                    class="form-control width-full" name="availability_start"
                                                    id="availability_start" value="<?php echo $availability_start ?>"
                                                    placeholder="00:00"></td>
                                            <td class="col-md-3 text-center"><input type="time"
                                                    class="form-control width-full" name="availability_end"
                                                    id="availability_end" value="<?php echo $availability_end ?>"
                                                    placeholder="00:00"></td>
                                            <td class="col-md-3 text-center" style="padding-top: 12px;">
                                                <button type="submit" class="btn table-button event-form-button"
                                                    name="edit_event_oneonone_availability"
                                                    style=" width: 115px;">Edit</button>
                                                <button type="submit" class="btn table-button"
                                                    name="delete_event_oneonone_availability" style=" width: 115px;"
                                                    onclick="return confirm('Are you sure to delete this slot?')">Delete</button>
                                            </td>
                        </div>
                        </form>
                        </tr>
                        <?php
                    }
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
<script type="text/javascript">
function is_regular_event() {
    if ($('#is_regular').get(0).checked)
        $('#availability_specific_date').attr('disabled', true);
    else
        $('#availability_specific_date').attr('disabled', false);
}
</script>

<script>
$('.date_input').datepicker({
    format: 'y-MM-dd'
});
</script>
<script>
$(document).ready(function() {
    $('#availabilities_table').DataTable({
        "iDisplayLength": 25,
        "ordering": false,
        "info": false
    });
});
</script>