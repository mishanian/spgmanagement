<?php

class Invoice
{
  private $lease_payment_id;
  private $invoice_number;
  //tenants info
  private $tenants_name;
  private $tenants_email;
  private $tenants_telephone;
  // unit info
  private $company_info;
  private $unit_building;
  private $unit_floor;
  private $unit_room;
  //invoice info
  private $due_date;
  private $rent_outstanding;
  private $rent_amount;
  private $paid_status;
  private $comments;
  //payment details
  private $payment_details;

  //tax
  private $apartment_type;
  private $area;
  private $gst;
  private $qst;
  private $hst;

  private $path;


  public function __construct($lease_payment_id)
  {
    $path = "/home/spgmgmt/public_html";
    if (strpos(getcwd(), "admin") != false) {
      $path = "..";
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "../..";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "../../..";
    }
    include($path . "/pdo/dbconfig.php");
    include_once($path . "/pdo/Class.Payment.php");
    $DB_payment = new Payment($DB_con);

    $this->lease_payment_id = $lease_payment_id;
    $this->invoice_number = 'INV-' . date('Y') . '-' . $lease_payment_id;

    $row = $DB_payment->get_invoice_info($lease_payment_id);
    $this->due_date = $row['due_date'];
    $this->rent_outstanding = $row['outstanding'];
    $this->rent_amount = $row['total'];

    $tenant_ids = explode(',', $row['tenants_id']);
    $this->tenants_email = array();
    $this->tenants_telephone = array();
    $this->tenants_name = array();

    foreach ($tenant_ids as $id) {
      $temp = $DB_payment->get_tenant_info($id);
      array_push($this->tenants_name, $temp['full_name']);
      array_push($this->tenants_email, $temp['email']);
      if (!empty($temp['mobile_number'])) {
        array_push($this->tenants_telephone, $temp['mobile_number']);
      } else {
        array_push($this->tenants_telephone, "");
      }
    }

    $this->unit_building = $row['building'];
    $this->unit_floor = $row['floor'];
    $this->unit_room = $row['unit'];
    $this->company_info = $DB_payment->get_company_info_from_invoice($lease_payment_id);
    $this->paid_status = $row['payment_status'];
    $this->comments = $row['comment'];

    //payment details
    $this->payment_details = $DB_payment->get_all_payment_details_for_invoice($lease_payment_id);

    //tax
    $this->apartment_type = $row['apartment_type_id'];
    $this->area = $row['area'];
    $this->gst = $row['tax1'];
    $this->qst = $row['tax2'];
    $this->hst = $row['tax3'];
  }

  public function send_invoice_by_email()
  {

    $tenant_number = sizeof($this->tenants_name);
    for ($i = 0; $i < $tenant_number; $i++) {
      $to_email = $this->tenants_email[$i];
      $to_name = $this->tenants_name[$i];
      if ($to_email != null)
        $this->send_invoice_email($to_email, $to_name);
    }
  }

  public function send_invoice_by_sms()
  {

    $tenant_number = sizeof($this->tenants_name);
    for ($i = 0; $i < $tenant_number; $i++) {
      $to_telephone = $this->tenants_telephone[$i];
      $to_name = $this->tenants_name[$i];
      if ($to_telephone != null)
        $this->send_invoice_sms($to_telephone, $to_name);
    }
  }

  public function invoice_download()
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
    $receipt->Image('../images/logo.png');


    $receipt->SetXY($x, $y);
    $receipt->SetFont('Arial', 'B', 17);
    $receipt->SetTextColor(39, 64, 139);
    $receipt->Cell(190, 15, "Invoice", 0, 1, 'C');
    $y = $receipt->GetY() + 15;

    //table
    $receipt->SetXY($x + 15, $y);
    $receipt->SetTextColor(54, 54, 54);
    $receipt->SetFont('Arial', '', 11);

    $receipt->SetX($x + 15);
    $receipt->Cell(110, 10, 'From : ' . $this->company_info['name'], 0, 1, 'L');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Invoice No", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->invoice_number, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Tenant Name", 1, 0, 'C');
    $receipt->Cell(110, 10, implode(",", $this->tenants_name), 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Building Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_building, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Unit Number", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_room . ' in ' . $this->unit_floor, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Rent Due", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->due_date, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Rent Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, number_format($this->rent_amount, 2) . ' CAD', 1, 1, 'C');

    if ($this->apartment_type == 2) {
      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Tax", 1, 0, 'C');
      $receipt->Cell(110, 10, 'GST : ' . sprintf("%.2f", ($this->gst * $this->area / 12)) . ' CAD ' . '/ QST : ' . sprintf("%.2f", ($this->qst * $this->area / 12)) . ' CAD' . '/ HST : ' . sprintf("%.2f", ($this->hst * $this->area / 12)) . ' CAD', 1, 1, 'C');

      $receipt->SetX($x + 15);
      $receipt->Cell(45, 10, "Total Amount", 1, 0, 'C');

      $total_acount = sprintf('%.2f', ($this->rent_amount + ($this->gst * $this->area / 12) + ($this->qst * $this->area / 12) + ($this->hst * $this->area / 12)));
      $receipt->Cell(110, 10, $total_acount . 'CAD', 1, 1, 'C');
    }

    //paid details
    if (sizeof($this->payment_details) > 0) {
      $receipt->SetX($x + 15);
      $receipt->Cell(155, 10, "Payment Details", 1, 1, 'C');
      $receipt->SetX($x + 15);
      $receipt->Cell(40, 8, "Payment Time", 1, 0, 'C');
      $receipt->Cell(40, 8, "Payment Amount", 1, 0, 'C');
      $receipt->Cell(40, 8, "Payment Method", 1, 0, 'C');
      $receipt->Cell(35, 8, "Balance", 1, 1, 'C');

      $count = $this->rent_amount;

      foreach ($this->payment_details as $r) {
        $receipt->SetX($x + 15);
        $receipt->Cell(40, 8, $r['entry_datetime'], 1, 0, 'C');
        $receipt->Cell(40, 8, $r['amount'], 1, 0, 'C');
        $receipt->Cell(40, 8, $r['payment_method'], 1, 0, 'C');
        $count -= $r['amount'];
        $receipt->Cell(35, 8, number_format($count, 2), 1, 1, 'C');
      }
    }

    //footer
    $receipt->SetY($y + 225);
    $receipt->SetTextColor(207, 207, 207);
    $receipt->SetFont('Arial', 'B', 8);
    $receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
    $receipt->SetFont('Arial', '', 7);
    $receipt->Cell(190, 5, '2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');

    $receipt->Output('D', 'invoice_spgmanagement.pdf');
    exit;
  }



  //private methods -- inner

  private function send_invoice_email($to_email, $to_name)
  {
    $path = "/home/spgmgmt/public_html/admin/custom/";
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

    $email_sbj = "Invoice of rent -- spgmanagement.com";
    $email_content = $this->get_invoice_template($to_name);
    MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $to_email, $to_name, $email_sbj, $email_content);
  }

  private function send_invoice_sms($to_telephone, $to_name)
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
    $SMS_message = "Dear " . $to_name . ",\nYour invoice of rent this month is ready, please check email or download it from www.spgmanagement.com/admin/login\n-- spgmanagement.com";
    SendSMS($this->formalize_telephone($to_telephone), $SMS_message);
  }

  private function formalize_telephone($original_tele)
  {
    $formal_tele = trim($original_tele);
    $formal_tele = str_replace(' ', '', $formal_tele);
    $formal_tele = str_replace('-', '', $formal_tele);
    if (strlen($formal_tele) == 10)
      $formal_tele = '1' . $formal_tele;
    return $formal_tele;
  }


  private function get_invoice_template($to_name)
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
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">The invoice of rent this month is ready !</span></div>
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
                  <td><div style="margin:0;"><div style="font-size:28px; color: #27408B; font-family: Arial, sans-serif, serif, EmojiFont;"><b>Invoice</b></div></div></td>
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
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Invoice No</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->invoice_number . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Tenant Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . implode(',', $this->tenants_name) . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Building Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_building . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Unit Number</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_room . ' in ' . $this->unit_floor . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Rent Date</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->due_date . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Rent Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . number_format($this->rent_amount, 2) . '</td></tr>';
    if ($this->apartment_type == 2) {
      $content = $content . '<tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Taxes</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;"> GST:' . sprintf("%.2f", ($this->gst * $this->area / 12)) . ' / QST:' . sprintf("%.2f", ($this->qst * $this->area / 12)) . ' / HST:' . sprintf("%.2f", ($this->hst * $this->area / 12)) . '</td></tr>';
      $content = $content . '<tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount Total</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . ($this->rent_amount + ($this->gst * $this->area / 12) + ($this->qst * $this->area / 12) + ($this->hst * $this->area / 12)) . '</td></tr>';
    }
    $content = $content . '
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/custom/invoice_receipt/invoice_receipt_controller.php?download_invoice=0&lease_payment_id=' . $this->lease_payment_id . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Invoice</b></span></font></a> </span></font></div>
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
        </tbody>
        </table>
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
</div>

';
    return $content;
  }
}