<?php
/**
 * Created by PhpStorm.
 * User: Wenyu Gu
 * Date: 2017/6/10
 * Time: 5:06pm
 */
include_once 'dbconfig.php';

/*$crud->query('INSERT INTO apartment_infos (id, building_id, floor_id) VALUES (:id, :building_id, :floor_id)');

$crud->bind(':id', '20');
$crud->bind(':building_id', 2);
$crud->bind('floor_id', '8');

$crud->execute();

echo $crud->lastInsertId();*/

//$row = $crud->getAptInfo('2');
//echo $row['unit_number'];
//
//$row = $crud->getBdInfo('4');
//echo $row['building_name'];

$row = $DB_apt->getAptInfo('5');
echo $row['unit_number'];
?>