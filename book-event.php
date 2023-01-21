<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('pdo/dbconfig.php');
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('pdo/Class.Calendar.php');
$DB_calendar = new Calendar($DB_con);
include_once('pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con);


include "subdomain_check.php";

if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php
            include_once("pdo/dbconfig.php");
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
    $building_id = $_GET['building_id'];
    $apt_id      = $_GET['apt_id'];
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
                            <li>
                                <a
                                    href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"><?php echo $DB_building->getBdName($building_id); ?></a>
                            </li>
                            <li class="active">Book an Appointment</li>
                        </ol>
                        <div class="page-title-left">
                            <h2>Book an Appointment</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id="content-area" class="contact-area">
                        <div class="white-block">
                            <div class="row">
                                <?php
                                include_once("pdo/dbconfig.php");
                                $event_ids = $DB_calendar->get_event_ids($building_id);

                                $event_id          = $event_ids[0]['id'];
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
                                <form id="create-event-oneonone-form" action="controller_event.php" method="post">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                    <legend>Appointment Information</legend>

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
                                                    <div class="col-lg-12">
                                                        <strong>Description</strong>
                                                        <span
                                                            style="text-transform:capitalize"><?php echo $event_description ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!--                <div class="col-sm-12 col-xs-12 contact-block-inner">-->
                                    <!--                  <div class="form-group create-event-form-col-1 col-md-6">-->
                                    <!--                    <label class="control-label" for="event_name">Appointment Name: -->
                                    <?php //echo $event_name
                                    ?>
                                    <!--</label>-->
                                    <!--                  </div>-->
                                    <!--                  <div class="form-group create-event-form-col-1 col-md-6">-->
                                    <!--                    <label class="control-label" for="event_location">Duration: -->
                                    <?php //echo $event_duration
                                    ?>
                                    <!-- min</label>-->
                                    <!--                  </div>-->
                                    <!--                  <div class="form-group create-event-form-col-2 col-md-12">-->
                                    <!--                    <label class="control-label" for="event_description">Location: -->
                                    <?php //echo $event_location
                                    ?>
                                    <!--</label>-->
                                    <!--                  </div>-->
                                    <!--                  <div class="form-group create-event-form-col-2 col-md-12">-->
                                    <!--                    <label class="control-label" for="event_description">Description: -->
                                    <?php //echo $event_description
                                    ?>
                                    <!--</label>-->
                                    <!--                  </div>-->
                                    <!--                </div>-->

                                </form>
                                <form id="add-event-oneonone-availability-form" action="controller_event.php"
                                    method="post">
                                    <!--                <input type="hidden" id="event_id" name="event_id" value="-->
                                    <?php //echo $event_id;
                                    ?>
                                    <!--" />-->
                                    <input type="hidden" id="building_id" name="building_id"
                                        value="<?php echo $building_id; ?>">
                                    <input type="hidden" id="apt_id" name="apt_id" value="<?php echo $apt_id; ?>">
                                    <?php if (isset($_GET['portal'])) { ?>
                                    <input type="hidden" id="from_portal" name="from_portal" value="true">
                                    <?php
                                    } ?>
                                    <!--                <input type="hidden" id="event_description" name="event_description" value="-->
                                    <?php //echo $event_description;
                                    ?>
                                    <!--">-->
                                    <!--                <input type="hidden" id="event_location" name="event_location" value="-->
                                    <?php //echo $event_location
                                    ?>
                                    <!--;">-->
                                    <legend>Please Fill in Your Information</legend>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6">
                                            <label>Appointment Unit </label>
                                            <div style="clear:both;">&nbsp;</div>
                                            <div class="prop_details">
                                                <span><?php echo $DB_apt->getAptInfo($apt_id)['unit_number'] ?>
                                                    in
                                                    <?php echo $DB_building->getBdInfo($building_id)['building_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6">
                                            <label>Appointment Date</label>
                                            <select id="book_event_date" name="book_event_date"
                                                class="form-control book_event_field" type="text" autocomplete="off">
                                                <?php
                                                include_once('action_booking.php');
                                                $action_booking = new action_booking();
                                                $dates          = $action_booking->get_availablility_dates($building_id);

                                                for ($i = 0; $i < count($dates); $i++) {
                                                    $year      = date("Y", strtotime($dates[$i]));
                                                    $month     = date("m", strtotime($dates[$i]));
                                                    $day       = date("d", strtotime($dates[$i]));
                                                    $jd        = gregoriantojd($month, $day, $year);
                                                    $dayofweek = jddayofweek($jd, 1);
                                                    $text      = $dates[$i] . ' ' . $dayofweek;
                                                ?>
                                                <option value="<?php echo $dates[$i]; ?>"><?php echo $text; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6">
                                            <label>Appointment Time</label>
                                            <select id="book_event_slots" name="book_event_slots"
                                                class="form-control book_event_field" type="text" autocomplete="off"
                                                required></select>
                                        </div>
                                        <div class="book-event-col-2 col-md-4 form-item-top-padding-1">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Expected Move-In Month</label>
                                            <select class="form-control" name="movein_month" id="movein_month">
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Expected Move-In Year</label>
                                            <select class="form-control" name="movein_year" id="movein_year">
                                                <option value="default">Select year</option>
                                                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?>
                                                </option>
                                                <?php
                                                $number_years = 1;
                                                while ($number_years <= 10) {
                                                    $date = date('Y', strtotime("+$number_years year"));
                                                ?>
                                                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
                                                <?php
                                                    $number_years++;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Name</label><input
                                                type="text" class="form-control" name="visitor_name" id="visitor_name"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Email</label><input
                                                type="email" class="form-control" name="visitor_email"
                                                id="visitor_email" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Telephone</label><input
                                                type="tel" class="form-control" name="visitor_phone" id="visitor_phone"
                                                required></div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Please Check the Box</label>
                                            <div class="g-recaptcha"
                                                data-sitekey="6LeSFDEUAAAAAPSBwXLuph-qK5d4uksBUyD6_3un"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6">
                                            <button type="submit" class="btn btn-secondary event-form-button"
                                                id="book_event_choose_time" name="book_event_choose_time">Confirm & Book
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end section page body-->

    <!--start footer section-->
    <?php include_once("footer.php"); ?>
    <!--end footer section-->

    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>
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
    <!-- <script type="text/javascript" src="js/infobox.js"></script> -->
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
    <script type="text/javascript">
    jQuery(document).ready(function() {
        getEventTime();

        jQuery('#book_event_date').on("change", function(e) {
            e.stopImmediatePropagation();
            getEventTime();
        });

        // On submit click - check if the email provided is a valid email address.
        jQuery("#book_event_choose_time").on("click", function() {
            let email_provided = jQuery("#visitor_email").val();
            if (/\S+@\S+\.\S+/.test(email_provided) == false) {
                alert("Enter a valid email address.");
                return false;
            }
        });

        function getEventTime() {
            jQuery('#book_event_slots option').remove();
            jQuery.ajax({
                type: "GET",
                url: "book_event_time_source.php",
                dataType: 'json',
                data: {
                    building_id: jQuery('#building_id').val(),
                    book_event_date: jQuery('#book_event_date').val()
                },
                async: true,
                success: function(json) {
                    if (json.length < 1) {
                        alert("No slots available for the selected date.");
                        return;
                    }
                    jQuery.each(json, function(key, value) {
                        jQuery('#book_event_slots').append('<option value="' + value + '&' +
                            key + '">' + key + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
            jQuery('#book_event_time').show();
        }

    });
    </script>
</body>

</html>