<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$building_id = $_GET['bid'];
$rate = $_GET['r'];
if (empty($building_id)) {
    die("wrong id");
}
$SelectSql = "UPDATE apartment_infos AI 
SET 
monthly_price=ROUND(ROUND(((SELECT DISTINCT lease_amount FROM lease_infos WHERE lease_status_id IN (1,7,8,9,10) AND apartment_id=AI.apartment_id)*$rate)/5,0)*5,0), 
weekly_price=ROUND(ROUND(((SELECT DISTINCT lease_amount FROM lease_infos WHERE lease_status_id IN (1,7,8,9,10) AND apartment_id=AI.apartment_id)*$rate/4)/5,0)*5,0) WHERE building_id=$building_id
        
       ";
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
?>Done