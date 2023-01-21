<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../../pdo/dbconfig.php');
include_once('../../../pdo/Class.Request.php');
$DB_request = new Request($DB_con);
include_once('../../../pdo/Class.Vendor.php');
$DB_vendor = new Vendor($DB_con);



if (isset($_POST) && !empty($_POST)) {

	if ($_POST['action'] == 'set_recipients') {
		$request_id = $_POST['request_id'];

		//which methods will be used to notify default employees.
		$notify_default_by_email = 0;
		$notify_default_by_sms   = 0;
		$notify_default_by_voice = 0;
		$notify_methods          = explode(",", $DB_request->get_default_ntf_methods_request_id($request_id)['ntf_methods']);
		if (in_array("1", $notify_methods)) {
			$notify_default_by_email = 1;
		}
		if (in_array("2", $notify_methods)) {
			$notify_default_by_sms = 1;
		}
		if (in_array("3", $notify_methods)) {
			$notify_default_by_voice = 1;
		}

		$assigned_recipients = $DB_request->get_assigned_recipients($request_id);

		$DB_request->delete_recipients($request_id);

		$employee_rows = $_POST['employees'];
		foreach ($employee_rows as $employee_row) {

			if ($employee_row['assigned'] == '1') {

				// $existed = false;
				// foreach ($assigned_recipients as $assigned_recipient) {
				// 	if ($employee_row['employee_id'] == $assigned_recipient['user_id']) {
				// 		echo "employee ".$employee_row['employee_id'];
				// 		$existed = true;
				// 	}
				// }

				// if (!$existed) {
				$DB_request->add_request_assignee($request_id, $employee_row['employee_id'], '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);
				// }
			}

			if ($employee_row['assigned'] == '0') {
				// foreach ($assigned_recipients as $assigned_recipient) {
				// if ($employee_row['employee_id'] == $assigned_recipient['user_id']) {
				$DB_request->delete_recipient($request_id, $employee_row['employee_id']);
				// }
				// }
			}
		}

		if (isset($_POST['tenants'])) {
			$tenant_rows = $_POST['tenants'];
			foreach ($tenant_rows as $tenant_row) {
				if ($tenant_row['assigned'] == '1') {
					// $existed = false;
					// foreach ($assigned_recipients as $assigned_recipient) {
					// 	if ($tenant_row['tenant_id'] == $assigned_recipient['user_id']) {
					// 		$existed = true;
					// 	}
					// }
					// if (!$existed) {
					// }

					$DB_request->add_request_assignee($request_id, $tenant_row['tenant_id'], '2000-01-01 00:00:00', 0, 0, 0);
				} else if ($tenant_row['assigned'] == '0') {
					// foreach ($assigned_recipients as $assigned_recipient) {
					// 	if ($tenant_row['tenant_id'] == $assigned_recipient['user_id']) {
					$DB_request->delete_recipient($request_id, $assigned_recipient['user_id']);
					// 	}
					// }
				}
			}
		}

		if (isset($_POST['vendors'])) {
			$vendor_rows = $_POST['vendors'];
			foreach ($vendor_rows as $vendor_row) {
				if ($vendor_row['assigned'] == '1') {
					// $existed = false;
					// foreach ($assigned_recipients as $assigned_recipient) {
					// 	if ($vendor_row['vendor_id'] == $assigned_recipient['user_id']) {
					// 		$existed = true;
					// 	}
					// }
					// if (!$existed) {
					//
					// }

					$DB_request->add_request_assignee($request_id, $vendor_row['vendor_id'], '2000-01-01 00:00:00', 0, 0, 0);

					// Update the vendor id for the request id
					$DB_request->updateVendorId($request_id, $vendor_row['vendor_id']);
				} else if ($vendor_row['assigned'] == '0') {
					// foreach ($assigned_recipients as $assigned_recipient) {
					// 	if ($vendor_row['vendor_id'] == $assigned_recipient['user_id']) {
					$DB_request->delete_recipient($request_id, $assigned_recipient['user_id']);
					// 	}
					// }
				}
			}
		}

		//update the issue_category if it become tenant issue
		$DB_request->update_issue_category_if_tenant_issue($request_id);
		return true;
	} else if ($_POST['action'] == 'get_modal_info') {
		$request_id = $_POST['request_id'];
		$user_id    = $_POST['user_id'];
		$unit_id    = $_POST['user_unit_id'];   // to handle the case tenant has many units

		// change the last time when user click this issue
		$current_time = date('Y-m-d H:i:s');
		$DB_request->update_client_last_access_time($current_time, $request_id, $user_id);


		// get modal info
		$request_info                       = $DB_request->get_one_request_info($request_id);
		$created_user_info                  = $DB_request->get_user_info($request_info['employee_id']);
		$apart_info                         = $DB_request->get_apartment_info($request_id);
		$feedback                           = array();
		$feedback['request_id']             = $request_id;
		$feedback['request_category']       = $request_info['request_category'];
		$feedback['open_or_close']          = $request_info['close_or_open'];
		$feedback['request_type']           = $request_info['request_type'];
		$feedback['created_user_name']      = $created_user_info['full_name'];
		$feedback['created_user_telephone'] = $created_user_info['mobile'];
		$feedback['request_status']         = strtoupper($request_info['request_status']);
		$created_time                       = $request_info['entry_datetime'];
		$feedback['created_date']           = date("Y/m/d", strtotime($created_time));
		$feedback['created_time']           = date("H:i:s", strtotime($created_time));

		$feedback['building_name']    = $apart_info['building_name'];
		$feedback['specific_area']    = $apart_info['specific_area'];
		$feedback['building_address'] = $apart_info['building_address'];

		//if seen by manager, status change from pending to read_by_manager
		if ($request_info['request_status'] == 'Pending' && $user_id < 100000) {
			$DB_request->change_request_status($request_id, 5);
		}


		if (strlen($apart_info['building_picture']) > 0) {
			$feedback['building_picture'] = 'files/requests/' . $apart_info['building_picture'];
		} else {
			$feedback['building_picture'] = 'files/requests/request_building_mask.jpeg';
		}


		$statistics                     = count_open_unread_issue($user_id, $unit_id);
		$feedback['open_issue_count']   = $statistics['open_issue_count'];
		$feedback['unread_issue_count'] = $statistics['unread_issue_count'];

		echo json_encode($feedback);
	} /*
else if($_POST['action'] == 'get_attachments'){
    $pictures= array();

    $request_id = $_POST['request_id'];
    $request_info = $DB_request->get_one_request_info($request_id);
    $files = $request_info['file'];

    if(strlen($files)>0){
        $img_urls = explode(',',$request_info['file']);
        foreach ($img_urls as $url){
            $temp = 'files/requests/'.$url;
            array_push($pictures,$temp);
        }
    }

    echo json_encode($pictures);
}

else if($_POST['action'] == 'add_attachments'){
    $request_id = $_POST['request_id'];

    //get the old value of file field
    $request_info = $DB_request->get_one_request_info($request_id);
    $current_files = explode(',',$request_info['file']);

    $file_uploader = $_FILES['file'];
    $pictures = [];
    $num_pics = sizeof($file_uploader['name']);

    //transfer html 2-d array into pic-based 2-d array
    for ($index = 0; $index < $num_pics; $index++) {
        $temp = array();
        $temp['name'] = $file_uploader['name'][$index];
        $temp['type'] = $file_uploader['type'][$index];
        $temp['tmp_name'] = $file_uploader['tmp_name'][$index];
        $temp['error'] = $file_uploader['error'][$index];
        array_push($pictures, $temp);
    }

    //deal with pictures
    $files_name_arr = array();

    foreach ($pictures as $one) {
        if ($one['error'] <= 0) {
            $picture_name = $one['name'];
            while (file_exists("../../files/requests/" . $picture_name)) {
                $picture_name = str_replace(".", "_a.", $picture_name);
            }
            $picture_tmp_name = $one['tmp_name'];
            move_uploaded_file($picture_tmp_name, "../../files/requests/" . $picture_name);
            array_push($files_name_arr, $picture_name);
        }
    }

    $new_file_value = array_merge($current_files, $files_name_arr);

    $file = implode(',', $new_file_value);

    $DB_request->update_attachments($request_id,$file);

    //feedback
    $request_info = $DB_request->get_one_request_info($request_id);
    $files = $request_info['file'];

    if(strlen($files)>0){
        $img_urls = explode(',',$files);
        foreach ($img_urls as $url){
            $temp = 'files/requests/'.$url;
            array_push($pictures,$temp);
        }
    }

    echo json_encode($pictures);
}
*/ else if ($_POST['action'] == 'get_recipients') {
		//get the request id and the assigned recipients
		$request_id          = $_POST['request_id'];
		$assigned_recipients = $DB_request->get_assigned_recipients($request_id);

		$request_open_or_close = $DB_request->check_request_open_or_closed($request_id);

		$feedback['open_or_close'] = $request_open_or_close;

		//get employees
		$employee_rows = $DB_request->get_request_employees($request_id);
		$arr_employees = array();
		foreach ($employee_rows as $employee_row) {
			$employee_row['assigned']               = 0;
			$employee_row['notifyEmployeesByEmail'] = 0;
			$employee_row['notifyEmployeesBySms']   = 0;
			$employee_row['notifyEmployeesByVoice'] = 0;
			//check if this employee is in the assigned_recipient list
			foreach ($assigned_recipients as $assigned_recipient) {
				if ($employee_row['employee_id'] == $assigned_recipient['user_id']) {
					$employee_row['assigned']               = 1;
					$employee_row['notifyEmployeesByEmail'] = $assigned_recipient['notify_by_email'];
					$employee_row['notifyEmployeesBySms']   = $assigned_recipient['notify_by_sms'];
					$employee_row['notifyEmployeesByVoice'] = $assigned_recipient['notify_by_voice'];
				}
			}
			array_push($arr_employees, $employee_row);
		}
		$feedback['employees'] = $arr_employees;

		//get tenants
		if (intval($DB_request->get_request_ori_info($request_id)['location']) == 2) {
			$tenant_rows = $DB_request->get_request_tenants($request_id);
			$arr_tenants = array();
			foreach ($tenant_rows as $tenant_row) {
				$tenant_row['assigned'] = 0;
				foreach ($assigned_recipients as $assigned_recipient) {
					if ($tenant_row['tenant_id'] == $assigned_recipient['user_id']) {
						$tenant_row['assigned']             = 1;
						$tenant_row['notifyTenantsByEmail'] = $assigned_recipient['notify_by_email'];
						$tenant_row['notifyTenantsBySms']   = $assigned_recipient['notify_by_sms'];
						$tenant_row['notifyTenantsByVoice'] = $assigned_recipient['notify_by_voice'];
					}
				}
				array_push($arr_tenants, $tenant_row);
			}
			$feedback['tenants'] = $arr_tenants;
		}

		// Get Vendors List
		$requestInfo   = $DB_request->get_one_request_info($request_id);
		$requestTypeId = $DB_request->getRequestType($request_id);
		$requestTypeId = strval($requestTypeId["request_type_id"]);
		$vendorsList   = $DB_vendor->getVendorsList();

		$assignedUsersVendor = $DB_request->get_assigned_recipients($request_id);
		$assignedUsersIds    = array();
		foreach ($assignedUsersVendor as $assignedUsers) {
			array_push($assignedUsersIds, $assignedUsers["user_id"]);
		}

		$feedback['vendors'] = array();
		// Filter vendors by request type id
		$vendorsListFiltered = array();
		foreach ($vendorsList as $vendor) {
			$vendorRequestTypeIds = $vendor["request_type_id"];
			$explodeTypes         = explode(",", $vendorRequestTypeIds);

			$vendor["assigned"] = 0;

			if (in_array($requestTypeId, $explodeTypes)) {
				if (in_array($vendor["vendor_id"], $assignedUsersIds)) {
					$vendor["assigned"] = 1;
				}
				array_push($vendorsListFiltered, $vendor);
			}
		}

		$feedback['vendors']    = $vendorsListFiltered;
		$feedback['request_id'] = $request_id;

		echo json_encode($feedback);
	} else if ($_POST['action'] == 'get_editing') {
		$request_id = $_POST['request_id'];
		$user_level = $_POST['user_level'];
		$user_id    = $_POST['user_id'];

		$request_info = $DB_request->get_request_ori_info($request_id);

		$open_or_close         = $request_info['close_or_open'];
		$issue_past_after_days = $request_info['issue_past_after_days'];
		$last_update_time      = $DB_request->get_issue_last_update_time($request_id);
		$time_flag             = strtotime("$last_update_time + $issue_past_after_days day");

		$forbid_editing = 0;
		if ($open_or_close == 'closed' && strtotime(date('Y-m-d')) > $time_flag) {
			$forbid_editing = 1;
		}

		$notify_methods = $DB_request->get_notify_by($request_id, $user_id);

		$request_info                   = array_merge($request_info, $notify_methods);
		$request_info['forbid_editing'] = $forbid_editing;

		//echo print_r($request_info);
		echo json_encode($request_info);
	} else if ($_POST['action'] == 'get_report_ready') {
		$user_level = $_POST['user_level'];
		if ($user_level == 5) {
			$tenant_id  = $_POST['user_id'];
			$unit_id    = $_POST['user_unit_id'];  // to handle the case(one tenant has many units)
			$ready_info = $DB_request->get_tenant_unit($unit_id);
			$roomsInfo  = $DB_request->getRoomsByBuildingUnit(2);
			//note: $ready_info['unit_number'] is in it
			//note: $ready_info['apartment_id'] is in it
			$building_id                 = $DB_request->get_building_id($unit_id)['building_id'];
			$ready_info['building_id']   = $building_id;
			$ready_info['building_name'] = $DB_request->get_building_info($building_id)['building_name'];
			$ready_info['request_types'] = $DB_request->get_request_types_external();
			$ready_info['rooms_info']    = $roomsInfo;

			//request status
			$ready_info['request_status'] = $DB_request->get_request_status_external();

			echo json_encode($ready_info);
		} else {
			$employee_id                 = $_POST['user_id'];
			$ready_info['buildings']     = $DB_request->get_all_buildings($employee_id);
			$ready_info['request_types'] = $DB_request->get_request_types_all();

			//request status
			$request_status              = $DB_request->get_request_status_internal();
			$request_status_frequent     = array();
			$request_status_not_frequent = array();
			foreach ($request_status as $status) {
				if ($status['frequency_use'] == 1) {
					array_push($request_status_frequent, $status);
				} else {
					array_push($request_status_not_frequent, $status);
				}
			}

			$ready_info['request_status']   = $request_status_frequent;
			$ready_info['request_status_2'] = $request_status_not_frequent;

			echo json_encode($ready_info);
		}
	} else if ($_POST['action'] == 'get_floors') {
		$building_id = $_POST['building_id'];
		$floors      = $DB_request->get_floors($building_id);
		echo json_encode($floors);
	} else if ($_POST['action'] == 'get_apartments') {
		$floor_id   = $_POST['floor_id'];
		$apartments = $DB_request->get_apartments($floor_id);
		echo json_encode($apartments);
	} else if ($_POST['action'] == 'get_tenants') {
		$apartment_id = $_POST['apartment_id'];
		$tenant_ids   = $DB_request->get_tenants($apartment_id);
		$tenant_ids   = explode(",", $tenant_ids['tenant_ids']);
		$tenant_rows  = array();
		foreach ($tenant_ids as $tenant_id) {
			$tenant_row['tenant_id']   = $tenant_id;
			$tenant_row['tenant_name'] = $DB_request->get_tenant_name($tenant_id)['full_name'];
			array_push($tenant_rows, $tenant_row);
		}
		echo json_encode($tenant_rows);
	} else if ($_POST['action'] == 'add_request_upload_pictures') {
		$file_uploader = $_FILES['file'];

		$pictures = [];
		$num_pics = sizeof($file_uploader['name']);

		//transfer html 2-d array into pic-based 2-d array
		for ($index = 0; $index < $num_pics; $index++) {
			$temp             = array();
			$temp['name']     = $file_uploader['name'][$index];
			$temp['type']     = $file_uploader['type'][$index];
			$temp['tmp_name'] = $file_uploader['tmp_name'][$index];
			$temp['error']    = $file_uploader['error'][$index];
			array_push($pictures, $temp);
		}

		//deal with pictures
		$files_name_arr = array();
		foreach ($pictures as $one) {
			if ($one['error'] <= 0) {
				$picture_name = $one['name'];
				$picture_name = preg_replace('/\s+/', '', $picture_name);

				while (file_exists("../../files/requests/" . $picture_name)) {
					$picture_name = str_replace(".", "_a.", $picture_name);
				}
				$picture_tmp_name = $one['tmp_name'];
				move_uploaded_file($picture_tmp_name, "../../files/requests/" . $picture_name);
				array_push($files_name_arr, $picture_name);
			}
		}
		echo json_encode(array("result" => true, "values" => $files_name_arr));
	}
	/**
	 * report a new request
	 */
	else if ($_POST['action'] == 'add_request') {
		$reportRoomInfoSelect = 0;
		$projectId            = 0;
		$contractid           = 0;
		$vendor_id            = 0;

		if (isset($_POST["user_level"]) && $_POST["user_level"] == 5) {
			$_POST["newreportTasktype"]      = 0;
			$_POST["request_material"]       = "";
			$_POST["reportMaterialprovider"] = 0;
			if (!empty($_POST["reportRoomInfoSelect"])) {
				$reportRoomInfoSelect            = $_POST["reportRoomInfoSelect"];
			} else {
				$reportRoomInfoSelect            = 0;
			} /* Room in which the issue exists for the tenant */
		}

		//write request_info into DB
		$building_id = null;
		if (isset($_POST['reportBuildingId'])) {
			$building_id = $_POST['reportBuildingId'];
		}
		$apartment_id = isset($_POST['reportApartmentId']) ? $_POST['reportApartmentId'] : null;
		$floor_id     = isset($_POST['reportFloorId']) ? $_POST['reportFloorId'] : null;
		$location     = $_POST['reportArea'];
		if (isset($_POST['reportLocationDetails'])) {
			$common_area_details = $_POST['reportLocationDetails'];
		} else {
			$common_area_details = null;
		}
		$created_user_id = $_POST['reportUserId'];
		$request_type_id = $_POST['reportRequestType'];
		$status_id       = 1;

		$invoiceIdForRequest = "";
		if (isset($_POST["request_invoice_id"])) {
			$invoiceIdForRequest = $_POST["request_invoice_id"];
		}

		$invoiceAmtForRequest = 0;
		if (isset($_POST["request_invoice_amt"])) {
			$invoiceAmtForRequest = intval($_POST["request_invoice_amt"]);
		}

		// Vendor Detail
		$request_vendor         = $_POST['reportRequestType'];
		$vendor_id              = intval($_POST["vendor_id"]);
		$vendor_estimated_price = $_POST["vendor_estimated_price"]; // estimated price for the vendor
		$material_provided      = $_POST["material_provided"]; // Material provided for the vendor for the repair - this is a serialized form data - Parse and insert into the table

		// Project name if the task type is a project
		$projectName = "";

		// Check if the vendor ID is set - if yes update the number of projects in the vendor_infos
		//	if ($vendor_id) {
		//		echo $DB_vendor->updateProjectCount($vendor_id);
		//	}

		// Handyman detail
		$handyman_id = 0;

		$requestSetdateAndTime = null;
		$taskType              = $_POST["newreportTasktype"];

		/* if the task type is a project / contract request - get the project id and contract id */
		if (intval($taskType) == 2) {
			$projectId  = $_POST["projectSelectDetail"];
			$contractid = $_POST["contractSelectDetail"];

			if (!empty($contractid)) {
				$vendorDetail = $DB_request->getContractDataByContractId($contractid);
				$vendor_id    = $vendorDetail["vendor_id"];
			}
		}

		$taskAdditionalDetail = $_POST["task_detail_form"];

		// Parse the URL encoded values into array
		parse_str($taskAdditionalDetail, $taskAdditionalDetailArray); // Additional detail of the task from the additional detail tab in the create new request modal

		if (isset($taskAdditionalDetailArray["pictureImages"])) {
			$files_name_arr = $taskAdditionalDetailArray["pictureImages"];
		}

		$invoiceFiles = "";
		if (isset($taskAdditionalDetailArray["newRequest_invoicefiles_uploaded"])) {
			$invoiceFiles = $taskAdditionalDetailArray["newRequest_invoicefiles_uploaded"];
			if (count($invoiceFiles) > 0) {
				$invoiceFiles = json_encode($invoiceFiles);
			}
		}

		$requestSetdateAndTimeFrom = "";
		$requestSetTaskDateTimeTo  = "";

		// Request date and time - check if set
		if (isset($taskAdditionalDetailArray["isRequestSetTaskDateTime"]) && $taskAdditionalDetailArray["isRequestSetTaskDateTime"] == 1) {
			// date and time value for the request is set
			$requestSetdateAndTimeFrom = $taskAdditionalDetailArray["requestSetTaskDateTimeFrom"];
			$requestSetTaskDateTimeTo  = $taskAdditionalDetailArray["requestSetTaskDateTimeTo"];
		}

		if (!array_key_exists("user_level", $_POST)) {
			parse_str($material_provided, $materialProvidedArray); // Parsing the material provided data into a array
			$count_of_material            = count($materialProvidedArray["request_material"]);
			$request_material_provider_id = $materialProvidedArray["reportMaterialprovider"]; // the provider ID : possible values : 1 : vendor , 2: owner
			$materialDataMain             = array(); // this will be the json that will be stored ot the database

			// Get the material values properly as an array
			for ($count = 0; $count < $count_of_material; $count++) {
				$singleMaterial                             = array();
				$singleMaterial["material_name"]            = $materialProvidedArray["request_material"][$count];
				$singleMaterial["material_online_store_id"] = $materialProvidedArray["request_material_purchase_shop"][$count];
				$singleMaterial["material_url"]             = $materialProvidedArray["request_material_purchase_url"][$count];

				array_push($materialDataMain, $singleMaterial);
			}

			if ($request_material_provider_id == 1 || $request_material_provider_id == "1") {
				$materialDataForDB["detail"]      = json_encode($materialDataMain); // JSON value will be stored to the database
				$materialDataForDB["provider_id"] = $request_material_provider_id;
			} else {
				$materialDataForDB["detail"]      = "";
				$materialDataForDB["provider_id"] = $request_material_provider_id;
			}
		} else {
			$materialDataForDB = "";
		}
		// handyman code by tianen - not required as suggested by FRANK - using vendor hereafter
		$approveVisit = "";
		//    $approveVisit = $_POST['reportApprovedVisit'];
		//    if ($approveVisit == 0){
		//        $timeFromVisit = null;
		//        $timeToVisit = null;
		//    }else{
		//        $timeFromVisit = $_POST['reportVisitTime'];
		//        $timeDuration = $_POST['reportVisitDuration'];
		//        $timeToVisit = date('H:i:s', strtotime($_POST['reportVisitTime']." + $timeDuration  minutes"));
		//    }

		$message        = $taskAdditionalDetailArray['reportMessage'];
		$entry_datetime = date('Y-m-d H:i:s');

		//check the issue is tenant issue or not
		$request_category = 1;
		if (($_POST['reportUserId'] > 100000 && $_POST['reportUserId'] < 200000)) {
			$request_category = 2;
		}

		$reporttenantIds = array();
		if (isset($_POST['reportTenantIds'])) {
			$reporttenantIds = $_POST['reportTenantIds'];
		} else {
			if ($request_category == 2) {
				array_push($reporttenantIds, intval($_POST['reportUserId']));
			}
		}

		$request_id = $DB_request->add_request_info($building_id, $apartment_id, $floor_id, $location, $created_user_id, $request_type_id, $status_id, 0, "00:00:00", "00:00:00", $message, $entry_datetime, $common_area_details, $request_category, $taskType, $invoiceFiles, $handyman_id, $vendor_id, $projectName, $materialDataForDB, $vendor_estimated_price, $projectId, $contractid, $invoiceIdForRequest, $invoiceAmtForRequest, $reportRoomInfoSelect);


		/* Update the request count for the vendor in the vendor_infos table - this method will calculate all the requests in the request_infos table by the vendor_id and update the requests_no column in the vendor_infos */
		if (intval($taskType) == 2) {
			$DB_request->calculateProjectRequestCount($vendor_id);
		}

		$DB_request->updateEventDateTime($request_id, $requestSetdateAndTimeFrom, $requestSetTaskDateTimeTo);

		//add the request message as the first communication
		$DB_request->add_one_communication($request_id, $created_user_id, $message, $entry_datetime, 1);

		// Uncomment once picture upload refactored
		if (isset($files_name_arr) && !empty($files_name_arr)) {
			foreach ($files_name_arr as $one_file) {
				$DB_request->add_one_communication($request_id, $created_user_id, $one_file, $entry_datetime, 1, 1);
			}
		}

		//start to send notification, after all biz logic finish
		$notification_queue = array();

		//add the reporter assignee
		$entry_datetime = date('Y-m-d H:i:s', strtotime($entry_datetime . ' + 1 second'));
		$accessDateTime = date('Y-m-d H:i:s', strtotime("2000-01-01 00:00:00"));
		//		$DB_request->add_request_assignee($request_id, $created_user_id, $entry_datetime, $taskAdditionalDetailArray['notifyMeByEmail'], $taskAdditionalDetailArray['notifyMeBySms'], $taskAdditionalDetailArray['notifyMeByVoice']);
		$DB_request->add_request_assignee($request_id, $created_user_id, $accessDateTime, $taskAdditionalDetailArray['notifyMeByEmail'], $taskAdditionalDetailArray['notifyMeBySms'], $taskAdditionalDetailArray['notifyMeByVoice']);
		array_push($notification_queue, $created_user_id);

		// add the vendor assignee
		// Sharan's code
		$DB_request->add_request_assignee($request_id, $vendor_id, $entry_datetime, $taskAdditionalDetailArray['notifyMeByEmail'], $taskAdditionalDetailArray['notifyMeBySms'], $taskAdditionalDetailArray['notifyMeByVoice']);

		# register the responsible employees according to the building_id and the request_type_id + administrator
		$notify_default_by_email = 0;
		$notify_default_by_sms   = 0;
		$notify_default_by_voice = 0;
		$notify_methods          = '';

		$building_notification_settings = $DB_request->get_building_request_notification_settings($building_id);
		if (in_array($request_type_id, parse_issue_level($building_notification_settings['issue_normal']))) {
			$notify_methods = $building_notification_settings['issue_normal_ntf'];
		}
		if (in_array($request_type_id, parse_issue_level($building_notification_settings['issue_serious']))) {
			$notify_methods = $building_notification_settings['issue_serious_ntf'];
		}
		if (in_array($request_type_id, parse_issue_level($building_notification_settings['issue_urgent']))) {
			$notify_methods = $building_notification_settings['issue_urgent_ntf'];
		}


		$responseArray = array(
			"request_id" => $request_id,
			"request_type_id" => $request_type_id,
			"building_id" => $building_id,
			"notify_default_by_email" => $notify_default_by_email,
			"notify_default_by_sms" => $notify_default_by_sms,
			"notify_default_by_voice" => $notify_default_by_voice,
			"notify_methods" => $notify_methods,
			"notifcation_queue" => $notification_queue,
			"employee_id" => $created_user_id,
			"message" => $message,
			"entry_datetime" => $entry_datetime,
			"reportTenantIds" => $reporttenantIds,
			"vendor_id" => $vendor_id
		);

		echo json_encode($responseArray);

		// reserve the time slot - Not used as of today : 2018-06-07
		//    if($approveVisit == 1 && !empty($_POST['reportVisitDate']) && !empty($_POST['reportVisitDate'])){
		//        $reserved_slot_id = $_POST['reportVisitDate'];
		//        $handyman_booking_id = $DB_calendar->add_one_handyman_booking($reserved_slot_id,$created_user_id,$apartment_id,$timeFromVisit,$timeToVisit,1);
		//        //add communication to get confirm from manager
		//        //system_msg_type[0:default, 1:get confirm from property manager, 2: get confirm from handyman]
		//        $DB_request->add_system_communication_for_request($request_id,null, $handyman_booking_id, $entry_datetime,0,1,1);
		//        $DB_request->add_system_communication_for_request($request_id,null, $handyman_booking_id, $entry_datetime,0,1,2);
		//    }

	} // This is called after the add_request - send notification for the request to all the responsible employees and admin and tenants if exist


	else if ($_POST['action'] == 'saveNotificationSend') {
		include_once("../Class.SendSMS.php");
		include_once("../sendSMSEmail.php");

		$data 			= $_POST["data"];
		$requestId 	= $data["request_id"];
		$info			 	= $data["data"];
		$receivers  = array();

		$updatedByUserId = $data["user_id"];

		$requestInfo = $DB_request->get_request_info($requestId);
		$message = $requestInfo["message"];

		$apartmentInfo = $DB_request->get_apartment_info($requestId);
		$reporter_info   = $DB_request->get_user_info($requestInfo["employee_id"]);

		array_push($receivers, array("email" => $reporter_info["email"], "name" => $reporter_info["full_name"]));

		if (array_key_exists("tenant_ids", $apartmentInfo) && !empty($apartmentInfo["tenant_ids"])) {
			$tenants = explode(",", $apartmentInfo["tenant_ids"]);
			foreach ($tenants as $value) {
				$tenantInfo = $DB_request->get_user_info($value);
				array_push($receivers, array("email" => $tenantInfo["email"], "name" => $tenantInfo["full_name"]));
			}
		}

		// EMAIL TEMPLATE
		$title          = "Request Notification";
		$subtitle       = "Update for Request #$requestId in SPGManagement.";
		$body1          = "There was an update to the Request# $requestId at " . date("Y-m-d h:i") . ".";
		$body1          .= "<br>Please refer to the details below :";
		$body2          = "<p><strong> Reporter Name </strong>: " . $reporter_info['full_name'] . "</p>";
		$body2         .= "<p><strong> Section Updated </strong>: " . $info . "</p>";
		$body2 				 .= "<p> <strong>Message </strong> : " . $message . "</p>";

		if (!empty($updatedByUserId)) {
			$updatedByUserInfo = $DB_request->get_user_info($updatedByUserId);
			$body2          .= "<p><strong> Updated By </strong>: " . $updatedByUserInfo['full_name'] . "</p>";
		}

		$button_url     = "https://spgmanagement.com/admin/requestadd?action=rview&rid=" . $requestId;
		$button_content = "View Request Info";

		include_once "../../../pdo/Class.Template.php";
		$template       = new Template();

		foreach ($receivers as $receiver) {
			$email_template = $template->emailTemplate($title, $subtitle, $receiver["name"], $body1, $body2, $button_url, $button_content);
			$subject        = "Request Update Notification from spgmanagement.com";
			SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $receiver["email"], $receiver["name"], $subject, $email_template);
		}

		exit;
	} else if ($_POST['action'] == 'send_notifications') {
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
					$building_name   = $request_info['location_1'];
					$location        = $request_info['location_2'];
					$body2          = "<p><strong>Reporter Name </strong>: " . $reporter_info['full_name'] . "</p><p> <strong> Created Time : </strong>" . $entry_datetime . "</p><p> <strong>Building: </strong> " . $building_name . "</p> <p><strong>Location:</strong>" . $location . "</p> <p> <strong>Message </strong> : " . $message . "</p>";
					$button_url     = "https://spg.spgmanagement.com/approvereq.php?rid=" . $request_id . "&mker=" . base64_encode("SPGManagementrequesttenanturl");
					$button_content = "Mark the request as complete";

					include_once "../../../pdo/Class.Template.php";
					$template       = new Template();
					$email_template = $template->emailTemplate($title, $subtitle, $receiver, $body1, $body2, $button_url, $button_content);
					$subject        = "Notification from spgmanagement.com";
					MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email_address, $receiver, $subject, $email_template);
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
				$building_name   = $request_info['location_1'];
				$location        = $request_info['location_2'];
				$body1          = "There is a new request in spgmanagement.com. Please refer to the details below";
				$body2          = "<p>Reporter Name : " . $reporter_info['full_name'] . "</p><p>Created Time : " . $entry_datetime . "</p><p>Message : " . $message . "</p>";
				$body2          .= "<p> <strong>Building : </strong> " . $building_name . "</p> <p><strong>Location :</strong>" . $location . "</p>";
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
	} else if ($_POST['action'] == 'edit_request') {
		$closed_by_user_name = NULL;
		$list_request_status = array(3, 4, 14, 15, 16, 17, 18, 19, 20);
		if (in_array($_POST['request_status'], $list_request_status)) {
			$user_name_edit_request = $DB_request->get_user_info($_POST['user_id']);
			$closed_by_user_name    = $user_name_edit_request['full_name'];
		}

		/* If the status is set to the default option - change the status to PENDING */
		if ($_POST['request_status'] == 0) {
			$_POST['request_status'] = 1;
		}

		if ($_POST['user_level'] == 5) {
			$DB_request->edit_request_by_tenant($_POST['request_id'], $_POST['user_id'], $_POST['request_status'], $_POST['user_id']);
		} else {
			$DB_request->edit_request_by_employee($_POST['request_id'], $_POST['user_id'], $_POST['request_type'], $_POST['request_status'], $_POST['message'], $_POST['user_id']);

			/* All the fields required to edit the request */
			$editRequestFields       = array(
				'editRequestLocationReportArea', 'editRequestLocationBuilding', 'editRequestLocationCommonArea',
				'editRequestLocationFloor', 'editRequestLocationApt', 'reportEditDateTimeFrom', 'reportEditDateTimeTo', 'editRequestProjectName'
			);
			$locationAndScheduleData = array();

			/* iterate all the fields and check if the required field is set */
			foreach ($editRequestFields as $editRequestFieldsName) {
				if (isset($_POST[$editRequestFieldsName])) {
					$locationAndScheduleData[$editRequestFieldsName] = $_POST[$editRequestFieldsName];
				} else {
					$locationAndScheduleData[$editRequestFieldsName] = "";
				}
			}

			$DB_request->updateLocationAndSchedule($_POST['request_id'], $locationAndScheduleData);

			/* Adding the message again is not necesssary */
			//	$DB_request->add_one_communication($_POST['request_id'], $_POST['user_id'], $_POST['message'], date('Y-m-d H:i:s'), 1);
		}

		if (!isset($_POST['notifyMeByVoice'])) {
			$_POST['notifyMeByVoice'] = 0;
		}
		if (!isset($_POST['notifyMeBySms'])) {
			$_POST['notifyMeBySms'] = 0;
		}
		if (!isset($_POST['notifyMeByEmail'])) {
			$_POST['notifyMeByEmail'] = 0;
		}
		$DB_request->edit_notify($_POST['request_id'], $_POST['user_id'], date('Y-m-d H:i:s'), $_POST['notifyMeByEmail'], $_POST['notifyMeBySms'], $_POST['notifyMeByVoice']);
	} else if ($_POST['action'] == 'get_vendors') {
		//get the request id and the assigned recipients
		$speciality_type  = $_POST['vendorSpeciality'];
		$speciality_level = $_POST['vendorSpecialityLevel'];
		$vendor_type_id   = intval($_POST['vendor_type']);
		$vendorLicenses   = $_POST['vendorLicenses'];

		$dataToCheck = array("vendorSpeciality", "vendorSpecialityLevel", "vendor_type", "vendorLicenses");
		$dataToSave  = array();

		foreach ($dataToCheck as $variable) {
			if (!isset($_POST[$variable]) || empty($_POST[$variable]) || $_POST[$variable] == "default") {
				$dataToSave[$variable] = false;
			} else {
				$dataToSave[$variable] = $_POST[$variable];
			}
		}

		// Get Vendors List
		$requestTypeId = strval($speciality_type);
		$vendorsList   = $DB_vendor->getVendorsListFilter($dataToSave);

		if (count($vendorsList) < 1) {
			echo json_encode(array("value" => false));
		} else {
			echo json_encode(array("value" => $vendorsList));
		}

		/* Below code no more used as the filter is set in the MYSQL itself
			Don't delete the code for future references / usage
		 */
		//	// Filter vendors by request type id
		//	$vendorsListFiltered = array();
		//	foreach ($vendorsList as $vendor) {
		//		// Vendor Type
		//		if ($vendor_type_id != intval($vendor["vendor_type_id"])) {
		//			continue;
		//		}
		//
		//		// Vendor Speciality level
		//		if ($speciality_level != intval($vendor["speciality_level"])) {
		//			continue;
		//		}
		//
		//		if ($speciality_level != 7 || $speciality_type != true) {
		//			// Vendor Speciality
		//			$vendorRequestTypeIds = $vendor["request_type_id"];
		//			$explodeTypes         = explode(",", $vendorRequestTypeIds);
		//			if (in_array($speciality_type, $explodeTypes)) {
		//				array_push($vendorsListFiltered, $vendor);
		//			}
		//		}
		//		else {
		//			array_push($vendorsListFiltered, $vendor);
		//		}
		//	}
		//
		//	$vendorLicensesFiltered = array();
		//
		//	foreach ($vendorsListFiltered as $filteredVendor) {
		//		// Vendor Licenses
		//		$currentVendorLicenses = $filteredVendor["licenses_type_ids"];
		//		// Vendor Licenses as Array
		//		$currentVendorLicensesArr = explode(",", $currentVendorLicenses);
		//		if (in_array(intval($vendorLicenses), $currentVendorLicensesArr)) {
		//			array_push($vendorLicensesFiltered, $filteredVendor);
		//		}
		//	}
	}

	if ($_POST['action'] == 'get_units') {
		$building_id  = $_POST['building_id'];
		$result       = $DB_request->get_unit_lst_in_building($building_id);
		$data_content = array();
		foreach ($result as $row) {
			$temp                 = array();
			$temp['apartment_id'] = $row['apartment_id'];
			$temp['unit_number']  = $row['unit_number'];
			array_push($data_content, $row);
		}

		$feedback                 = array();
		$feedback['status']       = 'success';
		$feedback['data_content'] = $data_content;

		echo json_encode($feedback);
	}

	if ($_POST["action"] == "getMaterialProvided") {
		$request_id = $_POST['request_id'];
		echo json_encode($DB_request->getMaterialProvided($request_id));
	}

	if ($_POST["action"] == "get_attached_invoices") {
		$request_id  = $_POST['request_id'];
		$invoiceData = $DB_request->getInvoicesAttached($request_id);
		if ($invoiceData["invoices_attached"]) {
			echo $invoiceData["invoices_attached"];
		}
	}

	if ($_POST["action"] == "updateMaterialProvided") {
		$request_id        = $_POST['request_id'];
		$material_provided = $_POST["data"]; // Material provided for the vendor for the repair - this is a serialized form data - Parse and insert into the table

		parse_str($material_provided, $materialProvidedArray); // Parsing the material provided data into a array

		$count_of_material            = count($materialProvidedArray["editRequest_material"]);
		$request_material_provider_id = $materialProvidedArray["editRequestMaterialprovider"]; // the provider ID : possible values : 1 : vendor , 2: owner
		$materialDataMain             = array(); // this will be the json that will be stored ot the database

		// Get the material values properly as an array
		for ($count = 0; $count < $count_of_material; $count++) {
			$singleMaterial                             = array();
			$singleMaterial["material_name"]            = $materialProvidedArray["editRequest_material"][$count];
			$singleMaterial["material_online_store_id"] = $materialProvidedArray["editRequest_material_purchase_shop"][$count];
			$singleMaterial["material_url"]             = $materialProvidedArray["editRequest_material_purchase_url"][$count];

			array_push($materialDataMain, $singleMaterial);
		}

		$materialDataMain["detail"]      = json_encode($materialDataMain); // JSON value will be stored to the database
		$materialDataMain["provider_id"] = $request_material_provider_id;

		$updateMaterialprovided = $DB_request->setMaterialProvided($materialDataMain, $request_id);
		echo $updateMaterialprovided;
	}

	if ($_POST["action"] == "newrequestInvoiceFileUpload") {

		$data = array();

		if (isset($_FILES)) {
			$error = false;
			$files = array();

			$uploaddir = "../../files/requests/";
			foreach ($_FILES as $file) {
				$fileName = basename($file['name']);
				while (file_exists("../../files/requests/" . $fileName)) {
					$fileName = str_replace(".", "_a.", $fileName);
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

	if ($_POST["action"] == "editrequestInvoiceFileUpload") {

		$data       = array();
		$request_id = $_POST["request_id"];

		if (isset($_FILES)) {
			$error = false;
			$files = array();

			$uploaddir = "../../files/requests/";
			foreach ($_FILES as $file) {
				$fileName = basename($file['name']);
				while (file_exists("../../files/requests/" . $fileName)) {
					$fileName = str_replace(".", "_a.", $fileName);
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

		if (isset($files)) {
			if (count($files) > 0) {
				$existing_files     = $DB_request->getInvoicesAttached($request_id);
				$existing_filesData = $existing_files["invoices_attached"];
				if (strlen($existing_filesData) > 0 && !empty($existing_filesData)) {
					$existing_filesDataArray = json_decode($existing_filesData);
					$updateFiles             = array_merge($existing_filesDataArray, $files);
					$invoiceFiles            = json_encode($updateFiles);
					/* Update the invoice files for the request ID */
					if ($DB_request->updateInvoicesAttached($request_id, $invoiceFiles) > 0) {
						echo json_encode($updateFiles);
					}
				} else {
					$invoiceFiles = json_encode($files);
					/* Update the invoice files for the request ID */
					if ($DB_request->updateInvoicesAttached($request_id, $invoiceFiles) > 0) {
						echo json_encode($files);
					}
				}
			} else {
				echo false;
			}
		} else {
			echo false;
		}
	}

	/* Update the invoice amount and the invoice id for a request */
	if ($_POST["action"] == "updateInvoiceDetails") {
		$request_id = $_POST["request_id"];
		$invoice_id = $invoice_amount = 0;

		if (array_key_exists("invoice_id", $_POST) && array_key_exists("invoice_amount", $_POST)) {
			$invoice_id     = $_POST["invoice_id"];
			$invoice_amount = $_POST["invoice_amount"];
		}

		$invoiceDetails["invoice_id"]     = $invoice_id;
		$invoiceDetails["invoice_amount"] = $invoice_amount;

		if ($DB_request->updateInvoiceDetails($request_id, $invoiceDetails) > 0) {
			echo json_encode(array("value" => true));
		} else {
			echo json_encode(array("value" => false));
		}
	}

	/* Delete the attached invoice files for the given request */
	if ($_POST["action"] == "deleteInvoiceAttachedFile") {
		if (isset($_POST["request_id"]) && array_key_exists("fileIndex", $_POST)) {
			$invoiceFilesAttached    = $DB_request->getInvoicesAttached($_POST["request_id"]);
			$existing_files          = $invoiceFilesAttached["invoices_attached"];
			$existing_filesDataArray = json_decode($existing_files);
			$fileIndex               = intval($_POST["fileIndex"]);
			unset($existing_filesDataArray[$fileIndex]);
			$existing_filesDataArray = array_values($existing_filesDataArray);
			echo $DB_request->updateInvoicesAttached($_POST["request_id"], json_encode($existing_filesDataArray));
		} else {
			echo false;
		}
	}

	/* Controller action to create a new project */
	if ($_POST["action"] == "addNewProject") {
		$dataToCheck   = array("name", "location", "company_id");
		$dataToSave    = array();
		$missingValues = array();

		foreach ($dataToCheck as $variable) {
			if (!isset($_POST[$variable]) || empty($_POST[$variable])) {
				array_push($missingValues, $variable);
			} else {
				$dataToSave[$variable] = $_POST[$variable];
			}
		}

		if (count($missingValues) > 0) {
			echo false;
		} else {
			$dataToSave["address"] = $_POST["address"];
			if ($DB_request->createProject($dataToSave) > 0) {
				echo true;
			} else {
				echo false;
			}
		}
	}

	/* Controller action to create a new contract */
	if ($_POST["action"] == "addNewContract") {
		$dataToCheck   = array("projectId", "vendorId", "typeId", "contractDesc", "company_id"); // estimatedprice not included as it can be 0 and it will be considered as empty by the below script
		$dataToSave    = array();
		$missingValues = array();

		foreach ($dataToCheck as $variable) {
			if (!isset($_POST[$variable]) || empty($_POST[$variable])) {
				array_push($missingValues, $variable);
			} else {
				$dataToSave[$variable] = $_POST[$variable];
			}
		}

		if (count($missingValues) > 0) {
			echo false;
		} else {
			$dataToSave["estimatedPrice"] = $_POST["estimatedPrice"];
			$dataToSave["contractPrice"]  = $_POST["contractPrice"];

			if ($DB_request->createContract($dataToSave) > 0) {
				/* Calculate the project count for the vendor and update the count in the vendor infos table */
				calculateProjectCount($dataToSave["vendorId"], $DB_request);
				echo true;
			} else {
				echo false;
			}
		}
	}

	if ($_POST["action"] == "getContractsForProject") {
		if (empty($_POST["project_id"])) {
			echo false;
		} else {
			/* get the contracts for the project id given from contract infos table */
			echo json_encode($DB_request->getContractsByProjectId($_POST["project_id"]));
		}
	}

	if ($_POST["action"] == "getContractDetail") {
		if (empty($_POST["contractId"])) {
			echo false;
		} else {
			/* get the contracts for the project id given from contract infos table */
			echo json_encode($DB_request->getContractDataByContractId($_POST["contractId"]));
		}
	}

	if ($_POST["action"] == "getAllProjects") {
		/* get the contracts for the project id given from contract infos table */
		echo json_encode($DB_request->getProjectInfos());
	}

	if ($_POST["action"] == "deleteRequest") {
		if (isset($_POST["request_id"])) {
			echo json_encode($DB_request->deactivateRequest($_POST["request_id"]));
		} else {
			echo false;
		}
	}

	if ($_POST["action"] == "deleteProject") {
		if (isset($_POST["project_id"])) {
			echo json_encode($DB_request->deactivateProject($_POST["project_id"]));
		} else {
			echo false;
		}
	}

	if ($_POST["action"] == "deleteContract") {
		if (isset($_POST["contract_id"])) {
			echo json_encode($DB_request->deactivateContract($_POST["contract_id"]));
		} else {
			echo false;
		}
	}

	if ($_POST["action"] == "requestStatusUpdate") {
		// die(var_dump($_POST));
		if (isset($_POST["request_id"]) && isset($_POST["status_id"]) && isset($_POST["user_id"])) {
			$user_id = $_POST["user_id"];
			$userInfo = $DB_request->get_user_info($user_id);
			$userFullName = "N/A";
			if ($userInfo) {
				$userFullName = $userInfo['full_name'];
			}
			echo $DB_request->updateRequestStatus($_POST["request_id"], $_POST["status_id"], $user_id);
		} else {
			echo false;
		}
	}

	if ($_POST["action"] == "tenantSignatureUpload") {
		$data       = array();
		$request_id = $_POST["request_id"];

		if (isset($_FILES)) {
			$error = false;
			$files = array();

			$uploaddir = "../../files/requests/";
			foreach ($_FILES as $key => $file) {
				$fileName = "signature_" . $key . "_req_" . $request_id . ".jpeg";

				if (move_uploaded_file($file['tmp_name'], $uploaddir . $fileName)) {
					$files[$key] = $fileName;
				} else {
					$error = true;
				}
			}

			$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
		} else {
			$data = array('value' => 'No file data was sent ', result => false);
		}


		$DB_request->updateRequestStatus($request_id, 4, 0);
		echo json_encode($data);
		exit;
	}
}

if (isset($_GET) && !empty($_GET)) {

	if ($_GET["action"] == "getContractsDisplay") {
		if (isset($_GET["project_id"])) {
			if (empty($_GET["project_id"]) || $_GET["project_id"] == "all") {
				$contracts = $DB_request->getContractInfos();
			} else {
				/* get the contracts for the project id given from contract infos table */
				$contracts = $DB_request->getContractsByProjectId($_GET["project_id"]);
			}

			$contractData = array();
			if ($contracts) {
				foreach ($contracts as $contract) {
					if (!is_null($contract["is_active"]) && $contract["is_active"] == 0) {
						continue;
					}

					$contract["vendor_name"] = "-";
					if ($vendorName = $DB_vendor->getVendorName($contract["vendor_id"])) {
						$contract["vendor_name"] = $vendorName;
					}

					if (array_key_exists("vendor_id", $_GET)) {
						if ($_GET["vendor_id"] != "0") {
							if ($_GET["vendor_id"] != $contract["vendor_id"]) {
								continue;
							}
						}
					}

					if (!isset($contract["vendor_contract_price"])) {
						$contract["vendor_contract_price"] = "-";
					} else {
						$contract["vendor_contract_price"] = "$" . number_format($contract["vendor_contract_price"], 2);
					}

					array_push($contractData, $contract);
				}
			}
			echo json_encode(array("data" => $contractData));
		} else {
			echo json_encode(array("data" => ""));
		}
	}

	/* Get all the requests of a contract */
	if ($_GET["action"] == "getContractRequests") {
		$user_id                   = $_GET["user_id"];
		$contract_id               = $_GET["contract_id"];
		$issues                    = $DB_request->get_employee_issue_list($user_id);
		$contract_project_requests = array();

		foreach ($issues as $one) {
			$issue_status          = $one['issue_status'];
			$issue_past_after_days = $one['issue_past_after_days'];
			$last_update_time      = date('Y-m-d', strtotime($one['last_update_time']));
			$time_flag             = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list

			if ($contract_id != "all") {
				if ($one["task_type"] == 2 || $one["task_type"] == "2") {
					if ($one["contract_id"] != $contract_id) {
						continue;
					}

					$projectName         = $DB_request->getProjectInfo($one["project_id"])["name"];
					$one["project_name"] = $projectName;

					$vendorName        = $DB_vendor->getVendorName($one["vendor_id"]);
					$one["vendorName"] = $vendorName;

					$projectStartDate = "-";
					if (!empty($one["datetime_from"])) {
						$projectStartDate = $one["datetime_from"];
					}
					$one["projectStartDate"] = $projectStartDate;

					$request_detailed_status_id = $one['status_id'];
					$request_detailed_name      = $DB_request->get_request_status_name($request_detailed_status_id)['name'];
					$one["request_status"]      = $request_detailed_name;

					$message        = $one['message'];
					$one["message"] = $message;

					$request_type        = $one['request_type'];
					$one["request_type"] = $request_type;

					$request_type_id      = $one['request_type_id'];
					$building_id          = $one['building_id'];
					$request_level        = $DB_request->get_request_level($request_type_id, $building_id);
					$one["request_level"] = $request_level;

					//calculate time diff
					$created_time = strtotime($one['created_time']);
					$now          = strtotime(date('Y-m-d H:i:s'));
					$timediff     = timediff($created_time, $now);    // array
					$diff_day     = $timediff['day'];
					$diff_hour    = $timediff['hour'];
					$diff_min     = $timediff['min'];
					$diff_sec     = $timediff['sec'];

					$time_interval = '- ';
					if ($diff_day == 1) {
						$time_interval .= $diff_day . ' day';
					} elseif ($diff_day > 1) {
						$time_interval .= $diff_day . ' days';
					} elseif ($diff_hour == 1) {
						$time_interval .= $diff_hour . ' hour';
					} elseif ($diff_hour > 1) {
						$time_interval .= $diff_hour . ' hours';
					} elseif ($diff_min == 1) {
						$time_interval .= $diff_min . ' min';
					} elseif ($diff_min > 1) {
						$time_interval .= $diff_min . ' mins';
					} elseif ($diff_sec == 1) {
						$time_interval .= $diff_sec . ' sec';
					} elseif ($diff_sec > 1) {
						$time_interval .= $diff_sec . ' secs';
					}

					if (is_null($one['employee_id'])) {
						$created_user_info['full_name'] = "SYSTEM";
					} else {
						$created_user_info = $DB_request->get_user_info($one['employee_id']);
					}

					$created_time        = date('Y-m-d', $created_time);
					$one["created_time"] = $created_time;

					$create_user_full_name       = $created_user_info['full_name'];
					$one["created_userfullname"] = $create_user_full_name;

					$closed_by_user_name = " - ";
					if ($one['closed_by'] != NULL) {
						$closed_by_user_name = $one['closed_by'];
					}

					$one["closed_byusername"] = $closed_by_user_name;
					$one["created_ago"]       = $time_interval;

					$billHref = (!empty($one["vendor_id"])) ? "addBillByRequest.php?request_id=" . $one["id"] : "addEditBill.php?request_id=" . $one["id"];

					$one["billHtml"] = "<a class='billLinkHref' href='" . $billHref . "'>  <i class='fas fa-link'></i> </a>";

					array_push($contract_project_requests, $one);
				}
			}
		}

		echo json_encode(array("data" => $contract_project_requests));
	}

	if ($_GET["action"] == "getAllVendorsNames") {
		$vendors     = $DB_vendor->getVendorsList();
		$vendorsList = array();

		foreach ($vendors as $vendor) {
			$singleVendor       = array();
			$singleVendor["id"] = $vendor["vendor_id"];
			if (!isset($vendor["full_name"])) {
				$singleVendor["name"] = $vendor["company_name"];
			} else {
				$singleVendor["name"] = $vendor["full_name"];
			}

			if (is_null($singleVendor["name"])) {
				$singleVendor["name"] = "User";
			}

			array_push($vendorsList, $singleVendor);
		}

		echo json_encode($vendorsList);
		exit;
	}

	if ($_GET["action"] == "getAllVendorsEmail") {
		$vendors          = $DB_vendor->getVendorsList();
		$vendorsEmailList = array();

		foreach ($vendors as $vendor) {
			$singleVendor = array();
			if (!isset($vendor["email"]) || is_null($vendor["email"])) {
				continue;
			}
			$singleVendor["email"] = $vendor["email"];
			array_push($vendorsEmailList, $singleVendor);
		}

		echo json_encode($vendorsEmailList);
		exit;
	}
}

function parse_issue_level($issue_type_list_string)
{
	$output = array();
	if (!empty($issue_type_list_string)) {
		$output = explode(',', $issue_type_list_string);
	}
	return $output;
}

function formalize_telephone($original_tele)
{
	$formal_tele = trim($original_tele);
	$formal_tele = str_replace(' ', '', $formal_tele);
	$formal_tele = str_replace('-', '', $formal_tele);
	if (strlen($formal_tele) == 10) {
		$formal_tele = '1' . $formal_tele;
	}
	return $formal_tele;
}

function count_open_unread_issue($user_id, $unit_id)
{
	global $DB_request;
	$open_issue_counter = 0;
	$unread_issue_count = 0;


	if ($user_id > 100000 && $user_id < 200000) {      // tenant
		$issues = $DB_request->get_tenant_issue_list($user_id, $unit_id);
	} else {      //employee
		$issues = $DB_request->get_employee_issue_list($user_id);
	}

	foreach ($issues as $one) {
		if ($one['issue_status'] == 'open') {
			$open_issue_counter += 1;
		}
		if (strtotime($one['last_access_time']) < strtotime($one['last_update_time'])) {
			$unread_issue_count += 1;
		}
	}

	$result                       = array();
	$result['open_issue_count']   = $open_issue_counter;
	$result['unread_issue_count'] = $unread_issue_count;

	return $result;
}

/* Calculate the number of projects for the vendor */
function calculateProjectCount($vendorId, $DB_request)
{
	$DB_request->calculateProjectCount($vendorId);
}

updateAllVendorsProjectCount($DB_vendor, $DB_request);

function updateAllVendorsProjectCount($DB_vendor, $DB_request)
{
	$vendors = $DB_vendor->getVendorsList();
	foreach ($vendors as $vendor) {
		$vendorId = $vendor["vendor_id"];
		$DB_request->calculateProjectCount($vendorId);
		$DB_request->calculateProjectRequestCount($vendorId);
	}
}

function timediff($begin_time, $end_time)
{
	$timediff = $end_time - $begin_time;

	$days   = intval($timediff / 86400);
	$remain = $timediff % 86400;
	$hours  = intval($remain / 3600);
	$remain = $remain % 3600;
	$mins   = intval($remain / 60);
	$secs   = $remain % 60;
	$res    = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
	return $res;
}