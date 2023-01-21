<?php
$building_id = $_GET['building_id'];
$event_id = $_GET['event_id'];
$employee_id = $_SESSION['employee_id'];
?>
<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/table_style.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/calendar-events.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script src="custom/calendar_visit/js/bootstrap-datepicker.js"></script>
<style>
label {
    font-weight: normal;
}

button {
    width: 115px;
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
                        <li class="active">Details Event</li>
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
              $row = $DB_calendar->get_visit_event($event_id);
              $event_name = $row['event_name'];
              $event_location = $row['event_location'];
              $event_description = $row['event_description'];
              $event_duration = $row['event_duration'];
              $event_custom_duration = $row['event_custom_duration'];
              $event_max_book_per_day = $row['event_max_book_per_day'];
              $event_increment = $row['event_increment'];
              $event_buffer_before = $row['event_buffer_before'];
              $event_buffer_after = $row['event_buffer_after'];
              $event_less_hour = $row['event_less_hour'];
              $person_in_charge = $row['resonsible_employee_id'];
              ?>
                            <form id="create-event-oneonone-form" action="custom/calendar_visit/controller_event.php"
                                method="post">
                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                <input type="hidden" name="building_id" value="<?php echo $building_id; ?>">

                                <div class="col-md-12">
                                    <legend>What event is this?</legend>
                                </div>
                                <div class="col-sm-12 col-xs-12 contact-block-inner">
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="event_name">Event Name</label>
                                        <input class="form-control" name="event_name" id="event_name"
                                            value="<?php echo $event_name ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="event_location">Location</label>
                                        <input class="form-control" name="event_location" id="event_location"
                                            value="<?php echo $event_location ?>">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="event_description">Description</label>
                                        <textarea class="form-control" name="event_description" rows="4"
                                            id="event_description"><?php echo $event_description ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <legend>When can people book this event?</legend>
                                </div>
                                <div class="col-sm-12 col-xs-12 contact-block-inner">
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_duration">Event Duration</label>
                                            <select name="event_duration" id="event_duration" class="form-control"
                                                autocomplete="off" onchange="if_custom_min()">
                                                <option value="15" <?php if ($event_duration == 15) {
                                              echo ("selected");
                                            } ?>>15 min
                                                </option>
                                                <option value="30" <?php if ($event_duration == 30) {
                                              echo ("selected");
                                            } ?>>30 min
                                                </option>
                                                <option value="45" <?php if ($event_duration == 45) {
                                              echo ("selected");
                                            } ?>>45 min
                                                </option>
                                                <option value="60" <?php if ($event_duration == 60) {
                                              echo ("selected");
                                            } ?>>60 min
                                                </option>
                                                <option value="0" <?php if ($event_duration == 0) {
                                            echo ("selected");
                                          } ?>>Custom min
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_custom_duration">Input Custom
                                                Min</label>
                                            <input class="form-control" name="event_custom_duration"
                                                id="event_custom_duration" value="<?php echo $event_custom_duration ?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_max_book_per_day">Max number of book
                                                per day</label>
                                            <input class="form-control" name="event_max_book_per_day"
                                                id="event_max_book_per_day"
                                                value="<?php if ($event_max_book_per_day != 0) {
                                                                                                                      echo $event_max_book_per_day;
                                                                                                                    } ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_increment">Show availability in
                                                increments of</label>
                                            <select name="event_increment" class="form-control" autocomplete="off">
                                                <option value="5" <?php if ($event_increment == 5) {
                                            echo ("selected");
                                          } ?>>5 min
                                                </option>
                                                <option value="10" <?php if ($event_increment == 10) {
                                              echo ("selected");
                                            } ?>>10 min
                                                </option>
                                                <option value="15" <?php if ($event_increment == 15) {
                                              echo ("selected");
                                            } ?>>15 min
                                                </option>
                                                <option value="30" <?php if ($event_increment == 30) {
                                              echo ("selected");
                                            } ?>>30 min
                                                </option>
                                                <option value="45" <?php if ($event_increment == 45) {
                                              echo ("selected");
                                            } ?>>45 min
                                                </option>
                                                <option value="60" <?php if ($event_increment == 60) {
                                              echo ("selected");
                                            } ?>>60 min
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_buffer_before">Buffer before
                                                event</label>
                                            <select name="event_buffer_before" class="form-control" autocomplete="off">
                                                <option value="0" <?php if ($event_buffer_before == 0) {
                                            echo ("selected");
                                          } ?>>0 min
                                                </option>
                                                <option value="15" <?php if ($event_buffer_before == 15) {
                                              echo ("selected");
                                            } ?>>15 min
                                                </option>
                                                <option value="30" <?php if ($event_buffer_before == 30) {
                                              echo ("selected");
                                            } ?>>30 min
                                                </option>
                                                <option value="45" <?php if ($event_buffer_before == 45) {
                                              echo ("selected");
                                            } ?>>45 min
                                                </option>
                                                <option value="60" <?php if ($event_buffer_before == 60) {
                                              echo ("selected");
                                            } ?>>60 min
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_buffer_after">Buffer after
                                                event</label>
                                            <select name="event_buffer_after" class="form-control" autocomplete="off">
                                                <option value="0" <?php if ($event_buffer_after == 0) {
                                            echo ("selected");
                                          } ?>>0 min
                                                </option>
                                                <option value="15" <?php if ($event_buffer_after == 15) {
                                              echo ("selected");
                                            } ?>>15 min
                                                </option>
                                                <option value="30" <?php if ($event_buffer_after == 30) {
                                              echo ("selected");
                                            } ?>>30 min
                                                </option>
                                                <option value="45" <?php if ($event_buffer_after == 45) {
                                              echo ("selected");
                                            } ?>>45 min
                                                </option>
                                                <option value="60" <?php if ($event_buffer_after == 60) {
                                              echo ("selected");
                                            } ?>>60 min
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 ">
                                        <div class="col-md-6">
                                            <label class="control-label" for="event_less_hour">Book event more than (
                                                Hours ) away</label>
                                            <input class="form-control" name="event_less_hour" id="event_less_hour"
                                                value="<?php echo $event_less_hour; ?>">
                                        </div>
                                        <div class=" col-md-6">
                                            <label class="control-label" for="person_in_charge">Person in charge</label>
                                            <select name="person_in_charge" id="person_in_charge" class="form-control"
                                                autocomplete="off">
                                                <?php
                        $staff = $DB_calendar->get_same_company_staff($employee_id);
                        foreach ($staff as $row) {
                          $id = $row['employee_id'];
                          $name = $row['full_name'];
                          $email = $row['email'];
                          if ($id == $person_in_charge)
                            echo "<option value=\"$id\" selected>$name - $email</option>";
                          else
                            echo "<option value=\"$id\">$name - $email</option>";
                        }
                        ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group create-event-form-col-2 col-md-12">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-long"
                                                name="details_update_event_oneonone">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <form id="add-event-oneonone-availability-form"
                                action="custom/calendar_visit/controller_event.php" method="post">
                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                <input type="hidden" name="building_id" value="<?php echo $building_id; ?>">

                                <div class="col-md-12">
                                    <legend>Availability</legend>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class=" col-md-6">
                                        <label class="control-label" for="availability_start">From</label>
                                        <input type="time" class="form-control" name="availability_start"
                                            id="availability_start" placeholder="00:00">
                                    </div>
                                    <div class=" col-md-6">
                                        <label class="control-label" for="availability_end">To</label>
                                        <input type="time" class="form-control" name="availability_end"
                                            id="availability_end" placeholder="00:00">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="col-md-5">
                                        <label class="control-label" for="availability_specific_date">Choose Specific
                                            Date</label>
                                        <input type="text" class="form-control date-input"
                                            name="availability_specific_date" id="availability_specific_date"
                                            placeholder="YYYY-MM-DD">
                                    </div>

                                    <div class="col-md-3 form-item-top-padding-1 col-md-offset-1">
                                        <button type="submit" class="btn btn-primary event-form-button"
                                            name="details_add_event_oneonone_availability"
                                            style="margin-top: 22px;">Add</button>
                                    </div>
                                </div>
                                <div class="checkbox form-group col-md-12">
                                    <label>
                                        <input type="checkbox" id="is_regular" name="is_regular" value="checked"
                                            data-toggle="collapse" data-target="#regular_days"
                                            onchange="is_regular_event()">Regular events
                                    </label>
                                </div>
                                <div class="collapse" id="regular_days">
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="start_day">From</label>
                                            <input type="text" class="form-control date-input" name="start_day"
                                                id="start_day" placeholder="YYYY-MM-DD" data-date-start-date="0d">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_end_day">To</label>
                                            <input type="text" class="form-control date-input" name="end_day"
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
                                <legend style="margin-top: 20px;">Edit Availability</legend>
                            </div>

                            <table class="table table-striped" id="availabilities_table"
                                style="width: 100%!important;background-color: white;">
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
                                            <input type="hidden" name="building_id" value="<?php echo $building_id; ?>">
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
                                                <button type="submit" class="btn table-button"
                                                    name="details_edit_event_oneonone_availability"
                                                    style=" width: 115px;">Edit</button>
                                                <button type="submit" class="btn table-button"
                                                    name="details_delete_event_oneonone_availability"
                                                    style=" width: 115px;"
                                                    onclick="return confirm('Are you sure to delete this slot?')">Delete</button>
                                            </td>
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
</section>
<!--end section page body-->

<script>
function if_custom_min() {
    var selected = $('#event_duration').val();
    if (selected == 0)
        $('#event_custom_duration').attr("disabled", false);
    else
        $('#event_custom_duration').attr("disabled", true);
}

function is_regular_event() {
    if ($('#is_regular').get(0).checked)
        $('#availability_specific_date').attr('disabled', true);
    else
        $('#availability_specific_date').attr('disabled', false);
}



$(document).ready(function() {
    var table = $('#availabilities_table').DataTable({
        "iDisplayLength": 25,
        "ordering": false,
        "info": false,
        "scrollX": true
    });

    //add custom filter
    $('#availabilities_table_length').append('<div class="hidden-xs col-md-5" style="float: right">\n' +
        '                  <div class="form-group">\n' +
        '                    <label for="filter_from" class="hidden-xs col-md-2 filter-text">From:</label>\n' +
        '                    <div class="col-sm-8 col-md-10">\n' +
        '                      <input type="text" class="form-control date-input" id="filter_from" style="min-width: 0">\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                </div>');

    $('#availabilities_table_filter').prepend('<div class="hidden-xs col-md-5">\n' +
        '                  <div class="form-group">\n' +
        '                    <label for="filter_to" class="hidden-xs col-md-2 filter-text">To:</label>\n' +
        '                    <div class="col-sm-8 col-md-10">\n' +
        '                      <input type="text" class="form-control date-input" id="filter_to" style="min-width: 0">\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                </div>');


    //bind with custom filter
    $('#filter_from, #filter_to').change(function() {
        table.draw();
    });

    //date_picker
    $('.date-input').datepicker({
        format: 'y-MM-dd'
    });

});


//filtering the date
function uk_date_parse(uk_date) {
    if (uk_date == null || uk_date === '') {
        return 0;
    }
    var date_arr = uk_date.split('/');
    return (date_arr[2] + date_arr[1] + date_arr[0]) * 1;
}

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    var min_date = uk_date_parse($('#filter_from').val());
    var max_date = uk_date_parse($('#filter_to').val());
    var date = uk_date_parse(data[0]) || 0;

    if ((min_date == 0 && max_date == 0) ||
        (min_date == 0 && date <= max_date) ||
        (min_date <= date && max_date == 0) ||
        (min_date <= date && date <= max_date)) {
        return true;
    } else {
        return false;
    }
});
</script>