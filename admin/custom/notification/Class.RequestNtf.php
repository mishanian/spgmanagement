<?php

class RequestNtf
{
  private $request_no;
  private $building_name;
  private $building_pic;
  private $unit_number;

  private $tenant_name;
  private $tenant_phone;
  private $tenant_email;

  private $request_type;
  private $message;
  private $entity_date;
  private $entity_time;
  private $responsible_employee_name;
  private $responsible_employee_phone;
  private $responsible_employee_email;

  private $location;


  function __construct($request_id)
  {
    if (getcwd() == "/home/beavertest/public_html/admin") {
      include_once("../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include_once("../../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../../../pdo/dbconfig.php");
    }
    $record = $DB_request_ntf->get_request_info($request_id);
    $this->request_no = 'RQST' . ($record['request_id'] + 1000000);
    $this->building_name = $record['building'];
    $this->building_pic = 'http://www.spgmanagement.com/admin/files/building_pictures/' . $record['building_pic'];
    $this->unit_number = $record['unit_number'];
    $tenant_id = $record['tenant_ids'];

    $temp = $DB_payment->get_tenant_info($tenant_id);
    $this->tenant_name = $temp['full_name'];
    $this->tenant_phone = $temp['mobile'];
    $this->tenant_email = $temp['email'];

    $this->request_type = $record['request'];
    $this->message = $record['message'];
    $date_time = $record['entry_datetime'];
    $this->entity_date = date('Y-m-d', strtotime($date_time));
    $this->entity_time = date("H:i:s", strtotime($date_time));

    $employee_info = $DB_request_ntf->get_employee_info($record['employee_id']);
    $this->responsible_employee_name = $employee_info['full_name'];
    $this->responsible_employee_phone = $employee_info['email'];
    $this->responsible_employee_email = $employee_info['mobile'];

    $apartment_id = $record['apartment_id'];
    $location_info = $DB_request_ntf->get_location($apartment_id);
    $this->location = $location_info['building_name'] . ' ' . $location_info['floor'] . ' ' . $location_info['unit_number'] . 'Apt.';
  }


  public function new_issue_notify_by_email()
  {
    if (getcwd() == "/home/beavertest/public_html/admin") {
      include_once("custom/sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include_once("sendSMSEmail.php");
    }

    $email_sbj = "Request -- spgmanagement.com";
    // email to employee
    $employee_name = $this->responsible_employee_name;
    $email_content = $this->new_issue_message($employee_name);
    SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->responsible_employee_email, $this->responsible_employee_name, $email_sbj, $email_content);
    // SendEmail('info@mgmgmt.ca','Info - spgmanagement.com',"tianen.chen@outlook.com",$this->responsible_employee_name,$email_sbj,$email_content);

    // email to client
    $tenant_name = $this->tenant_name;
    $email_content = $this->new_issue_message($tenant_name);
    SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->tenant_email, $tenant_name, $email_sbj, $email_content);
    //SendEmail('info@mgmgmt.ca','Info - spgmanagement.com',"Tianen.chen@outlook.com",$tenant_name,$email_sbj,$email_content);

  }


  public function new_issue_notify_by_sms()
  {
    if (getcwd() == "/home/beavertest/public_html/admin") {
      include_once("custom/sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include_once("sendSMSEmail.php");
    }
    $SMS_message = "New issue #\n" . $this->request_no . "\nDate Created:" . $this->entity_date . "\nTime:" . $this->entity_time
      . "Type:Tenant\n" . "Category:" . $this->request_type . "\nCreated By" . $this->tenant_name . "\nMobile Phone:" . $this->tenant_phone
      . "Location:" . $this->location . "\n Description:" . $this->message . "\n" . "Log in for more details";
    // sent to employee
    SendSMS($this->formalize_telephone($this->responsible_employee_phone), $SMS_message);
    //send to tenant
    SendSMS($this->formalize_telephone($this->tenant_phone), $SMS_message);
    //   SendSMS('438-921-0238',$SMS_message);

  }


  public function update_issue_notify_by_email($request_communication_id)
  {
    if (getcwd() == "/home/beavertest/public_html/admin") {
      include_once("custom/sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include_once("sendSMSEmail.php");
    }

    if (getcwd() == "/home/beavertest/public_html/admin") {
      include("../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include("../../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../../../pdo/dbconfig.php");
    }

    $request_communications_info = $DB_request_ntf->get_request_communication_info($request_communication_id);
    $assign_to_employee_info = $DB_request_ntf->get_employee_info($request_communications_info['assign_employee_id']);
    $assign_to_employee_name = $assign_to_employee_info['full_name'];
    $assign_to_employee_email = $assign_to_employee_info['email'];
    $email_sbj = "Request -- spgmanagement.com";
    // send to assign employee
    $email_content = $this->issue_update_message($assign_to_employee_name);
    SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $assign_to_employee_email, $assign_to_employee_name, $email_sbj, $email_content);
    // send to original employee
    SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->responsible_employee_email, $this->responsible_employee_name, $email_sbj, $email_content);
  }

  public function update_issue_notify_by_sms($request_communication_id)
  {
    if (getcwd() == "/home/beavertest/public_html/admin") {
      include_once("custom/sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../sendSMSEmail.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include_once("sendSMSEmail.php");
    }

    if (getcwd() == "/home/beavertest/public_html/admin") {
      include("../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom") {
      include("../../pdo/dbconfig.php");
    }
    if (getcwd() == "/home/beavertest/public_html/admin/custom/invoice_receipt") {
      include_once("../../../pdo/dbconfig.php");
    }

    $request_communications_info = $DB_request_ntf->get_request_communication_info($request_communication_id);
    $assign_to_employee_info = $DB_request_ntf->get_employee_info($request_communications_info['assign_employee_id']);
    $assign_to_employee_name = $assign_to_employee_info['full_name'];
    $assign_to_employee_phone = $assign_to_employee_info['mobile'];

    $SMS_message = "Issue Update #\n" . $this->request_no . "\nDate Created:" . $this->entity_date . "\nTime:" . $this->entity_time
      . "Type:Tenant\n" . "Category:" . $this->request_type . "\nCreated By" . $this->tenant_name . "\nMobile Phone:" . $this->tenant_phone
      . "Location:" . $this->location . "\n Description:" . $this->message . "\n" . "Log in for more details";

    //send to assign employee
    SendSMS($this->formalize_telephone($assign_to_employee_phone), $SMS_message);
    // sent to orignal employee
    SendSMS($this->formalize_telephone($this->responsible_employee_phone), $SMS_message);
    //  SendSMS('438-921-0238',$SMS_message);

  }


  private function issue_update_message($to_name)
  {
    return
      '<div align="center" style="text-align: center; width:100%; background-color:#E8E8E8;">
  <table align="center" cellspacing="0" cellpadding="0" style="max-width:600px;">
    <tbody>
    <tr style="height:30px;"></tr>
    <tr>
      <td>
        <table cellspacing="0" cellpadding="0" style="background-color:white;">
          <tbody>
          <tr>
            <td style="height:100px;padding:0 20px;">
              <table align="left" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr height="115" style="height:80px;">
                  <td><div style="margin:0;"><div style="font-size:28px; color: #00B2EE; font-family: Arial, sans-serif, serif, EmojiFont;"><b>Issue Update</b></div></div></td>
                  <td><div style="width: 160px"></div></td>
                  <td><div style="background-color:#80C246;padding:5px 20px;"><font face="Arial,sans-serif" size="2" color="white" style="font-family: Arial, sans-serif, serif, EmojiFont;"><div style="font-size:18px;background-color:#80C246;"><b>ID # ' . $this->request_no . '</b></div></font></div></td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 25px;">
            <img src="http://www.beaveraittesting.site/admin/phpimages/cityskyline.jpg" width = "640px"></td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:15px 20px;">
              <div><span style="font-size:16px;font-family: Arial, sans-serif, serif, EmojiFont; color:#696969;">Dear&nbsp;<b>' . $to_name . '</b>,</span></div>
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">There are new updates to an issue which involves you,' . $this->request_type . ' Please login for more details.</span></div>
            </td>
          </tr>

          <tr>
            <td style="background-color: white; padding-top: 6px;"></td>
          </tr>

          <tr style="text-align:center;">
            <td style="background-color:whitesmoke; padding:12px 10px 7px 10px;border-right:20px solid white;border-left:20px solid white;">
              <table align="left" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr style="text-align:center;">
                    <td><img src="' . $this->building_pic . '" height="120" style="max-width:150px;"></td>
                    <td style="padding-left: 40px"></td>
                    <td>
                      <font face="Arial,sans-serif" size="2" color="#999999" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><b>' . $this->location . '</b></span></font>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>


          <tr style="background-color:white;">
            <td style="padding:30px 20px 10px 20px;">
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:7px 0;">
                <tbody>
                <tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueissue.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Issue Category</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color:#696969;"><span style="font-size:13px;"><b>' . $this->request_type . '</b></span></font></td>
                </tr>
                </tbody>
              </table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluedate.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Date of Creation</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color: #696969;"><span style="font-size:13px;"><b>' . $this->entity_date . '</b></span></font></td>
                </tr>
                </tbody>
              </table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:7px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluetime.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Time Created</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;color: #696969;"><b>' . $this->entity_time . '</b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueuser.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Created By</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color:#696969;"><span style="font-size:13px;"><b>' . $this->tenant_name . '</b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluephone.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Mobile Number</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;"><b><a href="tel:+1%' . $this->tenant_phone . '" target="_blank" rel="noopener noreferrer">' . $this->tenant_phone . '</a></b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueemail.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Email</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;"><b><a href="mailto:' . $this->tenant_email . '" target="_blank" rel="noopener noreferrer">' . $this->tenant_email . '</a></b></span></font></td>
                </tr>
                </tbody></table>
            </td>
          </tr>

          <tr>
            <td style="padding:25px 20px 0 20px;border-top:1px solid #EEEEEE;">
              <div style="margin:0;"><font face="Arial,sans-serif" size="2" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:16px;color:#696969;"><b>Description</b></span></font></div>
              <div style="margin:0;"><font face="Arial,sans-serif" size="2" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:14px;font-weight:normal;color: #696969">' . $this->message . '</span></font></div>
            </td>
          </tr>


          <tr style="background-color:white;">
            <td style="padding:6px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>View Issue</b></span></font></a> </span></font></div>
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
            <span style="font-size:13px;"><a href="" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">Contact Us</font></a></span></div>
          </td>
        </tr>
        </tbody></table>
        </span>
      </td>
    </tr>

    <tr>
      <td align="center" style="text-align:center;padding:0 20px;">
        <div style="margin:10px 0;"><span style="font-size:15px; color: #363636">SPGManagement -- <a href="15149373529" target="_blank" rel="noopener noreferrer">15149373529</a></span></font></div>
      </td>
    </tr>

    <tr>
      <td align="center" style="text-align:center;padding:0 20px 40px 20px;">
        <div><span style="font-size:14px; color: #363636;">@2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.</span></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>

';
  }



  private function new_issue_message($to_name)
  {
    return
      '<div align="center" style="text-align: center; width:100%; background-color:#E8E8E8;">
  <table align="center" cellspacing="0" cellpadding="0" style="max-width:600px;">
    <tbody>
    <tr style="height:30px;"></tr>
    <tr>
      <td>
        <table cellspacing="0" cellpadding="0" style="background-color:white;">
          <tbody>
          <tr>
            <td style="height:100px;padding:0 20px;">
              <table align="left" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr height="115" style="height:80px;">
                  <td><div style="margin:0;"><div style="font-size:28px; color: #00B2EE; font-family: Arial, sans-serif, serif, EmojiFont;"><b>New Issue</b></div></div></td>
                  <td><div style="width: 160px"></div></td>
                  <td><div style="background-color:#80C246;padding:5px 20px;"><font face="Arial,sans-serif" size="2" color="white" style="font-family: Arial, sans-serif, serif, EmojiFont;"><div style="font-size:18px;background-color:#80C246;"><b>ID # ' . $this->request_no . '</b></div></font></div></td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 25px;"><img src="http://www.beaveraittesting.site/admin/phpimages/cityskyline.jpg" width ="640px"></td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:15px 20px;">
              <div><span style="font-size:16px;font-family: Arial, sans-serif, serif, EmojiFont; color:#696969;">Dear&nbsp;<b>' . $to_name . '</b>,</span></div>
              <div style="margin-top:12pt; color:#696969;"><span style="font-size:15px; font-family: Arial, sans-serif, serif, EmojiFont;">A new issue has been added which involves you,' . $this->request_type . ' Please login for more details.</span></div>
            </td>
          </tr>

          <tr>
            <td style="background-color: white; padding-top: 6px;"></td>
          </tr>

          <tr style="text-align:center;">
            <td style="background-color:whitesmoke; padding:12px 10px 7px 10px;border-right:20px solid white;border-left:20px solid white;">
              <table align="left" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr style="text-align:center;">
                    <td><img src="' . $this->building_pic . '" height="120" style="max-width:150px;"></td>
                    <td style="padding-left: 40px"></td>
                    <td>
                      <font face="Arial,sans-serif" size="2" color="#999999" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><b>' . $this->location . '</b></span></font>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>


          <tr style="background-color:white;">
            <td style="padding:30px 20px 10px 20px;">
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:7px 0;">
                <tbody>
                <tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueissue.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Issue Category</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color:#696969;"><span style="font-size:13px;"><b>' . $this->request_type . '</b></span></font></td>
                </tr>
                </tbody>
              </table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluedate.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Date of Creation</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color: #696969;"><span style="font-size:13px;"><b>' . $this->entity_date . '</b></span></font></td>
                </tr>
                </tbody>
              </table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:7px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluetime.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Time Created</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;color: #696969;"><b>' . $this->entity_time . '</b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueuser.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Created By</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;color:#696969;"><span style="font-size:13px;"><b>' . $this->tenant_name . '</b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconbluephone.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Mobile Number</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;"><b><a href="tel:+1%' . $this->tenant_phone . '" target="_blank" rel="noopener noreferrer">' . $this->tenant_phone . '</a></b></span></font></td>
                </tr>
                </tbody></table>
              <table width="270" align="left" cellspacing="0" cellpadding="0" style="width:270px;padding:8px 0;">
                <tbody><tr>
                  <td width="25" valign="middle" style="width:25px;padding:2px 0;"><img src="https://dlprdcaecontentst01.blob.core.windows.net/emailimage/v2/iconblueemail.png"> </td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;font-weight:normal;">Email</span></font></td>
                </tr>
                <tr>
                  <td style="padding:2px 0;"></td>
                  <td width="245" valign="middle" style="width:245px;margin:0;padding:2px 0;"><font face="Arial,sans-serif" size="1" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:13px;"><b><a href="mailto:' . $this->tenant_email . '" target="_blank" rel="noopener noreferrer">' . $this->tenant_email . '</a></b></span></font></td>
                </tr>
                </tbody></table>
            </td>
          </tr>

          <tr>
            <td style="padding:25px 20px 0 20px;border-top:1px solid #EEEEEE;">
              <div style="margin:0;"><font face="Arial,sans-serif" size="2" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:16px;color:#696969;"><b>Description</b></span></font></div>
              <div style="margin:0;"><font face="Arial,sans-serif" size="2" color="#444444" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:14px;font-weight:normal;color: #696969">' . $this->message . '</span></font></div>
            </td>
          </tr>


          <tr style="background-color:white;">
            <td style="padding:6px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>View Issue</b></span></font></a> </span></font></div>
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
        <div style="margin:10px 0;"><span style="font-size:15px; color: #363636">SPGManagement -- <a href="15149373529" target="_blank" rel="noopener noreferrer">15149373529</a></span></font></div>
      </td>
    </tr>

    <tr>
      <td align="center" style="text-align:center;padding:0 20px 40px 20px;">
        <div><span style="font-size:14px; color: #363636;">@2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.</span></div>
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