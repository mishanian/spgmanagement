<?
include_once ('invoice_receipt/Class.DepositReceipt.php');
$deposit_id = $_GET['id'];
$deposit = new DepositReceipt($deposit_id);
$deposit->send_dp_receipt_by_email();
$deposit->send_dp_receipt_by_sms();
?>