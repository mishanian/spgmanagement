<?php
/* * *
 * Cron to send an email to the agent when the followup time between him and the customer is more than 24 hours
 * Author : Sharan
 * Date : 2018-06-26
 * * */
if (strpos(getcwd(), "cron_scripts") == false) {
	$path = "../../pdo/";
} else {
	$path = "../../../pdo/";
}

$file = $path . 'dbconfig.php';
include_once($file);

include_once("sendSMSEmail.php");
include_once $path . "Class.Template.php";

$agent_reminder      = "SELECT * FROM view_questions_and_visits WHERE potential_status IN (1,2) AND last_followup_dt < NOW() - INTERVAL 1 DAY;";
$agent_reminder_stmt = $DB_con->prepare($agent_reminder);
$agent_reminder_stmt->execute();

if ($agent_reminder_stmt->rowCount() > 0) {
	foreach ($agent_reminder_stmt->fetchAll(PDO::FETCH_ASSOC) as $rows) {
		sendreminderEmail($DB_calendar, $rows["potential_id"], $rows["visit_or_question"]);
	}
}


function sendreminderEmail($DB_calendar, $id, $type)
{
	$template = new Template();

	// Visit
	if ($type == 0) {
		$bookingInfo       = $DB_calendar->getBookingInfo($id);
		$data["name"]      = $bookingInfo["customer_name"];
		$data["telephone"] = $bookingInfo["telephone"];
		$data["email"]     = $bookingInfo["customer_email"];
		$data["date"]      = $bookingInfo["booking_date"];
		$data["start"]     = $bookingInfo["booking_start"];

		$row                       = $DB_calendar->get_visit_event($bookingInfo["event_id"]);
		$event_name                = $row['event_name'];
		$event_location            = $row['event_location'];
		$event_description         = $row['event_description'];
		$event_person_in_charge_id = $row['resonsible_employee_id'];

		//email to agent
		$title          = "Notification";
		$subtitle       = "We have an important message for you!";
		$email_sbj      = "spgmanagement.com - You have not responded to the Visit booking.";
		$employee_body1 = "You have a pending Visit booking appointment to respond to.";
		$employee_body2 = '<b>Detail:</b>';
		$employee_body2 .= '<br>Customer Name :' . $bookingInfo["customer_name"];
		$employee_body2 .= '<br>Customer Telephone :' . $bookingInfo["telephone"];
		$employee_body2 .= '<br>Customer Email :' . $bookingInfo["customer_email"];
		$employee_body2 .= '<br>Event Description : ' . $event_description;
		$employee_body2 .= '<br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
		$employee_body2 .= '<br>Event Location : ' . $event_location;
	} else {
		// Question asked by the customer
		$bookingInfo       = $DB_calendar->getQuestionInfo($id);
		$data["name"]      = $bookingInfo["customer_name"];
		$data["telephone"] = $bookingInfo["customer_phone"];
		$data["email"]     = $bookingInfo["customer_email"];
		$data["date"]      = $bookingInfo["submission_date"];

		if (isset($bookingInfo["employee_id"])) {
			$event_person_in_charge_id = $bookingInfo["employee_id"];
		} else {
			$event_person_in_charge_id = $bookingInfo["last_followup_employee_id"];
		}

		//email to agent
		$title          = "Notification";
		$subtitle       = "We have an important message for you!";
		$email_sbj      = "spgmanagement.com - You have not responded to a question.";
		$employee_body1 = "You have a pending question to respond to.";
		$employee_body2 = '<b>Detail:</b>';
		$employee_body2 .= '<br>Customer Name :' . $bookingInfo["customer_name"];
		$employee_body2 .= '<br>Customer Telephone :' . $bookingInfo["telephone"];
		$employee_body2 .= '<br>Customer Email :' . $bookingInfo["customer_email"];
	}

	$event_person_in_charge           = $DB_calendar->get_employee_info($event_person_in_charge_id);
	$event_company_id                 = $event_person_in_charge['company_id'];
	$event_person_in_charge_name      = $event_person_in_charge['full_name'];
	$event_person_in_charge_telephone = $event_person_in_charge['mobile'];
	$event_person_in_charge_telephone = formalize_telephone($event_person_in_charge_telephone);
	$event_person_in_charge_email     = $event_person_in_charge['email'];

	$button_url     = "https://spg.spgmanagement.com/admin/";
	$button_content = "Respond Now";

	$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, $button_url, $button_content);
	SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);
}