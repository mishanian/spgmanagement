<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 2017/6/12
 * Time: pm3:12
 */

class Building {
    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    public function getBdInfo($id){
        $row = $this->crud->getBdInfo($id);
        return $row;
    }
}