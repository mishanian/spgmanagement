<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/styles.css" rel="stylesheet" type="text/css" />
  <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color: white;">
  <?php
  include_once("../../../pdo/dbconfig.php");
  $event_id = $_GET['event_id'];
  $row = $DB_calendar->get_office_maintenance_event_detail($event_id);
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
      <label class="control-label"><b>Event Name:</b> <?php echo $event_name ?></label>
    </div>
    <div class="form-group col-md-12">
      <label class="control-label"><b>Event Category:</b> <?php echo $event_category ?></label>
    </div>
    <div class="form-group col-md-12">
      <label class="control-label"><b>Person in Contact:</b> <?php echo $person_in_contact ?></label>
    </div>
    <div class="form-group col-md-12">
      <label class="control-label"><b>Contact Number:</b> <?php echo $contact_number ?></label>
    </div>
    <div class="form-group col-md-12">
      <label class="control-label"><b>Event Date:</b> <?php echo $event_date_value ?></label>
    </div>
    <div class="form-group col-md-12" style="word-wrap: break-word;">
      <label class="control-label"><b>Event Location:</b><?php echo $event_building_address ?></label>
    </div>
    <div class="form-group col-md-12" style="word-wrap: break-word;">
      <label class="control-label"><b>Event Description:</b> <?php echo $event_info ?></label>
    </div>
    <div class="form-group col-md-12">
      <label class="control-label"><b>Created By:</b> <?php echo $event_created_by_employee; ?></label>
    </div>

    <legend>Attachements</legend>


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
    <?php } ?>
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