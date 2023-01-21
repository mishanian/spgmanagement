
<?php

include_once ("pdo/dbconfig.php");
include_once ('pdo/Class.Calendar.php');
$DB_calendar = new Calendar($DB_con);
require('action_booking.php');

$action_booking = new action_booking();
$building_id = $_GET['building_id'];
$book_event_date = $_GET['book_event_date'];
$time_slots_send = array();

$event_ids = $DB_calendar->get_event_ids($building_id);
foreach ($event_ids as $r) {
    $event_id=$r['id'];
    $row = $DB_calendar->get_visit_event($event_id);
    $event_name = $row['event_name'];
    $event_location = $row['event_location'];
    $event_description = $row['event_description'];
    $event_increment = $row['event_increment'];
    $event_max_book_per_day = $row['event_max_book_per_day'];
    $event_max_book_per_slot = $row['event_max_book_per_slot'];
    $event_buffer_before = $row['event_buffer_before'];
    $event_buffer_after = $row['event_buffer_after'];
    $event_less_hour = $row['event_less_hour'];
    if ($row['event_duration'] == 0) {
        $event_duration = $row['event_custom_duration'];
    } else {
        $event_duration = $row['event_duration'];
    }

    $time_slots = array();
    $result = $DB_calendar->get_bookings($event_id, $book_event_date);
    if ($event_max_book_per_day == 0 || ($event_max_book_per_day != 0 && count($result) < $event_max_book_per_day)) {
        $time_slots = $action_booking->get_time_slots($event_id, $book_event_date, $event_increment, $event_max_book_per_slot, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_duration);

        for ($i = 0; $i < count($time_slots); $i++) {
            $key = date("H:i", strtotime($time_slots[$i]));
            $value = $event_id;
            $time_slots_send[$key] = $value;
        }
    }
}

echo json_encode($time_slots_send);

exit;
?>

