<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
$headers = apache_request_headers();
// print_r($headers);
$ApiKey=(!empty($headers['Spg-Api-Key']))?$headers['Spg-Api-Key']:'';
if ((!empty($_POST['u']) && !empty($_POST['p'])) && $ApiKey=='448a078b-47bd-4c2e-b924-8ff827de664d') {
    $sql = "select user_id, full_name, username, userpass, user_level, email, mobile, company_id, picture from userlist where username='".$_POST['u']."' and userpass='".$_POST['p']."'";
    $result = $Crud->query($sql);
    $user_info= $Crud->resultSingle();
    if (empty($user_info)){
        http_response_code(404);
        die("Wrong Username or Password");}
    echo json_encode($user_info);
}else{
    http_response_code(404);
    die("Authentication not defined with key=".$ApiKey);
}