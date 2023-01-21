<?php
session_start();

if (isset($_SESSION["company_id"])) {
    $company_id = $_SESSION["company_id"];
}
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$lease_id = $_GET['leaseId'];
$signDT = $_GET['signDate'];

$SelectSql = "update lease_infos set lease_status_id=11, is_signed=1, signDT='$signDT', comments=CONCAT(IFNULL(comments,''),'signed manually on " . date("y-m-d") . " by " . $_SESSION['UserFullName'] . "') where id=$lease_id";
// die($SelectSql);
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
echo "updated.";