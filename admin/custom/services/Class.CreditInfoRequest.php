<?php


class CreditCheckRequest
{

  private $landlord_name;
  private $landlord_phone;
  private $landlord_email;

  private $tenant_name;
  private $tenant_email;
  private $auto_token;

  function __construct($credit_check_request_id)
  {
    if (!isset($DB_services)) {
      if (strpos(getcwd(), "admin") != false) {
        $path = "..";
      }
      if (strpos(getcwd(), "custom") != false) {
        $path = "../..";
      }
      if (strpos(getcwd(), "services") != false) {
        $path = "../../..";
      }

      include($path . "/pdo/dbconfig.php");
      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }
      include_once("$path/pdo/dbconfig.php");
      include_once("$path/pdo/Class.Services.php");
      $DB_services = new Services($DB_con);
    }
    $credit_check_info = $DB_services->get_credit_check_request_info_by_id($credit_check_request_id);
    $this->landlord_name = $credit_check_info['requester_name'];
    $this->landlord_email = $credit_check_info['requester_mail'];
    $this->landlord_phone = $credit_check_info['requester_phone'];

    $this->tenant_name = $credit_check_info['tenant_name'];
    $this->tenant_email = $credit_check_info['requester_mail'];
    $this->auto_token = $credit_check_info['auth_token'];
  }

  function send_credit_info_request_email()
  {

    require_once("../sendSMSEmail.php");
    $email_sbj = "Credit Information Request -- spgmanagement.com";
    $email_content = $this->get_credit_info_request_template();
    MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->tenant_email, $this->tenant_name, $email_sbj, $email_content);
  }

  function send_credit_info_request_sms()
  {

    require_once("../sendSMSEmail.php");
    $message = "Dear " . $this->tenant_name . ",\nWe are requested to make a investigation about your credit status.Please check your email, or go to\nwww.8zar8.com/credit_check?autho=" . $this->auto_token . "directly.\nThanks for your cooperation!\n-- spgmanagement.com";
    SendSMS($this->tenant_name, $message);
  }



  private function get_credit_info_request_template()
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
            <td style="padding:30px 20px 5px 20px;">
              <table align="left" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                  <td><div style="margin:0;"><div style="font-size:40px; font-family:Arial, sans-serif, serif, EmojiFont; color: #00B2EE;"><b>Credit Information Request</b></div></div></td>
                 </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;width: 600px;height: 25px;">
            <img src="https://www.spgmanagement.com/admin/phpimages/cityskyline.jpg" width = "640px"></td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">Dear&nbsp;<b>' . $this->user_full_name . '</b>,</span></div>
              <div style="margin-top:12pt; color:#4F4F4F;"><span style="font-size:15px;font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">We are requested to make a investigation about your credit status.</span></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;"><b>Please click the link below, fill your personal information about credit. We will check your credit based on the information you provided.</b></span></div>
              </td>
          </tr>

         <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;"><a href="www.8zar8.com/credit_check?auth=' . $this->auto_token . '">www.8zar8.com/credit_check?auth=' . $this->auto_token . '</a></span></div>
              </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:6px 20px 5px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/login" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Fill Information Now !</b></span></font></a> </span></font></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;"><b>Thanks for your cooperation!</b></span></div>
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
}