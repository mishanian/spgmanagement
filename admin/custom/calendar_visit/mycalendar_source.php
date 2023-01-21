<?php
include ("pdo/dbconfig.php");

$calendar_type = "visit";
$results = $DB_calendar->get_events_oneonone($building_id, $calendar_type);
date_default_timezone_set('America/New_York');

$out = array();
foreach ( $results as $row ) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $result = $DB_calendar->get_all_bookings($event_id);
    foreach ( $result as $r ) {
        $out[] = array(
            'id' => $r['id'],
            'title' => $event_name,
            'url' => "booking-details.php?"."booking_date=".$r['booking_date']."&booking_start=".$r['booking_start']."&event_id=" . $event_id."&booking_end=".$r['booking_end'],
            "class" => "event-important",
            'start' => strtotime($r['booking_date'].' '.$r['booking_start']) . '000',
            'end' => strtotime($r['booking_date'].' '.$r['booking_end']) .'000'
        );
    }
}

echo json_encode(array('success' => 1, 'result' => $out));
exit;
?>