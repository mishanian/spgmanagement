<?php
session_start();
error_reporting(E_ALL);
include_once('../../pdo/dbconfig.php');
include_once('../../pdo/Class.LeasePayment.php');
$DB_ls_payment = new LeasePayment($DB_con);
include_once('../../pdo/Class.Payment.php');
$DB_payment = new Payment($DB_con);
if (!empty($_POST)) {
    $forward_recipient_name = $_POST['forward_recipient_name'];
    $forward_recipient_email = $_POST['forward_recipient_email'];
    $forward_recipient_mobile = $_POST['forward_recipient_mobile'];
    $forward_message = $_POST['forward_message'];
    $payment_amount = floatval(number_format($_POST['payment_amount'], 2, '.', ''));
    $post_product = $_POST['product'];
}

// var_dump($_POST);
// die(var_dump($_POST));
// $forward_recipient_name = "Mehran";
// $forward_recipient_email = "mishanian@gmail.com";
// $forward_recipient_mobile = "514923356";
// $forward_message = "test forward";
// $payment_amount = 1500;
// $post_product="Lease";

if ($post_product == 'Services') {
    # --------------------------- Services PAYMENT ----------------------------
    $user_id = $_POST['user_id'];
    $service_type = $_POST['service_type'];
    $service_buy_count = $_POST['service_buy_count'];
    $service_price = $_POST['service_price'];
    $data = 'service_type=' . $service_type . '&service_buy_count=' . $service_buy_count . '&service_price=' . $service_price;
    $user_id = $_SESSION['UserID'];
    $product = 'services';
    $production = 'Services in ilvein.xyz';
}

if ($post_product == 'Kijiji') {
    # --------------------------- KIJIJI PAYMENT ----------------------------
    $slot_count = $_POST['slot_count'];
    $slots_period = $_POST['slots_period'];
    $employee_id = $_POST['employee_id'];
    $slots_price = $_POST['slots_price'];
    $employee_name = $_POST['employee_name'];
    $data = 'slot_count=' . $slot_count . '&slots_period=' . $slots_period . '&slots_price=' . $slots_price;
    $user_id = $_SESSION['employee_id'];
    $product = 'kijiji';
    $production = 'Kijiji advertisement slots';
}

if ($post_product == 'Lease') {
    # ----------------------- LEASE PAYMENT PAYMENT ---------------------------
    $lease_payment_id = $_POST['lease_payment_id'];
    $data = 'lease_payment_id=' . $lease_payment_id;
    $user_id = $_SESSION['UserID'];
    $product = 'lease';
    $production = 'Apartment Rent';
}

//generate unique random token
$random_bytes = bin2hex(openssl_random_pseudo_bytes(10));
//add timestamp
$check_token = uniqid($random_bytes);
$current_time = date("Y-m-d H:i:s");

include_once('../../pdo/dbconfig.php');
$last_insert_id = $DB_payment->add_payment_forwards($user_id, $current_time, $check_token, $product, $payment_amount, $forward_recipient_name, $forward_recipient_email, $forward_recipient_mobile, 1, $data);
$user_info = $DB_payment->get_user_info($user_id);
$requester_name = $user_info['full_name'];
$requester_email = $user_info['email'];
$requester_mobile = $user_info['mobile'];
// die("kk");
if (!empty($last_insert_id)) {
    try {
        include_once('sendSMSEmail.php');
        include_once('../../pdo/Class.Template.php');
        $template = new Template();

        $SMS_message = "Dear " . $forward_recipient_name . ",\nYou have reserved a payment request from " . $requester_name . "\nPlease check your email(" . $forward_recipient_email . ") about the detailed information and then process the payment\n Thank you!\n-- spgmanagement.com";
        SendSMS($forward_recipient_mobile, $SMS_message);

        //email
        $title = "Payment Request";
        $subtitle = "You reserved a forward payment request!";
        $email_sbj = "Forward Payment Request - spgmanagement.com";
        $client_body1 = "This is a payment request from " . $requester_name . " !";
        $client_body2 = '<b>Payment Requester Information :</b>';
        $client_body2 .= '<br>Requester Name : ' . $requester_name;
        $client_body2 .= '<br>Requester Mobile : ' . $requester_mobile;
        $client_body2 .= '<br>Requester Email : ' . $requester_email;
        $client_body2 .= '<br><br>';
        $client_body2 .= '<b>Payment Details :</b>';
        $client_body2 .= '<br>Production : ' . $production;
        $client_body2 .= '<br>Payment Amount :  $' . number_format($payment_amount, 2);
        $client_body2 .= '<br><br><b>Please contact with the payment requester and check the payment details before processing the payment.</b>';

        $button_url = "https://spgmanagement.com/admin/custom/forward_payment.php?token=" . $check_token;
        $button_content = "Processing Payment Now";
        $client_content = $template->emailTemplate($title, $subtitle, $forward_recipient_name, $client_body1, $client_body2, $button_url, $button_content);
        MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $forward_recipient_email, $forward_recipient_name, $email_sbj, $client_content);
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    header('Location: request_send_success.php?product=' . $product);
}