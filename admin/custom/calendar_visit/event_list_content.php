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
</head>

<body>

    <section id="section-body">
        <?php $building_id = $_GET['building_id']; ?>
        <div class="container">
            <div class="page-title breadcrumb-top">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index-2.html"><i class="fa fa-home"></i></a></li>
                            <li class="active">Event List</li>
                        </ol>
                        <div class="page-title-left">
                            <h2>Events | <a href="calendar.php?building_id=<?php echo $building_id; ?>">Calendar</a>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id="content-area" class="contact-area">
                        <div class="white-block">
                            <div class="row">

                                <legend>One-on-One Events | <a
                                        href="create-event-oneonone.php?building_id=<?php echo $building_id; ?>">Create</a>
                                </legend>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-xs-2 text-center">Event ID</th>
                                            <th class="col-xs-4 text-center">Event Name</th>
                                            <th class="col-xs-4 text-center">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                    include_once("pdo/dbconfig.php");

                    $calendar_type = "visit";
                    $results = $DB_calendar->get_events_oneonone($building_id, $calendar_type);
                    foreach ($results as $row) {
                      $event_id = $row['id'];
                      $event_name = $row['event_name'];
                      $event_location = $row['event_location'];
                      $event_max_book_per_slot = $row['event_max_book_per_slot'];
                      if ($event_max_book_per_slot == 1) {
                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $event_id; ?></td>
                                            <td class="text-center"><?php echo $event_name; ?></td>
                                            <td class="text-center"><?php echo $event_location; ?></td>
                                            <form action="controller_event.php" method="post">
                                                <td class="text-center">
                                                    <input type="hidden" name="event_id"
                                                        value="<?php echo $event_id; ?>" />
                                                    <input type="hidden" name="building_id"
                                                        value="<?php echo $building_id ?>">
                                                    <button type="submit" class="btn btn-secondary event-table-button "
                                                        name="book_event" style="width: 85px;">Book</button>
                                                    <button type="submit" class="btn btn-secondary event-table-button "
                                                        name="event_booking_list" style="width: 85px;">Booked</button>
                                                    <button type="submit" class="btn btn-secondary event-table-button"
                                                        name="details_event_oneonone">Details</button>
                                                    <button type="submit" class="btn btn-secondary event-table-button"
                                                        name="delete_event_oneonone"
                                                        onclick="return confirm('Are you sure to delete this event?')">Delete</button>
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


    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.html"></script>
    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
    <script type="text/javascript" src="js/infobox.js"></script>
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>

</body>

</html>