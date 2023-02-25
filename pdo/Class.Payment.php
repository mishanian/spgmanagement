<?php
class Payment
{
	private $crud;

	public function __construct($DB_con)
	{
		$this->crud = new Crud($DB_con);
	}

	/**
	 * get info of lease_payment_details
	 * lease_payment_details table : the table to keep real rent transaction
	 * the function only be used for online payment (not manual payment)
	 */
	public function get_payment_details($lease_payment_details_id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payment_details WHERE id = :id");
			$this->crud->bind(':id', $lease_payment_details_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get detail info of the lease_payment_details
	 * including info about lease_payment due data, outstanding, payment method, payment type, processed_by, tenant, building, unit
	 */
	public function get_all_info_from_payment_details($lease_payment_details_id)
	{
		try {
			$this->crud->query("SELECT lease_payments.due_date AS due_date,lease_payments.outstanding AS outstanding, lease_payments.total AS total, payment_methods.name AS payment_method,payment_types.name AS payment_type,lease_payment_details.entry_user_id AS proceed_by,lease_infos.tenant_ids AS tenants_id, building_infos.building_name AS building, floor_infos.floor_name AS floor, apartment_infos.unit_number AS unit FROM lease_payment_details,lease_payments,payment_methods,payment_types,lease_infos,apartment_infos,building_infos,floor_infos WHERE lease_payment_details.lease_payment_id = lease_payments.id AND payment_methods.id = lease_payment_details.payment_method_id AND lease_payment_details.payment_type_id = payment_types.id AND lease_infos.id = lease_payments.lease_id AND apartment_infos.apartment_id = lease_infos.apartment_id AND building_infos.building_id = apartment_infos.building_id AND floor_infos.floor_id = apartment_infos.floor_id AND lease_payment_details.id = :lease_payment_details_id");
			$this->crud->bind(':lease_payment_details_id', $lease_payment_details_id);
			$result = $this->crud->resultSingle();

			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get info of tenant
	 */
	public function get_tenant_info($tenant_id)
	{
		try {
			$this->crud->query("SELECT * FROM tenant_infos WHERE tenant_id = :id");
			$this->crud->bind(':id', $tenant_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the company info for a lease_payment_details
	 */
	public function get_company_info($lease_payment_details_id)
	{
		try {
			$this->crud->query("SELECT * FROM company_infos WHERE company_infos.id = ( SELECT company_id FROM employee_infos WHERE employee_infos.employee_id = ( SELECT lease_infos.employee_id FROM lease_infos  WHERE lease_infos.id = ( SELECT lease_payments.lease_id FROM lease_payments WHERE lease_payments.id = ( SELECT lease_payment_details.lease_payment_id FROM lease_payment_details WHERE lease_payment_details.id=:lease_payment_details_id))))");
			$this->crud->bind(':lease_payment_details_id', $lease_payment_details_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the info of employee
	 */
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


	//-------------------------  Invoice class ----------------------------

	/**
	 * get the detailed info about a lease_payment
	 * the function is used to generate the invoice
	 */
	public function get_invoice_info($lease_payment_id)
	{
		try {
			$this->crud->query("SELECT lease_payments.due_date AS due_date,lease_payments.outstanding AS outstanding, lease_payments.total AS total, lease_infos.tenant_ids AS tenants_id,building_infos.building_name AS building, floor_infos.floor_name AS floor, apartment_infos.unit_number AS unit, apartment_infos.apartment_type_id , apartment_infos.area , apartment_infos.tax1 , apartment_infos.tax2 , apartment_infos.tax3 , payment_status.name AS payment_status,lease_payments.comments AS comment  FROM lease_payments,lease_infos,apartment_infos,building_infos,floor_infos, payment_status WHERE lease_infos.id = lease_payments.lease_id AND apartment_infos.apartment_id = lease_infos.apartment_id AND building_infos.building_id = apartment_infos.building_id AND floor_infos.floor_id = apartment_infos.floor_id  AND payment_status.id = lease_payments.payment_status_id AND lease_payments.id=:id");
			$this->crud->bind(':id', $lease_payment_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the company info
	 * the function is used to generate the invoice
	 */
	public function get_company_info_from_invoice($lease_payments_id)
	{
		try {
			$this->crud->query('SELECT * FROM company_infos WHERE company_infos.id = ( SELECT company_id FROM employee_infos WHERE employee_infos.employee_id = ( SELECT lease_infos.employee_id FROM lease_infos  WHERE lease_infos.id = ( SELECT lease_payments.lease_id FROM lease_payments WHERE lease_payments.id = :lease_payments_id )))');
			$this->crud->bind(':lease_payments_id', $lease_payments_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the detailed info about a lease_payment
	 * the function is only used to generate the invoice
	 */
	public function get_all_payment_details_for_invoice($lease_payments_id)
	{
		try {
			$this->crud->query('SELECT payment_methods.name AS payment_method,amount,entry_datetime FROM lease_payment_details,payment_methods WHERE payment_methods.id = lease_payment_details.payment_method_id AND lease_payment_details.lease_payment_id = :lease_payments_id');
			$this->crud->bind(':lease_payments_id', $lease_payments_id);
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//---------------------------  deposit notification --------------------------

	/**
	 * get info of deposit
	 * the function is used to generate the deposit receipt
	 */
	public function get_deposit_info($deposit_id)
	{
		try {
			$this->crud->query("SELECT
			building_infos.building_name AS building_name,
			apartment_infos.unit_number AS unit_number,
			floor_infos.floor_name AS `floor`,
			tenant_id,
			payment_methods.name AS payment_method,
			amount,
			paid_date,
			deposits.comments,
			company_infos.name AS company_name,
			employee_infos.full_name AS proceed_by,
			deposit_status,
			deposit_types.name AS deposit_type,
			return_date,
			return_employee_id
		  FROM
			deposits
			LEFT JOIN building_infos ON building_infos.building_id = deposits.building_id
			LEFT JOIN apartment_infos ON apartment_infos.apartment_id = deposits.apartment_id
			LEFT JOIN payment_methods ON payment_methods.id = deposits.payment_method_id
			LEFT JOIN employee_infos ON employee_infos.employee_id = deposits.employee_id
			LEFT JOIN floor_infos ON floor_infos.floor_id = apartment_infos.floor_id
			LEFT JOIN company_infos ON company_infos.id = employee_infos.company_id
			LEFT JOIN deposit_types ON deposit_types.id = deposits.deposit_type_id
		  WHERE deposits.id = :id ");
			$this->crud->bind(":id", $deposit_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//-------------------- Online Payment Module (2017-12-18) --------------------

	/**
	 * get the convenience free rate (global level)
	 */
	public function get_convenience_fee_rate()
	{
		try {
			$this->crud->query("SELECT CF_PP_Balance_F,CF_PP_CC_P,CF_M_CC_P,CF_M_Interac_F FROM settings WHERE id = 1");
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one record to transactions_paypal table
	 * whatever the product (which module needs payment), as long as paying by paypal, one record need to be add in transactions_paypal table
	 */
	public function add_paypal_transaction_record($p_id, $p_state, $p_create_time, $p_invoice_number, $p_payer_id, $p_merchant_id, $p_total, $p_description)
	{
		try {
			$this->crud->query("INSERT INTO transactions_paypal(p_id, p_state, p_create_time, p_invoice_number, p_payer_id, p_merchant_id, p_total,p_description) VALUES (:p_id, :p_state, :p_create_time, :p_invoice_number, :p_payer_id, :p_merchant_id, :p_total,:p_description)");
			$this->crud->bind(":p_id", $p_id);
			$this->crud->bind(":p_state", $p_state);
			$this->crud->bind(":p_create_time", $p_create_time);
			$this->crud->bind(":p_invoice_number", $p_invoice_number);
			$this->crud->bind(":p_payer_id", $p_payer_id);
			$this->crud->bind(":p_merchant_id", $p_merchant_id);
			$this->crud->bind(":p_total", $p_total);
			$this->crud->bind(":p_description", $p_description);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one record to transactions_moneris table
	 * whatever the product (which module needs payment), as long as paying by Moneris / Interac, one record need to be add in transactions_moneris table
	 */
	public function add_moneris_transaction_record($response_order_id, $bank_transaction_id, $bank_approval_code, $issuer_name, $issuer_confirm, $iso_code, $trans_name, $cardholder, $creditcard_no, $creditcard, $expiry_date, $result, $convenience_fee, $cf_success, $cf_fee_rate, $cf_fee_type, $response_code)
	{
		try {
			$this->crud->query("INSERT INTO transactions_moneris(response_order_id, bank_transaction_id, bank_approval_code, issuer_name,issuer_confirm, iso_code, trans_name, cardholder, creditcard_no, creditcard, expiry_date, result, convenience_fee, cf_success, cf_fee_rate, cf_fee_type, response_code) VALUES (:response_order_id, :bank_transaction_id, :bank_approval_code, :issuer_name, :issuer_confirm, :iso_code, :trans_name, :cardholder, :creditcard_no, :creditcard, :expiry_date, :result, :convenience_fee, :cf_success, :cf_fee_rate, :cf_fee_type, :response_code)");
			$this->crud->bind(":response_order_id", $response_order_id);
			$this->crud->bind(":bank_transaction_id", $bank_transaction_id);
			$this->crud->bind(":bank_approval_code", $bank_approval_code);
			$this->crud->bind(":issuer_name", $issuer_name);
			$this->crud->bind(":issuer_confirm", $issuer_confirm);
			$this->crud->bind(":iso_code", $iso_code);
			$this->crud->bind(":trans_name", $trans_name);
			$this->crud->bind(":cardholder", $cardholder);
			$this->crud->bind(":creditcard_no", $creditcard_no);
			$this->crud->bind(":creditcard", $creditcard);
			$this->crud->bind(":expiry_date", $expiry_date);
			$this->crud->bind(":result", $result);
			$this->crud->bind(":convenience_fee", $convenience_fee);
			$this->crud->bind(":cf_success", $cf_success);
			$this->crud->bind(":cf_fee_rate", $cf_fee_rate);
			$this->crud->bind(":cf_fee_type", $cf_fee_type);
			$this->crud->bind(":response_code", $response_code);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the full_name, mobile, email for the user
	 * based on the user_id, the function will check from different table
	 */
	public function get_user_info($user_id)
	{
		if ($user_id > 100000 and $user_id < 200000)
			$query = "SELECT full_name, mobile, email FROM tenant_infos WHERE tenant_id =:user_id";
		else if ($user_id > 200000)
			$query = "SELECT full_name,mobile,email FROM owner_infos WHERE owner_id = :user_id";
		else
			$query = "SELECT full_name, email,mobile FROM employee_infos WHERE employee_id =:user_id";
		try {
			$this->crud->query($query);
			$this->crud->bind("user_id", $user_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get lease_infos id , based on lease_payment_details
	 *
	 */
	public function get_lease_info_id_by_lease_payment_details($lease_payment_details_id)
	{
		try {
			$this->crud->query("SELECT lease_payments.lease_id FROM lease_payments,lease_payment_details WHERE lease_payment_details.lease_payment_id = lease_payments.id AND lease_payment_details.id = :lease_payment_details_id");
			$this->crud->bind(":lease_payment_details_id", $lease_payment_details_id);
			$result = $this->crud->resultSingle();
			return $result['lease_id'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}



	//--------------------- Lease Payment --------------------

	/**
	 * get info from rental_payment view
	 * rental_payment view keeps info about current payment outstanding ....
	 */
	public function get_rental_payment_by_lease_payment_id($lease_payment_id)
	{
		try {
			$this->crud->query("SELECT * FROM rental_payments WHERE lease_payment_id = :lease_payment_id");
			$this->crud->bind(":lease_payment_id", $lease_payment_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get tenant_id, full_name of tenant, from a string list of tenant_id
	 * input: if tenant_ids [10001,10002] -> 10001,10002
	 */
	public function get_tenants_info($tenant_ids)
	{
		try {
			$this->crud->query("SELECT tenant_id,full_name FROM tenant_infos WHERE tenant_id IN(" . $tenant_ids . ")");
			$result = $this->crud->resultSet();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one record to lease_payment_details table
	 * before sending payment request to bank, need to add the payment record firstly, leave other filed null and is_paid=0
	 * the function will be used before sending request to bank
	 */
	public function create_lease_payment_detail_record($entry_user_id, $lease_payment_id, $cf_amount, $amount, $paid_amount, $entry_datetime)
	{
		try {
			$this->crud->query("INSERT INTO lease_payment_details (entry_user_id, lease_payment_id, cf_amount, amount, paid_amount, entry_datetime, is_paid) VALUES (:entry_user_id, :lease_payment_id, :cf_amount, :amount, :paid_amount, :entry_datetime, 0)");
			$this->crud->bind(":entry_user_id", $entry_user_id);
			$this->crud->bind(":lease_payment_id", $lease_payment_id);
			$this->crud->bind(":cf_amount", $cf_amount);
			$this->crud->bind(":amount", $amount);
			$this->crud->bind(":paid_amount", $paid_amount);
			$this->crud->bind(":entry_datetime", $entry_datetime);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * this method is to update comment value in lease_payment_details, to pass the several lease_payment_details_id when do the lease payment
	 * Node : if several lease_payment ids are selected for a payment, because the string allow to pass to bank is limited & SESSION is not stable.
	 * So, save the string of several lease_payment ids  in the first row's comment filed, and then only pass the id of first row to bank
	 */
	public function update_lease_payment_details_comment($comments, $id)
	{
		try {
			$this->crud->query("UPDATE lease_payment_details SET comments = :comments WHERE id = :id");
			$this->crud->bind(":comments", $comments);
			$this->crud->bind(":id", $id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the employee_id of building manager
	 */
	public function get_building_manager_user_id($building_id)
	{
		try {
			$this->crud->query("SELECT employee_id FROM building_infos WHERE building_id = :building_id");
			$this->crud->bind(":building_id", $building_id);
			$result = $this->crud->resultSingle();
			return $result['employee_id'];
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the lease_payment_details status, inovice_number, payment method , paypal_id
	 * the function will be used after get the confirm from paypal, to change the payment record to be valid
	 * Note: this is only work for papal & lease payment
	 */
	public function update_lease_payment_details_paypal($lease_payment_detail_id, $invoice_number, $payment_type_id, $payment_method_id, $payment_date, $paypal_transaction_id)
	{
		try {
			$this->crud->query("UPDATE lease_payment_details SET invoice_number = :invoice_number, payment_type_id = :payment_type_id, payment_method_id = :payment_method_id, payment_date = :payment_date,paypal_id = :paypal_id, is_paid = 1 WHERE id = :id");
			$this->crud->bind(":invoice_number", $invoice_number);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->bind(":payment_date", $payment_date);
			$this->crud->bind(":paypal_id", $paypal_transaction_id);
			$this->crud->bind(":id", $lease_payment_detail_id);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the lease_payment_details status, inovice_number, payment method , moneris_id
	 * the function will be used after get the confirm from moneris, to change the payment record to be valid
	 * Note: this is only work for moneris & lease payment
	 */
	public function update_lease_payment_details_moneris($lease_payment_detail_id, $invoice_number, $payment_type_id, $payment_method_id, $payment_date, $moneris_transaction_id)
	{
		try {
			$this->crud->query("UPDATE lease_payment_details SET invoice_number = :invoice_number, payment_type_id = :payment_type_id, payment_method_id = :payment_method_id, payment_date = :payment_date, moneris_id = :moneris_id, is_paid = 1 WHERE id = :id");
			$this->crud->bind(":invoice_number", $invoice_number);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->bind(":payment_date", $payment_date);
			$this->crud->bind(":moneris_id", $moneris_transaction_id);
			$this->crud->bind(":id", $lease_payment_detail_id);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get info of transactions_paypal
	 */
	public function get_paypal_transaction_info($paypal_id)
	{
		try {
			$this->crud->query("SELECT * FROM transactions_paypal WHERE id = :paypal_id");
			$this->crud->bind(":paypal_id", $paypal_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get info of transactions_moneris
	 */
	public function get_moneris_transaction_info($moneris_id)
	{
		try {
			$this->crud->query("SELECT * FROM transactions_moneris WHERE id = :moneris_id");
			$this->crud->bind(":moneris_id", $moneris_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the info of lease_payment_details
	 */
	public function get_lease_payment_detail_info_by_id($id)
	{
		try {
			$this->crud->query("SELECT * FROM lease_payment_details WHERE id = :id");
			$this->crud->bind(":id", $id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//---------------------- Kijiji Payment --------------------

	/**
	 * get info of kijiji payment
	 */
	public function get_kijiji_payment_info_by_id($id)
	{
		try {
			$this->crud->query("SELECT * FROM kijiji_payments WHERE id = :id");
			$this->crud->bind(":id", $id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the kijiji_payments status, invoice_number, payment method , paypal_id
	 * the function will be used after get the confirm from paypal, to change the payment record to be valid
	 * Note: this function is only work for paypal & kijiji payment
	 */
	public function update_kijiji_payment_info_paypal($kjj_payment_id, $invoice_number, $payment_time, $slots_due_time, $payment_status, $transactions_paypal_id, $payment_type_id, $payment_method_id)
	{
		try {
			$this->crud->query("UPDATE kijiji_payments SET payment_time = :payment_time, inovice_number = :inovice_number, slots_due_time = :slots_due_time , payment_status = :payment_status, transactions_paypal_id = :transactions_paypal_id, payment_type_id = :payment_type_id , payment_method_id = :payment_method_id where id = :id");
			$this->crud->bind(":id", $kjj_payment_id);
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":inovice_number", $invoice_number);
			$this->crud->bind(":slots_due_time", $slots_due_time);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_paypal_id", $transactions_paypal_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the kijiji_payments status, invoice_number, payment method , moneris_id
	 * the function will be used after get the confirm from moneris, to change the payment record to be valid
	 * Note: this function is only work for moneris & kijiji payment
	 */
	public function update_kijiji_payment_info_moneris($kjj_payment_id, $payment_time, $invoice_number, $slots_due_time, $payment_status, $transactions_moneris_id, $payment_type_id, $payment_method_id)
	{
		try {
			$this->crud->query("UPDATE kijiji_payments SET payment_time = :payment_time,inovice_number = :inovice_number, slots_due_time = :slots_due_time, payment_status = :payment_status, transactions_moneris_id = :transactions_moneris_id, payment_type_id = :payment_type_id , payment_method_id = :payment_method_id where id = :id");
			$this->crud->bind(":id", $kjj_payment_id);
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":inovice_number", $invoice_number);
			$this->crud->bind(":slots_due_time", $slots_due_time);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_moneris_id", $transactions_moneris_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 *  add one record to kijiji_payments table
	 * before sending payment request to bank, need to add the payment record firstly, leave other filed null and is_paid=0
	 * the function will be used before sending payment request to bank
	 */
	public function create_kijiji_record($employee_id, $buy_slots_count, $buy_slots_price, $created_time, $payment_amount, $C_F_amount, $total_amount)
	{
		try {
			$this->crud->query("INSERT INTO kijiji_payments (employee_id, buy_slots_count, buy_slots_price, created_time,payment_amount,C_F_amount,total_amount,payment_status) VALUES ( :employee_id, :buy_slots_count, :buy_slots_price, :created_time, :payment_amount, :C_F_amount, :total_amount, 0)");
			$this->crud->bind(":employee_id", $employee_id);
			$this->crud->bind(":buy_slots_count", $buy_slots_count);
			$this->crud->bind(":buy_slots_price", $buy_slots_price);
			$this->crud->bind(":created_time", $created_time);
			$this->crud->bind(":payment_amount", $payment_amount);
			$this->crud->bind(":C_F_amount", $C_F_amount);
			$this->crud->bind(":total_amount", $total_amount);
			$this->crud->execute();
			$id = $this->crud->lastInsertId();
			return $id;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the detailed info about kijiji payment
	 * the function only work for generating receipt
	 */
	public function get_kjj_payment_info_for_receipt($kijiji_payment_id)
	{
		try {
			$this->crud->query("SELECT inovice_number,buy_slots_count,buy_slots_price,payment_time,slots_due_time,payment_amount,C_F_amount,total_amount,payment_status, transactions_paypal_id, transactions_moneris_id, employee_infos.email AS email , employee_infos.full_name AS employee_name,payment_types.name AS payment_type, payment_methods.name AS payment_method FROM kijiji_payments , employee_infos, payment_types, payment_methods WHERE employee_infos.employee_id = kijiji_payments.employee_id AND payment_methods.id = kijiji_payments.payment_method_id AND payment_types.id = kijiji_payments.payment_type_id AND kijiji_payments.id = :kijiji_payments_id");
			$this->crud->bind(":kijiji_payments_id", $kijiji_payment_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * the function is only used in kijiji
	 */
	public function get_kjj_paypal_transaction_info($paypal_id)
	{
		try {
			$this->crud->query("SELECT * FROM transactions_paypal WHERE id = :paypal_id");
			$this->crud->bind(":paypal_id", $paypal_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * the function is only used in kijiji
	 */
	public function get_kjj_moneris_transaction_info($moneris_id)
	{
		try {
			$this->crud->query("SELECT * FROM transactions_moneris WHERE id = :moneris_id");
			$this->crud->bind(":moneris_id", $moneris_id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}


	//---------------------- Services payment --------------------

	/**
	 * get the services(online_lease_sign & credit_check) price
	 */
	public function get_servies_price()
	{
		try {
			$this->crud->query("SELECT service_price_crecheck, service_price_leasign FROM settings WHERE id = 1");
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * add one record to service_payments table
	 * before sending payment request to bank, need to add the payment record firstly, leave other filed null and is_paid=0
	 * the function will be used before sending payment request to bank
	 */
	public function create_services_payment_record($user_id, $service_type, $service_price, $services_count, $payment_amount, $convenience_fee, $total_amount)
	{
		try {
			$this->crud->query("INSERT INTO service_payments (user_id, service_type, service_price, services_count, payment_amount, convenience_fee, total_amount, payment_status) VALUES (:user_id ,:service_type, :service_price, :services_count, :payment_amount, :convenience_fee, :total_amount, 0)");
			$this->crud->bind(":user_id", $user_id);
			$this->crud->bind(":service_type", $service_type);
			$this->crud->bind(":service_price", $service_price);
			$this->crud->bind(":services_count", $services_count);
			$this->crud->bind(":payment_amount", $payment_amount);
			$this->crud->bind(":convenience_fee", $convenience_fee);
			$this->crud->bind(":total_amount", $total_amount);
			$this->crud->execute();
			$id = $this->crud->lastInsertId();
			return $id;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get info of service_payments
	 */
	public function get_services_payment_info($services_payment_id)
	{
		try {
			$this->crud->query("SELECT * FROM service_payments WHERE id = :services_payment_id");
			$this->crud->bind(":services_payment_id", $services_payment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the service payment type name, service payment method name for a service_payment payment
	 */
	public function get_services_payment_method_type($services_payment_id)
	{
		try {
			$this->crud->query("SELECT payment_types.name AS payment_type, payment_methods.name AS payment_method FROM service_payments, payment_methods, payment_types WHERE payment_methods.id = service_payments.payment_method_id AND payment_types.id = service_payments.payment_type_id AND service_payments.id = :services_payment_id");
			$this->crud->bind(":services_payment_id", $services_payment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the service_payments status, invoice_number, payment method , paypal_id
	 * the function will be used after get the confirm from paypal, to change the payment record to be valid
	 * Note: this function is only work for paypal & service_payments
	 */
	public function update_services_payment_info_paypal($payment_time, $invoice_number, $payment_status, $transactions_paypal_id, $payment_method_id, $payment_type_id, $id)
	{
		try {
			$this->crud->query("UPDATE service_payments SET payment_time = :payment_time, inovice_number = :inovice_number , payment_status = :payment_status, transactions_paypal_id = :transactions_paypal_id, payment_method_id = :payment_method_id, payment_type_id = :payment_type_id WHERE id = :id");
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":inovice_number", $invoice_number);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_paypal_id", $transactions_paypal_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":id", $id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * update the service_payments status, invoice_number, payment method , moneris_id
	 * the function will be used after get the confirm from moneris, to change the payment record to be valid
	 * Note: this function is only work for moneris & service_payments
	 */
	public function update_services_payment_info_moneris($payment_time, $invoice_number, $payment_status, $transactions_moneris_id, $payment_method_id, $payment_type_id, $id)
	{
		try {
			$this->crud->query("UPDATE service_payments SET payment_time = :payment_time, inovice_number = :inovice_number, payment_status = :payment_status, transactions_moneris_id = :transactions_moneris_id, payment_method_id = :payment_method_id, payment_type_id = :payment_type_id WHERE id = :id");
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":inovice_number", $invoice_number);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_moneris_id", $transactions_moneris_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":id", $id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//-------------------------- forward payment --------------------------

	/**
	 * add one record to payment_forward_infos
	 * when issue a forward_payment, add auto_generated unique token, payment amount, forward_to recipient contact to the table
	 * only token to be pass for processing the forward payment
	 */
	public function add_payment_forwards($created_user_id, $created_time, $check_token, $product, $payment_amount, $forward_to_name, $forward_to_email, $forward_to_mobile, $is_active, $data)
	{
		try {
			$this->crud->query("INSERT INTO payment_forward_infos (created_user_id, created_time, check_token, product, payment_amount, forward_to_name, forward_to_email, forward_to_mobile, is_active, data) VALUES (:created_user_id,:created_time,:check_token,:product,:payment_amount,:forward_to_name,:forward_to_email,:forward_to_mobile,:is_active, :data)");
			$this->crud->bind(":created_user_id", $created_user_id);
			$this->crud->bind(":created_time", $created_time);
			$this->crud->bind(":check_token", $check_token);
			$this->crud->bind(":product", $product);
			$this->crud->bind(":payment_amount", $payment_amount);
			$this->crud->bind(":forward_to_name", $forward_to_name);
			$this->crud->bind(":forward_to_email", $forward_to_email);
			$this->crud->bind(":forward_to_mobile", $forward_to_mobile);
			$this->crud->bind(":is_active", $is_active);
			$this->crud->bind(":data", $data);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * get the forward payment info, based on token
	 * the function will be used after recipient open the link of forward payment
	 */
	public function get_payment_forwards_info_by_token($check_token)
	{
		try {
			$this->crud->query("SELECT * FROM payment_forward_infos WHERE check_token = :check_token");
			$this->crud->bind(":check_token", $check_token);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * after done the forward payment, deactivate the token
	 * after that the token is invalid
	 */
	public function deactivate_forward_payment_token($check_token)
	{
		try {
			$this->crud->query("UPDATE payment_forward_infos SET is_active = 0 WHERE check_token = :check_token");
			$this->crud->bind(":check_token", $check_token);
			$this->crud->execute();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	//------------------------- SALE PAYMENT ------------------------------
	public function get_sale_payment_info_by_id($id)
	{
		try {
			$this->crud->query("SELECT * FROM sale_payments WHERE id = :id");
			$this->crud->bind(":id", $id);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_sale_payment_info_by_invoice_number($invoice_number)
	{
		try {
			$this->crud->query("SELECT * FROM sale_payments WHERE invoice_number = :invoice_number");
			$this->crud->bind(":invoice_number", $invoice_number);
			$result = $this->crud->resultSingle();
			return $result;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function create_sale_record($invoice_number, $employee_id, $created_time, $payment_amount, $C_F_amount, $total_amount, $item_name, $item_desc, $tenantId, $customerName, $isCustomerTenantValue)
	{
		try {
			$this->crud->query("INSERT INTO sale_payments (employee_id, invoice_number, created_time,payment_amount,C_F_amount,total_amount,payment_status,item_name,item_description,sale_date,is_customer_tenant,tenant_id,customer_fullname) VALUES ( :employee_id, :invoice_number,:created_time, :payment_amount, :C_F_amount, :total_amount, 0, :item_name,:item_desc,NOW(),:is_customer_tenant,:tenant_id,:customer_fullname)");
			$this->crud->bind(":employee_id", $employee_id);
			$this->crud->bind(":invoice_number", $invoice_number);
			$this->crud->bind(":created_time", $created_time);
			$this->crud->bind(":payment_amount", $payment_amount);
			$this->crud->bind(":C_F_amount", $C_F_amount);
			$this->crud->bind(":total_amount", $total_amount);
			$this->crud->bind(":item_name", $item_name);
			$this->crud->bind(":item_desc", $item_desc);
			$this->crud->bind(":is_customer_tenant", $isCustomerTenantValue);
			$this->crud->bind(":tenant_id", $tenantId);
			$this->crud->bind(":customer_fullname", $customerName);
			$this->crud->execute();
			return $this->crud->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function update_sale_payment_info_paypal($sale_record_id, $payment_time, $payment_status, $transactions_paypal_id, $payment_type_id, $payment_method_id)
	{
		try {
			$this->crud->query("UPDATE sale_payments SET payment_time = :payment_time, payment_status = :payment_status, transactions_paypal_id = :transactions_paypal_id, payment_type_id = :payment_type_id , payment_method_id = :payment_method_id where id = :id");
			$this->crud->bind(":id", $sale_record_id);
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_paypal_id", $transactions_paypal_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function update_sale_payment_info_moneris($sale_record_id, $payment_time, $payment_status, $transactions_moneris_id, $payment_type_id, $payment_method_id)
	{
		try {
			$this->crud->query("UPDATE sale_payments SET payment_time = :payment_time,payment_status = :payment_status, transactions_moneris_id = :transactions_moneris_id, payment_type_id = :payment_type_id , payment_method_id = :payment_method_id where invoice_number = :invoice_number");
			$this->crud->bind(":invoice_number", $sale_record_id);
			$this->crud->bind(":payment_time", $payment_time);
			$this->crud->bind(":payment_status", $payment_status);
			$this->crud->bind(":transactions_moneris_id", $transactions_moneris_id);
			$this->crud->bind(":payment_type_id", $payment_type_id);
			$this->crud->bind(":payment_method_id", $payment_method_id);
			$this->crud->execute();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function get_sale_payment_method_type($sale_payment_id)
	{
		try {
			$this->crud->query("SELECT payment_types.name AS payment_type, payment_methods.name AS payment_method FROM sale_payments, payment_methods, payment_types WHERE payment_methods.id = sale_payments.payment_method_id AND payment_types.id = sale_payments.payment_type_id AND sale_payments.id = :sale_payment_id");
			$this->crud->bind(":sale_payment_id", $sale_payment_id);
			return $this->crud->resultSingle();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}
