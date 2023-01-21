<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("pdo/dbconfig.php");
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once("pdo/Class.Calendar.php");
$DB_calendar = new Calendar($DB_con);
include "subdomain_check.php";

if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php

            $company_id = $_SESSION['company_id'];
            echo $DB_company->getWebTitle($company_id); ?></title>
    <!--    <base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once "links-for-html.php"; ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>

</head>

<body>
    <?php
    include_once("header.php");
    include_once("search-bar.php");
    $bid = $_GET['bid'];
    ?>
    <section id="section-body">
        <div class="container">
            <div class="page-title breadcrumb-top">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                            <!--            <li><a href="event-list.php">Event List</a></li>-->
                            <li><a href="map-listing.php">Property</a></li>
                        </ol>
                        <div class="page-title-left">
                            <h2>Message the Agent</h2>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $bookingId         = $_GET["bid"];
            $bookingInfo       = $DB_calendar->getBookingInfo($bookingId);
            $event_id          = $bookingInfo["event_id"];
            $row               = $DB_calendar->get_event($event_id);
            $event_name        = $row['event_name'];
            $event_location    = $row['event_location'];
            $event_description = $row['event_description'];
            if ($row['event_duration'] == 0) {
                $event_duration = $row['event_custom_duration'];
            } else {
                $event_duration = $row['event_duration'];
            }
            ?>

            <input type="hidden" name="booking_employee_id_value" id="booking_employee_id_value"
                value="<?php echo $bookingInfo["employee_id"]; ?>">
            <input type="hidden" name="booking_company_id_value" id="booking_company_id_value"
                value="<?php echo $bookingInfo["company_id"]; ?>">
            <input type="hidden" name="booking_id_value" id="booking_id_value" value="<?php echo $bookingId; ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading"> Message</div>
                        <div class="panel-body">
                            <div class="row form-group">
                                <div class="col-md-8">
                                    <textarea name="agent_message" class="form-control" id="agent_message" cols="12"
                                        rows="5" placeholder="Enter your message"></textarea>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-8">
                                    <button id="message_agent_send" class="btn btn-info btn-md">Send Message</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form class="panel panel-primary" id="create-event-oneonone-form" action="controller_event.php"
                        method="post">
                        <div class="panel-heading"> Appointment Information</div>
                        <div class="panel-body">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />

                            <div class="col-sm-12 col-xs-12 contact-block-inner">
                                <div class="form-group create-event-form-col-1 col-md-6">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Appointment Name</strong>
                                                <span><?php echo $event_name ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group create-event-form-col-1 col-md-6">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Duration</strong>
                                                <span><?php echo $event_duration ?> min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group create-event-form-col-1 col-md-12">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-12">
                                                <strong>Location</strong>
                                                <span><?php echo $event_location ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group create-event-form-col-1 col-md-12">
                                    <div class="row ">
                                        <div class="prop_details">
                                            <div class="col-lg-8">
                                                <strong>Booking Time</strong>
                                                <span
                                                    style="text-transform:capitalize"><?php echo date('h:i A', strtotime($bookingInfo["booking_start"])) . " - " . date('h:i A', strtotime($bookingInfo["booking_end"])); ?></span>
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>Customer Name</strong>
                                                <span
                                                    style="text-transform:capitalize"><?php echo ucwords($bookingInfo["customer_name"]); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </section>
    <!--end section page body-->

    <!--start footer section-->
    <?php include_once("footer.php"); ?>
    <!--end footer section-->

    <!--Start Scripts-->
    <script src="js/jquery.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyyF-y54uDrGJfNZKUmhnu1hGSbGKasqs&callback=initMap&libraries=&v=weekly">
    </script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
    <script type="text/javascript" src="js/infobox.js"></script>
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
    <script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery("#message_agent_send").on("click", function() {

            message = jQuery("#agent_message").val();
            employee_id = jQuery("#booking_employee_id_value").val();
            company_id = jQuery("#booking_company_id_value").val();
            booking_id = jQuery("#booking_id_value").val();

            data = {
                action: "sendMessageToAgent",
                message: message,
                employee_id: employee_id,
                company_id: company_id,
                booking_id: booking_id
            };

            jQuery.ajax({
                type: "POST",
                url: "admin/custom/potential_tenant/booking_controller.php",
                dataType: 'json',
                data: data,
                success: function(json) {
                    if (json) {
                        if (json.value) {
                            jQuery("#agent_message").val("");
                            alert("Message sent to the agent.");
                        }
                    }
                }
            });

        });
    });
    </script>
</body>

</html>