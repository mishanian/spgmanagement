<?php
// namespace PHPMaker2023\spgmanagement;
// use \Request;
class WelcomeNtf
{
  private $user_full_name;
  private $user_name;
  private $user_email;
  private $user_password;

  function __construct($user_id)
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
    include_once($path . "/pdo/dbconfig.php");
    global $DB_con;
    if (!class_exists('Request')) {
      include($path . "/pdo/Class.Request.php");
    }
    $DB_request_ntf = new Request($DB_con);

    $user_info = $DB_request_ntf->get_user_info($user_id);
    // die(var_dump($user_info));
    $this->user_full_name = $user_info['full_name'];
    $this->user_name = $user_info['username'];
    $this->user_email = $user_info['email'];
    $this->user_password = $user_info['userpass'];
  }

  function send_welcome_email()
  {
    global $path;
    include_once($path . "custom/sendSMSEmail.php");

    $email_sbj = "Welcome to use our system -- spgmanagement.com";
    $email_content = $this->get_welcome_email_template();

    MySendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $this->user_email, $this->user_full_name, $email_sbj, $email_content, false);
  }



  private function get_welcome_email_template()
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
                  <td><div style="margin:0;"><div style="font-size:40px; font-family:Arial, sans-serif, serif, EmojiFont; color: #00B2EE;"><b>Welcome!</b></div></div></td>
                 </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:0px 20px 30px 20px;">
              <div style="color:#4F4F4F;"><span style="font-size:20px;font-family: Arial, sans-serif, serif, EmojiFont; color: #696969;">Simplify your management experience with SPGManagement</span></div>
            </td>
          </tr>

          <tr style="background-color:#E0F3FA;">
            <td align="center" style="text-align:center;width: 600px;height: 25px;">
            <img src="https://www.spgmanagement.com/admin/phpimages/cityskyline.jpg" width = "640px"></td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">Dear&nbsp;<b>' . $this->user_full_name . '</b>,</span></div>
              <div style="margin-top:12pt; color:#4F4F4F;"><span style="font-size:15px;font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">Welcome to SPGManagement! We look forward to having you as our newest user. You are about to join a service aimed at improving the rental experience.</span></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:5px 20px;">
              <div style="color:#4F4F4F;"><span style="font-size:15px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">Your username is <b style="color: #4876FF;">' . $this->user_name . '</b></span></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:5px 20px;">
              <div style="color:#4F4F4F;"><span style="font-size:15px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;">Your password is <b style="color: #4876FF;">' . $this->user_password . '</b></span></div>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div><span style="font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;"><b>We strongly suggest changing your password after login your account !</b></span></div>
              </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:6px 20px 5px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="https://www.spgmanagement.com/admin/login" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Login SPGManagement</b></span></font></a> </span></font></div>
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