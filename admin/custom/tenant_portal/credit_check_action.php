<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../../../pdo/dbconfig.php');
include('../../../pdo/Class.Template.php');
include('../sendSMSEmail.php');
// $_POST['pay_slip_file']=$_FILES['pay_slip_file']['name'];
// $_POST['bank_statement_file']=$_FILES['bank_statement_file']['name'];
$signatureData = $_POST['signatureData'];
unset($_POST["signatureData"]);
unset($_POST["submit"]);
// die("signatureData=$signatureData");
$_POST['userName'] = $_POST['tenant_email'];
$randomPass = randomPassword(9, 1, "lower_case,upper_case,numbers,special_symbols")[0];
$_POST['userPass'] = $randomPass;
$last_id = insertAllPost($DB_con, "credit_check", $_POST, ["form_submit"], ['banking_movingdate']);
$paySlipFileName = $_FILES['pay_slip_file']['name'];


$fileFields = ['pay_slip_file', 'bank_statement_file', 'void_check_file', 'photo1_file', 'photo2_file'];
foreach ($fileFields as $fileField) {
  if (!empty($_FILES[$fileField]['name'])) {
    $returnFileMessage = json_decode(uploadFile($fileField, $fileField . "_" . $last_id, '../../files/credit_check'));
    $fileName = $returnFileMessage->fileName;
    update($DB_con, 'credit_check', [$fileField => $fileName], ['creditcheck_id' => $last_id]);
  }
}

$TemplateObj = new Template();

$tenant_email = $_POST['tenant_email'];
$tenant_name = $_POST['tenant_firstname'] . " " . $_POST['tenant_surname'];
$subject = "The Credit Check is submitted";
$title = "The Credit Check is submitted";
$bodyStaff1 = "A new <a href='https://www.spgmanagement.com/admin/creditcheckedit/" . $last_id . "'>credit check</a> is added.";
$bodyTenant1 = "Your credit check is submitted. You can check the status of credit check online. <br>Your username: <b>" . $_POST['userName'] . "</b><br>Your Password: <b>" . $_POST['userPass'] . "</b>";

if (!empty($_GET['company_id'])) {
  $company_id = $_GET['company_id'];
} else {
  $company_id = 9;
} //SPG Canada

//mail("apply@spg-canada.com","Credit Check","Hi, new <a href='https://www.spgmanagement.com/admin/creditcheckedit/$last_id'>credit check</a> is added.", $headers); //apply@spg-canada.com
$email_template = $TemplateObj->emailTemplate($title, '', 'Staff', $bodyStaff1, '', "https://www.spgmanagement.com/admin/creditcheckedit/$last_id", 'credit check', $company_id, 0);
$smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', 'apply@spg-canada.com', 'Apply', $subject, $email_template); // Send to Manager

$email_template = $TemplateObj->emailTemplate($title, '', $tenant_name, $bodyTenant1, '', 'https://www.spgmanagement.com/admin/home', 'check progress', $company_id, 0);
$smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $tenant_email, $tenant_name, $subject, $email_template); // Send to Tenant


$tenant_signature = "../../files/credit_check/tenant_signature_" . $last_id . ".png";
$encoded_image = explode(",", $signatureData)[1];
$decoded_image = base64_decode($encoded_image);
file_put_contents($tenant_signature, $decoded_image);


// die("start PDF");
?>
<?
require('credit_check_send_sign.php');
require('fpdf.php');
$dwelling_address = $_POST['dwelling_address'];
$dwelling_address = iconv('UTF-8', 'windows-1252', $dwelling_address);

$dwelling_apt = $_POST['dwelling_apt'];
$dwelling_apt = iconv('UTF-8', 'windows-1252', $dwelling_apt);

$dwelling_city = $_POST['dwelling_city'];
$dwelling_city = iconv('UTF-8', 'windows-1252', $dwelling_city);

$dwelling_province = $_POST['dwelling_province'];
$dwelling_province = iconv('UTF-8', 'windows-1252', $dwelling_province);

$dwelling_postalcode = $_POST['dwelling_postalcode'];
$dwelling_postalcode = iconv('UTF-8', 'windows-1252', $dwelling_postalcode);


$tenant_surname = $_POST['tenant_surname'];
$tenant_surname = iconv('UTF-8', 'windows-1252', $tenant_surname);

$tenant_firstname = $_POST['tenant_firstname'];
$tenant_firstname = iconv('UTF-8', 'windows-1252', $tenant_firstname);

$tenant_driverid = $_POST['tenant_driverid'];
$tenant_driverid = iconv('UTF-8', 'windows-1252', $tenant_driverid);

$tenant_passportid = $_POST['tenant_passportid'];
$tenant_passportid = iconv('UTF-8', 'windows-1252', $tenant_passportid);

$tenant_dateofbirth = $_POST['tenant_dateofbirth'];
$tenant_dateofbirth = iconv('UTF-8', 'windows-1252', $tenant_dateofbirth);

$tenant_sinno = $_POST['tenant_sinno'];
$tenant_sinno = iconv('UTF-8', 'windows-1252', $tenant_sinno);


$tenant_gender = "";
if (isset($_POST['tenant_gender']))
  $tenant_gender = $_POST['tenant_gender'];

$tenant_status = "";
if (isset($_POST['tenant_status']))
  $tenant_status = $_POST['tenant_status'];

$tenant_address = $_POST['tenant_address'];
$tenant_address = iconv('UTF-8', 'windows-1252', $tenant_address);

$tenant_apt = $_POST['tenant_apt'];
$tenant_apt = iconv('UTF-8', 'windows-1252', $tenant_apt);

$tenant_tel = $_POST['tenant_tel'];
$tenant_tel = iconv('UTF-8', 'windows-1252', $tenant_tel);

$tenant_city = $_POST['tenant_city'];
$tenant_city = iconv('UTF-8', 'windows-1252', $tenant_city);

$tenant_province = $_POST['tenant_province'];
$tenant_province = iconv('UTF-8', 'windows-1252', $tenant_province);

$tenant_postalcode = $_POST['tenant_postalcode'];
$tenant_postalcode = iconv('UTF-8', 'windows-1252', $tenant_postalcode);

$tenant_howlong = $_POST['tenant_howlong'];
$tenant_howlong = iconv('UTF-8', 'windows-1252', $tenant_howlong);

$tenant_email = $_POST['tenant_email'];
$tenant_email = iconv('UTF-8', 'windows-1252', $tenant_email);

$tenant_landlord = $_POST['tenant_landlord'];
$tenant_landlord = iconv('UTF-8', 'windows-1252', $tenant_landlord);

$tenant_onlease = $_POST['tenant_onlease'];
$tenant_onlease = iconv('UTF-8', 'windows-1252', $tenant_onlease);

$tenant_tellandlord = $_POST['tenant_tellandlord'];
$tenant_tellandlord = iconv('UTF-8', 'windows-1252', $tenant_tellandlord);

$tenant_endlease = $_POST['tenant_endlease'];
$tenant_endlease = iconv('UTF-8', 'windows-1252', $tenant_endlease);

$tenant_rent = $_POST['tenant_rent'];
$tenant_rent = iconv('UTF-8', 'windows-1252', $tenant_rent);

$tenant_nationality = $_POST['tenant_nationality'];
$tenant_nationality = iconv('UTF-8', 'windows-1252', $tenant_nationality);


$dependent_number = $_POST['dependent_number'];
$dependent_number = iconv('UTF-8', 'windows-1252', $dependent_number);

$dependent_gender = $_POST['dependent_gender'];
$dependent_gender = iconv('UTF-8', 'windows-1252', $dependent_gender);

$dependent_age = $_POST['dependent_age'];
$dependent_age = iconv('UTF-8', 'windows-1252', $dependent_age);


$employment_name = $_POST['employment_name'];
$employment_name = iconv('UTF-8', 'windows-1252', $employment_name);

$employment_tel = $_POST['employment_tel'];
$employment_tel = iconv('UTF-8', 'windows-1252', $employment_tel);

$employment_address = $_POST['employment_address'];
$employment_address = iconv('UTF-8', 'windows-1252', $employment_address);

$employment_unit = $_POST['employment_unit'];
$employment_unit = iconv('UTF-8', 'windows-1252', $employment_unit);

$employment_city = $_POST['employment_city'];
$employment_city = iconv('UTF-8', 'windows-1252', $employment_city);

$employment_province = $_POST['employment_province'];
$employment_province = iconv('UTF-8', 'windows-1252', $employment_province);

$employment_postalcode = $_POST['employment_postalcode'];
$employment_postalcode = iconv('UTF-8', 'windows-1252', $employment_postalcode);

$employment_occupation = $_POST['employment_occupation'];
$employment_occupation = iconv('UTF-8', 'windows-1252', $employment_occupation);

$employment_salary = $_POST['employment_salary'];
$employment_salary = iconv('UTF-8', 'windows-1252', $employment_salary);

$employment_howlong = $_POST['employment_howlong'];
$employment_howlong = iconv('UTF-8', 'windows-1252', $employment_howlong);

$employment_other = $_POST['employment_other'];
$employment_other = iconv('UTF-8', 'windows-1252', $employment_other);


$employment_paystub = "";
if (isset($_POST['employment_paystub']))
  $employment_paystub = $_POST['employment_paystub'];

$banking_name = $_POST['banking_name'];
$banking_name = iconv('UTF-8', 'windows-1252', $banking_name);

$banking_address = $_POST['banking_address'];
$banking_address = iconv('UTF-8', 'windows-1252', $banking_address);

$banking_accountno = $_POST['banking_accountno'];
$banking_accountno = iconv('UTF-8', 'windows-1252', $banking_accountno);

$banking_transit = $_POST['banking_transit'];
$banking_transit = iconv('UTF-8', 'windows-1252', $banking_transit);

$banking_institution = $_POST['banking_institution'];
$banking_institution = iconv('UTF-8', 'windows-1252', $banking_institution);

$banking_tel = $_POST['banking_tel'];
$banking_tel = iconv('UTF-8', 'windows-1252', $banking_tel);

//$banking_creditcard = $_POST['banking_creditcard'];
$banking_company = $_POST['banking_company'];
$banking_company = iconv('UTF-8', 'windows-1252', $banking_company);

//$banking_expire = $_POST['banking_expire'];
$banking_limit = $_POST['banking_limit'];
$banking_limit = iconv('UTF-8', 'windows-1252', $banking_limit);


$banking_payment = "";
if (isset($_POST['banking_payment']))
  $banking_payment = $_POST['banking_payment'];

$banking_insurance = $_POST['banking_insurance'];
$banking_insurance = iconv('UTF-8', 'windows-1252', $banking_insurance);

$banking_policyno = $_POST['banking_policyno'];
$banking_policyno = iconv('UTF-8', 'windows-1252', $banking_policyno);

$banking_insurancetype = "";
if (isset($_POST['banking_insurancetype']))
  $banking_insurancetype = $_POST['banking_insurancetype'];

$banking_hydroquebec = $_POST['banking_hydroquebec'];
$banking_hydroquebec = iconv('UTF-8', 'windows-1252', $banking_hydroquebec);

$banking_active = "";
if (isset($_POST['banking_active']))
  $banking_active = $_POST['banking_active'];

$banking_movingdate = $_POST['banking_movingdate'];
$banking_movingdate = iconv('UTF-8', 'windows-1252', $banking_movingdate);

$question_crime = "";
if (isset($_POST['question_crime']))
  $question_crime = $_POST['question_crime'];

$question_bankruptcy = "";
if (isset($_POST['question_bankruptcy']))
  $question_bankruptcy = $_POST['question_bankruptcy'];

$question_file = "";
if (isset($_POST['question_file']))
  $question_file = $_POST['question_file'];

$question_declined = "";
if (isset($_POST['question_declined']))
  $question_declined = $_POST['question_declined'];

$question_creditcompany = "";
if (isset($_POST['question_creditcompany']))
  $question_creditcompany = $_POST['question_creditcompany'];
$question_creditcompany = iconv('UTF-8', 'windows-1252', $question_creditcompany);

$agent_name = "";
if (isset($_POST['agent_name']))
  $agent_name = $_POST['agent_name'];

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(200, 5, "Credit Investigation", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(190, 5, "This is not a lease that binds the Applicant with the Landlord , but an invitation to sign such residential or commercial Lease agreement.", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(190, 4, "Dwelling ID", 1, 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(45, 3.5, "Applying Dwelling Address\n" . iconv('UTF-8', 'windows-1252', "Adresse logements Demandé"), "L");
$pdf->SetXY($x + 45, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, $dwelling_address, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Apt\n" . "Apt", 0);
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 7, $dwelling_apt, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "City\n" . "Ville", "L");
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $dwelling_city, "", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(15, 3.5, "Province\n" . "Province", "");
$pdf->SetXY($x + 15, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $dwelling_province, "", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(20, 3.5, "Postal Code\n" . "Code Postal", "");
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 7, $dwelling_postalcode, "R", 1, 'L');



$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(190, 4, "Prospective Tenant", 1, 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Surname\n" . "Nom de Famille", "L");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_surname, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Driver ID\n" . "Permis de Conduire", 0);
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $tenant_driverid, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Date of Birth\n" . "Date de Naissance", 0);
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_dateofbirth, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "First Name\n" . iconv('UTF-8', 'windows-1252', "Prénom"), "L");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_firstname, "", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Passport ID\n" . "Passeport", "");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $tenant_passportid, "", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "SIN No.\n" . "No. de NAS", "");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_sinno, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Gender\n" . iconv('UTF-8', 'windows-1252', "Sexe"), "BL");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_gender, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Status\n" . iconv('UTF-8', 'windows-1252', "État Civil"), "B");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $tenant_status, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Nationality\n" . iconv('UTF-8', 'windows-1252', "Nationalité"), "B");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $tenant_nationality, "BR", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, "Dependents \ " . iconv('UTF-8', 'windows-1252', "Les Personnes à Change"), "LR", 1, 'L');
$pdf->SetXY($x, $y + 5);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Number\n" . "Nombre", "LB");
$pdf->SetXY($x + 30, $y + 5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $dependent_number, "B", 0, 'L');


$pdf->SetXY($x + 60, $y + 5);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Gender\n" . "Sexe", "B");
$pdf->SetXY($x + 90, $y + 5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $dependent_gender, "B", 0, 'L');


$pdf->SetXY($x + 130, $y + 5);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Age\n" . iconv('UTF-8', 'windows-1252', "Âge"), "B");
$pdf->SetXY($x + 160, $y + 5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $dependent_number, "BR", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Current Address\n" . "Adresse Actuelle", "L");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $tenant_address, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Apt\n" . "Apt", 0);
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $tenant_apt, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Tel\n" . iconv('UTF-8', 'windows-1252', "Tél"), 0);
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $tenant_tel, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "City\n" . "Ville", "L");
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $tenant_city, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(15, 3.5, "Province\n" . "Province", 0);
$pdf->SetXY($x + 15, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $tenant_province, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(20, 3.5, "Postal Code\n" . "Code Postal", 0);
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 7, $tenant_postalcode, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(50, 3.5, "How long have lived there\n" . iconv('UTF-8', 'windows-1252', "Depuis combien de temps y résider"), "L");
$pdf->SetXY($x + 50, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $tenant_howlong, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "E-Mail\n" . iconv('UTF-8', 'windows-1252', "Curriel Électronique"), 0);
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, $tenant_email, "R", 1, 'L');



$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "Name of the Landlord\n" . iconv('UTF-8', 'windows-1252', "Nom du Propriétaire"), "L");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, $tenant_landlord, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "Your Name on the Lease?\n" . iconv('UTF-8', 'windows-1252', "Votre nom sur le bail?"), 0);
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $tenant_onlease, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(50, 3.5, "Tel of the Landlord/Superintenant\n" . iconv('UTF-8', 'windows-1252', "Tél du Propriétaire/Directeur"), "BL");
$pdf->SetXY($x + 50, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $tenant_tellandlord, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "End of Current Lease Date\n" . "Date de Fin du Bail Actuel", "B");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $tenant_endlease, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(20, 3.5, "Current Rent\n" . "Loyer Actuel", "B");
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $tenant_rent, "BR", 1, 'L');



$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(190, 4, "Employment", 'BRL', 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "Name of Employer\n" . "Nom de l'Employeur", "L");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $employment_name, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Tel\n" . iconv('UTF-8', 'windows-1252', "Tél"), 0);
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $employment_tel, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "Company Address\n" . iconv('UTF-8', 'windows-1252', "Adresse de la Société"), "L");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 7, $employment_address, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Unit\n" . "Unite", 0);
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $employment_unit, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "City\n" . "Ville", "L");
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $employment_city, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(15, 3.5, "Province\n" . "Province", 0);
$pdf->SetXY($x + 15, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $employment_province, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(20, 3.5, "Postal Code\n" . "Code Postal", 0);
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 7, $employment_postalcode, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(35, 3.5, "Occupational Title\n" . "Titre de la Profession", "L");
$pdf->SetXY($x + 35, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $employment_occupation, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(25, 3.5, "Monthly Salary\n" . "Salaire Mensuel", 0);
$pdf->SetXY($x + 25, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 7, $employment_salary, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(55, 3.5, "How long have you worked there\n" . iconv('UTF-8', 'windows-1252', "Combien de temps avez-vous travaillé"), 0);
$pdf->SetXY($x + 55, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $employment_howlong, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(40, 3.5, "Other resource of Income\n" . "Autre ressource de revenu", "L");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, $employment_other, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(50, 3.5, "Do you have the most recent pay-stub\n" . iconv('UTF-8', 'windows-1252', "Avez-vous le plus récenttalon de paie"), 0);
$pdf->SetXY($x + 50, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, $employment_paystub, "R", 1, 'L');



$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(190, 4, "Banking", 1, 1, 'L');



$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(27, 3.5, "Name of Bank\n" . "Nom de la Banque", "L");
$pdf->SetXY($x + 27, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(43, 7, $banking_name, 0, 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(50, 3.5, "Bank Branch Address\n" . "Adresse de la Succursale de Banque", 0);
$pdf->SetXY($x + 50, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 7, $banking_address, "R", 1, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(27, 3.5, "Account No.\n" . iconv('UTF-8', 'windows-1252', "Numéro de Compte"), "BL");
$pdf->SetXY($x + 27, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(23, 7, $banking_accountno, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(26, 3.5, "Transit Number\n" . iconv('UTF-8', 'windows-1252', "Numéro de Transit"), "B");
$pdf->SetXY($x + 26, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(24, 7, $banking_transit, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(32, 3.5, "Institutional Number\n" . iconv('UTF-8', 'windows-1252', "Numéro de l'Institution"), "B");
$pdf->SetXY($x + 32, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(23, 7, $banking_institution, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Tel\n" . iconv('UTF-8', 'windows-1252', "Tél"), "B");
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 7, $banking_tel, "BR", 1, 'L');



$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(45, 3.5, "Credit Card Company\n" . iconv('UTF-8', 'windows-1252', "Compagnie de Carte de Crédit"), "L");
$pdf->SetXY($x + 45, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, $banking_company, "", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(25, 3.5, "Credit Limit\n" . iconv('UTF-8', 'windows-1252', "Limite de Crédit"), "");
$pdf->SetXY($x + 25, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $banking_limit, "", 0, '');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(10, 3.5, "Type\n" . "Type", "");
$pdf->SetXY($x + 10, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $banking_payment, "R", 1, 'L');



$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(50, 3.5, "Name of Insurance Company\n" . iconv('UTF-8', 'windows-1252', "Nom de la Compagnie d'Assurance"), "TL");
$pdf->SetXY($x + 50, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $banking_insurance, "T", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Policy No.\n" . iconv('UTF-8', 'windows-1252', "Numéro de Police"), "T");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 7, $banking_policyno, "T", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "Type of Insurance\n" . "Type d'Assurance", "T");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $banking_insurancetype, "TR", 1, 'L');



$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(30, 3.5, "HydroQuec Account #\n" . "No. de Compte Hydro", "BL");
$pdf->SetXY($x + 30, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 7, $banking_hydroquebec, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(80, 3.5, "Account is active on the current above mentioned address\n" . iconv('UTF-8', 'windows-1252', "Compte est actif à l'adresse mentionnée ci-dessus en cours"), "B");
$pdf->SetXY($x + 80, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 7, $banking_active, "B", 0, 'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(27, 3.5, "Moving Date\n" . "Date d'Occupation", "B");
$pdf->SetXY($x + 27, $y);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(23, 7, $banking_movingdate, "BR", 1, 'L');




$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(190, 4, "Questions", 1, 1, 'L');



$pdf->SetFont('Arial', '', 8);
$pdf->Cell(60, 3, "Have you ever been convicted of a crime? ", "L", 0, 'L');
$pdf->Cell(130, 3, $question_crime, "R", 1, 'L');


$pdf->Cell(50, 3, "Have you ever declared bankruptcy? ", "L", 0, 'L');
$pdf->Cell(140, 3, $question_bankruptcy, "R", 1, 'L');

$pdf->Cell(95, 3, "Have you ever been opening a file in Regie du Logement of Quebec? ", "L", 0, 'L');
$pdf->Cell(95, 3, $question_file, "R", 1, 'L');

$pdf->Cell(115, 3, "Have you ever been declined by a Credit Card Company, if yes, which credit card. ", "L", 0, 'L');
$pdf->Cell(75, 3, $question_declined, "R", 1, 'L');

$pdf->Cell(60, 3, "If yes, name of the Credit Card Company. ", "L", 0, 'L');
$pdf->Cell(130, 3, $question_creditcompany, "R", 1, 'L');

// $pdf->Cell(60,3,"Agent Name","L",0,'L');
// $pdf->Cell(130,3,$agent_name,"R",1,'L');


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 4, "Authentication", 1, 1, 'L');


$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(190, 3, "I declare and certify that the information provided herein is true. I authorize the landlord to obtain through any credit agency, personal information regarding me, most notably my credit history, financial situation and payment history, and this from any records, or persons having financial or contractual relationships with me, and from all persons whose name' have supplied as references. I authorize the landlord to communicate any such information regarding me to the SPG Canada, 1-514-9373529 and to any mandate designated by the latter in the course of a credit investigation. The landlord will not receive such information after the end of my contract, but may continue to communicate such information for the purpose of updating and maintaining the integrity of credit investigation agency services and the credit authorization process. A fee of $25 will be charged to the applicant if the credit check is not approved by the Landlord.", "RL");
$pdf->Cell(190, 1, "", 'RL', 1, 'L');
$pdf->MultiCell(190, 3, iconv('UTF-8', 'windows-1252', "Je déclare et certifie que les informations fournies dans ce document est vrai. Je autoriser le propriétaire à obtenir par un organisme de crédit, les renseignements personnels me concernant, notamment mes antécédents de crédit, la situation financière et les antécédents de paiement, et ce à partir des registres ou des personnes ayant des relations financières ou contractuelles avec moi, et de toutes les personnes dont le nom «ont fourni des références. J'autorise le locateur à communiquer ces renseignements à mon sujet au Groupe de SPG Canada, et 1-514-9373529 à tout mandat désigné par ce dernier dans le cadre d'une enquête de crédit. Le propriétaire ne sera pas recevoir de telles informations après la fin de mon contrat, mais peut continuer à communiquer de telles informations dans le but de mettre à jour et le maintien de l'intégrité des services de crédit et d'enquête agence le processus d'autorisation de crédit. Des frais de 25 $ seront facturés au demandeur si la vérification de crédit ne est pas approuvé par le propriétaire."), "RL");

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, "Prospective Tenant ", "RL", 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 6, "Signature ", "BL", 0, 'L');
$pdf->Cell(20);
$pdf->Cell(20, 6, $pdf->Image($tenant_signature, $pdf->GetX(), $pdf->GetY() - 10, 40), 0, 0, 'L', false);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 6, "", "B", 0, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, "Date  " . date("Y/m/d"), "B", 0, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 6, "", "BR", 0, 'L');





//$pdf->Output();
$pdf->Output('F', '../../files/credit_check/creditcheck_' . $last_id . '_1.pdf'); //'Credit Investigation.pdf');

// die();


// $ss = new send_sign();
// $result=$ss->request_signature_on_a_document($tenant_email, "Mr / Ms", '../../files/credit_check/creditcheck_' .$last_id.'_1.pdf', "Credit Investigation.pdf");
// echo($result);
header("Location: credit_check_approve.php");
exit;


/*
$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(20,6,"Gender\n" . "Sexe","BL");
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,12,$tenant_gender,"RB",0,'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(20,6,"Status\n" . iconv('UTF-8', 'windows-1252', "État Civil"),"B");
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,12,$tenant_status,"BR",0,'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(110,5,"Dependents \ " . iconv('UTF-8', 'windows-1252', "Les Personnes à Change"),"R",1,'L');
$pdf->SetXY($x, $y + 5);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(15,3.5,"Number\n" . "Nombre","B");
$pdf->SetXY($x + 15, $y + 5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(25,7,$dependent_number,"B",0,'L');


$pdf->SetXY($x + 40, $y + 5);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(15,3.5,"Gender\n" . "Sexe","B");
$pdf->SetXY($x + 55, $y + 5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(25,7,$dependent_gender,"B",0,'L');


$pdf->SetXY($x + 80, $y + 5);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(10,3.5,"Age\n" . iconv('UTF-8', 'windows-1252', "Âge"),"B");
$pdf->SetXY($x + 90, $y + 5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,7,$dependent_number,"BR",1,'L');
*/


/*
$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(40,3.5,"Credit Card Number\n" . iconv('UTF-8', 'windows-1252', "Numéro de la carte de crédit"),"L");
$pdf->SetXY($x + 40, $y);
$pdf->SetFont('Arial','',10);
$pdf->Cell(60,7,"",0,0,'L');


$y = $pdf->GetY();
$x = $pdf->GetX();
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(20,3.5,"Expire Date\n" . "Date d'Exp",0);
$pdf->SetXY($x + 20, $y);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,7,"","R",1,'L');
*/

function insertAllPost($DB_con, $table, $array, $notInclude, $dateFields)
{
  $query = "INSERT INTO " . $table;
  $fis = array();
  $vas = array();
  foreach ($array as $field => $val) {
    if (!in_array($field, $notInclude)) {
      $fis[] = "`$field`";
      if (in_array($field, $dateFields) && $val == null) {
        $vas[] = "'0000-00-00'";
      } else {
        $vas[] = "'" . str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($val)) . "'";
      }
    }
  }
  $query .= " (" . implode(", ", $fis) . ") VALUES (" . implode(", ", $vas) . ") ";

  $stmt = $DB_con->prepare($query);
  try {
    if ($stmt->execute())
      return $DB_con->lastInsertId();
    else return false;
  } catch (Exception $e) {
    error_log($e->getMessage() . "\n" . $query . "\n");
    die("wrong data. please try again. you may contact <a href='mailto:techsupport@spgmanagement.com'>techsupport@spgmanagement.com</a> for assitance. " . date("Y-m-d H:i:s"));
    return false;
  }
}

function uploadFile($fileFieldName, $newFileNameWOExt, $uploadFolder)
{
  if (isset($_FILES[$fileFieldName])) {
    $errors = array();
    $file_name = $_FILES[$fileFieldName]['name'];
    $file_size = $_FILES[$fileFieldName]['size'];
    $file_tmp = $_FILES[$fileFieldName]['tmp_name'];
    //  $file_type=$_FILES[$fileFieldName]['type'];


    //$rawBaseName = pathinfo($newFileNameWOExt, PATHINFO_FILENAME );
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $counter = 1;
    $file_name = $newFileNameWOExt . "_" . $counter . '.' . $file_ext;;

    while (file_exists("$uploadFolder/" . $newFileNameWOExt . "_" . $counter . '.' . $file_ext)) {
      $counter++;
      $file_name = $newFileNameWOExt . "_" . $counter . '.' . $file_ext;
    };

    if (empty($errors) == true) {
      move_uploaded_file($file_tmp, "$uploadFolder/" . $file_name);
      return json_encode(['fileName' => $file_name, 'fileExt' => $file_ext, '$fileSize' => $file_size]);
    } else {
      return json_encode(["error" => $errors]);
    }
  }
}

function update($DB_con, $table, $data, $where)
{
  $param = [];
  $query = 'UPDATE `' . $table . '` SET ';

  foreach ($data as $key => $value) {
    $query .= '`' . $key . '` = :' . $key . ',';
    $param[":$key"] = $value;
  }
  $query = substr($query, 0, -1);
  if (count($where) > 0) {
    $query .= ' WHERE ';
    foreach ($where as $key => $value) {
      $query .= '`' . $key . '` = :' . $key . ',';
      $param[":$key"] = $value;
    }
    $query = substr($query, 0, -1);
  }

  $update = $DB_con->prepare($query);
  $update->execute($param);
}


function randomPassword($length, $count, $characters)
{

  // $length - the length of the generated password
  // $count - number of passwords to be generated
  // $characters - types of characters to be used in the password

  // define variables used within the function
  $symbols = array();
  $passwords = array();
  $used_symbols = '';
  $pass = '';

  // an array of different character types
  $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
  $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $symbols["numbers"] = '1234567890';
  $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

  $characters = explode(",", $characters); // get characters types to be used for the passsword
  foreach ($characters as $key => $value) {
    $used_symbols .= $symbols[$value]; // build a string with all characters
  }
  $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

  for ($p = 0; $p < $count; $p++) {
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
      $n = rand(0, $symbols_length); // get a random character from the string with all characters
      $pass .= $used_symbols[$n]; // add the character to the password string
    }
    $passwords[] = $pass;
  }

  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $passwords = substr(str_shuffle($chars), 0, 8);

  return $passwords; // return the generated password
}

//$my_passwords = randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
// generate one password using 5 upper and lower case characters
//randomPassword(5,1,"lower_case,upper_case");
// generate three passwords using 10 lower case characters and numbers
//randomPassword(10,3,"lower_case,numbers");
// generate five passwords using 12 lower case and upper case characters, numbers and special symbols
//randomPassword(12,5,"lower_case,upper_case,numbers,special_symbols");

?>