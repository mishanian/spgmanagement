<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

include_once ('pdo/dbconfig.php');
include_once ('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once ('pdo/Class.Question.php');
$DB_question = new Question($DB_con);
include_once ('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once ('pdo/Class.Employee.php');
$DB_employee = new Employee($DB_con);




if (isset($_SESSION["company_id"])) {
    $company_id = $_SESSION["company_id"];
} else {
    $company_id = null;
}

include_once "pdo/dbconfig.php";
if (true) {
    if (isset($_POST['submit_inquiry'])) {
        //running robot check
        $captcha = $_POST['g-recaptcha-response'];
        if (!$captcha) {
            echo '<h2>Please check the the captcha(I\'m not a robot) form.</h2>';
            exit;
        }
        $secretKey = '6Lep_ykUAAAAAFKESQmqUL8DTi4cNqo4IclJRitm';
        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);
        if(intval($responseKeys["success"]) !== 1) {
            exit;
        }
        //restore data
        $building_id = $_POST['building_id'];
        $apt_id = $_POST['apt_id'];
        $customer_name = $_POST['customer_name'];
        $customer_email = $_POST['customer_email'];
        $customer_phone = $_POST['customer_phone'];
        $inquiry_content = $_POST['inquiry_content'];
        date_default_timezone_set('America/New_York');
        $today = date("Y-m-d");
        $potential_status = 0;
        $ps_modified_by = null;
        //the source of the question by default of 1-ilivein front end
        if (isset($_POST['from_portal'])) {
            $source_type = 3;
        } else {
            $source_type = 1;
        }

        $job_before_email_done = $DB_question->createQuestion($customer_name, $customer_phone, $customer_email, $inquiry_content, $apt_id, $potential_status, $ps_modified_by, $today, $source_type, $company_id);

        if ($job_before_email_done) {
            include_once "admin/custom/sendSMSEmail.php";
            include_once "pdo/Class.Template.php";
            $template = new Template();
            //email
            $employee_id = $DB_building->getBdInfo($building_id)['employee_id'];
            $employee_name = $DB_employee->getEmployeeName($employee_id);
            //$employee_email = $DB_employee->getEmployeeEmail($employee_id);
            $employee_email = "fishisycho@gmail.com";

            $email_sbj = "spgmanagement.com - A Customer Has Sent an Inquiry";
            $title = "Notification";
            $subtitle = "A customer sent you an inquiry.";
            $name = $employee_name;
            $body1 = "A customer has sent an inquiry to you through ilivin.xyz.";
            $body2 = "Inquiry Detail:";
            $body2 .= "<br>Customer Name: " . $customer_name;
            $body2 .= "<br>Customer Email: " . $customer_email;
            $body2 .= "<br>Customer Phone: " . $customer_phone;
            $body2 .= "<br>Customer Inquity: " . $inquiry_content;
            $button_url = "http://www.beaveraittesting.com/";
            $button_content = "Log in to ilivein";
            $email_content = $template->emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content);

            MySendEmail('info@spgmanagement.com', 'Info - spgmanagement.com', $employee_email, $employee_name, $email_sbj, $email_content);

            header('Location: successful-inquiry.php?building_id='.$building_id);
        }
    }
}