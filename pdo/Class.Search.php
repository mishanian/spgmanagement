<?php
/**
 * Created by PhpStorm.
 * User: Mehran
 * Date: 2019/08/17
 */
class Search{

    private $DB_building, $DB_apt, $DB_lease;

    public function __construct($DB_con)
    {
        $this->DB_building = new Building($DB_con);
        $this->DB_apt = new Apt($DB_con);
        $this->DB_lease = new Lease($DB_con);
    }

    //-------------------------------Searching Units-------------------------------------

    //Search from index bottom by size
    public function searchAptBySize($apt_size_id, $company_id){
        $apt_arr = array();
        foreach ($this->DB_apt->getAllAvailableUnits($company_id) as $apt_row){
            if ($this->compareAptSize($apt_size_id, $apt_row) &&
                $this->DB_apt->isUnitShowed($apt_row['apartment_id'])){
                array_push($apt_arr, $apt_row['apartment_id']);
            }
        }
        return $apt_arr;
    }
    public function countAptsBySize($apt_size_id, $company_id,$apartmentText = false){
        $number = count($this->searchAptBySize($apt_size_id, $company_id));
        if ($number == 0){
            return "None Available";
        }else if ($number == 1){
            return "1 Apartment";
        }else{
          if($apartmentText){
            return $number;  
          }
          return $number." Apartments";
        }
    }

    //Search from index search bar
    public function searchAptFromIndex($province_id, $size_id, $price, $company_id){
        $apt_arr = array();
        foreach ($this->DB_apt->getAllAvailableUnits($company_id) as $apt_row) {
            if ($this->compareAptProvince($province_id, $apt_row) &&
                $this->compareAptSize($size_id, $apt_row) &&
                $this->compareAptPrice($price, $apt_row)&&
                $this->DB_apt->isUnitShowed($apt_row['apartment_id']))
            {
                array_push($apt_arr, $apt_row['apartment_id']);
            }
        }
        return $apt_arr;
    }

    //Search from Search Bar
    public function searchAptFromSearchBar($province_id, $size_id, $price, $bedroom, $area, $company_id){
        $apt_arr = array();
        foreach ($this->DB_apt->getAllAvailableUnits($company_id) as $apt_row) {
            if (
                $this->compareAptProvince($province_id, $apt_row) &&
                $this->compareAptSize($size_id, $apt_row) &&
                $this->compareAptPrice($price, $apt_row) &&
                $this->compareAptBedrooms($bedroom, $apt_row) &&
                $this->compareAptArea($area, $apt_row)&&
                $this->DB_apt->isUnitShowed($apt_row['apartment_id'])
            ){
                array_push($apt_arr, $apt_row['apartment_id']);
            }
        }
        return $apt_arr;
    }

    //-------------------------comparing functions for apt search.-----------------------
    private function compareAptSize($apt_size_id, $apt_row){
        if ($apt_size_id == 0){
            return true;
        }else if ($apt_row['size_type_id'] == $apt_size_id){
            return true;
        }else{
            return false;
        }
    }

    private function compareAptBedrooms($bedroom, $apt_row){
        if ($bedroom == 0){
            return true;
        }else{
            if ($apt_row['bedrooms'] == $bedroom){
                return true;
            }else{
                return false;
            }
        }
    }

    private function compareAptArea($area, $apt_row){
        if ($area == 0){
            return true;
        }else{
            //restore the area range condition.
            $area_max = $area;
            $area_min = $area-500;
            if ($apt_row['area'] <= $area_max && $apt_row['area'] > $area_min){
                return true;
            }else{
                return false;
            }
        }
    }

    private function compareAptProvince($province_id, $apt_row){
        $building_row = $this->DB_building->getBdInfo($apt_row['building_id']);
        return $this->compareProvince($province_id, $building_row);
    }

    private function compareAptPrice($price, $apt_row){
        //option: Any Price
        if ($price == 0){
            return true;
        }else if ($price == 2500) {
            if ($apt_row['monthly_price'] > $price) {
                return true;
            }else {
                return false;
            }
        }else {
            //restore the price range condition.
            $range_max = $price;
            $range_min = $price - 500;

            if ($apt_row['monthly_price'] <= $range_max && $apt_row['monthly_price'] > $range_min) {
                return true;
            } else {
                return false;
            }
        }
    }

    //------------------------------Searching buildings, not in use-----------------------

    public function searchFromIndex($province_id, $size_id, $price, $employee_id){
        $building_arr = array();
        foreach ($this->DB_building->getAllBdRowsByEmployee($employee_id) as $building_row){
            if ($this->compareProvince($province_id, $building_row) &&
                $this->compareSize($size_id, $building_row) &&
                $this->comparePrice($price, $building_row))
            {
                array_push($building_arr, $building_row['building_id']);
            }
        }
        return $building_arr;
    }

    public function searchFromIndexSize($size_id, $employee_id){
        $building_arr = array();
        foreach ($this->DB_building->getAllBdRowsByEmployee($employee_id) as $building_row){
            if ($this->compareSize($size_id, $building_row)){
                array_push($building_arr, $building_row['building_id']);
            }
        }
        return $building_arr;
    }

    public function searchFromSearchBar($province_id, $size_id, $price, $bedroom, $area, $employee_id){
        $building_arr = array();
        foreach ($this->DB_building->getAllBdRowsByEmployee($employee_id) as $building_row){
            if ($this->compareProvince($province_id, $building_row) &&
                $this->compareSize($size_id, $building_row) &&
                $this->comparePrice($price, $building_row) &&
                $this->compareBedroom($bedroom, $building_row) &&
                $this->compareArea($area, $building_row))
            {
                array_push($building_arr, $building_row['building_id']);
            }
        }
        return $building_arr;
    }

    public function searchBasedOnTypes($building_type_id, $employee_id){
        $building_arr = array();
        foreach ($this->DB_building->getAllBdRowsByEmployee($employee_id) as $building_row) {
            if ($building_row['building_type_id'] == $building_type_id){
                array_push($building_arr, $building_row['building_id']);
            }
        }
        return $building_arr;
    }

    private function compareProvince($province_id, $building_row){
        //option: All province
        if ($province_id == 0){
            return true;
        }
        if ($building_row['province_id'] == $province_id){
            return true;
        }else{
            return false;
        }
    }

    private function compareSize($size_id, $building_row){
        //option: Any Size
        if ($size_id == 0){
            return true;
        }
        $apt_rows = $this->DB_apt->getAptInfoInBuilding($building_row['building_id']);
        foreach ($apt_rows as $apt_row){
            if ($apt_row['size_type_id'] == $size_id){
                return true;
            }
        }
        return false;
    }

    private function comparePrice($price, $building_row){
        //option: Any Price
        if ($price == 0){
            return true;
        }
        $apt_rows = $this->DB_apt->getAptInfoInBuilding($building_row['building_id']);
        //restore the price range condition.
        $range_max = $price;
        $range_min = $price-500;
        foreach ($apt_rows as $apt_row){
            if ($apt_row['monthly_price']<=$range_max && $apt_row['monthly_price']>$range_min){
                return true;
            }
        }
        return false;
    }

    private function compareBedroom($bedroom, $building_row){
        //option: bedrooms
        if ($bedroom == 0){
            return true;
        }
        foreach ($this->DB_apt->getAptInfoInBuilding($building_row['building_id']) as $apt_row){
            if ($apt_row['bedrooms'] == $bedroom){
                return true;
            }
        }
        return false;
    }

    private function compareArea($area, $building_row){
        //option: area
        if ($area == 0){
            return true;
        }
        //restore the area range condition.
        $area_max = $area;
        $area_min = $area-500;
        foreach ($this->DB_apt->getAptInfoInBuilding($building_row['building_id']) as $apt_row){
            if ($apt_row['area'] <= $area_max && $apt_row['area'] > $area_min){
                return true;
            }
        }
        return false;
    }

}
