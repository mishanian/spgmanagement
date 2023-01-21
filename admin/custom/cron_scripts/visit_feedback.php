<?php
/* * *
 * Cron to send an email to the customers who have booked a visit. Mail will be sent after the visit time has passed.
 * Author : Mehran
 * Date : 2020-12-05
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

$passed_bookings_visit = "SELECT * FROM booking_infos WHERE booking_date = CURDATE() AND CURTIME() > booking_end";
$passed_bookings       = $DB_con->prepare($passed_bookings_visit);
$passed_bookings->execute();

if ($passed_bookings->rowCount() > 0) {
	foreach ($passed_bookings->fetchAll(PDO::FETCH_ASSOC) as $visitBooking) {
		$customerEmail = $visitBooking["customer_email"];
		$customerName  = $visitBooking["customer_name"];
		visitFeedbackEmail($DB_calendar, $visitBooking["id"]);
	}
}


function visitFeedbackEmail($DB_calendar, $bookingId)
{
	/* Send EMAIL confirmation to the customer and the agent who is assigned about the confirmation */
	$template = new Template();

	$bookingInfo = $DB_calendar->getBookingInfo($bookingId);

	$row                              = $DB_calendar->get_visit_event($bookingInfo["event_id"]);
	$event_location                   = $row['event_location'];

	// email to customer about feedback for the visited apartment
	$title        = "Feedback";
	$subtitle     = "We need your feedback!";
	$email_sbj    = "spgmanagement.com - Feedback about your recent visit to $event_location";
	$client_body1 = "Please tell us about your experience on your recent visit to $event_location. Your feedback helps us create a better experience for you and for all of our customers.";

	$client_body2 = 'Click on the button below to give your feedback.';

	$button_url     = "https://spg.spgmanagement.com/potential_feedback.php?bid=$bookingId";
	$button_content = "Feedback form";
	$client_content = $template->emailTemplate($title, $subtitle, $bookingInfo["customer_name"], $client_body1, $client_body2, $button_url, $button_content);
	MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $bookingInfo["customer_email"], $bookingInfo["customer_name"], $email_sbj, $client_content);
	echo "Visit feedback eMail sent to $bookingInfo[customer_email]";
}