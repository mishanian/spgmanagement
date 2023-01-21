<?php
//include_once ("custom/notification/Class.RequestNtf.php");

//// new request
//$request_id=1;
//$test = new RequestNtf($request_id);
//$test->new_issue_notify_by_email();
////$test->new_issue_notify_by_sms();
//
//
//// if update issue
////create a object firstly
////
//$request_communication_id =1 ;
////$test->update_issue_notify_by_sms($request_communication_id);
//$test->update_issue_notify_by_email($request_communication_id);
//
//// welcome
////include_once ('custom/notification/Class.WelcomeNtf.php');
////$user_id =1;
////$welcome_test = new WelcomeNtf($user_id);
////$welcome_test->send_welcome_email();


include_once ('invoice_receipt/Class.DepositReceipt.php');
$deposit_id = 4;
$deposit = new DepositReceipt($deposit_id);
$deposit->send_dp_receipt_by_email();
$deposit->send_dp_receipt_by_sms();