<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 2017/6/14
 * Time: pm1:12
 */
class Owner {
    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    public function getOwnerName ($id){
        try{
            $this->crud->query("SELECT full_name FROM owner_infos WHERE owner_id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultField();
            return $row;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getOwnerInfo ($id){
        try{
            $this->crud->query("SELECT * FROM owner_infos WHERE owner_id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            return $row;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function addOwnerInfo($id, $employee_id, $full_name, $mobile, $email, $address, $address2, $country_id, $province_id, $city, $postal_code, $owner_type_id, $username, $userpass, $manager_id){
        try{
            $this->crud->query("INSERT INTO owner_infos(owner_id, employee_id, full_name, mobile, email, address, address2, country_id, province_id, city, postal_code, owner_type_id, username, userpass, manager_id)
                                      VALUES (:id, :employee_id, :full_name, :mobile, :email, :address, :address2, :country_id, :province_id, :city, :postal_code, :owner_type_id, :username, :userpass, :manager_id)");

            $this->crud->bind(':id',$id);
            $this->crud->bind(':employee_id',$employee_id);
            $this->crud->bind(':full_name',$full_name);
            $this->crud->bind(':mobile',$mobile);
            $this->crud->bind(':email',$email);
            $this->crud->bind(':address',$address);
            $this->crud->bind(':address2',$address2);
            $this->crud->bind(':country_id',$country_id);
            $this->crud->bind(':province_id',$province_id);
            $this->crud->bind(':city',$city);
            $this->crud->bind(':postal_code',$postal_code);
            $this->crud->bind(':owner_type_id',$owner_type_id);
            $this->crud->bind(':username',$username);
            $this->crud->bind(':userpass',$userpass);
            $this->crud->bind(':manager_id',$manager_id);

            $this->crud->execute();
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function editOwnerInfo($id, $employee_id, $full_name, $mobile, $email, $address, $address2, $country_id, $province_id, $city, $postal_code, $owner_type_id, $username, $userpass, $manager_id){
        try{
            $this->crud->query("UPDATE  owner_infos
                                      SET     employee_id=:employee_id,   full_name=:full_name, 
                                              mobile=:mobile,       email=:email,               address=:address, 
                                              address2=:address2,   country_id=:country_id,     province_id=:province_id, 
                                              city=:city,           postal_code=:postal_code,   owner_type_id=:owner_type_id, 
                                              username=:username,   userpass=:userpass,         manager_id=:manager_id
                                      WHERE   owner_id = :id");

            $this->crud->bind(':id',$id);
            $this->crud->bind(':employee_id',$employee_id);
            $this->crud->bind(':full_name',$full_name);
            $this->crud->bind(':mobile',$mobile);
            $this->crud->bind(':email',$email);
            $this->crud->bind(':address',$address);
            $this->crud->bind(':address2',$address2);
            $this->crud->bind(':country_id',$country_id);
            $this->crud->bind(':province_id',$province_id);
            $this->crud->bind(':city',$city);
            $this->crud->bind(':postal_code',$postal_code);
            $this->crud->bind(':owner_type_id',$owner_type_id);
            $this->crud->bind(':username',$username);
            $this->crud->bind(':userpass',$userpass);
            $this->crud->bind(':manager_id',$manager_id);

            $this->crud->execute();
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function deleteOwnerInfo($id){
        try{
            $this->crud->query("DELETE FROM owner_infos WHERE owner_id = :id");
            $this->crud->bind(':id', $id);
            $this->crud->execute();
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }
}