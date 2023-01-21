<?php
  include ("pdo/dbconfig.php");
  date_default_timezone_set('America/New_York');
  $building_id = $_GET['building_id'];
  $event_category = $_GET['event_category'];

  $result = $DB_event->get_calendar_events($building_id, $event_category);
  $time_limit = date('Y-m-d', strtotime('today + 1 year'));
  $out = array();
  foreach ( $result as $row ) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    if($event_type == "regular") {
      $current_date = $event_date . " 09:00:00";
      while($current_date <= $time_limit) {
        $out[] = array(
          'id' => $event_id,
          'title' => $event_name,
          'url' => "event-details.php?" . "&event_id=" . $event_id,
          "class" => "event-info",
          'start' => strtotime($current_date) . '000',
          'end' => strtotime($current_date) .'000'
        );

        $current_date = date('Y-m-d H:i:s', strtotime($current_date . ' + ' . $event_frequency . ' ' . $event_frequency_type));
      }
    }
    else {
      $out[] = array(
        'id' => $event_id,
        'title' => $event_name,
        'url' => "event-details.php?" . "&event_id=" . $event_id,
        "class" => "event-important",
        'start' => strtotime($event_date) . '000',
        'end' => strtotime($event_date) .'000'
      );
    }
  }

  echo json_encode(array('success' => 1, 'result' => $out));
  exit;
?>