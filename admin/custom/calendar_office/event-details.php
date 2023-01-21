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
  $event_id = $_GET['event_id']; 

  $row = $DB_event->get_event($event_id);
  $event_name = $row['event_name']; 
  $event_contact = $row['event_contact'];
  $event_date = $row['event_date'];
  $event_frequency = $row['event_frequency'];
  $event_frequency_type = $row['event_frequency_type'];
  $event_info = $row['event_info'];
  $event_preparation = $row['event_preparation'];
  $event_type = $row['event_type'];
  $event_category = $row['event_category'];

?>
<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
<legend>Event Information</legend>
<div class="col-sm-12 col-xs-12 contact-block-inner">
  <div class="form-group col-md-12">
    <label class="control-label" for="event_name">Event Name: <?php echo $event_name?></label>
  </div>
  <div class="form-group col-md-12">
    <label class="control-label" for="event_contact">Person in Contact: <?php echo $event_contact?></label>
  </div>
  <div class="form-group col-md-12">
    <label class="control-label" for="event_date">Start Date: <?php echo $event_date?></label>
  </div>
  <?php
  if($event_type == "regular") {
  ?>
  <div class="form-group col-md-12">
    <label class="control-label" for="event_frequency">Frequency: <?php echo $event_frequency . " " . $event_frequency_type ?></label>
  </div>
  <?php
  }
  ?>
  <div class="form-group col-md-12">
    <label class="control-label" for="event_info">  <?php if($event_type == "regular") { echo "Company Information"; } else { echo "Contractor Information"; } ?>: <?php echo $event_info?></label>
  </div> 
  <div class="form-group col-md-12">
    <label class="control-label" for="event_preparation">Preparation Instruction: <?php echo $event_preparation?></label>
  </div>  
  <div class="form-group col-md-12">
    <label class="control-label">Attachements: </label>
  </div>  
  <?php
  $results = $DB_event->get_event_uploads($event_id);
  foreach ( $results as $row ) {
    $upload_id = $row['id'];                
    $upload_date = $row['upload_date'];
    $upload_name = $row['upload_name'];
    $upload_url = "./uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name; 
  ?>
    <div class="form-group col-md-12">
      <label class="control-label"><a href="<?php echo $upload_url;?>" target="_blank"><?php echo $upload_name;?></a></label>
    </div> 
  <?php
  }
  ?>                  
</div>               

</body>
</html>