<?php
error_reporting(E_ALL);
include_once('../../../pdo/dbconfig.php');
include_once('../../../pdo/Class.Request.php');
$DB_request = new Request($DB_con);
/* Default time zone set to New YORK - eastern time */
date_default_timezone_set("America/New_York");

if ($_POST['action'] == 'get_communications') {
	$request_id = $_POST['request_id'];
	$user_id    = $_POST['user_id'];
	echo json_encode(get_communications($request_id, $user_id));
}


if ($_POST['action'] == 'add_communication') {

	$img_file = null;
	$img_path = null;

	$request_id        = $_POST['request_id'];
	$message           = $_POST['message'];
	$user_id           = $_POST['user_id'];
	$message           = $_POST['message'];
	$if_seen_by_tenant = $_POST['if_seen_by_tenant'];
	$if_notify         = $_POST['if_notify'];
	$entry_time        = date("Y-m-d H:i:s");

	if (!empty($_FILES['upload_img'])) {
		$files = array();
		$error = false;

		//		$img_file = $_FILES['upload_img'];
		//		if ($img_file['error'] <= 0) {
		//			$file_name = $img_file['name'];
		//			while (file_exists("../../files/requests/" . $file_name)) {
		//				$file_name = str_replace(".", "_a.", $file_name);
		//			}
		//			$file_tmp_name = $img_file['tmp_name'];
		//			move_uploaded_file($file_tmp_name, "../../files/requests/" . $file_name);
		//			$img_path = $file_name;
		//		}

		// Count # of uploaded files in array
		$total = count($_FILES['upload_img']['name']);

		for ($i = 0; $i < $total; $i++) {
			$tmpFilePath = $_FILES['upload_img']['tmp_name'][$i];

			$fileName = $_FILES['upload_img']['name'][$i];

			if ($tmpFilePath != "") {
				$newFilePath = "../../files/requests/" . $_FILES['upload_img']['name'][$i];
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$files[] = $fileName;
				} else {
					$error = true;
				}
			}
		}
	}

	if (count($files) > 0) {
		foreach ($files as $uploadedImg) {
			$DB_request->add_one_communication($request_id, $user_id, $uploadedImg, $entry_time, $if_seen_by_tenant, 1);
		}
	} else {
		if (strlen($message) > 0) {
			$DB_request->add_one_communication($request_id, $user_id, $message, $entry_time, $if_seen_by_tenant);
		}
	}

	//change the last time when user create this issue
	$current_time = date('Y-m-d H:i:s');
	$DB_request->update_client_last_access_time($current_time, $request_id, $user_id);

	//return latest communications
	$feedback = get_communications($request_id, $user_id);

	//force notify
	if ($if_notify == 'true') {
		//	include_once("../Class.SendSMS.php");
		include_once("../sendSMSEmail.php");

		$reporter_info = $DB_request->get_user_info($user_id);
		$all_assignees = $DB_request->get_all_assignees_for_issue($request_id);

		foreach ($all_assignees as $one) {
			$user_id       = $one['user_id'];
			$user_info     = $DB_request->get_user_info($user_id);
			$receiver      = $user_info['full_name'];
			$phone_number  = $user_info['mobile'];
			$email_address = $user_info['email'];
			$methods       = $DB_request->get_notify_by($request_id, $user_id);
			if (!empty($_POST['building_id'])) {
				$building_id = $_POST['building_id'];
				$building_info = $DB_request->get_building_info($building_id);
				$building_name = $building_info['building_name'];
			} else {
				$building_name = "";
			}
			if (!empty($_POST['apartment_id'])) {
				$apartment_id = $_POST['apartment_id'];
				$apartment_info = $DB_request->get_tenant_unit($apartment_id);
				$apartment_name = $apartment_info['apartment_name'];
			} else {
				$apartment_name = "";
			}
			if ($methods['notify_by_email'] == 1) {
				$title          = "Notification";
				$subtitle       = "There is a new communication in Requests  --spgmanagement.com";
				$body1          = "There is a new communication in Request. Please refer to the details below";
				$body2          = "<p>Reporter Name : " . $reporter_info['full_name'] . "<br>" . $reporter_info['mobile'] . " - " . $reporter_info['email'] . "<br>$building_name - $apartment_name</be></p><p>Created Time : " . $entry_time . "</p><p>Message : " . $_POST['message'] . "</p>";
				$button_url     = "https://www.spgmanagement.com/admin/login";
				$button_content = "Log in to check it";

				include_once "../../../pdo/Class.Template.php";
				$template       = new Template();
				$email_template = $template->emailTemplate($title, $subtitle, $receiver, $body1, $body2, $button_url, $button_content);

				$subject = "Notification from spgmanagement.com";
				$smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email_address, $receiver, $subject, $email_template);
			}
			if ($methods['notify_by_sms'] == 1) {
				$message      = "Dear " . $receiver . ",\n you have a new communication in Request: \nMessage: " . $_POST['message'] . "\n-- spgmanagement.com";
				$phone_number = formalize_telephone($phone_number);
				// SendSMS($phone_number, $message);
				// sleep(1);
				//$send_sms->sendSMS();
			}
			if ($methods['notify_by_voice'] == 1) {
				//no voice now
			}
		}
	}

	if (array_key_exists('close_request', $_POST) && isset($_POST['close_request'])) {
		$DB_request->change_request_status($request_id, 4);
	}

	echo json_encode($feedback);
}

//------------ confirmation of handyman reservation --------------

if ($_POST['action'] == 'change_repair_event_status') {
	$request_communication_id = $_POST['request_communication_id'];

	$user_action = $_POST['user_action'];
	$user_id     = $_POST['user_id'];
	$request_id  = $_POST['request_id'];

	$current_time = date('Y-m-d H:i:s');

	if ($user_action == 'force_confirm') {
		$DB_request->change_handyman_booking_confirm_status($request_communication_id, 2);
		//change communication message
		$remark = "THE REPAIR EVENT HAS BEEN CONFIRMED !";
		$DB_request->edit_system_communication_for_request($request_communication_id, $remark, $current_time, 1, 1, 0);
	} else if ($user_action == 'confirm') {
		$confirmed_status = intval($DB_request->get_handyman_booking_confirm_status($request_communication_id));
		$confirmed_status++;
		$DB_request->change_handyman_booking_confirm_status($request_communication_id, $confirmed_status);
		//change communication message
		$remark = "THE REPAIR EVENT HAS BEEN APPROVED !";
		$DB_request->edit_system_communication_for_request($request_communication_id, $remark, $current_time, 0, 1, 0);
	} else if ($user_action == 'cancel') {
		$DB_request->change_handyman_booking_confirm_status($request_communication_id, 0);
		//change communication message
		$remark = 'SORRY ! THE REPAIR EVENT HAS BEEN CANCELLED FOR SOME REASONS';
		$DB_request->edit_system_communication_for_request($request_communication_id, $remark, $current_time, 1, 1, 0);
	}

	//return
	echo json_encode(get_communications($request_id, $user_id));
}


//--------------  inner functions -------------

function get_communications($request_id, $user_id)
{
	global $DB_request;

	$is_tenant           = false;
	$is_handyman         = false;
	$is_property_manager = false;

	if ($user_id > 100000 && $user_id < 200000)
		$is_tenant = true;

	if ($user_id < 100000) {
		$employee_info = $DB_request->get_employee_info($user_id);
		if ($employee_info['user_level'] == 11) {
			$is_handyman = true;
		}
		if ($employee_info['admin_id'] == 1) {
			$is_property_manager = true;
		}
	}

	$communications     = $DB_request->get_all_communications($request_id);
	$arr_communications = array();

	foreach ($communications as $communication) {
		$communication_id    = $communication['communication_id'];
		$creator_id          = $communication['user_id'];
		$remark              = $communication['remarks'];
		$creator_role        = $communication['user_role'];
		$created_time        = $communication['entry_date'];
		$is_system_msg       = $communication['is_system_msg'];
		$if_seen_by_tenant   = $communication['if_seen_by_tenant'];
		$is_image            = $communication['is_image'];
		$system_message_type = $communication['system_msg_type'];
		$creator_name        = '';

		if ($is_tenant && $if_seen_by_tenant == 0)
			continue;
		if ($is_handyman && $system_message_type == 1)
			continue;
		if ($is_property_manager && $system_message_type == 2)
			continue;

		if ($creator_id != 0) {
			$user_info    = $DB_request->get_user_info($creator_id);
			$creator_name = $user_info['full_name'];
		}

		$arr_one_communication                        = array();
		$arr_one_communication['communication_id']    = $communication_id;
		$arr_one_communication['remark']              = $is_image == 0 ? $remark : "files/requests/" . $remark;
		$arr_one_communication['creator_id']          = $creator_id;
		$arr_one_communication['creator_name']        = $creator_name;
		$arr_one_communication['creator_role']        = $creator_role;
		$arr_one_communication['created_time']        = $created_time;
		$arr_one_communication['is_system_msg']       = $is_system_msg;
		$arr_one_communication['if_seen_by_tenant']   = $if_seen_by_tenant;
		$arr_one_communication['is_image']            = $is_image;
		$arr_one_communication['system_message_type'] = $system_message_type;

		//related assignees for every communication
		$arr_assignees     = array();
		$assignees_related = $DB_request->get_assignee_detail_infos($request_id);

		foreach ($assignees_related as $row) {
			$assignee_id           = $row['user_id'];
			$user_name             = $row['user_name'];
			$user_role             = $row['user_role'];
			$user_last_access_time = $row['last_access_time'];


			if (strtotime($created_time) <= strtotime($user_last_access_time))
				$read_status = 'read';
			else
				$read_status = 'unread';

			$one_assignee_info                     = array();
			$one_assignee_info['user_name']        = $user_name;
			$one_assignee_info['user_role']        = $user_role;
			$one_assignee_info['read_status']      = $read_status;
			$one_assignee_info['last_access_time'] = $user_last_access_time;

			array_push($arr_assignees, $one_assignee_info);
		}

		$arr_one_communication['assignees_status'] = $arr_assignees;

		// add one communication info to communications set
		array_push($arr_communications, $arr_one_communication);
	}

	return $arr_communications;
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