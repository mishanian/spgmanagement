<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
$project_ids = ["0"=>"Please Select"];
$contract_ids = [];

if (!empty($_GET['v']) && empty($_GET['pa']) || !empty($_GET['v']) && empty($_GET['va'])) {
    $whereVendor = "vendor_id=" . $_GET['v'];
    $whereTableVendor="vendor_id=" . $_GET['v'];
} else {
    $whereVendor = "true";
    $whereTableVendor="vendor_id is not null";
    // $whereVendor = "vendor_id is not null";
}


if (!empty($_SESSION['company_id'])) {
    $whereCompany = "company_id=" . $_SESSION['company_id'];
} elseif (!empty($_GET['co'])) {
    $whereCompany = "company_id=" . $_GET['co'];
} else {
    die("Company not defined");
}

if (empty($_GET['p']) && !empty($_GET['pa'])) {
    $whereProject = "true";
    $whereVendor = "true";
} else {
    $whereProject = "project_id=".$_GET['p'];
}


/********************** Project */
$sql = "select distinct project_id,`project_name` as project_name, IFNULL(start_date,'-') as start_date, IFNULL(address,'-') as address,  IFNULL(budget,'0') as budget,  IFNULL(PR.end_date,'-') as end_date,  IFNULL(PR.project_type_id,'-') as project_type_id,   IFNULL(PR.project_status_id,'-') as project_status_id,  IFNULL(PR.project_price,'0') as project_price,  IFNULL(paid_amount,'-') as paid_amount,  IFNULL(non_deductible,'-') as non_deductible  from view_project_vendor PR where $whereCompany AND ".$whereVendor."  and $whereProject and project_id IS NOT NULL order by project_name";
// die($sql);
$result = $Crud->query($sql);
$project_ids= $Crud->resultSet();
if (empty($_GET["noselect"])) {
    array_unshift($project_ids, ["project_id"=>"0","project_name"=>"Please Select:"]);
}


if (!empty($_GET['p'])) {
    $whereProject = "project_id=" . $_GET['p'];
} else {
    $whereProject = "project_id is not null";
}



// if (!empty($_GET['v']) && empty($_GET['va'])) {
//     $whereVendor = "vendor_id=" . $_GET['v'];
// } else {
//     // $whereVendor = "vendor_id is not null";
//     $whereVendor = "vendor_id";
// }


$whereContract=""; // Initial
$whereAttContract=""; // Initial
/********************** Proposal */
if (!empty($_GET['l']) && empty($_GET['la'])) {
    $whereContract = " and contract_id=" . $_GET['l']; // No Need to add "AND" elsewhere
    $whereAttContract = " and proposal_id=" . $_GET['l']; // No Need to add "AND" elsewhere
} 

$sql = "select  distinct contract_id, contract_desc as contract_name, CI.project_id, IFNULL(contract_price,0) as contract_price, IFNULL(CI.non_deductible,0) as non_deductible, IFNULL(CI.paid_amount,0) as paid_amount, CI.is_proposal, PR.name as project_name from contract_infos CI left join project_infos PR on CI.project_id=PR.project_id where CI.$whereCompany $whereContract  and  PR.$whereProject and $whereVendor and is_proposal=1  order by contract_desc";
// die($sql);
$result = $Crud->query($sql);
$proposal_ids = $Crud->resultSet();
if (empty($proposal_ids)) {
    $proposal_ids = [];
}

//print_r($proposal_ids);







/********************** Contract */
if (!empty($_GET['c']) && empty($_GET['ca'])) {
    $whereContract = " and contract_id=" . $_GET['c'];
    $whereAttContract = " and contract_id=" . $_GET['c']; // No Need to add "AND" elsewhere
} 

$sql = "select  distinct contract_id, contract_desc as contract_name, CI.project_id, IFNULL(contract_price,0) as contract_price, IFNULL(CI.non_deductible,0) as non_deductible, IFNULL(CI.paid_amount,0) as paid_amount, CI.is_proposal, PR.name as project_name from contract_infos CI left join project_infos PR on CI.project_id=PR.project_id where CI.$whereCompany $whereContract  and  PR.$whereProject and $whereVendor and is_proposal=0 order by contract_desc";
// die($sql);
$result = $Crud->query($sql);
$contract_ids = $Crud->resultSet();
if (empty($contract_ids)) {
    $contract_ids =[];
}

// print_r($contract_ids);



/********************** Invoice */
if (!empty($_GET['i']) && empty($_GET['ia'])) {
    $whereInvoice = "invoice_id=" . $_GET['i'];
} else {
    $whereInvoice = "true";
}

    $sql = "select  distinct invoice_id, invoice_no, invoice_date,  IFNULL(II.amount,0) as amount,  IFNULL(II.paid_amount,0) as paid_amount, VI.company_name as vendor_name from invoice_infos II left join project_infos PR on II.project_id=PR.project_id Left join vendor_infos VI on VI.vendor_id=II.vendor_id where II.$whereCompany and PR.$whereProject $whereContract and $whereInvoice order by invoice_no";
    $result = $Crud->query($sql);
    $invoice_ids = $Crud->resultSet();

if (empty($invoice_ids)) {
    $invoice_ids = [];
}


if (!empty($_GET['y'])) {
    $wherePayment = "id=" . $_GET['y'];
} else {
    $wherePayment = "id is not null";
}

/********************** Payment */
$sql = "select  distinct id as payment_id, payment_date, PI.amount_wo_tax, PI.amount from payment_infos PI left join project_infos PR on PI.project_id=PR.project_id where PI.$whereCompany and  PR.$whereProject $whereContract and $whereInvoice and $wherePayment order by payment_date";
$result = $Crud->query($sql);
$payment_ids = $Crud->resultSet();
if (empty($payment_ids)) {
    $payment_ids = [];
}


/********************** Vendor */
$sql = "select DISTINCT VI.vendor_id, VI.company_name, VI.phone from vendor_infos VI left join view_project_vendor PR on VI.vendor_id=PR.vendor_id where VI.$whereCompany and VI.$whereTableVendor $whereContract and PR.$whereProject order by company_name";
// die($sql);
$result = $Crud->query($sql);
$vendor_ids = $Crud->resultSet();
if (empty($vendor_ids)) {
    $vendor_ids = [];
}

$sql="SELECT IFNULL(SUM(amount),0) FROM payment_infos WHERE $whereProject AND payment_action_id=7 AND material_by_owner=1 AND is_shared=0  and $whereTableVendor";
$result = $Crud->query($sql);
$amount = $Crud->resultField();
// echo "$sql<br>amount=$amount";
// die();
/************************************* Attachment */

if (!empty($_GET['a']) && empty($_GET['aa'])) {
    $whereAttachment = "attachment_id=" . $_GET['a'];
} else {
    $whereAttachment = "attachment_id is not null";
}

$sql = "select attachment_id,`file`, remarks, staff_id, tenant_id, owner_id, parking_id, storage_id, lock_id, employee_id, company_id, building_id, apartment_id, floor_id, lease_id, project_id, contract_id, vendor_id, invoice_id, paintcode_id, equipment_id, appliance_id, proposal_id, assign_employee_id, attachment_status, createddatetime
 from attachment_infos ATT where $whereAttachment and $whereCompany and $whereVendor and ATT.$whereProject $whereAttContract order by `remarks`";
// die($sql);
$result = $Crud->query($sql);
$attachment_ids = $Crud->resultSet();
if (empty($attachment_ids)) {
    $attachment_ids = [];
}

/************** Other Condistions */
if (!empty($_GET['c']) && empty($_GET['ca']) && empty($_GET['pa'])) {
    $project_ids=["project_id"=>$contract_ids[0]['project_id'],"project_name"=>$contract_ids[0]['project_name']];
    $proposal_ids=[];
}

if (!empty($_GET['l']) && empty($_GET['la']) && empty($_GET['pa'])) {
    $project_ids=["project_id"=>$proposal_ids[0]['project_id'],"project_name"=>$proposal_ids[0]['project_name']];
    $contract_ids=[];
    $invoice_ids=[];
    $payment_ids=[];
}


$json = array("p" => $project_ids,"l" => $proposal_ids, "c" => $contract_ids, "i" => $invoice_ids, "y" => $payment_ids, "v" => $vendor_ids, "a"=> $attachment_ids);
echo json_encode($json);
