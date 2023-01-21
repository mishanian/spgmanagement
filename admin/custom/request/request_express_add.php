<?
include_once("../Class.SendSMS.php");
include_once("../sendSMSEmail.php");

$notificationData = $_POST["data"];

// Variables necessary for the notification OP
$notify_methods          = $notificationData["notify_methods"];
$building_id             = $notificationData["building_id"];
$request_type_id         = $notificationData["request_type_id"];
$request_id              = $notificationData["request_id"];
$notification_queue      = array();
$notify_default_by_email = $notificationData["notify_default_by_email"];
$notify_default_by_sms   = $notificationData["notify_default_by_sms"];
$notify_default_by_voice = $notificationData["notify_default_by_voice"];
$message                 = $notificationData["message"];
$entry_datetime          = $notificationData["entry_datetime"];
$created_user_id         = $notificationData["employee_id"];
$reportTenantIds         = $notificationData["reportTenantIds"];
$vendorId                = $notificationData["vendor_id"]; /* Vendor id is 0 if the request is not a project / contract type request */

// Assign the default values for the modes of notification
if (!empty($notify_methods)) {
	$notify_methods = explode(',', $notify_methods);
	if (in_array("1", $notify_methods)) {
		$notify_default_by_email = 1;
	}
	if (in_array("2", $notify_methods)) {
		$notify_default_by_sms = 1;
	}
	if (in_array("3", $notify_methods)) {
		$notify_default_by_voice = 1;
	}
}

//add administrator
$administrators = $DB_request->get_administrator_for_building($building_id);
foreach ($administrators as $admin) {
	$employee_id = $admin['employee_id'];
	if ($employee_id == $created_user_id) {
		// This employeeID is already assigned the request
		continue;
	}
	if (!in_array($employee_id, $notification_queue)) {
		$DB_request->add_request_assignee($request_id, $employee_id, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);
		array_push($notification_queue, $employee_id);
	}
}

//add responsible employees
$default_employees = $DB_request->get_default_employees($building_id, $request_type_id);
foreach ($default_employees as $default_employee) {
	$default_employee_id = $default_employee['employee_id'];

	if ($default_employee_id == $created_user_id) {
		// This $default_employee_id is already assigned the request
		continue;
	}

	if (!in_array($default_employee_id, $notification_queue)) {
		$DB_request->add_request_assignee($request_id, $default_employee_id, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);
		array_push($notification_queue, $default_employee_id);
	}
}

//----------------- notification -------------------
$reporter_info   = $DB_request->get_user_info($created_user_id);
$request_info    = $DB_request->get_notify_info($request_id);
$building_name   = $request_info['location_1'];
$location        = $request_info['location_2'];
$request_type    = $request_info['request_type_name'];
$request_message = $request_info['message'];

/* Sending Vendor an email about the request */
/* if the vendor iD is not 0 - push the id as a assignee for the request and send an email - same as the tenant */
if (intval($vendorId) != 0) {
	$DB_request->add_request_assignee($request_id, $vendorId, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);

	$user_id       = $vendorId;
	$user_info     = $DB_request->get_user_info($user_id);
	$receiver      = $user_info['full_name'];
	$phone_number  = $user_info['mobile'];
	$email_address = $user_info['email'];
	$methods       = $DB_request->get_notify_by($request_id, $user_id);

	if (intval($methods['notify_by_email']) == 1) {
		$title          = "Task Notification";
		$subtitle       = "New Request for you from SPGManagement";
		$body1          = "There is a new request task for you. <br><br> ";
		$body1          .= "When the requested task is complete, please click on the button below to mark it as complete and get the signature of the tenant if applicable. <br/><br/>";
		$body1          .= "Please refer to the details below :";
		$body2          = "<p><strong>Reporter Name </strong>: " . $reporter_info['full_name'] . "</p>  <p> <strong>Contact #:</strong> " . $phone_number . " </p> <p> <strong> Created Time : </strong>" . $entry_datetime . "</p><p> <strong>Message </strong> : " . $message . "</p>";
		$button_url     = "https://spg.spgmanagement.com/approvereq.php?rid=" . $request_id . "&mker=" . base64_encode("SPGManagementrequesttenanturl");
		$button_content = "Mark the task as complete";

		include_once "../../../pdo/Class.Template.php";
		$template       = new Template();
		$email_template = $template->emailTemplate($title, $subtitle, $receiver, $body1, $body2, $button_url, $button_content);
		$subject        = "Notification from spgmanagement.com";
		SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email_address, $receiver, $subject, $email_template);
	}
	if ($methods['notify_by_sms'] == 1) {
		$message      = "Dear " . $receiver . ",\n you have a new request: \nLocation: " . $location . " in " . $building_name . ". \nRequest Type: " . $request_type . ". \nMessage: " . $request_message . "\n-- spgmanagement.com";
		$phone_number = formalize_telephone($phone_number);
		$send_sms     = new SendSMS($phone_number, $message);
		sleep(1);
		try {
			$send_sms->sendSMS();
		} catch (Exception $e) {
			/* Store the errors */
		}
	}
}

//register tenants (if any) to DB
if (!empty($reportTenantIds)) {
	foreach ($reportTenantIds as $report_tenant_id) {
		$DB_request->add_request_assignee($request_id, $report_tenant_id, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);

		$user_id       = $report_tenant_id;
		$user_info     = $DB_request->get_user_info($user_id);
		$receiver      = $user_info['full_name'];
		$phone_number  = $user_info['mobile'];
		$email_address = $user_info['email'];
		$methods       = $DB_request->get_notify_by($request_id, $user_id);

		if (intval($methods['notify_by_email']) == 1) {
			$title          = "Notification";
			$subtitle       = "New Request for your Apartment in SPGManagement";
			$body1          = "There is a new request for your apartment in spgmanagement.com. <br><br> ";
			$body1          .= "When the requested task is complete. Please click on the button below to mark it as complete.";
			$body1          .= "Please refer to the details below :";
			$body2          = "<p><strong>Reporter Name </strong>: " . $reporter_info['full_name'] . "</p><p> <strong> Created Time : </strong>" . $entry_datetime . "</p><p> <strong>Message </strong> : " . $message . "</p>";
			$button_url     = "https://spg.spgmanagement.com/approvereq.php?rid=" . $request_id . "&mker=" . base64_encode("SPGManagementrequesttenanturl");
			$button_content = "Mark the request as complete";

			include_once "../../../pdo/Class.Template.php";
			$template       = new Template();
			$email_template = $template->emailTemplate($title, $subtitle, $receiver, $body1, $body2, $button_url, $button_content);
			$subject        = "Notification from spgmanagement.com";
			SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email_address, $receiver, $subject, $email_template);
		}
		if ($methods['notify_by_sms'] == 1) {
			$message      = "Dear " . $receiver . ",\n you have a new request: \nLocation: " . $location . " in " . $building_name . ". \nRequest Type: " . $request_type . ". \nMessage: " . $request_message . "\n-- spgmanagement.com";
			$phone_number = formalize_telephone($phone_number);
			$send_sms     = new SendSMS($phone_number, $message);
			sleep(1);
			try {
				$send_sms->sendSMS();
			} catch (Exception $e) {
				/* Store the errors */
			}
		}
	}
}

foreach ($notification_queue as $one) {
	$user_id       = $one;
	$user_info     = $DB_request->get_user_info($user_id);
	$receiver      = $user_info['full_name'];
	$phone_number  = $user_info['mobile'];
	$email_address = $user_info['email'];
	$methods       = $DB_request->get_notify_by($request_id, $user_id);

	if (intval($methods['notify_by_email']) == 1) {
		$title          = "Notification";
		$subtitle       = "There is a new request in spgmanagement.com";
		$body1          = "There is a new request in spgmanagement.com. Please refer to the details below";
		$body2          = "<p>Reporter Name : " . $reporter_info['full_name'] . "</p><p>Created Time : " . $entry_datetime . "</p><p>Message : " . $message . "</p>";
		$button_url     = "https://www.spgmanagement.com/admin/login";
		$button_content = "Log in to check it";

		include_once "../../../pdo/Class.Template.php";
		$template       = new Template();
		$email_template = $template->emailTemplate($title, $subtitle, $receiver, $body1, $body2, $button_url, $button_content);
		$subject        = "Notification from spgmanagement.com";
		SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email_address, $receiver, $subject, $email_template);
	}
	if ($methods['notify_by_sms'] == 1) {
		$message      = "Dear " . $receiver . ",\n you have a new request: \nLocation: " . $location . " in " . $building_name . ". \nRequest Type: " . $request_type . ". \nMessage: " . $request_message . "\n-- spgmanagement.com";
		$phone_number = formalize_telephone($phone_number);
		$send_sms     = new SendSMS($phone_number, $message);
		sleep(1);
		try {
			$send_sms->sendSMS();
		} catch (Exception $e) {
			/* Store the errors */
		}
	}
	/* Add another if for the Voice when needed */
}