<?php
class Vendor
{
	private $crud;

	public function __construct($DB_con)
	{
		$this->crud = new Crud($DB_con);
	}

	public function getVendorName($id)
	{
		if (isset($id)) {
			// echo "id=$id<br>\n";
			$row = $this->getVendorInfo($id);
			if (isset($row['full_name'])) {
				return $row['full_name'];
			} else {
				if (isset($row['company_name'])) {
					return $row['company_name'];
				} else {
					return "";
				}
			}
		}
		return false;
	}

	public function getVendorInfo($id)
	{
		try {
			$this->crud->query("SELECT * FROM vendor_infos WHERE vendor_id=:id");
			$this->crud->bind(':id', $id);
			$row = $this->crud->resultSingle();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorChequeNames($vendor_id, $cheque_name_id)
	{
		try {
			$query = "SELECT name FROM vendor_cheque_names WHERE vendor_id=$vendor_id and id=$cheque_name_id";
			//            echo $query;
			$this->crud->query($query);
			$row = $this->crud->resultField();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getVendorCompany($id)
	{
		try {
			$this->crud->query("SELECT company_id FROM vendor_infos WHERE vendor_id=:id");
			$this->crud->bind(':id', $id);
			$row = $this->crud->resultField();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorOfContract($contract_id)
	{
		try {
			$this->crud->query("SELECT vendor_id FROM contract_infos WHERE contract_id=$contract_id");
			$row = $this->crud->resultField();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getVendorProjects($vendor_id)
	{
		try {
			$this->crud->query("SELECT PI.project_id FROM project_infos PI LEFT JOIN contract_infos CI ON CI.project_id=PI.project_id LEFT JOIN vendor_infos VI ON CI.vendor_id=VI.vendor_id WHERE CI.vendor_id=:vendor_id or FIND_IN_SET($vendor_id, PI.vendor_ids)");
			$this->crud->bind(':vendor_id', $vendor_id);
			$row = $this->crud->resultArray();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorProjectNames($vendor_id)
	{
		try {
			$this->crud->query("SELECT distinct PI.project_id,PI.name FROM project_infos PI LEFT JOIN contract_infos CI ON CI.project_id=PI.project_id LEFT JOIN vendor_infos VI ON CI.vendor_id=VI.vendor_id WHERE CI.vendor_id=:vendor_id order by PI.name");
			$this->crud->bind(':vendor_id', $vendor_id);
			$row = $this->crud->resultSet();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorProjectContracts($project_id, $vendor_id)
	{
		try {
			$this->crud->query("SELECT distinct contract_id,contract_desc from contract_infos WHERE vendor_id=:vendor_id and project_id=:project_id order by contract_desc");
			$this->crud->bind(':vendor_id', $vendor_id);
			$this->crud->bind(':project_id', $project_id);
			$row = $this->crud->resultSet();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorContractInvoices($contract_id, $vendor_id)
	{
		try {
			$this->crud->query("SELECT distinct invoice_id,invoice_no from invoice_infos WHERE vendor_id=:vendor_id and contract_id=:contract_id order by invoice_no");
			$this->crud->bind(':vendor_id', $vendor_id);
			$this->crud->bind(':contract_id', $contract_id);
			$row = $this->crud->resultSet();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getOtherVendors($vendor_id)
	{
		try {
			$this->crud->query("SELECT DISTINCT vendor_id FROM invoice_infos WHERE contract_id IN (SELECT contract_id FROM contract_infos WHERE vendor_id=:vendor_id) AND material_by_owner=1");
			$this->crud->bind(':vendor_id', $vendor_id);
			$row = $this->crud->resultArray();
			return $row;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	public function getVendorWage($id)
	{
		$row = $this->getVendorInfo($id);
		if ($row['hourly_wage']) {
			return $row['hourly_wage'];
		}
		return 0;
	}

	//If there's an email in Employee table then fetch it. If not, go to according Company table and fetch the company email.
	public function getVendorEmail($id)
	{
		try {
			$row = $this->getVendorInfo($id);
			if ($row['email']) {
				return $row['email'];
			}
			return null;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//If there's a mobile in Employee table then fetch it. If not, go to according Company table and fetch the company phone.
	public function getVendorMobile($id)
	{
		try {
			$row = $this->getVendorInfo($id);
			if ($row['mobile']) {
				return $row['mobile'];
			}
			return null;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorRequestType($id)
	{
		try {
			$this->crud->query("SELECT request_type_id FROM vendor_infos WHERE vendor_id=:id");
			$this->crud->bind(':id', $id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorTypes()
	{
		try {
			$this->crud->query("SELECT * FROM vendor_types");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorsList()
	{
		try {
			$this->crud->query("SELECT * FROM vendor_infos");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorsListByCompany($company_id)
	{
		try {
			$this->crud->query("SELECT * FROM vendor_infos where company_id=$company_id order by company_name");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorOwnerListByCompany($company_id, $ownerORvendor)
	{
		try {
			$this->crud->query("SELECT * FROM view_owner_vendors where company_id=$company_id and co in ($ownerORvendor) order by owner_vendor_name");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorsListFilter($data)
	{
		$filter_criteria = "true ";
		if ($data["vendorSpeciality"] != false) {
			$filter_criteria .= " AND FIND_IN_SET($data[vendorSpeciality],request_type_id)";
		}
		if ($data["vendorSpecialityLevel"] != false) {
			$filter_criteria .= " AND speciality_level = $data[vendorSpecialityLevel]";
		}
		if ($data["vendor_type"] != false) {
			$filter_criteria .= " AND vendor_type_id = $data[vendor_type]";
		}
		if ($data["vendorLicenses"] != false) {
			$filter_criteria .= " AND FIND_IN_SET($data[vendorLicenses],licenses_type_ids)";
		}

		$query = "SELECT * FROM vendor_infos WHERE " . $filter_criteria;

		try {
			$this->crud->query($query);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorsId()
	{
		try {
			$this->crud->query("SELECT GROUP_CONCAT(vendor_id) as vendors FROM vendor_infos");
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getVendorsListByReqId($request_id)
	{
		try {
			$this->crud->query("SELECT GROUP_CONCAT(user_id) FROM request_assignees where request_id = :request_id");
			$this->crud->bind(':request_id', $request_id);
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function setPaymentDetails($paymentDetails, $request_id, $vendor_id)
	{
		$paymentExists = $this->checkIfRequestPaymentExists($request_id);
		if ($paymentExists) {
			// Request ID payment details already exists
			// Update the details
			return $this->updatePaymentDetails($request_id, $vendor_id, $paymentDetails);
		}
		$invoiceDetails  = explode("=", $paymentDetails["invoice_attached"][0]);
		$invoiceNum      = $invoiceDetails[0];
		$invoiceAttached = $invoiceDetails[1];
		try {
			$this->crud->query("INSERT INTO request_payments(request_id,
                                                            payment_amount,
                                                            is_approved,
                                                            repair_detail,
                                                            invoice_detail,
                                                            invoice_attachment,
                                                            job_hours,
                                                            vendor_id,
                                                            other_expenses) 
                                VALUES(:request_id,
                                       :amount,
                                       0,
                                       :repair_detail,
                                       :invoice_detail,
                                       :invoice_attach,
                                       :job_hours,
                                       :vendor_id,
                                       :expenses
                                       ) 
                              ");
			$this->crud->bind(':request_id', $request_id);
			$this->crud->bind(':amount', $paymentDetails["request_pay_perhr"]);
			$this->crud->bind(':repair_detail', $paymentDetails["request_payinfo"]);
			$this->crud->bind(':invoice_detail', $invoiceNum);
			$this->crud->bind(':invoice_attach', $invoiceAttached);
			$this->crud->bind(':job_hours', $paymentDetails["request_payhours"]);
			$this->crud->bind(':vendor_id', $vendor_id);
			$this->crud->bind(':expenses', $paymentDetails["request_pay_expenses"]);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function checkIfRequestPaymentExists($request_id)
	{
		try {
			$this->crud->query("SELECT id FROM request_payments where request_id = :request_id");
			$this->crud->bind(':request_id', $request_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updatePaymentDetails($request_id, $vendor_id, $paymentDetails)
	{
		$invoiceDetails  = explode("=", $paymentDetails["invoice_attached"][0]);
		$invoiceNum      = $invoiceDetails[0];
		$invoiceAttached = $invoiceDetails[1];
		try {
			$this->crud->query("UPDATE request_payments SET                                      
                                       payment_amount = :amount,                                       
                                       is_approved = 0,
                                       repair_detail = :repair_detail,
                                       invoice_detail = :invoice_detail,
                                       invoice_attachment = :invoice_attach,
                                       job_hours = :job_hours,
                                       vendor_id = :vendor_id,
                                       other_expenses = :expenses
                                       WHERE request_id = :request_id                                        
                              ");
			$this->crud->bind(':request_id', $request_id);
			$this->crud->bind(':amount', $paymentDetails["request_pay_perhr"]);
			$this->crud->bind(':repair_detail', $paymentDetails["request_payinfo"]);
			$this->crud->bind(':invoice_detail', $invoiceNum);
			$this->crud->bind(':invoice_attach', $invoiceAttached);
			$this->crud->bind(':job_hours', $paymentDetails["request_payhours"]);
			$this->crud->bind(':vendor_id', $vendor_id);
			$this->crud->bind(':expenses', $paymentDetails["request_pay_expenses"]);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getPaymentDetails($request_id)
	{
		try {
			$this->crud->query("SELECT * FROM request_payments where request_id = :request_id");
			$this->crud->bind(':request_id', $request_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function approvePayment($request_id, $comment, $amount)
	{
		try {
			$this->crud->query("UPDATE request_payments SET is_approved = 1,approval_amount=:amount,approval_comment=:comment WHERE request_id = :request_id");
			$this->crud->bind(':request_id', $request_id);
			$this->crud->bind(':amount', $amount);
			$this->crud->bind(':comment', $comment);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	// get speciality levels for the vendors
	public function getVendorSpecialityLevels()
	{
		try {
			$this->crud->query("SELECT * FROM speciality_levels");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getLicenseTypes()
	{
		try {
			$this->crud->query("SELECT * FROM license_types");
			return $this->crud->resultSet();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function updateProjectCount($vendor_id)
	{
		try {

			$this->crud->query("UPDATE vendor_infos SET projects_no = projects_no + 1 WHERE vendor_id = :vendor_id");
			$this->crud->bind(':vendor_id', $vendor_id);
			return $this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}