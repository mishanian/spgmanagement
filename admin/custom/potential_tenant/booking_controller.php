<?php

session_start();
include_once("../../../pdo/dbconfig.php");
include_once "../../../Class.Calendar.php";
$DB_calendar = new Calendar($DB_con);
$employee_id = $_SESSION["employee_id"];

$checkListItemsDisplay = array(
	"previous_landlord" => 1,
	"regiedulogement" => 2,
	"payslip" => 3,
	"guarantor" => 4,
	"bankstatement" => 5,
	"voidcheck" => 6,
	"idphoto" => 7
);

if (isset($_POST['action'])) {

	/* Get all the pending visit bookings in the system */
	if ($_POST['action'] == "getAllBookings") {
		$return["data"] = $DB_calendar->get_all_bookings_eid($employee_id);
		echo json_encode($return);
	}

	/* Confirm  a booking request by the customer ( potential tenant ) */
	if ($_POST['action'] == "confirmBooking") {
		$bookingId = $_POST["booking_id"];

		if ($DB_calendar->updateBookingConfirmation($bookingId) > 0) {
			/* Send EMAIL confirmation to the customer and the agent who is assigned about the confirmation */
			include_once "../../../sendSMSEmail.php";
			include_once "../../../Class.Template.php";
			$template = new Template();

			$bookingInfo = $DB_calendar->getBookingInfo($bookingId);

			$row                              = $DB_calendar->get_visit_event($bookingInfo["event_id"]);
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

			//email to agent
			$title            = "Notification";
			$subtitle         = "We have an important message for you!";
			$email_sbj        = "spgmanagement.com - Confirmation of visit event in SPGManagement";
			$employee_body1   = "You have confirmed the visit event appointment!";
			$employee_body2   = '<b>Appointment Detail:</b>';
			$employee_body2   .= '<br>Customer Name :' . $bookingInfo["customer_name"];
			$employee_body2   .= '<br>Customer Telephone :' . $bookingInfo["telephone"];
			$employee_body2   .= '<br>Customer Email :' . $bookingInfo["customer_email"];
			$employee_body2   .= '<br><br>Event Description : ' . $event_description;
			$employee_body2   .= '<br><br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
			$employee_body2   .= '<br><br>Event Location : ' . $event_location;
			$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, "", "");
			MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);

			//email to customer
			$title        = "Congratulations!";
			$subtitle     = "Your Visit Appointment is confirmed!";
			$email_sbj    = "spgmanagement.com - Confirmation of appointment";
			$client_body1 = "Congratulations! Your visit appointment is confirmed by the Agent.";
			$client_body2 = '<b>Appointment Detail :</b>';
			$client_body2 .= '<br><br>Event Description : ' . $event_description;
			$client_body2 .= '<br><br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
			$client_body2 .= '<br><br>Event Location : ' . $event_location;
			$client_body2 .= '<br><b>Your agent information :</b>';
			$client_body2 .= '<br>Agent Name : ' . $event_person_in_charge_name;
			$client_body2 .= '<br>Agent Telephone : ' . $event_person_in_charge_telephone;
			$client_body2 .= '<br><br><b>Please contact with your agent if you want to cancel the appointment.</b>';

			$button_url     = "https://spg.spgmanagement.com/potential_message.php?bid=$bookingId";
			$button_content = "Talk to Agent";
			$client_content = $template->emailTemplate($title, $subtitle, $bookingInfo["customer_name"], $client_body1, $client_body2, $button_url, $button_content);
			MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $bookingInfo["customer_email"], $bookingInfo["customer_name"], $email_sbj, $client_content);

			echo json_encode(array("value" => true));
		} else {
			echo json_encode(array("value" => false));
		}
	}

	/* Get all the confirmed bookings */
	if ($_POST['action'] == "getConfirmedBookings") {
		$return["data"] = $DB_calendar->get_confirmed_bookings_eid($employee_id);
		echo json_encode($return);
	}

	if ($_POST['action'] == "modify_booking") {

		if (isset($_POST["booking_id"])) {
			/* Check if all the required values are set */
			$valuesToCheck = array("book_event_date", "book_event_slots", "movein_month", "visitor_name", "visitor_email", "visitor_phone", "event_id");
			$dataToSave    = array();
			$missingValues = array();

			foreach ($valuesToCheck as $variable) {
				if (!isset($_POST[$variable]) || empty($_POST[$variable])) {
					array_push($missingValues, $variable);
				} else {
					$dataToSave[$variable] = $_POST[$variable];
				}
			}

			// There are no errors
			if (count($missingValues) < 1) {
				// Modify the format of booking time
				$bookingTime             = explode("&", $dataToSave["book_event_slots"]);
				$startTime               = $bookingTime[1];
				$dataToSave["startTime"] = $startTime;

				// Update booking details
				if ($DB_calendar->updateBookingData($_POST["booking_id"], $dataToSave) > 0) {
					echo json_encode(array("value" => true));
					bookingModifyEmail($DB_calendar, $_POST["booking_id"]);
				} else {
					echo json_encode(array("value" => false));
				}
			}
		} else {
			echo json_encode(array("value" => false));
		}
	}

	/* Customer to Send a message to agent */
	if ($_POST['action'] == "sendMessageToAgent") {
		if (isset($_POST["message"])) {
			/* Insert the message as potential follow up */
			$assignToId      = $_POST["employee_id"];
			$followupType    = 2;
			$sourceType      = 1;
			$companyId       = $_POST["company_id"];
			$bookingId       = $_POST["booking_id"];
			$visitorQuestion = 0;

			$dataToSave = array(
				"followup" => $_POST["message"],
				"assignToId" => $assignToId,
				"followUpType" => $followupType,
				"sourceType" => $sourceType,
				"company_id" => $companyId,
				"visitOrQuestion" => $visitorQuestion,
				"potentialId" => $bookingId,
				"entry_date" => date("Y-m-d h:i:s", time())
			);
			if ($DB_calendar->addPotentialFollowUpRecord($dataToSave) > 0) {
				echo json_encode(array("value" => true));
			} else {
				echo json_encode(array("value" => false));
			}
			// Send email
		} else {
			echo json_encode(array("value" => false));
		}
	}

	/* Customer submits feedback after the visit to the apartment */
	if ($_POST['action'] == "feedback_visit") {
		$booking_id = $_POST["booking_id"];

		if (isset($_POST["rating_apartment"]) && isset($_POST["rating_agent"])) {
			$apartment_feedback = "";
			if (isset($_POST["comments_apartment"])) {
				$apartment_feedback = $_POST["comments_apartment"];
			}
			$agent_feedback = "";
			if (isset($_POST["comments_agent"])) {
				$agent_feedback = $_POST["comments_agent"];
			}

			$dataToSave = array(
				"agent" => $_POST["rating_agent"],
				"apartment" => $_POST["rating_apartment"],
				"feedback_agent" => $_POST["comments_apartment"],
				"feedback_apt" => $_POST["comments_agent"],
			);

			/* Update the respective columns in booking_infos table by booking_id */
			if ($DB_calendar->updateVisitFeedback($booking_id, $dataToSave) > 0) {
				echo json_encode(array("value" => true));
				visitfeedbackEmail($DB_calendar, $booking_id, $dataToSave);
				/* Send email to the agent about the feedback */
			} else {
				echo json_encode(array("value" => false));
			}
		} else {
			echo json_encode(array("value" => false));
		}
	}

	/* Send an email to the potential tenant when the admin clicks a button - the email consists of a link to the credit check info page */
	if ($_POST['action'] == "credit_check_email") {
		$data = $_POST["potential_id"];
		creditCheckEmail($DB_calendar, $data);
	}

	/* Approve the credit check for the potential id */
	if ($_POST['action'] == "credit_check_approve") {
		$data            = $_POST["potential_id"];
		$potentialSource = intval($_POST["visit_or_question"]);
		if ($DB_calendar->approve_credit_check($potentialSource, $data) > 0) {
			echo "Updated the credit check";
		}
	}

	/* Update the credit check checklist */
	if ($_POST['action'] == "updateCreditCheckList") {
		$potential_id   = $_POST["potential_id"];
		$data           = $_POST["data"];
		$visit_question = $_POST["visit_question"];
		$dataExists     = intval($_POST["checklistDataExists"]);

		if ($dataExists == 0) {
			if ($DB_tenant->insertCreditChecklist($data, $potential_id, $visit_question) > 0) {
				creditCheckListNotifyEmail($DB_calendar, $potential_id, $visit_question, $data, $checkListItemsDisplay);
				echo json_encode(array("value" => true));
			}
		} else {
			if ($DB_tenant->updateCreditChecklist($data, $potential_id, $visit_question) > 0) {
				creditCheckListNotifyEmail($DB_calendar, $potential_id, $visit_question, $data, $checkListItemsDisplay);
				echo json_encode(array("value" => true));
			}
		}
	}

	/* Tenant uploads page - file upload handler */
	if ($_POST["action"] == "tenant_file_upload") {
		$data = array();

		if (isset($_FILES)) {
			$error = false;
			$files = array();

			$uploaddir = "../../files/";
			foreach ($_FILES as $file) {
				$fileName = basename($file['name']);
				while (file_exists("../../files/" . $fileName)) {
					$fileName = str_replace(".", "_x.", $fileName);
				}

				if (move_uploaded_file($file['tmp_name'], $uploaddir . $fileName)) {
					$files[] = $fileName;
				} else {
					$error = true;
				}
			}

			$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
		} else {
			$data = array('value' => 'No file data was sent ', result => false);
		}

		echo json_encode($data);
	}

	/* Update data t*/
	if ($_POST["action"] == "tenant_uploaded_data") {
		$potential_id   = $_POST["potential"];
		$visit_question = $_POST["vorq"];
		$formData       = $_POST["value"]; /* This will be an array with the keys as each detail that was requested by the agent - each index value is a JS serialized value */

		$uploadedInfo = array();

		$info                  = $DB_tenant->getPotentialCreditChecklist($potential_id, $visit_question);
		$checkListExistingData = $info["data"];

		/* Check if the "data" column in the table is not empty for the selected potential ID record
		* If empty - update with an empty detail array in the else
		*/
		if (strlen($checkListExistingData) > 0) {
			$checkListExistingData = json_decode($checkListExistingData, true);
		} else {
			$checkListExistingData = array(
				"previous_landlord" => "",
				"regiedulogement" => "",
				"payslip" => "",
				"guarantor" => "",
				"bankstatement" => "",
				"voidcheck" => "",
				"idphoto" => ""
			);
		}

		/* Loop through the FORM post data and push it to the array $checkListExistingData */
		foreach ($formData as $data) {
			parse_str($data, $detailArray); /* Parse the JS serialized data to PHP array */
			switch (intval($detailArray["type"])) {
				case 1:
					/* Previous landlord detail */
					$checkListExistingData["previous_landlord"] = array(
						"full_name" => $detailArray["previous_landlord_fullName"],
						"telephone" => $detailArray["previous_landlord_telephone"]
					);
					break;
				case 2:
					/* Regie Du Logement */
					$checkListExistingData["regiedulogement"] = $detailArray["regiedulogement_file_choice"];
					break;
				case 3:
					/* Payslip file */
					$checkListExistingData["payslip"] = $detailArray["fileDetail"];
					break;
				case 4:
					/* Guarantor detail */
					$checkListExistingData["guarantor"] = array(
						"full_name" => $detailArray["guarantor_fullName"],
						"dob" => $detailArray["guarantor_dob"],
						"address" => $detailArray["guarantor_fulladdress"],
					);
					break;
				case 5:
					/* Bank statement file */
					$checkListExistingData["bankstatement"] = $detailArray["fileDetail"];
					break;
				case 6:
					/* Void check file */
					$checkListExistingData["voidcheck"] = $detailArray["fileDetail"];
					break;
				case 7:
					/* ID photo file */
					$checkListExistingData["idphoto"] = $detailArray["fileDetail"];
					break;
			}
		}

		if ($DB_tenant->updateCreditChecklistData(json_encode($checkListExistingData), $potential_id, $visit_question) > 0) {
			echo json_encode(array("value" => true));
		}
	}
}

/* Helper Functions */
function formalize_telephone($original_tele)
{
	$formal_tele = trim($original_tele);
	$formal_tele = str_replace(' ', '', $formal_tele);
	$formal_tele = str_replace('-', '', $formal_tele);
	if (strlen($formal_tele) == 10)
		$formal_tele = '1' . $formal_tele;
	return $formal_tele;
}

function bookingModifyEmail($DB_calendar, $bookingId)
{
	/* Send EMAIL confirmation to the customer and the agent who is assigned about the confirmation */
	include_once "../../../sendSMSEmail.php";
	include_once "../../../Class.Template.php";
	$template = new Template();

	$bookingInfo = $DB_calendar->getBookingInfo($bookingId);

	$row                              = $DB_calendar->get_visit_event($bookingInfo["event_id"]);
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

	//email to agent
	$title            = "Notification";
	$subtitle         = "We have an important message for you!";
	$email_sbj        = "spgmanagement.com - Confirmation of visit event in SPGManagement";
	$employee_body1   = "You have confirmed a visit event appointment!";
	$employee_body2   = '<b>Appointment Detail:</b>';
	$employee_body2   .= '<br>Customer Name :' . $bookingInfo["customer_name"];
	$employee_body2   .= '<br>Customer Telephone :' . $bookingInfo["telephone"];
	$employee_body2   .= '<br>Customer Email :' . $bookingInfo["customer_email"];
	$employee_body2   .= '<br>Event Description : ' . $event_description;
	$employee_body2   .= '<br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
	$employee_body2   .= '<br>Event Location : ' . $event_location;
	$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, "", "");
	SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);

	// email to customer about booking modification
	$title        = "Congratulations!";
	$subtitle     = "Your Visit Appointment is confirmed!";
	$email_sbj    = "spgmanagement.com - Confirmation of appointment";
	$client_body1 = "Congratulations! Your visit appointment is confirmed by the Agent.";
	$client_body2 = '<b>Appointment Detail :</b>';
	$client_body2 .= '<br>Event Description : ' . $event_description;
	$client_body2 .= '<br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
	$client_body2 .= '<br>Event Location : ' . $event_location;
	$client_body2 .= '<br><b>Your agent information :</b>';
	$client_body2 .= '<br>Agent Name : ' . $event_person_in_charge_name;
	$client_body2 .= '<br>Agent Telephone : ' . $event_person_in_charge_telephone;
	$client_body2 .= '<br><b>Please contact with your agent if you want to cancel the appointment.</b>';

	$button_url     = "https://spg.spgmanagement.com/visit_messages.php";
	$button_content = "Talk to Agent";
	$client_content = $template->emailTemplate($title, $subtitle, $bookingInfo["customer_name"], $client_body1, $client_body2, $button_url, $button_content);
	MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $bookingInfo["customer_email"], $bookingInfo["customer_name"], $email_sbj, $client_content);
}

function visitfeedbackEmail($DB_calendar, $bookingId, $dataToSave)
{
	include_once "../../../sendSMSEmail.php";
	include_once "../../../Class.Template.php";
	$template = new Template();

	$bookingInfo = $DB_calendar->getBookingInfo($bookingId);

	$row                              = $DB_calendar->get_visit_event($bookingInfo["event_id"]);
	$event_location                   = $row['event_location'];
	$event_description                = $row['event_description'];
	$event_person_in_charge_id        = $row['resonsible_employee_id'];
	$event_person_in_charge           = $DB_calendar->get_employee_info($event_person_in_charge_id);
	$event_person_in_charge_name      = $event_person_in_charge['full_name'];
	$event_person_in_charge_telephone = $event_person_in_charge['mobile'];
	$event_person_in_charge_email     = $event_person_in_charge['email'];

	//email to agent
	$title          = "Notification";
	$subtitle       = "We have an important message for you!";
	$email_sbj      = "spgmanagement.com - Customer Feedback of Visit";
	$employee_body1 = "A customer has submitted feedback of their recent visit to $event_location";
	$employee_body2 = '<b>Appointment Detail:</b>';
	$employee_body2 .= '<br>Customer Name :' . $bookingInfo["customer_name"];
	$employee_body2 .= '<br>Customer Telephone :' . $bookingInfo["telephone"];
	$employee_body2 .= '<br>Customer Email :' . $bookingInfo["customer_email"];
	$employee_body2 .= '<br>Event Description : ' . $event_description;
	$employee_body2 .= '<br>Event Time : ' . $bookingInfo["booking_date"] . ' ' . $bookingInfo["booking_start"];
	$employee_body2 .= '<br>Event Location : ' . $event_location;

	$employee_body2 .= '<br> <b> Feedback </b>';
	$employee_body2 .= '<br> Agent : ' . $dataToSave["agent"] . '/5';
	$employee_body2 .= '<br> <b> Comments about Agent : </b> ' . $dataToSave["feedback_agent"];
	$employee_body2 .= '<br> Apartment : ' . $dataToSave["apartment"] . '/5';
	$employee_body2 .= '<br> <b> Comments about Apartment : </b> ' . $dataToSave["feedback_apt"];

	$employee_content = $template->emailTemplate($title, $subtitle, $event_person_in_charge_name, $employee_body1, $employee_body2, "", "");
	MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $event_person_in_charge_email, $event_person_in_charge_name, $email_sbj, $employee_content);
}

function creditCheckEmail($DB_calendar, $potentialId)
{
	include_once "../../../sendSMSEmail.php";
	include_once "../../../Class.Template.php";
	$template = new Template();

	$potentialCustomerData = $DB_calendar->getBookingInfo($potentialId);

	$customerName  = $potentialCustomerData["customer_name"];
	$customerEmail = $potentialCustomerData["customer_email"];

	//email to agent
	$title          = "Notification";
	$subtitle       = "We have an important message for you!";
	$email_sbj      = "spgmanagement.com - Online Credit Check ";
	$employee_body1 = "You are invited to use our online Credit Investigation System.";

	$employee_body2 = "<br>Please click on the button below to fill your details.<br>";

	$button_url     = "https://spg.spgmanagement.com/creditcheck/form.php?pt=$potentialId";
	$button_content = "Click here";

	$employee_content = $template->emailTemplate($title, $subtitle, $customerName, $employee_body1, $employee_body2, "", "");
	MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $customerEmail, $customerName, $email_sbj, $employee_content);
}

function creditCheckListNotifyEmail($DB_calendar, $potentialId, $visit_question, $data, $checkListItemsDisplay)
{
	include_once "../../../sendSMSEmail.php";
	include_once "../../../Class.Template.php";
	$template = new Template();

	$potentialCustomerData = $DB_calendar->getPotentialDetailFromView($potentialId, $visit_question);

	$customerName              = $potentialCustomerData["customer_name"];
	$customerEmail             = $potentialCustomerData["customer_email"];
	$requiredDetailsFromTenant = array();

	foreach ($data as $key => $datum) {
		if ($data[$key] == 0) {
			array_push($requiredDetailsFromTenant, $checkListItemsDisplay[$key]);
		}
	}

	$requiredDetailsText = implode("-", $requiredDetailsFromTenant);

	//email to agent
	$title          = "Notification";
	$subtitle       = "We have an important message for you!";
	$email_sbj      = "spgmanagement.com - Online Credit Check Details";
	$employee_body1 = "We require some more details from you to proceed further with the Credit Check process : ";
	$employee_body1 = "<br> $requiredDetailsText";

	$employee_body2 = "<br>Please click on the button below to fill your details.<br>";

	$button_url     = urlencode("https://spg.spgmanagement.com/creditcheck/tenant_uploads.php?p=" . $potentialId . "&req=" . $requiredDetailsText . "&vorq=" . $visit_question);
	$button_content = "Click here";

	$employee_content = $template->emailTemplate($title, $subtitle, $customerName, $employee_body1, $employee_body2, "", "");
	MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $customerEmail, $customerName, $email_sbj, $employee_content);
}