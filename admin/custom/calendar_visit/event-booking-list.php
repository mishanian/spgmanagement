<?php
$building_id = $_GET['building_id'];
$event_id = $_GET['event_id'];
?>

<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/table_style.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/calendar-events.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script src="custom/calendar_visit/js/bootstrap-datepicker.js"></script>

<section id="section-body">
    <div class="container">

        <div class="page-title breadcrumb-top">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="event-list.php?building_id=<?php echo $building_id; ?>&direct=visitor_events">Visit
                                Events</a></li>
                        <li class="active">Event Booking List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div id="content-area" class="contact-area">
                    <div class="white-block">
                        <div class="row">
                            <?php
              include_once("../pdo/dbconfig.php");
              $row = $DB_calendar->get_visit_event($event_id);
              $event_name = $row['event_name'];
              $event_location = $row['event_location'];
              $event_description = $row['event_description'];
              $event_person_in_charge = $row['resonsible_employee_id'];
              $event_person_in_charge = $DB_calendar->get_employee_info($event_person_in_charge);
              $event_person_in_charge_name = $event_person_in_charge['full_name'];
              $event_person_in_charge_telephone = $event_person_in_charge['mobile'];
              $event_person_in_charge_email = $event_person_in_charge['email'];
              if ($row['event_duration'] == 0) {
                $event_duration = $row['event_custom_duration'];
              } else {
                $event_duration = $row['event_duration'];
              }
              ?>
                            <form id="create-event-oneonone-form" action="custom/calendar_visit/controller_event.php"
                                method="post">
                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />


                                <div class="col-md-12">
                                    <legend>Event Information</legend>
                                </div>
                                <div class="col-sm-12 col-xs-12 contact-block-inner">
                                    <div class="form-group col-md-8">
                                        <h5>Event Name: <?php echo $event_name; ?></h5>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <h5>Duration: <?php echo $event_duration; ?> min</h5>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <h5>Location: <?php echo $event_location; ?></h5>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <h5>Person in charge: <?php echo $event_person_in_charge_name; ?></h5>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <h5>Description: <?php echo $event_description; ?></h5>
                                    </div>
                                </div>
                            </form>


                            <div class="col-md-12">
                                <legend>Bookings</legend>
                            </div>
                            <table class="table table-striped" id="bookings_table" style="background: white">
                                <thead>
                                    <tr>
                                        <th class="col-md-2 text-center">Date</th>
                                        <th class="col-md-2 text-center">Start Time</th>
                                        <th class="col-md-2 text-center">Customer Name</th>
                                        <th class="col-md-2 text-center">Email</th>
                                        <th class="col-md-2 text-center">Telephone</th>
                                        <th class="col-md-3 text-center">Desired Apartment</th>
                                        <th class="col-md-1 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="booking_list_body">
                                    <?php
                  $results = $DB_calendar->get_all_bookings($event_id);
                  foreach ($results as $row) {
                    $booking_id = $row['id'];
                    $booking_date = $row['booking_date'];
                    $booking_start = $row['booking_start'];
                    $customer_name = $row['customer_name'];
                    $customer_email = $row['customer_email'];
                    $telephone = $row['telephone'];
                    $desired_unit_id = $row['visitor_desired_unit'];
                    $desired_unit_name = $DB_apt->getAptInfo($desired_unit_id)['unit_number'];

                    $is_past = false;
                    if (strtotime($booking_date) < strtotime(date("Y-m-d"))) {
                      $is_past = true;
                    }

                  ?>
                                    <tr class="<?php if ($is_past) echo 'txt-gray'; ?>">
                                        <td class="text-center"><?php echo date('Y-m-d', strtotime($booking_date)); ?>
                                        </td>
                                        <td class="text-center"><?php echo $booking_start; ?></td>
                                        <td class="text-center"><?php echo $customer_name; ?></td>
                                        <td class="text-center"><?php echo $customer_email; ?></td>
                                        <td class="text-center"><?php echo $telephone; ?></td>
                                        <td class="text-center"><?php echo $desired_unit_name; ?></td>

                                        <form action="custom/calendar_visit/controller_event.php" method="post">
                                            <td class="text-center">
                                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $booking_id; ?>" />
                                                <input type="hidden" name="building_id"
                                                    value="<?php echo $building_id; ?>" />
                                                <input type="hidden" name="customer_name"
                                                    value="<?php echo $customer_name; ?>" />
                                                <input type="hidden" name="telephone"
                                                    value="<?php echo $telephone; ?>" />
                                                <input type="hidden" name="customer_email"
                                                    value="<?php echo $customer_email; ?>" />
                                                <input type="hidden" name="event_description"
                                                    value="<?php echo $event_description; ?>" />
                                                <input type="hidden" name="event_location"
                                                    value="<?php echo $event_location; ?>" />
                                                <input type="hidden" name="booking_time"
                                                    value="<?php echo $booking_date . ' ' . $booking_start; ?>" />
                                                <input type="hidden" name="person_in_charge_name"
                                                    value="<?php echo $event_person_in_charge_name; ?>" />
                                                <input type="hidden" name="person_in_charge_telephone"
                                                    value="<?php echo $event_person_in_charge_telephone; ?>" />
                                                <input type="hidden" name="person_in_charge_email"
                                                    value="<?php echo $event_person_in_charge_email; ?>" />
                                                <button type="submit"
                                                    class="btn table-button <?php if ($is_past) echo 'non-display'; ?>"
                                                    name="cancel_booking"
                                                    onclick="return confirm('Are you sure to cancel this booking?')">Cancel</button>
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
<script>
$(document).ready(function() {
    var table = $('#bookings_table').DataTable({
        "iDisplayLength": 25,
        "scrollX": true,
        "ordering": false,
        "order": [
            [0, "desc"]
        ],
        "columnDefs": [{
            "type": "date-uk",
            targets: 0
        }]
    });

    //add custom filter
    $('#bookings_table_length').append('<div class="hidden-xs col-md-5" style="float: right">\n' +
        '                  <div class="form-group">\n' +
        '                    <label for="filter_from" class="hidden-xs col-md-2 filter-text">From:</label>\n' +
        '                    <div class="col-sm-8 col-md-10">\n' +
        '                      <input type="text" class="form-control date-input" id="filter_from" style="min-width: 0">\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                </div>');

    $('#bookings_table_filter').prepend('<div class="hidden-xs col-md-5">\n' +
        '                  <div class="form-group">\n' +
        '                    <label for="filter_to" class="hidden-xs col-md-2 filter-text">To:</label>\n' +
        '                    <div class="col-sm-8 col-md-10">\n' +
        '                      <input type="text" class="form-control date-input" id="filter_to" style="min-width: 0">\n' +
        '                    </div>\n' +
        '                  </div>\n' +
        '                </div>');


    //bind with custom filter
    $('#filter_from, #filter_to').change(function() {
        table.draw();
    });

    //date_picker
    $('.date-input').datepicker({
        format: 'y-MM-dd'
    });
});

$.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function(a) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },

    "date-uk-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-uk-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});


//filtering the date
function uk_date_parse(uk_date) {
    if (uk_date == null || uk_date === '') {
        return 0;
    }
    var date_arr = uk_date.split('/');
    return (date_arr[2] + date_arr[1] + date_arr[0]) * 1;
}

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    var min_date = uk_date_parse($('#filter_from').val());
    var max_date = uk_date_parse($('#filter_to').val());
    var date = uk_date_parse(data[0]) || 0;

    if ((min_date == 0 && max_date == 0) ||
        (min_date == 0 && date <= max_date) ||
        (min_date <= date && max_date == 0) ||
        (min_date <= date && date <= max_date)) {
        return true;
    } else {
        return false;
    }
});
</script>