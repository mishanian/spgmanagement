<?php


class DepositReceipt
{
  private $deposit_id;
  private $deposit_status;
  private $deposit_type;

  //tenants info
  private $tenant_name;
  private $tenant_email;
  private $tenant_telephone;

  // unit info
  private $company_name;
  private $unit_building;
  private $unit_floor;
  private $unit_room;

  //payment
  private $proceed_by;
  private $receipt_amount;
  private $paid_method;
  private $paid_date;
  private $comments;

  //return deposit
  private $return_date;
  private $return_employee;


  function __construct($deposit_id)
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
    require($path . "/pdo/Class.Payment.php");
    $DB_payment = new Payment($DB_con);


    $info = $DB_payment->get_deposit_info($deposit_id);
    $this->deposit_id = $deposit_id;
    //tenant info
    $tenant_id = $info['tenant_id'];
    $tenant_info = $DB_payment->get_tenant_info($tenant_id);
    $this->tenant_name = $tenant_info['full_name'];
    $this->tenant_email = $tenant_info['email'];
    $this->tenant_telephone = $tenant_info['mobile'];

    $this->deposit_status = $info['deposit_status'];
    $this->deposit_type = $info['deposit_type'];
    $this->company_name = $info['company_name'];
    $this->unit_building = $info['building_name'];
    $this->unit_floor = $info['floor'];
    $this->unit_room = $info['unit_number'];

    $this->proceed_by = $info['proceed_by'];
    $this->receipt_amount = $info['amount'];
    $this->paid_date = $info['paid_date'];
    $this->paid_method = $info['payment_method'];
    $this->comments = 'Deposit receipt ' . $info['comments'];

    $this->return_date = $info['return_date'];
    $this->return_employee = $info['return_employee_id'];
    if ($this->return_employee > 0)
      $this->return_employee = $DB_payment->get_employee_info($this->return_employee)['full_name'];
  }


  function send_dp_receipt_by_email()
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

    $to_name = $this->tenant_name;
    $to_email = $this->tenant_email;
    $email_sbj = "Receipt -- spgmanagement.com";
    if ($this->deposit_status > 0)
      $email_content = $this->dp_return_receipt_email_template($to_name);
    else
      $email_content = $this->dp_get_receipt_email_template($to_name);
    MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $to_email, $to_name, $email_sbj, $email_content);
  }



  function send_dp_receipt_by_sms()
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

    $to_name = $this->tenant_name;
    $to_telephone = $this->tenant_telephone;

    if ($this->deposit_status > 0)
      $SMS_message = "Dear " . $to_name . ",\n We have returned the payment of deposit $" . $this->receipt_amount . ". \n If you never received any return from us, please contact with us.\nFor more details, please check receipt in email or login spgmanagement.com\n -- spgmanagement.com";
    else
      $SMS_message = "Dear " . $to_name . ",\n We have received the payment of deposit $" . $this->receipt_amount . ". \nFor more details, please check receipt in email or login spgmanagement.com\n -- spgmanagement.com";
    SendSMS($this->formalize_telephone($to_telephone), $SMS_message);
  }

  //------------------ download pdf -----------------

  function dp_receipt_download()
  {
    if ($this->deposit_status > 0)
      $this->dp_return_receipt_download();
    else
      $this->dp_get_receipt_download();
  }

  private function dp_return_receipt_download()
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

    //header
    //        $receipt->SetXY($x+140,$y);
    //        $receipt->SetFont('Arial','B',10);
    //        $receipt->SetTextColor(79,148,205);
    //        $receipt->Cell(50,8,"NO.".$this->invoice_number,0,0,'R');

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
    $receipt->Cell(110, 10, 'From : ' . $this->company_name, 0, 1, 'L');

    //receipt id
    $receipt_id = 'R' . ($this->deposit_id + 100000);
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Receipt ID", 1, 0, 'C');
    $receipt->Cell(110, 10, $receipt_id, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Tenant Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->tenant_name, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Building Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_building, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Unit Number", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_room . ' in ' . $this->unit_floor, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Deposit Type", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->deposit_type, 1, 1, 'C');


    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->receipt_amount . ' CAD', 1, 1, 'C');


    //paid date
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Paid Date", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->paid_date, 1, 1, 'C');


    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Return Date", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->return_date, 1, 1, 'C');


    //return by
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Return By", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->return_employee, 1, 1, 'C');

    //footer
    $receipt->SetY($y + 225);
    $receipt->SetTextColor(207, 207, 207);
    $receipt->SetFont('Arial', 'B', 8);
    $receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
    $receipt->SetFont('Arial', '', 7);
    $receipt->Cell(190, 5, '2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');

    $receipt->Output('D', 'invoice_SPGManagement.pdf');
    exit;
  }

  private function dp_get_receipt_download()
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

    //header
    //        $receipt->SetXY($x+140,$y);
    //        $receipt->SetFont('Arial','B',10);
    //        $receipt->SetTextColor(79,148,205);
    //        $receipt->Cell(50,8,"NO.".$this->invoice_number,0,0,'R');

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
    $receipt->Cell(110, 10, 'From : ' . $this->company_name, 0, 1, 'L');

    //receipt id
    $receipt_id = 'D' . ($this->deposit_id + 100000);
    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Receipt ID", 1, 0, 'C');
    $receipt->Cell(110, 10, $receipt_id, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Tenant Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->tenant_name, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Building Name", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_building, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Unit Number", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->unit_room . ' in ' . $this->unit_floor, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Deposit Type", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->deposit_type, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Payment Method", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->paid_method, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Amount", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->receipt_amount . ' CAD', 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Paid Date", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->paid_date, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Comment", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->comments, 1, 1, 'C');

    $receipt->SetX($x + 15);
    $receipt->Cell(45, 10, "Proceed By", 1, 0, 'C');
    $receipt->Cell(110, 10, $this->proceed_by, 1, 1, 'C');


    //footer
    $receipt->SetY($y + 225);
    $receipt->SetTextColor(207, 207, 207);
    $receipt->SetFont('Arial', 'B', 8);
    $receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
    $receipt->SetFont('Arial', '', 7);
    $receipt->Cell(190, 5, '2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');

    $receipt->Output('D', 'invoice_SPGManagement.pdf');
    exit;
  }


  // ----------------  email template --------------

  private function dp_return_receipt_email_template($to_name)
  {
    $receipt_id = 'R' . ($this->deposit_id + 100000);
    return
      '
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
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">Your deposit has been returned, Please login for more details. If It is a wrong message, Please contact with us.</span></div>
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
                <tr><td colspan="2" style="border-bottom:1px solid; border-left: none; text-align: left;"> From : ' . $this->company_name . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Receipt ID</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $receipt_id . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Tenant Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->tenant_name . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Building Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_building . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Unit Number</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_room . ' in ' . $this->unit_floor . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->receipt_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Paid Date</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->paid_date . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Return Date</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->return_date . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Return By</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->return_employee . '</td></tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/custom/invoice_receipt/invoice_receipt_controller.php?download_dp_receipt=0&dp_id=' . $this->deposit_id . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Receipt</b></span></font></a> </span></font></div>
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
</div>

';
  }

  private function dp_get_receipt_email_template($to_name)
  {
    $receipt_id = 'D' . ($this->deposit_id + 100000);
    return
      '
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
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">Thanks for your payment in spgmanagement.com, Please login for more details.</span></div>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 234px;">
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
                <tr><td colspan="2" style="border-bottom:1px solid; border-left: none; text-align: left;"> From : ' . $this->company_name . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Receipt ID</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $receipt_id . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Tenant Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->tenant_name . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Building Name</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_building . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Unit Number</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->unit_room . ' in ' . $this->unit_floor . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Payment Method</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->paid_method . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->receipt_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Paid Date</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->paid_date . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Comment</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->comments . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Proceed By</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $this->proceed_by . '</td></tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/custom/invoice_receipt/invoice_receipt_controller.php?download_dp_receipt=0&dp_id=' . $this->deposit_id . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Receipt</b></span></font></a> </span></font></div>
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
</div>

';
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
}