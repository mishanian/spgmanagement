<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	include_once("../../../pdo/dbconfig.php");
	include_once('../../../pdo/Class.Services.php');
    $DB_services = new Services($DB_con);
if (isset($_POST['process_click'])) {
	$user_id            = $_SESSION["UserID"];
	$company_id         = $_SESSION["company_id"];
	$service_avails     = $DB_services->get_services_availabilities_for_user($user_id);
	$credit_check_avail = $service_avails["credit_check"];
	$lease_sign_avail   = $service_avails["lease_sign"];

	if ($company_id == 9) {
		$credit_check_avail = 1;
		$lease_sign_avail   = 1;
	}

	$service_type = $_POST['services'];

	if ($service_type === 'credit_check' && $credit_check_avail > 0) {
		header('Location:../../creditcheck.php');
	}
	elseif ($service_type === 'lease_sign' && $lease_sign_avail > 0) {
		header('Location:../../lease_signing.php');
	}
	else {
		header('Location:../../services.php?no_accessibility');
	}
}

if (isset($_POST['credit_check_submit'])) {

	$user_id          = $_SESSION['UserID'];
	$landlord_name    = $_POST['client_name'];
	$landlord_tel     = $_POST['client_tel'];
	$landlord_email   = $_POST['client_email'];
	$landlord_address = $_POST['client_address'];
	$tenant_name      = $_POST['tenant_name'];
	$tenant_tel       = $_POST['tenant_tel'];
	$tenant_email     = $_POST['tenant_email'];
	$tenant_address   = $_POST['tenant_address'];
	$tenant_province  = $_POST['tenant_province'];

	//generate unique random token
	$random_bytes = bin2hex(openssl_random_pseudo_bytes(10));
	//add timestamp
	$auto_token = uniqid($random_bytes);

	$current_time = date("Y-m-d H:i:s");

	$credit_request_id = $DB_services->add_credit_check_request($user_id, $current_time, $auto_token, $landlord_name, $landlord_email, $landlord_tel, $landlord_address, $tenant_name, $tenant_tel, $tenant_email, $tenant_address, $tenant_province);

	include_once("Class.CreditInfoRequest.php");
	$send_requst = new CreditCheckRequest($credit_request_id);
	$send_requst->send_credit_info_request_email();
	$send_requst->send_credit_info_request_sms();

	header('Location:../../service_approve.php?service_type=credit_check');
}


//------------------------- lease signing requests handler --------------------------

if (isset($_POST['action']) && $_POST['action'] == 'auto_fill_building_info') {

	$selected_building_id = $_POST['building_id'];
	$data                 = array();
	try {
		include_once("../../../pdo/dbconfig.php");

		//get unit list
		$apart_list = $DB_services->get_apt_list_in_building($selected_building_id);

		$unit_list = array();

		foreach ($apart_list as $one) {
			$temp                 = array();
			$temp['apartment_id'] = $one['apartment_id'];
			$temp['unit_number']  = $one['unit_number'];

			//calculate number of rooms
			$bedrooms = $bath_rooms = 0;
			if ($one['bedrooms'] != null) {
				$bedrooms = $one['bedrooms'];
			}
			if ($one['bath_rooms'] != null) {
				$bath_rooms = $one['bath_rooms'];
			}
			$temp['rooms'] = $bedrooms + $bath_rooms;

			array_push($unit_list, $temp);
		}

		$data['apartments'] = $unit_list;

		//get the building info details
		$building_info       = $DB_services->get_building_info_by_id($selected_building_id);
		$data['city']        = $building_info['city'];
		$data['postal_code'] = $building_info['postal_code'];

		$feedback['code'] = 1;
		$feedback['data'] = $data;

	}
	catch (Exception $e) {
		$feedback['code']    = 0;
		$feedback['message'] = $e->getMessage();
	}

	echo json_encode($feedback);
}
