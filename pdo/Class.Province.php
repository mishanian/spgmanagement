<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 2017/7/5
 * Time: 下午3:39
 */
class Province
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    //Returns the String of province name.
    public function getProvince($id)
    {
        try {
            $this->crud->query("SELECT * FROM provinces WHERE id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            $province = $row['name'];
            return $province;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getProvinceName($id)
    {
        try {
            $this->crud->query("SELECT province_short_code FROM provinces WHERE id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultField();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the String of province name.
    public function getProvinceInfo($id)
    {
        try {
            $this->crud->query("SELECT * FROM provinces WHERE id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns all the provinces.
    public function getAllProvinces(){
        try{
            $this->crud->query("SELECT * FROM provinces");
            $rows = $this->crud->resultSet();
            return $rows;
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}