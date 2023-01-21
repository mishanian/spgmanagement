<?php

Class Company
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    public function getCompanyInfo($id)
    {
        try {
            $sql="SELECT * FROM company_infos WHERE id=$id";
          //  die(var_dump($this->crud));
            $this->crud->query($sql);
            //$this->crud->bind(":id", $id);

            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllEmployeesInCompany($company_id)
    {
        try {
            $this->crud->query("SELECT employee_id FROM employee_infos WHERE company_id=:company_id");
            $this->crud->bind(":company_id", $company_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLogo($id)
    {
        if ($logo = $this->getCompanyInfo($id)['logo']) {
            return "admin/files/logos/" . $logo;
        } else {
            return "images/logo.png";
        }
    }

    public function getName($id)
    {
        try {
            $this->crud->query("SELECT name FROM company_infos WHERE id=:id");
            $this->crud->bind(":id", $id);
            return $this->crud->resultField();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getHistory($id)
    {
        try {
            return $this->getCompanyInfo($id)['about_history'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAbout($id)
    {
        try {
            $about = $this->getCompanyInfo($id)['about'];
            return $about;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLatitude($id)
    {
        if ($this->getCompanyInfo($id)['latitude']) {
            return $this->getCompanyInfo($id)['latitude'];
        } else {
            return $this->getLatLng($this->getAddress($id))['results'][0]['geometry']['location']['lat'];
        }
    }

    public function getLongitude($id)
    {
        if ($this->getCompanyInfo($id)['longitude']) {
            return $this->getCompanyInfo($id)['longitude'];
        } else {
            return $this->getLatLng($this->getAddress($id))['results'][0]['geometry']['location']['lng'];
        }
    }

    private function getLatLng($address)
    {
        $address = utf8_decode($address);
        $address = str_replace(' ', '+', $address);
        $response = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=AIzaSyBiVqB039D3b_MUY6WD0_xgOQlR6BSDYTg");
        $responseKeys = json_decode($response, true);
        return $responseKeys;
    }

    public function getAddress($id)
    {
        try {
            $row = $this->getCompanyInfo($id);
            $address1 = $row['address1'];
            $address2 = '';
            if ($row['address2']) {
                $address2 = $row['address2'] . ", ";
            }
            $city = $row['city'];
            return $address2 . $address1 . ", " . $city . ", " . $row['postal_code'] . ", Canada";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getPhone($id)
    {
        try {
            return $this->getCompanyInfo($id)['phone'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getEmail($id)
    {
        try {
            return $this->getCompanyInfo($id)['contact_email'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBackground($id)
    {
        try {
            return "admin/files/" . $this->getCompanyInfo($id)['homepage_background'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getFacebook($id)
    {
        try {
            return $this->getCompanyInfo($id)['facebook'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getTwitter($id)
    {
        try {
            return $this->getCompanyInfo($id)['twitter'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getGooglePlus($id)
    {
        try {
            return $this->getCompanyInfo($id)['googleplus'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLinkedIn($id)
    {
        try {
            return $this->getCompanyInfo($id)['linkedin'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getWebTitle($id)
    {
        return $this->getCompanyInfo($id)['web_title'];
    }

    public function getRenewalGapDay($id)
    {
        return $this->getCompanyInfo($id)['renewal_gap_day'];
    }

    public function getLatePaymentGap($id)
    {
        return $this->getCompanyInfo($id)['late_gap'];
    }

    public function getLatePaymentReminderDays($id)
    {
        return $this->getCompanyInfo($id)['late_reminder_day'];
    }

    public function getLatePaymentType($id)
    {
        return $this->getCompanyInfo($id)['late_type'];
    }

    public function getLatePaymentAmt($id)
    {
        return $this->getCompanyInfo($id)['late_amount'];
    }

    public function getRenewalNotificationDay($id)
    {
        return $this->getCompanyInfo($id)['renewal_notification_day'];
    }


public function getCompanyCanAdvertise()
{
    try {
        $sql="SELECT id FROM company_infos WHERE can_advertise=1";
        //  die(var_dump($this->crud));
        $this->crud->query($sql);
        //$this->crud->bind(":id", $id);

        return $this->crud->resultArray();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

}