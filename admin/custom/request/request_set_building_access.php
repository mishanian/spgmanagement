<?php

namespace PHPMaker2023\spgmanagement;

use \Request;

ini_set('display_errors', 'on');
ini_set('log_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('error_reporting', E_ALL);


/**
 * this method should be invoked before doing buildings accessibility modification
 */
function prep_change_request_building_access_for_employee($employee_id)
{
    global $DB_request;
    //put this param into background php, useful for the method below

    //avoid redeclaration
    if ($DB_request == null) {
        include('../pdo/dbconfig.php');
        if (!class_exists('Request')) {
            include_once('../pdo/Class.Request.php');
        }
        $DB_request = new Request($DB_con);
    }
    // die(var_dump($DB_request->get_building_access_for_employee($employee_id)));

    $original_accessed_buildings = $DB_request->get_building_access_for_employee($employee_id);

    $_SESSION['original_accessed_buildings'] = $original_accessed_buildings;
}


/**
 * this method should be invoked after doing buildings accessibility modification
 */
function done_change_request_building_access_for_employee($employee_id)
{
    global $DB_request;

    $original_accessed_buildings = $_SESSION['original_accessed_buildings'];

    //avoid redeclaration
    if ($DB_request == null) {
        include('../pdo/dbconfig.php');
        include_once('../pdo/Class.Request.php');
        $DB_request = new Request($DB_con);
    }


    $new_accessed_buildings = $DB_request->get_building_access_for_employee($employee_id);


    $original_accessed_buildings = explode(',', $original_accessed_buildings);
    $new_accessed_buildings = explode(',', $new_accessed_buildings);


    //first step: delete the buildings access
    $lost_access_buldings = array_diff($original_accessed_buildings, $new_accessed_buildings);
    if (sizeof($lost_access_buldings) > 0) {
        $DB_request->delete_building_access_for_employee($employee_id, $lost_access_buldings);
    }

    // second step: add the got buildings access
    $got_access_buildings = array_diff($new_accessed_buildings, $original_accessed_buildings);
    if (sizeof($got_access_buildings) > 0) {
        $DB_request->add_building_access_for_employee($employee_id, $got_access_buildings);
    }


    ////------------ debug ----------
    //    $orig_str = implode(',',$original_accessed_buildings);
    //    $new_str = implode(',',$new_accessed_buildings);
    //    $lost_diff = implode(',',$lost_access_buldings);
    //    $got_diff = implode(',',$got_access_buildings);
    //
    //    die($orig_str.'||'.$new_str.'|||'.$lost_diff.'|||'.$got_diff);

}


/**
 * this method should be invoked when adding a new employee
 */
function add_request_building_access_for_new_employee($employee_id)
{
    global $DB_request;

    //avoid redeclaration
    if ($DB_request == null) {
        include('../pdo/dbconfig.php');
        include_once('../pdo/Class.Request.php');
        $DB_request = new Request($DB_con);
    }
    $accessed_buildings = $DB_request->get_building_access_for_employee($employee_id);

    if (is_array($accessed_buildings) && sizeof($accessed_buildings) > 0) {
        $DB_request->add_building_access_for_employee($employee_id, $accessed_buildings);
    }
}
