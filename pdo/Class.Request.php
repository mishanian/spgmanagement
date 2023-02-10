<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'Class.Crud.php';

class Request
{
	private $crud;
	private $DB_con;
	public function __construct($DB_con)
	{
		$this->crud = new Crud($DB_con);
		// var_dump($DB_con);
		$this->DB_con = $DB_con;
	}

	// -------------------  basic info -----------------------


	public function get_tenant_requests($tenant_id, $apartment_id)
	{
		try {
			$sql = "SELECT *, RS.name as request_status, RT.name as request_type, RI.id as request_id FROM request_infos RI
			left join request_status RS ON RI.status_id=RS.id
			left join request_types RT ON RI.request_type_id=RT.id
			WHERE employee_id=:tenant_id AND apartment_id=:apartment_id order by RI.id desc";
			$this->crud->query($sql);
			$this->crud->bind(":tenant_id", $tenant_id);
			$this->crud->bind(":apartment_id", $apartment_id);
			$result = $this->crud->resultset();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * get the detailed related info about the request
	 * info list :
	 *      1. building info related to the request
	 *      2. unit_number, tenant_ids (if request.location is apartment)
	 *      3. common_area_detail (if request.location is common_area)
	 * Note: how to get lease related to the apartment -> get the lease (lease_status_id = 1,7,8,9) of the apartment
	 */
	public function get_apartment_info($request_id)
	{
		try {
			$this->crud->query("SELECT building_infos.building_name as building_name, building_infos.address as building_address, building_infos.feature_picture as building_picture,
                                         if(request_infos.location='2',(SELECT apartment_infos.unit_number FROM apartment_infos WHERE apartment_infos.apartment_id=request_infos.apartment_id),
                                         if(request_infos.location='1',(SELECT request_infos.common_area_detail),NULL )) AS specific_area,
                                         if(request_infos.location='2', (SELECT GROUP_CONCAT(tenant_ids) as tenant_ids FROM lease_infos WHERE apartment_id = request_infos.apartment_id AND lease_status_id IN (1,7,8,9)),null) AS tenant_ids
                                         FROM building_infos,request_infos WHERE building_infos.building_id=request_infos.building_id
                                         AND request_infos.id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get full_name, mobile, email for the user
	 * the function will get the info from different table, based on the range of user_id
	 */
	public function get_user_info($user_id)
	{
		if ($user_id > 100000 and $user_id < 200000)
			$query = "SELECT full_name, mobile, email, username, userpass FROM tenant_infos WHERE tenant_id =:user_id";
		else if ($user_id > 200000 and $user_id < 300000)
			$query = "SELECT full_name,mobile,email, username, userpass FROM owner_infos WHERE owner_id = :user_id";
		else
			$query = "SELECT full_name, email,mobile, username, userpass FROM employee_infos WHERE employee_id =:user_id";

		if ($user_id > 300000) {
			$query = "SELECT full_name, company_name, email, mobile, username, userpass FROM vendor_infos WHERE vendor_id =:user_id";
		}

		try {
			$this->crud->query($query);
			$this->crud->bind("user_id", $user_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_employee_info($employee_id)
	{
		try {
			$this->crud->query("SELECT * FROM employee_infos WHERE employee_id = :employee_id");
			$this->crud->bind(":employee_id", $employee_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	// -------------------- request info -----------------------

	/**
	 * get the detail info of the request
	 * one projection here : close_or_open (if request_infos.status_id is (3,4,14,15,16,17,18,19,20,21,22) closed, or else open)
	 */
	public function get_one_request_info($request_id)
	{
		try {
			$this->crud->query("SELECT apartment_id,location,employee_id,vendor_id,request_infos.request_category, request_infos.status_id, request_types.name AS request_type,request_status.name AS request_status,(if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22),'closed','open')) AS close_or_open, approveVisit,timeFromVisit,timeToVisit,message,entry_datetime,invoice_id,invoice_amount FROM request_infos, request_status, request_types WHERE request_status.id = request_infos.status_id AND request_types.id =  request_infos.request_type_id AND request_infos.id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Get the detail info of the request
	 * @param $request_id
	 * @return mixed
	 */
	public function get_request_info($request_id)
	{
		try {
			$this->crud->query("SELECT * From request_infos WHERE id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * to check the request's status is closed or open
	 * request_infos.status_id IN (3,4,14,15,16,17,18,19,20,21,22) -> closed , or else open
	 */
	public function check_request_open_or_closed($request_id)
	{
		try {
			$this->crud->query("SELECT (if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22),'closed','open')) AS close_or_open FROM request_infos WHERE request_infos.id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $result = $this->crud->resultSingle()['close_or_open'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the level of the request
	 * It should reference from building_infos (building settings), because building has diff settings about request level
	 */
	public function get_request_level($request_type_id, $building_id)
	{
		// echo "$request_type_id, $building_id<br>";
		try {
			$this->crud->query("SELECT issue_normal,issue_serious, issue_urgent FROM building_infos WHERE building_id =:building_id");
			$this->crud->bind(":building_id", $building_id);
			$result = $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		$normal  = explode(',', $result['issue_normal'] ?? '');
		$serious = explode(',', $result['issue_serious'] ?? '');
		$urgent  = explode(',', $result['issue_urgent'] ?? '');

		if (in_array($request_type_id, $normal))
			return "NORMAL";
		else if (in_array($request_type_id, $serious))
			return "SERIOUS";
		else if (in_array($request_type_id, $urgent))
			return "URGENT";
		else
			return "NULL";
	}

	/**
	 * get all request list for the employee(including current requests and past requests)
	 * the function has filter function (used in filter) -> only return the request list that are satisfied with the condition
	 * by default : (only pass employee_id) -> the function will return all request list
	 * last_update_time : last entry in communication related to this request
	 * last_access_time : last_access_time in request_assignees table
	 * issue_past_after_days : from building_info table (building settings)
	 */
	public function get_employee_issue_list($user_id, $f_building = 'all', $f_category = 'all', $f_status = 'open', $f_unit = 'all', $f_from = 'none', $f_to = 'none', $f_employee = 'all', $f_order = 'recent_first', $f_tenant = 'all', $request_type_detail = 'all')
	{
		$additional_criteria = '';

		$isAdminUser = false;
		if (!empty($_SESSION['admin_id'])) {
			$isAdminUser    = true;
		}
		// die("is=".$isAdminUser);
		$companyId = true;
		if (!empty($_SESSION['company_id'])) {
			$companyId = $_SESSION['company_id'];
		}

		if ($f_building != 'all') {
			$additional_criteria .= ' AND request_infos.building_id = ' . $f_building;
		}
		if ($f_category != 'all') {
			$additional_criteria .= ' AND request_infos.request_category = ' . $f_category;
		}
		if ($f_status != 'all') {

			if ($f_status == 'closed') {
				$additional_criteria .= " AND (request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22)) ";
			} else {
				$additional_criteria .= " AND (request_infos.status_id NOT IN (3,4,14,15,16,17,18,19,20,22)) ";
			}
		}
		if ($f_unit != 'all') {
			$additional_criteria .= " AND request_infos.apartment_id = " . $f_unit;
		}
		if ($f_from != 'none') {
			$additional_criteria .= " AND request_infos.entry_datetime > '" . $f_from . " 00:00:00' ";
		}
		if ($f_to != 'none') {
			$additional_criteria .= " AND request_infos.entry_datetime < '" . $f_to . " 00:00:00' ";
		}
		if ($f_employee != 'all') {
			$additional_criteria .= " AND request_infos.employee_id = " . $f_employee;
		} else {
			$additional_criteria .= " AND request_infos.employee_id in (select user_id from userlist where company_id=" . $_SESSION['company_id'] . ") ";
		}

		if ($isAdminUser) {
			//	$additional_criteria .= " AND request_infos.employee_id IN (SELECT employee_id FROM employee_infos where company_id = $companyId)";
		}

		if ($f_tenant != 'all') {
			$additional_criteria .= " AND request_infos.id IN (SELECT request_assignees.request_id FROM request_assignees WHERE request_assignees.user_id IN (SELECT tenant_infos.tenant_id FROM tenant_infos WHERE tenant_infos.full_name LIKE '%" . $f_tenant . "%')) ";
		}

		if ($request_type_detail) {
			if ($request_type_detail != 'all') {
				$additional_criteria .= " AND request_infos.request_type_id = " . $request_type_detail;
			}
		}

		$additional_criteria .= " AND request_infos.building_id IS NOT NULL ";

		$order_by = 'request_infos.entry_datetime  DESC';

		if ($f_order == 'unread_first')
			//			$order_by = '(last_update_time-last_access_time) DESC';
			$order_by = ' FIELD(status_id, 1) DESC';

		$additional_criteria .= ' ORDER BY ' . $order_by;


		try {
			$userIdCriteriaAdminLastAccess = ($isAdminUser) ? "" : "AND user_id = :user_id_1";
			$maxLastAccess = "SELECT max(last_access_time) FROM request_assignees WHERE request_id = request_infos.id $userIdCriteriaAdminLastAccess";

			$userIdAdminrequestAssigneesCriteria = ($isAdminUser) ? "" : "WHERE user_id = :user_id_2";
			$requestAssigneesCriteria = "SELECT request_id  FROM request_assignees $userIdAdminrequestAssigneesCriteria";
			//	die("additional_criteria=".$additional_criteria);
			$queryString
				= "SELECT request_infos.id,task_type,vendor_id,request_infos.is_active,project_name,project_id,contract_id,datetime_from,building_id,apartment_id,location,employee_id,request_type_id,request_category,
					request_types.name AS request_type, status_id, approveVisit, timeFromVisit, timeToVisit, message,closed_by, entry_datetime AS created_time,
					(if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22),'closed','open')) AS issue_status,(SELECT issue_past_after_days FROM building_infos
					WHERE building_infos.building_id = request_infos.building_id) AS issue_past_after_days,
                  ($maxLastAccess) AS last_access_time,
                  (ifnull((SELECT entry_date FROM request_communications WHERE request_id = request_infos.id ORDER BY entry_date DESC limit 1),entry_datetime )) AS last_update_time
                  FROM request_infos,request_types  WHERE request_infos.id IN ($requestAssigneesCriteria)
				  AND request_types.id = request_infos.request_type_id" . $additional_criteria . " ";

			// die($queryString);

			$this->crud->query($queryString);
			// die($queryString);
			if (!$isAdminUser) {
				$this->crud->bind(":user_id_1", $user_id);
				$this->crud->bind(":user_id_2", $user_id);
			}

			$results = $this->crud->resultSet();
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all request list for the tenant(including current requests and past requests)
	 * last_update_time : last entry in communication related to this request
	 *** Note : if some communications are hidden by tenant -> these communication will not be taken to calculate the last_update_time
	 * last_access_time : last_access_time in request_assignees table
	 * issue_past_after_days : from building_info table (building settings)
	 */
	public function get_tenant_issue_list($user_id, $unit_id)
	{
		//For influence the last_update_time if user is tenant (only consider public communications)
		//if_seen_by_tenant = 1
		try {
			$this->crud->query("SELECT request_infos.id,building_id,apartment_id,request_infos.is_active, request_infos.status_id, location,employee_id,request_type_id,task_type,request_category, request_types.name AS request_type,status_id,approveVisit, timeFromVisit, timeToVisit,message, closed_by, entry_datetime AS created_time,
                                        (if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22),'closed','open')) AS issue_status,(SELECT issue_past_after_days FROM building_infos WHERE building_infos.building_id = request_infos.building_id) AS issue_past_after_days,
                                        (SELECT MAX(last_access_time) FROM request_assignees WHERE request_id = request_infos.id AND user_id = :user_id_1) AS last_access_time,
                                        (ifnull((SELECT MAX(entry_date) FROM request_communications WHERE request_id = request_infos.id AND if_seen_by_tenant = 1 ORDER BY entry_date DESC limit 1),entry_datetime )) AS last_update_time
                                        FROM request_infos,request_types  WHERE request_infos.id IN ( SELECT request_id FROM request_assignees WHERE user_id = :user_id_2)
                                        AND request_types.id = request_infos.request_type_id AND apartment_id = :unit_id ORDER BY request_infos.entry_datetime DESC");
			$this->crud->bind(":user_id_1", $user_id);
			$this->crud->bind(":user_id_2", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			$results = $this->crud->resultSet();
			//		die();
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the last update time for a request
	 * the function will not consider whether is hidden to tenant (in global level)
	 */
	public function get_issue_last_update_time($request_id)
	{
		try {
			$this->crud->query("SELECT entry_date FROM request_communications WHERE request_id = :request_id ORDER BY entry_date DESC limit 1");
			$this->crud->bind(":request_id", $request_id);
			$results = $this->crud->resultSingle()['entry_date'];
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//----------------------- recipient -----------------------

	/**
	 * get all the employees belonging to the company in which the request issues
	 * the function is used to get the recipient candidate list
	 */
	public function get_request_employees($request_id)
	{
		try {
			$this->crud->query("SELECT employee_id, full_name FROM employee_infos WHERE company_id=(SELECT company_id FROM employee_infos WHERE employee_id=(SELECT employee_id FROM building_infos WHERE building_id=(SELECT building_id FROM request_infos WHERE id=:request_id)))");
			$this->crud->bind(":request_id", $request_id);
			$results = $this->crud->resultSet();
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all tenant_id, full_name belonging to the apartment related to the request
	 * return a array
	 */
	public function get_request_tenants($request_id)
	{
		try {
			$this->crud->query("SELECT tenant_ids FROM lease_infos WHERE apartment_id = (SELECT apartment_id FROM request_infos WHERE id=:request_id) AND lease_status_id IN (1,7,8,9)");
			$this->crud->bind(":request_id", $request_id);
			$tenant_ids  = $this->crud->resultSingle();
			$tenant_ids  = explode(',', $tenant_ids['tenant_ids']);
			$tenant_rows = array();
			foreach ($tenant_ids as $tenant_id) {
				$this->crud->query("SELECT tenant_id, full_name FROM tenant_infos WHERE tenant_id=:tenant_id");
				$this->crud->bind(":tenant_id", $tenant_id);
				$tenant_row = $this->crud->resultSingle();
				array_push($tenant_rows, $tenant_row);
			}
			return $tenant_rows;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the assigned recipient for a request
	 * return user_id, and his notification method related to this request
	 * Hint: assigned recipient : the user who can access this request
	 */
	public function get_assigned_recipients($request_id)
	{
		try {
			$this->crud->query("SELECT user_id,notify_by_email,notify_by_sms,notify_by_voice FROM request_assignees WHERE request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_assigned_recipients_user_id($request_id)
	{
		try {
			$this->crud->query("SELECT GROUP_CONCAT(user_id) AS user_id FROM request_assignees WHERE request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add a user to the recipient list of the request
	 */
	public function add_recipient($request_id, $user_id)
	{
		try {
			$this->crud->query("INSERT INTO request_assignees(request_id, user_id) VALUES (:request_id, :user_id)");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * delete the user from the recipient list of the request
	 */
	public function delete_recipient($request_id, $user_id)
	{
		try {
			$this->crud->query("DELETE FROM request_assignees WHERE request_id=:request_id AND user_id=:user_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * delete the users from recipients
	 */
	public function delete_recipients($request_id)
	{
		try {
			$this->crud->query("DELETE FROM request_assignees WHERE request_id=:request_id AND (user_id > 200000 OR user_id < 100000)");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the detailed info about assigned recipient of the request (include user_name, user_role, notification method, last_access_time)
	 * Hint: assigned recipient : the user who can access this request
	 */
	public function get_assignee_detail_infos($request_id)
	{
		try {
			$this->crud->query("SELECT userlist.user_id AS user_id, userlist.full_name AS user_name,if(userlist.user_id < 100000,'Employee',if(userlist.user_id < 200000,'Tenant','Owner')) AS user_role,notify_by_sms,notify_by_email,notify_by_voice,last_access_time FROM request_assignees,userlist WHERE userlist.user_id = request_assignees.user_id AND request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the employees who take the responsible for the type of request in the building
	 * the function will check employee_infos table (employee settings)
	 */
	public function get_default_employees($building_id, $request_type_id)
	{
		try {
			$this->crud->query("SELECT employee_id,full_name FROM employee_infos WHERE find_in_set(:building_id,building_ids) AND find_in_set(:request_type_id,df_responsible_request_types)");
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":request_type_id", $request_type_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get settings related to request for the building
	 */
	public function get_building_request_notification_settings($building_id)
	{
		try {
			$this->crud->query("SELECT issue_normal, issue_normal_ntf, issue_serious,issue_serious_ntf,issue_urgent,issue_urgent_ntf FROM building_infos where building_id = :building_id");
			$this->crud->bind(":building_id", $building_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get default notification method for a request
	 * Approach : 1. get building_id and request_type_id of the request
	 *            2. call the method 'get_default_ntf_methods'
	 */
	public function get_default_ntf_methods_request_id($request_id)
	{
		try {
			$this->crud->query("SELECT building_id,request_type_id FROM request_infos WHERE id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			$row             = $this->crud->resultSingle();
			$building_id     = $row['building_id'];
			$request_type_id = $row['request_type_id'];
			return $this->get_default_ntf_methods($building_id, $request_type_id);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the default notification method settings for the type of request in the building
	 * the function will check building_infos table (building settings)
	 */
	public function get_default_ntf_methods($building_id, $request_type_id)
	{
		try {
			$this->crud->query("SELECT if(find_in_set(:request_type_id_1,issue_normal),issue_normal_ntf,if(find_in_set(:request_type_id_2,issue_serious),issue_serious_ntf,if(find_in_set(:request_type_id_3,issue_urgent),issue_urgent_ntf, NULL ))) AS ntf_methods FROM building_infos WHERE building_id=:building_id");
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":request_type_id_1", $request_type_id);
			$this->crud->bind(":request_type_id_2", $request_type_id);
			$this->crud->bind(":request_type_id_3", $request_type_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the administrator for the building
	 * administrator : not only employee_id in building_infos
	 * Approach : search all employee in the company to find (admin_id == 1)
	 */
	public function get_administrator_for_building($building_id)
	{
		try {
			$this->crud->query("SELECT employee_id FROM employee_infos WHERE company_id = (select company_id from building_infos where building_id = :building_id) and admin_id = 1");
			$this->crud->bind(":building_id", $building_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------------ communication ------------------------

	/**
	 * get info of all communications for a request
	 * info : communication_id,user_name,user_role,entry_date,remarks,if_seen_by_tenant ...
	 * user_role : System | Employee | Tenant | Owner
	 */
	public function get_all_communications($request_id)
	{
		try {
			$this->crud->query("SELECT request_communications.id AS communication_id, user_id,if(is_system_msg = 1,'System', if(user_id < 100000,'Employee',if(user_id < 200000,'Tenant','Owner'))) as user_role,entry_date,remarks,if_seen_by_tenant,is_system_msg,is_image,system_msg_type FROM request_communications WHERE request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one communication for a request
	 * by default, the message_body of communication is text, but if message_body is image, need to set is_iamge = 1
	 */
	public function add_one_communication($request_id, $user_id, $remarks, $entry_date, $if_seen_by_tenant, $is_image = 0)
	{
		try {
			$this->crud->query("INSERT INTO request_communications (request_id,user_id,remarks,entry_date,if_seen_by_tenant,is_image) VALUES (:request_id,:user_id,:remarks,:entry_date,:if_seen_by_tenant,:is_image)");
			$this->crud->bind(':request_id', $request_id);
			$this->crud->bind(':user_id', $user_id);
			$this->crud->bind(':remarks', $remarks);
			$this->crud->bind(':entry_date', $entry_date);
			$this->crud->bind(':is_image', $is_image);
			$this->crud->bind(':if_seen_by_tenant', $if_seen_by_tenant);
			$this->crud->execute();
			$request_communication_id = $this->crud->lastInsertId();
			return $request_communication_id;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------------- report an issue ---------------------------

	public function get_tenant_unit($unit_id)
	{
		try {
			$this->crud->query("SELECT apartment_id,unit_number FROM apartment_infos WHERE apartment_id = :unit_id");
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the building_id, building_name in which the apartment in
	 */
	public function get_building_id($apartment_id)
	{
		try {
			$this->crud->query("SELECT building_id FROM apartment_infos WHERE apartment_id=:apartment_id");
			$this->crud->bind(":apartment_id", $apartment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_building_info($building_id)
	{
		try {
			$this->crud->query("SELECT building_name,address FROM building_infos WHERE building_id = :building_id ");
			$this->crud->bind(":building_id", $building_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * get all building list belonging to the company where the employee in
	 */
	public function get_all_buildings($employee_id)
	{
		try {
			$this->crud->query("SELECT building_id,building_name FROM building_infos WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id=(SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))");
			$this->crud->bind(":employee_id", $employee_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_floors($building_id)
	{
		try {
			$this->crud->query("SELECT floor_id,floor_name FROM floor_infos WHERE building_id=:building_id");
			$this->crud->bind(":building_id", $building_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_apartments($floor_id)
	{
		try {
			$this->crud->query("SELECT apartment_id,unit_number FROM apartment_infos WHERE floor_id=:floor_id");
			$this->crud->bind(":floor_id", $floor_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the tenant_ids of the apartment currently
	 * Note : the function will to check the lease_infos get the active lease(lease_status_id = 1,7,8,9) currently
	 */
	public function get_tenants($apartment_id)
	{
		try {
			$this->crud->query("SELECT tenant_ids FROM lease_infos WHERE apartment_id=:apartment_id AND lease_status_id IN (1,7,8,9)");
			$this->crud->bind(":apartment_id", $apartment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_tenant_name($tenant_id)
	{
		try {
			$this->crud->query("SELECT tenant_id,full_name FROM tenant_infos WHERE tenant_id=:tenant_id");
			$this->crud->bind(":tenant_id", $tenant_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_request_types_all()
	{
		try {
			$this->crud->query("SELECT id, name FROM request_types ORDER by name ASC");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the request type which is external only
	 * external : can be selected / used by tenants
	 */
	public function get_request_types_external()
	{
		try {
			$this->crud->query("SELECT id, name FROM request_types WHERE internal_only = 0");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * get the request request_status which is internal only
	 * internal : can not be selected / used by tenants
	 */
	public function get_request_status_internal()
	{
		try {
			$this->crud->query("SELECT id,name,frequency_use FROM request_status WHERE internal_only = 1 AND system_auto = 0");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the request request_status which is external only
	 * external : can be selected / used by tenants
	 */
	public function get_request_status_external()
	{
		try {
			$this->crud->query("SELECT id,name FROM request_status WHERE external_only = 1 and  system_auto = 0");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add a new request, and return the id of the new added request
	 */
	public function add_request_info($building_id, $apartment_id, $floor_id, $location, $created_user_id, $request_type_id, $status_id, $approveVisit, $timeFromVisit, $timeToVisit, $message, $entry_datetime, $common_area_detail, $request_category, $task_type, $invoiceFiles, $handyman_id, $vendor_id, $projectName = "Project Name", $material_data = null, $estimated_price = 0, $project_id = 0, $contract_id = 0, $invoiceId = null, $invoiceAmt = null, $reportRoomInfoSelect = null)
	{
		try {
			$this->crud->query("INSERT INTO request_infos(building_id, apartment_id, floor_id, location, employee_id, request_type_id, status_id,  message, entry_datetime, common_area_detail, request_category, material_provider, material_detail,vendor_estimated_price,task_type,invoices_attached,handyman_id,vendor_id,project_name,project_id,contract_id,invoice_id,invoice_amount,room_id) VALUES (:building_id, :apartment_id, :floor_id , :location, :employee_id, :request_type_id, :status_id,  :message, :entry_datetime, :common_area_detail, :request_category, :material_provider_id, :material_detail,:estimated_price,:task_type,:invoices_attached,:handyman_id,:vendor_id,:project_name,:project_id,:contract_id,:invoice_id,:invoice_amt,:room_id)"); //:timeFromVisit, :timeToVisit :approveVisit, , //approveVisit, timeFromVisit, timeToVisit,
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":apartment_id", $apartment_id);
			$this->crud->bind(":floor_id", $floor_id);
			$this->crud->bind(":location", $location);
			$this->crud->bind(":employee_id", $created_user_id);
			$this->crud->bind(":request_type_id", $request_type_id);
			$this->crud->bind(":status_id", $status_id);
			//			$this->crud->bind(":approveVisit", $approveVisit);
			//			if (!empty($timeFromVisit)){$this->crud->bind(":timeFromVisit", $timeFromVisit);}
			//            if (!empty($timeToVisit)){$this->crud->bind(":timeToVisit", $timeToVisit);}
			$this->crud->bind(":message", $message);
			$this->crud->bind(":entry_datetime", $entry_datetime);
			$this->crud->bind(":common_area_detail", $common_area_detail);
			$this->crud->bind(":request_category", $request_category);
			$this->crud->bind(":task_type", $task_type);
			$this->crud->bind(":invoices_attached", $invoiceFiles);
			$this->crud->bind(":handyman_id", $handyman_id);
			$this->crud->bind(":vendor_id", $vendor_id);
			$this->crud->bind(":project_name", $projectName);
			$this->crud->bind(":project_id", $project_id);
			$this->crud->bind(":contract_id", $contract_id);
			$this->crud->bind(":invoice_id", $invoiceId);
			$this->crud->bind(":invoice_amt", $invoiceAmt);
			$this->crud->bind(":room_id", $reportRoomInfoSelect);

			if ($material_data != null) {
				$this->crud->bind(":material_provider_id", $material_data["provider_id"]);
				$this->crud->bind(":material_detail", $material_data["detail"]);
			} else {
				$this->crud->bind(":material_provider_id", -1);
				$this->crud->bind(":material_detail", "");
			}

			if ($estimated_price != 0) {
				$this->crud->bind(":estimated_price", $estimated_price);
			} else {
				$this->crud->bind(":estimated_price", 0);
			}

			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add the assignee for a request, and his notification method for this request
	 */
	public function add_request_assignee($request_id, $user_id, $last_access_time, $notify_by_email, $notify_by_sms, $notify_bu_voice)
	{
		try {
			$this->crud->query("INSERT INTO request_assignees(request_id, user_id, last_access_time, notify_by_email, notify_by_sms, notify_by_voice) VALUES (:request_id, :user_id, :last_access_time, :notify_by_email, :notify_by_sms, :notify_bu_voice)");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":last_access_time", $last_access_time);
			$this->crud->bind(":notify_by_email", $notify_by_email);
			$this->crud->bind(":notify_by_sms", $notify_by_sms);
			$this->crud->bind(":notify_bu_voice", $notify_bu_voice);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the last access time of the request for a user
	 * Note : the last access time will be used to judge if the request is read or unread
	 */
	public function update_client_last_access_time($timestamp, $request_id, $user_id)
	{
		try {
			$this->crud->query("UPDATE request_assignees SET last_access_time= :last_access_time WHERE request_id = :request_id AND user_id = :user_id");
			$this->crud->bind(":last_access_time", $timestamp);
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}



	//---------------------------- Edit a Request ------------------------------

	/**
	 * get related info about a request
	 * info : 1. all info in request_infos
	 *        2. request_type_name, status_name
	 *        3. unit_number (if request.location is apartment)
	 *        4. building_name
	 *        5.close_or_open
	 */
	public function get_request_ori_info($request_id)
	{
		try {
			$this->crud->query("SELECT request_infos.* ,request_types.name AS request_type_name,request_status.name AS status_name,request_status.external_only AS internal_status, IF(request_infos.location='2',(SELECT apartment_infos.unit_number FROM apartment_infos WHERE apartment_infos.apartment_id=request_infos.apartment_id),NULL )AS unit_number,building_infos.building_name AS building_name, building_infos.issue_past_after_days AS issue_past_after_days, (if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,22),'closed','open')) AS close_or_open  FROM request_infos,request_types,request_status,building_infos WHERE request_status.id=request_infos.status_id AND request_types.id=request_infos.request_type_id AND building_infos.building_id=request_infos.building_id AND request_infos.id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the notification method of the request for a user
	 */
	public function get_notify_by($request_id, $user_id)
	{
		try {
			$this->crud->query("SELECT notify_by_email,notify_by_sms,notify_by_voice FROM request_assignees WHERE request_id=:request_id AND user_id=:user_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_request_status_name($status_id)
	{
		try {
			$this->crud->query("SELECT name FROM request_status WHERE id=:status_id");
			$this->crud->bind(":status_id", $status_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * edit the request, if the request status is changed, add one system message in this request communication
	 * only used by employee
	 * Approach :
	 *      1. get the old request status
	 *      2. change the info for the request
	 *      3. (if request status change) add one record to request_communications(system message)
	 */
	public function edit_request_by_employee($request_id, $user_id, $request_type_id, $status_id, $message, $closed_by)
	{
		try {
			$this->crud->query("SELECT status_id FROM request_infos WHERE request_infos.id  = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$old_status_id = $this->crud->resultSingle()['status_id'];

			$this->crud->query("UPDATE request_infos SET request_type_id=:request_type_id, status_id=:status_id, message=:message, closed_by=:closed_by_user_name WHERE id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":request_type_id", $request_type_id);
			$this->crud->bind(":status_id", $status_id);
			$this->crud->bind(":closed_by_user_name", $closed_by);
			$this->crud->bind(":message", $message);
			$this->crud->execute();

			// add system message to request communication -- record this change
			if ($old_status_id != $status_id) {
				$this->crud->query("SELECT name FROM request_status WHERE id =:status_id");
				$this->crud->bind(":status_id", $status_id);
				$new_status = $this->crud->resultSingle()['name'];

				$this->crud->query("INSERT INTO request_communications (request_id,user_id,remarks, entry_date,if_seen_by_tenant, is_system_msg) VALUES (:request_id,:user_id,:remarks,:entry_date,:if_seen_by_tenant,:is_system_msg)");
				$this->crud->bind(":request_id", $request_id);
				$this->crud->bind(":user_id", $user_id);
				$this->crud->bind(":remarks", "REQUEST STATUS CHANGED TO " . strtoupper($new_status));
				$current_time = date("Y-m-d H:i:s");
				$this->crud->bind(":entry_date", $current_time);
				$this->crud->bind(":if_seen_by_tenant", 1);
				$this->crud->bind(":is_system_msg", 1);
				$this->crud->execute();
				return true;
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * edit the request, if the request status is changed, add one system message in this request communication
	 * only used by tenant (some limitation in editing)
	 * Approach :
	 *      1. get the old request status
	 *      2. change the info for the request
	 *      3. (if request status change) add one record to request_communications(system message)
	 */
	public function edit_request_by_tenant($request_id, $user_id, $status_id, $closed_by)
	{
		try {
			$this->crud->query("SELECT status_id FROM request_infos WHERE request_infos.id  = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$old_status_id = $this->crud->resultSingle()['status_id'];

			$this->crud->query("UPDATE request_infos SET status_id=:status_id, closed_by = :closed_by WHERE id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":status_id", $status_id);
			$this->crud->bind(":closed_by", $closed_by);
			$this->crud->execute();


			// add system message to request communication -- record this change
			if ($old_status_id != $status_id) {
				$this->crud->query("SELECT name FROM request_status WHERE id =:status_id");
				$this->crud->bind(":status_id", $status_id);
				$new_status = $this->crud->resultSingle()['name'];

				$this->crud->query("INSERT INTO request_communications (request_id,user_id,remarks, entry_date,if_seen_by_tenant, is_system_msg) VALUES (:request_id,:user_id,:remarks,:entry_date,:if_seen_by_tenant,:is_system_msg)");
				$this->crud->bind(":request_id", $request_id);
				$this->crud->bind(":user_id", $user_id);
				$this->crud->bind(":remarks", "REQUEST STATUS CHANGED TO " . strtoupper($new_status));
				$current_time = date("Y-m-d H:i:s");
				$this->crud->bind(":entry_date", $current_time);
				$this->crud->bind(":if_seen_by_tenant", 1);
				$this->crud->bind(":is_system_msg", 1);
				$this->crud->execute();
			}
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * edit the notification method of the request for the user
	 */
	public function edit_notify($request_id, $user_id, $last_access_time, $notify_by_email, $notify_by_sms, $notify_by_voice)
	{
		try {
			$this->crud->query("UPDATE request_assignees SET last_access_time=:last_access_time, notify_by_email=:notify_by_email,notify_by_sms=:notify_by_sms,notify_by_voice=:notify_by_voice WHERE request_id=:request_id AND user_id=:user_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":last_access_time", $last_access_time);
			$this->crud->bind(":notify_by_email", $notify_by_email);
			$this->crud->bind(":notify_by_sms", $notify_by_sms);
			$this->crud->bind(":notify_by_voice", $notify_by_voice);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_a_floor($apartment_id)
	{
		try {
			$this->crud->query("SELECT floor_id FROM apartment_infos WHERE apartment_id=:apartment_id");
			$this->crud->bind(":apartment_id", $apartment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function change_request_status($request_id, $change_to)
	{
		try {
			$this->crud->query("UPDATE request_infos SET status_id = :change_to WHERE id = :request_id");
			$this->crud->bind(":change_to", $change_to);
			$this->crud->bind(":request_id", $request_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * change the category to tenant request if the tenant is included in assignees list
	 * the method will be used for every time the recipient change, because the tenant may be add to assignee list in the middle
	 */
	public function update_issue_category_if_tenant_issue($request_id)
	{
		try {
			$this->crud->query("SELECT user_id FROM request_assignees WHERE request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$user_ids = $this->crud->resultSet();

			$request_category = 1;
			foreach ($user_ids as $row) {
				$user_id = $row['user_id'];
				if ($user_id > 100000 && $user_id < 200000) {
					$request_category = 2;
					break;
				}
			}

			$this->crud->query("UPDATE request_infos SET request_category = :request_category WHERE id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":request_category", $request_category);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Update the location for the request and the schedule (from time and the to time)
	// $data variable has the location,schedule data
	public function updateLocationAndSchedule($request_id, $data)
	{
		try {
			if ($data["editRequestLocationReportArea"] == 1) {
				$query = "UPDATE request_infos SET building_id = :building_id,location = :location, common_area_detail = :common_area, datetime_from = :datetime_from, datetime_to=:datetime_to, project_name = :project_name WHERE id = :request_id";
				$this->crud->query($query);
			} else {
				$query = "UPDATE request_infos SET building_id = :building_id,floor_id = :floor_id, apartment_id = :apartment_id, location = :location, common_area_detail = :common_area, datetime_from = :datetime_from, datetime_to=:datetime_to, project_name = :project_name WHERE id = :request_id";
				$this->crud->query($query);
				$this->crud->bind(":apartment_id", $data["editRequestLocationApt"]);
				$this->crud->bind(":floor_id", $data["editRequestLocationFloor"]);
			}
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":location", $data["editRequestLocationReportArea"]);
			$this->crud->bind(":common_area", $data["editRequestLocationCommonArea"]);
			$this->crud->bind(":building_id", $data["editRequestLocationBuilding"]);
			$this->crud->bind(":datetime_from", $data["reportEditDateTimeFrom"]);
			$this->crud->bind(":datetime_to", $data["reportEditDateTimeTo"]);
			$this->crud->bind(":project_name", $data["editRequestProjectName"]);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//--------------------- Send a Notification ----------------------

	/**
	 * get the info to sent notification for a request
	 * the function only be used for notification
	 */
	public function get_notify_info($request_id)
	{
		try {
			$this->crud->query("SELECT building_infos.building_name as location_1, if(request_infos.location='2',(SELECT apartment_infos.unit_number FROM apartment_infos WHERE apartment_infos.apartment_id=request_infos.apartment_id),if(request_infos.location='1',(SELECT request_infos.common_area_detail),NULL )) AS location_2,request_types.name AS request_type_name,request_infos.message,request_status.name AS request_status_name FROM building_infos,request_types,request_status,request_infos WHERE building_infos.building_id=request_infos.building_id AND request_types.id=request_infos.request_type_id AND request_status.id=request_infos.status_id AND request_infos.id=:request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_all_assignees_for_issue($request_id)
	{
		try {
			$this->crud->query("SELECT user_id FROM request_assignees where request_id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get user_name, remarks(message_body), entry_date of a request_communication
	 */
	public function get_communication_detail($request_communication_id)
	{
		try {
			$this->crud->query("SELECT userlist.full_name ,remarks,entry_date FROM request_communications,userlist WHERE userlist.user_id = request_communications.user_id AND request_communications.id = :request_communications_id");
			$this->crud->bind(":request_communications_id", $request_communication_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}




	//---------------------------------  filters ---------------------------------

	/**
	 * get the building list for the user
	 * if the user is employee, get all buildings belonging to his company
	 * if the user is tenant, get the building where he is living
	 */
	public function get_building_list($user_id)
	{
		if ($user_id < 100000 || $user_id > 200000) {
			try {
				$this->crud->query("SELECT building_id, building_name FROM building_infos WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id =:employee_id))");
				$this->crud->bind(":employee_id", $user_id);
				$results = $this->crud->resultSet();
				return $results;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			try {
				$query = "SELECT building_infos.building_id AS building_id , building_name FROM  building_infos  WHERE building_infos.building_id = (SELECT MAX(lease_infos.building_id) FROM lease_infos WHERE find_in_set(:tenant_id,lease_infos.tenant_ids) AND lease_status_id IN (1,7,8,9,10))";
				$this->crud->query($query);
				$this->crud->bind(":tenant_id", $user_id);
				$result = $this->crud->resultSingle();
				return $result;
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
	}


	public function get_unit_lst_in_building($building_id)
	{
		try {
			$this->crud->query("SELECT apartment_id,unit_number FROM apartment_infos WHERE building_id =:building_id");
			$this->crud->bind(":building_id", $building_id);
			$results = $this->crud->resultSet();
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all employee in the company in which the user(employee) in
	 */
	public function get_employees_lst($user_id)
	{
		try {
			$this->crud->query("SELECT employee_id,full_name FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id)");
			$this->crud->bind(":employee_id", $user_id);
			$results = $this->crud->resultSet();
			return $results;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//---------------------------------  bulletins ---------------------------------

	/**
	 * get the buildings for the employee
	 * the function will check whether the employee is manager(employee_infos.admin_id) or not
	 * if the employee is manager, return all building belonging to his company, not care about employee_infos.building_ids
	 * if the employee is not manager, return buildings, based on employee_infos.building_ids
	 */
	public function get_bulletin_buildings($user_id)
	{
		try {
			$result = null;
			$this->crud->query("SELECT admin_id FROM employee_infos WHERE employee_id = :employee_id");
			$this->crud->bind(":employee_id", $user_id);
			$if_admin = $this->crud->resultSingle()['admin_id'];

			if ($if_admin == 1) {
				$this->crud->query("SELECT building_id,building_name FROM building_infos WHERE building_id IN (SELECT building_id FROM building_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))");
				$this->crud->bind(":employee_id", $user_id);
				$result = $this->crud->resultSet();
			} else {
				$this->crud->query("SELECT building_id,building_name FROM building_infos WHERE find_in_set (building_id, (SELECT building_ids FROM employee_infos WHERE employee_id=:user_id))");
				$this->crud->bind(":user_id", $user_id);
				$result = $this->crud->resultSet();
			}
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenants_building($building_id)
	{
		try {
			$result = null;
			$this->crud->query("SELECT TI.tenant_id, TI.full_name, TI.email FROM tenant_infos TI, lease_infos LI WHERE (FIND_IN_SET(TI.tenant_id,LI.tenant_ids) and LI.lease_status_id in (1,7,8,9,10)) AND LI.building_id=$building_id");
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenants_floor($floor_id)
	{
		try {
			$result = null;
			$sql = "SELECT TI.tenant_id, TI.full_name, TI.email,APP.apartment_id FROM lease_infos LI LEFT JOIN tenant_infos TI ON (FIND_IN_SET(TI.tenant_id,LI.tenant_ids)) LEFT JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id WHERE LI.lease_status_id IN (1,2,7,8,9,10) AND floor_id=$floor_id ";
			//           echo($sql);
			$this->crud->query($sql);
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenants_apartments($apartment_ids)
	{
		try {
			$result = null;
			$sql = "SELECT TI.tenant_id, TI.full_name, TI.email,APP.apartment_id FROM lease_infos LI LEFT JOIN tenant_infos TI ON (FIND_IN_SET(TI.tenant_id,LI.tenant_ids)) LEFT JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id WHERE LI.lease_status_id IN (1,2,7,8,9,10) AND APP.apartment_id in ($apartment_ids) ";
			//         echo($sql);
			$this->crud->query($sql);
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	public function add_bulletin($building_id, $employee_id, $create_time, $issue_from, $issue_to, $message_title, $message_body, $attachment)
	{
		try {
			$this->crud->query("INSERT INTO bulletins(building_id, employee_id, create_time, issue_from, issue_to, message_title, message_body, attachment) VALUES (:building_id, :employee_id, :create_time, :issue_from, :issue_to, :message_title, :message_body, :attachment)");
			$this->crud->bind(":building_id", $building_id);
			$this->crud->bind(":employee_id", $employee_id);
			$this->crud->bind(":create_time", $create_time);
			$this->crud->bind(":issue_from", $issue_from);
			$this->crud->bind(":issue_to", $issue_to);
			$this->crud->bind(":message_title", $message_title);
			$this->crud->bind(":message_body", $message_body);
			$this->crud->bind(":attachment", $attachment);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get all bulletins for the tenant & employee
	 * if the user is employee, parameter unit_is is not useful
	 *      if the user is manager, get all bulletin related to all buildings of his company
	 *      if the user is not manager, get all bulletin related to buildings he can access (employee_infos.building_ids)
	 * if the user is tenant, get bulletin belonging to the building where he is living
	 * Note: tenant may has several unit in diff building -> need unit_id
	 */
	public function get_bulletin_list($user_id, $unit_id)
	{
		//$unit_id only useful when user is tenant(to handle the case one tenant has many units)
		$result = null;
		try {
			if ($user_id < 100000) {
				$this->crud->query("SELECT admin_id FROM employee_infos WHERE employee_id = :employee_id");
				$this->crud->bind(":employee_id", $user_id);
				$if_admin = $this->crud->resultSingle()['admin_id'];

				if ($if_admin == 1) {
					$this->crud->query("SELECT bulletins.id AS bulletin_id,building_infos.building_name AS building_name,employee_infos.full_name AS issuer_name,employee_infos.mobile AS issuer_telephone,create_time,issue_from,issue_to,message_title,message_body,is_active FROM bulletins,building_infos,employee_infos WHERE bulletins.building_id in (SELECT building_id FROM building_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id)) AND building_infos.building_id = bulletins.building_id AND employee_infos.employee_id = bulletins.employee_id ORDER BY create_time DESC");
					$this->crud->bind(":employee_id", $user_id);
					$result = $this->crud->resultSet();
				} else {
					$this->crud->query("SELECT bulletins.id AS bulletin_id,building_infos.building_name AS building_name,employee_infos.full_name AS issuer_name,employee_infos.mobile AS issuer_telephone,create_time,issue_from,issue_to,message_title,message_body,is_active FROM bulletins,building_infos,employee_infos WHERE find_in_set(bulletins.building_id,(SELECT building_ids FROM employee_infos WHERE employee_id = :employee_id)) AND building_infos.building_id = bulletins.building_id AND employee_infos.employee_id = bulletins.employee_id ORDER BY create_time DESC");
					$this->crud->bind(":employee_id", $user_id);
					$result = $this->crud->resultSet();
				}
			} elseif ($user_id < 200000) {
				$this->crud->query("SELECT bulletins.id AS bulletin_id,building_infos.building_name AS building_name,employee_infos.full_name AS issuer_name,employee_infos.mobile AS issuer_telephone,create_time,issue_from,issue_to,message_title,message_body,is_active FROM bulletins, building_infos, employee_infos WHERE building_infos.building_id = bulletins.building_id AND employee_infos.employee_id = bulletins.employee_id AND bulletins.building_id = (SELECT building_id FROM apartment_infos WHERE apartment_id = :unit_id) ORDER BY create_time DESC");
				$this->crud->bind(":unit_id", $unit_id);
				$result = $this->crud->resultSet();
			}
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get detailed info about the bulletin
	 * info :
	 *      1. building info
	 *      2. issuer info
	 *      3. bulletin info
	 */
	public function get_bulletin_info($bulletin_id)
	{
		try {
			$this->crud->query("SELECT bulletins.id AS bulletin_id,building_infos.building_name AS building_name,employee_infos.full_name AS issuer_name,employee_infos.mobile AS issuer_telephone,create_time,issue_from,issue_to,message_title,message_body FROM bulletins,building_infos,employee_infos WHERE bulletins.id = :bulletin_id AND building_infos.building_id = bulletins.building_id AND employee_infos.employee_id = bulletins.employee_id");
			$this->crud->bind(":bulletin_id", $bulletin_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the reading status of tenants for a bulletin
	 * Approach :
	 *      1. get tenants list related to the bulletin
	 *      2. get name, user_name, last_login_time for each tenant
	 */
	public function get_bulletin_reading_status($bulletin_id)
	{
		try {
			$this->crud->query('SELECT tenant_ids FROM lease_infos, bulletins WHERE lease_infos.building_id = bulletins.building_id AND lease_infos.lease_status_id IN (1,7,8,9) AND bulletins.id =:bulletin_id');
			$this->crud->bind(":bulletin_id", $bulletin_id);
			$tenants = $this->crud->resultSet();

			$tenants_list = array();
			foreach ($tenants as $row) {
				$temp = explode(',', $row['tenant_ids']);
				foreach ($temp as $t) {
					array_push($tenants_list, $t);
				}
			}

			$tenants_str = implode(',', $tenants_list);
			$tenants_str = '(' . $tenants_str . ')';
			$this->crud->query("SELECT full_name,username,last_login_time FROM tenant_infos WHERE tenant_infos.tenant_id in " . $tenants_str . " AND tenant_infos.last_login_time > (SELECT create_time FROM bulletins WHERE bulletins.id = :bulletin_id ) ORDER BY full_name");
			$this->crud->bind(":bulletin_id", $bulletin_id);
			$readed_tenant_infos = $this->crud->resultSet();
			return $readed_tenant_infos;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * close the bulletin by setting is_active = 0
	 * Note: the bulletin with (is_active=1 & time is in the bulletin range) will be seen by tenant
	 */
	public function close_bulletin($bulletin_id)
	{
		try {
			$this->crud->query("UPDATE bulletins SET is_active = 0 WHERE id = :id");
			$this->crud->bind(":id", $bulletin_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		return true;
	}


	//------------------------------  scripting to modify the building access for employee ---------------------------
	// the script will be run after click 'save' in employee's buildings settings.
	// if the employee is not manager, the request he can access is based on request_assignees table
	// for this reason, while doing some change in employee's building settings, the record need to be removed / added into request_assignees.

	public function get_building_access_for_employee($employee_id)
	{
		try {
			$this->crud->query("SELECT building_ids from employee_infos where employee_id = :employee_id");
			$this->crud->bind(":employee_id", $employee_id);
			return $this->crud->resultSingle()['building_ids'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * delete the access of all request belonging to a list of building for a employee
	 * parameter $building_ids is a array of building_id
	 */
	public function delete_building_access_for_employee($employee_id, $building_ids)
	{
		try {
			$building_ids = implode(',', $building_ids);

			$this->crud->query("DELETE FROM request_assignees WHERE user_id = :employee_id AND request_id IN (SELECT id FROM request_infos WHERE building_id IN ( " . $building_ids . " ))");
			$this->crud->bind(":employee_id", $employee_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add access of request belonging to the building_ids for the employee
	 * Approach :
	 *      1. get all request related to buildings
	 *      2. for each related request, set the notification method
	 *      3. add all to request_assignees
	 */
	public function add_building_access_for_employee($employee_id, $building_ids)
	{
		try {
			foreach ($building_ids as $one) {
				//get building settings about request level
				$this->crud->query("SELECT issue_normal,issue_normal_ntf,issue_serious,issue_serious_ntf,issue_urgent,issue_urgent_ntf FROM building_infos WHERE building_id = :building_id");
				$this->crud->bind(":building_id", $one);
				$building_settings = $this->crud->resultSingle();

				$issue_serious     = explode(',', $building_settings['issue_serious']);
				$issue_urgent      = explode(',', $building_settings['issue_urgent']);
				$issue_normal_ntf  = $building_settings['issue_normal_ntf'];
				$issue_serious_ntf = $building_settings['issue_serious_ntf'];
				$issue_urgent_ntf  = $building_settings['issue_urgent_ntf'];

				//get the request related to this building
				$this->crud->query('SELECT id,request_type_id from request_infos where building_id = :building_id and entry_datetime > "2001-01-01"');
				$this->crud->bind(":building_id", $one);
				$requests = $this->crud->resultSet();

				$query_set = array();

				foreach ($requests as $req) {

					$request_id = $req['id'];
					$query      = '(' . $request_id . ',' . $employee_id . ',';

					if (in_array($req['request_type_id'], $issue_serious)) {
						if (strpos($issue_serious_ntf, '1') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_serious_ntf, '2') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_serious_ntf, '3') === false)
							$query .= '0';
						else
							$query .= '1';
					} elseif (in_array($req['request_type_id'], $issue_urgent)) {

						if (strpos($issue_urgent_ntf, '1') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_urgent_ntf, '2') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_urgent_ntf, '3') === false)
							$query .= '0';
						else
							$query .= '1';
					} else {

						if (strpos($issue_normal_ntf, '1') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_normal_ntf, '2') === false)
							$query .= '0,';
						else
							$query .= '1,';

						if (strpos($issue_normal_ntf, '3') === false)
							$query .= '0,';
						else
							$query .= '1,';
					}

					$query .= '"2001-01-01 00:00:00")';
					array_push($query_set, $query);
				}

				$sql = 'INSERT INTO request_assignees (request_id,user_id,notify_by_sms,notify_by_email,notify_by_voice,last_access_time) VALUES ' . implode(',', $query_set);
				$this->crud->query($sql);
				$this->crud->execute();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//------------------------------------ handyman time slots ------------------------------

	/**
	 * get available handyman slot date for responsible employees in a building
	 * param $responsible_employee_list should be array
	 */
	public function get_avail_date($responsible_employee_list, $building_id)
	{
		if (!is_array($responsible_employee_list) || sizeof($responsible_employee_list) == 0) {
			return null;
		}
		try {
			$employee_id_str = implode(',', $responsible_employee_list);
			$this->crud->query('SELECT * FROM handyman_available_slots WHERE building_id = :building_id AND handyman_id IN (' . $employee_id_str . ')');
			$this->crud->bind(":building_id", $building_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * change the handyman booking confirm status
	 * parameter : $booking_status[0:available, 1:taken by tenant & wait to confirm , 2:only confirm by either handyman or manager, 3:confirm by both]
	 * Note : to record the handyman_bookings.id, the handyman_bookings.id will be kept in remark of the system message temporarily after the system message was created.
	 */
	public function change_handyman_booking_confirm_status($request_communication_id, $booking_status)
	{
		try {
			$this->crud->query("select remarks from request_communications where id = :id");
			$this->crud->bind(":id", $request_communication_id);
			$handyman_booking_id = intval(trim($this->crud->resultSingle()['remarks']));

			if ($booking_status != 0) {
				$this->crud->query("UPDATE handyman_bookings SET is_confirmed = :status WHERE id = :handyman_booking_id");
				$this->crud->bind(":handyman_booking_id", $handyman_booking_id);
				$this->crud->bind(":status", $booking_status);
				$this->crud->execute();
			} else {     //make it available
				//delete the record from bookings, make it available
				$this->crud->query("DELETE FROM handyman_bookings WHERE id = :handyman_booking_id");
				$this->crud->bind(":handyman_booking_id", $handyman_booking_id);
				$this->crud->execute();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the handyman booking confirm status, based on request communication id
	 * because for handyman system message, the corresponding handyman booking id kept in remarks in the communication
	 * so 1. get the handyman booking id from remark firstly, then get the is_confirmed based on this id
	 */
	public function get_handyman_booking_confirm_status($request_communication_id)
	{
		try {
			$this->crud->query("select remarks from request_communications where id = :id");
			$this->crud->bind(":id", $request_communication_id);
			$handyman_booking_id = intval(trim($this->crud->resultSingle()['remarks']));

			$this->crud->query("SELECT is_confirmed FROM handyman_bookings WHERE id = :handyman_booking_id");
			$this->crud->bind(":handyman_booking_id", $handyman_booking_id);
			return $this->crud->resultSingle()['is_confirmed'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//------------------------------------- system requests ---------------------------------------------------

	/**
	 * get all late payment list for this month
	 * the function is global level, for every building, every company in the system
	 */
	public function get_late_payment_list()
	{
		try {
			$today = date('Y-m-d');
			$this->crud->query("SELECT * FROM lease_payments,lease_infos WHERE lease_payments.lease_id = lease_infos.id
                                        AND lease_status_id IN (1,7,8,9) AND  payment_status_id IN (1,2) AND due_date > :today
                                        AND due_date BETWEEN CONCAT(YEAR(CURDATE()), '-',MONTH(CURDATE()),'-01') AND CONCAT(YEAR(CURDATE()),'-', MONTH(CURDATE()), '-30')");
			$this->crud->bind(":today", $today);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add system communication message for a request
	 * system message : created by system, including :
	 *      1. message about request status change
	 *      2. handyman status confirm
	 *      3. late lease payment prompt
	 *
	 * Param : system_msg_type : 0 - default value (the remark of this type message will showed in view)
	 *                           1 - (handyman event) get confirm from property manager - three button in view
	 *                           2 - (handyman event) get confirm from property manager - two button in view
	 */
	public function add_system_communication_for_request($request_id, $user_id, $remarks, $entry_date, $if_seen_by_tenant, $is_system_msg, $system_msg_type)
	{
		try {
			$this->crud->query("INSERT INTO request_communications (request_id, user_id, remarks, entry_date, if_seen_by_tenant, is_system_msg, system_msg_type) VALUES (:request_id, :user_id, :remarks, :entry_date, :if_seen_by_tenant, :is_system_msg, :system_msg_type)");
			$this->crud->bind(':request_id', $request_id);
			$this->crud->bind(':user_id', $user_id);
			$this->crud->bind(':remarks', $remarks);
			$this->crud->bind(':entry_date', $entry_date);
			$this->crud->bind(':if_seen_by_tenant', $if_seen_by_tenant);
			$this->crud->bind(':is_system_msg', $is_system_msg);
			$this->crud->bind(':system_msg_type', $system_msg_type);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * edit request system communication
	 * Param : system_msg_type : 0 - default value (the remark of this type message will showed in view)
	 *                           1 - (handyman event) get confirm from property manager - three button in view
	 *                           2 - (handyman event) get confirm from property manager - two button in view
	 */
	public function edit_system_communication_for_request($request_communication_id, $remarks, $entry_date, $if_seen_by_tenant, $is_system_msg, $system_msg_type)
	{
		try {
			$this->crud->query("UPDATE request_communications SET remarks = :remarks , entry_date = :entry_date, if_seen_by_tenant = :if_seen_by_tenant, is_system_msg = :is_system_msg, system_msg_type = :system_msg_type WHERE id = :id");
			$this->crud->bind(':remarks', $remarks);
			$this->crud->bind(':entry_date', $entry_date);
			$this->crud->bind(':if_seen_by_tenant', $if_seen_by_tenant);
			$this->crud->bind(':is_system_msg', $is_system_msg);
			$this->crud->bind(':system_msg_type', $system_msg_type);
			$this->crud->bind(':id', $request_communication_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getRequestType($request_id)
	{
		try {
			$this->crud->query("SELECT request_type_id FROM request_infos where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $result = $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// ----------------------------------- Misc ---------------------------------------------------------------
	public function getOnlineStores()
	{
		try {
			$this->crud->query("SELECT * FROM online_stores");
			return $result = $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getEstimatedPrice($request_id)
	{
		try {
			//			$this->crud->query("SELECT vendor_estimated_price FROM request_infos where id = :request_id");
			$this->crud->query("SELECT vendor_estimated_price FROM contract_infos WHERE project_id = (SELECT project_id FROM request_infos WHERE id = :request_id)");
			$this->crud->bind(":request_id", $request_id);
			return $result = $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getMaterialProvided($request_id)
	{
		try {
			$this->crud->query("SELECT material_detail,material_provider FROM request_infos where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $result = $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function setMaterialProvided($materialData, $request_id)
	{
		try {
			$this->crud->query("UPDATE request_infos SET material_detail = :materialDetail,material_provider=:material_provider where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":materialDetail", $materialData["detail"]);
			$this->crud->bind(":material_provider", $materialData["provider_id"]);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateEventDateTime($request_id, $requestSetdateAndTimeFrom, $requestSetTaskDateTimeTo)
	{
		try {
			$this->crud->query("UPDATE request_infos SET datetime_from = :dateTimeEventFrom,datetime_to = :dateTimeEventTo where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":dateTimeEventFrom", $requestSetdateAndTimeFrom);
			$this->crud->bind(":dateTimeEventTo", $requestSetTaskDateTimeTo);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getInvoicesAttached($request_id)
	{
		try {
			$this->crud->query("SELECT invoices_attached FROM request_infos where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			return $result = $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Get the location infos from the location_infos table
	* Fetches the Common area, apartment choices to display in the select input
	*/
	public function getLocationInfos()
	{
		try {
			$this->crud->query("SELECT * FROM location_infos");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo "Error=" . $e->getMessage();
		}
	}

	/* Get all the Projects from project_infos table */
	public function getProjectInfos()
	{
		try {
			$this->crud->query("SELECT * FROM project_infos ORDER BY name ASC");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getContractInfos()
	{
		try {
			$this->crud->query("SELECT * FROM contract_infos ORDER BY contract_desc ASC");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Create a new Project
	Store the details in project_infos table
	 */
	public function createProject($data)
	{
		$this->crud->query("INSERT INTO project_infos (name, location_id,address,company_id) VALUES (:name, :location_id,:address,:company_id)");
		$this->crud->bind(':name', $data["name"]);
		$this->crud->bind(':location_id', $data["location"]);
		$this->crud->bind(':address', $data["address"]);
		$this->crud->bind(':company_id', $data["company_id"]);
		$this->crud->execute();
		return $this->crud->rowCount();
	}

	/* Create a new contract
	Store the details in contract_infos table
	 */
	public function createContract($data)
	{
		$this->crud->query("INSERT INTO contract_infos (project_id, vendor_id, request_type_id,contract_desc,vendor_estimated_price,contract_price,company_id) VALUES (:project_id, :vendor_id,:request_type_id,:contract_desc,:vendor_estimated_price,:contract_price,:company_id)");
		$this->crud->bind(':project_id', $data["projectId"]);
		$this->crud->bind(':contract_desc', $data["contractDesc"]);
		$this->crud->bind(':vendor_id', $data["vendorId"]);
		$this->crud->bind(':request_type_id', $data["typeId"]);
		$this->crud->bind(':vendor_estimated_price', $data["estimatedPrice"]);
		$this->crud->bind(':contract_price', $data["contractPrice"]);
		$this->crud->bind(':company_id', $data["company_id"]);
		$this->crud->execute();
		return $this->crud->rowCount();
	}

	public function getContractsByProjectId($pid)
	{
		try {
			$this->crud->query("SELECT * FROM contract_infos WHERE project_id = :project_id and is_active != 0");
			$this->crud->bind(':project_id', $pid);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* get the contract info by contract id */
	public function getContractDataByContractId($cid)
	{

		try {
			$this->crud->query("SELECT * FROM contract_infos WHERE contract_id = :contract_id");
			$this->crud->bind(':contract_id', $cid);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Fetch a single project information*/
	public function getProjectInfo($pid)
	{
		try {
			$this->crud->query("SELECT * FROM project_infos WHERE project_id = :project_id");
			$this->crud->bind(':project_id', $pid);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Deactive the request - this request wont be shown in the request list */
	public function deactivateRequest($request_id)
	{
		try {
			$this->crud->query("UPDATE request_infos SET is_active = 0 where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function deactivateProject($project_id)
	{
		try {
			$this->crud->query("UPDATE project_infos SET is_active = 0 where project_id = :project_id");
			$this->crud->bind(":project_id", $project_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function deactivateContract($contract_id)
	{
		try {
			$this->crud->query("UPDATE contract_infos SET is_active = 0 where contract_id = :contract_id");
			$this->crud->bind(":contract_id", $contract_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Update the invoices attached files - add or delete */
	public function updateInvoicesAttached($request_id, $data)
	{
		try {
			$this->crud->query("UPDATE request_infos SET invoices_attached = :data where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":data", $data);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* update the invoice id and the invoice amount for a request */
	public function updateInvoiceDetails($request_id, $data)
	{
		try {
			$this->crud->query("UPDATE request_infos SET invoice_id = :invoice_id, invoice_amount = :invoice_amount where id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":invoice_id", $data["invoice_id"]);
			$this->crud->bind(":invoice_amount", $data["invoice_amount"]);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateVendorId($request_id, $vendor_id)
	{
		try {
			$this->crud->query("UPDATE request_infos SET vendor_id = :vendor_id WHERE id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":vendor_id", $vendor_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Get the sum of the paid amount for the contract ID specified */
	public function getBalanceFromBill($contractId)
	{
		try {
			$this->crud->query("SELECT SUM(paid_amount) AS balance FROM invoice_infos WHERE contract_id = :contract_id");
			$this->crud->bind(':contract_id', $contractId);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function calculateProjectCount($vendor_id)
	{
		try {
			//			$this->crud->query("UPDATE vendor_infos SET projects_no = (SELECT COUNT(DISTINCT(project_id)) FROM contract_infos WHERE vendor_id = :vendor_id and is_active = 1) WHERE vendor_id = :vendor_id");
			$this->crud->query("UPDATE vendor_infos SET projects_no = (SELECT COUNT(DISTINCT(ci.project_id)) FROM contract_infos ci JOIN project_infos pinfo ON ci.project_id = pinfo.project_id WHERE vendor_id = :vendor_id AND ci.is_active = 1 AND pinfo.is_active = 1) WHERE vendor_id = :vendor_id");
			$this->crud->bind(":vendor_id", $vendor_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function calculateProjectRequestCount($vendor_id)
	{
		try {
			$this->crud->query("UPDATE vendor_infos SET requests_no = (SELECT COUNT(id) FROM request_infos WHERE vendor_id = :vendor_id) WHERE vendor_id = :vendor_id");
			$this->crud->bind(":vendor_id", $vendor_id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Get all the unique projects from contract infos table - this is a list of all the projects in the system ( a project is considered only if it has a contract )*/
	public function getUniqueProjects()
	{
		try {
			$companyid = true;
			if (isset($_SESSION['company_id'])) {
				$companyid = $_SESSION['company_id'];
			}
			if (isset($_SESSION['tenant_id'])) {
				$this->crud->query("select company_id from tenant_infos where tenant_id=" . $_SESSION['tenant_id']);
				$companyid = $this->crud->resultField();
				//   die($companyid);
			}
			// $this->crud->query("SELECT DISTINCT(project_id) FROM contract_infos WHERE is_active = 1 ORDER BY project_id DESC ");
			$sql = "SELECT DISTINCT(contract_infos.project_id) FROM contract_infos JOIN project_infos ON project_infos.project_id = contract_infos.project_id WHERE contract_infos.is_active = 1 AND project_infos.company_id = $companyid ORDER BY contract_infos.project_id DESC";
			//die($sql);
			$this->crud->query($sql);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Functions from Nasrin
	 */

	/**
	 * Get all contracts
	 * @param $companyId
	 * @return mixed
	 */
	public function getAllContractsByCompanyId($companyId)
	{
		try {
			$this->crud->query("SELECT * FROM contract_infos WHERE company_id = :id");
			$this->crud->bind(":id", $companyId);
			$rows = $this->crud->resultSet();
			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	/**
	 * Get all projects
	 * @param $companyId
	 * @return mixed
	 */
	public function getAllProjectsByCompanyId($companyId)
	{
		try {
			$this->crud->query("SELECT * FROM project_infos WHERE company_id = :id");
			$this->crud->bind(":id", $companyId);
			$rows = $this->crud->resultSet();
			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function updateRequestStatus($request_id, $status_id, $userFullName)
	{
		// echo "UPDATE request_infos SET status_id = $status_id,closed_by = $userFullName WHERE id = $request_id";
		try {
			$this->crud->query("UPDATE request_infos SET status_id = :status_id,closed_by = :closed_by WHERE id = :request_id");
			$this->crud->bind(":request_id", $request_id);
			$this->crud->bind(":status_id", $status_id);
			$this->crud->bind(":closed_by", $userFullName);
			$this->crud->execute();
			// die("updated");
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getRoomsByBuildingUnit($buildingUnit)
	{
		try {
			$this->crud->query("SELECT * FROM rooms WHERE building_unit = :building_unit");
			$this->crud->bind(":building_unit", $buildingUnit);
			$rows = $this->crud->resultSet();
			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function getBulletin($secret, $fid)
	{
		try {
			$sql = sprintf("SELECT attachment FROM bulletins WHERE MD5(CONCAT(ID, '%s')) = '%s'", $secret, $fid);
			// echo $sql;
			$this->crud->query($sql);
			$rows = $this->crud->resultField();

			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function insertHistory($user_id, $history_type_id, $table_id, $user_email, $subject, $file_name, $comments, $client_data = "", $smtp_log = "")
	{
		try {
			$this->crud->query("insert into history (user_id,history_type_id,table_id,email,subject,file_name,comments,client_data,smtp_log,open_datetime) VALUES (:user_id,:history_type_id,:table_id,:user_email,:subject,:file_name,:comments,:client_data,:smtp_log,:open_datetime)");
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":history_type_id", $history_type_id);
			$this->crud->bind(":table_id", $table_id);
			$this->crud->bind(":user_email", $user_email);
			$this->crud->bind(":subject", $subject);
			$this->crud->bind(":file_name", $file_name);
			$this->crud->bind(":comments", $comments);
			$this->crud->bind(":client_data", $client_data);
			$this->crud->bind(":smtp_log", $smtp_log);
			//   $this->crud->bind(":server_ip",$_SERVER['SERVER_ADDR']);
			$this->crud->bind(":open_datetime", date('Y-m-d H:i:s'));
			$this->crud->execute();
			$count = $this->crud->rowCount();
			if (in_array($history_type_id, [9, 10, 11, 12, 13])) { // Renewal Email is opened
				$tenant_id = $user_id;
				$lease_id = $table_id;
				$sqlupdate = "UPDATE lease_infos SET renewal_notice_date=CURDATE(), renewal_notice_tenant_id=$tenant_id ,comments=CONCAT(comments,'- Renewal Notice Date automatically added when it is viewed')
			WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND id=$lease_id";
				$this->crud->query($sqlupdate);
				$this->crud->execute();

				$sqlupdate = "UPDATE lease_renewal_notice SET renewal_notice_date=CURDATE(), last_day_renewal=DATE_ADD(CURDATE(), INTERVAL 30 DAY)
			WHERE (renewal_notice_date IS NULL OR renewal_notice_date='0000-00-00') AND lease_id=$lease_id"; // and tenant_id=$tenant_id ; update for all tenants
				$this->crud->query($sqlupdate);
				$this->crud->execute();
			}
			return $count;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}


	public function updateHistory($history_id, $recipients_no, $delivered, $opened, $downloaded)
	{
		try {
			$sql = "update history set history_id=$history_id, ";
			if ($recipients_no != 0) {
				$sql .= "recipients_no=recipients_no+$recipients_no, ";
			}
			if ($delivered != 0) {
				$sql .= "delivered=delivered+$delivered, ";
			}
			if ($opened != 0) {
				$sql .= "opened=opened+$opened, ";
			}
			if ($downloaded != 0) {
				$sql .= "downloaded=downloaded+$downloaded, ";
			}
			$sql .= "last_activity='" . date('Y-m-d H:i:s') . "'";
			$this->crud->query($sql);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function getBulletinInfo($history_id)
	{
		try {
			$sql = "SELECT DISTINCT BU.id as bulletin_id, BU.server_ip, BU.employee_id as sender_id, HI.smtp_log, HI.user_id, BU.message_body, BU.checksum ,EM.full_name as employee_full_name, EM.email as employee_email, TI.full_name as tenant_full_name, TI.email as tenant_email, BU.create_time, BU.message_title, BU.attachment FROM bulletins BU left JOIN employee_infos EM on EM.employee_id = BU.employee_id LEFT JOIN history HI ON BU.id=HI.table_id LEFT JOIN tenant_infos TI on HI.user_id=TI.tenant_id  WHERE HI.id=$history_id";
			//  echo "$sql<br>";
			$this->crud->query($sql);
			$rows = $this->crud->resultSet();

			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function getTrackingEmailInfo($bulletin_id)
	{
		try {
			$sql = "SELECT DISTINCT * FROM history HI LEFT JOIN bulletins BI ON HI.table_id=BI.id WHERE table_id=$bulletin_id order by open_datetime";
			//     echo "$sql<br>";
			$this->crud->query($sql);
			$rows = $this->crud->resultSet();

			return $rows;
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}
}
