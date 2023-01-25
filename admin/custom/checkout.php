<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Defaults to empty
$tenant_month = "";
$building_name = "";
$unit_number = "";
// echo "Post=";
// die(print_r($_POST));
// print_r($_POST);
if (strpos(getcwd(), "custom") == false) {
	$path = "../pdo/";
} else {
	$path = "../../pdo/";
}
/* Include DB file */

include_once($path . 'dbconfig.php');
include_once("$path/Class.Payment.php");
$DB_payment = new Payment($DB_con);
include_once("$path/Class.LeasePayment.php");
$DB_ls_payment = new LeasePayment($DB_con);
include_once("$path/Class.Lease.php");
$DB_lease  = new Lease($DB_con);
include_once("$path/Class.Tenant.php");
$DB_tenant = new Tenant($DB_con);

//payment amount checking
if (floatval(number_format($_POST['payment_amount'], 2, '.', '')) <= 0.0) {
	if ($_POST['product'] == 'Services') {
		header('Location: ../services.php');
	} else if ($_POST['product'] == 'Kijiji') {
		header('Location: ../kijiji_listing.php');
	} else {  //Lease
		header('Location: ../home');
	}
	exit(0);
}



//convenience fee
$convenience_fee_rate = $DB_payment->get_convenience_fee_rate();
// die(print_r($convenience_fee_rate));
$CF_PP_Balance_P      = $convenience_fee_rate['CF_PP_Balance_F'];
$CF_PP_CC_P           = $convenience_fee_rate['CF_PP_CC_P'];
$CF_M_CC_P            = $convenience_fee_rate['CF_M_CC_P'];
$CF_M_Interac_F       = $convenience_fee_rate['CF_M_Interac_F'];

//passed payment amount
$payment_amount = floatval(number_format($_POST['payment_amount'], 2, '.', ''));

$gateway = null;
if (isset($_POST['gateway']) && !empty($_POST['gateway'])) {
	$gateway  = $_POST['gateway'];
	$lease_id = isset($_POST['lease_id']) ? $_POST['lease_id'] : 0;
} else {           //exception: tenant portal payment(from Goli)
	$apartment_id          = $_POST['apartment_id'];
	$lease_id              = $_POST['lease_id'];
	$selected_gateway_name = 'paymentType' . $apartment_id . '_' . $lease_id;
	$gateway               = $_POST[$selected_gateway_name];
}
// die(var_dump($_POST));
// die("gateway" . $gateway);
if ($gateway == 'p1')
	$convenience_fee = round($CF_PP_Balance_P / 100 * $payment_amount, 2);
else if ($gateway == 'p2')
	$convenience_fee = round($CF_PP_CC_P / 100 * $payment_amount, 2);
else if ($gateway == 'm1')
	$convenience_fee = round($CF_M_CC_P / 100 * $payment_amount, 2);
else if ($gateway == 'm2') //m2
	$convenience_fee = $CF_M_Interac_F; //+$interactCharge;
// die(var_dump($_POST));
// die("gateway=$gateway  CF_M_Interac_F= $CF_M_Interac_F convenience_fee=$convenience_fee");;
//total amount the client want to pay = payment_amount (how much need to pay) + convenience fee
$total_payment = floatval(number_format(($convenience_fee + $payment_amount), 2, '.', ''));
$total_payment = number_format($total_payment, 2);
$total_payment = str_replace(",", "", $total_payment);


if ($_POST['product'] == 'Services') {
	# --------------------------- Services PAYMENT ----------------------------
	$user_id            = intval($_POST['user_id']);
	$service_type       = $_POST['service_type'];
	$service_buy_count  = $_POST['service_buy_count'];
	$service_price      = $_POST['service_price'];
	$service_payment_id = $DB_payment->create_services_payment_record($user_id, $service_type, $service_price, $service_buy_count, $payment_amount, $convenience_fee, $total_payment);

	$invoice_no = "SEVS-" . $service_payment_id;

	//set paypal values
	$item1_name     = "Service fee - " . $service_type . " " . $service_buy_count . " time(s)";
	$sku_1          = $service_payment_id;
	$item1_price    = $service_price;
	$item1_quantity = $service_buy_count;
	$description    = "Service payment - service type:" . $service_type . " service price:" . $service_price;

	//set moneris id
	$cust_id = $service_payment_id;
}

if ($_POST['product'] == 'Kijiji') {
	# --------------------------- KIJIJI PAYMENT ----------------------------
	$slot_count    = $_POST['slot_count'];
	$slots_period  = $_POST['slots_period'];
	$employee_id   = $_POST['employee_id'];
	$slots_price   = $_POST['slots_price'];
	$employee_name = $_POST['employee_name'];
	$created_time  = date('Y-m-d H:i:s');

	$kijiji_payment_id = $DB_payment->create_kijiji_record($employee_id, $slot_count, $slots_price, $created_time, $payment_amount, $convenience_fee, $total_payment);

	$invoice_no = "KJJ-" . $kijiji_payment_id;

	// set paypal values
	$item1_name     = "kijiji slots fee - " . $slots_period . " month(s)";
	$sku_1          = $kijiji_payment_id;
	$item1_price    = $slots_price * $slots_period;
	$item1_quantity = $slot_count;
	$description    = "Kijiji slot payment - Employee :" . $employee_name . " Slots count :" . $slot_count . " Slots period :" . $slots_period . " Price:" . $slots_price;

	//set moneris id
	$cust_id = $kijiji_payment_id;
}

if ($_POST['product'] == 'Lease') {
	# ----------------------- LEASE PAYMENT PAYMENT ---------------------------

	$lease_payment_ids = $_POST['selected_lease_payment_ids'];

	if (count($lease_payment_ids) == 1) {

		$lease_payment_id    = $lease_payment_ids[0];
		$rental_payment_info = $DB_payment->get_rental_payment_by_lease_payment_id($lease_payment_id);
		$due_date            = $rental_payment_info['due_date'];
		$building_id         = $rental_payment_info['building_id'];
		$unit_number         = $rental_payment_info['unit_number'];
		$tenant_ids          = $rental_payment_info['tenant_ids'];
		$building_name       = $rental_payment_info['building_name'];

		$entry_user_id           = $DB_payment->get_building_manager_user_id($building_id);
		$current_time            = date('Y-m-d H:i:s');

		$lease_payment_detail_id = $DB_payment->create_lease_payment_detail_record($entry_user_id, $lease_payment_id, $convenience_fee, $payment_amount, $total_payment, $current_time);

		$invoice_no = "INV" . "-" . $building_id . "-" . $lease_id . "-" . $lease_payment_detail_id;

		//set paypal values
		$item1_name     = "Rent";
		$sku_1          = $lease_payment_detail_id;
		$item1_price    = $payment_amount;
		$item1_quantity = 1;
		$description    = "Rent Payment for: $building_name - Unit: $unit_number - Due Date: $due_date";

		//set moneris id
		$cust_id = $lease_payment_detail_id;
	} else {
		//pay for many lease payments at the same time
		$total_payment_amount          = $payment_amount;  //used to calculate value for very lease payment
		$current_time                  = date('Y-m-d H:i:s');
		$lease_payment_detail_id_array = array();
		$building_name                 = null;
		$unit_number                   = null;
		$due_date_array                = array();

		foreach ($lease_payment_ids as $lease_payment_id) {
			$rental_payment_info = $DB_payment->get_rental_payment_by_lease_payment_id($lease_payment_id);
			$due_date            = $rental_payment_info['due_date'];
			array_push($due_date_array, $due_date);
			$building_id         = $rental_payment_info['building_id'];
			$unit_number         = $rental_payment_info['unit_number'];
			$tenant_ids          = $rental_payment_info['tenant_ids'];
			$building_name       = $rental_payment_info['building_name'];
			$entry_user_id       = $DB_payment->get_building_manager_user_id($building_id);
			$payment_outstanding = $DB_payment->get_rental_payment_by_lease_payment_id($lease_payment_id)['outstanding'];

			$this_payment_amount = min($total_payment_amount, $payment_outstanding);

			if ($gateway == 'p1')
				$this_convenience_fee = round($CF_PP_Balance_P / 100 * $this_payment_amount, 2);
			else if ($gateway == 'p2')
				$this_convenience_fee = round($CF_PP_CC_P / 100 * $this_payment_amount, 2);
			else if ($gateway == 'm1')
				$this_convenience_fee = round($CF_M_CC_P / 100 * $this_payment_amount, 2);
			else  //m2
				$this_convenience_fee = $CF_M_Interac_F;
			$this_total_payment = floatval(number_format(($this_convenience_fee + $this_payment_amount), 2, '.', ''));

			//add this lease payment record into database
			$lease_payment_detail_id = $DB_payment->create_lease_payment_detail_record($entry_user_id, $lease_payment_id, $this_convenience_fee, $this_payment_amount, $this_total_payment, $current_time);
			array_push($lease_payment_detail_id_array, $lease_payment_detail_id);

			$total_payment_amount -= $this_payment_amount;

			if ($total_payment_amount <= 0.0) {
				break;
			}
		}
		/*
		 * Here is a trick: Because the parameter passed to Interac is limited, but we may have very long lease_payment_details_id.So I keep in DB firstly.
		 * the value saved in 'comment' field of first lease_payment_detail record.
		 * */
		$first_lease_payment_detail_id = $lease_payment_detail_id_array[0];
		$DB_payment->update_lease_payment_details_comment(implode(',', $lease_payment_detail_id_array), $first_lease_payment_detail_id);

		$invoice_no = "INV" . "-" . $building_id . "-" . $lease_id . "-" . $first_lease_payment_detail_id;

		//set paypal values
		$item1_name     = "Rent";
		$sku_1          = $first_lease_payment_detail_id;
		$item1_price    = $payment_amount;
		$item1_quantity = 1;
		$description    = "Rent Payment for: $building_name - Unit: $unit_number - Due Date: " . implode(" | ", $due_date_array);

		//set moneris id
		$cust_id = $first_lease_payment_detail_id;
	}
}

if ($_POST['product'] == 'saleItem') {
	// Fetch the infomation from the payment detail entry page and store in to the database
	$itemName       = $_POST["item_name"];
	$itemDetail     = $_POST["item_detail"];
	$itemSaleDate   = $_POST["item_sale_date"];
	$buildingId     = $_POST["building_filterpayment"];
	$itemSaleAmount = $_POST["item_sale_amt"];

	$isCustomerTenant      = "off";
	$isCustomerTenantValue = 0;
	$tenantId              = 0;

	// below 2 fields only if the customer is a tenant
	$tenantName   = $_POST["item_sale_tenant"];
	$customerName = $_POST["item_sale_customer"];

	// If the customer is a tenant - this will be "on"
	if (isset($_POST["customer_tenant_toggle"])) {
		$isCustomerTenant      = $_POST["customer_tenant_toggle"];
		$isCustomerTenantValue = 1;
		$tenantId              = $_POST["item_sale_tenant_id"];
		$tenantLease           = $DB_ls_payment->getLeaseTenantLike($tenantId);
		$customerName          = $DB_tenant->getTenantName($tenantId);

		if ($tenantLease && is_array($tenantLease)) {
			// Tenant has an active lease
			$customInvoiceDetails["lease_id"]     = $tenantLease["id"];
			$customInvoiceDetails["due_date"]     = date("Y-m-d");
			$customInvoiceDetails["lease_amount"] = $itemSaleAmount;
			$customInvoiceDetails["total"]        = $payment_amount;
			$customInvoiceDetails["outstanding"]  = $payment_amount;
			$customInvoiceDetails["comments"]     = "Generated from the Sale of product : $itemName.";

			// Add a custom invoice to the tenant's payments
			$customInvoiceId                = $DB_ls_payment->addCustomInvoice($customInvoiceDetails);
			$_SESSION["customInvoiceId"]    = $customInvoiceId;
			$_SESSION["customer_is_tenant"] = 1;
		}
	}

	$employee_id  = $_POST["employee_id"];

	// Invoice number and the created time for the sale payment
	$invoice_no   = "SALE-" . rand(100000, 999999);
	$created_time = date('Y-m-d H:i:s');

	$record_id = $DB_payment->create_sale_record($invoice_no, $employee_id, $created_time, $payment_amount, $convenience_fee, $total_payment, $itemName, $itemDetail, $tenantId, $customerName, $isCustomerTenantValue);

	$cust_id = $record_id;

	// set paypal values
	$item1_name     = "Sale of item : " . $itemName;
	$sku_1          = $record_id;
	$item1_price    = $itemSaleAmount;
	$item1_quantity = 1;
	$description    = "Sale of the item : " . $item1_name;

	//set record_id into session
	$_SESSION['pay_record_id'] = $record_id;
}

//set some value in SESSION, if there are some error in the middle, info in fail page based on SESSION
$_SESSION["pay_record_id"]       = $cust_id;
$_SESSION['pay_convenience_fee'] = $convenience_fee;
$_SESSION['pay_payment_amount']  = $payment_amount;



/**** Get Lease Info **********************/
$lease_info = $DB_lease->getLeaseInfoByLeaseId($lease_id);

$tenant_ids_array = $DB_tenant->getTenantForLeaseId($lease_id);
foreach ($tenant_ids_array as $tenant_id) {
	// var_dump($DB_tenant->getTenantInfo($tenant_id)['full_name']);
	$tenant_names[] = $DB_tenant->getTenantInfo($tenant_id)['full_name'];
	$tenant_phones[] = $DB_tenant->getTenantInfo($tenant_id)['mobile'];
	$tenant_emails[] = $DB_tenant->getTenantInfo($tenant_id)['email'];
}

if (!empty($tenant_names)) {
	$tenant_names = implode(",", $tenant_names);
}
if (!empty($tenant_phones)) {
	$tenant_phones = implode(",", $tenant_phones);
}
if (!empty($tenant_emails)) {
	$tenant_emails = implode(",", $tenant_emails);
}
//var_dump($tenant_names);
if (!empty($lease_payment_id)) {
	$lease_payment_info = $DB_ls_payment->getLeasePaymentInfo($lease_payment_id);
	//var_dump($lease_payment_info);
	$tenant_month = "Due Date:" . $lease_payment_info['due_date'] . " - Comments: " . addslashes($lease_payment_info['comments']);
}








# ======================  Paypal Gateway  =============================
require_once 'paypal/paypal/rest-api-sdk-php/sample/bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

if ($gateway == "p1" || $gateway == "p2") {

	if ($gateway == 'p1') {
		$payment_method = 'balance';
	} else {
		$payment_method = 'credit_card';
	}

	$payer = new Payer();
	$payer->setPaymentMethod("paypal");

	$item1 = new Item();
	$item1->setName($item1_name)
		->setCurrency('CAD')
		->setQuantity($item1_quantity)
		->setSku($sku_1)
		->setPrice($item1_price);

	$item2 = new Item();
	$item2->setName('convenience fee')
		->setCurrency('CAD')
		->setQuantity(1)
		->setSku($payment_method)
		->setPrice($convenience_fee);

	$itemList = new ItemList();
	$itemList->setItems(array($item1, $item2));

	$amount = new Amount();
	$amount->setCurrency("CAD")
		->setTotal($total_payment);

	$transaction = new Transaction();
	$transaction->setAmount($amount)
		->setItemList($itemList)
		->setDescription($description)
		->setInvoiceNumber($invoice_no);

	$baseUrl      = getBaseUrl();
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
		header("Location: payment_fail.php");
		exit(1);
	}
	$approvalUrl = $payment->getApprovalLink();

	header("Location: {$approvalUrl}");
	//ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);
	//return $payment;
}
?>
<?
#================================== Moneris Gateway ===============================

// if moneris credit card, send the request directly to moneris
if ($gateway == "m1") {
	// if (strpos($_SERVER['HTTP_HOST'], "beaveraittesting.site") !== false) { //  # FROM TEST ENVIRONMENT CALLS
	// 	$ps_store_id = "QZ6FA00392";    //QZ6FA00392      //FNNB3tore1
	// 	$hpp_key     = "hpFTDIZ3TO37";      //hpFTDIZ3TO37    //hp7ZM6RBY3VE
	// 	//	$hpp_key = "hpB4SQMH6XTR";
	// 	//	$ps_store_id = "D8YUCtore1";
	// 	$action = 'https://esqa.moneris.com/HPPDP/index.php';
	// } elseif (strpos($_SERVER['HTTP_HOST'], "spgmanagement.com") !== false) { // For Live Calls
	// die('1$gateway=' . $gateway);
	$ps_store_id = "YG7MH03793";
	$hpp_key     = "hp8G8QG8B7OJ";
	$action      = 'https://www3.moneris.com/HPPDP/index.php';
	// }

	//master 5454545454545454
	//visa  4242424242424242
?>
	<form METHOD="POST" name="moneris_form" id="moneris_form" ACTION="<?= $action ?>">
		<input type="hidden" name="ps_store_id" value="<?= $ps_store_id ?>">
		<input type="hidden" name="hpp_key" value="<?= $hpp_key ?>">
		<input type="hidden" name="charge_total" value="<?= str_replace(",", "", $total_payment) ?>">
		<input type="hidden" name="order_id" value="<?= $invoice_no ?>">
	</form>
	<script>
		moneris_form.submit();
	</script>
<?
}

//if moenris interac, switch to gateway page
if ($gateway == "m2") {
	if (strpos($_SERVER['HTTP_HOST'], "beaveraittesting.site") !== false) {
		$ps_store_id = "D2LYPtore3";
		$hpp_key     = "hp1XQB7ZSOCN";
		$action      = 'https://esqa.moneris.com/HPPDP/index.php';
	} elseif (strpos($_SERVER['HTTP_HOST'], "spgmanagement.com") !== false || strpos($_SERVER['HTTP_HOST'], "localhost") !== false) {
		$ps_store_id = "4CA6403793"; //"MU2YZ03793";
		$hpp_key     = "hpKC2JTZ6G8T"; //"hp9BRMRHU29E";
		$action      = 'https://www3.moneris.com/HPPDP/index.php';
	}

	//$total_payment=1.00;
?>
	<form METHOD="POST" name="moneris_interac_form" id="moneris_interac_form" ACTION="<?= $action ?>">
		<input type="hidden" name="ps_store_id" value="<?= $ps_store_id ?>">
		<input type="hidden" name="hpp_key" value="<?= $hpp_key ?>">
		<input type="hidden" name="charge_total" value="<?= str_replace(",", "", $total_payment) ?>">
		<input type="hidden" name="order_id" value="<?= $invoice_no ?>">
		<input type="hidden" name="note" value="<?= $tenant_month ?>">
		<input type="hidden" name="email" value="<?= $tenant_emails ?>">
		<input type="hidden" name="id1" value="<?= $invoice_no ?>">
		<input type="hidden" name="description1" value="<?= $tenant_month ?>">
		<input type="hidden" name="quantity1" value="1">
		<input type="hidden" name="price1" value="<?= str_replace(",", "", $total_payment) ?>">
		<input type="hidden" name="subtotal1" value="<?= str_replace(",", "", $total_payment) ?>">


		<input type="hidden" name="ship_first_name" value="<? if (!empty($tenant_names)) {
																echo $tenant_names;
															} else {
																"";
															} ?>">
		<input type="hidden" name="ship_address_one" value="<?= $building_name . "-" . $unit_number ?>">
		<input type="hidden" name="ship_phone" value="<?= $tenant_phones ?>">
		<input type="hidden" name="ship_fax" value="<?= $tenant_emails ?>">
		<input type="hidden" name="bill_first_name" value="<? if (!empty($tenant_names)) {
																echo $tenant_names;
															} else {
																"";
															} ?>">
		<input type="hidden" name="bill_address_one" value="<?= $building_name . "-" . $unit_number ?>">
		<input type="hidden" name="bill_phone" value="<? if (!empty($tenant_phones)) {
															echo $tenant_phones;
														} else {
															"";
														} ?>">
		<input type="hidden" name="bill_fax" value="<? if (!empty($tenant_emails)) {
														echo $tenant_emails;
													} else {
														"";
													} ?>">

	</form>
	<script>
		moneris_interac_form.submit();
	</script>
<?
}

/* Cash payment method - send to the same moneris page */
if ($gateway == "m3") {
?>
	<form METHOD="POST" name="cash_form" id="cash_form" ACTION="ExecutePayment_Cash.php">
		<input type="hidden" name="ps_store_id" value="<?= $ps_store_id ?>">
		<input type="hidden" name="hpp_key" value="<?= $hpp_key ?>">
		<input type="hidden" name="charge_total" value="<?= str_replace(",", "", $total_payment) ?>">
		<input type="hidden" name="order_id" value="<?= $invoice_no ?>">
		<input type="hidden" name="note" value="<?= $tenant_month ?>">
		<input type="hidden" name="email" value="<?= $tenant_emails ?>">
		<input type="hidden" name="id1" value="<?= $invoice_no ?>">
		<input type="hidden" name="description1" value="<?= $tenant_month ?>">
		<input type="hidden" name="quantity1" value="1">
		<input type="hidden" name="price1" value="<?= str_replace(",", "", $total_payment) ?>">
		<input type="hidden" name="subtotal1" value="<?= str_replace(",", "", $total_payment) ?>">


		<input type="hidden" name="ship_first_name" value="<?= $tenant_names ?>">
		<input type="hidden" name="ship_address_one" value="<?= $building_name . "-" . $unit_number ?>">
		<input type="hidden" name="ship_phone" value="<?= $tenant_phones ?>">
		<input type="hidden" name="ship_fax" value="<?= $tenant_emails ?>">
		<input type="hidden" name="bill_first_name" value="<?= $tenant_names ?>">
		<input type="hidden" name="bill_address_one" value="<?= $building_name . "-" . $unit_number ?>">
		<input type="hidden" name="bill_phone" value="<?= $tenant_phones ?>">
		<input type="hidden" name="bill_fax" value="<?= $tenant_emails ?>">
	</form>
	<script>
		cash_form.submit();
	</script>
<?
}
?>