<?php
class LeasePayment
{

	private $crud;

	public function __construct($DB_con)
	{
		$this->crud = new Crud($DB_con);
	}


	/**
	 * Get the Full lease information by Lease Payment ID
	 * @param int $lease_payment_id
	 * @return array
	 */
	public function get_lease_info_by_lease_payment_id($lease_payment_id)
	{
		try {
			$this->crud->query("SELECT rental_payments.lease_id AS 'lease_id',rental_payments.company_id AS 'company_id',rental_payments.employee_id AS 'employee_id',rental_payments.building_id AS 'building_id',rental_payments.unit_number AS 'unit',rental_payments.total AS 'lease_payment_amount',rental_payments.total_discount AS 'total_discount',rental_payments.outstanding AS 'outstanding',rental_payments.due_date AS 'payment_due' FROM rental_payments WHERE rental_payments.lease_payment_id = :lease_payment_id");
			$this->crud->bind(":lease_payment_id", $lease_payment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_lease_info_by_lease_payment_detail_id($lease_payment_detail_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_infos LI
			LEFT JOIN lease_payments LP ON LI.id=LP.lease_id
			 LEFT JOIN lease_payment_details LPD ON LP.id=LPD.lease_payment_id
			 WHERE LPD.id= :lease_payment_detail_id");
			$this->crud->bind(":lease_payment_detail_id", $lease_payment_detail_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getLeasePaymentInfo($lease_payment_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payments WHERE $lease_payment_id=:lease_payment_id");
			$this->crud->bind(':lease_payment_id', $lease_payment_id);
			$row = $this->crud->resultSingle();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Get Payment data for the building ID
	 * @param $buildingID - ID of the building from the building_infos table
	 * @param $previousDay - Previous date formatted
	 * @param $selectedDay -  Current date formatted
	 */
	public function getPaymentsData($buildingId, $selectedDate, $previousDate)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payment_details WHERE lease_payment_id IN (SELECT id FROM lease_payments WHERE lease_id IN (SELECT id FROM lease_infos WHERE building_id = :building_id)) AND payment_date <= :day1 AND payment_date >= :day0");
			$this->crud->bind(":day1", $selectedDate);
			$this->crud->bind(":day0", $previousDate);
			$this->crud->bind(":building_id", $buildingId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Returns the payment method detail
	 * @param int $paymentMethodId
	 * @return row
	 */
	public function getPaymentMethod($paymentMethodId)
	{
		try {
			$this->crud->query("SELECT * FROM payment_methods WHERE id = :methodId");
			$this->crud->bind(":methodId", $paymentMethodId);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getPaymentType($paymentTypeId)
	{
		try {
			$this->crud->query("SELECT * FROM payment_types  WHERE id = :typeId");
			$this->crud->bind(":typeId", $paymentTypeId);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 *
	 * @param int $lease_payment_id
	 * @return JSON
	 * Return all the data from lease payments table
	 */
	public function get_payment_info_by_lease_payment_id($lease_payment_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payments WHERE id = :lease_payment_id");
			$this->crud->bind(":lease_payment_id", $lease_payment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Get all the payments from the table lease_payment_details between the selected date and the previous dates
	 * @param datetime $selectedDate
	 * @param datetime $previousDate
	 */
	public function getAllPayments($selectedDate, $previousDate)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payment_details WHERE payment_date <= :day1 AND payment_date >= :day0");
			$this->crud->bind(":day1", $selectedDate);
			$this->crud->bind(":day0", $previousDate);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 *
	 * @param type $companyId
	 * @param int $employeeId
	 * @return array
	 */
	public function getAllPaymentsByCompanyId($companyId, $employeeId = false)
	{
		try {
			$queryCompanyId = "select "
				. "lease_payments.id as payment_id,"
				. "lease_payment_details.payment_type_id as type,"
				. "lease_payment_details.payment_method_id as method,"
				. "lease_payment_details.entry_datetime as timestamp,"
				. "lease_payments.due_date,"
				. "lease_payment_details.paid_amount as amount, "
				. "lease_payment_details.payment_detail,"
				. "lease_payment_details.comments,"
				. "lease_infos.tenant_ids"
				. " from "
				. "lease_payments "
				. "join lease_payment_details "
				. "on "
				. "lease_payment_details.lease_payment_id = lease_payments.id "
				. "join lease_infos "
				. "on "
				. "lease_infos.id = lease_payments.lease_id "
				. "where "
				. "lease_payments.lease_id "
				. "in "
				. "(select id from lease_infos where ";

			if ($employeeId) {
				$queryCompanyId .= "employee_id = :employee_id and ";
			}

			$queryCompanyId .= "company_id = :company_id) ";

			$this->crud->query($queryCompanyId);

			if ($employeeId) {
				$this->crud->bind(":employee_id", $employeeId);
			}

			$this->crud->bind(":company_id", $companyId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getAllPaymentsByLeaseId($leaseId)
	{
		try {
			$query = "select "
				. "lease_payments.id as payment_id,"
				. "lease_payment_details.payment_type_id as type,"
				. "lease_payment_details.payment_method_id as method,"
				. "lease_payment_details.entry_datetime as timestamp,"
				. "lease_payments.due_date,"
				. "lease_payment_details.paid_amount as amount,"
				. "lease_payments.paid_amount AS ls_paid_amount,"
				. "lease_payment_details.payment_detail,"
				. "lease_payment_details.comments,"
				. "lease_infos.tenant_ids"
				. " from "
				. "lease_payments "
				. "join lease_payment_details "
				. "on "
				. "lease_payment_details.lease_payment_id = lease_payments.id "
				. "join lease_infos "
				. "on "
				. "lease_infos.id = lease_payments.lease_id "
				. "where "
				. "lease_payments.lease_id = :lease_id";

			$this->crud->query($query);
			$this->crud->bind(":lease_id", $leaseId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Get all the payments by company Id to show in the deposit page
	 * These values are sorted by Due date and fetched according to the is_deposited column.
	 * @param type $companyId
	 * @param int $employeeId
	 * @return array
	 */

	public function getAllPaymentsByCompanyIdDeposit($buildingId, $companyId, $employeeId = false)
	{
		try {
			if ($buildingId) {
				$queryBuildingId = "and building_id = :building_id ";
			} else {
				$queryBuildingId = "";
			}


			$queryCompanyId = "select "
				. "lease_payments.id as payment_id,"
				. "lease_infos.building_id AS building_id,"
				. "lease_payment_details.id as payment_details_id,"
				. "lease_payment_details.payment_type_id as type,"
				. "lease_payment_details.payment_method_id as method,"
				. "lease_payment_details.entry_datetime as timestamp,"
				. "lease_payment_details.payment_date as payment_date,"
				. "lease_payments.due_date,"
				. "lease_payments.lease_id,"
				. "lease_payment_details.paid_amount as amount, "
				. "lease_payment_details.amount as subtotalamount, "
				. "lease_payment_details.cf_amount as cfamount, "
				. "lease_payment_details.payment_detail,"
				. "lease_payment_details.comments,"
				. "lease_payment_details.is_deposited as is_deposited,"
				. "lease_payment_details.payment_method_id as payment_method "
				. " from "
				. "lease_payments "
				. "join lease_payment_details "
				. "on "
				. "lease_payment_details.lease_payment_id = lease_payments.id "
				. "join lease_infos "
				. "on "
				. "lease_payments.lease_id = lease_infos.id "
				. "where "
				. "is_deposited = 0 and is_paid = 1 and lease_payment_details.payment_type_id != 2 and lease_payment_details.payment_method_id in (3,4,6) $queryBuildingId and "
				. "lease_payments.lease_id "
				. "in "
				. "(select id from lease_infos where ";

			if ($employeeId) {
				$queryCompanyId .= "employee_id = :employee_id and ";
			}

			$queryCompanyId .= "company_id = :company_id) ";

			$queryCompanyId .= " order by payment_date DESC";

			$this->crud->query($queryCompanyId);

			if ($employeeId) {
				$this->crud->bind(":employee_id", $employeeId);
			}

			$this->crud->bind(":company_id", $companyId);
			if ($buildingId) {
				$this->crud->bind(":building_id", $buildingId);
			}
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getAllPaymentsByBuildingIds($buildingIdArray, $companyId, $employeeId = false)
	{
		try {
			$queryCompanyId = "select "
				. "lease_payments.id as payment_id,"
				. "lease_infos.building_id AS building_id,"
				. "lease_payment_details.id as payment_details_id,"
				. "lease_payment_details.payment_type_id as type,"
				. "lease_payment_details.payment_method_id as method,"
				. "lease_payment_details.entry_datetime as timestamp,"
				. "lease_payment_details.payment_date as payment_date,"
				. "lease_payments.due_date,"
				. "lease_payments.lease_id,"
				. "lease_payment_details.paid_amount as amount, "
				. "lease_payment_details.amount as subtotalamount, "
				. "lease_payment_details.cf_amount as cfamount, "
				. "lease_payment_details.payment_detail,"
				. "lease_payment_details.comments,"
				. "lease_payment_details.is_deposited as is_deposited,"
				. "lease_payment_details.payment_method_id as payment_method "
				. " from "
				. "lease_payments "
				. "join lease_payment_details "
				. "on "
				. "lease_payment_details.lease_payment_id = lease_payments.id "
				. "join lease_infos "
				. "on "
				. "lease_payments.lease_id = lease_infos.id "
				. "where "
				. "is_deposited = 0 and is_paid = 1 and lease_payment_details.payment_type_id != 2 and lease_payment_details.payment_method_id in (3,4,6) and building_id in (" . implode(',', $buildingIdArray) . ") and "
				. "lease_payments.lease_id "
				. "in "
				. "(select id from lease_infos where ";

			if ($employeeId) {
				$queryCompanyId .= "employee_id = :employee_id and ";
			}

			$queryCompanyId .= "company_id = :company_id) ";

			$queryCompanyId .= " order by payment_date DESC";

			$this->crud->query($queryCompanyId);

			if ($employeeId) {
				$this->crud->bind(":employee_id", $employeeId);
			}

			$this->crud->bind(":company_id", $companyId);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getLeasePaymentsByLeaseId($lease_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payments WHERE lease_id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function updatePaymentComment($leasePaymentId, $comment)
	{
		try {
			$this->crud->query("UPDATE lease_payments set comments = :comment WHERE id = :lease_payment_id");
			$this->crud->bind(":lease_payment_id", $leasePaymentId);
			$this->crud->bind(":comment", $comment);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function addLatePaymentRecord($latePaymentDetails)
	{
		try {
			$this->crud->query("INSERT INTO lease_payments(lease_id,invoice_type_id,due_date,lease_amount,total,outstanding,payment_status_id,comments) VALUES(:lease_id,:invoice_type_id,:due_date,:lease_amount,:total,:outstanding,:payment_status_id,:comments)");
			$this->crud->bind(":lease_id", $latePaymentDetails["lease_id"]);
			$this->crud->bind(":invoice_type_id", 1);
			$this->crud->bind(":due_date", $latePaymentDetails["due_date"]);
			$this->crud->bind(":lease_amount", $latePaymentDetails["lease_amount"]);
			$this->crud->bind(":total", $latePaymentDetails["total"]);
			$this->crud->bind(":outstanding", $latePaymentDetails["outstanding"]);
			$this->crud->bind(":payment_status_id", 1);
			$this->crud->bind(":comments", $latePaymentDetails["comments"]);

			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function checkIfLatePaymentRecordExists($lease_id)
	{
		try {
			$this->crud->query("SELECT COUNT(id) as count_id FROM lease_payments WHERE lease_amount = 30 AND MONTH(due_date) = MONTH(CURRENT_DATE()) AND YEAR(due_date) = YEAR(CURRENT_DATE()) AND lease_id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function checkIfLateAmountAlreadyApplied($lease_id)
	{
		try {
			$this->crud->query("SELECT late_amount FROM lease_payments WHERE MONTH(due_date) = MONTH(CURRENT_DATE()) AND YEAR(due_date) = YEAR(CURRENT_DATE()) AND lease_id = :lease_id");
			$this->crud->bind(":lease_id", $lease_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param $paymentId - Lease payment details ID ( table : lease_payment_details )
	 * @return string
	 */
	public function updateDepositedRecord($paymentId, $depositBy, $depositDate)
	{
		try {
			$this->crud->query("UPDATE lease_payment_details set is_deposited = 1,deposit_date=:deposit_date,deposit_by=:deposit_by WHERE id = :payment_id");
			$this->crud->bind(":payment_id", $paymentId);
			$this->crud->bind(":deposit_by", $depositBy);
			$this->crud->bind(":deposit_date", $depositDate);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function updateLateAmount($data)
	{
		try {
			$this->crud->query("UPDATE lease_payments set late_amount = :late_amount, comments =:comments, outstanding = lease_amount - paid_amount - discount + :late_amount_add  WHERE id = :payment_id");
			$this->crud->bind(":payment_id", $data["lp_id"]);
			$this->crud->bind(":late_amount", $data["lease_amount"]);
			$this->crud->bind(":late_amount_add", $data["lease_amount"]);
			$this->crud->bind(":comments", $data["comments"]);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param $depositData
	 * @return string
	 */
	public function createDepositRecord($depositData)
	{
		$depositDate = date("Y-m-d");
		try {
			$this->crud->query("INSERT INTO deposit_infos(deposit_data,deposit_date) VALUES(:deposit_data,:deposit_date)");
			$this->crud->bind(":deposit_data", $depositData); // Data
			$this->crud->bind(":deposit_date", $depositDate); // Date of Deposit
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function updateDepositIdLpItems($depositInfosId, $lp_ids)
	{
		try {
			$this->crud->query("UPDATE lease_payment_details set deposit_id = :deposit_id WHERE id in (" . implode(",", $lp_ids) . ")");
			$this->crud->bind(":deposit_id", $depositInfosId);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getAllPaymentRecordsByDepositId($deposit_id)
	{
		try {
			$query = "select "
				. "lease_payments.id as payment_id,"
				. "lease_infos.building_id AS building_id,"
				. "lease_payment_details.id as payment_details_id,"
				. "lease_payment_details.payment_type_id as type,"
				. "lease_payment_details.payment_method_id as method,"
				. "lease_payment_details.entry_datetime as timestamp,"
				. "lease_payment_details.payment_date as payment_date,"
				. "lease_payments.due_date,"
				. "lease_payments.lease_id,"
				. "lease_payment_details.paid_amount as amount, "
				. "lease_payment_details.amount as subtotalamount, "
				. "lease_payment_details.cf_amount as cfamount, "
				. "lease_payment_details.payment_detail,"
				. "lease_payment_details.comments,"
				. "lease_payment_details.is_deposited as is_deposited,"
				. "lease_payment_details.payment_method_id as payment_method "
				. " from "
				. "lease_payments "
				. "join lease_payment_details "
				. "on "
				. "lease_payment_details.lease_payment_id = lease_payments.id "
				. "join lease_infos "
				. "on "
				. "lease_payments.lease_id = lease_infos.id "
				. "where "
				. "deposit_id = :deposit_id";
			$query .= " order by payment_date DESC";

			$this->crud->query($query);
			$this->crud->bind(":deposit_id", $deposit_id);

			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}


	public function getAllDepositRecords($id = false)
	{
		try {
			if ($id) {
				$this->crud->query("SELECT * FROM deposit_infos WHERE id = :record_id");
				$this->crud->bind(":record_id", $id); // Data
				return $this->crud->resultSingle();
			} else {
				$this->crud->query("SELECT * FROM deposit_infos ORDER BY id DESC");
				return $this->crud->resultSet();
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param $depositId
	 * @return string
	 */
	public function getDepositRecord($depositId)
	{
		try {
			$this->crud->query("SELECT * FROM deposit_infos WHERE id = :deposit_id");
			$this->crud->bind(":deposit_id", $depositId);
			$depositData = $this->crud->resultSingle()["deposit_data"];

			$depositData = json_decode($depositData, true);
			$paymentIds  = array();

			foreach ($depositData as $pid => $amount) {
				array_push($paymentIds, $pid);
			}

			$paymentIds = implode(",", $paymentIds);

			$paymentsData = $this->getPaymentsDataForDeposit($paymentIds);

			if ($paymentsData) {
				return $paymentsData;
			}

			return false;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getPaymentsDataForDeposit($paymentdIds)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payment_details WHERE id IN (:paymentIds)");
			$this->crud->bind(":paymentIds", $paymentdIds);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	// lease invoices
	public function addLeaseInvoices($lease_id, $employee_id, $company_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_infos WHERE id IN (:lease_id)");
			$this->crud->bind(":lease_id", $lease_id);
			$leaseDetails = $this->crud->resultSingle();

			$lease_amount = $leaseDetails['lease_amount'];
			$lease_id = $leaseDetails['id'];
			$start_date = date_create($leaseDetails['start_date']);
			$length_of_lease = $leaseDetails['length_of_lease'];
			$start_date = new \DateTime($leaseDetails['start_date']);
			$end_date = new \DateTime($leaseDetails['end_date']);
			$diff = date_diff($start_date, $end_date);
			$diff = $diff->format("%y") * 12 + $diff->format("%m") + 1;
			$payment_per_month = $lease_amount; ///$length_of_lease;
			for ($i = 0; $i < $diff; $i++) {
				$original_start_date = new \DateTime($leaseDetails['start_date']);
				$du_date = $original_start_date->add(new \DateInterval('P' . $i . 'M'));
				$du_date = $du_date->format('Y-m-d');
				if ($i == 0) {
					$invoice_type_id = 1;
				} else {
					$invoice_type_id = 2;
				}
				$query = "insert ignore into lease_payments (lease_id,invoice_type_id,due_date,lease_amount,total,outstanding, employee_id, company_id) values ($lease_id,$invoice_type_id,'$du_date',$payment_per_month,$payment_per_month,$payment_per_month," . $employee_id . "," . $company_id . ")";
				echo $query . "<br>";
				$this->crud->execute();
			}
			include_once("update_tables.php");
			update_tables($lease_id, "lease_id");
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	// Custom invoice - used in standalone payment page
	public function addCustomInvoice($details)
	{
		try {
			$this->crud->query("INSERT INTO lease_payments (lease_id,invoice_type_id,due_date,lease_amount,total,outstanding,payment_status_id,payment_method_id,comments) VALUES(:lease_id,:invoice_type_id,:due_date,:lease_amount,:total,:outstanding,:payment_status_id,:payment_method_id,:comments)");
			$this->crud->bind(":lease_id", $details["lease_id"]);
			$this->crud->bind(":invoice_type_id", 3);
			$this->crud->bind(":due_date", $details["due_date"]);
			$this->crud->bind(":lease_amount", $details["lease_amount"]);
			$this->crud->bind(":total", $details["total"]);
			$this->crud->bind(":outstanding", $details["outstanding"]);
			$this->crud->bind(":payment_status_id", 3); // Has to be "1" - later will make the change
			$this->crud->bind(":payment_method_id", 3);
			$this->crud->bind(":comments", $details["comments"]);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	// Fetch the lease details using tenant id
	public function getLeaseTenantLike($tenantId)
	{
		try {
			$this->crud->query("SELECT * FROM lease_infos WHERE tenant_ids LIKE ? AND lease_status_id IN (1,7,10)");
			$this->crud->bind(1, "%$tenantId%", PDO::PARAM_STR);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function updateLateReminderSent($id)
	{
		try {
			$this->crud->query("UPDATE lease_payments set latereminder_sent = 1 WHERE id = :payment_id");
			$this->crud->bind(":payment_id", $id);
			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/* GET THE deposit data about a lease ID */
	public function getLeaseDeposit($leaseId)
	{
		try {
			$this->crud->query("SELECT lease_id,LI.building_id,LI.apartment_id,deposit_type_id, tenant_id,payment_method_id, payment_type_id, amount, paid_date, DP.comments, DP.employee_id, return_date, return_employee_id, deposit_status  FROM deposits DP LEFT JOIN lease_infos LI ON DP.lease_id=LI.id WHERE lease_id= :lease_id");
			$this->crud->bind(":lease_id", $leaseId); // Data
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function createNewLeaseDeposit($newLeaseId, $depositData)
	{
		try {
			$sql = "INSERT INTO deposits(building_id,apartment_id,tenant_id,payment_method_id,payment_type_id,amount,paid_date,comments,employee_id,return_date,return_employee_id,deposit_status,lease_id,deposit_type_id) VALUES(:building_id,:apartment_id,:tenant_id,:payment_method_id,:payment_type_id,:amount,:paid_date,:comments,:employee_id,:return_date,:return_employee_id,:deposit_status,:lease_id, :deposit_type_id)";
			// echo "newLeaseId=$newLeaseId";
			// var_dump($depositData);
			$this->crud->query($sql);

			$this->crud->bind(":building_id", $depositData["building_id"]); // Data
			$this->crud->bind(":apartment_id", $depositData["apartment_id"]); // Data
			$this->crud->bind(":tenant_id", $depositData["tenant_id"]); // Data
			$this->crud->bind(":payment_method_id", $depositData["payment_method_id"]); // Data
			$this->crud->bind(":payment_type_id", $depositData["payment_type_id"]); // Data
			$this->crud->bind(":amount", $depositData["amount"]); // Data
			$this->crud->bind(":paid_date", $depositData["paid_date"]); // Data
			$this->crud->bind(":comments", $depositData["comments"]); // Data
			$this->crud->bind(":employee_id", $depositData["employee_id"]); // Data
			$this->crud->bind(":return_date", $depositData["return_date"]); // Data
			$this->crud->bind(":return_employee_id", $depositData["return_employee_id"]); // Data
			$this->crud->bind(":deposit_status", $depositData["deposit_status"]); // Data
			$this->crud->bind(":deposit_type_id", $depositData["deposit_type_id"]); // Data
			$this->crud->bind(":lease_id", $newLeaseId); // Data

			$this->crud->execute();
			return $this->crud->rowCount();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
}