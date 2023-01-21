<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 2017/6/12
 * Time: pm1:48
 */


class Apt {

    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    public function getAptInfo($id){
        $row = $this->crud->getAptInfo($id);
        return $row;
    }
}