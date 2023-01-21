<!DOCTYPE html>
<html lang="en">
<head>
<title>Commercial Property Management</title>
<!--Meta tags-->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="css/owl.carousel.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/calendar.css" rel="stylesheet">
</head>
<body>

<?php

  include_once ("pdo/dbconfig.php");
  $booking_start = $_GET['booking_start'];
  $booking_date = $_GET['booking_date'];
  $event_id = $_GET['event_id']; 

  $row = $DB_calendar->get_event($event_id);

  $event_name = $row['event_name']; 
  $event_location = $row['event_location'];
  $event_description = $row['event_description'];
  if($row['event_duration'] == 0) {
    $event_duration = $row['event_custom_duration'];
  }
  else {
    $event_duration = $row['event_duration'];      
  }

  $result = $DB_calendar->get_bookings_by_time($event_id, $booking_date, $booking_start);
  $bookings = array();
  foreach ( $result as $row ) {
    $bookings[] = array(
      'id' => $row['id'],
      'customer_name' => $row['customer_name'],
      'comment' => $row['comment'],
      'customer_email' => $row['customer_email']
      );
  }

?>
<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
<legend>Event Information</legend>
<div class="col-sm-12 col-xs-12 contact-block-inner">
  <div class="form-group create-event-form-col-1">
    <label class="control-label" for="event_name">Event Name: <?php echo $event_name?></label>
  </div>
  <div class="form-group create-event-form-col-1">
    <label class="control-label" for="event_location">Duration: <?php echo $event_duration?> min</label>
  </div>
  <div class="form-group create-event-form-col-1">
    <label class="control-label" for="booking_date">Date: <?php echo $booking_date?></label>
  </div>
  <div class="form-group create-event-form-col-1">
    <label class="control-label" for="booking_start">Start Time: <?php echo $booking_start?></label>
  </div>
  <div class="form-group create-event-form-col-2">
    <label class="control-label" for="event_description">Location: <?php echo $event_location?></label>
  </div> 
  <div class="form-group create-event-form-col-2">
    <label class="control-label" for="event_description">Description: <?php echo $event_description?></label>
  </div>                      
</div>               
<legend>Bookings</legend>

<?php

  for($i = 0; $i < count($bookings); $i++) { 
?>
  <form action="controller_event.php" method="post">
    <div class="col-sm-12 col-xs-12 contact-block-inner">
      <div class="form-group create-event-form-col-1">
        <label class="control-label" for="customer_name">Customer Name: <?php echo $bookings[$i]['customer_name'];?></label>
      </div>
      <div class="form-group create-event-form-col-1">
        <label class="control-label" for="customer_email">Email: <?php echo $bookings[$i]['customer_email'];?></label>
        <button type="submit" class="btn btn-secondary booking-details-button" name="cancel_booking" onclick="return confirm('Are you sure to cancel this booking?')">Cancel</button>  
      </div>
      <div class="form-group create-event-form-col-2">
        <label class="control-label" for="comment">Comment: <?php echo $bookings[$i]['comment'];?></label>
      </div>                      
    </div>
    <input type="hidden" name="booking_id" value="<?php echo $bookings[$i]['id'];?>" /> 
  </form>
  <legend></legend>
<?php
  }
?>

</body>
</html>