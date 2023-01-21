<?php

class Receipt
{
  private $lease_payment_details_id;

  //tenants info
  private $tenants_name;
  private $tenants_email;
  private $tenants_telephone;

  // unit info
  private $company_info;
  private $unit_building;
  private $unit_floor;
  private $unit_room;

  private $due_date;
  private $proceed_by;
  private $proceed_time;

  private $invoice_number;
  private $receipt_amount;
  private $service_charge;
  private $total_amount;

  private $paid_method;
  private $paid_types;
  private $lease_payment_id;
  private $email_url_param;

  private $payment_type_switch;
  //paypal
  private $paypal_id;
  private $paypal_status;
  private $p_payer_id;

  //moneris (interac)
  private $issuer_name;
  private $issuer_confirm;
  private $trans_name;
  private $response_code;
  private $interac_bank_transaction_id;
  private $interac_bank_approval_code;

  //moneris (credit card)
  private $credit_card;
  private $credit_card_no;
  private $moneris_bank_transaction_id;
  private $moneris_bank_approval_code;


  public function __construct($lease_payment_details_id, $is_all = false)
  {
    if (strpos(getcwd(), "admin") != false) {
      $path = "..";
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "../..";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "../../..";
    }
    require($path . "/pdo/dbconfig.php");

    if (!$is_all) {
      $this->email_url_param = 'download_receipt&lease_payment_details_id=' . $lease_payment_details_id;
      $this->lease_payment_details_id = $lease_payment_details_id;
      include_once($path . "/pdo/Class.Payment.php");
      $DB_payment = new Payment($DB_con);
      $row = $DB_payment->get_payment_details($lease_payment_details_id);
      // die(var_dump($row));
      $this->receipt_amount = $row['amount'];
      $this->service_charge = $row['cf_amount'];
      $this->total_amount = $this->receipt_amount + $this->service_charge;
      $this->proceed_time = $row['entry_datetime'];
      $this->lease_payment_id = $row['lease_payment_id'];
      $this->invoice_number = $row['invoice_number'];
      $paypal_id = $row['paypal_id'];
      $moneris_id = $row['moneris_id'];

      $details_row = $DB_payment->get_all_info_from_payment_details($lease_payment_details_id);
      $this->due_date = $details_row['due_date'];
    } else {
      $this->email_url_param = 'download_receipt&all&lease_payment_details_id=' . $lease_payment_details_id;
      $lease_payment_details_id_array = explode(',', $lease_payment_details_id);
      $this->lease_payment_details_id = implode('|', $lease_payment_details_id_array);

      $amount = 0.0;
      $cf_amount = 0.0;
      $due_date_array = array();
      foreach ($lease_payment_details_id_array as $lease_payment_details_id) {
        //$row in last record will be used below
        $row = $DB_payment->get_payment_details($lease_payment_details_id);
        $amount += $row['amount'];
        $cf_amount += $row['cf_amount'];
        //$details_row in last record will be used below
        $details_row = $DB_payment->get_all_info_from_payment_details($lease_payment_details_id);
        array_push($due_date_array, $details_row['due_date']);
      }

      $this->receipt_amount = $amount;
      $this->service_charge = $cf_amount;
      $this->total_amount = $this->receipt_amount + $this->service_charge;
      $this->proceed_time = $row['payment_date'];
      $this->lease_payment_id = $row['lease_payment_id'];
      $this->invoice_number = $row['invoice_number'];
      $paypal_id = $row['paypal_id'];
      $moneris_id = $row['moneris_id'];
      $this->due_date = implode("|", $due_date_array);
    }

    //more details
    $tenant_ids = explode(',', $details_row['tenants_id']);
    //tenants
    $this->tenants_email = array();
    $this->tenants_telephone = array();
    $this->tenants_name = array();
    foreach ($tenant_ids as $id) {
      $temp = $DB_payment->get_tenant_info($id);
      array_push($this->tenants_name, $temp['full_name']);
      array_push($this->tenants_email, $temp['email']);
      array_push($this->tenants_telephone, $temp['mobile']);
    }
    $this->unit_building = $details_row['building'];
    $this->unit_floor = $details_row['floor'];
    $this->unit_room = $details_row['unit'];
    $this->paid_method = $details_row['payment_method'];
    $this->paid_types = $details_row['payment_type'];

    $this->company_info = $DB_payment->get_company_info($lease_payment_details_id);

    //proceed_by
    if ($details_row['proceed_by'] > 0)
      $this->proceed_by = $DB_payment->get_employee_info($details_row['proceed_by'])['full_name'];
    else
      $this->proceed_by = 'Auto proceeding - transfer online';


    $this->payment_type_switch = -1;  //manually pay
    //transaction details
    if (!is_null($paypal_id)) {
      $paypal_transaction_info = $DB_payment->get_paypal_transaction_info($paypal_id);
      $this->paypal_id = $paypal_transaction_info['p_id'];
      $this->paypal_status = $paypal_transaction_info['p_state'];
      $this->p_payer_id = $paypal_transaction_info['p_payer_id'];
      $this->payment_type_switch = 0;
    } elseif (!is_null($moneris_id)) {
      $moneris_transaction_info = $DB_payment->get_moneris_transaction_info($moneris_id);

      if (!is_null($moneris_transaction_info['issuer_name'])) {
        $this->issuer_name = $moneris_transaction_info['issuer_name'];
        $this->issuer_confirm = $moneris_transaction_info['issuer_confirm'];
        $this->trans_name = $moneris_transaction_info['trans_name'];
        $this->response_code = $moneris_transaction_info['response_code'];
        $this->interac_bank_transaction_id = $moneris_transaction_info['bank_transaction_id'];
        $this->interac_bank_approval_code = $moneris_transaction_info['bank_approval_code'];
        $this->payment_type_switch = 1;
      } else {
        $this->credit_card = $moneris_transaction_info['creditcard'];
        $this->credit_card_no = $moneris_transaction_info['creditcard_no'];
        $this->moneris_bank_approval_code = $moneris_transaction_info['bank_approval_code'];
        $this->moneris_bank_transaction_id = $moneris_transaction_info['bank_transaction_id'];
        $this->payment_type_switch = 2;
      }
    }
  }

  public function send_receipt_by_email()
  {
    $tenant_number = sizeof($this->tenants_name);
    for ($i = 0; $i < $tenant_number; $i++) {
      $to_email = $this->tenants_email[$i];
      $to_name = $this->tenants_name[$i];
      if (!empty($to_email))
        $this->send_receipt_email($to_email, $to_name);
    }
  }

  public function send_receipt_by_sms()
  {

    $tenant_number = sizeof($this->tenants_name);
    for ($i = 0; $i < $tenant_number; $i++) {
      $to_telephone = $this->tenants_telephone[$i];
      $to_name = $this->tenants_name[$i];
      if (!empty($to_telephone)) {
        echo ("SMS1");
        $this->send_receipt_sms($to_telephone, $to_name);
        echo ("SMS2");
      }

      if ($i != $tenant_number - 1) {
        usleep(250000);
      }
    }
  }

  public function receipt_download()
  {
    if (strpos(getcwd(), "admin") != false) {
      $path = "custom/invoice_receipt/";
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "invoice_receipt/";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "";
    }
    require_once($path . "fpdf/fpdf.php");

    $receipt = new FPDF();
    $receipt->AddPage();
    $receipt->SetAutoPageBreak(true, 0);
    $receipt->SetTopMargin(15);
    $x = $receipt->GetX();
    $y = $receipt->GetY();
    $receipt->Image('../images/receipt_logo.jpg');


    $receipt->SetXY($x, $y);
    $receipt->SetFont('Arial', 'B', 17);
    $receipt->SetTextColor(39, 64, 139);
    $receipt->Cell(190, 15, "Receipt", 0, 1, 'C');
    $y = $receipt->GetY() + 10;

    //table
    $receipt->SetXY($x + 15, $y);
    $receipt->SetTextColor(54, 54, 54);
    $receipt->SetFont('Arial', '', 11);

    $receipt->SetX($x + 15);
    $receipt->Cell(110, 10, 'From : ' . $this->company_info['name'], 0, 1, 'L');

    if ($this->payment_type_switch >= 0) {
      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Order No.", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->invoice_number, 1, 1, 'C');
    }

    $reference_no = 'INV-' . $this->lease_payment_details_id;
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Reference No.", 1, 0, 'C');
    $receipt->Cell(110, 10, $reference_no, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Tenant Name", 1, 0, 'C');
    $receipt->Cell(110, 10, implode(',', $this->tenants_name), 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Building Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_building, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Unit Number", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_room . ' in ' . $this->unit_floor, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Due", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->due_date, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Type", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->paid_types, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Method", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->paid_method, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format(round($this->receipt_amount, 2), 2) . ' CAD', 1, 1, 'C');


    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Service Charge", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format(round($this->service_charge, 2), 2) . ' CAD', 1, 1, 'C');


    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Total Paid Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format(round($this->total_amount, 2), 2) . ' CAD', 1, 1, 'C');


    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Status", 1, 0, 'C');
    $receipt->Cell(110, 10, 'SUCCESS', 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Proceed By", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->proceed_by, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Proceed Time", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->proceed_time, 1, 1, 'C');


    if ($this->payment_type_switch == 0) {    //paypal
      $receipt->SetX($x + 15);
      $receipt->Cell(155, 10, str_repeat(' ', 8) . "Transaction Details", 1, 1, 'L');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Paypay ID", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->paypal_id, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Paypay Status", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->paypal_status, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Paypay Payer ID", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->p_payer_id, 1, 1, 'C');
    } else if ($this->payment_type_switch == 1) {      //interac
      $receipt->SetX($x + 15);
      $receipt->Cell(155, 10, str_repeat(' ', 8) . "Transaction Details", 1, 1, 'L');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Issuer Name", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->issuer_name, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Issuer Confirmation", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->issuer_confirm, 1, 1, 'C');


      $trans_name_arr = explode('_', $this->trans_name);
      $card_type = '';
      if ($trans_name_arr[0] == 'idebit')
        $card_type = 'Debit';
      $trans_type = ucwords($trans_name_arr[1]);

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Card Type", 1, 0, 'C');
      $receipt->Cell(110, 10, $card_type, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Transaction Type", 1, 0, 'C');
      $receipt->Cell(110, 10, $trans_type, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Response Code", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->response_code, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Bank Tranaction No", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->interac_bank_transaction_id, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Bank Approval Code", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->interac_bank_approval_code, 1, 1, 'C');
    } else if ($this->payment_type_switch == 2) {      //moneris credit card
      $receipt->SetX($x + 15);
      $receipt->Cell(155, 10, str_repeat(' ', 8) . "Transaction Details", 1, 1, 'L');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Credit Card", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->credit_card, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Card No", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->credit_card_no, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Bank Tranaction No", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->moneris_bank_transaction_id, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Bank Approval Code", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->moneris_bank_approval_code, 1, 1, 'C');
    }

    //footer
    $receipt->SetY($y + 235);
    $receipt->SetTextColor(207, 207, 207);
    $receipt->SetFont('Arial', 'B', 8);
    $receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
    $receipt->SetFont('Arial', '', 7);
    $receipt->Cell(190, 5, '2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');

    $receipt->Output('D', 'receipt_SPGManagement.pdf');
    exit;
  }


  //private methods -- inner
  private function send_receipt_email($to_email, $to_name)
  {
    if (strpos(getcwd(), "admin") != false) {
      $path = "custom/";
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "../";
    }
    require_once($path . "sendSMSEmail.php");

    $email_sbj = "Receipt -- spgmanagement.com";
    $email_content = $this->get_invoice_template($to_name);

    MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $to_email, $to_name, $email_sbj, $email_content, '');
  }

  private function send_receipt_sms($to_telephone, $to_name)
  {

    if (strpos(getcwd(), "admin") != false) {
      $path = "custom/";
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "../";
    }

    require_once($path . "sendSMSEmail.php");
    //sms
    $SMS_message = "Dear " . $to_name . ",\nWe have received the payment of rent $" . $this->receipt_amount . ".\n -- spgmanagement.com";
    //SendSMS($this->formalize_telephone($to_telephone),$SMS_message);
  }

  private function formalize_telephone($original_tele)
  {
    $formal_tele = trim($original_tele);
    $formal_tele = str_replace(' ', '', $formal_tele);
    $formal_tele = str_replace('-', '', $formal_tele);
    $formal_tele = str_replace('+', '', $formal_tele);
    if (strlen($formal_tele) == 10)
      $formal_tele = '1' . $formal_tele;
    return $formal_tele;
  }


  private function get_invoice_template($to_name)
  {
    $reference_no = 'INV-' . $this->lease_payment_details_id;
    $content = '
<div align="center" style="text-align: center; width:100%; background-color:#E8E8E8;">
  <table align="center" cellspacing="0" cellpadding="0" style="max-width:600px;">
    <tbody>
    <tr style="height:30px;"></tr>
    <tr>
      <td>
        <table cellspacing="0" cellpadding="0" style="background-color:white;">
          <tbody>
          
          <tr style="background-color:white;">
            <td style="padding:15px 20px;">
              <div><span style="font-size:16px;font-family: Arial, sans-serif, serif, EmojiFont; color:#696969;">Dear&nbsp;<b>' . $to_name . '</b>,</span></div>
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">Thanks for your payment in spgmanagement.com.</span></div>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 25px;">
            <img src="https://www.spgmanagement.com/admin/phpimages/cityskyline.jpg" width = "640px"></td>
          </tr>
          
          <tr>
            <td style="height:100px;padding:0 20px;">
              <table align="left" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr height="115" style="height:80px;">
                  <td><div style="width: 240px"></div></td>
                  <td><div style="margin:0;"><div style="font-size:28px; color: #27408B; font-family: Arial, sans-serif, serif, EmojiFont;"><b>Receipt</b></div></div></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:0px 20px 10px 20px;">
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width: 600px;padding:0px 0 0px 0;">
                <tbody>
                <tr><td colspan="2" style="border-bottom:1px solid; border-left: none; text-align: left;"> From : ' . $this->company_info['name'] . ' </td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Reference No.</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $reference_no . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Tenant Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . implode(',', $this->tenants_name) . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Building Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_building . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Unit Number</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_room . ' in ' . $this->unit_floor . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Payment Due</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->due_date . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Payment Type</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->paid_types . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Payment Method</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->paid_method . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">$ ' . $this->receipt_amount . ' CAD</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Service Charge</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">$ ' . $this->service_charge . ' CAD</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Total Paid Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">$ ' . $this->total_amount . ' CAD</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Proceed By</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->proceed_by . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Proceed Time</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->proceed_time . '</td></tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/custom/invoice_receipt/invoice_receipt_controller.php?' . $this->email_url_param . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Receipt</b></span></font></a></span></font></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:6px 20px 0px 20px;">
              <div><span style="font-size:15px;">&nbsp;</span></div>
              <div align="left" style="text-align:justify;padding-top:10px;padding-bottom:0;"><span style="font-size:17px;font-weight:normal;"><i>Regards, </i><i><br> -SPGManagement Team</i></span></div>
              <div style="margin-top:5px;">&nbsp;</div>
            </td>
          </tr>

          </tbody>
        </table>
      </td>
    </tr>

    <tr>
      <td valign="middle" style="height:50px;background-color:#CFCFCF;padding:0 20px;">
        <span style="background-color:#CFCFCF;">
        <table align="left" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
          <td>
          <div>
            <span style="font-size:13px;"><a href="mailto:info@mgmgmt.ca?Subject=From%20Client" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">Contact Us</font></a></span>
            </div>
 
          </td>
        </tr>
        </tbody></table>
        </span>
      </td>
    </tr>

    <tr>
      <td align="center" style="text-align:center;padding:0 20px;">
        <div style="margin:10px 0;"><span style="font-size:15px; color: #363636">SPGManagement -- <a href="" target="_blank" rel="noopener noreferrer">15149373529</a></span></font></div>
      </td>
    </tr>

    <tr>
      <td align="center" style="text-align:center;padding:0 20px 40px 20px;">
        <div><span style="font-size:14px; color: #363636;">2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.</span></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>';

    return $content;
  }
}