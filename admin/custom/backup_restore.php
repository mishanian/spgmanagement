<?
include_once('../../pdo/dbconfig.php');
$Crud=new CRUD($DB_con);
if(!empty($_GET['p'])) {
    $param=$_GET['p'];
    $table="project";
}
if(!empty($_GET['c'])) {
    $param=$_GET['c'];
    $table="contract";
}
if(!empty($_GET['r'])) {
    $param=$_GET['r'];
    $table="contract";
}
if(!empty($_GET['y'])) {
    $param=$_GET['y'];
    $table="payment";
}
if(!empty($_GET['v'])) {
    $param=$_GET['v'];
    $table="vendor";
}
if(!empty($_GET['a'])) {
    $param=$_GET['a'];
    $table="attachment";
}

if(!empty($_GET['y'])) {
  $fieldId=  "id";
}else{
    $fieldId=  $table."_id";
}


$sql1 = "INSERT INTO ".$table."_infos SELECT DISTINCT * FROM backup_".$table."_infos WHERE ".$fieldId."=". $param;
$sql2 = "DELETE FROM backup_".$table."_infos WHERE ".$fieldId."=" . $param;
//die($sql1."<br>".$sql2);
$Crud->query($sql1);
$Crud->execute();
$Crud->query($sql2);
$Crud->execute();

echo "Restored";
?>