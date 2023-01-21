<?php

class Project {

    private $crud;
  //  private $DB_country, $DB_province, $DB_facility, $DB_company;

    public function __construct($DB_con) {
        $this->crud = new Crud($DB_con);
//        $this->DB_country = new Country($DB_con);
//        $this->DB_province = new Province($DB_con);
//        $this->DB_facility = new Facility($DB_con);
//        $this->DB_company = new Company($DB_con);
    }

    public function getProjectInfo($project_id) {
        $this->crud->query("SELECT * FROM project_infos WHERE project_id=:project_id");
        $this->crud->bind(':project_id', $project_id);
        $row = $this->crud->resultSingle();
        return $row;
    }

    public function getContractInfo($contract_id) {
        $this->crud->query("SELECT * FROM contract_infos WHERE contract_id=:contract_id");
        $this->crud->bind(':contract_id', $contract_id);
        $row = $this->crud->resultSingle();
        return $row;
    }

    public function getProjectName($project_id) {
        $this->crud->query("SELECT name FROM project_infos WHERE project_id=:project_id");
        $this->crud->bind(':project_id', $project_id);
        $row = $this->crud->resultField();
        return $row;
    }

    public function getContactName($contract_id) {
        $this->crud->query("SELECT contract_desc FROM contract_infos WHERE contract_id=:contract_id");
        $this->crud->bind(':contract_id', $contract_id);
        $row = $this->crud->resultField();
        return $row;
    }

}
