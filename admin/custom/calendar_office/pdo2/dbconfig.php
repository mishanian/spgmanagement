<?php
/**
 * Created by PhpStorm.
 * User: Wenyu Gu
 * Date: 2017/6/10
 * Time: 4:25pm
 */

$DB_host = "localhost";
$DB_user = "iliveinx_admin";
$DB_pswd = "mZf@0v%AoE^m";
$DB_name = "iliveinx_property";

//Setting DSN
$dsn = "mysql:host=$DB_host;dbname=$DB_name;";

//Setting options
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
);

//Creating a new PDO instance
try{
    $DB_con = new PDO($dsn, $DB_user, $DB_pswd, $options);
}

//Catching any error
catch (PDOException $e){
    echo $e->getMessage();
}

//Including the Functioning class files and initializing the objects
include_once 'Class.Crud.php';

include_once 'Class.Apt.php';
$DB_apt = new Apt($DB_con);

include_once 'Class.Building.php';
$DB_building = new Building($DB_con);

//-------------------------------------------------
include_once 'Class.Event.php';
$DB_event = new Event($DB_con);
?>