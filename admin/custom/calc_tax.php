<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
$SelectSql = "SELECT province_id,PRV.tax1,PRV.tax2,PRV.tax3  FROM building_infos BLD LEFT JOIN provinces PRV ON BLD.province_id=PRV.id WHERE BLD.building_id='".$_GET['b']."'";
$statement = $DB_con->prepare($SelectSql);
$result=$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo $result[0]['province_id'].",".$result[0]['tax1'].",".$result[0]['tax2'].",".$result[0]['tax3'];
?>