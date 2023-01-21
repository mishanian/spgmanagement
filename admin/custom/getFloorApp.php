<?
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
if (!empty($_GET['fid'])) {
$sql = "select apartment_id from apartment_infos WHERE floor_id=" . $_GET['fid'];
//echo $sql;
$result=$Crud->query($sql);
$row=$Crud->resultJson();
echo $row;
}
?>