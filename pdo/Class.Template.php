<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
class Template
{

  /**
   * emailTemplate - used to all sending emails function. All emails should follow this template
   *
   * @param $title String The blue words in the beginning of the content.
   * @param $subtitle String The words under the title before the logo picture.
   * @param $name String The addressed name after "Dear".
   * @param $body1 String The first paragraph.
   * @param $body2 String The second paragraph.
   * @param $button_url String the url for the button.
   * @param $button_content String the words to be shown on the button.
   * @return $email_content The email content to be used.
   */

  public function emailTemplate($title, $subtitle, $name, $body1, $body2, $button_url, $button_content, $company_id = 0, $employee_id = 0)
  {
    include('dbconfig.php');
    include_once('Class.Company.php');
    $DB_company = new Company($DB_con);
    include_once('Class.Employee.php');
    $DB_employee = new Employee($DB_con);
    $current_year = date('Y');
    if ($company_id == 0 && !empty($_SESSION['company_id'])) {
      $company_id = $_SESSION['company_id'];
    }
    if ($employee_id == 0 && !empty($_SESSION['employee_id'])) {
      $employee_id = $_SESSION['employee_id'];
    }
    // die(var_dump($company_id));
    $row = $DB_company->getCompanyInfo($company_id);
    if ($row == false) {
      die("Company not found");
    }
    $company_name = $row['name'];
    $company_register = $row['registration_name'];
    $company_phone = $row['phone'];
    $company_website = $row['website'];
    $rowe = $DB_employee->getEmployeeInfo($employee_id);
    $employee_name = $rowe['full_name'];
    $employee_phone = $rowe['phone_number'];
    $employee_email = $rowe['email'];
    $email_content = "
<div align=\"center\" style=\"text-align: center; width:90%; background-color:#E8E8E8;\">
  <table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" style=\"max-width:600px; background:#E8E8E8;\">
    <tbody>
    <tr style=\"height:30px;\"></tr>
    <tr>
      <td>
        <table cellspacing=\"0\" cellpadding=\"0\" style=\"background-color:white;\">
          <tbody style=\"text-align: left\">

          <tr>
            <td style=\"padding:30px 20px 5px 20px;\">
              <table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tbody>
                <tr>
                  <td>
                  <p align='center'> <h1 style='color:red'> <b>*** DO NO REPLY TO THIS EMAIL *** </b></h1></p><hr>
                  <div style=\"margin:0;\"><div style=\"font-size:40px; font-family:Arial, sans-serif, serif, EmojiFont; color: #00B2EE;\"><b>";
    $email_content .= $title;
    $email_content .= "</b></div></div></td>
                 </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style=\"background-color:white;\">
            <td style=\"padding:0px 20px 30px 20px;\">
              <div style=\"color:#4F4F4F;\"><span style=\"font-size:20px;font-family: Arial, sans-serif, serif, EmojiFont; color: #696969;\">";
    $email_content .= $subtitle;
    $email_content .= "</span></div>
            </td>
          </tr>

          <tr style=\"background-color:#E0F3FA;\">
            <td align=\"center\" style=\"text-align:center;width: 600px;height: 25px;\">
             <img src=\"https://www.spgmanagement.com/admin/images/cityskyline.jpg\" width='640px'> </td>
          </tr>

          <tr style=\"background-color:white;\">
            <td style=\"padding:30px 20px 20px 20px;\">
              <div><span style=\"font-size:16px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;\">Dear&nbsp;<b>";
    $email_content .= $name;
    $email_content .= "</b>,</span></div>
              <div style=\"margin-top:12pt; color:#4F4F4F;\"><span style=\"font-size:15px;font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;\">";
    $email_content .= $body1;
    $email_content .= "</span></div>
            </td>
          </tr>

          <tr style=\"background-color:white;\">
            <td style=\"padding:5px 20px;\">
              <div style=\"color:#4F4F4F;\"><span style=\"font-size:15px; font-family:Arial, sans-serif, serif, EmojiFont; color: #696969;\">";
    $email_content .= $body2;
    $email_content .= "</b></span></div>
            </td>
          </tr>


          <tr style=\"background-color:white;\">
            <td style=\"padding:6px 20px 5px 20px;\">
              <div align=\"center\" style=\"text-align:center;margin:40px 0 20px 0;\"><font face=\"Arial,sans-serif\" size=\"2\" color=\"#3DA9F5\" style=\"font-family: Arial, sans-serif, serif, EmojiFont;\"><span style=\"font-size:15px;\"><a href=\"";
    $email_content .= $button_url;
    $email_content .= "\" target=\"_blank\" rel=\"noopener noreferrer\" style=\"text-decoration:none;\"><font color=\"white\"><span style=\"background-color:#80C246;padding:15px 45px;\"><b>";
    $email_content .= $button_content;
    $email_content .= "</b></span></font></a> </span></font></div>
            </td>
          </tr>


          <tr style=\"background-color:white;\">
            <td style=\"padding:6px 20px 0px 20px;\">
              <div><span style=\"font-size:15px;\">&nbsp;</span></div>
              <div align=\"left\" style=\"text-align:justify;padding-top:10px;padding-bottom:0;\"><span style=\"font-size:17px;font-weight:normal;\"><i>Regards, </i><i><br>$company_name</i></span></div>
              <div style=\"margin-top:5px;\">&nbsp;</div>
            </td>
          </tr>


          </tbody>
        </table>
      </td>
    </tr>

    <tr>
      <td valign=\"middle\" style=\"height:50px;background-color:#CFCFCF;padding:0 20px;\">
        <span style=\"background-color:#CFCFCF;\">
        <table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tbody>
        <tr>
          <td>
          <div>

            <div style=\"margin:10px 0\"><span style=\"font - size:15px; color: #363636;\">$company_name <a href=\"http://$company_website\" target=\"_blank\" rel=\"noopener noreferrer\">$company_website</a></span></font></div>
            </div>
          </td>
        </tr>
        </tbody></table>
        </span>
      </td>
    </tr>

    <tr>
      <td align=\"center\" style=\"text-align:center;padding:0 20px;\">

      </td>
    </tr>

    <tr>
      <td style=\"padding:0 10px 10px 10px;\">
        <div><br><span style=\"font-size:14px; color: #363636;\">&copy; $current_year. For technical support please contact SPG Management +15149373529. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.<br>This e-mail message and any attachments is intended be sent by the author to the addressee(s) only and may contain information which is confidential and privileged, and it is legally protected from disclosure. Confidentiality and privilege are not lost by this email having been sent to the wrong person. Any distribution, reproduction or other use of this email by an unintended recipient is prohibited. SPG Management is not the author of this email and should be contacted only if you experience technical difficulties with the e-mail software.

<br>Ce message ainsi que toutes les pièces jointes sont destinés envoyés par l'auteur au (x) destinataire (s) uniquement et peuvent contenir des informations confidentielles et privilégiées, et il est légalement protégé de divulgué. La confidentialité et les privilèges ne sont pas nulles si cet e-mail a été envoyé à la mauvaise personne. Toute distribution, reproduction ou autre utilisation de ce courrier par un destinataire sans authorize est interdite.  SPG Management n’est pas l’auteur de ce courrier et ne doit être contacté que si vous rencontrez des difficultés techniques avec le logiciel de courrier électronique.</span></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>";
    //      die($email_content);
    return $email_content;
  }
}