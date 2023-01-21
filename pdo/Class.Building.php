<?php

use PHPMaker2019\spgmanagement\facilities;

class Building {

    private $crud;
    private $DB_country, $DB_province, $DB_facility, $DB_company;

    public function __construct($DB_con) {
        $this->crud = new Crud($DB_con);
//        $this->DB_country = new Country($DB_con);
//        $this->DB_province = new Province($DB_con);
        include_once ('Class.Facility.php');
        include_once ('Class.Company.php');
        include_once ('Class.Apt.php');
        $this->DB_facility = new Facility($DB_con);
        $this->DB_company = new Company($DB_con);
        $this->DB_apt = new Apt($DB_con);
    }
    // public function getBdInfo($id) {
    //     $row = $this->crud->getBdInfo($id);
    //     return $row;
    // }

    public function getBdInfo($id){

        try{

            $this->crud->query("SELECT * FROM building_infos WHERE building_id=:id");

            $this->crud->bind(':id', $id);

            $row = $this->crud->resultSingle();

            return $row;

        }

        catch (PDOException $e){

            echo $e->getMessage();

        }

    }


    public function getBdInfoWORules($id) {
        $this->crud->query("SELECT building_name,longitude,latitude,featured FROM building_infos WHERE building_id=:id");
        $this->crud->bind(':id', $id);
        $row = $this->crud->resultSingle();
        return $row;
    }


    public function getFeaturedBds() {
        $this->crud->query("SELECT * FROM building_infos WHERE featured = 1");
        return $this->crud->resultSet();
    }

    public function getAmenityNamesBds($building_id) {
        $sql="SELECT `name` FROM amenities WHERE FIND_IN_SET(id, (SELECT amenity_general_ids FROM building_infos WHERE building_id = $building_id))";
    //    echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultArray();
    }

    public function getAmenityResNamesBds($building_id) {
        $sql="SELECT `name` FROM amenities WHERE FIND_IN_SET(id, (SELECT amenity_residential_ids FROM building_infos WHERE building_id = $building_id))";
        //    echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultArray();
    }

    public function getSmallPetAllowed($building_id) {
        $sql="SELECT COUNT(*) AS number FROM building_infos WHERE building_id=$building_id AND FIND_IN_SET(40,amenity_residential_ids)";
        //    echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultField();
    }

    public function getLargeDogAllowed($building_id) {
        $sql="SELECT COUNT(*) AS number FROM building_infos WHERE building_id=$building_id AND FIND_IN_SET(41,amenity_residential_ids)";
        //    echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultField();
    }

    public function getPetAllowed($building_id) {
        $sql="SELECT COUNT(*) AS number FROM building_infos WHERE building_id=$building_id AND (FIND_IN_SET(40,amenity_residential_ids) OR FIND_IN_SET(41,amenity_residential_ids))";
        //    echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultField();
    }

    //Returns the name of the building. If no buildings found, returns a String.
    public function getBdName($id) {
        try {
            if ($row = $this->getBdInfo($id)) {
                $name = $row['building_name'];
            } else {
                $name = "-";
            }
            return $name;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getFullAddress($id) {
        $row = $this->getBdInfo($id);
//        $country = $this->DB_country->getCountry($row['county_id']);
//        $province = $this->DB_province->getProvince($row['province_id']);
//        return $row['address'].", ".$row['city'].", ".$province.", ".$country.", ".$row['postal_code'];
        return $row['address'];
    }

    //Returns the next/previous building id if any. If there's none, returns the original id.
    public function getNextBdId($id, $company_id) {
        if ($row = $this->getNextBdRow($id, $company_id)) {
            $id = $row['building_id'];
        } else {
            $id = $this->getFirstBdRow($company_id)['building_id'];
        }
        return $id;
    }

    public function getPreviousBdId($id, $company_id) {
        try {
            if ($row = $this->getPreviousBdRow($id, $company_id)) {
                $id = $row['building_id'];
            } else {
                $id = $this->getLastBdRow($company_id)['building_id'];
            }
            return $id;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the next building's name if any. If there's none, return the first building's name.
    public function getNextBdName($id, $company_id) {
        if ($row = $this->getNextBdRow($id, $company_id)) {
            return $row['building_name'];
        } else {
            return $this->getFirstBdRow($company_id)['building_name'];
        }
    }

    //Returns the previous building's name if any. If there's none, return the last building's name.
    public function getPreviousBdName($id, $company_id) {
        if ($row = $this->getPreviousBdRow($id, $company_id)) {
            return $row['building_name'];
        } else {
            return $this->getLastBdRow($company_id)['building_name'];
        }
    }

    //getting the next/previous building row
    public function getNextBdRow($id, $company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE building_id = (SELECT MIN(building_id) FROM building_infos WHERE building_id>:id AND employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = :company_id))");
            $this->crud->bind(":id", $id);
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function getPreviousBdRow($id, $company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE building_id = (SELECT MAX(building_id) FROM building_infos WHERE building_id<:id AND employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = :company_id))");
            $this->crud->bind(":id", $id);
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //getting the first/last building row
    private function getFirstBdRow($company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE building_id = (SELECT MIN(building_id) FROM building_infos WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = :company_id))");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function getLastBdRow($company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE building_id = (SELECT MAX(building_id) FROM building_infos WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = :company_id))");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getMaxPrice($building_id) {
        try {
            $this->crud->query("SELECT max(monthly_price) FROM apartment_infos WHERE building_id=:building_id AND (apartment_status=7 OR apartment_status=5 OR apartment_status=8)");
            $this->crud->bind(":building_id", $building_id);
            $result = $this->crud->resultSingle();
            $max_price = 0;
            if ($result['max(monthly_price)']) {
                $max_price = $result['max(monthly_price)'];
            }
            return $max_price;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getMinPrice($building_id) {
        try {
            $this->crud->query("SELECT min(monthly_price) FROM apartment_infos WHERE building_id=:building_id AND (apartment_status=7 OR apartment_status=5 OR apartment_status=8)");
            $this->crud->bind(":building_id", $building_id);
            $result = $this->crud->resultSingle();
            $min_price = 0;
            if ($result['min(monthly_price)']) {
                $min_price = $result['min(monthly_price)'];
            }
            return $min_price;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

public function getPriceRangeOld($building_id) {
        try {
            $price_max = $this->getMaxPrice($building_id);
            $price_min = $this->getMinPrice($building_id);
            if ($price_max == 0 && $price_min == 0) {
                return "Price range not available"; //Yuhong: changed from "No Price Available"
            } else {
                return "$" . $price_min . "~" . "$" . $price_max . "/month";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getPriceRange($building_id, $company_id) {
        try {
    $apt_rows = $this->DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id);
    $price_max=0;
    $price_min=10000000;
    foreach ($apt_rows as $apt_row) {
        // echo $apt_row['unit_number']."--".$price_max."--<br>";
        if ($this->DB_apt->isUnitShowed($apt_row['apartment_id'])) {
            if (!$apt_row["front_force_list"]) {
                if (in_array($apt_row['renovation_status'], array(2, 3))) {
                    continue;
                }
            }
            if ($apt_row['monthly_price']>$price_max) {
                $price_max=$apt_row['monthly_price'];
                // echo $apt_row['unit_number']."--".$price_max."--<br>";
            }
            if ($apt_row['monthly_price']<$price_min) {
                $price_min=$apt_row['monthly_price'];
                // echo $apt_row['unit_number']."--".$price_max."--<br>";
            }
        }
    }
    if ($price_max == 0 && $price_min == 0) {
        $price_range= "Price range not available"; //Yuhong: changed from "No Price Available"
    } else {
        $price_range= "$" . $price_min . "~" . "$" . $price_max . "/month";
    }
    return  $price_range;
} catch (PDOException $e) {
    echo $e->getMessage();
}
}
    //Returns the url of the feature picture. If none, returns the url of the sample pic.
    public function getFeaturePic($building_id) {
        try {
            if ($feature_pic = $this->getBdInfo($building_id)['feature_picture']) {
                return "admin/files/building_pictures/" . $feature_pic;
            } else {
                return "images/listings/sample_pic.jpg";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllBdRows() {
        try {
            $this->crud->query("SELECT * FROM building_infos");
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllBdRowsByEmployee($employee_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE employee_id = :employee_id");
            $this->crud->bind(":employee_id", $employee_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $building_Ids
     * @return mixed
     */
    public function getBuildingsByIds($building_Ids) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE building_id in ($building_Ids)");
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllBdRowsByCompany($company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE company_id = :company_id ORDER BY building_id");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllBdRowsByCompanyWithHide($company_id) {
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE hide_in_frontend=0 and company_id = :company_id ORDER BY building_id");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function getAllBdIdsByCompany($company_id) {
        try {
            $this->crud->query("SELECT GROUP_CONCAT(building_id) FROM building_infos WHERE company_id = :company_id ORDER BY building_id");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the rules of this building
    public function getBdRules($id) {
        try {
            $row = $this->getBdInfo($id);
            if ($rules = $row['rules']) {
                return $rules;
            } else {
                return "No Conditions of Use Available.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the number of given type buildings
    public function getNumOfTypedBd($building_type, $employee_id) {

        try {
            $this->crud->query("SELECT * FROM building_types WHERE name=:name");
            $this->crud->bind(":name", $building_type);
            $row = $this->crud->resultSingle();
            $type_id = $row['id'];

            $this->crud->query("SELECT * FROM building_infos WHERE building_type_id = :type_id AND employee_id = :employee_id");
            $this->crud->bind(":type_id", $type_id);
            $this->crud->bind(":employee_id", $employee_id);
            $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllFacilities($building_id) {
        global $DB_con;
        $facility_ids_array = $this->getBdInfo($building_id);

        $facility_ids = $facility_ids_array['amenity_general_ids'];
        //var_dump($facility_ids);
        $facility_arr = explode(",", $facility_ids);
        $facilities = array();
        $this->DB_facility = new Facility($DB_con);
        foreach ($facility_arr as $facility_id) {
            $facility = $this->DB_facility->getFacility($facility_id);
            array_push($facilities, $facility);
        }
        return $facilities;
    }

    public function getImportantFacilities($building_id) {
        $facility_ids_array = $this->getBdInfo($building_id);
        $facility_ids = $facility_ids_array['amenity_general_ids'];
        $facility_arr = explode(",", $facility_ids);
        $facilities = array();
        foreach ($facility_arr as $facility_id) {
            if ($facility_id == '1' || $facility_id == '5' || $facility_id == '7' || $facility_id == '9' || $facility_id == '9' || $facility_id == '11' || $facility_id == '13' || $facility_id == '2') {
                $facility = $this->DB_facility->getFacility($facility_id);
                array_push($facilities, $facility);
            }
        }
        return $facilities;
    }

    public function parkingAvailability($building_id) {
        try {
            $this->crud->query("SELECT * FROM parking_unit_infos WHERE building_id=:building_id");
            $this->crud->bind(":building_id", $building_id);
            if ($this->crud->resultSingle()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * 
     * Total Parking slots available in the building - doesnt indicate vacancy
     * @param type $building_id
     * @return 
     */
    public function parkingAvailableSlots($building_id = false) {
        try {
            if ($building_id) {
                $this->crud->query("SELECT * FROM parking_unit_infos WHERE building_id=:building_id");
                $this->crud->bind(":building_id", $building_id);
            } else {
                $this->crud->query("SELECT * FROM parking_unit_infos");
            }
            if ($rows = $this->crud->resultSet()) {
                return $rows;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * 
     * Vacant parking slots in the building
     * @param type $building_id
     * @return boolean
     */
    public function parkingVacantSlots($building_id = false) {
        try {
            if ($building_id) {
                $this->crud->query("SELECT * FROM parking_unit_infos WHERE building_id=:building_id AND status_id=:status_id");
                $this->crud->bind(":building_id", $building_id);
            } else {
                $this->crud->query("SELECT * FROM parking_unit_infos WHERE status_id=:status_id");
            }
            $this->crud->bind(":status_id", 1);
            if ($rows = $this->crud->resultSet()) {
                return $rows;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * 
     * Available storage slots in the building : doesn't indicate vacant slots 
     * @param type $building_id
     * @return boolean
     */
    public function storageAvailableSlots($building_id = false) {
        try {
            if ($building_id) {
                $this->crud->query("SELECT * FROM storage_unit_infos WHERE building_id=:building_id");
                $this->crud->bind(":building_id", $building_id);
            } else {
                $this->crud->query("SELECT * FROM storage_unit_infos");
            }
            if ($rows = $this->crud->resultSet()) {
                return $rows;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * 
     * Vacant storage slots in the building 
     * @param type $building_id
     * @return boolean
     */
    public function storageVacantSlots($building_id = false) {
        try {
            if ($building_id) {
                $this->crud->query("SELECT * FROM storage_unit_infos WHERE building_id=:building_id AND status_id=:status_id");
                $this->crud->bind(":building_id", $building_id);
            } else {
                $this->crud->query("SELECT * FROM storage_unit_infos WHERE status_id=:status_id");
            }
            $this->crud->bind(":status_id", 1);
            if ($rows = $this->crud->resultSet()) {
                return $rows;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function storageAvailability($building_id) {
        try {
            $this->crud->query("SELECT * FROM storage_unit_infos WHERE building_id=:building_id");
            $this->crud->bind(":building_id", $building_id);
            if ($this->crud->resultSingle()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function addBdInfo($id, $building_type_id, $building_name, $address, $country_id, $province_id, $city, $postal_code, $physical_address, $physical_country, $physical_province, $physical_city, $physical_postal_code, $latitude, $longitude, $google_maps, $rules, $rules_fr, $facilities_id, $building_currency, $building_timezone, $daylight_savingtime, $feature_picture, $pictures, $employee_id, $owner_id, $manager_id) {
        try {
            $this->crud->query("INSERT INTO building_infos(building_id,building_type_id,building_name,address,county_id,province_id,city,postal_code,physical_address,physical_country,physical_province,physical_city,physical_postal_code,latitude,longitude,google_maps,rules,rules_fr,facilities_id,building_currency,building_timezone,daylight_savingtime,feature_picture,pictures,employee_id,owner_id,manager_id) VALUES (:id, :building_type_id, :building_name, :address, :country_id, :province_id, :city, :postal_code, :physical_address, :physical_country, :physical_province, :physical_city, :physical_postal_code, :latitude, :longitude, :google_maps, :rules, :rules_fr, :facilities_id, :building_currency, :building_timezone, :daylight_savingtime, :feature_picture, :pictures, :employee_id, :owner_id, :manager_id)");
            $this->crud->bind(":id", $id);
            $this->crud->bind(":building_type_id", $building_type_id);
            $this->crud->bind(":building_name", $building_name);
            $this->crud->bind(":address", $address);
            $this->crud->bind(":country_id", $country_id);
            $this->crud->bind(":province_id", $province_id);
            $this->crud->bind(":city", $city);
            $this->crud->bind(":postal_code", $postal_code);
            $this->crud->bind(":physical_address", $physical_address);
            $this->crud->bind(":physical_country", $physical_country);
            $this->crud->bind(":physical_province", $physical_province);
            $this->crud->bind(":physical_city", $physical_city);
            $this->crud->bind(":physical_postal_code", $physical_postal_code);
            $this->crud->bind(":latitude", $latitude);
            $this->crud->bind(":longitude", $longitude);
            $this->crud->bind(":google_maps", $google_maps);
            $this->crud->bind(":rules", $rules);
            $this->crud->bind(":rules_fr", $rules_fr);
            $this->crud->bind(":facilities_id", $facilities_id);
            $this->crud->bind(":building_currency", $building_currency);
            $this->crud->bind(":building_timezone", $building_timezone);
            $this->crud->bind(":daylight_savingtime", $daylight_savingtime);
            $this->crud->bind(":feature_picture", $feature_picture);
            $this->crud->bind(":pictures", $pictures);
            $this->crud->bind(":employee_id", $employee_id);
            $this->crud->bind(":owner_id", $owner_id);
            $this->crud->bind(":manager_id", $manager_id);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function editBdInfo($id, $building_type_id, $building_name, $address, $country_id, $province_id, $city, $postal_code, $physical_address, $physical_country, $physical_province, $physical_city, $physical_postal_code, $latitude, $longitude, $google_maps, $rules, $rules_fr, $facilities_id, $building_currency, $building_timezone, $daylight_savingtime, $feature_picture, $pictures, $employee_id, $owner_id, $manager_id) {
        try {
            $this->crud->query("UPDATE  building_infos 
                                      SET     building_type_id=:building_type_id,     building_name=:building_name,           address=:address,
                                              county_id=:country_id,                  province_id=:province_id,               city=:city,
                                              postal_code=:postal_code,               physical_address=:physical_address,     physical_country=:physical_country, 
                                              physical_province=:physical_province,   physical_city=:physical_city,           physical_postal_code=:physical_postal_code,
                                              latitude=:latitude,                     longitude=:longitude,                   google_maps=:google_maps,
                                              rules=:rules,                           rules_fr=:rules_fr,                     facilities_id=:facilities_id,
                                              building_currency=:building_currency,   building_timezone=:building_timezone,   daylight_savingtime=:daylight_savingtime,
                                              feature_picture=:feature_picture,       pictures=:pictures,                     employee_id=:employee_id,
                                              owner_id=:owner_id,                     manager_id=:manager_id 
                                      WHERE   building_id = :id  ");
            $this->crud->bind(":id", $id);
            $this->crud->bind(":building_type_id", $building_type_id);
            $this->crud->bind(":building_name", $building_name);
            $this->crud->bind(":address", $address);
            $this->crud->bind(":country_id", $country_id);
            $this->crud->bind(":province_id", $province_id);
            $this->crud->bind(":city", $city);
            $this->crud->bind(":postal_code", $postal_code);
            $this->crud->bind(":physical_address", $physical_address);
            $this->crud->bind(":physical_country", $physical_country);
            $this->crud->bind(":physical_province", $physical_province);
            $this->crud->bind(":physical_city", $physical_city);
            $this->crud->bind(":physical_postal_code", $physical_postal_code);
            $this->crud->bind(":latitude", $latitude);
            $this->crud->bind(":longitude", $longitude);
            $this->crud->bind(":google_maps", $google_maps);
            $this->crud->bind(":rules", $rules);
            $this->crud->bind(":rules_fr", $rules_fr);
            $this->crud->bind(":facilities_id", $facilities_id);
            $this->crud->bind(":building_currency", $building_currency);
            $this->crud->bind(":building_timezone", $building_timezone);
            $this->crud->bind(":daylight_savingtime", $daylight_savingtime);
            $this->crud->bind(":feature_picture", $feature_picture);
            $this->crud->bind(":pictures", $pictures);
            $this->crud->bind(":employee_id", $employee_id);
            $this->crud->bind(":owner_id", $owner_id);
            $this->crud->bind(":manager_id", $manager_id);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function deleteBdInfo($id) {
        try {
            $this->crud->query("DELETE FROM building_infos WHERE building_id=:id");
            $this->crud->bind(':id', $id);
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //get the type name
    public function getBuildingTypeName($building_type_id) {
        try {
            $this->crud->query("SELECT * FROM building_types WHERE building_id=:id");
            $this->crud->bind(":id", $building_type_id);
            return $this->crud->resultSingle()['name'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //---------------------------------------------------------------------------------------------
    //----------------------------- building overview (2018.1.2) ----------------------------------
    //---------------------------------------------------------------------------------------------

    public function getFloorsForOneBuilding($building_id) {
        try {
            $this->crud->query("SELECT floor_id,floor_name FROM floor_infos WHERE building_id = :building_id");
            $this->crud->bind(":building_id", $building_id);

            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnitsForOneFloor($floor_id) {
        try {
            $this->crud->query("SELECT apartment_id,unit_number,monthly_price,apartment_status,
                                        (SELECT start_date FROM lease_infos WHERE lease_infos.apartment_id = apartment_infos.apartment_id  AND lease_status_id IN (1,7,8,9)) AS lease_start,
                                        (SELECT end_date FROM lease_infos WHERE lease_infos.apartment_id = apartment_infos.apartment_id  AND lease_status_id IN (1,7,8,9)) AS lease_end,
                                        (SELECT tenant_ids FROM lease_infos WHERE lease_infos.apartment_id = apartment_infos.apartment_id  AND lease_status_id IN (1,7,8,9)) AS tenant_ids
                                        FROM apartment_infos WHERE floor_id = :floor_id");
            $this->crud->bind(":floor_id", $floor_id);

            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function updateBuildingTimelines($building_id, $buildingTimelines) {
        try {
            $this->crud->query("UPDATE building_infos SET building_timelines = :building_times WHERE building_id = :building_id");
            $this->crud->bind(":building_times", $buildingTimelines);
            $this->crud->bind(":building_id", $building_id);
            $this->crud->execute();
            return $this->crud->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBuildingTimeLines($building_id) {
        try {
            $this->crud->query("SELECT building_timelines FROM building_infos WHERE building_id = :building_id");
            $this->crud->bind(":building_id", $building_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /*     * *
     * Get the building Mapping Picture to show in the Mapping Area Configuration
     * Each building has a mapping_picture field in the building_infos table.
     */

    public function getMappingPicture($building_id) {
        try {
            $this->crud->query("SELECT mapping_picture FROM building_infos WHERE building_id = :building_id");
            $this->crud->bind(":building_id", $building_id);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function saveFloorsMap($building_id, $mapping_area) {
        try {
            $this->crud->query("UPDATE building_infos SET mapping_area = :mapping_area WHERE building_id = :building_id");
            $this->crud->bind(":mapping_area", $mapping_area);
            $this->crud->bind(":building_id", $building_id);
            $this->crud->execute();
            return $this->crud->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Fetch the mapping picture and the mapping area for a specified building_id
    public function getMappingData($buildingId) {
        try {
            $this->crud->query("SELECT mapping_picture,mapping_area FROM building_infos WHERE building_id = :building_id");
            $this->crud->bind(":building_id", $buildingId);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBldPhotos($building_id) {
        try {
            $sql="SELECT building_id, pictures FROM building_infos where building_id=$building_id";
            //        echo $sql;
            $this->crud->query($sql);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

}
