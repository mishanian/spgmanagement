<?php
//die(print_r($_POST));
session_start();
header("Content-disposition: inline;filename='cheque.pdf'");
//die(var_dump($_POST));
include_once("../../../pdo/dbconfig.php");
include_once("../../../pdo/Class.Vendor.php");
include_once '../../../pdo/Class.Employee.php';
include_once '../../../pdo/Class.Company.php';
include_once '../../../pdo/Class.Province.php';
include_once '../../../pdo/Class.Country.php';
$DB_vendor=new Vendor($DB_con);
$DB_employee = new Employee($DB_con);
$DB_province=new Province($DB_con);
$DB_country=new Country($DB_con);
if(!empty($_POST['email_manager_sent'])){$emailAddress=$_POST['email_manager_sent'];}
if(!empty($_POST['email_vendor_sent'])){$emailAddress=$_POST['email_vendor_sent'];}
if(!empty($_POST['email_pickup'])){$emailAddress=$_POST['email_pickup'];}
if(empty($emailAddress)){$emailAddress="";}

if(!empty($_POST['pickup_name'])){$pickup_name=$_POST['pickup_name'];}else{$pickup_name="";}
if(!empty($_POST['pickup_type'])){$pickup_type=$_POST['pickup_type'];}else{$pickup_type="";}
if(!empty($_POST['pickup_id'])){$pickup_id=$_POST['pickup_id'];}else{$pickup_id="";}

if(!empty($_POST['action_type_id'])){
	$action_type_id=$_POST['action_type_id'];
}else{
	if(!empty($_GET['action_type_id'])){
		$action_type_id=$_GET['action_type_id'];
	}else{$action_type_id=0;}
}

if(!empty($_POST['id'])){
	$payment_id=$_POST['id'];
}else{
	if(!empty($_GET['id'])){
		$payment_id=$_GET['id'];
	}else{$payment_id=0;}
}




if(!empty($_SESSION['employee_id'])){$employee_id=$_SESSION['employee_id'];}else{$employee_id="NULL";}
$pdfPathForEmail = "https://spgmanagement.com/admin/files/signatures/";
$pdfPathForPDF="../../files/signatures/";
$digital_sign=$DB_employee->getEmployeeInfo($employee_id)['digital_sign'];
//echo "$pdfPathForEmail -$digital_sign- ";
$updated_action_type_id=$action_type_id+1;
if($action_type_id==1 || $action_type_id==2){$updated_action_type_id=3;}//$action_type_id=8;
if($action_type_id==3 || $action_type_id==4 || $action_type_id==8){$action_type_id=9;$updated_action_type_id=5;}
if($action_type_id==6){$action_type_id=10;$updated_action_type_id=7;}
if($action_type_id==7){$action_type_id=7;$updated_action_type_id=7;}
$payment_id=$_POST["id"];
$SelectSql = "insert into payment_history (payment_id, employee_id, action_type_id, sent_email, pickup_name, pickup_type, pickup_id, create_dt) VALUES (".$payment_id .", $employee_id, $action_type_id, '$emailAddress', '$pickup_name', '$pickup_type', '$pickup_id',  '".date("Y-m-d H:i:s")."')";
$statement = $DB_con->prepare($SelectSql);
$result = $statement->execute();


$UpdateSql = "update payment_infos set payment_action_id=$updated_action_type_id where id=$payment_id";
//die($action_type_id."->".$UpdateSql);
$statement = $DB_con->prepare($UpdateSql);
$result = $statement->execute();


// Add a new record as incom if check is printed

include_once('../../../pdo/Class.Bill.php');
$DB_bill = new Bill($DB_con);
$Payments=$DB_bill->getPaymentByID($payment_id);
if($action_type_id ==5 && $Payments['is_printed']==0 && true==false){
	$qrystr = "SELECT * FROM payment_infos  WHERE id= " . $payment_id;
	$qryresult = $DB_con->query($qrystr);

	$result = $qryresult->fetchAll(PDO::FETCH_ASSOC);
	unset($result[0]['id']); //Remove ID from array
	if($result[0]['payment_inex_id']==2){$result[0]['income']=$result[0]['amount'];$result[0]['expence']=0;$result[0]['payment_inex_id']=1;$result[0]['payment_inex_type_id']=14;} //Expence change to //Income
	elseif($result[0]['payment_inex_id']==1){$result[0]['expence']=$result[0]['amount'];$result[0]['income']=0;$result[0]['payment_inex_id']=2;$result[0]['payment_inex_type_id']=1;}//Income change to //Expence
	$result[0]['payment_action_id']=7;
//	die(var_dump($result));
	$qrystr = " INSERT INTO payment_infos";
	$qrystr .= " ( " .implode(", ",array_keys($result[0])).") ";
	$qrystr .= " VALUES ('".implode("', '",array_values($result[0])). "')";
	$qrystr=str_replace("''","NULL",$qrystr);
//	die(var_dump($qrystr));
	$statement = $DB_con->prepare($qrystr);
	$result = $statement->execute();
//		die(var_dump($result));
}


// FPDF folder is in custom - along with the rotation.php file //
define('FPDF_FONTPATH','font/');
require('../fpdf/fpdf.php');


include_once('../../../pdo/Class.Bill.php');
$DB_bill = new Bill($DB_con);
/* ----------- End of file inclusions  -------------*/

class PDF extends FPDF {
	var $angle = 0;
	var $DB_con;

    public function __construct($DB_con){
        parent::__construct();
        $this->DB_con=$DB_con;
}

	function Header() {
        include_once("../../../pdo/dbconfig.php");
        include_once('../../../pdo/Class.Bill.php');
        $DB_bill = new Bill($this->DB_con);
		global $action_type_id;
		global $payment_id;

        $Payments=$DB_bill->getPaymentByID($payment_id);
     //   die(var_dump($Payments));
	//	die("$action_type_id");
        if ($Payments['is_printed']==1 && $action_type_id!=5){
       //     die("Void");
		//if (array_key_exists("paymentData", $_POST) && intval($_POST["paymentData"]["is_printed"]) == 1) {
			$this->SetFont('Arial', 'B', 50);
			$this->SetTextColor(255, 240, 241);
			$this->RotatedText(90, 120, 'VOID', 40);
		}
	}

	function RotatedText($x, $y, $txt, $angle) {
		//Text rotated around its origin
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}

	function Rotate($angle, $x = -1, $y = -1) {
		if ($x == -1)
			$x = $this->x;
		if ($y == -1)
			$y = $this->y;
		if ($this->angle != 0)
			$this->_out('Q');
		$this->angle = $angle;
		if ($angle != 0) {
			$angle *= M_PI / 180;
			$c     = cos($angle);
			$s     = sin($angle);
			$cx    = $x * $this->k;
			$cy    = ($this->h - $y) * $this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
		}
	}

	function _endpage() {
		if ($this->angle != 0) {
			$this->angle = 0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}

if (!array_key_exists("id", $_POST)) {
	echo json_encode(array("value" => false));
	exit;
}

$paymentPrint = $DB_bill->getPaymentById($payment_id);
$invoicePaid     = $DB_bill->getBillByID($paymentPrint['invoice_id']);
//die(var_dump($paymentPrint));
//die(var_dump($paymentPrint['invoice_id']));
$signatureDoneBy = 0; // 0 is owner ; 1 is vendor

// who signed - vendor / owner
if(array_key_exists('who-signed',$_POST) && !is_null($_POST["who-signed"])){
	$signatureDoneBy = intval($_POST["who-signed"]);
}

$_POST["paymentData"] = $paymentPrint;
$vendors = $DB_vendor->getVendorsList();

function numToWords($num)
{

	$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
	return $f->format($num);


}

$pdf = new PDF($DB_con);

// Add a page to that object
$pdf->AddPage();

$pdf->setleftmargin(10);
$pdf->setTopMargin(10);
/* -------------- Display Variables -------------- */
$paymentDate        = $paymentPrint['payment_date'];
$leftHeading        = numToWords($paymentPrint['amount']);

$rightAmountNumeric = $paymentPrint['amount'];
//die("a=".$invoicePaid['vendor_id']);
$result        = $DB_vendor->getVendorInfo($paymentPrint['vendor_id']);
$vendor        = $result['company_name'];
$cheque_name=$vendor;
if($paymentPrint['cheque_name_id']){$cheque_name=$DB_vendor->getVendorChequeNames($paymentPrint['vendor_id'],$paymentPrint['cheque_name_id']);}
//echo $paymentPrint['vendor_id']."-".$paymentPrint['cheque_name_id'];
//die($cheque_name);
$cheque_name=iconv('UTF-8', 'windows-1252',$cheque_name);

$address=$result['address'].", ".$result['city'].", ".$DB_province->getProvinceName($result['province_id']).", ".$DB_country->getCountryName($result['country_id'])." - ".$result['po_box'];
$vendorAddress = iconv('UTF-8', 'windows-1252',$address );
$vendor=iconv('UTF-8', 'windows-1252', $vendor);
$project_no=$contract_no=$invoide_no="";
$project_id=$paymentPrint['project_id'];
$contract_id=$paymentPrint['contract_id'];
$invoice_id=$paymentPrint['invoice_id'];
$ChequeNo=$paymentPrint['cheque_no'];

list($project_no,$contract_no,$invoice_no)=$DB_bill->getProjectContractNo($project_id,$contract_id,$invoice_id);

/* Memo Name */
//$memoName = "        P".$project_no."/C".$contract_no."/I".$invoice_no."/P$payment_id#$ChequeNo ";
$memoName = "        Ref:$payment_id / Chq#$ChequeNo ";
/* Memo Table Variables */
$billDate          = $invoicePaid['invoice_date'];
$billType          = "Bill";
$billMemo          = $paymentPrint['memo'];
$billGrandTotal    = $invoicePaid['amount'];
$billBalanceDue    = number_format($invoicePaid['amount'] - $invoicePaid['paid_amount'] - $paymentPrint['amount'],2);
//die($invoicePaid['amount'] ."-". $invoicePaid['paid_amount']."=".$billBalanceDue);
$billDiscount      = "";
$billPaymentAmount = $paymentPrint['amount'];

/* -------------- Display Variables -------------- */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 15, "\n");
$pdf->Ln();
/*  -------------------- DATE  --------------------  */
//$pdf->SetXY(-40, 5);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 10, "DATE   " . $paymentDate, 0, 1, "R");
/*  -------------------- Left Heading with Amount in Words  -------------------- */
$pdf->Cell(0, 10, $leftHeading, 0, 0, "L");
/* --------------------  Right amount in numeric  -------------------- */
$pdf->Cell(-11, 10, "**" . $rightAmountNumeric, 0, 1, "R");

/* -------------------- Line break --------------------  */
//$pdf->Ln();
//$pdf->Ln();

/* -------------------- Vendor Name to the left --------------------  */
$pdf->Cell(0, 8, " ", 0, 1, "L");
$pdf->Cell(0, 5, "                 ".$cheque_name, 0, 1, "L");
$pdf->Cell(0, 5, "                 ".$vendorAddress, 0, 1, "L");

/* -------------------- Line break --------------------  */
//$pdf->Ln();
//$pdf->Ln();
$pdf->Cell(0, 3, " ", 0, 1, "L");
/* -------------------- Memo Heading --------------------  */
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(13, 7, "                     ", 0, 0, "L");
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(13, 5, "     ".$paymentPrint['memo'], 0, 1, "L");
$pdf->Cell(13, 7, "          ".$memoName, 0, 1, "L");

/* -------------------- Line break --------------------  */
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
/* -------------------- Vendor Name Under Memo Heading  -------------------- */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, $vendor, 0, 0, "L");
$pdf->Cell(-30, 10, $paymentDate, 0, 1, "R");

/* -------------------- Memo Table headings --------------- */
/* -- th -- */
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, "Date", 0, 0, "L");
$pdf->Cell(20, 10, "Type", 0, 0, "C");
$pdf->Cell(30, 10, "Reference", 0, 0, "C");
$pdf->Cell(40, 10, "Original Amount", 0, 0, "C");
$pdf->Cell(30, 10, "Balance Due", 0, 0, "C");
$pdf->Cell(30, 10, "Discount", 0, 0, "C");
$pdf->Cell(30, 10, "Payment", 0, 1, "C");

/* -- tr -- */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 10, $billDate, 0, 0, "C");
$pdf->Cell(20, 10, $billType, 0, 0, "C");
$pdf->Cell(30, 30, $billMemo, 0, 0, "C");
$pdf->Cell(40, 10, $billGrandTotal, 0, 0, "C");
$pdf->Cell(30, 10, $billBalanceDue, 0, 0, "C");
$pdf->Cell(30, 10, $billDiscount, 0, 0, "C");
$pdf->Cell(30, 10, $billPaymentAmount, 0, 1, "C");


/* ----------------- Cheque Amount label --------------- */
$pdf->Cell(172, 10, "Cheque Amount", 0, 0, "R");
$pdf->Cell(20, 10, $billPaymentAmount, 0, 1, "R");
$pdf->Cell(20, 10, "      ".$memoName, 0, 0, "L");
/* -------------------- Line break --------------------  */
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

/* -------------------- 2nd Memo  Table headings --------------- */
/* -- th -- */
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, "Date", 0, 0, "L");
$pdf->Cell(20, 10, "Type", 0, 0, "C");
$pdf->Cell(30, 10, "Reference", 0, 0, "C");
$pdf->Cell(40, 10, "Original Amount", 0, 0, "C");
$pdf->Cell(30, 10, "Balance Due", 0, 0, "C");
$pdf->Cell(30, 10, "Discount", 0, 0, "C");
$pdf->Cell(30, 10, "Payment", 0, 1, "C");

/* -- tr -- */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 10, $billDate, 0, 0, "C");
//$pdf->Cell(20, 10, $billType, 0, 0, "C");
$pdf->Cell(30, 30, $billMemo, 0, 0, "C");
$pdf->Cell(40, 10, $billGrandTotal, 0, 0, "C");
$pdf->Cell(30, 10, $billBalanceDue, 0, 0, "C");
$pdf->Cell(30, 10, $billDiscount, 0, 0, "C");
$pdf->Cell(30, 10, $billPaymentAmount, 0, 1, "C");

/* ----------------- Cheque Amount label --------------- */
$pdf->Cell(172, 10, "Cheque Amount", 0, 0, "R");
$pdf->Cell(20, 10, $billPaymentAmount, 0, 1, "R");

/* Get the request data from the Print page AJAX request */
if (isset($_POST) && !empty($_POST)) {
	if (array_key_exists("action", $_POST)) {

		/* First Upload the signature image */
		if (isset($_FILES)) {
			$error = false;
			$files = array();

			$uploaddir = "../../files/attachments/";
			foreach ($_FILES as $key => $file) {
				$fileName = "signature_payment_" . $_POST["id"] . "_signedby_" . $signatureDoneBy . ".jpeg";

				if (move_uploaded_file($file['tmp_name'], $uploaddir . $fileName)) {
					$files[$key] = $fileName;
				}
				else {
					$error = true;
				}
			}

			if (!$error) {
				$signatureFileName    = "../../files/signatures/" . $fileName;
				$linkOfSignatureImage = "https://spgmanagement.com/admin/files/signatures/" . $fileName;


				$pdf->Ln();
				$pdf->Image($linkOfSignatureImage, $pdf->getX(), $pdf->getY(), 50, 20, '', "");

				// If the signature is done by vendor - update the is_vendor_signed field
				// Else update the is_signed field
				/* Update the is_signed field in the bill_payments table for the payment ID : $_POST["id"] */
				if($signatureDoneBy == 0){
						$DB_bill->updateField("is_signed", "1", "id", $_POST["id"]);
				}else if($signatureDoneBy == 1){
						$DB_bill->updateField("is_vendor_signed", "1", "id", $_POST["id"]);
				}else{
						$DB_bill->updateField("is_pickup", "1", "id", $_POST["id"]);

						$DB_bill->updateField("pickup_name", $_POST["pickup_name"], "id", $_POST["id"]);
						$DB_bill->updateField("pickup_id", $_POST["pickup_type"], "id", $_POST["id"]);
						$DB_bill->updateField("pickup_id_number", $_POST["pickup_id"], "id", $_POST["id"]);
				}
			}else{
				echo false;
			}
		}
		else {
			/* No files in the Request */

		}

	} // --------- End of array_key_exists("action", $_POST)) Check
}





/* this key is set when this page has to just display the generated PDF (do not store the PDF or download the pdf) */
if (!array_key_exists("action", $_POST)) {
    $Payments=$DB_bill->getPaymentByID($payment_id);

    if ($Payments['is_printed']==0 || $action_type_id==5){
//	if (isset($paymentPrint) && array_key_exists("is_printed", $paymentPrint) && intval($paymentPrint["is_printed"]) == 0) {


		if ($Payments['is_ready_print']==1 || !file_exists($pdfPathForPDF . "signature_payment_" . $_POST["id"]  . "_signedby_" . "0.jpeg")) {$linkOfSignatureImage = $pdfPathForEmail . $digital_sign;}
		else{$linkOfSignatureImage = $pdfPathForPDF . "signature_payment_" . $_POST["id"]  . "_signedby_" . "0.jpeg";}
    //    $linkOfSignatureImage = $pdfPathForEmail . $digital_sign;

		/* Update the is_printed field once the page has been printed (opened) */
		$DB_bill->updateField("is_printed", "1", "id", $_POST["id"]);

		$pdf->Ln();
		$pdf->Image($linkOfSignatureImage, 135, 51, 50, 20, '', "");

	}



	$linkOfVendorSignatureImage = $pdfPathForPDF . "signature_payment_" . $_POST["id"]  . "_signedby_" . "1.jpeg";
	$linkOfPickupSignatureImage = $pdfPathForPDF . "signature_payment_" . $_POST["id"]  . "_signedby_" . "2.jpeg";
	die(var_dump($linkOfVendorSignatureImage));
    if(file_exists($linkOfVendorSignatureImage)){ //$Payments['is_vendor_signed']==1 && 
		$pdf->Image($linkOfVendorSignatureImage, 150, 155, 50, 20, '', "");
	}

	if(file_exists($linkOfPickupSignatureImage)){ //$Payments['is_pickup']==1 && 
		$pdf->Image($linkOfPickupSignatureImage, 150, 250, 50, 20, '', "");
	}




	$fileName = "../../files/payment_proof/payment_proof_".$payment_id.".pdf";
	$pdf->Output($fileName,"F");
	header('location:'.$fileName);
//	$pdf->Output("I");
}
else {
	/* This case when the request comes from AJAX from the Signature page */
	$fileName = "../../files/payment_proof/payment_proof_".$payment_id.".pdf";
	//$fileName = "../../files/PaymentSignature_" . $_POST["id"] . ".pdf";
	$pdf->Output("F", $fileName); //Output("F", $fileName);
}



?>
