<?php
function get_handyman_avail_datetime($avail_slot_info,$slot_booking_infos,$handyman_slot_config){
    $config_event_duration = $handyman_slot_config['event_duration'];
    $config_max_book_daily = $handyman_slot_config['max_books_daily'];
    $config_avail_increment = $handyman_slot_config['availability_increment'];
    $config_buffer_before = $handyman_slot_config['buffer_before'];
    $config_buffer_after = $handyman_slot_config['buffer_after'];
    $config_book_before = $handyman_slot_config['book_event_before'];

    $slot_start = $avail_slot_info['slot_start_time'];
    $slot_end = $avail_slot_info['slot_end_time'];
    $slot_date = $avail_slot_info['slot_date'];

    //check whether over max book daily
    if(sizeof($slot_booking_infos) == $config_max_book_daily){
        $empty_slots = array();
        return $empty_slots;
    }

    //get all possible time slots
    $all_time_slots = array();
    $temp_start_time = strtotime($slot_start);
    $temp_end_time = strtotime(date("H:i:s", strtotime('+' . $config_event_duration . ' minutes', $temp_start_time)));
    while($temp_end_time <= strtotime($slot_end)){
        array_push($all_time_slots, date("H:i:s", $temp_start_time));
        $temp_start_time = strtotime(date("H:i:s", strtotime('+' . $config_avail_increment . ' minutes', $temp_start_time)));
        $temp_end_time =  strtotime(date("H:i:s", strtotime('+' . $config_event_duration . ' minutes', $temp_start_time)));
    }

    //exclude already booked slots
    $time_slots_after_taken_exclude = array();
    foreach ($all_time_slots as $slot){
        $slot_start_time = strtotime($slot);
        $slot_end_time = strtotime(date("H:i:s", strtotime('+' . $config_event_duration . ' minutes', $slot_start_time)));
        $is_taken = false;
        foreach ($slot_booking_infos as $one_booking){
            $event_start = strtotime($one_booking['book_from']);
            $event_end = strtotime($one_booking['book_to']);
            $event_buffer_start = strtotime(date("H:i:s", strtotime('-' . $config_buffer_before . ' minutes', $event_start)));
            $event_buffer_end = strtotime(date("H:i:s", strtotime('+' . $config_buffer_after . ' minutes', $event_end)));
            if($event_buffer_end > $slot_start_time && $event_buffer_start < $slot_end_time){
                $is_taken = true;
                break;
            }

            if($event_buffer_start >= $slot_start_time && $event_buffer_end <= $slot_end_time){
                $is_taken = true;
                break;
            }

            if($event_buffer_start < $slot_start_time && $event_buffer_end > $slot_start_time){
                $is_taken = true;
                break;
            }
        }

        if(!$is_taken){
            array_push($time_slots_after_taken_exclude, $slot);
        }
    }

    //exclude the slots which the time is over less hours
    $final_time_slots = array();
    $current_time = time();
    $limit_time = strtotime('+' . $config_book_before . ' days', $current_time);
    foreach ($time_slots_after_taken_exclude as $slot){
        $slot_time = strtotime($slot_date . ' ' . $slot);
        if($slot_time >= $limit_time){
            array_push($final_time_slots, $slot);
        }
    }

    return $final_time_slots;
}