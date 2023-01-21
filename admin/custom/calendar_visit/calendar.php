<?php
$employee_id = $_SESSION['employee_id'];
?>

<!--page content start-->
<link href="custom/calendar_visit/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/owl.carousel.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
<link href="custom/calendar_visit/css/calendar.css" rel="stylesheet">

<!--Start Scripts-->
<script type="text/javascript" src="custom/calendar_visit/js/modernizr.custom.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="custom/calendar_visit/js/bootstrap.js"></script>
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
        <div class="page-header">
            <div class="pull-right form-inline">
                <div class="btn-group">
                    <button class="btn btn-secondary btn-primary" data-calendar-nav="prev">
                        << Prev</button>
                            <button class="btn btn-primary" data-calendar-nav="today">Today</button>
                            <button class="btn btn-secondary btn-primary" data-calendar-nav="next">Next >></button>
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
            <div class="col-md-2 col-md-offset-4"><a class="pull-left event"
                    style="margin-top: 3px;"></a>&nbsp;Supervised Events</div>
            <div class="col-md-2"><a class="pull-left event event-info" style="margin-top: 3px;"></a>&nbsp;Visit Events
            </div>
            <div class="col-md-2"><a class="pull-left event event-warning" style="margin-top: 3px;"></a>&nbsp;Office
                Events</div>
            <div class="col-md-2"><a class="pull-left event event-important"
                    style="margin-top: 3px;"></a>&nbsp;Maintenance Events</div>
        </div>

    </div>
</section>
<!--end section page body-->

<script>
"use strict";

var options = {
    events_source: 'custom/calendar_visit/calendar_personal_event_source.php?employee_id=<?php echo $employee_id; ?>',
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