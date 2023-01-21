<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=300,height=200,initial-scale=1">
  <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/styles.css" rel="stylesheet" type="text/css" />
  <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color: white;">

  <?php
  include_once("../../../pdo/dbconfig.php");
  $booking_start = $_GET['booking_start'];
  $booking_date = $_GET['booking_date'];
  $booking_end = $_GET['booking_end'];
  $event_id = $_GET['event_id'];

  $row = $DB_calendar->get_visit_event($event_id);
  $event_building = $row['building_name'];
  $event_building_id = $row['building_id'];
  $event_name = $row['event_name'];
  $event_location = $row['event_location'];
  $event_description = $row['event_description'];
  $event_person_in_charge = $row['resonsible_employee_id'];
  $person_in_charge_name = $DB_calendar->get_employee_info($event_person_in_charge)['full_name'];

  if ($row['event_duration'] == 0) {
    $event_duration = $row['event_custom_duration'];
  } else {
    $event_duration = $row['event_duration'];
  }

  $result = $DB_calendar->get_bookings_by_time($event_id, $booking_date, $booking_start);
  $bookings = array();
  foreach ($result as $row) {
    $bookings[] = array(
      'id' => $row['id'],
      'customer_name' => $row['customer_name'],
      'telephone' => $row['telephone'],
      'customer_email' => $row['customer_email'],
      'desired_apartment_id' => $row['visitor_desired_unit']
    );
  }
  ?>

  <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
  <legend style="padding: 5px;" class="bg-primary">Event Information
    <span style="font-size: 15px;float: right; padding-top: 3px;">
      <i class="fa fa-building-o" style="font-size:20px;color:white;margin-right: 3px;"></i><a style="color: white;" target="_blank" href="../../event-list.php?building_id=<?php echo $event_building_id ?>"><?php echo $event_building; ?></a>
    </span>
  </legend>

  <div class="col-sm-12 col-xs-12 contact-block-inner">
    <div class="form-group create-event-form-col-1">
      <label class="control-label"><b>Event Name:</b> <?php echo $event_name ?> </label>
    </div>
    <div class="form-group create-event-form-col-1">
      <label class="control-label"><b>Person in charge:</b> <?php echo $person_in_charge_name; ?> </label>
    </div>
    <div class="form-group create-event-form-col-1">
      <label class="control-label"><b>Event Date:</b> <?php echo date('Y-m-d', strtotime($booking_date)); ?>
      </label>
    </div>
    <div class="form-group create-event-form-col-1">
      <label class="control-label"><b>Event Time:</b> <?php echo $booking_start . '-' . $booking_end; ?> </label>
    </div>
    <div class="form-group create-event-form-col-2">
      <label class="control-label"><b>Location:</b> <?php echo $event_location ?> </label>
    </div>
  </div>
  <legend>Reservation Information</legend>

  <?php
  for ($i = 0; $i < count($bookings); $i++) {
  ?>
    <form action="controller_event.php" method="post">
      <div class="col-sm-12 col-xs-12 contact-block-inner">
        <div class="form-group create-event-form-col-1">
          <label class="control-label" for="customer_name"><b>Visitor Name:</b>
            <?php echo $bookings[$i]['customer_name']; ?></label>
        </div>
        <div class="form-group create-event-form-col-1">
          <label class="control-label" for="customer_email"><b>Visitor Email:</b>
            <?php echo $bookings[$i]['customer_email']; ?></label>
        </div>
        <div class="form-group create-event-form-col-2">
          <label class="control-label" for="comment"><b>Visitor Telephone:</b>
            <?php echo $bookings[$i]['telephone']; ?></label>
        </div>
        <div class="form-group create-event-form-col-2">
          <label class="control-label" for="comment"><b>Visitor Desired Apartment:</b>
            <?php echo $DB_apt->getAptInfo($bookings[$i]['desired_apartment_id'])['unit_number']; ?></label>
        </div>
      </div>
      <input type="hidden" name="booking_id" value="<?php echo $bookings[$i]['id']; ?>" />
    </form>
  <?php
  }
  ?>
</body>

</html>