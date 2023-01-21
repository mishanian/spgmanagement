<?php
class Bill
{
    /**
     * @var Crud
     */
    private $crud;
    private $DB_size;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
        #include_once 'Class.SizeType.php';
        #$this->DB_size = new SizeType($DB_con);
    }


    /**
     * Get all bills
     * @return mixed
     */
    public function getAllPayments()
    {
        $condition = 'id is NOT NULL';
        if (!empty($_GET['vid'])) {
            $condition .= ' AND vendor_id in (' . $_GET['vid'] . ')';
        }

        try {
            $this->crud->query("SELECT * FROM payment_infos WHERE $condition");
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get payment by ID
     * @param $id
     * @return mixed
     */
    public function getPaymentByID($id)
    {

        try {

            $this->crud->query("SELECT * FROM payment_infos where id=$id");
            $rows = $this->crud->resultSingle();
           // die(var_dump($rows));
            return $rows;

        } catch (PDOException $e) {
            $e->getMessage();
        }
    }


    public function getPaymentInvoiceAttachment($invoice_id)
    {

        try {

            $this->crud->query("select file from attachment_infos where document_category_id=14 and invoice_id=$invoice_id");
            $rows = $this->crud->resultField();
            // die(var_dump($rows));
            return $rows;

        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getProjectContractNo($project_id,$contract_id,$invoice_id)
    {

        try {
            $project_no=$contract_no=$invoice_no="";
            $this->crud->query("select project_no from project_infos where project_id=$project_id");
            $project_no = $this->crud->resultField();
            $this->crud->query("select contract_no from contract_infos where contract_id=$contract_id");
            $contract_no = $this->crud->resultField();
            $this->crud->query("select invoice_no from invoice_infos where invoice_id=$invoice_id");
            $invoice_no = $this->crud->resultField();
            $returnarray=array($project_no,$contract_no,$invoice_no);
            //die(var_dump($invoice_no));
            return $returnarray;
            // die(var_dump($rows));
            //return $rows;

        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get all bills
     * @return mixed
     */
    public function getAllBills()
    {
        $condition = 'id is NOT NULL';
        if (!empty($_GET['vid'])) {
            $condition .= ' AND vendor_id in (' . $_GET['vid'] . ')';
        }
        if (!empty($_GET['eid'])) {
            $condition .= ' AND employee_id in (' . $_GET['eid'] . ')';
        }
        if (!empty($_GET['bid'])) {
            $condition .= ' AND building_id in (' . $_GET['bid'] . ')';
        }
        if (!empty($_GET['uid'])) {
            $condition .= ' AND apartment_id in (' . $_GET['uid'] . ')';
        }

        try {
            $this->crud->query("SELECT * FROM invoice_infos WHERE $condition");
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get bill by ID
     * @param $bill_id
     * @return mixed
     */
    public function getBillByID($invoice_id)
    {
        try {
            $this->crud->query("SELECT * FROM invoice_infos WHERE invoice_id=:invoice_id");
            $this->crud->bind(":invoice_id", $invoice_id);
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get bill by ID
     * @param $request_id
     * @return mixed
     */
    public function getBillByRequestID($request_id)
    {
        try {
            $this->crud->query("SELECT * FROM invoice_infos WHERE request_id=:request_id");
            $this->crud->bind(":request_id", $request_id);
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get bill by ID
     * @param $contract_id
     * @return mixed
     */
    public function getBillsByContractID($contract_id)
    {
        try {
            $this->crud->query("SELECT * FROM invoice_infos WHERE contract_id=:contract_id");
            $this->crud->bind(":contract_id", $contract_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get bill details by ID
     * @param $invoice_id
     * @return mixed
     */
    public function getBillDetailsByID($bill_id)
    {
        try {
            $this->crud->query("SELECT * FROM bills_details WHERE bill_id=$bill_id");
            $this->crud->bind(":bill_id", $bill_id);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Update Bill
     * @param $data
     * @return mixed
     */
    public function updateBill($data)
    {
        if (!empty($data['material'])) {
            if ($data['material'] == 'owner') {
                $materialByOwner = 1;
            } else {
                $materialByOwner = 0;
            }
        } else {
            $materialByOwner = 0;
        }

        if (!empty($data['labor'])) {
            if ($data['labor'] == 'labor') {
                $labor = 1;
            } else {
                $labor = 0;
            }
        } else {
            $labor = 0;
        }

        $dueDate = $data['billDate'];
        $billDate = $data['date'];
        $dueDate = date("Y-m-d", strtotime($dueDate));
        $billDate = date("Y-m-d", strtotime($billDate));

        $billID = $data['bill_id'];
        $vendorId = $data['vendor_id'];
        $memo = $data['memo'];
        $grandTotal = $data['grandTotal'];
        $approvedToPay = $data['approvedToPay'];
        $invoiceNo = $data['invoiceDetail'];
        if (!empty($data['requestID'])) {
            $requestID = $data['requestID'];
        } else {
            $requestID = 0;
        }

        if (!empty($data['unit'])) {
            $unitId = $data['unit'][0];
        } else {
            $unitId = 0;
        }
        if (!empty($data['building'])) {
            $buildingId = $data['building'][0];
        } else {
            $buildingId = 0;
        }
        try {
            $this->crud->query("UPDATE invoice_infos SET 
        vendor_id =  '" . $vendorId . "', 
        request_id =  '" . $requestID . "', 
        building_id =  '" . $buildingId . "', 
        apartment_id =  '" . $unitId . "', 
        due_date =  '" . $dueDate . "',
        bill_date =  '" . $billDate . "', 
        memo =  '" . $memo . "', 
        grand_total =  '" . $grandTotal . "',
        invoice_no =  '" . $invoiceNo . "',
        approved_to_pay =  '" . $approvedToPay . "',
        material_by_owner =  '" . $materialByOwner . "',
        is_labor =  '" . $labor . "'
        WHERE id= $billID");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Update Bill Details
     * @param $data
     * @return mixed
     */
    public function updateBillDetails($data)
    {
        $bill_id = $data['bill_id'];
        if (!empty($data['requestID'])) {
            $requestID = $data['requestID'];
        } else {
            $requestID = 0;
        }

        $this->deleteBillDetails($bill_id);
        foreach ($data['amount'] as $key => $value) {
            try {
                $this->crud->query("insert into bills_details (bill_id, request_id,building_id,apartment_id, account_type_id, tax_type, tax, amount ,
                  description, total)
                  values ($bill_id,$requestID, '" . $data['building'][$key] . "', '" . $data['unit'][$key] . "', '" . $value . "', '" . $data['taxType'][$key] . "', '" . $data['taxAmount'][$key] . "',
                 '" . $data['amount'][$key] . "' , '" . $data['description'][$key] . "', '" . $data['totalAmount'][$key] . "')");

                $this->crud->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Delete Bill Details
     * @param $bill_id
     */
    public function deleteBillDetails($bill_id)
    {
        try {
            $this->crud->query("DELETE FROM bills_details WHERE bill_id = :bill_id");
            $this->crud->bind(':bill_id', $bill_id);
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Insert Bill
     * @param $data
     */
    public function InsertBill($data)
    {
        if (!empty($data['material'])) {
            if ($data['material'] == 'owner') {
                $materialByOwner = 1;
                $materialGrandTotal= $data['grandTotal'];
            } else {
                $materialByOwner = 0;
                $materialGrandTotal= $data['grandTotal'];
            }
        } else {
            $materialByOwner = 0;
            $materialGrandTotal= 0;
        }

        if (!empty($data['labor'])) {
            if ($data['labor'] == 'labor') {
                $labor = 1;
                $laborGrandTotal= $data['grandTotal'];
            } else {
                $labor = 0;
                $laborGrandTotal= 0;
            }
        } else {
            $labor = 0;
            $laborGrandTotal= 0;
        }
        $dueDate = $data['billDate'];
        $billDate = $data['date'];
        $dueDate = date("Y-m-d", strtotime($dueDate));
        $billDate = date("Y-m-d", strtotime($billDate));

        $employeeId = $_SESSION['employee_id'];
        $companyId = $_SESSION['company_id'];
        $vendorId = $data['vendor_id'];
        $memo = $data['memo'];
        $grandTotal = $data['grandTotal'];
        $invoiceNo = $data['invoiceDetail'];
        $status = 0;

        if (!empty($data['approvedToPay'])) {
            $approvedToPay = $data['approvedToPay'];
        } else {
            $approvedToPay = 0;
        }

        if (!empty($data['projectID'])) {
            $projectId = $data['projectID'];
        } else {
            $projectId = 0;
        }

        if (!empty($data['contractID'])) {
            $contractId = $data['contractID'];
        } else {
            $contractId = 0;
        }

        if (!empty($data['unit'])) {
            $unitId = $data['unit'][0];
        } else {
            $unitId = 0;
        }
        if (!empty($data['building'])) {
            $buildingId = $data['building'][0];
        } else {
            $buildingId = 0;
        }

        if (!empty($data['refNumber'])) {
            $refNumber = $data['refNumber'];
        } else {
            $lastInsertedBill = $this->getLastInsertedBill();
            if (!empty($lastInsertedBill)) {
                $last_refNumber = $lastInsertedBill['ref_no'];
                $refNumber = $last_refNumber + 1;
            } else {
                $refNumber = 1;
            }
        }

        if (!empty($data['requestID'])) {
            $requestID = $data['requestID'];
        } else {
            $requestID = 0;
        }

        try {
            $this->crud->query("insert into invoice_infos (request_id,company_id, employee_id,vendor_id,building_id,apartment_id, due_date, ref_no, bill_date, memo , grand_total,material_grand_total,labor_grand_total,invoice_no, bill_status, approved_to_pay, project_id, contract_id, material_by_owner, is_labor) 
                  values ($requestID,$companyId,$employeeId,$vendorId,$buildingId,$unitId, '" . $dueDate . "','" . $refNumber . "', '" . $billDate . "','" . $memo . "', $grandTotal, $materialGrandTotal, $laborGrandTotal, '" . $invoiceNo . "', '" . $status . "', '" . $approvedToPay . "' , '" . $projectId . "' , '" . $contractId . "' ,'" . $materialByOwner . "','" . $labor . "' )");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $data = $this->getLastInsertedBill();

        if (!empty($_FILES['file01']['name'])) {
            $this->addFile($data['id']);
        }
    }


    private function addFile($billId)
    {
        $file01 = $_FILES['file01']['name'];
        //$file01_ = 1 . substr($$file01, strpos($file01, "."));
        $filePath = "custom/billing/uploads/" . $billId;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        move_uploaded_file($_FILES['file01']['tmp_name'], $filePath . "/" . $file01);
        try {
            $this->crud->query("update invoice_infos set
            file_name =  '" . $file01 . "'
            where id= $billId");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Insert Bill details
     * @param $data
     */
    public
    function InsertBillDetails($data)
    {
        $lastInsertedBill = $this->getLastInsertedBill();
        $last_id = $lastInsertedBill['id'];
        if (!empty($data['requestID'])) {
            $requestID = $data['requestID'];
        } else {
            $requestID = 0;
        }

        if (!empty($data['amount'])) {
            foreach ($data['amount'] as $key => $value) {
                try {
                    $this->crud->query("insert into bills_details (bill_id,request_id,building_id,apartment_id, account_type_id, tax_type, tax, amount ,
                  description, total)
                  values ($last_id,$requestID,'" . $data['building'][$key] . "' ,'" . $data['unit'][$key] . "' ,  '" . $value . "', '" . $data['taxType'][$key] . "', '" . $data['taxAmount'][$key] . "',
                 '" . $data['amount'][$key] . "' , '" . $data['description'][$key] . "', '" . $data['totalAmount'][$key] . "')");
                    $this->crud->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    /**
     * Get last inserted bill
     * @return mixed
     */
    public
    function getLastInsertedBill()
    {
        try {
            $this->crud->query("SELECT * FROM `invoice_infos` 
            ORDER BY `invoice_infos`.`id` DESC
            LIMIT 1 ");
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Update bill by paid amount
     * @param $billID
     * @param $totalPaidAmount
     */
    public
    function updatePaidAmount($billID, $totalPaidAmount)
    {
        try {
            $this->crud->query("update invoice_infos set
            paid_amount =  '" . $totalPaidAmount . "'
            where id= $billID");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Update bill's status
     * @param $status
     * @param $billID
     */
    public
    function updateBillStatus($billID, $status)
    {
        try {
            $this->crud->query("update invoice_infos set
            bill_status =  '" . $status . "'
            where id= $billID");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Insert payment
     * @param $billId
     * @param $data
     */
    public
    function insertPayment($invoice_id, $data)
    {
        $billInfo =$this->getBillByID($invoice_id);
        $vendorId = $billInfo['vendor_id'];
        $paymentDate = $data['paymentDate'];
        $paymentDate = date("Y-m-d", strtotime($paymentDate));
        $totalAmountToPay = $data['grandTotalToPay'];

        $employeeId = $_SESSION['employee_id'];
        $paymentMethod = $data['paymentMethod'];
        $accountType = $data['accountType'];
        if (!empty($data['accountSubType'])) {
            $accountSubType = $data['accountSubType'];
        } else {
            $accountSubType = 0;
        }

        if (!empty($data['checkNumber'])) {
            $checkNumber = $data['checkNumber'];
        } else {
            $checkNumber = '0';
        }

        $memo = $data['memo'];
        try {
            $this->crud->query("insert into payment_infos (invoice_id,payment_date, account_type, account_sub_type,method, check_no, amount, memo,employee_id, vendor_id)
                      values ('" . $invoice_id . "','" . $paymentDate . "','" . $accountType . "' , '" . $accountSubType . "', '" . $paymentMethod . "', '" . $checkNumber . "', '" . $totalAmountToPay . "', '" . $memo . "', $employeeId, $vendorId)");
            $this->crud->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Get all payment methods
     */
    public
    function getPaymentMethods()
    {
        try {
            $this->crud->query("SELECT * FROM payment_methods");
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }


    /**
     * Get all account types
     */
    public
    function getAccountTypes()
    {
        try {
            $this->crud->query("SELECT * FROM account_types");
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get all account sub types by account type id
     * @param $accountTypeID
     * @return mixed
     */
    public
    function getAccountSubTypesByAccountTypeID($accountTypeID)
    {
        try {
            $this->crud->query("SELECT * FROM account_sub_types WHERE account_type_id = :account_type_id");
            $this->crud->bind(":account_type_id", $accountTypeID);
            $rows = $this->crud->resultSet();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get all account sub types by account type id
     * @param $id
     * @return mixed
     */
    public
    function getAccountTypeByID($id)
    {
        try {
            $this->crud->query("SELECT * FROM account_types WHERE id = :id");
            $this->crud->bind(":id", $id);
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Checks if request id exists
     * @param $request_Id
     * @return boolean
     */
    /*    public function isExistRequestId($request_Id)
        {
            try {
                $this->crud->query("SELECT * FROM bills WHERE request_id = :request_id AND request_id != 0");
                $this->crud->bind(":request_id", $request_Id);
                $rows = $this->crud->resultSingle();

                if (!empty($rows)) {
                    $data['bill_id'] = $rows['id'];
                    $this->updateBill($data);
                    $this->updateBillDetails($data);
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $e->getMessage();
            }
        }*/

    /**
     * Get attachments by request id
     * @param $request_id
     * @return mixed
     */
    public
    function getAttachmentsByRequestID($request_id)
    {
        try {
            $this->crud->query("SELECT invoices_attached FROM request_infos WHERE id=:request_id");
            $this->crud->bind(":request_id", $request_id);
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Get last check number
     * @return mixed
     */
    public
    function getLastCheckNumber()
    {
        try {
            $this->crud->query("SELECT check_no FROM payment_infos WHERE check_no is NOT null  ORDER BY `payment_infos`.`id` DESC
            LIMIT 1");
            $rows = $this->crud->resultSingle();
            return $rows;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Update Status for printing
     * @param $fieldName
     * @param $value
     * @param $whereField
     * @param $whereValue
     * @return mixed
     */
    public function updateField($fieldName, $value, $whereField, $whereValue) {
        try {
            $this->crud->query("UPDATE payment_infos set $fieldName = :value where $whereField = :whereValue");
            $this->crud->bind(":value", $value);
            $this->crud->bind(":whereValue", $whereValue);
            $this->crud->execute();
            return $this->crud->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}