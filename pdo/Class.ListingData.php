<?php

/**
 * Modified by 
 * User: Mehran
 * Date: 2021-05-24
 */
//include_once ("Class.Building.php");

class ListingData
{

    private $DB_building, $DB_apt;

    public function __construct($DB_con)
    {
        $this->DB_building = new Building($DB_con);
        $this->DB_apt = new Apt($DB_con);
    }
    /* not in use
    public function allJsonData()
    {
        $building_rows = $this->DB_building->getAllBdRows();
        $ids = array();
        foreach ($building_rows as $building_row) {
            array_push($ids, $building_row['building_id']);
        }
        return $this->jsonData($ids, $company_id);
    }
    // not in use
    public function allJsonDataByEmployee($employee_id)
    {
        $building_rows = $this->DB_building->getAllBdRowsByEmployee($employee_id);
        $ids = array();
        foreach ($building_rows as $building_row) {
            array_push($ids, $building_row['building_id']);
        }
        return $this->jsonData($ids, $company_id);
    }
    */
    //in use
    public function allJsonDataByCompany($company_id)
    {
        $building_rows = $this->DB_building->getAllBdRowsByCompany($company_id);
        $ids = array();
        foreach ($building_rows as $building_row) {
            array_push($ids, $building_row['building_id']);
        }
        return $this->jsonData($ids, $company_id);
    }

    public function allJsonDataByCompanyWithHide($company_id)
    {
        $building_rows = $this->DB_building->getAllBdRowsByCompanyWithHide($company_id);
        $ids = array();
        foreach ($building_rows as $building_row) {
            array_push($ids, $building_row['building_id']);
        }
        return $this->jsonData($ids, $company_id);
    }



    //Yuhong: clean up dummy data with '', 2018-02-06
    public function jsonData($building_list, $company_id)
    {
        $i = 0;
        $pics_data = array('count' => '100'); //, 'photos'=>''
        $Bdata = array();
        foreach ($building_list as $building_id) {
            $building_row = $this->DB_building->getBdInfoWORules($building_id);

            $photos = array(
                'photo_id' => '',
                'photo_title' => $this->DB_building->getBdName($building_id),
                'photo_url' => '#',
                'photo_file_url' => $this->DB_building->getFeaturePic($building_id),
                'longitude' => $building_row['longitude'],
                'latitude' => $building_row['latitude'],
                'bedrooms' => '',
                'bathrooms' => '',
                'address' => $this->DB_building->getFullAddress($building_id),
                'images_count' => '',
                'owner_name' => '',
                'owner_url' => '',
                'square_feet' => '',
                'phone' => '',
                'type' => '',
                'price' => $this->DB_building->getPriceRange($building_id, $company_id),
                'monthly_price' => '',
                'status' => '',
                'project_type' => '',
                'width' => '',
                'height' => '',
                'building_id' => $building_id,
                'facilities' => $this->DB_building->getImportantFacilities($building_id),
            );
            if ($this->DB_building->getBdInfoWORules($building_id)['featured'] == 1) {
                $photos['project_type'] = '<span class="label-featured label label-success">Featured</span>';
            }
            if ($photos['longitude'] == null || $photos['latitude'] == null) {
                $address = $this->DB_building->getFullAddress($building_id);
                $latLng = $this->getLatLng($address);
                $photos['longitude'] = $latLng['results'][0]['geometry']['location']['lng'];
                $photos['latitude'] = $latLng['results'][0]['geometry']['location']['lat'];
            }
            array_push($Bdata, $photos);

            $i++;
        }
        // $pics = json_encode($pics_data);
        $BdataAr = array('count' => '100', 'photos' => $Bdata);
        $pics = json_encode($BdataAr);
        // var_dump($BdataAr);
        return $pics;
    }

    private function getLatLng($address)
    {
        $address = utf8_decode($address);
        $address = str_replace(' ', '+', $address);
        $response = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=AIzaSyBiVqB039D3b_MUY6WD0_xgOQlR6BSDYTg");
        $responseKeys = json_decode($response, true);
        return $responseKeys;
    }



    public function jsonDataUnits($apt_list, $company_id)
    {
        $i = 0;
        $pics_data = array('count' => '10785236'); //, 'photos'=>''
        $Bdata = array();
        foreach ($apt_list as $apt_id) {
            $apt_row = $this->DB_apt->getAptInfo($apt_id);
            $building_id = $apt_row['building_id'];
            $available_date = $apt_row['available_date'];
            $building_row = $this->DB_building->getBdInfoWORules($building_id);
            $photos = array(
                'photo_id' => '',
                'photo_title' => $this->DB_building->getBdName($building_id),
                'photo_url' => '#',
                'photo_file_url' => $this->DB_building->getFeaturePic($building_id),
                'longitude' => $building_row['longitude'],
                'latitude' => $building_row['latitude'],
                'address' => $this->DB_building->getFullAddress($building_id),
                'images_count' => '',
                'owner_name' => '',
                'owner_url' => '',
                'phone' => '',
                'price' => $this->DB_building->getPriceRange($building_id, $company_id),
                'monthly_price' => '',
                'status' => '',
                'project_type' => '',
                'width' => '',
                'height' => '',
                'building_id' => $building_id,
                'available_date' => $available_date,
                'facilities' => $this->DB_building->getImportantFacilities($building_id),

                'unit_price' => $this->DB_apt->getMonthlyPrice($apt_id),
                'bedrooms' => $apt_row['bedrooms'],
                'bathrooms' => $apt_row['bath_rooms'],
                'square_feet' => $apt_row['area'] . ' (Sq.ft)',
                'type' => $this->DB_apt->getSizeType($apt_id),
                'unit_number' => $apt_row['unit_number'],
                'unit_feature_pic' => $this->DB_apt->getFeaturePic($apt_id),
            );
            if ($photos['longitude'] == null || $photos['latitude'] == null) {
                $address = $this->DB_building->getFullAddress($building_id);
                $latLng = $this->getLatLng($address);
                $photos['longitude'] = $latLng['results'][0]['geometry']['location']['lng'];
                $photos['latitude'] = $latLng['results'][0]['geometry']['location']['lat'];
            }
            //$pics_data['photos'][$i] = $photos;

            array_push($Bdata, $photos);
            $i++;
        }
        $BdataAr = array('count' => '100', 'photos' => $Bdata);
        $pics = json_encode($BdataAr);

        return $pics;
    }
}