<?php
if(isset($_POST['update_buildings_settings'])){
    include_once ("../../../pdo/dbconfig.php");
    $building_ids=$_POST['building_ids'];
    $building_arr=explode(',',$building_ids);

    for($i=0;$i<sizeof($building_arr);$i++){
        $building_id=$building_arr[$i];
        //echo $building_id;
        $listing_priority_type=$_POST['listing_priority_type_'.$building_id];
        if(isset($_POST['prioritize_media_'.$building_id]))
            $prioritize_media=1;
        else
            $prioritize_media=0;
        if(isset($_POST['listing_diff_num_room_'.$building_id]))
            $listing_diff_num_room=1;
        else
            $listing_diff_num_room=0;

        $showed_units_count=$_POST['showed_units_count_'.$building_id];
        $employee_id=$_POST['employee_id'];
        $DB_kijiji->update_building_setting($building_id,$listing_priority_type,$prioritize_media,$listing_diff_num_room,$showed_units_count);
    }
    
    header("Location: ../../kijiji_listing.php");
}

if(isset($_POST['save_kijiji_settings'])){
    include_once ("../../../pdo/dbconfig.php");
    $employee_id=$_POST['employee_id'];
    if(isset($_POST['feed_by_price_priority_level']))
        $feed_by_price_priority_level=$_POST['feed_by_price_priority_level'];
    else
        $feed_by_price_priority_level=0;

    $feed_by_price_order_strategy=$_POST['feed_by_price_order_strategy'];


    if(isset($_POST['feed_by_price_switch']))
        $feed_by_price=$feed_by_price_priority_level;
    else
        $feed_by_price=0;

    if(isset($_POST['feed_by_value_priority_level']))
        $feed_by_value_priority_level=$_POST['feed_by_value_priority_level'];
    else
        $feed_by_value_priority_level=0;

    $feed_by_value_order_strategy=$_POST['feed_by_value_order_strategy'];

    if(isset($_POST['feed_by_value_switch']))
        $feed_by_value=$feed_by_value_priority_level;
    else
        $feed_by_value=0;

    if(isset($_POST['carousel_switch']))
        $carousel=1;
    else
        $carousel=0;

    //save the user settings
    $DB_kijiji->save_kijiji_settings($employee_id,$feed_by_price,$feed_by_price_order_strategy,$feed_by_value,$feed_by_value_order_strategy,$carousel);


    header("Location: ../../kijiji_listing.php");
}

//if(isset($_GET['update_feeding_list'])){
//    include_once ("../../../pdo/dbconfig.php");
//    $employee_id = $_GET['update_feeding_list'];
//    update_feeding_list($employee_id);
//    header("Location: ../../kijiji_listing.php");
//}



//function update_feeding_list($employee_id){
//    global $DB_kijiji;
//    //remove old the feeding list
//    $DB_kijiji->remove_all_feeding_list($employee_id);
//    $settings=$DB_kijiji->get_kijiji_settings_by_employee($employee_id);
//    $slots_number = $DB_kijiji->get_available_slots_number($employee_id);
//    $feed_by_price=$settings['feed_by_price'];
//    $feed_by_price_order_strategy=$settings['feed_by_price_order_strategy'];
//    $feed_by_value=$settings['feed_by_value'];
//    $feed_by_value_order_strategy=$settings['feed_by_value_order_strategy'];
//
//    //update new feeding list
//    $DB_kijiji->update_feeding_list($employee_id,$slots_number,$feed_by_price,$feed_by_price_order_strategy,$feed_by_value,$feed_by_value_order_strategy);

//}


