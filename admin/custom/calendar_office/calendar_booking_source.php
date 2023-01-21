<?php
  include ("pdo/dbconfig.php");
  $event_id = 9;

  $row = $DB_calendar->get_event($event_id);
  $event_name = $row['event_name'];
  
  $result = $DB_calendar->get_all_bookings($event_id);
  $out = array();
  foreach ( $result as $row ) {
    $out[] = array(
      'id' => $row['id'],
      'title' => $event_name . " " . $row['booking_start'],
      'url' => "booking-details.php?" . "booking_date=" . $row['booking_date'] . "&booking_start=" . $row['booking_start'] . "&event_id=" . $event_id,
      "class" => "event-important",
      'start' => strtotime($row['booking_date']) . '000',
      'end' => strtotime($row['booking_date']) .'000'
      );
  }

  echo json_encode(array('success' => 1, 'result' => $out));
  exit;
?>