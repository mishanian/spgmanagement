<?php

class Tenant
{

	private $crud;

	public function __construct($DB_con)
	{
		$this->crud = new Crud($DB_con);
	}

	public function getTenantName($id)
	{
		return $this->getTenantInfo($id)['full_name'];
	}

	/**
	 * Modified by Mehran : get_tenant_bulletin
	 * @param int $id
	 * @return Array
	 */
	public function getTenantInfo($id)
	{
		try {
			$this->crud->query("SELECT * FROM tenant_infos WHERE tenant_id=:id");
			$this->crud->bind(':id', $id);
			$row = $this->crud->resultSingle();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getTenantEmail($id)
	{
		return $this->getTenantInfo($id)['email'];
	}

	public function getTenantPhone($id)
	{
		return $this->getTenantInfo($id)['mobile'];
	}

	public function addTenantInfo($id, $full_name, $status_id, $active_status, $delinquent, $date_of_birth, $sex, $language, $home_number, $mobile_number, $work_number, $work_number_ext, $email, $home_address, $country_id, $comment, $username, $userpass, $employee_id, $owner_id)
	{
		try {
			$this->crud->query("INSERT INTO tenant_infos(id, full_name, status_id, active_status, delinquent, date_of_birth, sex, language, home_number, mobile_number, work_number, work_number_ext, email, home_address, country_id, comment, username, userpass, employee_id, owner_id)
                                      VALUES (:id, :full_name, :status_id, :active_status, :delinquent, :date_of_birth, :sex, :language, :home_number, :mobile_number, :work_number, :work_number_ext, :email, :home_address, :country_id, :comment, :username, :userpass, :employee_id, :owner_id)");

			$this->crud->bind(':id', $id);
			$this->crud->bind(':status_id', $status_id);
			$this->crud->bind(':full_name', $full_name);
			$this->crud->bind(':active_status', $active_status);
			$this->crud->bind(':delinquent', $delinquent);
			$this->crud->bind(':date_of_birth', $date_of_birth);
			$this->crud->bind(':sex', $sex);
			$this->crud->bind(':language', $language);
			$this->crud->bind(':home_number', $home_number);
			$this->crud->bind(':mobile_number', $mobile_number);
			$this->crud->bind(':work_number', $work_number);
			$this->crud->bind(':work_number_ext', $work_number_ext);
			$this->crud->bind(':email', $email);
			$this->crud->bind(':home_address', $home_address);
			$this->crud->bind(':country_id', $country_id);
			$this->crud->bind(':comment', $comment);
			$this->crud->bind(':username', $username);
			$this->crud->bind(':userpass', $userpass);
			$this->crud->bind(':employee_id', $employee_id);
			$this->crud->bind(':owner_id', $owner_id);

			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function editTenantInfo($id, $full_name, $status_id, $active_status, $delinquent, $date_of_birth, $sex, $language, $home_number, $mobile_number, $work_number, $work_number_ext, $email, $home_address, $country_id, $comment, $username, $userpass, $employee_id, $owner_id)
	{
		$this->crud->query("UPDATE tenant_infos
                                  SET    full_name=:full_name,        status_id=:status_id,             active_status=:active_status,
                                         delinquent=:delinquent,      date_of_birth=:date_of_birth,     sex=:sex,
                                         language=:language,          home_number=:home_number,         mobile_number=:mobile_number,
                                         work_number=:work_number,    work_number_ext=:work_number_ext, email=:email,
                                         home_address=:home_address,  country_id=:country_id,           comment=:comment,
                                         username=:username,          userpass=:userpass,               employee_id=:employee_id,
                                         owner_id=:owner_id
                                  WHERE  id = :id");

		$this->crud->bind(':id', $id);
		$this->crud->bind(':status_id', $status_id);
		$this->crud->bind(':full_name', $full_name);
		$this->crud->bind(':active_status', $active_status);
		$this->crud->bind(':delinquent', $delinquent);
		$this->crud->bind(':date_of_birth', $date_of_birth);
		$this->crud->bind(':sex', $sex);
		$this->crud->bind(':language', $language);
		$this->crud->bind(':home_number', $home_number);
		$this->crud->bind(':mobile_number', $mobile_number);
		$this->crud->bind(':work_number', $work_number);
		$this->crud->bind(':work_number_ext', $work_number_ext);
		$this->crud->bind(':email', $email);
		$this->crud->bind(':home_address', $home_address);
		$this->crud->bind(':country_id', $country_id);
		$this->crud->bind(':comment', $comment);
		$this->crud->bind(':username', $username);
		$this->crud->bind(':userpass', $userpass);
		$this->crud->bind(':employee_id', $employee_id);
		$this->crud->bind(':owner_id', $owner_id);

		$this->crud->execute();
	}

	public function deleteTenantInfo($id)
	{
		try {
			$this->crud->query("DELETE FROM tenant_infos WHERE id = :id");
			$this->crud->bind(':id', $id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//-------------------for tenant portal------------------------

	public function get_building_info_tenant($unit_id)
	{


		//SELECT * FROM building_infos WHERE building_infos.building_id=(SELECT building_id FROM apartment_infos WHERE apartment_id=(SELECT apartment_id FROM lease_infos WHERE find_in_set(:user_id, lease_infos.tenant_ids) AND lease_status_id IN (1,7,8,9)))

		try {
			$this->crud->query("SELECT * FROM building_infos WHERE building_id = (SELECT building_id FROM apartment_infos WHERE apartment_id = :unit_id)");
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_issues_for_tenant_unit($user_id, $unit_id)
	{
		try {
			$this->crud->query("SELECT request_infos.id,request_types.name AS request_type,request_status.name as request_status,message,entry_datetime AS created_time, MAX(request_assignees.last_access_time) as last_access_time,
                                        (if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,21,22),'closed','open')) AS issue_status,
                                        (SELECT issue_past_after_days FROM building_infos WHERE building_infos.building_id = request_infos.building_id) AS issue_past_after_days,
                                        (ifnull((SELECT entry_date FROM request_communications WHERE request_id = request_infos.id   ORDER BY entry_date DESC limit 1),entry_datetime )) AS last_update_time
                                        FROM request_infos,request_types, request_status,request_assignees WHERE request_infos.id IN ( SELECT request_id  FROM request_assignees WHERE user_id = :user_id)
                                        AND request_types.id = request_infos.request_type_id
                                        AND request_status.id = request_infos.status_id
                                        AND request_infos.apartment_id = :unit_id
                                        AND request_assignees.request_id=request_infos.id AND request_assignees.user_id = :user_id group BY request_assignees.request_id,request_assignees.user_id
                                        ORDER BY request_infos.id DESC"); //AND if_seen_by_tenant = 1
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_lease_payments($user_id, $unit_id)
	{
		try {
			$this->crud->query("SELECT lease_payment_id,due_date,total_discount,paid,outstanding FROM rental_payments WHERE find_in_set(:user_id,tenant_ids) AND rental_payments.lease_status_id IN (1,2,7,8,9) AND apartment_id = :unit_id order by due_date");
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_deposit_amount($lease_id)
	{
		try {
			$this->crud->query("SELECT amount,paid_date,comments FROM deposits WHERE lease_id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_lease_payments_wid($user_id, $unit_id, $lease_id)
	{
		try {
			$sql = "SELECT lease_payment_id,due_date,total_discount,paid,outstanding,invoice_type_id,tenant_comments FROM rental_payments WHERE find_in_set(:user_id,tenant_ids)  AND apartment_id = :unit_id and lease_id= :lease_id order by due_date";
			//echo $sql." $user_id, $unit_id,$lease_id";
			$this->crud->query($sql);
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			$this->crud->bind(":lease_id", $lease_id);

			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getInvoiceTypes()
	{
		try {
			$sql = "SELECT * FROM invoice_types";
			//echo $sql." $user_id, $unit_id,$lease_id";
			$this->crud->query($sql);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_document_type($document_category_id)
	{

		try {
			$this->crud->query("SELECT * FROM document_category WHERE id=$document_category_id");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_documents($document_category_id, $field_table, $id)
	{

		try {
			//   die("SELECT file FROM attachment_infos WHERE document_category_id=$document_category_id and $field_table=$id");
			$this->crud->query("SELECT file FROM attachment_infos WHERE document_category_id=$document_category_id and $field_table=$id");

			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_apartment_documents($unit_id)
	{
		try {
			$this->crud->query("SELECT apartment_documents.file AS apartment_file, (SELECT document_types.name FROM document_types WHERE document_types.id=apartment_documents.document_type_id) AS apartment_file_type FROM apartment_documents  WHERE apartment_documents.apartment_id = :unit_id  and show_id=1");
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_lease_documents($user_id, $unit_id)
	{
		try {
			$this->crud->query("SELECT lease_documents.file AS lease_file, (SELECT document_types.name FROM document_types WHERE document_types.id=lease_documents.document_type_id) AS lease_file_type FROM lease_documents WHERE lease_documents.lease_id=(SELECT id FROM lease_infos WHERE find_in_set(:user_id,tenant_ids) AND lease_status_id IN (1,7,8,9) AND apartment_id = :unit_id)  and show_id=1");
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenant_documents($user_id)
	{
		try {
			$this->crud->query("SELECT tenant_documents.file AS tenant_file, (SELECT document_types.name FROM document_types WHERE document_types.id=tenant_documents.document_type_id) AS tenant_file_type FROM tenant_documents WHERE tenant_documents.tenant_id=:user_id and show_id=1");
			$this->crud->bind(":user_id", $user_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_fire_escape_plan($unit_id)
	{
		try {
			$this->crud->query("SELECT fireescape_plan FROM floor_infos WHERE floor_id = (SELECT floor_id FROM apartment_infos WHERE apartment_id = :unit_id)");
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultField();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function get_appliances($unit_id, $appliance_type_id)
	{
		try {
			$this->crud->query("SELECT AT.name , AP.information from appliance_infos AP left join appliance_types AT on AP.appliance_type_id=AT.id where apartment_id=$unit_id and AP.appliance_type_id=$appliance_type_id");
			//    die("SELECT AT.name , AP.information from appliance_infos AP left join appliance_types AT on AP.appliance_type_id=AT.id where apartment_id=$unit_id and AP.appliance_type_id=$appliance_type_id");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenant_bulletin($user_id, $unit_id)
	{
		// echo ("$user_id, $unit_id");
		try {
			$sql = "SELECT * FROM bulletins WHERE issue_from<=CURDATE() AND issue_to>=CURDATE() AND is_active=1 AND  find_in_set(:unit_id, apartment_ids) AND building_id = (SELECT DISTINCT building_id FROM lease_infos WHERE find_in_set(:user_id,tenant_ids) AND lease_status_id IN (1,2,7,8,9,10) AND apartment_id = :unit_id) 
			AND issue_from <= (SELECT distinct MAX(start_date) FROM lease_infos WHERE FIND_IN_SET(:user_id, tenant_ids) AND lease_status_id IN (1,2,7,8,9,10) AND apartment_id = :unit_id)
			AND issue_to >= (SELECT distinct MIN(end_date) FROM lease_infos WHERE FIND_IN_SET(:user_id, tenant_ids) AND lease_status_id IN (1,2,7,8,9,10) AND apartment_id = :unit_id)
			order by create_time desc";
			// echo $sql;
			$this->crud->query($sql);
			//echo("SELECT * FROM bulletins WHERE building_id = (SELECT building_id FROM lease_infos WHERE find_in_set($user_id,tenant_ids) AND lease_status_id IN (1,2,7,8,9) AND apartment_id = $unit_id)");
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":unit_id", $unit_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function record_login_time($tenant_id)
	{
		try {
			$current_time = date('Y-m-d H:i:s');

			$this->crud->query("UPDATE tenant_infos SET last_login_time = :current_time_stamp WHERE tenant_id = :tenant_id");
			$this->crud->bind(":current_time_stamp", $current_time);
			$this->crud->bind(":tenant_id", $tenant_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_tenant_settings_about_request($tenant_id)
	{
		try {
			$this->crud->query("SELECT allow_create_request,view_past_issues FROM tenant_infos WHERE tenant_id = :tenant_id");
			$this->crud->bind(":tenant_id", $tenant_id);

			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 *
	 * @param type $employeeId
	 * @param type $tenantCount - If total tenant count is needed
	 * @param type $employee_admin
	 * @return type
	 */
	public function getTenantSignUpDetails($companyId, $employeeId, $tenantCount = false, $employee_admin = false)
	{
		try {
			$default_login_time = "0000-00-00 00:00:00";
			if ($tenantCount) { // If the total number of tenants is requested
				if ($employee_admin) {
					$this->crud->query("SELECT * FROM tenant_infos where company_id = :company_id");
				} else {
					$this->crud->query("SELECT * FROM tenant_infos WHERE employee_id = :employee_id and company_id = :company_id");
					$this->crud->bind(":employee_id", $employeeId);
				}
			} else { // Tenants only who have logged in
				if ($employee_admin) {
					$this->crud->query("SELECT * FROM tenant_infos WHERE last_login_time != :default_login and company_id = :company_id");
					$this->crud->bind(":default_login", $default_login_time);
				} else {
					$this->crud->query("SELECT * FROM tenant_infos WHERE last_login_time != :default_login AND employee_id = :employee_id and company_id = :company_id");
					$this->crud->bind(":employee_id", $employeeId);
					$this->crud->bind(":default_login", $default_login_time);
				}
			}
			$this->crud->bind(":company_id", $companyId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Fetches the issue details
	 * @param int $employeeId
	 * @return array
	 */
	public function getTenantRequestDetails($companyId, $employeeId = null)
	{
		try {
			$companyIdQuery = "select "
				. "request_infos.entry_datetime as "
				. "request_timestamp,request_assignees.id as assignee_table_id,"
				. "request_infos.id as request_info_table_id,"
				. "request_infos.employee_id,"
				. "request_assignees.user_id as assigned_to,"
				. "request_infos.message as issue_detail,"
				. "request_infos.request_category as category,"
				. "request_infos.request_type_id as type,"
				. "request_infos.location as location,"
				. "request_infos.apartment_id as apt_id "
				. "from "
				. "request_assignees "
				. "join request_infos on request_assignees.request_id = request_infos.id "
				. "where request_assignees.request_id "
				. "in "
				. "(select id from request_infos "
				. "where building_id "
				. "in "
				. "(select building_id "
				. "from building_infos "
				. "where "
				. "company_id = :company_id ";

			if ($employeeId) {
				$employeeIdQuery = "and employee_id = :employee_id ";
				$companyIdQuery  = $companyIdQuery . $employeeIdQuery;
			}

			$query = $companyIdQuery . " ) and location = 2 )";

			$this->crud->query($query);

			if ($employeeId) {
				$this->crud->bind(':employee_id', $employeeId);
			}

			$this->crud->bind(':company_id', $companyId);
			$rows = $this->crud->resultSet();
			return $rows;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getTenantRequestDetailsByAptId($apartmentId)
	{
		try {
			$query = "select "
				. "request_infos.entry_datetime as request_timestamp,"
				. "request_infos.id as request_info_table_id,"
				. "request_infos.employee_id,"
				. "request_infos.message as issue_detail,"
				. "request_infos.request_category as category,"
				. "request_infos.request_type_id as type,"
				. "request_infos.location as location,"
				. "request_infos.apartment_id as apt_id FROM request_infos "
				. "WHERE "
				. "request_infos.apartment_id = :apartment_id";

			$this->crud->query($query);
			$this->crud->bind(':apartment_id', $apartmentId);
			$rows = $this->crud->resultSet();
			return $rows;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getRequestType($typeId)
	{
		try {
			$this->crud->query("SELECT * FROM request_types WHERE id= :request_type_id");
			$this->crud->bind(":request_type_id", $typeId);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getTenantForLeaseId($lease_id)
	{
		try {
			$this->crud->query("SELECT tenant_ids FROM lease_infos WHERE id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultArray();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getTenantViewByBid($bid)
	{
		try {
			$this->crud->query("SELECT * FROM view_tenant_infos WHERE building_id= :bid");
			$this->crud->bind(":bid", $bid);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getTenantViewByLeaseId($lease_id)
	{
		try {
			$this->crud->query("SELECT * FROM view_tenant_infos WHERE lease_id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getTenantViewByAptId($apartment_id)
	{
		try {
			$this->crud->query("SELECT * FROM view_tenant_infos WHERE apartment_id = :apartment_id");
			$this->crud->bind(":apartment_id", $apartment_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Get all the tenants in the company
	public function getTenantsByCompany($companyId)
	{
		try {
			$this->crud->query("SELECT * FROM view_tenant_infos WHERE company_id = :cid");
			$this->crud->bind(":cid", $companyId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Get all the tenants in the company
	public function getTenantsForStandalonePage($companyId, $unitId, $buildingId)
	{
		try {
			$this->crud->query("SELECT * FROM view_tenant_infos WHERE company_id = :cid AND building_id = :building_id AND unit_number = :unit_id");
			$this->crud->bind(":cid", $companyId);
			$this->crud->bind(":building_id", $buildingId);
			$this->crud->bind(":unit_id", $unitId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getPotentialCreditChecklist($potentialId, $visitQuestion)
	{
		try {
			$this->crud->query("SELECT * FROM potential_credit_checklist WHERE potential_id = :potential_id AND visit_or_question = :visit_or_question");
			$this->crud->bind(":potential_id", $potentialId);
			$this->crud->bind(":visit_or_question", $visitQuestion);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function insertCreditChecklist($data, $potentialId, $visitQuestion)
	{
		try {
			$this->crud->query("INSERT INTO potential_credit_checklist(potential_id,visit_or_question,previous_landlord,regiedulogement,payslip,guarantor,bankstatement,voidcheck,idphoto,data) VALUES(:potential_id,:visit_or_question,:previous_landlord,:regiedulogement,:payslip,:guarantor,:bankstatement,:voidcheck,:idphoto,:data)");
			$this->crud->bind(":potential_id", $potentialId);
			$this->crud->bind(":visit_or_question", $visitQuestion);
			$this->crud->bind(":previous_landlord", $data["previous_landlord"]);
			$this->crud->bind(":regiedulogement", $data["regiedulogement"]);
			$this->crud->bind(":payslip", $data["payslip"]);
			$this->crud->bind(":guarantor", $data["guarantor"]);
			$this->crud->bind(":bankstatement", $data["bankstatement"]);
			$this->crud->bind(":voidcheck", $data["voidcheck"]);
			$this->crud->bind(":idphoto", $data["idphoto"]);
			$this->crud->bind(":data", json_encode($data));
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateCreditChecklist($data, $potentialId, $visitQuestion)
	{
		try {
			$this->crud->query("UPDATE potential_credit_checklist SET previous_landlord = :previous_landlord,regiedulogement = :regiedulogement,payslip = :payslip ,guarantor = :guarantor ,bankstatement = :bankstatement,voidcheck = :voidcheck ,idphoto = :idphoto ,data  = :data WHERE potential_id = :potential_id AND visit_or_question = :visit_or_question");
			$this->crud->bind(":potential_id", $potentialId);
			$this->crud->bind(":visit_or_question", $visitQuestion);
			$this->crud->bind(":previous_landlord", $data["previous_landlord"]);
			$this->crud->bind(":regiedulogement", $data["regiedulogement"]);
			$this->crud->bind(":payslip", $data["payslip"]);
			$this->crud->bind(":guarantor", $data["guarantor"]);
			$this->crud->bind(":bankstatement", $data["bankstatement"]);
			$this->crud->bind(":voidcheck", $data["voidcheck"]);
			$this->crud->bind(":idphoto", $data["idphoto"]);
			$this->crud->bind(":data", json_encode($data));
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/* Update just the data column in the potential_credit_checklist table */
	public function updateCreditChecklistData($data, $potentialId, $visitQuestion)
	{
		try {
			$this->crud->query("UPDATE potential_credit_checklist SET data  = :data WHERE potential_id = :potential_id AND visit_or_question = :visit_or_question");
			$this->crud->bind(":data", json_encode($data));
			$this->crud->bind(":potential_id", $potentialId);
			$this->crud->bind(":visit_or_question", $visitQuestion);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function deactivateTenant($id)
	{
		try {
			$this->crud->query("UPDATE tenant_infos SET active_status = 0 WHERE tenant_id=:id");
			$this->crud->bind(':id', $id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}