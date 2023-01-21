<?php
if (isset($_GET['download_failed_payment'])) {
    include_once('Class.FailedPaymentReceipt.php');

    $order_id = $_GET['id'];
    $reference_number = $_GET['ref'];
    $response_code = $_GET['response_code'];
    $response_message = $_GET['message'];
    $trans_amount = $_GET['amount'];

    $failed_payment_receipt = new FailedPaymentReceipt($order_id, $reference_number, $response_code, $response_message, $trans_amount);
    $failed_payment_receipt->failed_payment_receipt_download();
}

//-------------------------------- lease payment receipt --------------------------

else if (isset($_GET['download_invoice'])) {
    include_once('Class.Invoice.php');
    $lease_payment_id = $_GET['lease_payment_id'];
    $invoice = new Invoice($lease_payment_id);
    $invoice->invoice_download();
}
else if (isset($_GET['download_receipt'])) {
    include_once("Class.Receipt.php");
    $lease_payment_details_id = $_GET['lease_payment_details_id'];
    $all = isset($_GET['all']);
    $receipt = new Receipt($lease_payment_details_id,$all);
    $receipt->receipt_download();
}
else if (isset($_GET['download_dp_receipt'])) {
    include_once('Class.DepositReceipt.php');
    $dp_id = $_GET['dp_id'];
    $dp_receipt = new DepositReceipt($dp_id);
    $dp_receipt->dp_receipt_download();
}


//---------------------------- kijijii payment receipt -------------------------------

else if(isset($_GET['download_kjj_receipt'])){
    $kjj_payment_id = $_GET['kjj_payment_id'];
    include_once('Class.KijijiReceipt.php');
    $kjj_receipt = new KijijiReceipt($kjj_payment_id);
    $kjj_receipt->download_receipt();
}

else if(isset($_GET['send_kjj_receipt_email'])){
    $kjj_payment_id = $_GET['kjj_payment_id'];
    include_once('Class.KijijiReceipt.php');
    $kjj_receipt = new KijijiReceipt($kjj_payment_id);
    $kjj_receipt->send_receipt_by_email();
}


//---------------------------- services payment receipt ------------------------------
else if(isset($_GET['download_services_receipt'])){
    $services_payment_id = $_GET['services_payment_id'];
    include_once('Class.ServicesReceipt.php');
    $services_receipt = new ServicesReceipt($services_payment_id);
    $services_receipt->download_receipt();
}

// ---------------- Sale payment receipt --------------
else if(isset($_GET['download_sale_receipt'])){
    $sale_payment_id = $_GET['sale_payment_id'];
    include_once('Class.SaleReceipt.php');
    $sale_receipt = new SaleReceipt($sale_payment_id);
	$sale_receipt->download_receipt();
}



?>