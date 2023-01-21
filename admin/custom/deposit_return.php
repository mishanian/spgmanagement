<?
session_start();
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$id=$_GET['id'];
$SelectSql="update deposits set deposit_status=1, return_employee_id=".$_SESSION['employee_id'].", return_date='".date("Y-m-d H:i:s")."' where id=$id";
//die($SelectSql);
global $db;
$statement = $DB_con->prepare($SelectSql);
$result = $statement->execute();
echo "Done";






?>