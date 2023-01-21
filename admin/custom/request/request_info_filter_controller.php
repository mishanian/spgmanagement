<?php
include_once('../../../pdo/dbconfig.php');


if ($_POST['action'] == 'get_units'){
    $building_id = $_POST['building_id'];
    $result = $DB_request->get_unit_lst_in_building($building_id);
    $data_content = array();
    foreach ($result as $row){
        $temp = array();
        $temp['apartment_id'] = $row['apartment_id'];
        $temp['unit_number'] = $row['unit_number'];
        array_push($data_content,$row);
    }

    $feedback = array();
    $feedback['status'] = 'success';
    $feedback['data_content'] = $data_content;

    echo json_encode($feedback);
}


if($_POST['action'] == 'get_tenants_in_building'){
    $building_id = $_POST['building_id'];
}




if($_POST['action'] == 'get_tenants_in_unit'){
    $building_id = $_POST['building_id'];
    $unit_id = $_POST['unit_id'];
}
