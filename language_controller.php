<?php

session_start();
$employeeId = $_SESSION['employee_id'];
$companyId = $_SESSION["company_id"];

/**
 * Inclusion of database config file 
 */
$cwd = getcwd();
$cwdArray = explode("/", $cwd);
include_once ("pdo/dbconfig.php");
include_once ('pdo/Class.Snapshot.php');
$DB_snapshot = new Snapshot($DB_con);
$languages = $DB_snapshot->getLanguages();

$language_ids = array();

// Set the languages ID's array 
foreach ($languages as $language) {
    array_push($language_ids, $language["id"]);
}

// Handling the request to change the language.
// This will set the language selected value in the Session to the language selected.
if (isset($_POST['request']) && $_POST['request'] == 'lnChange') {
    if (isset($_POST['ln']) && in_array($_POST['ln'], $language_ids)) {
        $_SESSION["lang_set"] = $_POST['ln'];
        echo json_encode(array("result" => true, "value" => $_SESSION["lang_set"]));
    } else {
        echo json_encode(array("result" => false, "value" => "Could not process the language change request. Please resend the request."));
    }
}