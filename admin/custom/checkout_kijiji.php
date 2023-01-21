<?php
include_once ("../../pdo/dbconfig.php");

$slot_count=$_POST['slot_count'];
$slots_period=$_POST['slots_period'];
$employee_id=$_POST['employee_id'];
$slots_price=$_POST['price'];
$employee_name=$_POST['employee_name'];
$gateway = $_POST['gateway'];
$total_amount=$slot_count*$slots_price*$slots_period;

$invoice_no="KJJ-".rand(1, 1000);
$created_time = date('Y-m-d H:i:s', time());

$C_F_rules = $DB_kijiji->get_convenience_rules();
$CF_PP_Balance_F = $C_F_rules['CF_PP_Balance_F'];
$CF_PP_CC_P = $C_F_rules['CF_PP_CC_P'];
$CF_M_CC_P = $C_F_rules['CF_M_CC_P'];
$CF_M_Interac_F = $C_F_rules['CF_M_Interac_F'];

if($gateway == 'p1')
  $convenience_fee = $CF_PP_Balance_F;
else if($gateway == 'p2')
    $convenience_fee = round($CF_PP_CC_P/100*$total_amount,2);
else if($gateway == 'm1')
    $convenience_fee = round($CF_M_CC_P/100*$total_amount,2);
else
    $convenience_fee = $CF_M_Interac_F;


$paid_amount = floatval(number_format($convenience_fee + $total_amount,2));

$record_id=$DB_kijiji->create_kijiji_record($invoice_no,$employee_id,$slot_count,$slots_price,$created_time,$total_amount,$convenience_fee,$paid_amount);

require_once 'paypal/paypal/rest-api-sdk-php/sample/bootstrap.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

if ($_POST['gateway']=="p1" || $_POST['gateway']=="p2"){
    if ($_POST['gateway'] == 'p1'){
        $payment_method = 'balance';
    }
    if ($_POST['gateway'] == 'p2'){
        $payment_method = 'credit_card';
    }

    $payer = new Payer();
    $payer->setPaymentMethod("paypal");
    $item1 = new Item();
    $item1->setName("kijiji slots fee - ".$slots_period." month(s)")
        ->setCurrency('CAD')
        ->setQuantity($slot_count)
        ->setSku($record_id)
        ->setPrice($slots_price*$slots_period);

    $item2 = new Item();
    $item2->setName("Convenience fee")
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
        ->setDescription("Kijiji slot payment - Employee :".$employee_name." Slots count :".$slot_count." Slots period :".$slots_period." Price:".$slots_price)
        ->setInvoiceNumber($invoice_no);

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
    } catch (Exception $ex){
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

if ($_POST['gateway']=="m1"){
    $ps_store_id="QZ6FA00392"; //test QZ6FA00392   // real YG7MH03793
    $hpp_key="hpFTDIZ3TO37"; //hpFTDIZ3TO37   //real  hp8G8QG8B7OJ

    //master 5454545454545454
    // visa  4242424242424242
?>
    <form METHOD="POST" name="moneris_form" id="moneris_form" ACTION=https://esqa.moneris.com/HPPDP/index.php> <!--https://esqa.moneris.com/HPPDP/index.php    *** https://www3.moneris.com/HPPDP/index.php-->
        <input TYPE="hidden" NAME="ps_store_id" VALUE="<?=$ps_store_id?>">
        <input TYPE="hidden" NAME="hpp_key" VALUE="<?=$hpp_key?>">
        <input TYPE="hidden" NAME="charge_total" VALUE="<?= $paid_amount ?>">
        <input type="hidden" name="order_id" VALUE="<?=$invoice_no?>">
        <input type="hidden" name="cust_id" VALUE="<?=$record_id?>">
    </form>
    <script>moneris_form.submit();</script>
<?
}
if($_POST['gateway'] == "m2"){
?>
  <form name="interac_form" id="interac_form" action='kijiji/gateway_interac_kijiji.php' method='post'>
    <input type='hidden' name='invoice_no' value='<?=$invoice_no?>'>
    <input type="hidden" name="convenience_fee" value="<?=$convenience_fee?>">
    <input type="hidden" name="record_id" value="<?= $record_id?>">
    <input type="hidden" name="slots_period" value="<?= $slots_period?>">
    <input type="hidden" name="slots_count" value="<?=$slot_count?>">
    <input type="hidden" name="slots_price" value="<?=$slots_price?>">
    <input type="hidden" name="start_time" value="<?=$created_time?>">
  </form>
  <script>interac_form.submit();</script>
<?php } ?>