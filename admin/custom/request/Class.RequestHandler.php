<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (strpos(getcwd(), "request") == false) {
	$path = "../pdo/";
} else {
	$path = "../../../pdo/";
}
include $path . "Class.Repo.php";


class RequestHandler
{

	// Repo class object
	public $vendor;

	// Variables for the PDO classes
	public $request;
	private $repo;

	public function makeRepoConn()
	{


		$this->repo = Repo::getRepoConn(); //die("lll2");
	}

	// Create Object for a PDO class
	public function makePdoObject($className, $varname)
	{
		$this->$varname = $this->repo->getPdoObject($className);
	}

	public function get_report_ready($user_level, $user_id, $user_unit_id)
	{
		if ($user_level == 5) {
			$tenant_id  = $user_id;
			$unit_id    = $user_unit_id;  // to handle the case(one tenant has many units)
			$ready_info = $this->request->get_tenant_unit($unit_id);
			//note: $ready_info['unit_number'] is in it
			//note: $ready_info['apartment_id'] is in it
			$building_id                 = $this->request->get_building_id($unit_id)['building_id'];
			$ready_info['building_id']   = $building_id;
			$ready_info['building_name'] = $this->request->get_building_info($building_id)['building_name'];
			$ready_info['request_types'] = $this->request->get_request_types_external();

			//request status
			$ready_info['request_status'] = $this->request->get_request_status_external();

			return $ready_info;
		} else {
			$employee_id                 = $user_id;
			$ready_info['buildings']     = $this->request->get_all_buildings($employee_id);
			$ready_info['request_types'] = $this->request->get_request_types_all();

			//request status
			$request_status              = $this->request->get_request_status_internal();
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

			return $ready_info;
		}
	}

	public function get_modal_info($request_id, $user_id, $unit_id)
	{
		// change the last time when user click this issue
		$current_time = date('Y-m-d H:i:s');
		$this->request->update_client_last_access_time($current_time, $request_id, $user_id);

		// get modal info
		$request_info                       = $this->request->get_one_request_info($request_id);
		$created_user_info                  = $this->request->get_user_info($request_info['employee_id']);
		$apart_info                         = $this->request->get_apartment_info($request_id);
		$feedback                           = array();
		$feedback['request_id']             = $request_id;
		$feedback['request_category']       = $request_info['request_category'];
		$feedback['open_or_close']          = $request_info['close_or_open'];
		$feedback['request_type']           = $request_info['request_type'];
		$feedback['invoice_id']             = $request_info['invoice_id'];
		$feedback['invoice_amount']         = $request_info['invoice_amount'];
		$feedback['created_user_name']      = $created_user_info['full_name'];
		$feedback['created_user_telephone'] = $created_user_info['mobile'];
		$feedback['vendor_id']              = $request_info["vendor_id"];
		$feedback['request_status']         = strtoupper($request_info['request_status']);
		$feedback['request_status_id']      = $request_info['status_id'];
		$created_time                       = $request_info['entry_datetime'];
		$feedback['created_date']           = date("Y/m/d", strtotime($created_time));
		$feedback['created_time']           = date("H:i:s", strtotime($created_time));

		$feedback['building_name']    = $apart_info['building_name'];
		$feedback['specific_area']    = $apart_info['specific_area'];
		$feedback['building_address'] = $apart_info['building_address'];

		//if seen by manager, status change from pending to read_by_manager
		if ($request_info['request_status'] == 'Pending' && $user_id < 100000) {
			$this->request->change_request_status($request_id, 5);
		}

		if (strlen($apart_info['building_picture']) > 0)
			$feedback['building_picture'] = 'files/requests/' . $apart_info['building_picture'];
		else
			$feedback['building_picture'] = 'files/requests/request_building_mask.jpeg';

		$statistics                     = $this->count_open_unread_issue($user_id, $unit_id);
		$feedback['open_issue_count']   = $statistics['open_issue_count'];
		$feedback['unread_issue_count'] = $statistics['unread_issue_count'];

		return $feedback;
	}

	public function count_open_unread_issue($user_id, $unit_id)
	{
		$open_issue_counter = 0;
		$unread_issue_count = 0;

		if ($user_id > 100000 && $user_id < 200000) {      // tenant
			$issues = $this->request->get_tenant_issue_list($user_id, $unit_id);
		} else {      //employee
			$issues = $this->request->get_employee_issue_list($user_id);
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

	public function parse_issue_level($issue_type_list_string)
	{
		$output = array();
		if (!empty($issue_type_list_string)) {
			$output = explode(',', $issue_type_list_string ?? '');
		}
		return $output;
	}

	public function formalize_telephone($original_tele)
	{
		$formal_tele = trim($original_tele);
		$formal_tele = str_replace(' ', '', $formal_tele);
		$formal_tele = str_replace('-', '', $formal_tele);
		if (strlen($formal_tele) == 10)
			$formal_tele = '1' . $formal_tele;
		return $formal_tele;
	}

	public function get_recipients($request_id)
	{

		$assigned_recipients = $this->request->get_assigned_recipients($request_id);

		$request_open_or_close = $this->request->check_request_open_or_closed($request_id);

		$feedback['open_or_close'] = $request_open_or_close;

		//get employees
		$employee_rows = $this->request->get_request_employees($request_id);
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
		if ($this->request->get_request_ori_info($request_id)['location'] == 'apartment') {
			$tenant_rows = $this->request->get_request_tenants($request_id);



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

		//return $assigned_recipients;
		// Get Vendors List
		$requestInfo   = $this->request->get_one_request_info($request_id);
		$requestTypeId = $this->request->getRequestType($request_id);
		$requestTypeId = strval($requestTypeId["request_type_id"]);
		$vendorsList   = $this->vendor->getVendorsList();

		$assignedUsersVendor = $this->request->get_assigned_recipients($request_id);
		$assignedUsersIds    = array();
		foreach ($assignedUsersVendor as $assignedUsers) {
			array_push($assignedUsersIds, $assignedUsers["user_id"]);
		}

		$feedback['vendors'] = array();
		// Filter vendors by request type id
		$vendorsListFiltered = array();
		foreach ($vendorsList as $vendor) {
			$vendorRequestTypeIds = $vendor["request_type_id"];
			$explodeTypes         = explode(",", $vendorRequestTypeIds ?? '');

			$vendor["assigned"] = 0;

			//            if(in_array($requestTypeId, $explodeTypes)){   // Do not use this - This will let show all the vendors in the system irrespective of the type of the speciality of the vendor selected while creating the task
			if (in_array($vendor["vendor_id"], $assignedUsersIds)) {
				$vendor["assigned"] = 1;
			}
			array_push($vendorsListFiltered, $vendor);
			//            }

		}

		$feedback['vendors']    = $vendorsListFiltered;
		$feedback['request_id'] = $request_id;
		return $feedback;
	}

	public function get_floors($building_id)
	{
		return $this->request->get_floors($building_id);
	}

	public function get_apartments($floor_id)
	{
		return $this->request->get_apartments($floor_id);
	}

	public function get_editing($request_id, $user_id)
	{
		$request_info = $this->request->get_request_ori_info($request_id);

		$open_or_close         = $request_info['close_or_open'];
		$issue_past_after_days = $request_info['issue_past_after_days'];
		$last_update_time      = $this->request->get_issue_last_update_time($request_id);
		$time_flag             = strtotime("$last_update_time + $issue_past_after_days day");

		$forbid_editing = 0;
		if ($open_or_close == 'closed' && strtotime(date('Y-m-d')) > $time_flag) {
			$forbid_editing = 1;
		}

		$notify_methods = $this->request->get_notify_by($request_id, $user_id);

		if (is_array($request_info)) {
			if (is_array($notify_methods)) {
				$request_info = array_merge($request_info, $notify_methods);
			}
			$request_info['forbid_editing'] = $forbid_editing;
			return $request_info;
		} else {
			return false;
		}
	}

	public function get_attached_invoices($request_id)
	{
		$invoiceData = $this->request->getInvoicesAttached($request_id);
		if ($invoiceData["invoices_attached"]) {
			return $invoiceData["invoices_attached"];
		}
	}

	/* Get the location infos from the location_infos table
	* Fetches the Common area, apartment choices to display in the select input
	*/
	public function getLocationInfos()
	{
		return $this->request->getLocationInfos();
	}

	function get_communications($request_id, $user_id)
	{
		$is_tenant           = false;
		$is_handyman         = false;
		$is_property_manager = false;

		if ($user_id > 100000 && $user_id < 200000)
			$is_tenant = true;

		if ($user_id < 100000) {
			$employee_info = $this->request->get_employee_info($user_id);
			if ($employee_info['user_level'] == 11) {
				$is_handyman = true;
			}
			if ($employee_info['admin_id'] == 1) {
				$is_property_manager = true;
			}
		}

		$communications     = $this->request->get_all_communications($request_id);
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
				$user_info    = $this->request->get_user_info($creator_id);
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

			// add one communication info to communications set
			array_push($arr_communications, $arr_one_communication);
		}

		return $arr_communications;
	}

	public function getVendorNames()
	{
		$vendors     = $this->vendor->getVendorsList();
		$vendorsList = array();

		foreach ($vendors as $vendor) {
			$singleVendor          = array();
			$singleVendor["id"]    = $vendor["vendor_id"];
			$singleVendor["email"] = $vendor["email"];
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
		return $vendorsList;
	}
}