<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["employee_id"])) {
    die("Cant read employee information");
}
?>
<div class="container">
    <div class="row form-group">
        <div class="col-md-3">
            <div class="input-group date">
                <input type="text" class="form-control datepickerFrom" placeholder="Select Date">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button id="clearDateFilter" type="button" class="btn btn-default btn-sm"
                style="float:left;margin-left:5px">
                <span class="glyphicon glyphicon-remove"></span> Clear Date
            </button>
        </div>

        <div class="col-md-2">
            <select id="eventTypeFilter" name="eventTypeFilter" class="form-control">
                <option value="default">Select Event Type</option>
                <option value="maintenance">Maintenance</option>
                <option value="office">Office</option>
            </select>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-lg-12">
            <div class="table-responsive alert alert-warning-custom">
                <table class="table table-bordered table-condensed" id="agendaTable">
                    <thead>
                        <tr>
                            <th class="col-md-2 text-center">Date</th>
                            <th class="col-md-2 text-center">Event</th>
                            <th class="col-md-3 text-center">Company</th>
                            <th class="col-md-3 text-center">Person</th>
                            <th class="col-md-3 text-center">Contact</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="events-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="events-modal-body" class="modal-body" style="height: 500px;">
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>
<!---->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
<!--<script type="text/javascript" src="js/bootstrap.js"></script>-->
<!-- <link rel="stylesheet" href="custom/calendar_visit/css/bootstrap-datepicker.css">
<script type="text/javascript" src="custom/calendar_visit/js/bootstrap-datepicker.js"></script> -->

<!-- Style and scripts includes -->
<!-- *** Scripts that are not present in the header - Do not Remove *** -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script> -->
<script>
loadjs.ready(["jquery", "head"], function() {
    loadjs([
        "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css",
        "https://code.jquery.com/ui/1.12.1/jquery-ui.js",
        "https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css",
        "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js",
    ], 'datatable');
});


loadjs.ready(["datatable"], function() {
    loadjs([
        "https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js",
    ], 'jsloaded');
});
</script>
<script>
$(function() {
    var selected = [];
    var dateChanged = false;
    var categoryChanged = false;
    var newDateValue;

    $('.datepickerFrom').datepicker({
        onSelect: function(dateText) {
            // console.log("Selected date: " + dateText + "; input's current value: " + this.value);
            newDateValue = dateText;
            dateChanged = true;
            table.draw();
        },
        autoclose: true,
        numberOfMonths: 3,
        dateFormat: "dd/mm/yy",
        clearBtn: true
    });

    var table = $('#agendaTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "custom/calendar_visit/agenda_controller.php",
            "type": "POST"
        },
        "columns": [{
                "data": "event_date"
            },
            {
                "data": "event_name"
            },
            {
                "data": "company"
            },
            {
                "data": "username"
            },
            {
                "data": "contact"
            }
        ],
        "rowCallback": function(row, data) {
            // Custom data attributes for the row items
            $(row).attr('data-id', data.id);
            $(row).attr('data-url', data.url);
            $(row).attr('data-category', data.event_category);
            $(row).addClass('rowLink');

            if ($.inArray(data.DT_RowId, selected) !== -1) {
                $(row).addClass('selected');
            }
        }
    });

    /***
     * Code for the filters in the page
     */
    $("#eventTypeFilter").change(function(e) {
        var selectedValue = $(this).val();
        categoryChanged = true;
        if (selectedValue == "default") {
            categoryChanged = false;
        }
        table.draw();
        categoryChanged = false;
    });

    $('#agendaTable tbody').on('click', 'tr', function() {
        var id = this.id;
        var index = $.inArray(id, selected);

        if (index === -1) {
            selected.push(id);
        } else {
            selected.splice(index, 1);
        }
    });

    $('#clearDateFilter').on("click", function() {
        $('.datepickerFrom').val('').datepicker("refresh");
        dateChanged = false;
        table.draw();
    });

    /*
     * Data table filter and search
     */
    $("body").on("click", ".rowLink", function() {
        var id = $(this).attr("data-id");
        var url = "custom/calendar_visit/agenda-event-details.php?event_id=" + id;

        $.get(url, function(data) {
            $("#events-modal-body").html(data);
            $("#events-modal").modal("show");
        });
    });

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            // If the date filter is to be applied
            if (dateChanged) {
                var date = data[0].trim();
                if (date == newDateValue) {
                    return true;
                } else {
                    return false;
                }
            }

            if (categoryChanged) {
                var categoryValue = table
                    .row(dataIndex) //get the row to evaluate
                    .nodes() //extract the HTML - node() does not support to$
                    .to$() //get as jQuery object
                    .data('category'); //get the value of data-label
                var selectedCategoryValue = $("#eventTypeFilter").val();
                if (selectedCategoryValue == categoryValue) {
                    return true;
                }
                return false;
            }

            return true;
        }
    );
})
</script>

<style>
tr {
    cursor: pointer;
}

.alert-warning-custom {
    color: #8a6d3b;
    background-color: #f7f7f7;
    border-color: #faebcc;
}
</style>