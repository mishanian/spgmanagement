<?php
class Calendar {
	private $crud;

	public function __construct($DB_con) {
		$this->crud = new Crud($DB_con);
	}

	//------------------------- visit event ------------------------

	/**
	 * add a new visit_event template
	 * Note : here just add one visit_event template(settings)
	 * WorkFlow: add visit_event template firstly -> add slot inside the template -> visitor select the slot
	 */
	public function create_visit_event($event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day, $event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, $event_max_book_per_slot, $event_rolling_week, $building_id, $responsible_employee_id) {
		try {
			$this->crud->query("INSERT INTO event_infos (building_id, event_name, event_location, event_duration, event_custom_duration, event_max_book_per_day, event_increment, event_buffer_before, event_buffer_after, event_less_hour, event_description, event_max_book_per_slot, event_rolling_week,resonsible_employee_id) VALUES (:building_id, :event_name, :event_location, :event_duration, :event_custom_duration, :event_max_book_per_day, :event_increment, :event_buffer_before, :event_buffer_after, :event_less_hour, :event_description, :event_max_book_per_slot, :event_rolling_week, :resonsible_employee_id)");
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':event_name', $event_name);
			$this->crud->bind(':event_location', $event_location);
			$this->crud->bind(':event_duration', $event_duration);
			$this->crud->bind(':event_custom_duration', $event_custom_duration);
			$this->crud->bind(':event_max_book_per_day', $event_max_book_per_day);
			$this->crud->bind(':event_increment', $event_increment);
			$this->crud->bind(':event_buffer_before', $event_buffer_before);
			$this->crud->bind(':event_buffer_after', $event_buffer_after);
			$this->crud->bind(':event_less_hour', $event_less_hour);
			$this->crud->bind(':event_description', $event_description);
			$this->crud->bind(':event_max_book_per_slot', $event_max_book_per_slot);
			$this->crud->bind(':event_rolling_week', $event_rolling_week);
			$this->crud->bind(':resonsible_employee_id', $responsible_employee_id);
			$this->crud->execute();
			$event_id = $this->crud->lastInsertId();
			return $event_id;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the info for visit event template(event_infos table)
	 */
	public function update_visit_event($event_id, $event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day, $event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, $event_max_book_per_slot, $event_rolling_week, $person_in_charge) {
		try {
			$this->crud->query("UPDATE event_infos SET event_name = :event_name, event_location = :event_location, event_duration = :event_duration, event_custom_duration = :event_custom_duration, event_max_book_per_day = :event_max_book_per_day, event_increment = :event_increment, event_buffer_before = :event_buffer_before, event_buffer_after = :event_buffer_after, event_less_hour = :event_less_hour, event_description = :event_description, event_max_book_per_slot = :event_max_book_per_slot, event_rolling_week = :event_rolling_week, resonsible_employee_id=:resonsible_employee_id WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':event_name', $event_name);
			$this->crud->bind(':event_location', $event_location);
			$this->crud->bind(':event_duration', $event_duration);
			$this->crud->bind(':event_custom_duration', $event_custom_duration);
			$this->crud->bind(':event_max_book_per_day', $event_max_book_per_day);
			$this->crud->bind(':event_increment', $event_increment);
			$this->crud->bind(':event_buffer_before', $event_buffer_before);
			$this->crud->bind(':event_buffer_after', $event_buffer_after);
			$this->crud->bind(':event_less_hour', $event_less_hour);
			$this->crud->bind(':event_description', $event_description);
			$this->crud->bind(':event_max_book_per_slot', $event_max_book_per_slot);
			$this->crud->bind(':event_rolling_week', $event_rolling_week);
			$this->crud->bind(':resonsible_employee_id', $person_in_charge);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete one visit event template(event_infos table)
	 */
	public function delete_visit_event($event_id) {
		try {
			$this->crud->query("DELETE FROM event_infos WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->execute();

			$this->crud->query("DELETE FROM event_availabilities WHERE event_id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_visit_events($building_id) {
		try {
			$this->crud->query("SELECT * FROM event_infos WHERE event_max_book_per_slot = 1 AND building_id = :building_id");
			$this->crud->bind(':building_id', $building_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event templates(event infos) for one specific employee and specific building
	 */
	public function get_visit_events_by_employee($building_id, $employee_id) {
		try {
			$this->crud->query("SELECT * FROM event_infos WHERE event_max_book_per_slot = 1 AND building_id = :building_id AND resonsible_employee_id=:resonsible_employee_id");
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':resonsible_employee_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event templates for the specific employee(not property manager)
	 */
	public function get_personal_visit_events_by_employee($employee_id) {
		try {
			$this->crud->query("SELECT * FROM event_infos WHERE event_max_book_per_slot = 1 AND resonsible_employee_id = :resonsible_employee_id");
			$this->crud->bind(':resonsible_employee_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event templates for a specific employee in buildings list
	 * The function only used for manger
	 * manager : [admin_id=1 in employee_infos] can see all visit events of employee belonging to his company
	 */
	public function get_visit_events_by_admin($employee_id, $managed_buildings) {
		try {
			if(!$managed_buildings){
				$query = "SELECT * FROM event_infos";
			}else{
				$query = "SELECT * FROM event_infos WHERE building_id IN (" . $managed_buildings . ") AND resonsible_employee_id != :employee_id";
			}
			$this->crud->query($query);
			$this->crud->bind(':employee_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event templates for the specific employee and specific building
	 * The function only used for manger
	 * manager : [admin_id=1 in employee_infos] can see all visit events of employee belonging to his company
	 */
	public function get_visit_events_by_admin_building($employee_id, $building_id) {
		try {
			$this->crud->query("SELECT * FROM event_infos WHERE building_id = :building_id AND resonsible_employee_id != :employee_id");
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':employee_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one available slot for a visit_event template
	 */
	public function add_event_availability($event_id, $availability_start, $availability_end, $availability_type, $availability_specific_date) {
		try {
			$this->crud->query("INSERT INTO event_availabilities (event_id, availability_start, availability_end, availability_type, availability_specific_date) VALUES (:event_id, :availability_start, :availability_end, :availability_type, :availability_specific_date)");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':availability_start', $availability_start);
			$this->crud->bind(':availability_end', $availability_end);
			$this->crud->bind(':availability_type', $availability_type);
			$this->crud->bind(':availability_specific_date', $availability_specific_date);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------ visit event availability ---------------------

	/**
	 * edit the available slot for the visit_event template
	 * Only allow to change start_time and end_time
	 */
	public function edit_event_availability($availability_id, $availability_start, $availability_end) {
		try {
			$this->crud->query("UPDATE event_availabilities SET availability_start = :availability_start, availability_end = :availability_end WHERE id = :availability_id");

			$this->crud->bind(':availability_start', $availability_start);
			$this->crud->bind(':availability_end', $availability_end);
			$this->crud->bind(':availability_id', $availability_id);

			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete the available slot
	 */
	public function delete_event_availability($availability_id) {
		try {
			$this->crud->query("DELETE FROM event_availabilities WHERE id = :availability_id");
			$this->crud->bind(':availability_id', $availability_id);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all available slot for a specific visit_event template
	 * Order by availability_specific_date ASE
	 */
	public function get_event_availability($event_id) {
		try {
			$today = '"' . date("Y-m-d") . '"';
			$this->crud->query("SELECT * FROM event_availabilities WHERE event_id = :event_id AND availability_specific_date >= " . $today . " ORDER BY availability_specific_date ASC");
			$this->crud->bind(':event_id', $event_id);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * create a visit event booking
	 * the function will be used in spg.ilivein.xyz or book function in kijiji(ilivein.xyz)
	 */
	public function create_booking($event_id, $booking_start, $telephone, $customer_email, $booking_date, $customer_name, $desired_unit_id, $company_id, $employee_id, $potential_status = 0, $ps_modified_by = null, $source_type = 2, $moveInMonth = "default", $moveInYear = 1970) {
		try {
			//get the event_duration
			$this->crud->query("SELECT event_duration FROM event_infos WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$duration    = $this->crud->resultSingle();
			$booking_end = date("H:i:s", strtotime($booking_start) + 60 * $duration['event_duration']);
			$entry_time  = date("Y-m-d H:i:s");

			//creating booking records
			$this->crud->query("INSERT INTO booking_infos (event_id, booking_start, telephone, customer_email, booking_date, customer_name, booking_end, visitor_desired_unit, potential_status, ps_modified_by, source_type,entry_time,company_id,employee_id,move_in_month,is_booking_confirmed,move_in_year) VALUES (:event_id, :booking_start, :telephone, :customer_email, :booking_date, :customer_name, :booking_end, :desired_unit_id, :potential_status, :ps_modified_by, :source_type, :entry_time, :company_id, :employee_id,:move_in_month,:is_booking_confirmed,:move_in_year)");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':booking_start', $booking_start);
			$this->crud->bind(':telephone', $telephone);
			$this->crud->bind(':customer_email', $customer_email);
			$this->crud->bind(':booking_date', $booking_date);
			$this->crud->bind(':customer_name', $customer_name);
			$this->crud->bind(':booking_end', $booking_end);
			$this->crud->bind(":desired_unit_id", $desired_unit_id);
			$this->crud->bind(":potential_status", $potential_status);
			$this->crud->bind(":ps_modified_by", $ps_modified_by);
			$this->crud->bind(":source_type", $source_type);
			$this->crud->bind(":entry_time", $entry_time);
			$this->crud->bind(":company_id", $company_id);
			$this->crud->bind(":employee_id", $employee_id);
			$this->crud->bind(":move_in_month", $moveInMonth);
			$this->crud->bind(":is_booking_confirmed", 0);
			$this->crud->bind(":move_in_year", $moveInYear);

			$this->crud->execute();
			return $this->crud->lastInsertId();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------- visit event booking --------------------

	/**
	 * get all visit_event bookings for a specific visit_event template on specific date
	 */
	public function get_bookings($event_id, $book_event_date) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE event_id = :event_id AND booking_date = :book_event_date");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':book_event_date', $book_event_date);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_bookings_pending($event_id, $book_event_date) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE event_id = :event_id AND booking_date = :book_event_date AND is_booking_confirmed = 0");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':book_event_date', $book_event_date);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_bookings_confirmed($event_id, $book_event_date) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE event_id = :event_id AND booking_date = :book_event_date AND is_booking_confirmed = 1");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':book_event_date', $book_event_date);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event bookings for a specific visit_event template
	 */
	public function get_all_bookings($event_id) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE event_id = :event_id ORDER BY booking_date, booking_start ASC");
			$this->crud->bind(':event_id', $event_id);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_all_bookings_eid($employee_id) {
		try {
			$this->crud->query("SELECT * FROM booking_infos boi JOIN apartment_infos ai ON boi.visitor_desired_unit = ai.apartment_id JOIN building_infos bi ON ai.building_id = bi.building_id WHERE boi.employee_id = :employee_id AND boi.is_booking_confirmed = 0 ORDER BY booking_date, booking_start ASC");
			$this->crud->bind(':employee_id', $employee_id);
			return $this->crud->resultSet();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_confirmed_bookings_eid($employee_id) {
		try {
			$this->crud->query("SELECT * FROM booking_infos boi JOIN apartment_infos ai ON boi.visitor_desired_unit = ai.apartment_id JOIN building_infos bi ON ai.building_id = bi.building_id WHERE boi.employee_id = :employee_id AND boi.is_booking_confirmed = 1 ORDER BY booking_date, booking_start ASC");
			$this->crud->bind(':employee_id', $employee_id);
			return $this->crud->resultSet();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * cancel a specific booking
	 */
	public function cancel_booking($booking_id) {
		try {
			$this->crud->query("DELETE FROM booking_infos WHERE id = :booking_id");
			$this->crud->bind(':booking_id', $booking_id);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}

	}

	/**
	 * get all bookings for a specific visit_event template at a time(data, time)
	 */
	public function get_bookings_by_time($event_id, $booking_date, $booking_start) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE event_id = :event_id AND booking_date = :booking_date AND booking_start = :booking_start");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':booking_date', $booking_date);
			$this->crud->bind(':booking_start', $booking_start);
			$results = $this->crud->resultSet();

			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}

	}

	/**
	 * add one office_maintenance event record
	 */
	public function create_office_maintenance_event($building_id, $event_category, $event_name, $event_date, $contact_person, $contact_number, $event_created_by, $event_frequency, $event_frequency_type, $event_type, $event_info, $created_date, $request_type_id) {
		try {
			//insert records
			$this->crud->query("INSERT INTO office_maintenance_event_infos (event_name,event_date, person_in_contact,contact_number,event_created_by_user_id, event_frequency, event_frequency_type, event_type ,event_info,building_id,event_category,created_date,request_type_id) VALUES (:event_name, :event_date, :person_in_contact,:contact_number,:event_created_by_user_id, :event_frequency, :event_frequency_type, :event_type ,:event_info,:building_id, :event_category, :created_date,:request_type_id)");
			$this->crud->bind(':event_name', $event_name);
			$this->crud->bind(':event_date', $event_date);
			$this->crud->bind(':person_in_contact', $contact_person);
			$this->crud->bind(':contact_number', $contact_number);
			$this->crud->bind(':event_created_by_user_id', $event_created_by);
			$this->crud->bind(':event_frequency', $event_frequency);
			$this->crud->bind(':event_frequency_type', $event_frequency_type);
			$this->crud->bind(':event_type', $event_type);
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':event_info', $event_info);
			$this->crud->bind(':event_category', $event_category);
			$this->crud->bind(':created_date', $created_date);
			$this->crud->bind(':request_type_id', $request_type_id);
			$this->crud->execute();

			//get records id
			$event_id = $this->crud->lastInsertId();
			return $event_id;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------ office & maintenance events ----------------

	/**
	 * get info of office_maintenance event
	 */
	public function get_office_maintenance_events($event_category, $building_id, $employee_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE event_category = :event_category AND building_id = :building_id AND id IN (select office_maintenance_event_id from office_maintenance_event_assigntos where assigned_user_id=:user_id)");
			$this->crud->bind(':event_category', $event_category);
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':user_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all office_maintenance events for a employee
	 * because the employee may be involved with several buildings
	 * event_category:[office,maintenance]
	 */
	public function get_personal_office_maintenance_events($event_category, $employee_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE event_category = :event_category AND id IN (select office_maintenance_event_id from office_maintenance_event_assigntos where assigned_user_id=:user_id)");
			$this->crud->bind(':event_category', $event_category);
			$this->crud->bind(':user_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all office_maintenance events for ADMIN users
	 * event_category:[office,maintenance]
	 */
	public function get_personal_office_maintenance_events_admin($event_category) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE event_category = :event_category");
			$this->crud->bind(':event_category', $event_category);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * get office_maintenance event list for a employee in a building
	 */
	public function get_office_maintenance_events_list($building_id, $employee_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE building_id = :building_id AND id IN (select office_maintenance_event_id from office_maintenance_event_assigntos where assigned_user_id=:user_id) ORDER BY event_category DESC");
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':user_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete a office_maintenance event
	 */
	public function delete_office_maintenance_event($event_id) {
		try {
			//delete all assigned records
			$this->crud->query("DELETE FROM office_maintenance_event_assigntos WHERE office_maintenance_event_id =:event_id");
			$this->crud->bind('event_id', $event_id);
			$this->crud->execute();

			//delete event records
			$this->crud->query("DELETE FROM office_maintenance_event_infos WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the details of info about the one office_maintenance event
	 * info: person_in_contact, building_info ...
	 */
	public function get_office_maintenance_event_detail($event_id) {
		try {
			$this->crud->query("SELECT event_name,event_date, person_in_contact, contact_number, event_created_by_user_id, event_frequency,event_frequency_type, event_type, event_info, office_maintenance_event_infos.building_id AS building_id, building_name, address,event_category,created_date FROM office_maintenance_event_infos,building_infos WHERE office_maintenance_event_infos.building_id = building_infos.building_id AND id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the office_maintenance event
	 */
	public function update_office_maintenance_event($event_id, $event_name, $event_date, $person_in_contact, $contact_number, $event_frequency, $event_frequency_type, $event_type, $event_info) {
		try {
			$this->crud->query("UPDATE office_maintenance_event_infos SET event_name = :event_name, event_date = :event_date, person_in_contact = :person_in_contact, contact_number = :contact_number, event_frequency = :event_frequency,event_frequency_type = :event_frequency_type, event_type = :event_type, event_info = :event_info WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->bind(':event_name', $event_name);
			$this->crud->bind(':event_date', $event_date);
			$this->crud->bind(':person_in_contact', $person_in_contact);
			$this->crud->bind(':contact_number', $contact_number);
			$this->crud->bind(':event_frequency', $event_frequency);
			$this->crud->bind(':event_frequency_type', $event_frequency_type);
			$this->crud->bind(':event_type', $event_type);
			$this->crud->bind(':event_info', $event_info);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get teh office_maintenance events that the employee created in a building
	 */
	public function get_created_office_maintenance_events($building_id, $employee_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE building_id=:building_id AND event_created_by_user_id=:event_created_by_user_id ORDER BY created_date DESC ");
			$this->crud->bind(':building_id', $building_id);
			$this->crud->bind(':event_created_by_user_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * upload a attachment for a office_maintenance event
	 */
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
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the attachments list for a office_maintenance event
	 */
	public function get_event_uploads($event_id) {

		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_uploads WHERE event_id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSet();

			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete a attachment for a upload attachment
	 */
	public function delete_upload($upload_id) {

		try {
			$this->crud->query("DELETE FROM office_maintenance_event_uploads WHERE id = :upload_id");
			$this->crud->bind(':upload_id', $upload_id);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * assign the office_maintenance event to someone else
	 * someone else maybe employee / third-party principal
	 * If someone else is employee, record his user_id in assigned_user_id field
	 * If someone else is third-party principal, add the info to office_maintenance_event_principal_info table and then add this id to assigned_user_id field
	 */
	public function assign_event($event_id, $assigned_user_id, $assigned_employee_id, $assigned_manager_id, $assigned_owner_id, $assigned_other_principal_id) {
		try {
			$this->crud->query("INSERT INTO office_maintenance_event_assigntos(office_maintenance_event_id,assigned_user_id,assigned_employee_id,assigned_manager_id,assigned_owner_id,assigned_other_principal_id) VALUES (:office_maintenance_event_id,:assigned_user_id,:assigned_employee_id,:assigned_manager_id,:assigned_owner_id,:assigned_other_principal_id)");
			$this->crud->bind(':office_maintenance_event_id', $event_id);
			$this->crud->bind(':assigned_user_id', $assigned_user_id);
			$this->crud->bind(':assigned_employee_id', $assigned_employee_id);
			$this->crud->bind(':assigned_manager_id', $assigned_manager_id);
			$this->crud->bind(':assigned_owner_id', $assigned_owner_id);
			$this->crud->bind(':assigned_other_principal_id', $assigned_other_principal_id);
			$this->crud->execute();
			return true;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the assignee info list for a office_maintenance event
	 */
	public function get_event_assgintos_info($event_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_assigntos WHERE office_maintenance_event_id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the assignee detail info (employee_id, full_name, email, mobile) for a office_maintenance event
	 */
	public function get_event_assigntos_details($event_id) {
		try {
			$this->crud->query("SELECT employee_infos.employee_id AS id,employee_infos.full_name AS full_name ,employee_infos.email AS email,employee_infos.mobile AS telephone  FROM office_maintenance_event_assigntos,employee_infos WHERE assigned_user_id=employee_infos.employee_id AND office_maintenance_event_id=:event_id AND assigned_user_id >0 ");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete all assigntos info about for a office_maintenance event
	 */
	public function delete_event_all_assigntos($event_id) {
		try {
			$this->crud->query("DELETE FROM office_maintenance_event_assigntos WHERE office_maintenance_event_id= :event_id");
			$this->crud->bind(':event_id', $event_id);
			$this->crud->execute();
			return true;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all employees from the company where the employee is in
	 */
	public function get_same_company_staff($employee_id) {
		try {
			$this->crud->query("select * from employee_infos where company_id = (select company_id from employee_infos where employee_id=:id)");
			$this->crud->bind(':id', $employee_id);
			$results = $this->crud->resultSet();
			return $results;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * create principal info
	 * Hint: principal here is the person involved with a event, but he is not in the system -> third-party principal
	 * all these info keeps in office_maintenance_event_principal_info table
	 */
	public function create_principal($building_id, $name, $email, $telephone) {
		try {
			//INSERT
			$this->crud->query("INSERT INTO office_maintenance_event_principal_info(principal_name,principal_email,principal_telephone,building_id) VALUES (:principal_name,:principal_email,:principal_telephone,:building_id)");
			$this->crud->bind(':principal_name', $name);
			$this->crud->bind(':principal_email', $email);
			$this->crud->bind(':principal_telephone', $telephone);
			$this->crud->bind(':building_id', $building_id);
			$this->crud->execute();

			//GET record_id
			$id = $this->crud->lastInsertId();
			return $id;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}



	//--------------- office $maintenance events -- other principals (third-party)-------------

	/**
	 * get the info about third-party principal
	 */
	public function get_principal_info($principal_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_principal_info WHERE id =:id");
			$this->crud->bind(":id", $principal_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * use principal name,email,telephone,building_id to match the pricinpal info in office_maintenance_event_principal_info table
	 * office_maintenance_event_principal_info table keeps the info about third-party principal
	 */
	public function get_principal_id($principal_name, $principal_email, $principal_telephone, $building_id) {
		try {
			$this->crud->query("SELECT id FROM office_maintenance_event_principal_info WHERE principal_name =:principal_name AND principal_email=:principal_email AND principal_telephone=:principal_telephone AND building_id=:building_id");
			$this->crud->bind(':principal_name', $principal_name);
			$this->crud->bind(':principal_email', $principal_email);
			$this->crud->bind(':principal_telephone', $principal_telephone);
			$this->crud->bind(':building_id', $building_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all visit_event templates for a specific building
	 */
	public function get_event_ids($building_id) {
		try {
			$this->crud->query("SELECT id FROM event_infos WHERE building_id = :building_id");
			$this->crud->bind(":building_id", $building_id);
			$result = $this->crud->resultSet();
			return $result;

		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//-------------------- front end booking slots -----------------------
	// all method in this part is invoked from spg.ilivein.xyz

	/**
	 * get the detail of a visit_event template
	 */
	public function get_event($event_id) {
		try {
			$this->crud->query("SELECT * FROM event_infos WHERE id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSingle();

			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the full_name, mobile, email for all kind of user
	 * If user_id is in range(0,100000), it will get info from employee_infos table
	 * If user_id is in range(100000,200000), it will get info from tenant_infos table
	 * If user_id is in (200000, ), it will get info from owner_infos table
	 */
	public function get_user_info($user_id) {
		if ($user_id > 100000 and $user_id < 200000)
			$query = "SELECT full_name, mobile, email FROM tenant_infos WHERE tenant_id =:user_id";
		else if ($user_id > 200000)
			$query = "SELECT full_name,mobile,email FROM owner_infos WHERE owner_id = :user_id";
		else
			$query = "SELECT full_name, email,mobile FROM employee_infos WHERE employee_id =:user_id";

		try {
			$this->crud->query($query);
			$this->crud->bind("user_id", $user_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//---------------------- others --------------------------

	/**
	 * get the building_name, apartment_name for a apartment_id
	 */
	public function get_building_apartment_info_from_apartment_id($apartment_id) {
		try {
			$this->crud->query("SELECT building_infos.building_name AS building_name, building_infos.building_id AS building_id, apartment_infos.unit_number AS apartment FROM building_infos, apartment_infos WHERE apartment_infos.building_id = building_infos.building_id AND apartment_infos.apartment_id = :apartment_id");
			$this->crud->bind(':apartment_id', $apartment_id);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * create handyman configuration
	 * It will be invoked by system automatically[when a handyman enter repair event config in Calendar, if there is not a record related to him, it will be added auto]
	 */
	public function create_handyman_config($handyman_id, $event_duration = 15, $max_books_daily = 1, $availability_increment = 15, $buffer_before = 0, $buffer_after = 0, $book_event_before = 1) {
		try {
			$this->crud->query("INSERT INTO handyman_configurations (handyman_id, event_duration, max_books_daily, availability_increment, buffer_before, buffer_after, book_event_before) VALUES (:handyman_id, :event_duration, :max_books_daily, :availability_increment, :buffer_before, :buffer_after, :book_event_before)");
			$this->crud->bind(':handyman_id', $handyman_id);
			$this->crud->bind(':event_duration', $event_duration);
			$this->crud->bind(':max_books_daily', $max_books_daily);
			$this->crud->bind(':availability_increment', $availability_increment);
			$this->crud->bind(':buffer_before', $buffer_before);
			$this->crud->bind(':buffer_after', $buffer_after);
			$this->crud->bind(':book_event_before', $book_event_before);
			$this->crud->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the config settings for a handyman
	 */
	public function update_handyman_config($handyman_id, $event_duration, $max_books_daily, $availability_increment, $buffer_before, $buffer_after, $book_event_before) {
		try {
			$this->crud->query("UPDATE handyman_configurations SET event_duration = :event_duration, max_books_daily = :max_books_daily, availability_increment = :availability_increment, buffer_before = :buffer_before, buffer_after = :buffer_after, book_event_before = :book_event_before WHERE handyman_id = :handyman_id");
			$this->crud->bind(':handyman_id', $handyman_id);
			$this->crud->bind(':event_duration', $event_duration);
			$this->crud->bind(':max_books_daily', $max_books_daily);
			$this->crud->bind(':availability_increment', $availability_increment);
			$this->crud->bind(':buffer_before', $buffer_before);
			$this->crud->bind(':buffer_after', $buffer_after);
			$this->crud->bind(':book_event_before', $book_event_before);
			$this->crud->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//---------------------------- handyman calendar -----------------------------------

	/**
	 * get the detail of the handyman config
	 */
	public function get_handyman_config($handyman_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_configurations WHERE handyman_id = :handyman_id");
			$this->crud->bind(":handyman_id", $handyman_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * create handyman slot for specific handyman in specific building
	 * one handyman can be available for several buildings(building_id)
	 */
	public function create_handyman_avail_slot($handyman_id, $building_id, $slot_date, $slot_start_time, $slot_end_time) {
		try {
			$this->crud->query("INSERT INTO handyman_available_slots(handyman_id, building_id, slot_date, slot_start_time, slot_end_time) VALUES (:handyman_id, :building_id, :slot_date, :slot_start_time, :slot_end_time)");
			$this->crud->bind(":handyman_id", $handyman_id);
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":slot_date", $slot_date);
			$this->crud->bind(":slot_start_time", $slot_start_time);
			$this->crud->bind(":slot_end_time", $slot_end_time);
			$this->crud->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the handyman slot time
	 * only allow to update start time and end time
	 */
	public function update_handyman_avail_slot_info($id, $slot_start_time, $slot_end_time) {
		try {
			$this->crud->query("UPDATE handyman_available_slots SET slot_start_time = :slot_start_time , slot_end_time = :slot_end_time WHERE id = :id");
			$this->crud->bind(":id", $id);
			$this->crud->bind(":slot_start_time", $slot_start_time);
			$this->crud->bind(":slot_end_time", $slot_end_time);
			$this->crud->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete the handyman slot
	 */
	public function delete_handyman_avail_slot($id) {
		try {
			$this->crud->query("DELETE FROM handyman_available_slots WHERE id = :id");
			$this->crud->bind(":id", $id);
			$this->crud->execute();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get available handyman slot for handyman, based on building
	 * the slot list in every building is independent with each other
	 */
	public function get_handyman_avail_slot($handyman_id, $building_id) {
		try {
			$today = date('Y-m-d');
			$this->crud->query("SELECT * FROM handyman_available_slots WHERE handyman_id = :handyman_id AND building_id = :building_id AND slot_date > :today  ORDER BY slot_date");
			$this->crud->bind(":handyman_id", $handyman_id);
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":today", $today);
			return $this->crud->resultSet();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get handyman books for a available slot
	 */
	public function get_handyman_bookings($avail_slot_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_bookings WHERE avail_slot_id = :avail_slot_id");
			$this->crud->bind(":avail_slot_id", $avail_slot_id);
			return $this->crud->resultSet();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the info of handyman slot
	 */
	public function get_handyman_avail_info($avail_slot_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_available_slots WHERE id = :id");
			$this->crud->bind(":id", $avail_slot_id);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get info of a handyman booking
	 */
	public function get_one_handyman_booking($handyman_booking_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_bookings WHERE id = :id");
			$this->crud->bind(":id", $handyman_booking_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one handyman booking
	 */
	public function add_one_handyman_booking($avail_slot_id, $tenant_id, $apartment_id, $book_from, $book_to, $is_confirmed) {
		try {
			$this->crud->query("INSERT INTO handyman_bookings(avail_slot_id, tenant_id, apartment_id, book_from, book_to, is_confirmed) VALUES (:avail_slot_id, :tenant_id, :apartment_id, :book_from, :book_to, :is_confirmed)");
			$this->crud->bind(":avail_slot_id", $avail_slot_id);
			$this->crud->bind(":tenant_id", $tenant_id);
			$this->crud->bind(":apartment_id", $apartment_id);
			$this->crud->bind(":book_from", $book_from);
			$this->crud->bind(":book_to", $book_to);
			$this->crud->bind(":is_confirmed", $is_confirmed);
			$this->crud->execute();

			return $this->crud->lastInsertId();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all handyman booking for a handyman
	 * The handyman may be available for several building (infos here collect all bookings from all building)
	 * the function is used in personal(total) calendar
	 */
	public function get_all_handyman_bookings_for_handyman($handyman_employee_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_bookings WHERE avail_slot_id IN (SELECT id FROM handyman_available_slots WHERE handyman_id = :handyman_id) AND is_confirmed = 2");
			$this->crud->bind(":handyman_id", $handyman_employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all handyman booking for a handyman
	 * infos here just bases on one building, will not collect from all building
	 */
	public function get_all_handyman_bookings_for_handyman_building($handyman_employee_id, $building_id) {
		try {
			$this->crud->query("SELECT * FROM handyman_bookings WHERE avail_slot_id IN (SELECT id FROM handyman_available_slots WHERE handyman_id = :handyman_id AND building_id = :building_id) AND is_confirmed = 2");
			$this->crud->bind(":handyman_id", $handyman_employee_id);
			$this->crud->bind(":building_id", $building_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function setNotificationPreference($eventDetails, $preference) {
		try {
			$this->crud->query("INSERT INTO event_notifications (event_id, sms_notify, email_notify, voice_notify, event_type, frequency, event_date, event_category, notify_when_type, notify_when) VALUES (:event_id, :sms_notify, :email_notify, :voice_notify, :event_type, :frequency, :event_date, :event_category, :notify_when_type, :notify_when)");
			$this->crud->bind(':event_id', $eventDetails["event_id"]);
			$this->crud->bind(':sms_notify', $preference["sms"]);
			$this->crud->bind(':email_notify', $preference["email"]);
			$this->crud->bind(':voice_notify', $preference["voice"]);
			$this->crud->bind(':event_type', $eventDetails["type"]);
			$this->crud->bind(':frequency', $eventDetails["frequency"]);
			$this->crud->bind(':event_date', $eventDetails["date"]);
			$this->crud->bind(':event_category', $eventDetails["category"]);
			$this->crud->bind(':notify_when_type', $eventDetails["notify_when_type"]);
			$this->crud->bind(':notify_when', $eventDetails["notify_when"]);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateNotificationPreference($eventDetails, $preference) {
		try {
			$this->crud->query("UPDATE event_notifications SET sms_notify = :sms_notify, email_notify = :email_notify, voice_notify = :voice_notify, frequency = :frequency, event_date = :event_date, notify_when_type = :notify_when_type, notify_when = :notify_when WHERE event_id = :event_id");
			$this->crud->bind(':event_id', $eventDetails["event_id"]);
			$this->crud->bind(':sms_notify', $preference["sms"]);
			$this->crud->bind(':email_notify', $preference["email"]);
			$this->crud->bind(':voice_notify', $preference["voice"]);
			$this->crud->bind(':frequency', $eventDetails["frequency"]);
			$this->crud->bind(':event_date', $eventDetails["date"]);
			$this->crud->bind(':notify_when_type', $eventDetails["notify_when_type"]);
			$this->crud->bind(':notify_when', $eventDetails["notify_when"]);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//-------------------------------- From Sharan ---------------------------------

	// Sets the notification preference for the Event ID specified

	public function updateNotifs($eventId, $preference) {
		try {
			$this->crud->query("UPDATE event_notifications SET sms_notify = :sms_notify, email_notify = :email_notify, voice_notify = :voice_notify,notify_when_type = :notify_when_type, notify_when = :notify_when WHERE event_id = :event_id");
			$this->crud->bind(':event_id', $eventId);
			$this->crud->bind(':sms_notify', $preference["sms"]);
			$this->crud->bind(':email_notify', $preference["email"]);
			$this->crud->bind(':voice_notify', $preference["voice"]);
			$this->crud->bind(':notify_when_type', $preference["notify_when_type"]);
			$this->crud->bind(':notify_when', $preference["notify_when"]);
			$this->crud->execute();
			return TRUE;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}

	}

	public function getNotificationPreference($eventId) {
		try {
			$this->crud->query("SELECT * FROM event_notifications WHERE event_id = :event_id");
			$this->crud->bind(':event_id', $eventId);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Just update the sms / email / voice details and the notification when type unliked the above method

	public function getAllNotificationPreferences() {
		try {
			$this->crud->query("SELECT * FROM event_notifications");
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Get the notification preference for an Event ID

	public function get_office_maintenance_eventswo_bid($employee_id) {
		try {
			$this->crud->query("SELECT * FROM office_maintenance_event_infos WHERE id IN (select office_maintenance_event_id from office_maintenance_event_assigntos where assigned_user_id=:user_id) ORDER BY id DESC");
			$this->crud->bind(':user_id', $employee_id);
			$result = $this->crud->resultSet();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Confirm the booking when the agent
	 */
	public function updateBookingConfirmation($bookingId) {
		try {
			$this->crud->query("UPDATE booking_infos SET is_booking_confirmed = :is_booking_confirmed WHERE id = :booking_id");
			$this->crud->bind(':booking_id', $bookingId);
			$this->crud->bind(':is_booking_confirmed', 1);
			$this->crud->execute();
			return $this->crud->rowCount();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateBookingData($bookingId, $data) {
		try {
			$this->crud->query("SELECT event_duration FROM event_infos WHERE id = :event_id");
			$this->crud->bind(':event_id', $data["event_id"]);
			$duration    = $this->crud->resultSingle();
			$booking_end = date("H:i:s", strtotime($data["startTime"]) + 60 * $duration['event_duration']);

			$this->crud->query("UPDATE booking_infos SET booking_start = :booking_start, telephone = :telephone, customer_email = :customer_email, booking_date = :booking_date, customer_name = :customer_name, booking_end = :booking_end, move_in_month = :move_in_month WHERE id = :booking_id");

			$this->crud->bind(':booking_id', $bookingId);
			$this->crud->bind(':telephone', $data["visitor_phone"]);
			$this->crud->bind(':customer_email', $data["visitor_email"]);
			$this->crud->bind(':booking_date', $data["book_event_date"]);
			$this->crud->bind(':booking_start', $data["startTime"]);
			$this->crud->bind(':customer_name', $data["visitor_name"]);
			$this->crud->bind(':booking_end', $booking_end);
			$this->crud->bind(':move_in_month', $data["movein_month"]);

			$this->crud->execute();
			return $this->crud->rowCount();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Get the booking details for a booking ID */
	public function getBookingInfo($bookingId) {
		try {
			$this->crud->query("SELECT * FROM booking_infos WHERE id = :booking_id");
			$this->crud->bind(':booking_id', $bookingId);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getQuestionInfo($questionId) {
		try {
			$this->crud->query("SELECT * FROM question_infos WHERE id = :question_id");
			$this->crud->bind(':question_id', $questionId);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getPotentialInfo($potentialId) {
		try {
			$this->crud->query("SELECT * FROM potential_tenant_infos WHERE id = :potential_id");
			$this->crud->bind(':potential_id', $potentialId);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Get the Assigned user ID for the booking Event
	 */
	public function getAssignedEmployeeId($eventId) {
		$row                              = $this->get_visit_event($eventId);
		$event_person_in_charge_id        = $row['resonsible_employee_id'];
		$event_person_in_charge           = $this->get_employee_info($event_person_in_charge_id);
		$event_company_id                 = $event_person_in_charge['company_id'];
		$event_person_in_charge_name      = $event_person_in_charge['full_name'];
		$event_person_in_charge_telephone = $event_person_in_charge['mobile'];
		$event_person_in_charge_telephone = formalize_telephone($event_person_in_charge_telephone);
		$event_person_in_charge_email     = $event_person_in_charge['email'];
	}

	/**
	 * get the detail information about visit_event template(event_infos table)
	 */
	public function get_visit_event($event_id) {
		try {
			$this->crud->query("SELECT event_infos.building_id ,building_name,address, event_name, event_location, event_duration, event_custom_duration, event_max_book_per_day,event_increment, event_buffer_before, event_buffer_after,event_less_hour, event_is_open, event_description, event_max_book_per_slot, event_rolling_week,resonsible_employee_id FROM event_infos,building_infos WHERE building_infos.building_id = event_infos.building_id AND id = :event_id");
			$this->crud->bind(':event_id', $event_id);
			$result = $this->crud->resultSingle();

			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the info of the employee
	 */
	public function get_employee_info($employee_id) {
		try {
			$this->crud->query("select * from employee_infos where employee_id= :employee_id");
			$this->crud->bind(':employee_id', $employee_id);
			$result = $this->crud->resultSingle();
			return $result;
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function addPotentialFollowUpRecord($data) {
		try {
			$this->crud->query("INSERT INTO potential_followups (followup, potential_id, source_type_id, followup_type, assign_to_employee_id, employee_id, company_id, followup_dt, visit_or_question, send_email) VALUES (:followup, :potential_id, :source_type_id, :followup_type, :assign_to_employee_id, :employee_id, :company_id, :followup_dt, :visit_or_question, :send_email)");
			$this->crud->bind(":followup", $data["followup"]);
			$this->crud->bind(":potential_id", $data["potentialId"]);
			$this->crud->bind(":source_type_id", $data["sourceType"]);
			$this->crud->bind(":followup_type", $data["followUpType"]);
			$this->crud->bind(":assign_to_employee_id", $data["assignToId"]);
			$this->crud->bind(":employee_id", $data["assignToId"]);
			$this->crud->bind(":company_id", $data["company_id"]);
			$this->crud->bind(":followup_dt", $data["entry_date"]);
			$this->crud->bind(":visit_or_question", $data["visitOrQuestion"]);
			$this->crud->bind(":send_email", 1);

			$this->crud->execute();
			return $this->crud->rowCount();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Update the booking_infos table with the user feedback for the visit */
	public function updateVisitFeedback($bookingId, $data) {
		try {
			$this->crud->query("UPDATE booking_infos SET apartment_feedback = :apartment_feedback, apartment_rating = :apartment_rating, agent_feedback = :agent_feedback, agent_rating = :agent_rating  WHERE id = :booking_id");

			$this->crud->bind(':booking_id', $bookingId);
			$this->crud->bind(':apartment_feedback', $data["feedback_apt"]);
			$this->crud->bind(':apartment_rating', $data["apartment"]);
			$this->crud->bind(':agent_feedback', $data["feedback_agent"]);
			$this->crud->bind(':agent_rating', $data["agent"]);

			$this->crud->execute();
			return $this->crud->rowCount();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getPotentialDetailFromView($potential_id, $visit_or_question) {
		try {
			$this->crud->query("SELECT * FROM view_questions_and_visits WHERE potential_id = :potential_id and visit_or_question = :visit_or_question");
			$this->crud->bind(':potential_id', $potential_id);
			$this->crud->bind(':visit_or_question', $visit_or_question);
			return $this->crud->resultSingle();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function approve_credit_check($potentialSource, $potentialId) {
		$tableName = "potential_tenant_infos";
		switch ($potentialSource) {
			case 0:
				$tableName = "booking_infos";
				break;
			case 1:
				$tableName = "question_infos";
				break;
			case 2:
				$tableName = "potential_tenant_infos";
				break;
		}

		try {
			$this->crud->query("UPDATE $tableName SET is_creditcheck_confirmed = 1 WHERE id = :potential_id");
			$this->crud->bind(':potential_id', $potentialId);
			$this->crud->execute();
			return $this->crud->rowCount();
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


}
