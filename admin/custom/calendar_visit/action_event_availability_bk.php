<?php

class action_event_availability {

	public function add_event_availability($event_id, $availability_start, $availability_end, $availability_type, $availability_specific_date) {
		require('db_connection.php');

		$sql = "INSERT INTO event_availabilities (event_id, availability_start, availability_end, availability_type, availability_specific_date)	
				VALUES ('$event_id', '$availability_start', '$availability_end', '$availability_type', '$availability_specific_date')";

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
		mysqli_close($conn);
	}

	public function edit_event_availability($availability_id, $availability_start, $availability_end) {
		require('db_connection.php');

		$sql = "UPDATE event_availabilities SET availability_start = '$availability_start', availability_end = '$availability_end' WHERE id = '$availability_id'";

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
		mysqli_close($conn);
	}

	public function delete_event_availability($availability_id) {
		require('db_connection.php');

		$sql = "DELETE FROM event_availabilities WHERE id = $availability_id" ;

		if ($conn->query($sql) === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
		mysqli_close($conn);
	}

	public function get_event_availability($event_id) {
		require('db_connection.php');
            
        $sql = "SELECT * FROM event_availabilities WHERE event_id = '$event_id' ORDER BY FIELD(availability_type,'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'specific')";
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