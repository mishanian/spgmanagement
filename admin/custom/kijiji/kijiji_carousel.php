<?php
include("../../pdo/dbconfig.php");
include_once("../../pdo/Class.Kijiji.php");
$DB_kijiji = new Kijiji($DB_con);
// include_once("../pdo/Class.Request.php");
// $DB_request = new Request($DB_con);
$carousels = $DB_kijiji->get_carousel_infos();
foreach ($carousels as $row) {
    $employee_id = $row['employee_id'];
    $c_index = $row['c_index'];

    //echo('c_index'.$c_index.'\n');

    //update candidate(important)
    $DB_kijiji->update_candidates($employee_id);

    $available_slots = $DB_kijiji->get_available_slots_number($employee_id);
    $candidates_count = $DB_kijiji->get_candidates_count($employee_id);
    //echo ('candidate_count'.$candidates_count.'\n');



    //remove old feeding list
    $DB_kijiji->remove_all_feeding_list($employee_id);
    //add new feeding list
    if ($c_index + $available_slots <= $candidates_count)
        $DB_kijiji->carousel_update_feeding($employee_id, $c_index, $available_slots);
    else {
        $DB_kijiji->carousel_update_feeding($employee_id, $c_index, $available_slots);
        $DB_kijiji->carousel_update_feeding($employee_id, 0, ($available_slots - $candidates_count + $c_index));
    }
    //update settings
    $new_c_index = ($c_index + $available_slots) % $candidates_count;


    $DB_kijiji->update_c_index($employee_id, $new_c_index);
}

echo ("<br>");
echo ('Kijiji-carousel have run successfully');
echo ("<br>");