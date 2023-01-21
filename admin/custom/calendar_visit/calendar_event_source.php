<?php
include("../../../pdo/dbconfig.php");
$building_id = $_GET['building_id'];
$employee_id = $_GET['employee_id'];

$time_limit = date('Y-m-d', strtotime('today + 3 year'));
$out = array();

//office events
$result_office = $DB_calendar->get_office_maintenance_events("office", $building_id, $employee_id);
foreach ($result_office as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    if ($event_type == "regular") {
        $current_date = $event_date;
        while ($current_date <= $time_limit) {
            $out[] = array(
                'id' => $event_id,
                'title' => $event_name,
                'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
                "class" => "event-warning",
                'start' => strtotime($current_date) . '000',
                'end' => strtotime($current_date) . '000'
            );
            $current_date = date('Y-m-d H:i:s', strtotime($current_date . ' + ' . $event_frequency . ' ' . $event_frequency_type));
        }
    }
    else {
        $out[] = array(
            'id' => $event_id,
            'title' => $event_name,
            'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
            "class" => "event-warning",
            'start' => strtotime($event_date) . '000',
            'end' => strtotime($event_date) . '000'
        );
    }
}


//maintenance events
$result_maintenance = $DB_calendar->get_office_maintenance_events("maintenance", $building_id, $employee_id);
foreach ($result_maintenance as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    if ($event_type == "regular") {
        $current_date = $event_date;
        while ($current_date <= $time_limit) {
            $out[] = array(
                'id' => $event_id,
                'title' => $event_name,
                'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
                "class" => "event-important",
                'start' => strtotime($current_date) . '000',
                'end' => strtotime($current_date) . '000'
            );

            $current_date = date('Y-m-d H:i:s', strtotime($current_date . ' + ' . $event_frequency . ' ' . $event_frequency_type));
        }
    }
    else {
        $out[] = array(
            'id' => $event_id,
            'title' => $event_name,
            'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
            "class" => "event-important",
            'start' => strtotime($event_date) . '000',
            'end' => strtotime($event_date) . '000'
        );
    }
}


//visit events
$results_visit = $DB_calendar->get_visit_events_by_employee($building_id, $employee_id);
foreach ($results_visit as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $result = $DB_calendar->get_all_bookings($event_id);
    foreach ($result as $r) {
        $out[] = array(
            'id' => $r['id'],
            'title' => $event_name,
            'url' => "custom/calendar_visit/booking-details.php?" . "booking_date=" . $r['booking_date'] . "&booking_start=" . $r['booking_start'] . "&event_id=" . $event_id . "&booking_end=" . $r['booking_end'],
            "class" => "event-info",
            'start' => strtotime($r['booking_date'] . ' ' . $r['booking_start']) . '000',
            'end' => strtotime($r['booking_date'] . ' ' . $r['booking_end']) . '000'
        );
    }
}


// managed employee visit events
$employee_info = $DB_calendar->get_employee_info($employee_id);
$is_admin = $employee_info['admin_id'];

if($is_admin == 1){
    $managed_visit_events = $DB_calendar->get_visit_events_by_admin_building($employee_id,$building_id);
    foreach ($managed_visit_events as $row){
        $event_id = $row['id'];
        $event_name = $row['event_name'];
        $result = $DB_calendar->get_all_bookings($event_id);
        foreach ( $result as $r ) {
            $out[] = array(
                'id' => $r['id'],
                'title' => $event_name,
                'url' => "custom/calendar_visit/booking-details.php?"."booking_date=".$r['booking_date']."&booking_start=".$r['booking_start']."&event_id=" . $event_id."&booking_end=".$r['booking_end'],
                "class" => "",
                'start' => strtotime($r['booking_date'].' '.$r['booking_start']) . '000',
                'end' => strtotime($r['booking_date'].' '.$r['booking_end']) .'000'
            );
        }
    }
}

/*
// handyman events
$handyman_bookings = $DB_calendar->get_all_handyman_bookings_for_handyman($employee_id);
foreach ($handyman_bookings as $row){
    $handyman_booking_id = $row['id'];
    $avail_slot_id = $row['avail_slot_id'];
    $apartment_id = $row['apartment_id'];
    $book_from = $row['book_from'];
    $book_to = $row['book_to'];

    $slot_info = $DB_calendar->get_handyman_avail_info($avail_slot_id);
    $handyman_slot_date = $slot_info['slot_date'];

    // get building info
    $building_info = $DB_calendar->get_building_apartment_info_from_apartment_id($apartment_id);
    $building_name = $building_info['building'];
    $unit = $building_info['apartment'];
    $out[] = array(
        'id' => $avail_slot_id,
        'title' => "Repair in $building_name $unit",
        'url' => "custom/calendar_visit/handyman_event_details.php?"."handyman_booking_id=".$handyman_booking_id."&handyman_slot_id=".$avail_slot_id,
        "class" => "event-success",
        'start' => strtotime($handyman_slot_date.' '.$book_from) . '000',
        'end' => strtotime($handyman_slot_date.' '.$book_to) .'000'
    );
}

*/

//encode
echo json_encode(array('success' => 1, 'result' => $out));
exit;
?>