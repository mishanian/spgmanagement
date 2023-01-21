<?php
$building_id = $_GET['building_id'];
$calendar_url = "calendar.php?building_id=" . $building_id;
$visit_events_url = "event-list.php?building_id=" . $building_id;
$employee_id = $_SESSION['employee_id'];
?>
<link href="custom/calendar_visit/form-control.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/table_style.css" rel="stylesheet" type="text/css" />

<section id="section-body">
  <div class="container">
    <div class="page-title breadcrumb-top">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href="<?php echo $visit_events_url; ?>">Visit Events</a></li>
            <li class="active">Office & Maintenance Events</li>
            <li><a href="<?php echo $calendar_url; ?>">Calendar</a></li>
          </ol>
        </div>
      </div>
    </div>

    <div class="row">
      <a href="office-create-event.php?event_category=office&building_id=<?php echo $building_id; ?>" class="btn btn-primary btn-md" role="button" style="margin: 0px 0px 25px 15px;">Create Events</a>
    </div>

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div id="content-area" class="contact-area">
          <div class="white-block">

            <div class="row">
              <legend>My Events</legend>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="col-xs-2 text-center">Event Name</th>
                    <th class="col-xs-2 text-center">Event Category</th>
                    <th class="col-xs-2 text-center">Event Type</th>
                    <th class="col-xs-2 text-center">Event Date</th>
                    <th class="col-xs-2 text-center">Created By</th>
                    <th class="text-center" style="width: 12px;"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include_once("../pdo/dbconfig.php");
                  $results = $DB_calendar->get_office_maintenance_events_list($building_id, $employee_id);
                  foreach ($results as $row) {
                    $event_id = $row['id'];
                    $event_name = $row['event_name'];
                    $event_date = $row['event_date'];
                    $event_type = $row['event_type'];
                    $event_category = $row['event_category'];
                    $event_created_by = $row['event_created_by_user_id'];
                    //get created by persons' name
                    $results_for_name = $DB_calendar->get_employee_info($event_created_by);
                    $event_created_by_name = $results_for_name['full_name'];

                    $event_frequency_type = $row['event_frequency_type'];
                    if ($event_type == "regular")
                      $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
                    else
                      $event_date_value = date('Y-m-d', strtotime($event_date));
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $event_name; ?></td>
                      <td class="text-center"><?php echo $event_category; ?></td>
                      <td class="text-center"><?php echo $event_type; ?></td>
                      <td class="text-center"><?php echo $event_date_value; ?></td>
                      <td class="text-center"><?php echo $event_created_by_name; ?></td>
                      <form action="custom/calendar_visit/office-controller-event.php" method="post">
                        <td class="text-center" style="border-right: 0px; width: 13%;">
                          <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                          <input type="hidden" name="building_id" value="<?php echo $building_id; ?>" />
                          <button type="submit" class="btn table-button" name="details_event">Details</button>
                          <button type="submit" class="btn table-button" name="delete_event" onclick="return confirm('Are you sure to delete this event?')">Delete</button>
                        </td>
                      </form>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <div class="row">
              <legend>Created Office & Maintenance Events</legend>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="col-xs-2 text-center">Event Name</th>
                    <th class="col-xs-2 text-center">Event Category</th>
                    <th class="col-xs-2 text-center">Event Date</th>
                    <th class="col-xs-2 text-center">Assign To</th>
                    <th class="col-xs-2 text-center">Created Date</th>
                    <th class="text-center" style="width: 12%"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $results = $DB_calendar->get_created_office_maintenance_events($building_id, $employee_id);
                  foreach ($results as $row) {
                    $event_id = $row['id'];
                    $event_name = $row['event_name'];
                    $event_category = $row['event_category'];
                    $event_date = $row['event_date'];
                    $created_date = $row['created_date'];
                    $event_type = $row['event_type'];
                    $event_frequency_type = $row['event_frequency_type'];

                    if ($event_type == "regular")
                      $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
                    else
                      $event_date_value = $event_date;

                    $assigntos_set = $DB_calendar->get_event_assigntos_details($event_id);
                    $assigntos_value = "";
                    foreach ($assigntos_set as $r) {
                      $assigned_name = $r['full_name'];
                      $assigntos_value .= $assigned_name . ' ';
                    }
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $event_name; ?></td>
                      <td class="text-center"><?php echo $event_category; ?></td>
                      <td class="text-center"><?php echo $event_date_value; ?></td>
                      <td class="text-center"><?php echo $assigntos_value; ?></td>
                      <td class="text-center"><?php echo date('Y-m-d', strtotime($created_date)); ?>
                      </td>
                      <form action="custom/calendar_visit/office-controller-event.php" method="post">
                        <td class="text-center" style="border-right: 0px;width: 13%">
                          <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                          <input type="hidden" name="building_id" value="<?php echo $building_id; ?>" />
                          <button type="submit" class="btn table-button" name="details_event" style="">Details</button>
                          <button type="submit" class="btn table-button" name="delete_event" onclick="return confirm('Are you sure to delete this event?')">Delete</button>
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
</section>
<!--end section page body-->


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