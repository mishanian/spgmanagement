<?php
/**
 * Created by PhpStorm.
 * User: Mehran
 * Date: 10/26/2017
 * Time: 11:33 AM
 */
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$SelectSql = "SELECT monthly_price  FROM apartment_infos APP WHERE APP.apartment_id='".$_GET['a']."'";
$statement = $DB_con->prepare($SelectSql);
$result=$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo $result[0]['monthly_price'];
?>