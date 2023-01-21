<?php
/**
 * Created by PhpStorm.
 * User: Mehran
 * Date: 2020/7/3
 */
class Facility{
    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    //Return a String of facility name.
    public function getFacility ($id){
        try{
            $this->crud->query("SELECT * FROM amenities WHERE id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            $name = $row['name'];
            return $name;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }
}