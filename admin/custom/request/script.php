<?php
include_once("../../../pdo/dbconfig.php");
$new_accessed_buildings = $DB_request->get_building_access_for_employee($employee_id);
$original_accessed_buildings = [];

$got_access_buildings = array_diff($new_accessed_buildings, $original_accessed_buildings);
if (sizeof($got_access_buildings) > 0) {
    $DB_request->add_building_access_for_employee($employee_id, $got_access_buildings);
}