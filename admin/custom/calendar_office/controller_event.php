<?php
session_start();
include_once ("pdo/dbconfig.php");

if(true) {

	if (isset($_POST['create_event'])) { 
		$building_id = $_POST['building_id'];
		$event_name = $_POST['event_name'];
		$event_contact = $_POST['event_contact'];
		$event_date = $_POST['event_date'];
		$event_info = $_POST['event_info'];
		$event_preparation = $_POST['event_preparation'];
		$event_type = $_POST['event_type'];
		$event_category = $_POST['event_category'];

		if($event_type == "regular") {
			$event_frequency = $_POST['event_frequency'];
			$event_frequency_type = $_POST['event_frequency_type'];
		}
		else {
			$event_frequency = 0;
			$event_frequency_type = "";			
		}

		$result = $DB_event->create_event($building_id, $event_name, $event_contact, $event_date, $event_frequency, $event_frequency_type, $event_info, $event_preparation, $event_type, $event_category);
		if($result) {
			header('Location: event-list.php'."?building_id=$building_id");
		} 
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['delete_event'])) { 

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];

		$result = $DB_event->delete_event($event_id);

		if($result) {
			header('Location: event-list.php'."?building_id=$building_id");
		} 
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_event'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];

		header('Location: details-event.php?event_id='.$event_id.'&building_id='.$building_id);

	}

	else if (isset($_POST['update_event'])) { 
		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		$calendar_id = 1;
		$event_name = $_POST['event_name'];
		$event_contact = $_POST['event_contact'];
		$event_date = $_POST['event_date'];
		$event_info = $_POST['event_info'];
		$event_preparation = $_POST['event_preparation'];
		$event_type = $_POST['event_type'];
		$event_category = $_POST['event_category'];

		if($event_type == "regular") {
			$event_frequency = $_POST['event_frequency'];
			$event_frequency_type = $_POST['event_frequency_type'];
		}
		else {
			$event_frequency = 0;
			$event_frequency_type = "";			
		}

		$result = $DB_event->update_event($event_id, $calendar_id, $event_name, $event_contact, $event_date, $event_frequency, $event_frequency_type, $event_info, $event_preparation, $event_type, $event_category);
		if($result) {
			header('Location: details-event.php?event_id='.$event_id.'&building_id='.$building_id);
		} 
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['upload_file']) && $_FILES["file_to_upload"]["name"] != null) {

		$event_id = $_POST['event_id'];
		date_default_timezone_set('America/New_York');
		$upload_date = date('Y-m-d H:i:s');
		$upload_name = basename($_FILES["file_to_upload"]["name"]);
		$upload_id = $DB_event->upload($event_id, $upload_name, $upload_date);

		if(!is_null($upload_id)) {
			$target_dir = "uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name;
			move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_dir);
			header('Location: details-event.php?event_id=' . $event_id);		
		} 
		else {
			echo "Error!";
		}	

	}

	else if (isset($_POST['delete_upload'])) {
		$event_id = $_POST['event_id']; 
		$upload_id = $_POST['upload_id'];
		$upload_name = $_POST['upload_name'];
		$result = $DB_event->delete_upload($upload_id);

		if($result) {
			$upload_url = "./uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name; 
			unlink($upload_url);
			header('Location: details-event.php?event_id=' . $event_id);			
		} 
		else {
			echo "Error!";
		}
	}
}
?>