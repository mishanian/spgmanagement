<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$SelectSql = "SELECT monthly_price  FROM parking_unit_infos PUI WHERE PUI.parking_id='".$_GET['a']."'";
$statement = $DB_con->prepare($SelectSql);
$result=$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo $result[0]['monthly_price'];
?>