<?php
session_start();
include_once("pdo/dbconfig.php");

if (true) {
	if (isset($_POST['create_event_oneonone'])) {

		$event_name        = $_POST['event_name'];
		$event_location    = $_POST['event_location'];
		$event_description = $_POST['event_description'];
		$event_duration    = $_POST['event_duration'];

		if ($event_duration == 0) {
			$event_custom_duration = $_POST['event_custom_duration'];
		} else {
			$event_custom_duration = 0;
		}

		if (isset($_POST['event_max_book_per_day']) && $_POST['event_max_book_per_day'] > 0) {
			$event_max_book_per_day = $_POST['event_max_book_per_day'];
		} else {
			$event_max_book_per_day = 0;
		}

		$event_increment         = $_POST['event_increment'];
		$event_buffer_before     = $_POST['event_buffer_before'];
		$event_buffer_after      = $_POST['event_buffer_after'];
		$event_less_hour         = $_POST['event_less_hour'];
		$event_rolling_week      = $_POST['event_rolling_week'];
		$event_max_book_per_slot = 1;
		$building_id             = 0;
		$calendar_type           = "visit";

		$event_id = $DB_calendar->create_event(
			$event_name,
			$event_location,
			$event_duration,
			$event_custom_duration,
			$event_max_book_per_day,
			$event_increment,
			$event_buffer_before,
			$event_buffer_after,
			$event_less_hour,
			$event_description,
			$event_max_book_per_slot,
			$event_rolling_week,
			$building_id,
			$calendar_type
		);
		if (!is_null($event_id)) {
			header('Location: create-event-oneonone-availability.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['add_event_oneonone_availability'])) {

		$event_id           = $_POST['event_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end   = $_POST['availability_end'];
		$availability_type  = $_POST['availability_type'];

		if ($availability_type == "specific") {
			$availability_specific_date = date("Y-m-d", strtotime($_POST['availability_specific_date']));
		} else {
			$availability_specific_date = 0;
		}

		$result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end, $availability_type, $availability_specific_date);

		if ($result) {
			header('Location: create-event-oneonone-availability.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['edit_event_oneonone_availability'])) {

		$event_id           = $_POST['event_id'];
		$availability_id    = $_POST['availability_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end   = $_POST['availability_end'];

		$result = $DB_calendar->edit_event_availability($availability_id, $availability_start, $availability_end);

		if ($result) {
			header('Location: create-event-oneonone-availability.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['delete_event_oneonone_availability'])) {

		$event_id        = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];

		$result = $DB_calendar->delete_event_availability($availability_id);

		if ($result) {
			header('Location: create-event-oneonone-availability.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['details_event_oneonone'])) {

		$event_id = $_POST['event_id'];
		header('Location: details-event-oneonone.php?event_id=' . $event_id);
	} else if (isset($_POST['details_add_event_oneonone_availability'])) {

		$event_id           = $_POST['event_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end   = $_POST['availability_end'];
		$availability_type  = $_POST['availability_type'];

		if ($availability_type == "specific") {
			$availability_specific_date = date("Y-m-d", strtotime($_POST['availability_specific_date']));
		} else {
			$availability_specific_date = 0;
		}

		$result = $DB_calendar->add_event_availability($event_id, $availability_start, $availability_end, $availability_type, $availability_specific_date);

		if ($result) {
			header('Location: details-event-oneonone.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['details_edit_event_oneonone_availability'])) {

		$event_id           = $_POST['event_id'];
		$availability_id    = $_POST['availability_id'];
		$availability_start = $_POST['availability_start'];
		$availability_end   = $_POST['availability_end'];

		$result = $DB_calendar->edit_event_availability($availability_id, $availability_start, $availability_end);

		if ($result) {
			header('Location: details-event-oneonone.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['details_delete_event_oneonone_availability'])) {

		$event_id        = $_POST['event_id'];
		$availability_id = $_POST['availability_id'];

		$result = $DB_calendar->delete_event_availability($availability_id);

		if ($result) {
			header('Location: details-event-oneonone.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['details_update_event_oneonone'])) {
		$event_id          = $_POST['event_id'];
		$event_name        = $_POST['event_name'];
		$event_location    = $_POST['event_location'];
		$event_description = $_POST['event_description'];
		$event_duration    = $_POST['event_duration'];

		if ($event_duration == 0) {
			$event_custom_duration = $_POST['event_custom_duration'];
		} else {
			$event_custom_duration = 0;
		}

		if (isset($_POST['event_max_book_per_day']) && $_POST['event_max_book_per_day'] > 0) {
			$event_max_book_per_day = $_POST['event_max_book_per_day'];
		} else {
			$event_max_book_per_day = 0;
		}

		$event_increment     = $_POST['event_increment'];
		$event_buffer_before = $_POST['event_buffer_before'];
		$event_buffer_after  = $_POST['event_buffer_after'];
		$event_less_hour     = $_POST['event_less_hour'];
		$event_rolling_week  = $_POST['event_rolling_week'];

		$result = $DB_calendar->update_event(
			$event_id,
			$event_name,
			$event_location,
			$event_duration,
			$event_custom_duration,
			$event_max_book_per_day,
			$event_increment,
			$event_buffer_before,
			$event_buffer_after,
			$event_less_hour,
			$event_description,
			1,
			$event_rolling_week
		);
		if ($result) {
			header('Location: details-event-oneonone.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['delete_event_oneonone'])) { //

		$event_id = $_POST['event_id'];

		$result = $DB_calendar->delete_event($event_id);

		if ($result) {
			header('Location: event-list.php');
		} else {
			echo "Error!";
		}
	} else if (isset($_POST['book_event'])) {

		$event_id = $_POST['event_id'];
		header('Location: book-event.php?event_id=' . $event_id);
	} else if (isset($_POST['book_event_choose_date'])) {

		$event_id        = $_POST['event_id'];
		$book_event_date = $_POST['book_event_date'];

		header('Location: book-event-time.php?event_id=' . $event_id . '&date=' . $book_event_date);
	}


	# action of booking a visit event from spg front end
	else if (isset($_POST['book_event_choose_time'])) {
		//running robot checker
		$captcha = $_POST['g-recaptcha-response'];
		if (!$captcha) {
			echo '<h2>Please check the the captcha(I\'m not a robot) form.</h2>';
			exit;
		}
		$secretKey    = '6Lep_ykUAAAAAFKESQmqUL8DTi4cNqo4IclJRitm';
		$ip           = $_SERVER['REMOTE_ADDR'];
		$response     = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha . "&remoteip=" . $ip);
		$responseKeys = json_decode($response, true);
		if (intval($responseKeys["success"]) !== 1) {
			exit;
		}

		//$event_id = $_POST['event_id'];
		$book_event_slots = $_POST['book_event_slots'];
		$appointment_unit = $_POST['apt_id'];

		$book_event_time = trim(explode('&', $book_event_slots)[1]);
		$book_event_id   = trim(explode('&', $book_event_slots)[0]);

		$book_event_date = $_POST['book_event_date'];
		$building_id     = $_POST['building_id'];
		$customer_name   = $_POST['visitor_name'];
		$customer_email  = $_POST['visitor_email'];
		$moveInMonth     = $_POST['movein_month'];

		if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
			// Email is not valid
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}

		$telephone = formalize_telephone($_POST['visitor_phone']);
		//$event_description = $_POST['event_description'];
		//$event_location = $_POST['event_location'];

		$row                              = $DB_calendar->get_visit_event($book_event_id);
		$event_name                       = $row['event_name'];
		$event_location                   = $row['event_location'];
		$event_description                = $row['event_description'];
		$event_person_in_charge_id        = $row['resonsible_employee_id'];
		$event_person_in_charge           = $DB_calendar->get_employee_info($event_person_in_charge_id);
		$event_company_id                 = $event_person_in_charge['company_id'];
		$event_person_in_charge_name      = $event_person_in_charge['full_name'];
		$event_person_in_charge_telephone = $event_person_in_charge['mobile'];
		$event_person_in_charge_telephone = formalize_telephone($event_person_in_charge_telephone);
		$event_person_in_charge_email     = $event_person_in_charge['email'];

		$potential_status = 0;
		$ps_modified_by   = null;
		//the source of the appointment by default of 1-SPG Management front end
		if (isset($_POST['from_portal'])) {
			$source_type = 3;
		} else {
			$source_type = 1;
		}

		$result = $DB_calendar->create_booking($book_event_id, $book_event_time, $telephone, $customer_email, $book_event_date, $customer_name, $appointment_unit, $event_company_id, $event_person_in_charge_id, $potential_status, $ps_modified_by, $source_type, $moveInMonth);


		/**
		 * New code according to the Latest Tenant Workflow
		 * Send the agent a mail indicating a new booking has been made.
		 * Send the tenant an email to wait for the confirmation from the agent.
		 * Send the tenant details of the agent in the same email.
		 */

		if ($result) {
			include_once "sendSMSEmail.php";
			include_once "Class.Template.php";
			$template = new Template();

			//email
			$title            = "Notification";
			$subtitle         = "We have an important message for you!";
			$email_sbj        = "spgmanagement.com - You received a visit event in SPG Management";
			$employee_body1   = "You have a new visit event appointment!";
			$employee_body2   = '<b>Appointment Detail:</b>';
			$employee_body2   .= '<br>Customer Name :' . $customer_name;
			$employee_body2   .= '<br>Customer Telephone :' . $telephone;
			$employee_body2   .= '<br>Customer Email :' . $customer_email;
			$employee_body2   .= '<br>Event Description : ' . $event_description;
			$employee_body2   .= '<br>Event Time : ' . $book_event_date . ' ' . $book_event_time;
			$employee_body2   .= '<br>Event Location : ' . $event_location;
			$employee_body2   .= '<br> Please confirm the booking clicking the button below.';
			$button_url       = "https://www.spgmanagement.com/admin/";
			$button_content   = "Log in to SPG Management";
			$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, $button_url, $button_content);
			MySendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);

			//email to customer
			$title        = "Congratulations!";
			$subtitle     = "You requested a Visit Appointment!";
			$email_sbj    = "spgmanagement.com - Reservation of an appointment";
			$client_body1 = "Congratulations! You requested a visit appointment.";
			$client_body1 = "<br> The visit agent is yet to confirm your booking. We'll notify you once the confirmation is done.";
			$client_body2 = '<b>Appointment Detail :</b>';
			$client_body2 .= '<br><br>Event Description : ' . $event_description;
			$client_body2 .= '<br><br>Event Time : ' . $book_event_date . ' ' . $book_event_time;
			$client_body2 .= '<br><br>Event Location : ' . $event_location;
			$client_body2 .= '<br><b>Your agent information :</b>';
			$client_body2 .= '<br>Agent Name : ' . $event_person_in_charge_name;
			$client_body2 .= '<br>Agent Telephone : ' . $event_person_in_charge_telephone;
			$client_body2 .= '<br><br><b>Please contact with your agent if you want to cancel the appointment.</b>';

			$button_url     = "https://spg.spgmanagement.com/potential_message.php?bid=$result";
			$button_content = "Talk to Agent";
			$client_content = $template->emailTemplate($title, $subtitle, $customer_name, $client_body1, $client_body2, $button_url, $button_content);
			MySendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $customer_email, $customer_name, $email_sbj, $client_content);
		}


		//		if ($result) {
		//			include_once "sendSMSEmail.php";
		//			include_once "Class.Template.php";
		//			$template = new Template();
		//
		//			//notification to client
		//			//sms
		//			$SMS_message = "Dear " . $customer_name . ",\nYou have reserved a appointment with us successfully.\nYour appointment has been scheduled in " . $event_location . "at " . $book_event_date . " " . $book_event_time . "\nYour agent Information:\nName:" . $event_person_in_charge_name . "\nTelephone:" . $event_person_in_charge_telephone . "\n(Please contact with your agent if you want to cancel the appointment)\n-- spgmanagement.com";
		//			SendSMS($telephone, $SMS_message);
		//
		//			//email
		//			$title        = "Congratulations!";
		//			$subtitle     = "You successfully reserved a visit appointment!";
		//			$email_sbj    = "spgmanagement.com - Successful reservation of appointment";
		//			$client_body1 = "Congratulations! You successfully reserved a visit appointment!";
		//			$client_body2 = '<b>Appointment Detail :</b>';
		//			$client_body2 .= '<br>Event Description : ' . $event_description;
		//			$client_body2 .= '<br>Event Time : ' . $book_event_date . ' ' . $book_event_time;
		//			$client_body2 .= '<br>Event Location : ' . $event_location;
		//			$client_body2 .= '<br><b>Your agent information :</b>';
		//			$client_body2 .= '<br>Agent Name : ' . $event_person_in_charge_name;
		//			$client_body2 .= '<br>Agent Telephone : ' . $event_person_in_charge_telephone;
		//			$client_body2 .= '<br><b>Please contact with your agent if you want to cancel the appointment.</b>';
		//
		//			$button_url     = "https://www.spgmanagement.com/spg";
		//			$button_content = "See More Properties";
		//			$client_content = $template->emailTemplate($title, $subtitle, $customer_name, $client_body1, $client_body2, $button_url, $button_content);
		//			SendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $customer_email, $customer_name, $email_sbj, $client_content);
		//
		//			//notification to employee
		//			//sms
		//			$SMS_message_employee = "Dear " . $event_person_in_charge_name . ",\nYou have reserved visit event.\nYour appointment has been scheduled in " . $event_location . " at " . $book_event_date . " " . $book_event_time . "\nPlease check your calendar about details\n-- spgmanagement.com";
		//			usleep(500000);
		//			SendSMS($event_person_in_charge_telephone, $SMS_message_employee);
		//
		//			//email
		//			$title            = "Notification";
		//			$subtitle         = "We have an important message for you!";
		//			$email_sbj        = "spgmanagement.com - You received a visit event in SPG Management";
		//			$employee_body1   = "You reserved a visit event appointment";
		//			$employee_body2   = '<b>Appointment Detail:</b>';
		//			$employee_body2   .= '<br>Customer Name :' . $customer_name;
		//			$employee_body2   .= '<br>Customer Telephone :' . $telephone;
		//			$employee_body2   .= '<br>Customer Email :' . $customer_email;
		//			$employee_body2   .= '<br>Event Description : ' . $event_description;
		//			$employee_body2   .= '<br>Event Time : ' . $book_event_date . ' ' . $book_event_time;
		//			$employee_body2   .= '<br>Event Location : ' . $event_location;
		//			$button_url       = "https://www.spgmanagement.com/admin/";
		//			$button_content   = "Log in to SPG Management";
		//			$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, $button_url, $button_content);
		//			SendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);
		//
		//			//Check if there is a current lease for the apartment. If there is one, send notifications to the tenants
		//			include_once "pdo/dbconfig.php";
		//			if ($DB_lease->isOccupied($appointment_unit)) {
		//				$tenant_ids = explode(",", $DB_lease->isOccupied($appointment_unit));
		//				foreach ($tenant_ids as $tenant_id) {
		//					$name = $DB_tenant->getTenantName($tenant_id);
		//
		//					//sms
		//					$toNumber    = $DB_tenant->getTenantPhone($tenant_id);
		//					$toNumber    = formalize_telephone($toNumber);
		//					$sms_content = "Dear " . $name . ",\nWe made an appointment with a potential customer who is interested in the apartment you are now living in.\nWe will be visiting your apartment at " . $book_event_time . " on " . $book_event_date . ".\nWe would ask you to allow this visit.\n-- spgmanagement.com";
		//					//usleep(300000);
		//					//SendSMS($toNumber, $sms_content);
		//
		//					//email
		//					$toEmail        = $DB_tenant->getTenantEmail($tenant_id);
		//					$title          = "Notification -- SPG Management";
		//					$subtitle       = "Could we lead the potential tenants to visit your apartment ?";
		//					$body1          = "Hello, my tenant, <br> We made an appointment with a potential customer who is interested in the apartment you are now living in. Could we lead the customer to visit your apartment ? <br><br> We are going to visit your apartment at " . $book_event_time . " on " . $book_event_date . ". Please clear up your apartment, if you do not mind.";
		//					$body2          = "Thank you in advance for your cooperation and understanding.";
		//					$button_url     = "https://www.spgmanagement.com/admin/";
		//					$button_content = "Log in to SPG Management";
		//					$email_content  = $template->emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content);
		//					$email_sbj      = "spgmanagement.com - You have a coming visit";
		//					SendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $toEmail, $name, $email_sbj, $email_content);
		//				}
		//			}
		//			header('Location: successful-book-event.php?building_id=' . $building_id);
		//		}
		//		else {
		//			echo "Error!";
		//		}

		header('Location: successful-book-event.php?building_id=' . $building_id);
	} else if (isset($_POST['book_event_back'])) {

		$event_id = $_POST['event_id'];
		header('Location: book-event.php?event_id=' . $event_id);
	} else if (isset($_POST['event_booking_list'])) {

		$event_id = $_POST['event_id'];
		header('Location: event-booking-list.php?event_id=' . $event_id);
	} else if (isset($_POST['cancel_booking'])) {

		$booking_id = $_POST['booking_id'];
		$event_id   = $_POST['event_id'];
		$result     = $DB_calendar->cancel_booking($booking_id);

		if ($result) {
			header('Location: event-booking-list.php?event_id=' . $event_id);
		} else {
			echo "Error!";
		}
	}
}


function formalize_telephone($original_tele)
{
	$formal_tele = trim($original_tele);
	$formal_tele = str_replace(' ', '', $formal_tele);
	$formal_tele = str_replace('-', '', $formal_tele);
	if (strlen($formal_tele) == 10)
		$formal_tele = '1' . $formal_tele;
	return $formal_tele;
}