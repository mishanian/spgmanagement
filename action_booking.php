<?php

		
class action_booking {
	
	public $DB_calendar;

	function __construct(){
        require("pdo/dbconfig.php");
        include_once('pdo/Class.Calendar.php');
        $this->DB_calendar = new Calendar($DB_con);
        date_default_timezone_set('America/New_York');
    }
	
    function get_availablility_dates($building_id){
        $today = date('Y-m-d', strtotime('today'));
        $max_date = date('Y-m-d', strtotime('today + 1 week'));
        $dates = array();

        $event_ids=$this->DB_calendar->get_event_ids($building_id);
        foreach($event_ids as $r){
			$event_id=$r['id'];
            $availabilities = $this->DB_calendar->get_event_availability($event_id);
            foreach ( $availabilities as $row ) {
                $specific_date = $row['availability_specific_date'];
                if(!in_array($specific_date, $dates)) {
                    if($specific_date <= $max_date && $specific_date >= $today) {
                        $dates[] = $specific_date;
                    }
                }
            }
        }

        //sort the date
        usort($dates, function($date1, $date2) {
            $d1 = strtotime($date1);
            $d2 = strtotime($date2);
            return $d1 - $d2;
        });

        return $dates;
    }


	public function get_time_slots($event_id, $book_event_date, $event_increment, $event_max_book_per_slot, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_duration) {

		$time_ranges = $this->get_time_ranges($event_id, $book_event_date);
		$combine_ranges = $this->get_combine_ranges($time_ranges);

		$time_slots = $this->get_all_time_slots($combine_ranges, $event_increment, $event_duration);
		$time_slots = $this->get_time_slots_exclude_booking($time_slots, $event_duration, $event_buffer_before, $event_buffer_after, $event_max_book_per_slot, $event_id, $book_event_date);
		$time_slots = $this->get_time_slots_exclude_less_hour($time_slots, $event_less_hour, $book_event_date);
		
		usort($time_slots, function($date1, $date2) {
			$d1 = strtotime($date1);
			$d2 = strtotime($date2);
			return $d1 - $d2;
		});

		return $time_slots;
	}

	private function get_time_ranges($event_id, $book_event_date) {
		require ("pdo/dbconfig.php");

		$year = date("Y",strtotime($book_event_date));
		$month = date("m",strtotime($book_event_date));
		$day = date("d",strtotime($book_event_date));
		$jd = gregoriantojd($month, $day, $year); 
		$dayofweek = jddayofweek($jd, 1);

		 $time_ranges = array();
		$results = $this->DB_calendar->get_event_availability($event_id);

		foreach ( $results as $row ) {
			$availability_type = $row['availability_type']; 
			if($availability_type == strtolower($dayofweek)) {
				$start = $row['availability_start'];
				$end = $row['availability_end'];
				$time_ranges[] = array("start" => $start, "end" => $end);
			}       
			else if($availability_type == 'specific') {
				$specific_date = $row['availability_specific_date'];
				if(strtotime($specific_date) - strtotime($book_event_date) == 0) {
					$start = $row['availability_start'];
					$end = $row['availability_end'];
					$time_ranges[] = array("start" => $start, "end" => $end);
				}
			} 
		}
		return $time_ranges;
	}

	private function get_combine_ranges($time_ranges) {
		$combine_ranges = array();
		$combine_indexes = array();
		for($j = 0; $j < count($time_ranges); $j++) {
			$start1 = $time_ranges[$j]["start"];
			$end1 = $time_ranges[$j]["end"];

			for($k = $j + 1; $k < count($time_ranges); $k++) {

				$start2 = $time_ranges[$k]["start"];
				$end2 = $time_ranges[$k]["end"]; 

				if(strtotime($end1) >= strtotime($start2) && strtotime($start2) >= strtotime($start1)) {
					$combine_start = $start1; 

					if(strtotime($end1) >= strtotime($end2)) {
						$combine_end = $end1;
					}
					else {
						$combine_end = $end2;                                  
					}
					$combine_ranges[] = array("start" => $combine_start, "end" => $combine_end);
					$combine_indexes[] = $j;
					$combine_indexes[] = $k;
				} 

			}
			if(!in_array($j, $combine_indexes)) {
				$combine_ranges[] = array("start" => $start1, "end" => $end1);
			}
		}
		return $combine_ranges;
	}

	private function get_all_time_slots($combine_ranges, $event_increment, $event_duration) {
		$time_slots = array();
		for($o = 0; $o < count($combine_ranges); $o++) {
			$start = $combine_ranges[$o]["start"];
			$end = $combine_ranges[$o]["end"];

			$event_start = $start;
			$event_start_time = strtotime($event_start);
			$event_end_time = strtotime(date("H:i:s", strtotime('+' . $event_duration . ' minutes', $event_start_time)));   

			while($event_end_time <= strtotime($end)) {
				$time_slots[] = date("H:i:s", $event_start_time);

				$event_start_time = strtotime(date("H:i:s", strtotime('+' . $event_increment . ' minutes', $event_start_time)));
				$event_end_time = strtotime(date("H:i:s", strtotime('+' . $event_duration . ' minutes', $event_start_time)));
			} 
		}

		return $time_slots;
	}

	private function get_time_slots_exclude_booking($time_slots, $event_duration, $event_buffer_before, $event_buffer_after, $event_max_book_per_slot, $event_id, $book_event_date) {
		$booked = array();
		include ("pdo/dbconfig.php");

		$result = $this->DB_calendar->get_bookings($event_id, $book_event_date);

		foreach ( $result as $row ) {
			$booked[] = $row['booking_start'];
		}
		$time_slots_length = count($time_slots);
		for($x = 0; $x < count($booked); $x++) { 
			$booked_start = $booked[$x];
			$booked_end = date("H:i:s", strtotime('+' . $event_duration . ' minutes', strtotime($booked_start)));

			$booked_start_buffer = date("H:i:s", strtotime('-' . $event_buffer_before . ' minutes', strtotime($booked_start)));                             
			$booked_end_buffer = date("H:i:s", strtotime('+' . $event_buffer_after . ' minutes', strtotime($booked_end)));

			for($y = 0; $y < $time_slots_length; $y++) {
				if(array_key_exists($y, $time_slots)) {
					$slot_start = $time_slots[$y];
					$slot_end = date("H:i:s", strtotime('+' . $event_duration . ' minutes', strtotime($time_slots[$y])));

					if(strtotime($booked_start) == strtotime($slot_start) && strtotime($slot_end) == strtotime($booked_end)) { 
						if($event_max_book_per_slot == 1) {
							unset($time_slots[$y]);
						}
						else {
							$s = "SELECT * FROM booking_infos WHERE event_id = '$event_id' AND booking_date = '$book_event_date' AND booking_start = '$booked_start'";
							$r = $conn->query($s);  
							if(mysqli_num_rows($r) >= $event_max_book_per_slot) {
								unset($time_slots[$y]);
							}                                
						}
					}
					else if((strtotime($slot_start) < strtotime($booked_end_buffer) && strtotime($booked_start_buffer) <= strtotime($slot_start)) 
						|| (strtotime($slot_end) <= strtotime($booked_end_buffer) && strtotime($booked_start_buffer) < strtotime($slot_end))) {

						unset($time_slots[$y]);
					} 
				}
			}
		}       
		$time_slots = array_values($time_slots);
		return $time_slots;
	}

	public function get_time_slots_exclude_less_hour($time_slots, $event_less_hour, $book_event_date) {
		date_default_timezone_set('America/New_York');
		$current_time = time();
		$limit_time = date("Y-m-d H:i:s", strtotime('+' . $event_less_hour . ' hours', $current_time));  

		$time_slots_length = count($time_slots);
		for($z = 0; $z < $time_slots_length; $z++) { 

			if(strtotime($book_event_date . $time_slots[$z]) < strtotime($limit_time)) {  
				unset($time_slots[$z]);
			}
		}

		return $time_slots;
	}
}
?>