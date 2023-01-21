<?php

/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 2017/6/14
 * Time: pn1:15
 */
include_once("Class.Company.php");
class Employee
{
    private $crud;
    private $DB_company;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
        $this->DB_company = new Company($DB_con);
    }

    public function getEmployeeInfo($id)
    {
        try {
            $this->crud->query("SELECT * FROM employee_infos WHERE employee_id=:id");
            $this->crud->bind(':id', $id);
            $row = $this->crud->resultSingle();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getCompanyId($id)
    {
        try {
            $row = $this->getEmployeeInfo($id);
            return $row['company_id'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getEmployeeName($id)
    {
        $employee = $this->getEmployeeInfo($id);
        if (!empty($employee)) {
            return $employee['full_name'];
        } else {
            return "";
        }
    }

    //If there's an email in Employee table then fetch it. If not, go to according Company table and fetch the company email.
    public function getEmployeeEmail($id)
    {
        try {
            $row = $this->getEmployeeInfo($id);
            if ($row['email']) {
                return $row['email'];
            } else {
                $company_id = $this->getCompanyId($id);
                return $this->DB_company->getEmail($company_id);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //If there's a mobile in Employee table then fetch it. If not, go to according Company table and fetch the company phone.
    public function getEmployeeMobile($id)
    {
        try {
            $row = $this->getEmployeeInfo($id);
            if ($row['mobile']) {
                return $row['mobile'];
            } else {
                $company_id = $this->getCompanyId($id);
                return $this->DB_company->getPhone($company_id);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function addEmployeeInfo($id, $company_id, $full_name, $email, $mobile, $phone_number, $phone_ext, $country_id, $province_id, $city, $job_title_id, $language_id, $notify_from, $notify_to, $active_id, $username, $userpass)
    {
        try {
            $this->crud->query("INSERT INTO employee_infos(employee_id, company_id, full_name, email, mobile, phone_number, phone_ext, country_id, province_id, city, job_title_id, language_id, notify_from, notify_to, active_id, username, userpass)
                                      VALUES (:id, :company_id, :full_name, :email, :mobile, :phone_number, :phone_ext, :country_id, :province_id, :city, :job_title_id, :language_id, :notify_from, :notify_to, :active_id, :username, :userpass)");

            $this->crud->bind(':id', $id);
            $this->crud->bind(':company_id', $company_id);
            $this->crud->bind(':full_name', $full_name);
            $this->crud->bind(':email', $email);
            $this->crud->bind(':mobile', $mobile);
            $this->crud->bind(':phone_number', $phone_number);
            $this->crud->bind(':phone_ext', $phone_ext);
            $this->crud->bind(':country_id', $country_id);
            $this->crud->bind(':province_id', $province_id);
            $this->crud->bind(':city', $city);
            $this->crud->bind(':job_title_id', $job_title_id);
            $this->crud->bind(':language_id', $language_id);
            $this->crud->bind(':notify_from', $notify_from);
            $this->crud->bind(':notify_to', $notify_to);
            $this->crud->bind(':active_id', $active_id);
            $this->crud->bind(':username', $username);
            $this->crud->bind(':userpass', $userpass);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function editEmployeeInfo($id, $company_id, $full_name, $email, $mobile, $phone_number, $phone_ext, $country_id, $province_id, $city, $job_title_id, $language_id, $notify_from, $notify_to, $active_id, $username, $userpass)
    {
        try {
            $this->crud->query("UPDATE  employee_infos
                                      SET     company_id=:company_id,       full_name=:full_name, 
                                              email=:email,                 mobile=:mobile,               phone_number=:phone_number, 
                                              phone_ext=:phone_ext,         country_id=:country_id,       province_id=:province_id, 
                                              city=:city,                   job_title_id=:job_title_id,   language_id=:language_id, 
                                              notify_from=:notify_from,     notify_to=:notify_to,         active_id=:active_id, 
                                              username=:username,           userpass=:userpass
                                      WHERE   employee_id = :id");

            $this->crud->bind(':id', $id);
            $this->crud->bind(':company_id', $company_id);
            $this->crud->bind(':full_name', $full_name);
            $this->crud->bind(':email', $email);
            $this->crud->bind(':mobile', $mobile);
            $this->crud->bind(':phone_number', $phone_number);
            $this->crud->bind(':phone_ext', $phone_ext);
            $this->crud->bind(':country_id', $country_id);
            $this->crud->bind(':province_id', $province_id);
            $this->crud->bind(':city', $city);
            $this->crud->bind(':job_title_id', $job_title_id);
            $this->crud->bind(':language_id', $language_id);
            $this->crud->bind(':notify_from', $notify_from);
            $this->crud->bind(':notify_to', $notify_to);
            $this->crud->bind(':active_id', $active_id);
            $this->crud->bind(':username', $username);
            $this->crud->bind(':userpass', $userpass);

            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function deleteEmployeeInfo($id)
    {
        try {
            $this->crud->query("DELETE FROM employee_infos WHERE employee_id = :id");
            $this->crud->bind(':id', $id);
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getHandyman()
    {
        try {
            $this->crud->query("SELECT * FROM employee_infos WHERE user_level = 11");
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLevelName($level)
    {
        try {
            $this->crud->query("SELECT userlevelname FROM userlevels WHERE userlevelid = $level");
            return $this->crud->resultField();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}