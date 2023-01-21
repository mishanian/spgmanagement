<?
session_start();
include_once '../../db.php';

foreach ($_POST as $key => $value){$$key=$value;}
$lease_payment_id=$id;
$SelectSql = "SELECT due_date,total,outstanding,payment_amount, APP.building_id, building_name, APP.unit_number,tenant_ids 
FROM lease_payments LP 
LEFT JOIN lease_infos LI ON LI.id=LP.lease_id
LEFT JOIN lease_payment_details LPD ON LPD.lease_payment_id=LP.id
LEFT JOIN apartment_infos APP ON APP.id=LI.apartment_id
LEFT JOIN building_infos BLD ON BLD.id=APP.building_id 
";
$SelectSql .= "where LP.id=$lease_payment_id";
$statement = $db->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
$row = $result[0];
foreach ($row as $key => $value) {
    $$key = $value;
}
$due_date = date_format(date_create($due_date), "M d, Y");

$SelectSql = "SELECT id, full_name FROM tenant_infos where id in($tenant_ids)";
$statement = $db->prepare($SelectSql);
$statement->execute();
$tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($row as $key => $value) {
    $$key = $value;
}


$trans_id=$lease_payment_id;
$price=$outstanding;
?><?php
require 'paypal/paypal/rest-api-sdk-php/sample/bootstrap.php';

/*
$paypal= new \Paypal\Rest\ApiContext (
    new \PayPal\Auth\OAuthTokenCredential('Ab-JwSvdICBAB17sIgvrI2-rvZISJpmnh9uOeVcxTQhBXA_ws0tyaErrxDT7shVUK-3e7XGWg8aHZgKN',
        'EIdJZ9EKqTgJycp6qUJHgMSQfQ7FOjv1JyVXJzdMEOh6z5i8bp2hzJG1-Qd6JO7Cpd_SIWyifNR6WYkP')
);
*/
//die(print_r($paypal));
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

//$trans_id=$_POST['trans_id'];
//$product=$_POST['product'];
//$price=$_POST['price'];

$trans_id=3;
$product="Rent";
$price=50;


$payer = new Payer();
$payer->setPaymentMethod("paypal");

$item1 = new Item();
$item1->setName('Rent')
    ->setCurrency('CAD')
    ->setQuantity(1)
    ->setSku("123123") // Similar to `item_number` in Classic API
    ->setPrice(2);
$item2 = new Item();
$item2->setName('Granola bars')
    ->setCurrency('CAD')
    ->setQuantity(3)
    ->setSku("321321") // Similar to `item_number` in Classic API
    ->setPrice(2);

$itemList = new ItemList();
$itemList->setItems(array($item1, $item2));

$details = new Details();
$details->setShipping(1.5)
    ->setTax(0.5)
    ->setSubtotal(8);

$amount = new Amount();
$amount->setCurrency("CAD")
    ->setTotal(10)
    ->setDetails($details);

$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setItemList($itemList)
    ->setDescription("Payment description")
    ->setInvoiceNumber(uniqid());

$baseUrl = getBaseUrl();
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")
    ->setCancelUrl("$baseUrl/ExecutePayment.php?success=false");

$payment = new Payment();
$payment->setIntent("sale")
    ->setPayer($payer)
    ->setRedirectUrls($redirectUrls)
    ->setTransactions(array($transaction));

$request = clone $payment;

try {
    $payment->create($apiContext);
} catch (Exception $ex) {
    ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
   exit(1);
    die();
}
$approvalUrl = $payment->getApprovalLink();

//die(var_dump($payment));

//header("Location: {$approvalUrl}");
ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

//return $payment;

?>