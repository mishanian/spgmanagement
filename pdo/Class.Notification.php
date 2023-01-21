<?php

class Notification
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    // -------   request notification -----------

    public function get_request_info($request_id){
        try{
            $this->crud->query("SELECT request_infos.id AS request_id,building_infos.building_name AS building, building_infos.feature_picture AS building_pic, apartment_infos.unit_number AS unit_number,tenant_ids,location,request_types.name AS request,message,entry_datetime,request_infos.employee_id , request_infos.apartment_id FROM request_infos,building_infos, apartment_infos,request_types WHERE building_infos.building_id  = request_infos.building_id AND apartment_infos.apartment_id = request_infos.apartment_id AND request_types.id = request_infos.request_type_id AND request_infos.id = :request_id");
            $this->crud->bind(":request_id",$request_id);
            $record = $this->crud->resultSingle();
            return $record;
        }catch (PDOException $e){ echo $e->getMessage();}
    }


    public function get_employee_info($employee_id){
        try{
            $this->crud->query("SELECT full_name, email, mobile FROM employee_infos WHERE employee_id =:employee_id");
            $this->crud->bind(":employee_id",$employee_id);
            $record = $this->crud->resultSingle();
            return $record;
        }catch (PDOException $e){echo $e->getMessage();}
    }


    public function get_location($apartment_id){
        try{
            $this->crud->query("SELECT unit_number ,floor_infos.floor_name AS floor,building_infos.building_name AS building_name FROM apartment_infos , building_infos , floor_infos WHERE apartment_infos.building_id = building_infos.building_id AND floor_infos.floor_id = apartment_infos.floor_id AND apartment_infos.apartment_id = :apartment_id");
            $this->crud->bind(":apartment_id",$apartment_id);
            $record =$this->crud->resultSingle();
            return $record;
        }catch (PDOException $e){echo $e->getMessage();}
    }


    public function get_request_communication_info($request_communication_id){
        try{
            $this->crud->query("SELECT request_communications.assign_employee_id, request_communications.assigner_id,request_status.name AS request_name,request_communications.remarks, request_communications.entry_date FROM request_communications, request_status WHERE request_status.id = request_communications.status_id AND request_communications.id = :request_communications_id");
            $this->crud->bind(":request_communications_id",$request_communication_id);
            $record = $this->crud->resultSingle();
            return $record;
        }catch (PDOException $e){echo $e->getMessage();}
    }



     // ----------   welcome notification ----------

    public function get_user_info($user_id){
        try{
            $this->crud->query("SELECT user_id,full_name,username,email,userpass FROM userlist WHERE user_id = :user_id");
            $this->crud->bind(":user_id",$user_id);
            $result = $this->crud->resultSingle();
            return $result;
        }catch (PDOException $e){echo $e->getMessage();}
    }







    /**
     * this method is to check the tenant is living in 2050,4238 building or not.
     * these two building is running in prod. env, other building still in testing env.
    */

    public function is_prod_or_not_sms($telephone){
        try{
            $this->crud->query("SELECT building_id FROM view_tenant_infos WHERE mobile = :mobile");
            $this->crud->bind(":mobile", $telephone);
            $building_id = $this->crud->resultSingle()['building_id'];

            if($building_id != null && ($building_id == 76 || $building_id == 86 || $building_id == 14))
                return true;
            else
                return false;

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }


    public function is_prod_or_not_email($email_addr){
        try{
            $this->crud->query("SELECT building_id FROM view_tenant_infos WHERE email = :email");
            $this->crud->bind(":email", $email_addr);
            $building_id = $this->crud->resultSingle()['building_id'];

            if($building_id != null && ($building_id == 76 || $building_id == 86 || $building_id == 14))
                return true;
            else
                return false;

        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
}