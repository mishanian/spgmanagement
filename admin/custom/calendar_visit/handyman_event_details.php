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
    $handyman_booking_id = $_GET['handyman_booking_id'];
    $handyman_slot_id = $_GET['handyman_slot_id'];
    $slot_info = $DB_calendar->get_handyman_avail_info($handyman_slot_id);
    $handyman_booking_info = $DB_calendar->get_one_handyman_booking($handyman_booking_id);
    $tenant_id = $handyman_booking_info['tenant_id'];
    $apartment_id = $handyman_booking_info['apartment_id'];
    $book_from = $handyman_booking_info['book_from'];
    $book_to = $handyman_booking_info['book_to'];

    //get tenant_info
    $requester_info = $DB_calendar->get_user_info($tenant_id);
    $requester_name = $requester_info['full_name'];
    $requester_email = $requester_info['email'];
    $requester_mobile = $requester_info['mobile'];

    // get building info
    $building_info = $DB_calendar->get_building_apartment_info_from_apartment_id($apartment_id);
    $building_name = $building_info['building_name'];
    $building_id = $building_info['building_id'];
    $unit = $building_info['apartment'];
    $handyman_slot_date = date('Y-m-d', strtotime($slot_info['slot_date']));
    ?>
    <legend style="padding: 5px;" class="bg-primary">Event Information
        <span style="font-size: 15px;float: right; padding-top: 3px;">
            <i class="fa fa-building-o" style="font-size:20px;color:white;margin-right: 3px;"></i><a style="color: white;" target="_blank" href="../../event-list.php?building_id=<?php echo $building_id; ?>"><?php echo $building_name; ?></a>
        </span>
    </legend>

    <div class="col-sm-12 col-xs-12 contact-block-inner">
        <div class="form-group create-event-form-col-1">
            <label class="control-label"><b>Event Name:</b> <?php echo "Repair in" . $building_name . " " . $unit ?> </label>
        </div>
        <div class="form-group create-event-form-col-1">
            <label class="control-label"><b>Event Date:</b> <?php echo $handyman_slot_date; ?> </label>
        </div>
        <div class="form-group create-event-form-col-1">
            <label class="control-label"><b>Event Time:</b> <?php echo $book_from . ' - ' . $book_to; ?> </label>
        </div>
        <div class="form-group create-event-form-col-2">
            <label class="control-label"><b>Location:</b> <?php echo $building_name ?> </label>
        </div>
    </div>

    <legend>Requester Information</legend>
    <div class="col-sm-12 col-xs-12 contact-block-inner">
        <div class="form-group create-event-form-col-1">
            <label class="control-label" for="customer_name"><b>Requester Name:</b>
                <?php echo $requester_name; ?></label>
        </div>
        <div class="form-group create-event-form-col-1">
            <label class="control-label" for="customer_email"><b>Requester Email:</b>
                <?php echo $requester_mobile ?></label>
        </div>
        <div class="form-group create-event-form-col-2">
            <label class="control-label" for="comment"><b>Requester Telephone:</b>
                <?php echo $requester_mobile; ?></label>
        </div>
    </div>
</body>

</html>