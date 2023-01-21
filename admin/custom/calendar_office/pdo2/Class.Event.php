<?php

class Event {

    private $crud;

    public function __construct($DB_con){
        $this->crud = new Crud($DB_con);
    }

    public function create_event($building_id, $event_name, $event_contact, $event_date, $event_frequency, $event_frequency_type, $event_info, $event_preparation, $event_type, $event_category){
        
        try {
            $calendar_type = $event_category;
            $calendar_id = $this->get_calendar_id($building_id, $calendar_type);

            $this->crud->query("INSERT INTO office_maintenance_event_infos (calendar_id, event_name, event_contact, event_date, event_frequency, event_frequency_type, event_info, event_preparation, event_type, event_category) VALUES (:calendar_id, :event_name, :event_contact, :event_date, :event_frequency, :event_frequency_type, :event_info, :event_preparation, :event_type, :event_category)");
            
            $this->crud->bind(':calendar_id', $calendar_id); 
            $this->crud->bind(':event_name', $event_name); 
            $this->crud->bind(':event_contact', $event_contact); 
            $this->crud->bind(':event_date', $event_date);
            $this->crud->bind(':event_frequency', $event_frequency);  
            $this->crud->bind(':event_frequency_type', $event_frequency_type); 
            $this->crud->bind(':event_info', $event_info); 
            $this->crud->bind(':event_preparation', $event_preparation); 
            $this->crud->bind(':event_type', $event_type); 
            $this->crud->bind(':event_category', $event_category); 

            $this->crud->execute();
            return TRUE;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    private function get_calendar_id($building_id, $calendar_type) {

        try {
            $this->crud->query("SELECT id FROM calendar_infos WHERE building_id = :building_id AND calendar_type = :calendar_type");
            $this->crud->bind(':building_id', $building_id); 
            $this->crud->bind(':calendar_type', $calendar_type);
            $calendar_result = $this->crud->resultSingle(); 
            $calendar_id = $calendar_result['id'];

            return $calendar_id;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function get_events($event_type, $event_category, $building_id) {

        try {
            $calendar_type = $event_category;
            $calendar_id = $this->get_calendar_id($building_id, $calendar_type);

            $this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE event_type = :event_type AND event_category = :event_category AND calendar_id = :calendar_id");
            $this->crud->bind(':event_type', $event_type); 
            $this->crud->bind(':event_category', $event_category);
            $this->crud->bind(':calendar_id', $calendar_id);
            $result = $this->crud->resultSet();

            return $result;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        } 
    }

    public function delete_event($event_id) {

        try {
            $this->crud->query("DELETE FROM office_maintenance_event_infos WHERE id = :event_id");
            $this->crud->bind(':event_id', $event_id); 
            $this->crud->execute();

            return TRUE;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function get_event($event_id) {
  
        try {
            $this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE id = :event_id");
            $this->crud->bind(':event_id', $event_id); 
            $result = $this->crud->resultSingle();

            return $result;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function update_event($event_id, $calendar_id, $event_name, $event_contact, $event_date, $event_frequency, $event_frequency_type, $event_info, $event_preparation, $event_type, $event_category) {

        try {
            $this->crud->query("UPDATE office_maintenance_event_infos SET event_name = :event_name, calendar_id = :calendar_id, event_contact = :event_contact, event_date = :event_date, event_frequency = :event_frequency, event_frequency_type = :event_frequency_type, event_info = :event_info, event_preparation = :event_preparation, event_type = :event_type, event_category = :event_category WHERE id = :event_id");

            $this->crud->bind(':event_id', $event_id); 
            $this->crud->bind(':calendar_id', $calendar_id); 
            $this->crud->bind(':event_name', $event_name); 
            $this->crud->bind(':event_contact', $event_contact); 
            $this->crud->bind(':event_date', $event_date);
            $this->crud->bind(':event_frequency', $event_frequency);  
            $this->crud->bind(':event_frequency_type', $event_frequency_type); 
            $this->crud->bind(':event_info', $event_info); 
            $this->crud->bind(':event_preparation', $event_preparation); 
            $this->crud->bind(':event_type', $event_type); 
            $this->crud->bind(':event_category', $event_category); 
           
            $this->crud->execute();
            return TRUE;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        } 
    }

    public function get_calendar_events($building_id, $event_category) {
  
        try {
            $calendar_type = $event_category;
            $calendar_id = $this->get_calendar_id($building_id, $calendar_type);

            $this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE calendar_id = :calendar_id AND event_category = :event_category");
            $this->crud->bind(':calendar_id', $calendar_id); 
            $this->crud->bind(':event_category', $event_category);    
            $result = $this->crud->resultSet();

            return $result;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function upload($event_id, $upload_name, $upload_date) {

        try {
            $this->crud->query("INSERT INTO office_maintenance_event_uploads (event_id, upload_name, upload_date) VALUES (:event_id, :upload_name, :upload_date)");

            $this->crud->bind(':event_id', $event_id); 
            $this->crud->bind(':upload_name', $upload_name); 
            $this->crud->bind(':upload_date', $upload_date);

            $this->crud->execute();
            $upload_id = $this->crud->lastInsertId();
            return $upload_id;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }      
    }

    public function get_event_uploads($event_id) {

        try {
            $this->crud->query("SELECT * FROM office_maintenance_event_uploads WHERE event_id = :event_id");
            $this->crud->bind(':event_id', $event_id); 
            $result = $this->crud->resultSet();

            return $result;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }    
    }

    public function delete_upload($upload_id) {

        try {
            $this->crud->query("DELETE FROM office_maintenance_event_uploads WHERE id = :upload_id");
            $this->crud->bind(':upload_id', $upload_id);
            $this->crud->execute();
            return TRUE;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

}