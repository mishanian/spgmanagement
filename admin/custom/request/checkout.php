<?
include_once '../../db.php';

foreach ($_POST as $key => $value){$$key=$value;}

$SelectSql = "SELECT * from rental_payments ";
$SelectSql .= "where lease_payment_id=$lease_payment_id";
$statement = $db->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
$row = $result[0];
foreach ($row as $key => $value) {
    $$key = $value;
}
$due_date_show = date_format(date_create($due_date), "M d, Y");

$CertNo=0;
$SelectSql = "select count(*) as CertNo from  lease_payments where id=$lease_payment_id";
$statement = $db->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
$row = $result[0];
$CertNo=$row['CertNo'];
$CertNo++;


$invoice_no="INV-" . $company_id . "/" . $building_id . "/" . $unit_number . "/" . $lease_payment_id . "/" . rand(1, 1000);



$SelectSql = "SELECT tenant_id, full_name FROM tenant_infos where tenant_id in($tenant_ids)";
$statement = $db->prepare($SelectSql);
$statement->execute();
$tenants = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($row as $key => $value) {
    $$key = $value;
}

// convenience fee
$sql = "SELECT CF_PP_Balance_F,CF_PP_CC_P,CF_M_CC_P,CF_M_Interac_F FROM settings WHERE id = 1";
$C_F = $db->prepare($sql);
$C_F->execute();
$convenience_rules = $C_F->fetchAll(PDO::FETCH_ASSOC)[0];
$CF_PP_Balance_F = $convenience_rules['CF_PP_Balance_F'];
$CF_PP_CC_P = $convenience_rules['CF_PP_CC_P'];
$CF_M_CC_P = $convenience_rules['CF_M_CC_P'];
$CF_M_Interac_F = $convenience_rules['CF_M_Interac_F'];


//$trans_id=$lease_payment_id;
//$price=$outstanding;
$price = floatval(number_format($_POST['price'],2));

if($_POST['gateway'] == 'p1')
    $convenience_fee = $CF_PP_Balance_F;
else if($_POST['gateway'] == 'p2')
    $convenience_fee = round($CF_PP_CC_P/100*$price,2);
else if($_POST['gateway'] == 'm1')
    $convenience_fee = round($CF_M_CC_P/100*$price,2);
else
    $convenience_fee = $CF_M_Interac_F;

$paid_amount = floatval(number_format($convenience_fee + $price,2));


?><?php
require_once 'paypal/paypal/rest-api-sdk-php/sample/bootstrap.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;


if ($_POST['gateway']=="p1" || $_POST['gateway']=="p2") {

    /*
    $paypal= new \Paypal\Rest\ApiContext (
        new \PayPal\Auth\OAuthTokenCredential('Ab-JwSvdICBAB17sIgvrI2-rvZISJpmnh9uOeVcxTQhBXA_ws0tyaErrxDT7shVUK-3e7XGWg8aHZgKN',
            'EIdJZ9EKqTgJycp6qUJHgMSQfQ7FOjv1JyVXJzdMEOh6z5i8bp2hzJG1-Qd6JO7Cpd_SIWyifNR6WYkP')
    );
    */
//die(print_r($paypal));

    if($_POST['gateway']=='p1'){
      $payment_method = 'balance';
    }
    if($_POST['gateway']=='p2'){
        $payment_method = 'credit_card';
    }

    $payer = new Payer();
    $payer->setPaymentMethod("paypal");

    $item1 = new Item();
    $item1->setName('Rent')
        ->setCurrency('CAD')
        ->setQuantity(1)
        ->setSku($lease_payment_id)
        ->setPrice($price);

    $item2 = new Item();
    $item2->setName('convenience fee')
        ->setCurrency('CAD')
        ->setQuantity(1)
        ->setSku($payment_method)
        ->setPrice($convenience_fee);


    $itemList = new ItemList();
    $itemList->setItems(array($item1,$item2));


    $amount = new Amount();
    $amount->setCurrency("CAD")
        ->setTotal($paid_amount);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription("Rent Payment for: $building_name - Unit: $unit_number - Due Date: $due_date")
        ->setInvoiceNumber($invoice_no); //uniqid()

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
        //   ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
        echo $ex;
        exit(1);
        die();
    }
    $approvalUrl = $payment->getApprovalLink();

//die(var_dump($payment));

    header("Location: {$approvalUrl}");
//ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

//return $payment;

} // End of Paypal

?>
<?
if ($_POST['gateway']=="m1" || $_POST['gateway']=="m2") {

    if ($_POST['gateway']=="m1"){
        $ps_store_id="QZ6FA00392"; //test QZ6FA00392   // real YG7MH03793
        $hpp_key="hpFTDIZ3TO37"; //hpFTDIZ3TO37   //real  hp8G8QG8B7OJ
        $payment_method_id = '2';
    }
    if ($_POST['gateway']=="m2"){
        $ps_store_id="ZEY9Ltore3"; // ZEY9Ltore3   //ZDQYA03793
        $hpp_key="hpYRTYX8MCLG"; //hpYRTYX8MCLG   //hpSU86D15YCK
        $payment_method_id = '1';
    }

    $note = $convenience_fee.','.$payment_method_id;

//master 5454545454545454
 // visa  4242424242424242

?>
    <FORM METHOD="POST" name="moneris_form" id="moneris_form" ACTION="https://esqa.moneris.com/HPPDP/index.php"><!--https://esqa.moneris.com/HPPDP/index.php    // https://www3.moneris.com/HPPDP/index.php-->
        <INPUT TYPE="HIDDEN" NAME="ps_store_id" VALUE="<?=$ps_store_id?>">
        <INPUT TYPE="HIDDEN" NAME="hpp_key" VALUE="<?=$hpp_key?>">
        <INPUT TYPE="HIDDEN" NAME="charge_total" VALUE="<?= $paid_amount ?>">
        <input type="hidden" name="order_id" VALUE="<?=$invoice_no?>">
        <input type="hidden" name="cust_id" VALUE="<?=$lease_payment_id?>">
        <input type="hidden" name="note" VALUE="<?=$note?>">
        <input type="hidden" name="note" VALUE="<?=$note?>">
        <!--MORE OPTIONAL VARIABLES CAN BE DEFINED HERE -->
        <INPUT TYPE="HIDDEN" NAME="IDEBIT_AMOUNT" VALUE="<?= $paid_amount ?>">

    </FORM>
    <script>moneris_form.submit();</script>
    <?
}
?>
