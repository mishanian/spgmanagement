<?php

class Feed
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    //------------------------------ feeding xml -------------------------------

    function BuildingType($building_id){
        try {
            $this->crud->query("SELECT mapping1 FROM building_types WHERE id=(select building_type_id from building_infos where building_id=$building_id)");
            $rows = $this->crud->resultField();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }




}
