<?php
/**
 * Created by PhpStorm.
 * User: Sharan
 * Date: 2018-06-21
 * Time: 11:08 AM
 */
?>

<div class="container">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#confirmed-list">Confirmed Bookings</a>
        </li>
        <li><a data-toggle="tab" href="#pending-list">Pending Bookings</a></li>
    </ul>

    <div class="tab-content">
        <div id="confirmed-list" class="tab-pane fade in active">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive whitebg alert">
                        <table class="table table-bordered" id="confirmedbookingsTable">
                            <thead>
                            <tr>
                                <th class="col-md-1 text-center">#</th>
                                <th class="col-md-2 text-center">Building</th>
                                <th class="col-md-1 text-center">Unit</th>
                                <th class="col-md-2 text-center">Customer Name</th>
                                <th class="col-md-1 text-center">Customer Email</th>
                                <th class="col-md-1 text-center">Booking Date</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="pending-list" class="tab-pane fade">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive whitebg alert">
                        <table class="table table-bordered" id="pendingbookingsTable">
                            <thead>
                            <tr>
                                <th class="col-md-1 text-center">#</th>
                                <th class="col-md-2 text-center">Building</th>
                                <th class="col-md-1 text-center">Unit</th>
                                <th class="col-md-2 text-center">Customer Name</th>
                                <th class="col-md-1 text-center">Customer Email</th>
                                <th class="col-md-1 text-center">Booking Date</th>
                                <th class="col-md-1 text-center">Confirm</th>
                                <th class="col-md-1 text-center">Modify</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- External files required in the page -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="js/booking.js"></script>
