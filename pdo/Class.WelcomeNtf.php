<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

class WelcomeNtf
{
  private $user_full_name;
  private $user_name;
  private $user_email;
  private $user_password;

  function __construct($user_id)
  {

    if (strpos(getcwd(), 'admin') != false) {
      $path = '..';
    }
    if (strpos(getcwd(), "custom") != false) {
      $path = "../..";
    }
    if (strpos(getcwd(), "invoice_receipt") != false) {
      $path = "../../..";
    }
    include($path . "/pdo/dbconfig.php");
    include_once($path . "/pdo/Class.Notification.php");
    $DB_request_ntf = new Notification($DB_con);

    $user_info = $DB_request_ntf->get_user_info($user_id);
    //  echo var_dump($DB_request_ntf);
    $this->user_full_name = $user_info['full_name'];
    $this->user_name = $user_info['username'];
    $this->user_email = $user_info['email'];
    $this->user_password = $user_info['userpass'];

    include_once('Class.Company.php');
    $DB_company = new Company($DB_con);
    include_once('Class.Employee.php');
    $this->current_year = date('Y');
    if (!empty($_SESSION['company_id'])) {
      $company_id = $_SESSION['company_id'];
    } else {
      die("Please login");
    }

    // die(var_dump($company_id));
    $row = $DB_company->getCompanyInfo($company_id);

    $this->company_name = $row['name'];
    $this->company_register = $row['registration_name'];
    $this->company_phone = $row['phone'];
    $this->company_website = $row['website'];
  }

  function send_welcome_email()
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

    $email_sbj = "Welcome to use our system - $this->company_name";
    $email_content = $this->get_welcome_email_template();
    MySendEmail('info@mgmgmt.ca', "Info - $this->company_name", $this->user_email, $this->user_full_name, $email_sbj, $email_content);
    echo "email send to $this->user_full_name - $this->user_email<br>\n";
  }



  private function get_welcome_email_template()
  {
    return
      "<div align='center' style='text-align: center; width:100%; background-color:#E8E8E8;'>
  <table align='center' cellspacing='0' cellpadding='0' style='max-width:600px;'>
    <tbody>
    <tr style='height:30px;'></tr>
    <tr>
      <td>
        <table cellspacing='0' cellpadding='0' style='background-color:white;'>
          <tbody>

          <tr>
            <td style='padding:30px 20px 5px 20px;'>
              <table align='left' border='0' cellspacing='0' cellpadding='0'>
                <tbody>
                <tr>
                  <td><div style='margin:0;'><div style='font-size:40px; font-family:Arial, sans-serif, serif, EmojiFont; color: #00B2EE;'><b>Welcome!</b></div></div></td>
                 </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:0px 20px 30px 20px;'>
              <div style='color:#4F4F4F;'><span style='font-size:20px;font-family: Arial, sans-serif, serif, EmojiFont; color: #696969;'>Simplify your management experience with $this->company_name</span></div>
            </td>
          </tr>

          <tr style='background-color:#E0F3FA;'>
            <td align='center' style='text-align:center;width: 600px;height: 25px;'>
            <img src='https://$this->company_website/admin/images/cityskyline.jpg' width = '640px'></td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:30px 20px 20px 20px;'>
              <div><span style='font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;'>Dear&nbsp;<b>$this->user_full_name</b>,</span></div>
              <div style='margin-top:12pt; color:#4F4F4F;'><span style='font-size:15px;font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;'>Welcome to $this->company_name! We look forward to having you as our newest user. You are about to join a service aimed at improving the rental experience.</span></div>
            </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:5px 20px;'>
              <div style='color:#4F4F4F;'><span style='font-size:15px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;'>Your username is <b style='color: #4876FF;'>$this->user_name</b></span></div>
            </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:5px 20px;'>
              <div style='color:#4F4F4F;'><span style='font-size:15px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;'>Your password is <b style='color: #4876FF;'>$this->user_password</b></span></div>
            </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:30px 20px 20px 20px;'>
              <div><span style='font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;'><b>We strongly suggest changing your password after login your account !</b></span></div>
              </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:6px 20px 5px 20px;'>
              <div align='center' style='text-align:center;margin:40px 0 20px 0;'><font face='Arial,sans-serif' size='2' color='#3DA9F5' style='font-family: Arial, sans-serif, serif, EmojiFont;'><span style='font-size:15px;'>
              <a href='https://$this->company_website/admin/login' target='_blank' rel='noopener noreferrer' style='text-decoration:none;'><font color='white'><span style='background-color:#80C246;padding:15px 45px;'><b>Login</b></span></font></a> </span></font></div>
            </td>
          </tr>

          <tr style='background-color:white;'>
            <td style='padding:6px 20px 0px 20px;'>
              <div><span style='font-size:15px;'>&nbsp;</span></div>
              <div align='left' style='text-align:justify;padding-top:10px;padding-bottom:0;'><span style='font-size:17px;font-weight:normal;'><i>Regards, </i><i><br> -$this->company_name Team</i></span></div>
              <div style='margin-top:5px;'>&nbsp;</div>
            </td>
          </tr>

          </tbody>
        </table>
      </td>
    </tr>

    <tr>
      <td valign='middle' style='height:50px;background-color:#CFCFCF;padding:0 20px;'>
        <span style='background-color:#CFCFCF;'>
        <table align='left' border='0' cellspacing='0' cellpadding='0'>
        <tbody>
        <tr>
          <td>
          <div>
            <span style='font-size:13px;'><a href='mailto:info@mgmgmt.ca?Subject=From%20Client' target='_blank' rel='noopener noreferrer' style='text-decoration:none;'>Contact Us</font></a></span></div>
          </td>
        </tr>
        </tbody></table>
        </span>
      </td>
    </tr>

    <tr>
      <td align='center' style='text-align:center;padding:0 20px;'>
        <div style='margin:10px 0;'><span style='font-size:15px; color: #363636'>$this->company_name <a href='http://$this->company_website' target='_blank' rel='noopener noreferrer'>$this->company_website</a></span></font></div>
      </td>
    </tr>

    <tr>
      <td align='center' style='text-align:center;padding:0 20px 40px 20px;'>
      <div><br><span style=\'font-size:14px; color: #363636;\'>&copy; $this->current_year. For technical support please contact SPG Management +15149373529. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.<br>This e-mail message and any attachments is intended be sent by the author to the addressee(s) only and may contain information which is confidential and privileged, and it is legally protected from disclosure. Confidentiality and privilege are not lost by this email having been sent to the wrong person. Any distribution, reproduction or other use of this email by an unintended recipient is prohibited. SPG Management is not the author of this email and should be contacted only if you experience technical difficulties with the e-mail software.

      <br>Ce message ainsi que toutes les pièces jointes sont destinés envoyés par l’auteur au (x) destinataire (s) uniquement et peuvent contenir des informations confidentielles et privilégiées, et il est légalement protégé de divulgué. La confidentialité et les privilèges ne sont pas nulles si cet e-mail a été envoyé à la mauvaise personne. Toute distribution, reproduction ou autre utilisation de ce courrier par un destinataire sans authorize est interdite.  SPG Management n’est pas l’auteur de ce courrier et ne doit être contacté que si vous rencontrez des difficultés techniques avec le logiciel de courrier électronique.</span></div>

      </td>
    </tr>
    </tbody>
  </table>
</div>

";
  }
}