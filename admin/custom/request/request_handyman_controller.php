<?php
include ("../../../pdo/dbconfig.php");

if($_POST['action'] == 'get_avail_date'){
    $building_id = $_POST['building_id'];
    $request_type_id = $_POST['request_type_id'];

    //handyman is in the responsible employees
    $responsible_employees = $DB_request->get_default_employees($building_id, $request_type_id);
    $employee_ids_array = array();
    foreach($responsible_employees as $one){
        array_push($employee_ids_array, $one['employee_id']);
    }
    $slots = $DB_request->get_avail_date($employee_ids_array, $building_id);
    $feedback =array();
    if(is_null($slots)){
        $feedback['status'] = 'fail';
    }
    else{
        $feedback['status'] = 'success';
        $slots_array = array();
        foreach ($slots as $slot){
            $temp = array();
            $temp['slot_id'] = $slot['id'];
            $temp['date'] = date('Y-m-d', strtotime($slot['slot_date']));
            array_push($slots_array, $temp);
        }
        $feedback['content'] = $slots_array;
    }
    echo json_encode($feedback);
}

if($_POST['action'] == 'get_avail_datetime'){
    $slot_id = $_POST['slot_id'];
    $avail_slot_info = $DB_calendar->get_handyman_avail_info($slot_id);
    $handyman_id = $avail_slot_info['handyman_id'];
    $slot_booking_infos = $DB_calendar->get_handyman_bookings($slot_id);
    $handyman_slot_config = $DB_calendar->get_handyman_config($handyman_id);

    include_once ('handyman_book_event.php');
    $time_slots = get_handyman_avail_datetime($avail_slot_info, $slot_booking_infos, $handyman_slot_config);
    $feedback['status'] = 'success';
    $content['duration'] = $handyman_slot_config['event_duration'];
    $content['slots'] = $time_slots;
    $feedback['content'] = $content;
    echo json_encode($feedback);
}