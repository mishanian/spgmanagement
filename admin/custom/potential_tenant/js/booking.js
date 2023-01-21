$(function () {

    /* Pendign booking table code */
    var pendingbookingtable = $('#pendingbookingsTable').DataTable({
        "ajax": {
            url: "booking_controller.php",
            "type": "POST",
            "data": {
                "action": "getAllBookings"
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "building_name"},
            {"data": "unit_number"},
            {"data": "customer_name"},
            {"data": "customer_email"},
            {"data": "booking_date"},
            {"data": ""},
            {"data": ""}
        ],
        "columnDefs": [{
            "targets": -2,
            "data": null,
            "defaultContent": "<button class='btn btn-success confirm-booking'> Confirm </button>"
        }, {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='btn btn-primary modify-booking'> Modify</button>"
        }],
        "rowCallback": function (row, data) {
            // Custom data attributes for the row items
            $(row).attr('data-bookingid', data.id);
        }
    });

    $('#pendingbookingsTable tbody').on('click', '.confirm-booking', function () {
        var data = pendingbookingtable.row($(this).parents('tr')).data();
        var row = $(this).parents('tr');
        bookingIdSelected = data.id;

        /* Send an ajax request to the booking controller and change the is_booking_confirmed flag to "1" */
        $.ajax({
            url: "booking_controller.php",
            type: 'POST',
            dataType: 'json',
            data: {
                "action": "confirmBooking",
                "booking_id": bookingIdSelected
            },
            success: function (response) {
                if (response) {
                        pendingbookingtable.row(row).remove().draw(); /* Remove the update row from the pending list table */

                        confirmedbookingtable.ajax.reload(); /* Reload the confirmed table to show the updated row in it's table */
                        alert("Selected Booking is confirmed");
                }
            }
        });


    });

    /* Confirmed booking table code */
    var confirmedbookingtable = $('#confirmedbookingsTable').DataTable({
        "ajax": {
            url: "booking_controller.php",
            "type": "POST",
            "data": {
                "action": "getConfirmedBookings"
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "building_name"},
            {"data": "unit_number"},
            {"data": "customer_name"},
            {"data": "customer_email"},
            {"data": "booking_date"},
        ],
        "rowCallback": function (row, data) {
            // Custom data attributes for the row items
            $(row).attr('data-bookingid', data.id);
        }
    });

});