<?php

class Country{
    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    public function getCountry($id){
        try{
            $this->crud->query("SELECT * FROM countries WHERE id=:id");
            $this->crud->bind(":id", $id);
            return $this->crud->resultSingle()['name'];
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getCountryInfo($id){
        try{
            $this->crud->query("SELECT * FROM countries WHERE id=:id");
            $this->crud->bind(":id", $id);
            return $this->crud->resultSingle();
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getCountryName($id){
        try{
            $this->crud->query("SELECT country_short_code FROM countries WHERE id=:id");
            $this->crud->bind(":id", $id);
            return $this->crud->resultField();
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }


}