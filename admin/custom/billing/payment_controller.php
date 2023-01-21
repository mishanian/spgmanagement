<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(!isset($_SESSION)){session_start();}

if (isset($_GET["action"])) {
    include_once("../../../pdo/dbconfig.php");
    include_once ('../../../pdo/Class.Company.php');
    $DB_company = new Company($DB_con);

    include_once ('../../../pdo/Class.Project.php');
    $DB_project = new Project($DB_con);

    include_once ('../../../pdo/Class.Employee.php');
    $DB_employee = new Employee($DB_con);

    include_once ('../../../pdo/Class.Request.php');
    $DB_request = new Request($DB_con);

    include_once ('../../../pdo/Class.Vendor.php');
    $DB_vendor = new Vendor($DB_con);

    if ($_GET["action"] == "sendmail") {
        $emailAddress = $_GET["email"];
        $action_type_id=$_GET['action_type_id'];
        $payment_id=$_GET["pid"];

        //Get Max Amount for employee to sign
        $SelectSql = "select * from payment_infos where id=".$payment_id;
       // die($SelectSql);
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $paymentInfos = $statement->fetchAll()[0];
        //die(var_dump($paymentInfos));
        $max_cheque_approve=$paymentInfos["amount"];
//var_dump($max_cheque_approve);
/* Details Started */

        $project_name = $DB_project->getProjectName($paymentInfos["project_id"]);
        $contract_name = $DB_project->getContactName($paymentInfos["contract_id"]);
        $invoice_no = $paymentInfos["invoice_no"];

        $company_name = $DB_company->getName($paymentInfos["company_id"]);

        $memo = $paymentInfos["memo"];
        $comments = $paymentInfos["comments"];
        if(!empty($paymentInfos["sender_id"])){
            $employee_id = $paymentInfos["sender_id"];
        }else{
            $employee_id = $paymentInfos["employee_id"];
        }

        if ($employee_id>300000) {
            $employee_FullName = $DB_vendor->getVendorInfo($employee_id)['company_name'];
            $employee_info = $DB_vendor->getVendorInfo($employee_id);
        }else{
            $employee_FullName=$DB_employee->getEmployeeName($employee_id);
            $employee_info=$DB_employee->getEmployeeInfo($employee_id);
        }

$employee_level_id=$employee_info['user_level'];
//die("pp=".$paymentInfos["user_level"]);
//die($employee_level_id);
if (!empty($employee_level_id)){
$employee_level_name = $DB_employee->getLevelName($employee_level_id);}else{$employee_level_name="-";}



$contractDetails = $DB_request->getContractDataByContractId($paymentInfos["contract_id"]);
$vendorId = $paymentInfos["vendor_id"];
$vendorDetails = $DB_vendor->getVendorInfo($vendorId);
$vendorName = $vendorDetails['company_name'];


 $Details="<br> Sender : <b>$employee_FullName</b> - Position: <b>$employee_level_name</b> - ID: <b>$employee_id</b></b><br>\n";
$Details .="Project : <b>$project_name</b>  <br>Contract : <b>$contract_name</b> <br>Invoice No. : <b>";
if(!empty($attach)) {
    $Details .="<br><span><a target='_blank' href='https://ilivein.xyz/admin/files/attachments/$attach'>";
}
        $Details .=$invoice_no."</a></b><br>Memo : <b>$memo</b><br>Bill Amount: <b>".$paymentInfos['amount']."</b>";

/* Details Ended */


        if(is_numeric ($emailAddress)) {
        $SelectSql = "select email from employee_infos where company_id=".$_SESSION['company_id']." and can_sign_payment=1 and max_cheque_approve>=".$max_cheque_approve;
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
            $emailAddress_array = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        }else{
            $emailAddress_array=array($emailAddress);
        }


        $emailAddresses = implode(",",$emailAddress_array);
 //       die(var_dump($emailAddresses));


        if(!empty($_GET["reason"])){$reason=$_GET["reason"];}else{$reason="";}
        $resetAction = 0;

        if (isset($_GET["reset"]) && strval($_GET["reset"]) == "true") {
            $resetAction = 1;
            $subject = "Accountant has requested to reset the bill $payment_id";
            $body1 = "Please approve if you want to reset the bill to process it.";
            $body2 = "Reason: $reason";
        }else{
            if($action_type_id ==1 || $action_type_id==2 || $action_type_id==5 ||  $action_type_id==7){$emailsql="email_manager_sent";$subject = ($_SESSION['employee_id']>=300000?"Vendor":"Accountant")." applied for approval $payment_id.";$action_type_id =2;}
            if($action_type_id ==3 || $action_type_id==4){$emailsql="email_vendor_sent";$subject = "Accountant ask vendor for signature. $payment_id";}
            if($action_type_id ==6){$emailsql="email_pickup";$subject = "Accountant ask pickup for signature. $payment_id";}
        //    if($action_type_id ==11){$emailsql="email_reset";}
            $body1 = "You need to sign on a bill for the Account to process it.";
            $body2 = "Click the button below to make your digital signature.";
        }
        if($action_type_id ==11){$emailsql="email_reset";$action_type_id=1;}
        $body1 .= $Details;
        include_once("../sendSMSEmail.php");
        $title = "Signature for the BILL";
        $subtitle = "Your signature is required for processing of Bill!";


        $button_url = "https://www.spgmanagement.com/paymentsignatureadmin.php?id=" . $payment_id . "&action_type_id=$action_type_id&resetact=" . $resetAction . "&ln=" . base64_encode("spgmanagement")."&rns=".rand(10,100000);
        $button_content = "Make Signature";
        $name =$company_name;

//die(var_dump($emailAddress));
        include_once "../../../pdo/Class.Template.php";
        $template = new Template();
        $email_template = $template->emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content);
        $pdfPathForEmail = "";// Just for test, we should make it correct

       // foreach ($emailAddress_array as $emailAddress) {
            $smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $emailAddress_array, $name, $subject, $email_template, $pdfPathForEmail);
       // }
      //  $smtp_log = MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', "mishanian@gmail.com", $name, $subject, $email_template, $pdfPathForEmail);

    //    echo $smtp_log;
 //die(date("H:i:s").$emailAddresses);


        $SelectSql = "update payment_infos set is_sent=1,payment_action_id=$action_type_id, $emailsql='$emailAddresses',reason='$reason',sender_id='".$_SESSION['employee_id']."' where id='" . $payment_id."'";
      //  echo $SelectSql;
      //  die($SelectSql);
        $statement = $DB_con->prepare($SelectSql);
        $result = $statement->execute();


        $SelectSql = "insert into payment_history (payment_id, employee_id, action_type_id, sent_email, reason, create_dt) VALUES (". $payment_id.", ".$_SESSION['employee_id'].", $action_type_id, '$emailAddresses', '$reason', '".date("Y-m-d H:i:s")."')";
      //  die($SelectSql);
        $statement = $DB_con->prepare($SelectSql);
        $result = $statement->execute();



    }






}