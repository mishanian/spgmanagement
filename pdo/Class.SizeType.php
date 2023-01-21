<?php

class SizeType
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    //Return the String of the size type (e.g. 1 ½ Studio)
    public function getSizeType($id)
    {
        try {
            $this->crud->query("SELECT * FROM size_types WHERE id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            if($size_type = $row['name']){
                return $size_type;
            }else{
                return "No Such Size Type";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns all the size types.
    public function getAllSizeType(){
        try{
            $this->crud->query("SELECT * FROM size_types");
            $rows = $this->crud->resultSet();
            $fixed_rows = array();
            foreach ($rows as $row){
                $name = $row['name'];
                array_push($fixed_rows, ['id'=>$row['id'], 'name'=>$name]);
            }
            return $fixed_rows;
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}