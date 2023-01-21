<?php

include_once 'Class.SizeType.php';
include_once 'Class.Lease.php';
include_once 'Class.Floor.php';

class Apt
{

    private $crud;
    private $DB_size, $DB_lease, $DB_floor;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
        $this->DB_size = new SizeType($DB_con);
        $this->DB_lease = new Lease($DB_con);
        $this->DB_floor = new Floor($DB_con);
    }

    public function getAptInfo($id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE apartment_id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnitNumber($apartment_id)
    {
        return $this->getAptInfo($apartment_id)['unit_number'];
    }

    //Returns all the apartments in one given building.
    public function getAptInfoInBuilding($building_id, $orderby = 'available_date')
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id=:building_id ORDER BY $orderby ASC,FIELD(apartment_status,5,8,9,7,1,2,3,4,6) ");
            $this->crud->bind(":building_id", $building_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns all the apartments in one given building.
    public function getAptInfoInBuildingLessRenewalDay($building_id, $company_id)
    {
        try {
            $this->crud->query("SELECT list_before_days FROM company_infos WHERE id=$company_id");
            $list_before_days = $this->crud->resultField();
            $lessDate = new DateTime(date("Y-m-d"));
            $lessDate = $lessDate->modify("+$list_before_days day")->format("Y-m-d");
            // echo ("Show Before: " . $lessDate);
            $sql = "SELECT * FROM apartment_infos WHERE building_id=:building_id AND (available_date<'$lessDate' OR front_force_list=1 OR apartment_status=9)  ORDER BY available_date ASC,FIELD(apartment_status,5,8,9,7,1,2,3,4,6) ";
            // echo $sql;
            $this->crud->query($sql); //and available_date>CURRENT_DATE()
            $this->crud->bind(":building_id", $building_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns all the apartments in all of company building.
    public function getAptInfoInAllBuildingLessRenewalDay($company_id)
    {
        try {
            $this->crud->query("SELECT list_before_days FROM company_infos WHERE id=$company_id");
            $list_before_days = $this->crud->resultField();
            $lessDate = new DateTime(date("Y-m-d"));
            $lessDate = $lessDate->modify("+$list_before_days day")->format("Y-m-d");
            //echo($list_before_days."-".$lessDate);
            $sql = "SELECT * FROM apartment_infos WHERE building_id in (select building_id from building_infos where company_id=$company_id) AND (available_date<'$lessDate' OR front_force_list=1 OR apartment_status=9) and available_date>CURRENT_DATE() ORDER BY available_date ASC,FIELD(apartment_status,5,8,9,7,1,2,3,4,6) ";
            //    echo $sql;
            $this->crud->query($sql);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }




    //Returns all the apartments in one given building.
    public function getAptInfoInBuildingAptSorted($building_id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id=:building_id ORDER BY unit_number ASC,FIELD(apartment_status,5,8,7,1,2,3,4,6)");
            $this->crud->bind(":building_id", $building_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     *
     * @param int $employee_id
     * @return array
     */
    public function getAllAptsbyEmployeeId($employee_id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE employee_id=:employee_id");
            $this->crud->bind(":employee_id", $employee_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     *
     * @param int $employee_id
     * @return array
     */
    public function getAllApts()
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos");
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }


    public function getAppPhotos($appartment_id)
    {
        try {
            $sql = "SELECT apartment_id, pictures FROM apartment_infos where apartment_id = $appartment_id";
            //        echo $sql;
            $this->crud->query($sql);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }


    /**
     * Returns all the vacant apartments in the building
     * @param int $building_id
     * @return array
     */
    public function getVacantAptInfoInBuilding($building_id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id=:building_id AND apartment_status=:vacant_status");
            $this->crud->bind(":building_id", $building_id);
            $this->crud->bind(":vacant_status", 5);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Fetches the list of vacant apartments in all the buildings in the table
     * @return array
     */
    public function getVacantAptInfoInAllBuildings()
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE apartment_status=:vacant_status");
            $this->crud->bind(":vacant_status", 5);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Filter the result set from apartment
     * @param array $rows
     */
    public function filterByEmployeeId($rows, $employeeId)
    {
        if ($employeeId == "0000") { // Denoting employee ID as ADMIN ID
            return $rows;
        }
        foreach ($rows as $index => $element) {
            if ($element['employee_id'] != $employeeId) {
                unset($rows[$index]);
            }
        }
        return $rows;
    }

    /**
     * Filter the $rows by Company ID
     * @param type $companyId
     * @return type
     */
    public function filterByCompanyId($rows, $companyId)
    {
        foreach ($rows as $index => $element) {
            if ($element['company_id'] != $companyId) {
                unset($rows[$index]);
            }
        }
        return $rows;
    }

    //Returns the first apartment information in the building.
    public function getFirstAptInfoByBID($building_id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id=:building_id");
            $this->crud->bind(':building_id', $building_id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getFirstAptId($building_id)
    {
        try {
            return $this->getFirstAptInfoByBID($building_id)['apartment_id'];
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the monthly price of an apartment.
    public function getMonthlyPrice($apartment_id, $monthText = false)
    {
        try {
            if ($monthly_price = $this->getAptInfo($apartment_id)['monthly_price']) {
                if ($monthText) {
                    return "$" . $monthly_price;
                }
                return "$" . $monthly_price . " /Month";
            } else {
                return "Not Available";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the size of an apartment.
    public function getSize($apartment_id)
    {
        try {
            if ($size = $this->getAptInfo($apartment_id)['area']) {
                return $size . " sq.ft";
            } else {
                return "Not Available";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the lease info of an apartment.
    public function getLeaseStatus($apartment_id)
    {
        try {
            return $this->DB_lease->getLeaseStatus($apartment_id);
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getLeaseLength($apartment_id)
    {
        try {
            return $this->DB_lease->getLeaseLength($apartment_id);
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getMoveInDate($apartment_id)
    {
        try {
            return $this->DB_lease->getMoveInDate($apartment_id);
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the available data of an apartment.
    public function getAvailableDate($apartment_id)
    {
        return $this->getAptInfo($apartment_id)['available_date'];
    }

    //Returns all the available units.
    public function getAllAvailableUnits($company_id)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE (employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = :company_id))");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns true if the building has any unit to show.
    public function isBuildingShowed($building_id)
    {
        $apt_rows = $this->getAptInfoInBuilding($building_id);
        foreach ($apt_rows as $apt_row) {
            if ($this->isUnitShowed($apt_row['apartment_id'])) {
                return true;
            }
        }
        return false;
    }

    //Returns true if the unit should be published.
    public function isUnitShowed($apartment_id)
    {
        // If the Front Force is Yes - show the apartment irrespective of any other condition
        if ($this->isFrontForcedToList($apartment_id)) {
            return true;
        }
        //    echo "<hr>$apartment_id=".var_dump($this->isVacant($apartment_id))."<hr>";
        if ($this->isVacant($apartment_id) || $this->isPendingRenewal($apartment_id) || $this->isUpcomingVacant($apartment_id)) {

            return true;
        } else {
            return false;
        }
    }

    public function isFrontForcedToList($apartment_id)
    {
        if ($this->getAptInfo($apartment_id)['front_force_list']) {
            return true;
        } else {
            return false;
        }
    }

    private function isVacant($apartment_id)
    {
        if ($this->getAptInfo($apartment_id)['apartment_status'] == '5') {
            return true;
        } else {
            return false;
        }
    }

    private function isUpcomingVacant($apartment_id)
    {
        if ($this->getAptInfo($apartment_id)['apartment_status'] == '7' || $this->getAptInfo($apartment_id)['apartment_status'] == '9') {
            return true;
        } else {
            return false;
        }
    }

    private function isPendingRenewal($apartment_id)
    {
        if ($this->getAptInfo($apartment_id)['apartment_status'] == '8') {
            return true;
        } else {
            return false;
        }
    }

    //Calculating the vacant duration before next lease starts.
    public function getDurationBeforeNextLease($apartment_id)
    {
        $this->crud->query("SELECT * FROM lease_infos WHERE apartment_id = :apartment_id AND lease_status_id = 2");
        $this->crud->bind(":apartment_id", $apartment_id);
        if ($this->crud->resultSingle()) {
            return $this->crud->resultSingle()['start_date'];
        } else {
            return "No Upcoming Leases";
        }
    }

    //Returns the size type of an apartment.
    public function getSizeType($apartment_id)
    {
        try {
            if ($size_type_id = $this->getAptInfo($apartment_id)['size_type_id']) {
                return $this->DB_size->getSizeType($size_type_id);
            } else {
                return "Not Applicable.";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the room details of an apartment.
    public function getRoomDetails($apartment_id)
    {
        try {
            $row = $this->getAptInfo($apartment_id);
            $bedrooms = $row['bedrooms'] . " Bedroom(s), ";
            $bathrooms = $row['bath_rooms'] . " Bathroom(s), ";
            return $bedrooms . $bathrooms;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the floor info of an apartment.
    public function getFloorInfo($apartment_id)
    {
        try {
            if ($floor = $this->DB_floor->getFloor($apartment_id)) {
                return $floor;
            } else {
                return "Not Applicable.";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //Returns the amenities info. array format
    public function getAmenities($apartment_id)
    {
        if ($this->getAptInfo($apartment_id)['amenities']) {
            $amenities = array();
            $amenities_arr = explode(",", $this->getAptInfo($apartment_id)['amenities']);
            foreach ($amenities_arr as $amenity_id) {
                array_push($amenities, $this->getAmenity($amenity_id));
            }
            return $amenities;
        } else {
            return null;
        }
    }

    private function getAmenity($amenity_id)
    {
        try {
            $this->crud->query("SELECT * FROM amenities WHERE id=:amenity_id");
            $this->crud->bind(":amenity_id", $amenity_id);
            if ($this->crud->resultSingle()) {
                return $this->crud->resultSingle()['name'];
            } else {
                return "No such amenity.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the comment of an apartment.
    public function getComment($apartment_id)
    {
        try {
            if ($comment = $this->getAptInfo($apartment_id)['comment']) {
                return $comment;
            } else {
                return "No Comments Available.";
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getFeaturePic($apt_id)
    {
        if ($this->getAptInfo($apt_id)['feature_picture']) {
            return "admin/files/apartment_pictures/" . $this->getAptInfo($apt_id)['feature_picture'];
        } else {
            return "admin/files/sample_pic_apt.jpeg";
        }
    }

    public function addAptInfo($id, $building_id, $floor_id, $unit_number, $size_type_id, $area, $bedrooms, $powder_room, $side_facing, $bath_rooms, $dining_room, $monthly_price, $weekly_price, $comment, $status, $available_date, $listing_type, $air_conditioning, $heating, $fire_place, $floor_type, $wifi, $furnished, $feature_picture, $pictures, $video, $amenities, $floor_plan, $public_comments, $coowner_id)
    {
        try {
            $this->crud->query("INSERT INTO apartment_infos(apartment_id,building_id,floor_id,unit_number,size_type_id,area,bedrooms,powder_room,side_facing,bath_rooms,dining_room,monthly_price,weekly_price,comment,status,available_date,listing_type,air_conditioning,heating,fire_place,floor_type,wifi,furnished,feature_picture,pictures,video,amenities,floor_plan,public_comments,coowner_id)
                                      VALUES (:id,:building_id,:floor_id,:unit_number,:size_type_id,:area,:bedrooms,:powder_room,:side_facing,:bath_rooms,:dining_room,:monthly_price,:weekly_price,:comment,:status,:available_date,:listing_type,:air_conditioning,:heating,:fire_place,:floor_type,:wifi,:furnished,:feature_picture,:pictures,:video,:amenities,:floor_plan,:public_comments,:coowner_id)");
            $this->crud->bind(':id', $id);
            $this->crud->bind(':building_id', $building_id);
            $this->crud->bind(':floor_id', $floor_id);
            $this->crud->bind(':unit_number', $unit_number);
            $this->crud->bind(':size_type_id', $size_type_id);
            $this->crud->bind(':area', $area);
            $this->crud->bind(':bedrooms', $bedrooms);
            $this->crud->bind(':powder_room', $powder_room);
            $this->crud->bind(':side_facing', $side_facing);
            $this->crud->bind(':bath_rooms', $bath_rooms);
            $this->crud->bind(':dining_room', $dining_room);
            $this->crud->bind(':monthly_price', $monthly_price);
            $this->crud->bind(':weekly_price', $weekly_price);
            $this->crud->bind(':comment', $comment);
            $this->crud->bind(':status', $status);
            $this->crud->bind(':available_date', $available_date);
            $this->crud->bind(':listing_type', $listing_type);
            $this->crud->bind(':air_conditioning', $air_conditioning);
            $this->crud->bind(':heating', $heating);
            $this->crud->bind(':fire_place', $fire_place);
            $this->crud->bind(':floor_type', $floor_type);
            $this->crud->bind(':wifi', $wifi);
            $this->crud->bind(':furnished', $furnished);
            $this->crud->bind(':feature_picture', $feature_picture);
            $this->crud->bind(':pictures', $pictures);
            $this->crud->bind(':video', $video);
            $this->crud->bind(':amenities', $amenities);
            $this->crud->bind(':floor_plan', $floor_plan);
            $this->crud->bind(':public_comments', $public_comments);
            $this->crud->bind(':coowner_id', $coowner_id);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function editAptInfo($id, $building_id, $floor_id, $unit_number, $size_type_id, $area, $bedrooms, $powder_room, $side_facing, $bath_rooms, $dining_room, $monthly_price, $weekly_price, $comment, $status, $available_date, $listing_type, $air_conditioning, $heating, $fire_place, $floor_type, $wifi, $furnished, $feature_picture, $pictures, $video, $amenities, $floor_plan, $public_comments, $coowner_id)
    {
        try {
            $this->crud->query("UPDATE apartment_infos
                                      SET    building_id=:building_id,          floor_id=:floor_id,
                                             unit_number=:unit_number,          size_type_id=:size_type_id,       area=:area,
                                             bedrooms=:bedrooms,                powder_room=:powder_room,         side_facing=:side_facing,
                                             bath_rooms=:bath_rooms,            dining_room=:dining_room,         monthly_price=:monthly_price,
                                             weekly_price=:weekly_price,        comment=:comment,                 status=:status,
                                             available_date=:available_date,    listing_type=:listing_type,       air_conditioning=:air_conditioning,
                                             heating=:heating,                  fire_place=:fire_place,           floor_type=:floor_type,
                                             wifi=:wifi,                        furnished=:furnished,             feature_picture=:feature_picture,
                                             pictures=:pictures,                video=:video,                     amenities=:amenities,
                                             floor_plan=:floor_plan,            public_comments=:public_comments, coowner_id=:coowner_id
                                      WHERE  apartment_id = :id ");

            $this->crud->bind(':id', $id);
            $this->crud->bind(':building_id', $building_id);
            $this->crud->bind(':floor_id', $floor_id);
            $this->crud->bind(':unit_number', $unit_number);
            $this->crud->bind(':size_type_id', $size_type_id);
            $this->crud->bind(':area', $area);
            $this->crud->bind(':bedrooms', $bedrooms);
            $this->crud->bind(':powder_room', $powder_room);
            $this->crud->bind(':side_facing', $side_facing);
            $this->crud->bind(':bath_rooms', $bath_rooms);
            $this->crud->bind(':dining_room', $dining_room);
            $this->crud->bind(':monthly_price', $monthly_price);
            $this->crud->bind(':weekly_price', $weekly_price);
            $this->crud->bind(':comment', $comment);
            $this->crud->bind(':status', $status);
            $this->crud->bind(':available_date', $available_date);
            $this->crud->bind(':listing_type', $listing_type);
            $this->crud->bind(':air_conditioning', $air_conditioning);
            $this->crud->bind(':heating', $heating);
            $this->crud->bind(':fire_place', $fire_place);
            $this->crud->bind(':floor_type', $floor_type);
            $this->crud->bind(':wifi', $wifi);
            $this->crud->bind(':furnished', $furnished);
            $this->crud->bind(':feature_picture', $feature_picture);
            $this->crud->bind(':pictures', $pictures);
            $this->crud->bind(':video', $video);
            $this->crud->bind(':amenities', $amenities);
            $this->crud->bind(':floor_plan', $floor_plan);
            $this->crud->bind(':public_comments', $public_comments);
            $this->crud->bind(':coowner_id', $coowner_id);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function deleteAptInfo($id)
    {
        try {
            $this->crud->query("DELETE FROM apartment_infos WHERE apartment_id = :id");
            $this->crud->bind(':id', $id);
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getApartmentInfoByFloor($floorId, $bid)
    {
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE floor_id=:floor_id and building_id = :building_id");
            $this->crud->bind(":floor_id", $floorId);
            $this->crud->bind(":building_id", $bid);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function saveUnitMap($unitId, $mapping_area)
    {
        try {
            $this->crud->query("UPDATE apartment_infos SET mapping_area = :mapping_area WHERE apartment_id= :apartment_id");
            $this->crud->bind(":mapping_area", $mapping_area);
            $this->crud->bind(":apartment_id", $unitId);
            $this->crud->execute();
            return $this->crud->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnitPlanImage($apartmentId)
    {
        try {
            $this->crud->query("SELECT mapping_picture FROM apartment_infos WHERE apartment_id = :apartment_id");
            $this->crud->bind(":apartment_id", $apartmentId);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Fetch the mapping picture and the mapping area for a specified apartment_id
    public function getMappingData($apartmentId)
    {
        try {
            $this->crud->query("SELECT mapping_picture,mapping_area FROM apartment_infos WHERE apartment_id = :apartment_id");
            $this->crud->bind(":apartment_id", $apartmentId);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}