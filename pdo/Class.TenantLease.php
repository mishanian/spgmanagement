<?php

class TenantLease
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con); // to get Create , Edit , Delete Function
    }

    public function getTenantLeaseInfo($tenant_ids) //$tenant id is passing
    {
        try {
            // die("debug");
            $sql = "SELECT * FROM lease_infos LI LEFT JOIN building_infos BI on BI.building_id=LI.building_id left join apartment_infos APP ON APP.apartment_id = LI.apartment_id  WHERE FIND_IN_SET( :tenant_ids, tenant_ids ) and lease_status_id in( 1,2,7,8,9,10,11)";
            //            echo $sql;
            $this->crud->query($sql);

            $this->crud->bind(':tenant_ids', $tenant_ids);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getTableToArray($table_name) //$tenant id is passing
    {
        try {
            // die("debug");
            $sql = "SELECT * FROM $table_name";
            //            echo $sql;
            $this->crud->query($sql);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getRenewDetails($tenant_id, $lease_id)
    {
        try {
            $sql = "select * from view_renew_details WHERE FIND_IN_SET($tenant_id,tenant_ids) and lease_id=$lease_id";
            $this->crud->query($sql);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getRenewDetailsFull($tenant_id, $lease_id)
    {
        try {
            $sql = "select * from view_renew_details_full WHERE FIND_IN_SET($tenant_id,tenant_ids) and lease_id=$lease_id";
            $this->crud->query($sql);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getHistoryDetails($tenant_id, $lease_id, $history_type_id = "")
    {
        try {
            $sql = "select *, PV.name as province_name, AI.`monthly_price` AS `monthly_amount`  ,
            (SELECT full_name FROM tenant_infos TI WHERE tenant_id=$tenant_id) AS tenant_name
            from history HI left join lease_infos LI ON HI.table_id=LI.id  
            LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
            LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
            LEFT JOIN provinces PV ON BI.province_id=PV.id
            WHERE HI.user_id=$tenant_id and HI.table_id=$lease_id";
            if ($history_type_id != "") {
                $sql .= " and history_type_id IN($history_type_id)";
            }
            // console_log($sql);
            $this->crud->query($sql);
            return $this->crud->resultSingle();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}