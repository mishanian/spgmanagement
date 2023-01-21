<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

/* * *
 * Get the post Values from the request and update the respective building 
 */

if (isset($_POST["bid"])) {
    $buildingTimeLines = json_encode($_POST);
    $rowCount = $DB_building->updateBuildingTimelines($_POST["bid"], $buildingTimeLines);
    if ($rowCount > 0) {
        echo json_encode(array("value" => "true"));
    }
}   

if ((isset($_GET["action"]) == "fillBuildingTimelines") && (isset($_GET["bid"]))) {
    $buildingTimeLines = $DB_building->getBuildingTimeLines($_GET["bid"]);
    if (!empty($buildingTimeLines)) {
        echo json_encode(array("result" => 1, "value" => $buildingTimeLines));
    } else {
        echo json_encode(array("result" => 0));
    }
}