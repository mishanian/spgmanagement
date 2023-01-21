<?php

class Services
{
    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    /**
     * get the services availabilities for user currently
     * services : credit_check & lease_sign
     * Approach : 1. calculate the payment record to get the bought slot number
     *            2. calculate the slot user used
     *            3. bought_slot_number - slot_already_used
    */
    public function get_services_availabilities_for_user($user_id){
        try{
            $this->crud->query("SELECT service_type, sum(services_count) AS buy_count FROM service_payments WHERE user_id = :user_id AND payment_status=1 group by service_type");
            $this->crud->bind(':user_id', $user_id);
            $result = $this->crud->resultSet();

            $services_avails = array();
            foreach ($result as $one){
                $services_avails[$one['service_type']] = $one['buy_count'];
            }

            $buy_credit_check = 0;
            if(array_key_exists("credit_check",$services_avails)){
                $buy_credit_check = $services_avails["credit_check"];
            }
            $buy_lease_sign = 0;
            if(array_key_exists("lease_sign",$services_avails)){
                $buy_lease_sign = $services_avails["lease_sign"];
            }

            $this->crud->query("SELECT count(*) AS count FROM service_credit_check_requests WHERE spgmanagement_user_id = :user_id");
            $this->crud->bind(':user_id', $user_id);
            $used_credit_check = $this->crud->resultSingle()['count'];

            $this->crud->query("SELECT count(*) AS count FROM service_lease_sign_requests WHERE spgmanagement_user_id = :user_id");
            $this->crud->bind(':user_id', $user_id);
            $used_lease_sign = $this->crud->resultSingle()['count'];

            $re['credit_check'] = $buy_credit_check - $used_credit_check;
            $re['lease_sign'] = $buy_lease_sign - $used_lease_sign;
            return $re;
        }catch (PDOException $e){echo $e->getMessage();}
    }

    //---------------------------------------- lease signing ------------------------------------

    /**
     * get the company info for a specific employee
    */
    public function get_employee_company_info($employee_id){
        try{
            $this->crud->query("SELECT * FROM company_infos WHERE id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id)");
            $this->crud->bind(':employee_id', $employee_id);
            return $this->crud->resultSingle();
        }catch (PDOException $e){echo $e->getMessage();}
    }

    /**
     * get buildings info (building_id, building_name, address, city ...) for a employee
     * Note : based on building_ids in employee_infos table
    */
    public function get_building_addresses_by_employee($employee_id){
        try{
            $this->crud->query("SELECT building_id,building_name,address,city,postal_code FROM building_infos WHERE find_in_set(building_id, (SELECT building_ids FROM employee_infos WHERE employee_id = :employee_id))");
            $this->crud->bind(':employee_id', $employee_id);
            return $this->crud->resultSet();
        }catch (PDOException $e){echo $e->getMessage();}
    }


    /**
     * get all apartments for a building
    */
    public function get_apt_list_in_building($building_id){
        try{
            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id = :building_id");
            $this->crud->bind(':building_id', $building_id);
            return $this->crud->resultSet();
        }catch (PDOException $e){echo $e->getMessage();}
    }


    public function get_building_info_by_id($building_id){
        try{
            $this->crud->query("SELECT * FROM building_infos WHERE building_id = :building_id");
            $this->crud->bind(':building_id', $building_id);
            return $this->crud->resultSingle();
        }catch (PDOException $e){echo $e->getMessage();}
    }

    /**
     * add one record to service_lease_sign_requests table
     * after user issue one lease_sign request, record info about lease_info, tenant_info .... 8zar8_user_id/spgmanagement_user_id
    */
    public function add_lease_sign_request($spgmanagement_user_id,$created_time,$lessee_name,$lessee_email,$lessee_telephone,$lease_address,$lease_apartment){
        try{
            $this->crud->query("INSERT INTO service_lease_sign_requests (spgmanagement_user_id,created_time,lessee_name,lessee_email,lessee_telephone,lease_address,lease_apartment) VALUES (:spgmanagement_user_id,:created_time,:lessee_name,:lessee_email,:lessee_telephone,:lease_address,:lease_apartment)");
            $this->crud->bind(':spgmanagement_user_id', $spgmanagement_user_id);
            $this->crud->bind(':created_time', $created_time);
            $this->crud->bind(':lessee_name', $lessee_name);
            $this->crud->bind(':lessee_email',$lessee_email);
            $this->crud->bind(':lessee_telephone',$lessee_telephone);
            $this->crud->bind(':lease_address',$lease_address);
            $this->crud->bind(':lease_apartment',$lease_apartment);
            $this->crud->execute();
        }catch (PDOException $e){echo $e->getMessage();}
    }

    //---------------------------------- credit check ---------------------------------------

    public function get_employee_id($employee_id){
        try{
            $this->crud->query("select * from employee_infos where employee_id = :employee_id");
            $this->crud->bind(':employee_id', $employee_id);
            return $this->crud->resultSingle();
        }catch (PDOException $e){echo $e->getMessage();}
    }

    /**
     * add one record to service_credit_check_requests table
     * after user issue one credit check request, add auto_generated token, tenant_info .... 8zar8_user_id/spgmanagement_user_id
     * token will be used to track the record to process the credit check
     */
    public function add_credit_check_request($user_id,$created_time, $auth_token, $requester_name, $requester_mail, $requester_phone, $requester_address, $tenant_name, $tenant_phone, $tenant_mail, $tenant_address, $tenant_province){
        try{
            $this->crud->query("INSERT INTO service_credit_check_requests (spgmanagement_user_id, created_time, auth_token, requester_name, requester_mail, requester_phone, requester_address, tenant_name, tenant_phone, tenant_mail, tenant_address, tenant_province) VALUES (:spgmanagement_user_id, :created_time, :auth_token, :requester_name, :requester_mail, :requester_phone, :requester_address, :tenant_name, :tenant_phone, :tenant_mail, :tenant_address, :tenant_province)");
            $this->crud->bind(":spgmanagement_user_id", $user_id);
            $this->crud->bind(":created_time", $created_time);
            $this->crud->bind(":auth_token", $auth_token);
            $this->crud->bind(":requester_name", $requester_name);
            $this->crud->bind(":requester_mail", $requester_mail);
            $this->crud->bind(":requester_phone", $requester_phone);
            $this->crud->bind(":requester_address", $requester_address);
            $this->crud->bind(":tenant_name", $tenant_name);
            $this->crud->bind(":tenant_phone", $tenant_phone);
            $this->crud->bind(":tenant_mail", $tenant_mail);
            $this->crud->bind(":tenant_address", $tenant_address);
            $this->crud->bind(":tenant_province", $tenant_province);
            $this->crud->execute();
            return $this->crud->lastInsertId();
        }catch (PDOException $e){echo $e->getMessage();}
    }


    public function get_credit_check_request_info_by_id($service_credit_check_request){
        try{
            $this->crud->query("SELECT * FROM service_credit_check_requests WHERE id = :service_credit_check_request");
            $this->crud->bind(":service_credit_check_request", $service_credit_check_request);
            return $this->crud->resultSingle();
        }catch (PDOException $e){echo $e->getMessage();}
    }
}