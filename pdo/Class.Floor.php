<?php


Class Floor {

    private $crud;

    public function __construct($DB_con) {
        $this->crud = new Crud($DB_con);
    }

//    public function updateUnitNumbers() {
//        try {
//            $this->crud->query("SELECT unit_number,apartment_id FROM apartment_infos WHERE unit_number LIKE '%-%'");
//            $unitNumbers = $this->crud->resultSet();
//            echo  "<pre>"; print_r($unitNumbers);
//            foreach($unitNumbers as $index => $unitNum){
//                $broken = explode("-",$unitNum["unit_number"]);
//                $this->crud->query("UPDATE apartment_infos SET unit_number = :unit_number WHERE apartment_id = :aptid and building_id != 3");
//                $this->crud->bind(":unit_number", $broken[1]);
//                $this->crud->bind(":aptid", $unitNum["apartment_id"]);
//                $this->crud->execute();
//                echo $index;
//                echo $broken[1]." : ".$this->crud->rowCount().PHP_EOL;
//            }
//        } catch (PDOException $e) {
//            echo $e->getMessage();
//        }
//    }

    public function getFloor($apartment_id) {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE apartment_id=:id");
            $this->crud->bind(":id", $apartment_id);
            $floor_id = $this->crud->resultSingle()['floor_id'];
            //$floor_type = $this->getFloorType($floor_id);
            $floor_name = $this->getFloorName($floor_id);
            return $floor_name;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

	public function getFloorInfo($apartment_id) {
		try {
			$this->crud->query("SELECT * FROM apartment_infos WHERE apartment_id=:id");
			$this->crud->bind(":id", $apartment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

    private function getFloorName($floor_id) {
        $this->crud->query("SELECT * FROM floor_infos WHERE floor_id=:id");
        $this->crud->bind(":id", $floor_id);
        $row = $this->crud->resultSingle();
        return $row['floor_name'];
    }

    private function getFloorType($floor_id) {
        try {
            $this->crud->query("SELECT * FROM floor_infos WHERE id=:id");
            $this->crud->bind(":id", $floor_id);
            $row = $this->crud->resultSingle();
            return $row['floor_type_id'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBuildingId($floorId) {
        try {
            $this->crud->query("SELECT building_id FROM floor_infos WHERE floor_id=:floor_id");
            $this->crud->bind(":floor_id", $floorId);
            $row = $this->crud->resultSingle();
            return $row['building_id'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns a floor_type name. e.g. Floor 16
    private function getFloorNameFromType($floor_type_id) {
        try {
            $this->crud->query("SELECT * FROM floor_types WHERE id=:id");
            $this->crud->bind(":id", $floor_type_id);
            if ($floor_name = $this->crud->resultSingle()['name']) {
                return $floor_name;
            } else {
                return "No Floor Info Found from Table 'floor_types'.";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*     * *
     * Fetch all the floors details in a building.
     */

    public function getAllFloorInfos($buildingId) {
        try {
            $this->crud->query("SELECT * FROM floor_infos where building_id = :building_id");
            $this->crud->bind(":building_id", $buildingId);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function saveFloorMaps($floorId, $mapping_area) {
        try {
            $this->crud->query("UPDATE floor_infos SET mapping_area = :mapping_area WHERE floor_id = :floor_id");
            $this->crud->bind(":mapping_area", $mapping_area);
            $this->crud->bind(":floor_id", $floorId);
            $this->crud->execute();
            return $this->crud->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function saveFloorUnitsMaps($floorId, $mapping_area) {
        try {
            $this->crud->query("UPDATE floor_infos SET mapping_area = :mapping_area WHERE floor_id = :floor_id");
            $this->crud->bind(":mapping_area", $mapping_area);
            $this->crud->bind(":floor_id", $floorId);
            $this->crud->execute();
            return $this->crud->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getFloorPlanImage($floorId) {
        try {
            $this->crud->query("SELECT mapping_picture FROM floor_infos WHERE floor_id = :floor_id");
            $this->crud->bind(":floor_id", $floorId);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Fetch the mapping picture and the mapping area for a specified floor_id
    public function getMappingData($floorId) {
        try {
            $this->crud->query("SELECT mapping_picture,mapping_area FROM floor_infos WHERE floor_id = :floor_id");
            $this->crud->bind(":floor_id", $floorId);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}
