<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
$user_ids = [];

if (empty($_GET['uid'])) {
   die("Error");
} else{
    $uid=$_GET['uid'];
}


/********************** Employee */
$sql = "select employee_id, full_name, mobile, email, picture from employee_infos where employee_id=$uid order by full_name";
// die($sql);
$result = $Crud->query($sql);
$user_ids= $Crud->resultSet();

$json = array("u" => $user_ids);
echo json_encode($json);
