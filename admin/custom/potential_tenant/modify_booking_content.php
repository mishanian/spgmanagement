<?php
include_once("../pdo/dbconfig.php");
if (!array_key_exists("bid", $_GET)) {
	echo "Invalid page";
	exit;
}
else {
	$pageData["bookingInfo"] = $DB_calendar->getBookingInfo($_GET["bid"]);
	$pageData["bid"]         = $_GET["bid"];
}
?>

<input type="hidden" id="booking_time_val" value="<?php echo $pageData["bookingInfo"]["booking_start"]; ?>"/>

<div class="container-fluid">
    <div class="panel panel-info">
        <div class="panel-heading">
            Modify Visit Booking # <?php echo $pageData["bid"]; ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id="content-area" class="contact-area">
                        <div class="white-block">
                            <div class="row">
								<?php
								$apt_id   = $pageData["bookingInfo"]["visitor_desired_unit"];
								$event_id = $pageData["bookingInfo"]["event_id"];

								$row               = $DB_calendar->get_event($event_id);
								$building_id       = $row['building_id'];
								$event_name        = $row['event_name'];
								$event_location    = $row['event_location'];
								$event_description = $row['event_description'];
								if ($row['event_duration'] == 0) {
									$event_duration = $row['event_custom_duration'];
								}
								else {
									$event_duration = $row['event_duration'];
								}
								?>

                                <form class="panel panel-primary" id="create-event-oneonone-form"
                                      action="controller_event.php" method="post" style="margin:10px">
                                    <div class="panel-heading"> Appointment Information</div>
                                    <div class="panel-body">
                                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>

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
                                                            <span style="text-transform:capitalize"><?php echo $event_description ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                                <form style="margin:10px" class="panel panel-primary"
                                      id="modify-event-oneonone-availability-form"
                                      action="controller_event.php" method="post">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
                                    <input type="hidden" name="booking_id" value="<?php echo $_GET["bid"]; ?>">
                                    <input type="hidden" name="action" value="modify_booking">
                                    <div class="panel-heading">Booking Information</div>
                                    <div class="panel-body">
                                        <input type="hidden" id="building_id" name="building_id"
                                               value="<?php echo $building_id; ?>">
                                        <input type="hidden" id="apt_id" name="apt_id" value="<?php echo $apt_id; ?>">
										<?php if (isset($_GET['portal'])) { ?>
                                            <input type="hidden" id="from_portal" name="from_portal" value="true">
											<?php
										} ?>

                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6">
                                                <label>Appointment Unit </label>
                                                <div style="clear:both;">&nbsp;</div>
                                                <div class="prop_details">
                                            <span><?php echo $DB_apt->getAptInfo($apt_id)['unit_number'] ?>
                                                in <?php echo $DB_building->getBdInfo($building_id)['building_name']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6">
                                                <label>Appointment Date</label>
                                                <select id="book_event_date" name="book_event_date"
                                                        class="form-control book_event_field" type="text"
                                                        autocomplete="off">
													<?php
													include_once('../action_booking.php');
													$action_booking = new action_booking();
													$dates          = $action_booking->get_availablility_dates($building_id);

													for ($i = 0; $i < count($dates); $i++) {
														$year      = date("Y", strtotime($dates[$i]));
														$month     = date("m", strtotime($dates[$i]));
														$day       = date("d", strtotime($dates[$i]));
														$jd        = gregoriantojd($month, $day, $year);
														$dayofweek = jddayofweek($jd, 1);
														$text      = $dates[$i] . ' ' . $dayofweek;
														$selected  = "";
														if ($pageData["bookingInfo"]["booking_date"] == $dates[$i]) {
															$selected = "selected";
														}
														?>
                                                        <option value="<?php echo $dates[$i]; ?>" <?php echo $selected; ?>><?php echo $text; ?></option>
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
                                                        class="form-control book_event_field" type="text"
                                                        autocomplete="off"
                                                        required></select>
                                            </div>
                                            <div class="book-event-col-2 col-md-4 form-item-top-padding-1">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6"><label>Expected Move-In Month</label>
                                                <select class="form-control" name="movein_month" id="movein_month">
													<?php
													$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
													foreach ($months as $index => $month) {
														$selected = "";
														if ($index == $pageData["bookingInfo"]["move_in_month"]) {
															$selected = "selected";
														}
														?>
                                                        <option value="<?php echo $index; ?>" <?php echo $selected; ?>> <?php echo $month; ?></option>
													<?php }
													?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6"><label>Customer Name</label><input
                                                        type="text"
                                                        class="form-control"
                                                        name="visitor_name"
                                                        id="visitor_name"
                                                        value="<?php echo $pageData["bookingInfo"]["customer_name"]; ?>"
                                                        required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6"><label>Customer Email</label><input
                                                        type="email"
                                                        class="form-control"
                                                        name="visitor_email"
                                                        id="visitor_email"
                                                        value="<?php echo $pageData["bookingInfo"]["customer_email"]; ?>"
                                                        required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6"><label>Customer
                                                    Telephone</label><input
                                                        type="tel" class="form-control" name="visitor_phone"
                                                        id="visitor_phone"
                                                        value="<?php echo $pageData["bookingInfo"]["telephone"]; ?>"
                                                        required></div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="book-event-col-1 col-md-6">
                                                <button type="button" class="btn btn-primary event-form-button"
                                                        id="modify_event_choose_time" name="book_event_choose_time">
                                                    Modify
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        getEventTime();

        $('#book_event_date').on("change", function (e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            console.log("a");
            getEventTime();
        });

        // On submit click - check if the email provided is a valid email address.
        $("#modify_event_choose_time").on("click", function () {
            let email_provided = $("#visitor_email").val();
            if (/\S+@\S+\.\S+/.test(email_provided) == false) {
                alert("Enter a valid email address.");
                return false;
            }

            formData = $("#modify-event-oneonone-availability-form").serialize();

            /* Send an ajax request to the booking controller to modify the booking done by the customer and send a notification to both the customer and the agent */
            $.ajax({
                type: "POST",
                url: "custom/potential_tenant/booking_controller.php",
                dataType: 'json',
                data: formData,
                success: function (json) {
                    if (json) {
                        if (json.value) {
                            alert("Booking has been modified and notified to the customer.");
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert("There was an error in modifying the booking. Please try again!");
                    console.log(xhr.responseText);
                }
            });

        });

        function getEventTime() {
            $('#book_event_slots option').remove();
            $.ajax({
                type: "GET",
                url: "../../../book_event_time_source.php",
                dataType: 'json',
                data: {building_id: $('#building_id').val(), book_event_date: $('#book_event_date').val()},
                async: true,
                success: function (json) {
                    if (json.length < 1) {
                        alert("No slots available for the selected date.");
                        return;
                    }
                    booking_id_time = $("#booking_time_val").val();
                    $.each(json, function (key, value) {
                        selected = "";
                        if (booking_id_time == key) {
                            selected = "selected";
                        }
                        $('#book_event_slots').append('<option value="' + value + '&' + key + '" ' + selected + '>' + key + '</option>');
                    });
                },
                error: function (xhr, status, error) {
                    alert("There was an error fetching the available slots.");
                }
            });
            $('#book_event_time').show();
        }


    });

</script>

