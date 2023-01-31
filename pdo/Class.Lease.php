<?php

/**
 * Created by PhpStorm.
 * User: Mehran
 * Date: 2017/7/4
 */

class Lease
{

    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    /**
     * Pass Lease ID and retrieve Lease details from the lease_infos table
     * @param int $lease_id
     * @return array
     */
    public function getLeaseInfoByLeaseId($lease_id)
    {
        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE id = :id");
            $this->crud->bind(':id', $lease_id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBuildingAndApartmentIdByLeaseId($lease_id)
    {
        try {
            $this->crud->query("SELECT building_id, apartment_id FROM lease_infos WHERE id = :id");
            $this->crud->bind(':id', $lease_id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLeaseInfo($apartment_id)
    {
        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE apartment_id=:id");
            $this->crud->bind(':id', $apartment_id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    //Returns the string of lease length (e.g. 12)
    public function getLeaseLength($apartment_id)
    {
        try {
            if ($length = $this->getLeaseInfo($apartment_id)['length_of_lease']) {
                return $length . " Month";
            } else {
                return "Not Applicable.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the string of move in date (e.g. 2017-01-19)
    public function getMoveInDate($apartment_id)
    {
        try {
            if ($date = $this->getLeaseInfo($apartment_id)['move_in_date']) {
                return $date;
            } else {
                return "Not Applicable.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Returns the string of lease status (e.g. Active/Upcoming)
    public function getLeaseStatus($apartment_id)
    {
        try {
            if ($lease_status_id = $this->getLeaseInfo($apartment_id)['lease_status_id']) {
                $this->crud->query("SELECT * FROM lease_status WHERE id=:id");
                $this->crud->bind(':id', $lease_status_id);
                $lease_status_row = $this->crud->resultSingle();
                $lease_status = $lease_status_row['name'];
                return $lease_status;
            } else {
                return "Not Applicable.";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getActiveLeaseEndDate($apartment_id)
    {
        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE apartment_id = :apartment_id AND lease_status_id = 1");
            $this->crud->bind(":apartment_id", $apartment_id);
            return $this->crud->resultSingle()['end_date'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLeaseInfoByAptId($apartmentId)
    {
        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE apartment_id=:id");
            $this->crud->bind(':id', $apartmentId);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function isOccupied($apartment_id)
    {
        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE apartment_id = :apartment_id");
            $this->crud->bind(":apartment_id", $apartment_id);
            $rows = $this->crud->resultSet();
            date_default_timezone_set('America/New_York');
            $today = date("Y-m-d");
            foreach ($rows as $row) {
                if (strtotime($row['start_date']) < strtotime($today) && strtotime($row['end_date']) > strtotime($today)) {
                    return $row['tenant_ids'];
                }
            }
            return false;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /* 20 days after the move out date - tenant account has to be inactive */
    public function getTenantIdsMoveOut20Days()
    {
        try {
            $this->crud->query("SELECT id,lease_status_id,tenant_ids,move_out_date,DATE_ADD(move_out_date, INTERVAL +20 DAY) AS dateafter20 FROM lease_infos where start_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)");
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}