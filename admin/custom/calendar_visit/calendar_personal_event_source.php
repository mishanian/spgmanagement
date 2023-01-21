<?php
/**
 * this php file is controller to produce the personal events in calendar module.
 * User: t.e.chen
 * Date: 2018-01-29
 */

include ("../../../pdo/dbconfig.php");
$employee_id=$_GET['employee_id'];

$time_limit = date('Y-m-d', strtotime('today + 3 year'));
$out = array();

//office events
$result_office = $DB_calendar->get_personal_office_maintenance_events("office",$employee_id);
foreach ($result_office as $row ) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    if($event_type == "regular") {
        $current_date = $event_date;
        while($current_date <= $time_limit) {
            $out[] = array(
                'id' => $event_id,
                'title' => $event_name,
                'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
                "class" => "event-warning",
                'start' => strtotime($current_date).'000',
                'end' => strtotime($current_date).'000'
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
            'start' => strtotime($event_date).'000',
            'end' => strtotime($event_date) .'000'
        );
    }
}


//maintenance events
$result_maintenance = $DB_calendar->get_personal_office_maintenance_events( "maintenance",$employee_id);
foreach ($result_maintenance as $row ) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    if($event_type == "regular") {
        $current_date = $event_date;
        while($current_date <= $time_limit) {
            $out[] = array(
                'id' => $event_id,
                'title' => $event_name,
                'url' => "custom/calendar_visit/office-event-details.php?" . "&event_id=" . $event_id,
                "class" => "event-important",
                'start' => strtotime($current_date).'000',
                'end' => strtotime($current_date).'000'
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
            'start' => strtotime($event_date).'000',
            'end' => strtotime($event_date) .'000'
        );
    }
}


//visit events
$results_visit = $DB_calendar->get_personal_visit_events_by_employee($employee_id);
foreach ( $results_visit as $row ) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $result = $DB_calendar->get_all_bookings($event_id);
    foreach ( $result as $r ) {
        $out[] = array(
            'id' => $r['id'],
            'title' => $event_name,
            'url' => "custom/calendar_visit/booking-details.php?"."booking_date=".$r['booking_date']."&booking_start=".$r['booking_start']."&event_id=" . $event_id."&booking_end=".$r['booking_end'],
            "class" => "event-info",
            'start' => strtotime($r['booking_date'].' '.$r['booking_start']) . '000',
            'end' => strtotime($r['booking_date'].' '.$r['booking_end']) .'000'
        );
    }
}



// managed employee visit events
$employee_info = $DB_calendar->get_employee_info($employee_id);
$managed_buildings = $employee_info['building_ids'];
$is_admin = $employee_info['admin_id'];

if($is_admin == 1){
    $managed_visit_events = $DB_calendar->get_visit_events_by_admin($employee_id,$managed_buildings);
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


//encode
echo json_encode(array('success' => 1, 'result' => $out));
exit;



