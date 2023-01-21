<?php

class KijijiReceipt
{
  private $kijiji_payment_id;
  private $buy_slots_price;
  private $buy_slots_count;
  private $slots_due_time;
  private $payment_time;
  private $payment_amount;
  private $C_F_amount;
  private $invoice_number;
  private $employee_name;
  private $employee_email;
  private $total_amount;
  private $payment_method;
  private $payment_type;


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



  public function __construct($kijiji_payment_id)
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

    $this->kijiji_payment_id = $kijiji_payment_id;

    $info = $DB_payment->get_kjj_payment_info_for_receipt($kijiji_payment_id);
    $this->invoice_number = $info['inovice_number'];
    $this->employee_name = $info['employee_name'];
    $this->employee_email = $info['email'];

    $this->buy_slots_count = $info['buy_slots_count'];
    $this->buy_slots_price = $info['buy_slots_price'];
    $this->payment_time = $info['payment_time'];
    $this->slots_due_time = $info['slots_due_time'];

    $this->payment_amount = $info['payment_amount'];
    $this->C_F_amount = $info['C_F_amount'];
    $this->total_amount = $info['total_amount'];
    $this->payment_method = $info['payment_method'];
    $this->payment_type = $info['payment_type'];


    if (!is_null($info['transactions_paypal_id'])) {
      $paypal_transaction_info = $DB_payment->get_kjj_paypal_transaction_info($info['transactions_paypal_id']);
      $this->paypal_id = $paypal_transaction_info['p_id'];
      $this->paypal_status = $paypal_transaction_info['p_state'];
      $this->p_payer_id = $paypal_transaction_info['p_payer_id'];
      $this->payment_type_switch = 0;
    } elseif (!is_null($info['transactions_moneris_id'])) {
      $moneris_transaction_info = $DB_payment->get_kjj_moneris_transaction_info($info['transactions_moneris_id']);

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
    $email_content = $this->get_receipt_template($this->employee_name);
    SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->employee_email, $this->employee_name, $email_sbj, $email_content);
  }

  function download_receipt()
  {
    require_once('fpdf/fpdf.php');

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
    $y = $receipt->GetY() + 15;

    //table
    $receipt->SetXY($x + 15, $y);
    $receipt->SetTextColor(54, 54, 54);
    $receipt->SetFont('Arial', '', 11);

    $receipt->SetX($x + 15);
    $receipt->Cell(110, 10, 'From : spgmanagement.com', 0, 1, 'L');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Order No.", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->invoice_number, 1, 1, 'C');

    $reference_no = 'KJJ-' . $this->kijiji_payment_id;
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Reference No.", 1, 0, 'C');
    $receipt->Cell(110, 10, $reference_no, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Price($/slot/mon.)", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->buy_slots_price, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Slots Count", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->buy_slots_count, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Slot Due Time", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->slots_due_time, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format($this->payment_amount, 2) . 'CAD', 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Service Charge", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format($this->C_F_amount, 2) . 'CAD', 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Total Paid Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, '$' . number_format($this->total_amount, 2) . 'CAD', 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Type", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->payment_type, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Method", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->payment_method, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Status", 1, 0, 'C');
    $receipt->Cell(110, 10, "SUCCESS", 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Proceed Time", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->payment_time, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(155, 10, str_repeat(' ', 8) . "Transaction Details", 1, 1, 'L');


    if ($this->payment_type_switch == 0) {    //paypal
      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Paypay ID", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->paypal_id, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Transaction Status", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->paypal_status, 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Paypay Payer ID", 1, 0, 'C');
      $receipt->Cell(110, 10, $this->p_payer_id, 1, 1, 'C');
    } else if ($this->payment_type_switch == 1) {      //interac
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
    $receipt->SetY($y + 225);
    $receipt->SetTextColor(207, 207, 207);
    $receipt->SetFont('Arial', 'B', 8);
    $receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
    $receipt->SetFont('Arial', '', 7);
    $receipt->Cell(190, 5, '© 2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');
    $receipt->Output('D', 'receipt_SPGManagement.pdf');
    exit;
  }


  private function get_receipt_template($to_name)
  {
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
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">You have paid for Kijiji Posting Slots in spgmanagement.com successfully! </span></div>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 25px;"><img src="https://www.spgmanagement.com/admin/phpimages/cityskyline.jpg" width = "640px"></td>
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
                <tr><td colspan="2" style="border-bottom:1px solid; border-left: none; text-align: left;"> From : spgmanagement.com </td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Invoice No</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->invoice_number . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Price($/slot/mon.)</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->buy_slots_price . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Slots Count</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->buy_slots_count . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Slots Due Time</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->slots_due_time . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->payment_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Convenience Fee</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->C_F_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Total Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->total_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Proceed Time</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->payment_time . '</td></tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/custom/invoice_receipt/invoice_receipt_controller.php?download_kjj_receipt&kjj_payment_id=' . $this->kijiji_payment_id . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Receipt</b></span></font></a> </span></font></div>
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
            <span style="font-size:13px;"><a href="mailto:info@mgmgmt.ca?Subject=From%20Client" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">Contact Us</font></a></span></div>
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