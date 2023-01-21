<?php

class action_event {

	public function create_event($event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day, $event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, $event_max_book_per_slot) {
		require('db_connection.php');

		$sql = "INSERT INTO event_infos (event_name, event_location, event_duration, event_custom_duration, event_max_book_per_day, event_increment, event_buffer_before, event_buffer_after, event_less_hour, event_description, event_max_book_per_slot) VALUES ('$event_name', '$event_location', '$event_duration', '$event_custom_duration', '$event_max_book_per_day', '$event_increment', '$event_buffer_before', '$event_buffer_after', '$event_less_hour', '$event_description', '$event_max_book_per_slot')";
		
		if ($conn->query($sql) === TRUE) {
			$event_id = $conn->insert_id;
			return $event_id;
		} else {
			return null;
		}
		mysqli_close($conn);
	}

	public function update_event($event_id, $event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day, $event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, $event_max_book_per_slot) {
		require('db_connection.php');

		$sql = "UPDATE event_infos SET event_name = '$event_name', event_location = '$event_location', event_duration = '$event_duration', event_custom_duration = '$event_custom_duration', event_max_book_per_day = '$event_max_book_per_day', event_increment = '$event_increment', event_buffer_before = '$event_buffer_before', event_buffer_after = '$event_buffer_after', event_less_hour = '$event_less_hour', event_description = '$event_description', event_max_book_per_slot = '$event_max_book_per_slot' WHERE id = '$event_id'";
		
		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
		mysqli_close($conn);
	}

	public function delete_event($event_id) {
		require('db_connection.php');

		$sql = "DELETE FROM event_infos WHERE id = $event_id" ;

		if ($conn->query($sql) === TRUE) {

		} else {
			return FALSE;
		}

		$sql = "DELETE FROM event_availabilities WHERE event_id = $event_id" ;

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
		mysqli_close($conn);
	}

	public function get_event($event_id) {
		require('db_connection.php');	

		$sql = "SELECT * FROM event_infos WHERE id = '$event_id'";

		$result = $conn->query($sql);
		if (!is_null($result)) {
			return $result;
		} else {
			return null;
		}
		mysqli_close($conn);
	}

	public function get_events_oneonone() {
		require('db_connection.php');	

		$sql = "SELECT * FROM event_infos WHERE event_max_book_per_slot = 1";

		$result = $conn->query($sql);
		if (!is_null($result)) {
			return $result;
		} else {
			return null;
		}
		mysqli_close($conn);
	}

	public function get_events_group() {
		require('db_connection.php');	

		$sql = "SELECT * FROM event_infos WHERE event_max_book_per_slot > 1";

		$result = $conn->query($sql);
		if (!is_null($result)) {
			return $result;
		} else {
			return null;
		}
		mysqli_close($conn);
	}
}
?>