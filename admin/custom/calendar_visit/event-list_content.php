<?php
$building_id = $_GET['building_id'];
$employee_id = $_SESSION['employee_id'];
?>
<link href="custom/calendar_visit/css/table_style.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/owl.carousel.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/calendar.css" rel="stylesheet">

<!--Start Scripts-->
<script type="text/javascript" src="custom/calendar_visit/js/modernizr.custom.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/jquery.matchHeight-min.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/bootstrap-select.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/jquery-ui.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/masonry.pkgd.min.html"></script>
<script type="text/javascript" src="custom/calendar_visit/js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/infobox.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/markerclusterer.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/custom.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/calendar.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/underscore-min.js"></script>
<style>
    .content-wrapper {
        background-image: none !important;
    }
</style>

<section id="section-body">
    <div class="container">
        <!-- nav -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#calendar_panel" aria-controls="calendar_panel" role="tab" data-toggle="tab">Calendar</a></li>
            <li role="presentation"><a id="nav_visitor_events" href="#visit_event_list_panel" aria-controls="visit_event_list_panel" role="tab" data-toggle="tab">Visit Events</a></li>
            <li role="presentation"><a id="nav_office_events" href="#office_events_list_panel" aria-controls="office_events_list_panel" role="tab" data-toggle="tab">Office & Maintenance
                    Events</a></li>
        </ul>

        <div class="tab-content">
            <!-- calendar panel -->
            <div role="tabpanel" class="tab-pane fade in active" id="calendar_panel">
                <div class="page-header row" style="margin-top: 10px;">
                    <div class="pull-right form-inline">
                        <div class="btn-group">
                            <button class="btn btn-secondary btn-primary" data-calendar-nav="prev">
                                << Prev</button>
                                    <button class="btn btn-primary" data-calendar-nav="today">Today</button>
                                    <button class="btn btn-secondary btn-primary" data-calendar-nav="next">Next
                                        >></button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-secondary btn-primary" data-calendar-view="year">Year</button>
                            <button class="btn btn-secondary active btn-primary" data-calendar-view="month">Month</button>
                            <button class="btn btn-secondary btn-primary" data-calendar-view="week">Week</button>
                            <button class="btn btn-secondary btn-primary" data-calendar-view="day">Day</button>
                        </div>
                    </div>
                    <h3></h3>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div id="content-area" class="contact-area">
                            <div class="white-block">
                                <div class="row">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="events-modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body" style="height: 500px;">
                            </div>
                            <div class="modal-footer">
                                <a href="#" data-dismiss="modal" class="btn">Close</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="legend" style="margin-top: 8px;">
                    <div class="col-md-2 col-md-offset-4"><a class="pull-left event" style="margin-top: 3px;"></a>Supervised Events</div>
                    <div class="col-md-2"><a class="pull-left event event-info" style="margin-top: 3px;"></a>&nbsp;Visit
                        Events</div>
                    <div class="col-md-2"><a class="pull-left event event-warning" style="margin-top: 3px;"></a>&nbsp;Office Events</div>
                    <div class="col-md-2"><a class="pull-left event event-important" style="margin-top: 3px;"></a>&nbsp;Maintenance Events</div>
                </div>
            </div>

            <!-- visit_event panel -->
            <div role="tabpanel" class="tab-pane" id="visit_event_list_panel">
                <div class="row" style="margin-top: 10px;">
                    <a href="create-event-oneonone.php?building_id=<?php echo $building_id; ?>" id="create-event" class="btn btn-primary btn-md" role="button" style="margin: 0px 0px 25px 15px;">Create
                        Events</a>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div id="content-area" class="contact-area">
                            <div class="white-block">

                                <legend>Visit Events</legend>

                                <div id="visit_events_alert_board"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="col-xs-3 text-center">Event Name</th>
                                                <th class="col-xs-3 text-center">Location</th>
                                                <th class="col-xs-2 text-center">Person in charge</th>
                                                <th class="text-center" style="width: 2%">Edit</th>
                                                <th class="text-center" style="width: 2%">Del.</th>
                                                <th class="text-center" style="width: 12%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include_once("../pdo/dbconfig.php");
                                            $results = $DB_calendar->get_visit_events($building_id);
                                            foreach ($results as $row) {
                                                $event_id = $row['id'];
                                                $event_name = $row['event_name'];
                                                $event_location = $row['event_location'];
                                                $event_max_book_per_slot = $row['event_max_book_per_slot'];
                                                $event_responsible_employee_id = $row['resonsible_employee_id'];
                                                $person_in_charge = $DB_calendar->get_employee_info($event_responsible_employee_id)['full_name'];
                                                if ($event_max_book_per_slot == 1) {
                                            ?>
                                                    <tr>
                                                        <form action="custom/calendar_visit/controller_event.php" method="post">
                                                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                                            <input type="hidden" name="building_id" value="<?php echo $building_id ?>">

                                                            <td class="text-center"><?php echo $event_name; ?></td>
                                                            <td class="text-center"><?php echo $event_location; ?></td>
                                                            <td class="text-center"><?php echo $person_in_charge; ?></td>
                                                            <td class="text-center" style="width: 2%;">
                                                                <button type="submit" class="table-icon" id="<?php echo 'details_event_oneonone_' . $event_id; ?>" name="details_event_oneonone">
                                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                                </button>
                                                            </td>

                                                            <td class="text-center" style="width: 2%;">
                                                                <button type="submit" class="table-icon" name="delete_event_oneonone" onclick="return confirm('Are you sure to delete this event?')">
                                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                                </button>
                                                            </td>

                                                            <td class="text-center" style="border-right: 0px; width: 13%;">
                                                                <button type="submit" class="btn table-button" name="book_event">Book</button>
                                                                <button type="submit" class="btn table-button" id="<?php echo 'event_booking_list' . $event_name; ?>" name="event_booking_list">Booked</button>
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
            </div>

            <!-- office_event panel -->
            <div role="tabpanel" class="tab-pane" id="office_events_list_panel">
                <div class="row" style="margin-top: 10px;">
                    <a href="office-create-event.php?event_category=office&building_id=<?php echo $building_id; ?>" class="btn btn-primary btn-md" role="button" style="margin: 0px 0px 25px 15px;">Create
                        Events</a>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div id="content-area" class="contact-area">
                            <div class="white-block">

                                <div class="row">
                                    <legend>My Events</legend>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="col-xs-2 text-center">Event Name</th>
                                                    <th class="col-xs-2 text-center">Event Category</th>
                                                    <th class="col-xs-2 text-center">Event Type</th>
                                                    <th class="col-xs-2 text-center">Event Date</th>
                                                    <th class="col-xs-2 text-center">Created By</th>
                                                    <th class="text-center" style="width: 2%">Edit</th>
                                                    <th class="text-center" style="width: 2%">Del.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                include_once("../pdo/dbconfig.php");
                                                $results = $DB_calendar->get_office_maintenance_events_list($building_id, $employee_id);
                                                foreach ($results as $row) {
                                                    $event_id = $row['id'];
                                                    $event_name = $row['event_name'];
                                                    $event_date = $row['event_date'];
                                                    $event_type = $row['event_type'];
                                                    $event_category = $row['event_category'];
                                                    $event_created_by = $row['event_created_by_user_id'];
                                                    //get created by persons' name
                                                    $results_for_name = $DB_calendar->get_employee_info($event_created_by);
                                                    $event_created_by_name = $results_for_name['full_name'];

                                                    $event_frequency_type = $row['event_frequency_type'];
                                                    if ($event_type == "regular")
                                                        $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
                                                    else
                                                        $event_date_value = date('Y-m-d', strtotime($event_date));
                                                ?>
                                                    <tr>
                                                        <form action="custom/calendar_visit/office-controller-event.php" method="post">
                                                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                                            <input type="hidden" name="building_id" value="<?php echo $building_id; ?>" />

                                                            <td class="text-center"><?php echo $event_name; ?></td>
                                                            <td class="text-center"><?php echo $event_category; ?></td>
                                                            <td class="text-center"><?php echo $event_type; ?></td>
                                                            <td class="text-center"><?php echo $event_date_value; ?></td>
                                                            <td class="text-center"><?php echo $event_created_by_name; ?>
                                                            </td>

                                                            <td class="text-center" style="width: 2%">
                                                                <button type="submit" class="table-icon" name="details_event">
                                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                                </button>
                                                            </td>

                                                            <td class="text-center" style="width: 2%">
                                                                <button type="submit" class="table-icon" name="delete_event" onclick="return confirm('Are you sure to delete this event?')">
                                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                                </button>
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

                                <div class="row">
                                    <legend>Created Office & Maintenance Events</legend>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-condensed">
                                            <thead>
                                                <tr>
                                                    <th class="col-xs-2 text-center">Event Name</th>
                                                    <th class="col-xs-2 text-center">Event Category</th>
                                                    <th class="col-xs-2 text-center">Event Date</th>
                                                    <th class="col-xs-2 text-center">Assign To</th>
                                                    <th class="col-xs-2 text-center">Created Date</th>
                                                    <th class="text-center" style="width: 2%">Edit</th>
                                                    <th class="text-center" style="width: 2%">Del.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $results = $DB_calendar->get_created_office_maintenance_events($building_id, $employee_id);
                                                foreach ($results as $row) {
                                                    $event_id = $row['id'];
                                                    $event_name = $row['event_name'];
                                                    $event_category = $row['event_category'];
                                                    $event_date = $row['event_date'];
                                                    $created_date = $row['created_date'];
                                                    $event_type = $row['event_type'];
                                                    $event_frequency_type = $row['event_frequency_type'];

                                                    if ($event_type == "regular")
                                                        $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
                                                    else
                                                        $event_date_value = $event_date;

                                                    $assigntos_set = $DB_calendar->get_event_assigntos_details($event_id);
                                                    $assigntos_value = "";
                                                    foreach ($assigntos_set as $r) {
                                                        $assigned_name = $r['full_name'];
                                                        $assigntos_value .= $assigned_name . ' ';
                                                    }
                                                ?>
                                                    <tr>
                                                        <form action="custom/calendar_visit/office-controller-event.php" method="post">
                                                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                                            <input type="hidden" name="building_id" value="<?php echo $building_id; ?>" />

                                                            <td class="text-center"><?php echo $event_name; ?></td>
                                                            <td class="text-center"><?php echo $event_category; ?></td>
                                                            <td class="text-center"><?php echo $event_date_value; ?></td>
                                                            <td class="text-center"><?php echo $assigntos_value; ?></td>
                                                            <td class="text-center">
                                                                <?php echo date('Y-m-d', strtotime($created_date)); ?></td>

                                                            <td class="text-center" style="width: 2%">
                                                                <button type="submit" class="table-icon" name="details_event" style="">
                                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                                </button>
                                                            </td>

                                                            <td class="text-center" style="width: 2%">
                                                                <button type="submit" class="table-icon" name="delete_event" onclick="return confirm('Are you sure to delete this event?')">
                                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                                </button>
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
                </div>
            </div>
            <!-- end of all panels -->
        </div>

    </div>
</section>

<script>
    "use strict";

    var options = {
        events_source: 'custom/calendar_visit/calendar_event_source.php?building_id=<?php echo $building_id; ?>&employee_id=<?php echo $employee_id; ?>',
        view: 'month',
        tmpl_path: 'custom/calendar_visit/tmpls/',
        tmpl_cache: false,
        day: 'now',
        modal: "#events-modal",
        modal_title: function(event) {
            return event.title;
        },
        onAfterEventsLoad: function(events) {
            if (!events) {
                return;
            }
            var list = $('#eventlist');
            list.html('');

            $.each(events, function(key, val) {
                $(document.createElement('li'))
                    .html('<a href="' + val.url + '">' + val.title + '</a>')
                    .appendTo(list);
            });
        },
        onAfterViewLoad: function(view) {
            $('.page-header h3').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
        },
        classes: {
            months: {
                general: 'label'
            }
        }
    };

    var calendar = $('#calendar').calendar(options);
    $('.btn-group button[data-calendar-nav]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.navigate($this.data('calendar-nav'));
        });
    });

    $('.btn-group button[data-calendar-view]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.view($this.data('calendar-view'));
        });
    });

    $('#first_day').change(function() {
        var value = $(this).val();
        value = value.length ? parseInt(value) : null;
        calendar.setOptions({
            first_day: value
        });
        calendar.view();
    });

    $('#language').change(function() {
        calendar.setLanguage($(this).val());
        calendar.view();
    });

    $('#events-in-modal').change(function() {
        var val = $(this).is(':checked') ? $(this).val() : null;
        calendar.setOptions({
            modal: val
        });
    });
    $('#format-12-hours').change(function() {
        var val = $(this).is(':checked') ? true : false;
        calendar.setOptions({
            format12: val
        });
        calendar.view();
    });
    $('#show_wbn').change(function() {
        var val = $(this).is(':checked') ? true : false;
        calendar.setOptions({
            display_week_numbers: val
        });
        calendar.view();
    });
    $('#show_wb').change(function() {
        var val = $(this).is(':checked') ? true : false;
        calendar.setOptions({
            weekbox: val
        });
        calendar.view();
    });
</script>


<?php
if (isset($_GET['direct'])) {

    if ($_GET['direct'] == 'visitor_events') {
?>
        <script>
            $(document).ready(function() {
                $('#nav_visitor_events').trigger('click');
            });
        </script>
        <?php
        if ($_GET['alert'] == 'booked_events_can_not_delete') {
        ?>
            <script>
                $('#visit_events_alert_board').append(
                    '<div class="alert alert-warning alert-dismissible fade in" role="alert" id="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' +
                    '<b>The visit event can not be deleted, because there are booked appointments in the future ! </b>' +
                    '</div>');
            </script>
        <?php
        }
    } elseif ($_GET['direct'] == 'inner_events') {
        ?>
        <script>
            $(document).ready(function() {
                $('#nav_office_events').trigger('click');
            });
        </script>

<?php
    }
}
function cast_event_date_value($event_date, $event_frequency_type)
{
    if ($event_frequency_type == "day")
        return "workdays";
    else if ($event_frequency_type == "week") {
        $weekday = date('l', strtotime($event_date));
        return $weekday . 's';
    } else if ($event_frequency_type == "month") {
        $month_day = date('d', strtotime($event_date));
        return $month_day . ' per month';
    } else if ($event_frequency_type == "year") {
        $year_day = date('F,j', strtotime($event_date));
        return $year_day . ' every year';
    }
}
?>