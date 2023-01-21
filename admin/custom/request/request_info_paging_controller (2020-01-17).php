<?php
include_once('../../../pdo/dbconfig.php');

if ($_POST['action'] == 'get_current_issue_page') {
	$user_id     = $_POST['user_id'];
	$page_number = $_POST['page_number']; #based on 1
	$unit_id     = $_POST['user_unit_id'];

	// filter parameters
	$filter_building_id = $_POST['filter_building_id'];
	$filter_category    = $_POST['filter_category'];
	$filter_status      = $_POST['filter_status'];
	$filter_unit        = $_POST['filter_unit'];

	if (!isset($filter_unit)) {
		$filter_unit = "all";
	}

	$filter_from          = parse_uk_date($_POST['filter_from']);
	$filter_to            = parse_uk_date($_POST['filter_to']);
	$filter_employee_id   = $_POST['filter_employee_id'];
	$filter_order         = $_POST['filter_order'];
	$filter_tenant        = $_POST['filter_tenant'];
	$filter_read_category = $_POST['filter_read_category'];
	$request_type_detail  = $_POST['request_type_detail'];
	$vendor_id            = $_POST["vendor_id"];

	if ($filter_from == '')
		$filter_from = 'none';
	if ($filter_to == '')
		$filter_to = 'none';
	if ($filter_tenant == '')
		$filter_tenant = 'all';

	if ($user_id > 100000 && $user_id < 200000) {      // tenant
		$issues = $DB_request->get_tenant_issue_list($user_id, $unit_id);
	}
	else {      //employee
		$issues = $DB_request->get_employee_issue_list($user_id, $filter_building_id, $filter_category, $filter_status, $filter_unit, $filter_from, $filter_to, $filter_employee_id, $filter_order, $filter_tenant, $request_type_detail);
	}

	$raw_current_issues = array();

	foreach ($issues as $one) {
		$issue_status          = $one['issue_status'];
		$issue_past_after_days = $one['issue_past_after_days'];
		$last_update_time      = date('Y-m-d', strtotime($one['last_update_time']));
		$time_flag             = strtotime("$last_update_time + $issue_past_after_days days"); //timestamp for past issue list

		if ($one["is_active"] == 0) {
			continue;
		}

		// Skip if the Task type is a "Project"
		if ($one["task_type"] == 2) {
			continue;
		}

		if ($issue_status == 'open' || ($issue_status == 'closed' && strtotime(date('Y-m-d')) < $time_flag)) {

			//if unread request is selected($filter_read_category == 1), only return the unread requests
//			if($filter_read_category == 1 && (strtotime($one['last_access_time']) >= strtotime($one['last_update_time'] ))){
//				continue;
//			}

			if ($filter_read_category == 1 && in_array($one["status_id"], array(3, 4, 5, 6, 9, 10, 12, 15, 16, 17, 19))) {
				continue;
			}

			if (!empty($vendor_id) && !is_null($vendor_id) && $one["vendor_id"] != $vendor_id) {
				continue;
			}

			array_push($raw_current_issues, $one);
		}
	}

	$offset         = ($page_number - 1) * 20;
	$current_issues = array_slice($raw_current_issues, $offset, 20);

	$current_issues = $raw_current_issues;

	$feedback                = array();
	$feedback['data_count']  = sizeof($current_issues);
	$feedback['total_pages'] = ceil(sizeof($raw_current_issues) / 20);

	//data
	$data = array();
	date_default_timezone_set('America/New_York');
	$today            = date_create(date("M j, Y", time()));

	foreach ($current_issues as $row) {
		$vendor_id               = $row['vendor_id'];
		$request_id              = $row['id'];
		$request_type_id         = $row['request_type_id'];
		$request_type            = $row['request_type'];
		$building_id             = $row['building_id'];
		$request_status          = $row['issue_status'];
		$request_detailed_status = $DB_request->get_request_status_name($row['status_id'])['name'];
		$request_level           = $DB_request->get_request_level($request_type_id, $building_id);

		$created_time     = date("M j, Y", strtotime($row['created_time']));
		$created_datetime = date_create($created_time);
		$interval         = date_diff($created_datetime, $today)->format('%d');

		//calculate time diff
		$created_time = strtotime($row['created_time']);
		$now          = strtotime(date('Y-m-d H:i:s'));
		$timediff     = timediff($created_time, $now);    // array
		$diff_day     = $timediff['day'];
		$diff_hour    = $timediff['hour'];
		$diff_min     = $timediff['min'];
		$diff_sec     = $timediff['sec'];

		$time_interval = '- ';
		if ($diff_day == 1) {
			$time_interval .= $diff_day . ' day';
		}
									elseif ($diff_day > 1) {
			$time_interval .= $diff_day . ' days';
		}
									elseif ($diff_hour == 1) {
			$time_interval .= $diff_hour . ' hour';
		}
									elseif ($diff_hour > 1) {
			$time_interval .= $diff_hour . ' hours';
		}
									elseif ($diff_min == 1) {
			$time_interval .= $diff_min . ' min';
		}
									elseif ($diff_min > 1) {
			$time_interval .= $diff_min . ' mins';
		}
									elseif ($diff_sec == 1) {
			$time_interval .= $diff_sec . ' sec';
		}
									elseif ($diff_sec > 1) {
			$time_interval .= $diff_sec . ' secs';
		}

		$interval = $time_interval;

		$created_user_info = $DB_request->get_user_info($row['employee_id']);
		$apart_info        = $DB_request->get_apartment_info($request_id);
		$address           = $apart_info['specific_area'] . ' ' . $apart_info['building_name'];
		$message           = $row['message'];
		if (strlen($message) == 0)
			$message = ' - ';

		$closed_by_user_name = " - ";
		if ($row['closed_by'] != NULL)
			$closed_by_user_name = $row['closed_by'];

		$last_access_time = $row['last_access_time'];
		$last_update_time = $row['last_update_time'];
		$request_category = $row['request_category'];
		if (strtotime($last_access_time) < strtotime($last_update_time))
			$style_class = 'danger';
		else if ($request_category == 1) //internal
			$style_class = 'success';
		else //tenant
			$style_class = 'warning';

		$style_class = "";

		//add value belonging on row into record -- a map
		$one_record                    = array();
		$one_record['style_class']     = $style_class;
		$one_record['request_id']      = $request_id;
		$one_record['issue_status']    = strtoupper($request_status);
		$one_record['request_level']   = $request_level;
		$one_record['created_time']    = $created_time;
		$one_record['interval']        = $interval;
		$one_record['request_type']    = $request_type;
		$one_record['detailed_status'] = strtoupper($request_detailed_status);

		$one_record['creator_mobile']    = $created_user_info['mobile'];
		$one_record['creator_email']     = $created_user_info['email'];
		$one_record['creator_full_name'] = $created_user_info['full_name'];

		$one_record['address']   = $address;
		$one_record['message']   = $message;
		$one_record['closed_by'] = $closed_by_user_name;
		$one_record['vendor_id'] = $vendor_id;

		//add to feedback
		array_push($data, $one_record);
	}

	$feedback['data_content'] = $data;

	echo json_encode($feedback);
}

if ($_POST['action'] == 'get_past_issue_page') {
	$user_id     = $_POST['user_id'];
	$page_number = $_POST['page_number']; #based on 1
	$unit_id     = $_POST['user_unit_id'];
	// filter parameters
	$filter_building_id = $_POST['filter_building_id'];
	$filter_category    = $_POST['filter_category'];
	$filter_status      = $_POST['filter_status'];
	$filter_unit        = $_POST['filter_unit'];
	if (!isset($filter_unit)) {
		$filter_unit = "all";
	}
	$filter_from        = parse_uk_date($_POST['filter_from']);
	$filter_to          = parse_uk_date($_POST['filter_to']);
	$filter_employee_id = $_POST['filter_employee_id'];
	$filter_order       = $_POST['filter_order'];
	$filter_tenant      = $_POST['filter_tenant'];
	$vendor_id          = $_POST["vendor_id"];

	if ($filter_from == '')
		$filter_from = 'none';
	if ($filter_to == '')
		$filter_to = 'none';
	if ($filter_tenant == '')
		$filter_tenant = 'all';


	if ($user_id > 100000 && $user_id < 200000) {      // tenant
		$issues = $DB_request->get_tenant_issue_list($user_id, $unit_id);
	}
	else {      //employee
		$issues = $DB_request->get_employee_issue_list($user_id, $filter_building_id, $filter_category, $filter_status, $filter_unit, $filter_from, $filter_to, $filter_employee_id, $filter_order, $filter_tenant);
	}

	$raw_past_issues = array();

	foreach ($issues as $one) {
		if ($one["is_active"] == 0) {
			continue;
		}

		// Skip if the Task type is a "Project"
		if ($one["task_type"] == 2) {
			continue;
		}

		if (!empty($vendor_id) && !is_null($vendor_id) && $one["vendor_id"] != $vendor_id) {
			continue;
		}

		$issue_status          = $one['issue_status'];
		$issue_past_after_days = $one['issue_past_after_days'];
		$last_update_time      = date('Y-m-d', strtotime($one['last_update_time']));
		$time_flag             = strtotime("$last_update_time + $issue_past_after_days days"); //timestamp for past issue list

		if ($issue_status == 'closed' && strtotime(date('Y-m-d')) > $time_flag) {
			array_push($raw_past_issues, $one);
		}
	}

	$offset = ($page_number - 1) * 20;
//	$past_issue = array_slice($raw_past_issues, $offset, 20);
	$past_issue = $raw_past_issues;

	$feedback                = array();
	$feedback['data_count']  = sizeof($past_issue);
	$feedback['total_pages'] = ceil(sizeof($raw_past_issues) / 20);

	//data
	$data = array();
	foreach ($past_issue as $row) {
		$vendor_id       = $row['vendor_id'];
		$request_id      = $row['id'];
		$request_type_id = $row['request_type_id'];
		$request_type    = $row['request_type'];
		$building_id     = $row['building_id'];
		$request_status  = $row['issue_status'];
		$request_level   = $DB_request->get_request_level($request_type_id, $building_id);

		$created_time     = date("M j, Y", strtotime($row['created_time']));
		$created_datetime = date_create($created_time);
		$today            = date_create(date("M j, Y", time()));
		$interval         = date_diff($created_datetime, $today)->format('%d');

		//calculate time diff
		$created_time = strtotime($row['created_time']);
		$now          = strtotime(date('Y-m-d H:i:s'));
		$timediff     = timediff($created_time, $now);    // array
		$diff_day     = $timediff['day'];
		$diff_hour    = $timediff['hour'];
		$diff_min     = $timediff['min'];
		$diff_sec     = $timediff['sec'];

		$time_interval = '- ';
		if ($diff_day == 1) {
			$time_interval .= $diff_day . ' day';
		}
									elseif ($diff_day > 1) {
			$time_interval .= $diff_day . ' days';
		}
									elseif ($diff_hour == 1) {
			$time_interval .= $diff_hour . ' hour';
		}
									elseif ($diff_hour > 1) {
			$time_interval .= $diff_hour . ' hours';
		}
									elseif ($diff_min == 1) {
			$time_interval .= $diff_min . ' min';
		}
									elseif ($diff_min > 1) {
			$time_interval .= $diff_min . ' mins';
		}
									elseif ($diff_sec == 1) {
			$time_interval .= $diff_sec . ' sec';
		}
									elseif ($diff_sec > 1) {
			$time_interval .= $diff_sec . ' secs';
		}

		$interval = $time_interval;


		$created_user_info = $DB_request->get_user_info($row['employee_id']);
		$apart_info        = $DB_request->get_apartment_info($request_id);
		$address           = $apart_info['specific_area'] . ' - ' . $apart_info['building_name'];
		$message           = $row['message'];
		if (strlen($message) == 0)
			$message = ' - ';

		$last_access_time = $row['last_access_time'];
		$last_update_time = $row['last_update_time'];
		$request_category = $row['request_category'];

		if ($request_category == 1) //internal
			$style_class = 'success';
		else //tenant
			$style_class = 'warning';


		$one_record                     = array();
		$one_record['style_class']      = $style_class;
		$one_record['request_id']       = $request_id;
		$one_record['last_update_time'] = date('Y-m-d', strtotime($last_access_time));
		$one_record['request_level']    = $request_level;
		$one_record['created_time']     = $created_time;
		$one_record['request_type']     = $request_type;

		$one_record['creator_mobile']    = $created_user_info['mobile'];
		$one_record['creator_email']     = $created_user_info['email'];
		$one_record['creator_full_name'] = $created_user_info['full_name'];

		$one_record['address']   = $address;
		$one_record['message']   = $message;
		$one_record['vendor_id'] = $vendor_id;

		//add to feedback
		array_push($data, $one_record);
	}

	$feedback['data_content'] = $data;

	echo json_encode($feedback);
}

if ($_POST['action'] == 'get_all_issue_page') {
	$user_id     = $_POST['user_id'];
	$page_number = $_POST['page_number']; #based on 1
	$unit_id     = $_POST['user_unit_id'];

	// filter parameters
	$filter_building_id = $_POST['filter_building_id'];
	$filter_category    = $_POST['filter_category'];
	$filter_status      = $_POST['filter_status'];
	$filter_unit        = $_POST['filter_unit'];

	if (!isset($filter_unit)) {
		$filter_unit = "all";
	}

	$filter_from          = parse_uk_date($_POST['filter_from']);
	$filter_to            = parse_uk_date($_POST['filter_to']);
	$filter_employee_id   = $_POST['filter_employee_id'];
	$filter_order         = $_POST['filter_order'];
	$filter_tenant        = $_POST['filter_tenant'];
	$filter_read_category = $_POST['filter_read_category'];
	$request_type_detail  = $_POST['request_type_detail'];
	$vendor_id            = $_POST["vendor_id"];

	if ($filter_from == '')
		$filter_from = 'none';
	if ($filter_to == '')
		$filter_to = 'none';
	if ($filter_tenant == '')
		$filter_tenant = 'all';

	if ($user_id > 100000 && $user_id < 200000) {      // tenant
		$issues = $DB_request->get_tenant_issue_list($user_id, $unit_id);
	}
	else {      //employee
		$issues = $DB_request->get_employee_issue_list($user_id, $filter_building_id, $filter_category, $filter_status, $filter_unit, $filter_from, $filter_to, $filter_employee_id, $filter_order, $filter_tenant, $request_type_detail);
	}

	$raw_current_issues = array();

	foreach ($issues as $one) {
		$issue_status          = $one['issue_status'];
		$issue_past_after_days = $one['issue_past_after_days'];
		$last_update_time      = date('Y-m-d', strtotime($one['last_update_time']));
		$time_flag             = strtotime("$last_update_time + $issue_past_after_days days"); //timestamp for past issue list

		if ($one["is_active"] == 0) {
			continue;
		}

		// Skip if the Task type is a "Project"
		if ($one["task_type"] == 2) {
			continue;
		}

		if (!empty($vendor_id) && !is_null($vendor_id) && $one["vendor_id"] != $vendor_id) {
			continue;
		}

		array_push($raw_current_issues, $one);

	}

	$offset         = ($page_number - 1) * 20;
	$current_issues = array_slice($raw_current_issues, $offset, 20);

	$current_issues = $raw_current_issues;

	$feedback                = array();
	$feedback['data_count']  = sizeof($current_issues);
	$feedback['total_pages'] = ceil(sizeof($raw_current_issues) / 20);

	//data
	$data = array();
	foreach ($current_issues as $row) {
		$vendor_id               = $row['vendor_id'];
		$request_id              = $row['id'];
		$request_type_id         = $row['request_type_id'];
		$request_type            = $row['request_type'];
		$building_id             = $row['building_id'];
		$request_status          = $row['issue_status'];
		$request_detailed_status = $DB_request->get_request_status_name($row['status_id'])['name'];
		$request_level           = $DB_request->get_request_level($request_type_id, $building_id);

		date_default_timezone_set('America/New_York');
		$created_time     = date("M j, Y", strtotime($row['created_time']));
		$created_datetime = date_create($created_time);
		$today            = date_create(date("M j, Y", time()));
		$interval         = date_diff($created_datetime, $today)->format('%d');

		$created_user_info = $DB_request->get_user_info($row['employee_id']);
		$apart_info        = $DB_request->get_apartment_info($request_id);
		$address           = $apart_info['specific_area'] . ' ' . $apart_info['building_name'];
		$message           = $row['message'];
		if (strlen($message) == 0)
			$message = ' - ';

		$closed_by_user_name = " - ";
		if ($row['closed_by'] != NULL)
			$closed_by_user_name = $row['closed_by'];

		$last_access_time = $row['last_access_time'];
		$last_update_time = $row['last_update_time'];
		$request_category = $row['request_category'];
		if (strtotime($last_access_time) < strtotime($last_update_time))
			$style_class = 'danger';
		else if ($request_category == 1) //internal
			$style_class = 'success';
		else //tenant
			$style_class = 'warning';

		$style_class = "";

		//add value belonging on row into record -- a map
		$one_record                    = array();
		$one_record['style_class']     = $style_class;
		$one_record['request_id']      = $request_id;
		$one_record['issue_status']    = strtoupper($request_status);
		$one_record['request_level']   = $request_level;
		$one_record['created_time']    = $created_time;
		$one_record['interval']        = $interval;
		$one_record['request_type']    = $request_type;
		$one_record['detailed_status'] = strtoupper($request_detailed_status);

		$one_record['creator_mobile']    = $created_user_info['mobile'];
		$one_record['creator_email']     = $created_user_info['email'];
		$one_record['creator_full_name'] = $created_user_info['full_name'];

		$one_record['address']   = $address;
		$one_record['message']   = $message;
		$one_record['closed_by'] = $closed_by_user_name;
		$one_record['vendor_id'] = $vendor_id;

		//add to feedback
		array_push($data, $one_record);
	}

	$feedback['data_content'] = $data;

	echo json_encode($feedback);
}

function parse_uk_date($uk_date) {
	if (empty($uk_date)) {
		return '';
	}
	$date_arr = explode('/', $uk_date);
	return $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
}

function timediff($begin_time, $end_time) {
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
