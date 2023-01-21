<?php

namespace PHPMaker2023\spgmanagement;

use \PDO;

//======================================================================================================================

$DB_host = "localhost";
$DB_user = "spgmgmt_admin";
$DB_pswd = "no*_Qk+I0KwC";
$DB_name = "spgmgmt_db";


//echo $DB_name;


//======================================================================================================================


//Setting DSN
$dsn = "mysql:host=$DB_host;dbname=$DB_name;";
global $DB_con;
//Setting options
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true
);

//Creating a new PDO instance
try {
    $DB_con = new PDO($dsn, $DB_user, $DB_pswd, $options);
    $DB_con->query("set names utf8");
}
//Catching any error
catch (PDOException $e) {
    echo $e->getMessage();
}

//Including the Functioning class files and initializing the objects
include_once 'Class.Crud.php';