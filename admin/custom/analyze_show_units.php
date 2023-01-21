<?php
session_start();
include_once("../../pdo/dbconfig.php");
include_once("../../pdo/Class.Analyze.php");
$DB_analyze = new Analyze($DB_con);
if (isset($_GET['bid'])) {
    $building_id = $_GET["bid"];
} else {
    $building_id = 0;
}
$unit_id_names = $DB_analyze->getAllUnitsIdNameByBid($_SESSION['company_id'], $building_id);
for ($i = 0; $i < count($unit_id_names); $i++) {
    $UnitName = $unit_id_names[$i]['unit_number'];
    $UnitID = $unit_id_names[$i]['apartment_id'];
    echo "<label><input type=checkbox value='$UnitID' name='apartment_id' id='apartment_id' onchange='changeResult()'> $UnitName </label><br>";
} ?>


