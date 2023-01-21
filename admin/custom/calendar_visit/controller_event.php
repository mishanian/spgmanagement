<?php
include_once ("../../../pdo/dbconfig.php");

if(true) {
	if (isset($_POST['create_event_oneonone'])) {

		$event_name = $_POST['event_name'];
		$event_location = $_POST['event_location'];
		$event_description = $_POST['event_description'];
		$event_duration = $_POST['event_duration'];
		$building_id=$_POST['building_id'];

		if($event_duration == 0) {
			$event_custom_duration = $_POST['event_custom_duration'];
		}
		else {
			$event_custom_duration = 0;
		}

		if (isset($_POST['event_max_book_per_day']) && $_POST['event_max_book_per_day'] > 0) {
			$event_max_book_per_day = $_POST['event_max_book_per_day'];
		}
		else {
			$event_max_book_per_day = 0;
		}

		$event_increment = $_POST['event_increment'];
		$event_buffer_before = $_POST['event_buffer_before'];
		$event_buffer_after = $_POST['event_buffer_after'];
		$event_less_hour = $_POST['event_less_hour'];
		$event_rolling_week = 1;
		$event_max_book_per_slot = 1;
		$responsible_employee_id=$_POST['person_in_charge'];

		$event_id = $DB_calendar->create_visit_event($event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day,
					$event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, $event_max_book_per_slot, $event_rolling_week, $building_id,$responsible_employee_id);
		if(!is_null($event_id)) {
			header('Location: ../../create-event-oneonone-availability.php?event_id=' . $event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['add_event_oneonone_availability'])){
		$event_id = $_POST['event_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end = $_POST['availability_end'];
		$availability_type = $_POST['availability_type'];
		$start_day = date('Y-m-d',strtotime(str_replace('/', '-',$_POST['start_day'])));
		$end_day = date('Y-m-d',strtotime(str_replace('/', '-',$_POST['end_day'])));


		$building_id=$_POST['building_id'];


		if(!isset($_POST['is_regular'])){
			$availability_specific_date = date("Y-m-d", strtotime(str_replace('/', '-',$_POST['availability_specific_date'])));
            $result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end,"specific", $availability_specific_date);
        }else{
		    $arr_days=array();
		    $index=0;    //index of arr_days

            if($availability_type!=0){
                for ($i = strtotime($start_day); $i <= strtotime($end_day); $i = strtotime('+1 day', $i)){
                    if (date('N', $i) == $availability_type){   //Monday=1,Tuesday=2,....
                        $arr_days[$index]=date('Y-m-d', $i);
                        $index++;
                    }
                }
            }
            else{
                for ($i = strtotime($start_day); $i <= strtotime($end_day); $i = strtotime('+1 day', $i)){
                    if (date('N', $i) == 1 || date('N', $i) == 2 || date('N', $i) == 3 || date('N', $i) == 4 || date('N', $i) == 5){   //Monday=1,Tuesday=2,....
                        $arr_days[$index]=date('Y-m-d', $i);
                        $index++;
                    }
                }
            }

            //add to db
            for($i=0;$i<sizeof($arr_days);$i++){
                $result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end,"specific", $arr_days[$i]);
            }
        }

        //result
		if($result) {
			header('Location: ../../create-event-oneonone-availability.php?event_id=' . $event_id."&building_id=".$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['edit_event_oneonone_availability'])){

		$event_id = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end = $_POST['availability_end'];
		$building_id=$_POST['building_id'];

		$result = $DB_calendar->edit_event_availability($availability_id, $availability_start, $availability_end);

		if($result) {
			header('Location: ../../create-event-oneonone-availability.php?event_id='.$event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['delete_event_oneonone_availability'])) {

		$event_id = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];
        $building_id=$_POST['building_id'];
		$result = $DB_calendar->delete_event_availability($availability_id);

		if($result) {
			header('Location: ../../create-event-oneonone-availability.php?event_id='.$event_id."&building_id=".$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_event_oneonone'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		header('Location: ../../details-event-oneonone.php?event_id=' . $event_id.'&building_id='.$building_id);

	}

	else if (isset($_POST['details_add_event_oneonone_availability'])){

		$event_id = $_POST['event_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end = $_POST['availability_end'];
		$availability_type = $_POST['availability_type'];
		$building_id=$_POST['building_id'];
		$start_day = date('Y-m-d',strtotime(str_replace('/','-',$_POST['start_day'])));
		$end_day = date('Y-m-d',strtotime(str_replace('/','-',$_POST['end_day'])));

        if(!isset($_POST['is_regular'])){
            $availability_specific_date = date("Y-m-d", strtotime(str_replace('/','-',$_POST['availability_specific_date'])));
            $result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end,"specific", $availability_specific_date);
        }else{
            $arr_days=array();
            $index=0;    //index of arr_days

            if($availability_type!=0){
                for ($i = strtotime($start_day); $i <= strtotime($end_day); $i = strtotime('+1 day', $i)){
                    if (date('N', $i) == $availability_type){   //Monday=1,Tuesday=2,....
                        $arr_days[$index]=date('Y-m-d', $i);
                        $index++;
                    }
                }
            }
            else{
                for ($i = strtotime($start_day); $i <= strtotime($end_day); $i = strtotime('+1 day', $i)){
                    if (date('N', $i) == 1 || date('N', $i) == 2 || date('N', $i) == 3 || date('N', $i) == 4 || date('N', $i) == 5){   //Monday=1,Tuesday=2,....
                        $arr_days[$index]=date('Y-m-d', $i);
                        $index++;
                    }
                }
            }

            //add to db
            for($i=0;$i<sizeof($arr_days);$i++){
                $result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end,"specific", $arr_days[$i]);
            }
        }
		if($result) {
			header('Location: ../../details-event-oneonone.php?event_id='.$event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_edit_event_oneonone_availability'])) {

		$event_id = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end = $_POST['availability_end'];
        $building_id=$_POST['building_id'];

		$result = $DB_calendar->edit_event_availability($availability_id, $availability_start, $availability_end);

		if($result) {
			header('Location: ../../details-event-oneonone.php?event_id='.$event_id."&building_id=".$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_delete_event_oneonone_availability'])) {

		$event_id = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];
		$building_id=$_POST['building_id'];

		$result = $DB_calendar->delete_event_availability($availability_id);

		if($result) {
			header('Location: ../../details-event-oneonone.php?event_id='.$event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_update_event_oneonone'])) {
		$event_id = $_POST['event_id'];
		$event_name = $_POST['event_name'];
		$event_location = $_POST['event_location'];
		$event_description = $_POST['event_description'];
		$event_duration = $_POST['event_duration'];
		$building_id=$_POST['building_id'];

		if($event_duration == 0) {
			$event_custom_duration = $_POST['event_custom_duration'];
		}
		else {
			$event_custom_duration = 0;
		}

		if (isset($_POST['event_max_book_per_day']) && $_POST['event_max_book_per_day'] > 0) {
			$event_max_book_per_day = $_POST['event_max_book_per_day'];
		}
		else {
			$event_max_book_per_day = 0;
		}

		$event_increment = $_POST['event_increment'];
		$event_buffer_before = $_POST['event_buffer_before'];
		$event_buffer_after = $_POST['event_buffer_after'];
		$event_less_hour = $_POST['event_less_hour'];
		$event_rolling_week = 1;
		$person_in_charge=$_POST['person_in_charge'];

		$result = $DB_calendar->update_visit_event($event_id, $event_name, $event_location, $event_duration, $event_custom_duration, $event_max_book_per_day,
					$event_increment, $event_buffer_before, $event_buffer_after, $event_less_hour, $event_description, 1, $event_rolling_week,$person_in_charge);
		if($result) {
			header('Location: ../../details-event-oneonone.php?event_id=' . $event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['delete_event_oneonone'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];

		//check if booked event or not
        $booked_events = $DB_calendar->get_all_bookings($event_id);
        $flag = false;
        foreach ($booked_events as $one){
            $booked_date = $one['booking_date'];
            //if there is a booked event in the future
            if(strtotime($booked_date) >= strtotime(date("Y-m-d"))){
                $flag = true;
                break;
            }
        }


        if(!$flag){
            $result = $DB_calendar->delete_visit_event($event_id);
            $alert = '';
        }else{
            $alert = '&alert=booked_events_can_not_delete';
        }


        //switch page
        header('Location: ../../event-list.php?building_id='.$building_id.'&direct=visitor_events'.$alert);
	}

	else if (isset($_POST['book_event'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		header('Location: ../../book-event.php?event_id='.$event_id.'&building_id='.$building_id);

	}

	else if (isset($_POST['book_event_choose_date'])) {

		$event_id = $_POST['event_id'];
		$book_event_date = $_POST['book_event_date'];

		header('Location: ../../book-event-time.php?event_id=' . $event_id . '&date=' . $book_event_date);
	}

	else if (isset($_POST['book_event_choose_time'])){

		$event_id = $_POST['event_id'];
		$book_event_time = $_POST['book_event_time'];
		$book_event_date = $_POST['book_event_date'];
		$building_id=$_POST['building_id'];
		$customer_name = $_POST['visitor_name'];
		$customer_email = $_POST['visitor_email'];
		$telephone=formalize_telephone($_POST['visitor_phone']);
		$event_description=$_POST['event_description'];
		$event_location=$_POST['event_location'];
		$visitor_desired_unit_id=$_POST['desired_unit'];

		//person_in_charge
        $person_in_charge_name = $_POST['person_in_charge_name'];
        $person_in_charge_telephone = formalize_telephone($_POST['person_in_charge_telephone']);
        $person_in_charge_email = $_POST['person_in_charge_email'];
        $person_in_charge_id = $_POST['event_person_in_charge_id'];
        $event_company_id = $_POST['event_company_id'];

		$result = $DB_calendar->create_booking($event_id, $book_event_time, $telephone, $customer_email, $book_event_date, $customer_name,$visitor_desired_unit_id,$event_company_id,$person_in_charge_id);
		if($result){
            include_once "sendSMSEmail.php";

            header('Location: ../../event-list.php?building_id='.$building_id.'&direct=visitor_events');
            //notification to client
            //sms
            $SMS_message = "Dear ".$customer_name.",\nYou have reserved a appointment with us successfully.\nYour appointment has been scheduled in ".$event_location."at ".$book_event_date." ".$book_event_time."\nYour agent Information:\nName:".$person_in_charge_name."\nTelephone:".$person_in_charge_telephone."\n(Please contact with your agent if you want to cancel the appointment)\n-- spgmanagement.com";
            SendSMS($telephone,$SMS_message);
            //email
            $email_sbj="spgmanagement.com - Successful reservation of appointment";
            $email_content="Dear".$customer_name.',';
            $email_content.='<br><b>You reserved a event appointment successfully!</b>';
            $email_content.='<p>Appointment Detail:</p>';
            $email_content.='<p>Event Description : '.$event_description.'</p>';
            $email_content.='<p>Event Time : '.$book_event_date.' '.$book_event_time.'</p>';
            $email_content.='<p>Agent Information : '.$event_location.'</p>';
            $email_content.='<p>Agent Name : '.$person_in_charge_name.'</p>';
            $email_content.='<p>Agent Telephone : '.$person_in_charge_telephone.'</p>';
            $email_content.='<p>(Please contact with your agent, if you want to cancel the appointment)</p>';
            $email_content.='<br><br><p>Best regards,</p><a href="https://www.spgmanagement.com">spgmanagement.com</a>';
            SendEmail('info@spgmanagement.com','Info - spgmanagement.com',$customer_email,$customer_name,$email_sbj,$email_content);


            //notification to employee
            //sms
            $SMS_message_employee="Dear ".$person_in_charge_name.",\nYou have reserved visit event in spgmanagement.com.\nYour appointment has been scheduled in ".$event_location." at ".$book_event_date."  ".$book_event_time."\nPlease check your calendar about details\n-- spgmanagement.com";
            SendSMS($person_in_charge_telephone,$SMS_message_employee);
            //email
            $email_sbj="spgmanagement.com - You receive a visit event in Ilivein";
            $email_content="Dear".$customer_name.',';
            $email_content.='<br><b>You reserved a visit event appointment!</b>';
            $email_content.='<p>Appointment Detail:</p>';
            $email_content.='<p>Customer Name :'.$customer_name;
            $email_content.='<p>Customer Telephone :'.$telephone;
            $email_content.='<p>Customer Email :'.$customer_email;
            $email_content.='<p>Event Description : '.$event_description.'</p>';
            $email_content.='<p>Event Time : '.$book_event_date.' '.$book_event_time.'</p>';
            $email_content.='<p>Event Location : '.$event_location.'</p>';
            $email_content.='<br><br><p>Best regards,</p><a href="https://www.spgmanagement.com">spgmanagement.com</a>';
            SendEmail('info@spgmanagement.com','Info - spgmanagement.com',$person_in_charge_email,$person_in_charge_name,$email_sbj,$email_content);
        }
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['book_event_back'])) {

		$event_id = $_POST['event_id'];
		header('Location: ../../book-event.php?event_id=' . $event_id);
	}

	else if (isset($_POST['event_booking_list'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		header('Location: ../../event-booking-list.php?event_id='.$event_id.'&building_id='.$building_id);
	}

	else if (isset($_POST['cancel_booking'])) {

		$booking_id = $_POST['booking_id'];
		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		$customer_name=$_POST['customer_name'];
		$telephone=formalize_telephone($_POST['telephone']);
		$customer_email=$_POST['customer_email'];
		$booking_time=$_POST['booking_time'];
		$event_description=$_POST['event_description'];
		$event_location=$_POST['event_location'];
        //person_in_charge
        $person_in_charge_name=$_POST['person_in_charge_name'];
        $person_in_charge_telephone=formalize_telephone($_POST['person_in_charge_telephone']);
        $person_in_charge_email=$_POST['person_in_charge_email'];

		$result = $DB_calendar->cancel_booking($booking_id);

		if($result) {
		    include_once "sendSMSEmail.php";
            header('Location: ../../event-booking-list.php?event_id=' . $event_id.'&building_id='.$building_id);

            //notification to client
            //sms
            $SMS_message="Dear ".$customer_name.",\nWe are sorry to inform you : Your appointment in spgmanagement.com has been cancelled.\nIf you still want to have a appointment with us, please go to spg.spgmanagement.com  Thanks!\n--spgmanagement.com";
            SendSMS($telephone,$SMS_message);
            //email
            $email_sbj="spgmanagement.com - Your reservation of appointment has been cancelled";
            $email_content="Dear".$customer_name.',';
            $email_content.='<br>We are sorry to inform you:<b>Your reservation of appointment in spgmanagement.com has been cancelled!</b>';
            $email_content.='<p>Appointment Detail:</p>';
            $email_content.='<p>Event Description : '.$event_description.'</p>';
            $email_content.='<p>Event Time : '.$booking_time.'</p>';
            $email_content.='<p>Event Location : '.$event_location.'</p>';
            $email_content.='<p>If you still want to have a appointment with us, please go to</p><a href="https://www.spgmanagement.com/index">spgmanagement.com</a> and book a appointment.';
            $email_content.='<br><br><p>Best regards,</p><p>Ilivein</p>';
            SendEmail('info@spgmanagement.com','Info - spgmanagement.com',$customer_email,$customer_name,$email_sbj,$email_content);

		     //notification to employee
            //sms
            $SMS_message="Dear ".$person_in_charge_name.",\nYou have successfully cancelled the visit event with ".$customer_name." in ".$event_location." at ".$booking_time."\nThe visitor has been informed about the cancellation. Please check your calendar in spgmanagement.com about details.\n--spgmanagement.com";
            SendSMS($person_in_charge_telephone,$SMS_message);
            //email
            $email_sbj="spgmanagement.com - Your one visit event has been cancelled";
            $email_content="Dear".$person_in_charge_name.',';
            $email_content.='<br><b>Your one visit appointment in spgmanagement.com has been cancelled!</b>';
            $email_content.='<p>Appointment Detail:</p>';
            $email_content.='<p>Event Description : '.$event_description.'</p>';
            $email_content.='<p>Event Time : '.$booking_time.'</p>';
            $email_content.='<p>Event Location : '.$event_location.'</p>';
            $email_content.='<p>The visitor has been informed about the cancellation. Please check your calendar in spgmanagement.com about details.</p><a href="https://www.spgmanagement.com/admin/login">spgmanagement.com</a> ';
            $email_content.='<br><br><p>Best regards,</p><p>Ilivein</p>';
            SendEmail('info@spgmanagement.com','Info - spgmanagement.com',$person_in_charge_email,$person_in_charge_name,$email_sbj,$email_content);
        }
		else {
			echo "Error!";
		}
	}
}


//--------- methods-----------

function formalize_telephone($original_tele){
    $formal_tele=trim($original_tele);
    $formal_tele=str_replace(' ','',$formal_tele);
    $formal_tele=str_replace('-','',$formal_tele);
    if(strlen($formal_tele)==10)
        $formal_tele='1'.$formal_tele;
    return $formal_tele;
}