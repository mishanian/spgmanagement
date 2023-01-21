<?php
session_start();
include_once ("../../../pdo/dbconfig.php");
include_once ("../../../pdo/Class.Building.php");
$DB_building = new Building($DB_con);
include_once ("../../../pdo/Class.Employee.php");
$DB_employee = new Employee($DB_con);
include_once ("../../../pdo/Class.Apt.php");
$DB_apt = new Apt($DB_con);
include_once ("../../../pdo/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);
require('fpdf/fpdf.php');
include "Class.RequestHandler.php";

class PDF extends FPDF {
// Load data
	function LoadData($file) {
		// Read file lines
		$lines = file($file);
		$data  = array();
		foreach ($lines as $line)
			$data[] = explode(';', trim($line));
		return $data;
	}

// Simple table
	function BasicTable($header, $data) {
		// Header
		foreach ($header as $col)
			$this->Cell(40, 7, $col, 1);
		$this->Ln();
		// Data
		foreach ($data as $row) {
			foreach ($row as $col)
				$this->Cell(40, 6, $col, 1);
			$this->Ln();
		}
	}

// Better table
	function ImprovedTable($header, $data) {
		// Column widths
		$w = array(40, 35, 40, 45);
		// Header
		for ($i = 0; $i < count($header); $i++)
			$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
		$this->Ln();
		// Data
		foreach ($data as $row) {
			$this->Cell($w[0], 6, $row[0], 'LR');
			$this->Cell($w[1], 6, $row[1], 'LR');
			$this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R');
			$this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R');
			$this->Ln();
		}
		// Closing line
		$this->Cell(array_sum($w), 0, '', 'T');
	}

// Colored table
	function FancyTable($data) {
		// Colors, line width and bold font
		$this->SetFillColor(255, 0, 0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128, 0, 0);
		$this->SetLineWidth(.3);
		$this->SetFont('', 'B');
		// Header
		$w = array(40, 35, 40, 45);

		// Color and font restoration
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = false;
		foreach ($data as $row) {
			$this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
			$this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
			$this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
			$this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
			$this->Ln();
			$fill = !$fill;
		}
		// Closing line
		$this->Cell(array_sum($w), 0, '', 'T');
	}
}

$user_id     = $_SESSION['UserID'];
$user_level  = $_SESSION['UserLevel'];
$employee_id = $_SESSION['employee_id'];
$companyId   = $_SESSION['company_id'];

/* Object for the RequestHandler class */
$requestHandler = new RequestHandler();
$requestHandler->makeRepoConn();
$requestHandler->makePdoObject("Request", "request");
$requestHandler->makePdoObject("Vendor", "vendor");

$request_id = $_GET["id"];

/* Get all the details of the request
 * Info
 * Material
 * Invoices attached URL
 * Messages - Selected
 * List of recipients
 * If a project - show the project details and contract details
*/

$printData = array();

$printData["info"]       = $requestHandler->get_modal_info($request_id, $user_id, 0);
$printData["recipients"] = $requestHandler->get_recipients($request_id);
$orderData               = $requestHandler->get_editing($request_id, $user_id);

$printData["statusInfo"] = $orderData;

$vendorId = $printData["info"]["vendor_id"];

$contractId = $orderData["contract_id"];
$projectId  = $orderData["project_id"];
$task_type  = $orderData["task_type"];

$contractDetails = array();
$projectDetails  = array();
$billBalance     = 0;
if ($task_type == 2) {
	$contractDetails              = $requestHandler->request->getContractDataByContractId($contractId);
	$printData["contractDetails"] = $requestHandler->request->getContractDataByContractId($contractId);
	$printData["projectDetails"]  = $requestHandler->request->getProjectInfo($projectId);
	$billBalanceDetail            = $requestHandler->request->getBalanceFromBill($contractId)["balance"];
	if ($billBalanceDetail == null) {
		$billBalance = $contractDetails["vendor_contract_price"];
	}
	else {
		$billBalance = $contractDetails["vendor_contract_price"] - $billBalanceDetail;
	}
	$printData["billBalance"] = $billBalance;
}

$materialsData            = $requestHandler->request->getMaterialProvided($request_id);
$invoicesData             = $requestHandler->get_attached_invoices($request_id);
$reportLocationForRequest = $orderData["location"];

$printData["materialsData"] = $materialsData;
$printData["invoicesData"]  = $invoicesData;
$printData["messagesData"]  = array();

/* Messages from the request */
if (array_key_exists("message_id", $_GET)) {
	if (!empty($_GET["message_id"]) && count($_GET["message_id"]) > 0) {
		$messages_id = $_GET["message_id"];
		rsort($messages_id, 1);
		$messageDetails = $requestHandler->get_communications($request_id, $user_id);

		if (count($messageDetails) > 0) {
			foreach ($messageDetails as $communication_message) {
				if (in_array($communication_message["communication_id"], $messages_id)) {
					array_push($printData["messagesData"], $communication_message);
				}
			}

			function messagesSort($item1, $item2) {
				if ($item1['communication_id'] == $item2['communication_id']) return 0;
				return ($item1['communication_id'] < $item2['communication_id']) ? 1 : -1;
			}

			usort($printData["messagesData"], 'messagesSort');
		}
	}
}

// Create me a new pdf object:
$pdf = new PDF();

// Add a page to that object
$pdf->AddPage('L');

$pdf->setleftmargin(10);
$pdf->setX(10);
$pdf->setY(10);

//$pdf->SetTextColor(72, 61, 139);

// Add some text
$pdf->SetFont('Arial', 'BU', 12);
// width, height, text, no border, next line - below & left margin, alignement
$pdf->Cell(0, 10, 'Request #' . $printData["info"]["request_id"], 0, 1, "C");

//$pdf->SetFont('Arial', 'B', 11);
//$pdf->Cell(80, 10, 'Info', 1, 1, "C");

/* Request Detail Table header */
$pdf->SetFillColor(255, 255, 255); // Background color of header


//$pdf->Cell(80, 10, 'Name', 0, 0, "C", true); // First header column

$pdf->SetFont('Arial', 'BU', 11);
$pdf->Cell(80, 10, 'Detail', 0, 1, "L", true); // Second header column

/* Request Detail Table Body */
$pdf->SetFont('Arial', '', 10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Request Type', 0, 0, "L", true); // First header column
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, ucfirst($printData["info"]["request_type"]), 0, 1, "L", true); // Second header column

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Created By', 0, 0, "L", true); // First header column
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, ucfirst($printData["info"]["created_user_name"]), 0, 1, "L", true); // Second header column

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Status', 0, 0, "L", true); // First header column
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, ucwords($printData["info"]["request_status"]), 0, 1, "L", true); // Second header column

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Building ', 0, 0, "L", true); // First header column
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, ucwords($printData["info"]["building_name"]), 0, 1, "L", true); // Second header column

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Address ', 0, 0, "L", true); // First header column
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, ucwords($printData["info"]["building_address"]), 0, 1, "L", true); // Second header column

if ($reportLocationForRequest == "2") {
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(80, 10, 'Apartment ', 0, 0, "L", true); // First header column
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(80, 10, $DB_apt->getUnitNumber($orderData["apartment_id"]), 0, 1, "L", true); // Second header column

	$tenants = $DB_tenant->getTenantViewByAptId($orderData["apartment_id"]);

	if (count($tenants) > 0) {
		$pdf->SetFont('Arial', 'BU', 11);
		$pdf->Cell(80, 10, 'Tenant Details', 0, 1, "L");

		$pdf->Cell(80, 10, 'Name', 0, 0, "L", true); // First header column
		$pdf->Cell(80, 10, 'Phone', 0, 0, "L", true); // Second header column
		$pdf->Cell(80, 10, 'Email', 0, 1, "L", true); // Second header column

		$pdf->SetFont('Arial', '', 10);

		foreach ($tenants as $tenant) {
			$pdf->Cell(80, 10, $tenant["full_name"], 0, 0, "L", true); // First header column
			$pdf->Cell(80, 10, $tenant["mobile"], 0, 0, "L", true); // Second header column
			$pdf->Cell(80, 10, $tenant["email"], 0, 1, "L", true); // Second header column
		}
	}
}

if (isset($orderData["building_id"])) {
	$buildingInfo = $DB_building->getBdInfo($orderData["building_id"]);
	$employeeInfo = $DB_employee->getEmployeeInfo($buildingInfo["employee_id"]);

	if (!empty($employeeInfo)) {
		$pdf->SetFont('Arial', 'BU', 11);
		$pdf->Cell(80, 10, 'Property Manager Details', 0, 1, "L");

		$pdf->SetFont('Arial', '', 10);

		$pdf->Cell(80, 10, "Name", 0, 0, "L", true);
		$pdf->Cell(80, 10, $employeeInfo["full_name"], 0, 1, "L", true);

		$pdf->Cell(80, 10, "Mobile", 0, 0, "L", true);
		$pdf->Cell(80, 10, $employeeInfo["mobile"], 0, 1, "L", true);

		$pdf->Cell(80, 10, "Email", 0, 0, "L", true);
		$pdf->Cell(80, 10, $employeeInfo["email"], 0, 1, "L", true);
	}
}

/* Print the tenants if they are assigned to the request */
if (array_key_exists("tenants", $printData["recipients"]) && count($printData["recipients"]["tenants"]) > 0) {
	$pdf->Ln();
	/* Request Detail Table header */
	$pdf->SetFillColor(240, 248, 255); // Background color of header
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(80, 10, 'Tenant Name', 0, 0, "L", true); // First header column
	$pdf->Cell(80, 10, 'Notified', 0, 1, "C", true); // Second header column

	$pdf->SetFont('Arial', '', 10);

	foreach ($printData["recipients"]["tenants"] as $tenant) {
		$pdf->Cell(80, 10, $tenant["full_name"], 1, 0, "C", true); // First header column
		$pdf->Cell(80, 10, ($tenant["full_name"] == 0) ? "No" : "Yes", 1, 1, "C", true); // Second header column
	}
}

/* Show contract details */
if ($task_type == 2) {
	if (count($printData["contractDetails"]) > 0) {
		$pdf->SetFont('Arial', 'BU', 11);
		$pdf->Cell(80, 10, 'Project / Contract Details', 0, 1, "L");

		$pdf->SetFont('Arial', '', 10);

		/* Project ID */
		$pdf->Cell(80, 10, 'Project ID', 0, 0, "L", true);
		$pdf->Cell(80, 10, $printData["projectDetails"]["project_id"], 0, 1, "L", true);

		/* Project name */
		$pdf->Cell(80, 10, 'Project', 0, 0, "L", true);
		$pdf->Cell(80, 10, $printData["projectDetails"]["name"], 0, 1, "L", true);

		/* Contract Details */
		$pdf->Cell(80, 10, 'Contract Name', 0, 0, "L", true);
		$pdf->Cell(80, 10, $printData["contractDetails"]["contract_desc"], 0, 1, "L", true);

		/* Contract price */
		$pdf->Cell(80, 10, 'Contract Price', 0, 0, "L", true);
		$pdf->Cell(80, 10, "C$ " . $printData["contractDetails"]["vendor_contract_price"], 0, 1, "L", true);

		$pdf->Cell(80, 10, 'Contract Bill Outstanding', 0, 0, "L", true);
		$pdf->Cell(80, 10, "C$ " . $printData["billBalance"], 0, 1, "L", true);
	}
}

/* Material Details */
if (array_key_exists("materialsData", $printData) && strlen($printData["materialsData"]["material_detail"]) > 0) {
	$materials = json_decode($printData["materialsData"]["material_detail"], true);

	if (count($materials) > 0 && strlen($materials[0]["material_name"]) > 0) {
		$pdf->SetFont('Arial', 'BU', 11);
		$pdf->Cell(80, 10, 'Material Details', 0, 1, "L");

		$pdf->Cell(80, 10, 'Name', 1, 0, "C", true);
		$pdf->Cell(80, 10, 'Store URL', 1, 1, "C", true);

		$pdf->SetFont('Arial', '', 10);

		foreach ($materials as $material) {
			$pdf->Cell(80, 10, ucfirst($material["material_name"]), 1, 0, "C", true);
			$pdf->Cell(80, 10, $material["material_url"], 1, 1, "C", true);
		}
	}
}

/* Invoices Details */
//if (array_key_exists("invoicesData", $printData) && strlen($printData["invoicesData"]) > 0) {
//	$pdf->Ln();
//
//	$invoices = json_decode($printData["invoicesData"], true);
//
//	if (count($invoices) > 0 && strlen($invoices[0]) > 0) {
//
//		$pdf->SetFont('Arial', 'B', 11);
//
//		$pdf->Cell(80, 10, 'Invoice Details', 0, 1, "L");
//
//		$pdf->Cell(80, 10, '#', 1, 0, "C", true);
//		$pdf->Cell(80, 10, 'File URL', 1, 1, "C", true);
//
//		$pdf->SetFont('Arial', '', 10);
//
//		foreach ($invoices as $index => $invoice) {
//			$pdf->SetTextColor(0, 0, 0);
//			$pdf->Cell(80, 10, $index + 1, 1, 0, "C", true);
//
//			$pdf->SetTextColor(72, 61, 139);
//			$pdf->Cell(80, 10, 'File', 1, 1, "C", true);
//			$pdf->Link($pdf->getX() + 85, $pdf->getY() - 10, 40, 10, "https://spg.spgmanagement.com/admin/files/" . $invoice);
//		}
//	}
//}

/* Vendor Details */
//if (array_key_exists("vendors", $printData["recipients"]) && count($printData["recipients"]["vendors"]) > 0) {
if ($vendorId != 0) {
	$pdf->SetFont('Arial', 'BU', 11);
	$pdf->Cell(80, 10, 'Vendor Details', 0, 1, "L");

	$pdf->SetFont('Arial', '', 10);

	$vendor = $DB_vendor->getVendorInfo($vendorId);

	$pdf->Cell(80, 10, 'Vendor ID', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["vendor_id"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Company Name', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["company_name"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Registration Name', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["registration_name"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Full Name', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["full_name"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Email', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["email"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Mobile', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["mobile"], 0, 1, "L", true);

	$pdf->Cell(80, 10, 'Address', 0, 0, "L", true);
	$pdf->Cell(80, 10, $vendor["address"], 0, 1, "L", true);
}

/* Message Details */
if (array_key_exists("messagesData", $printData) && count($printData["messagesData"]) > 0) {
	$pdf->Ln();

	$pdf->SetFont('Arial', 'BU', 11);
	$pdf->Cell(80, 10, 'Message Details', 0, 1, "L");

	$pdf->SetFont('Arial', '', 10);


	if ($_SERVER['SERVER_NAME'] == "beaveraittesting.site") {
		$imagePath = "https://beaveraittesting.site/admin/";
	}
	else {
		$imagePath = "https://spgmanagement.com/admin/";
	}

	foreach ($printData["messagesData"] as $message) {
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(80, 10, $message["creator_name"], 0, 0, "L", true);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(80, 10, $message["created_time"], 0, 1, "R", true);

		if ($message["is_image"] == 1) {
			$link = $imagePath . $message["remark"];
			$pdf->Image($link, $pdf->getX(), $pdf->getY(), 30, 10, '', "{$link}");
		}
		else {
			$pdf->MultiCell(250, 10, $message["remark"]);
		}

		$pdf->Ln();
	}
}

$pdf->Ln();

$pdf->Cell(100, 10, "The Job was done by the Vendor : ", 0, 0, "L", false);
$pdf->Cell(100, 10, "Tenant Name: ", 0, 1, "R", false);

$pdf->Cell(30, 10, "Date : ", 0, 1, "L", false);

$pdf->Cell(100, 10, "Vendor Signature : ", 0, 0, "L", false);
$pdf->Cell(100, 10, "Tenant Signature : ", 0, 1, "R", false);


$fileName        = "../../files/requests_pdf/Request_" . $printData["info"]["request_id"] . ".pdf";
$pdfPathForEmail = "https://spgmanagement.com/admin/files/requests_pdf/Request_" . $printData["info"]["request_id"] . ".pdf";

// For beaver Site
if ($_SERVER["HTTP_HOST"] == "beaveraittesting.site") {
	$pdfPathForEmail = "http://beaveraittesting.site/admin/files/requests_pdf/Request_" . $printData["info"]["request_id"] . ".pdf";
}

//$pdf->Output("F", $fileName);
//$pdf->Output("D", $fileName);
$pdf->Output();

/* Below code to forward the pdf to someone (a handyman) */
/* Check if there is an email address passed */
if (array_key_exists("email", $_GET) || array_key_exists("id", $_GET)) {
	include_once("../sendSMSEmail.php");
	$name               = "User";
	$comments           = "";
	$vendorEmailAddress = "info@spg-canada.com";

	if (array_key_exists("vendorId", $_GET) && intval($_GET["vendorId"]) != 0) {
		$vendorEmailAddress = $DB_vendor->getVendorEmail(($_GET["vendorId"]));

		/* Update the vendor id for the request task*/
		$DB_request->updateVendorId($printData["info"]["request_id"], $_GET["vendorId"]);

		if (!$vendorEmailAddress || strlen($vendorEmailAddress) < 1) {
			$vendorEmailAddress = "info@spg-canada.com";
		}
		//$name               = $_GET["vendorName"];
	}
	else {
		if (array_key_exists("name", $_GET)) {
			$nameList = $_GET["name"]; // This is an array
			$name     = implode(",", $nameList);
		}
	}

	if (array_key_exists("comments", $_GET)) {
		$comments = $_GET["comments"];
	}

	if (array_key_exists("email", $_GET) && isset($_GET["email"])) {
		$emailAddress = $_GET["email"];
	}
	else {
		$emailAddress = array();
	}

	array_push($emailAddress, $vendorEmailAddress);

	if (strlen($comments) > 1) {
		$email_text .= " <b><u>Please find the below comments : </u></b><br> <br>";
		$email_text .= $comments;
	}

	$title          = "PDF of Request #" . $printData["info"]["request_id"];
	$subtitle       = "Please find the attached PDF";
	$body1          = $email_text;
	$body2          = "Click the button below when the request is complete.";
	$button_url     = "https://spg.spgmanagement.com/approvereq.php?rid=" . $printData["info"]["request_id"] . "&mker=" . base64_encode("SPGManagementrequesttenanturl");
	$button_content = "Mark the request as complete";
	$subject        = "Request #" . $printData["info"]["request_id"] . " - PDF";

	include_once "../../../pdo/Class.Template.php";
	$template       = new Template();
	$email_template = $template->emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content);

	if (is_array($emailAddress) && count($emailAddress) > 0) {
		foreach ($emailAddress as $emailSingle) {
			SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $emailSingle, $name, $subject, $email_template, $pdfPathForEmail);
		}
	}
}