<?php

if ($_POST['gateway'] == 'm2') {
  $purchaser = $_POST['purchaser'];
  $transaction_amount = $_POST['amount'];
  $payment_method = 'Moneris/Interac';
  $iss_name = $_POST['iss_name'];
  $iss_conf = $_POST['iss_conf'];
  $bank_trans_id = $_POST['bank_trans_id'];
  $timestamp = $_POST['timestamp'];
  $invoice_id = $_POST['invoice_id'];
  $record_id = $_POST['custom_id'];
  echo $record_id;
?>

<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin: 30px 30px">
        <div class="col-md-12 col-sm-12" style="margin:40px 30px">
            <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
                <img src="images/payment_success.png" style="max-height: 150px; max-width: 150px;">
            </div>
            <div class="col-md-8 col-sm-8" style="vertical-align: center">
                <h2 style="color: #7CCD7C">Your payment has been approved !</h2>
            </div>
        </div>


        <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2"
            style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
            <div class="col-sm-6 col-md-6" style="text-align: left;">
                <dl style="margin-bottom: 5px">Purchaser :</dl>
                <dl style="margin-bottom: 5px">Transaction Amount:</dl>
                <dl style="margin-bottom: 5px">Payment Method :</dl>
                <dl style="margin-bottom: 5px">Issuer Name :</dl>
                <dl style="margin-bottom: 5px">Issuer Confirmation :</dl>
                <dl style="margin-bottom: 5px">Bank Transaction No :</dl>
                <dl style="margin-bottom: 5px">Date/Time :</dl>
                <dl style="margin-bottom: 5px">Invoice No :</dl>
            </div>

            <div class="col-sm-6 col" style="text-align: right;">
                <dl style="margin-bottom: 5px"><?= $purchaser; ?></dl>
                <dl style="margin-bottom: 5px"><?= $transaction_amount; ?></dl>
                <dl style="margin-bottom: 5px"><?= $payment_method; ?></dl>
                <dl style="margin-bottom: 5px"><?= $iss_name; ?></dl>
                <dl style="margin-bottom: 5px"><?= $iss_conf; ?></dl>
                <dl style="margin-bottom: 5px"><?= $bank_trans_id; ?></dl>
                <dl style="margin-bottom: 5px"><?= $timestamp; ?></dl>
                <dl style="margin-bottom: 5px"><?= $invoice_id; ?></dl>
            </div>
        </div>

        <div class="col-sm-1 col-md-1 col-sm-offset-9 col-md-offset-9" style="margin-top: 20px;">
            <input class="btn btn-success" type="button" value="Print">
        </div>

        <div class="col-sm-1 col-md-1" style="margin-top: 20px;">
            <form action="pay_confirm_controller.php" method="post">
                <input type="hidden" name="kijiji_payment_id" value="<?= $record_id ?>">
                <input class="btn-success" type="submit" name="Receipt"></input>
            </form>
        </div>

    </div>
</body>

</html>

<?php
} elseif ($_POST['gateway'] == 'm1') {
  $purchaser = $_POST['purchaser'];
  $transaction_amount = $_POST['amount'];
  $payment_method = 'Moneris/Interac';
  $bank_trans_id = $_POST['bank_trans_id'];
  $timestamp = $_POST['timestamp'];
  $invoice_id = $_POST['invoice_id'];
  $card_holder = $_POST['card_holder'];
  $card_number = $_POST['card_number'];
  $bank_approve_code = $_POST['bank_approve_code'];
  $record_id = $_POST['custom_id'];
?>
<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin: 30px 30px">
        <div class="col-md-12 col-sm-12" style="margin:40px 30px">
            <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
                <img src="images/payment_success.png" style="max-height: 150px; max-width: 150px;">
            </div>
            <div class="col-md-8 col-sm-8" style="vertical-align: center">
                <h2 style="color: #7CCD7C">Your payment has been approved !</h2>
            </div>
        </div>


        <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2"
            style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
            <div class="col-sm-6 col-md-6" style="text-align: left;">
                <dl style="margin-bottom: 5px">Purchaser :</dl>
                <dl style="margin-bottom: 5px">Transaction Amount:</dl>
                <dl style="margin-bottom: 5px">Payment Method :</dl>
                <dl style="margin-bottom: 5px">Card Holder :</dl>
                <dl style="margin-bottom: 5px">Card Number :</dl>
                <dl style="margin-bottom: 5px">Bank Approval Code :</dl>
                <dl style="margin-bottom: 5px">Bank Transaction No :</dl>
                <dl style="margin-bottom: 5px">Date/Time :</dl>
                <dl style="margin-bottom: 5px">Invoice No :</dl>
            </div>

            <div class="col-sm-6 col" style="text-align: right;">
                <dl style="margin-bottom: 5px"><?= $purchaser; ?></dl>
                <dl style="margin-bottom: 5px"><?= $transaction_amount; ?></dl>
                <dl style="margin-bottom: 5px"><?= $payment_method; ?></dl>
                <dl style="margin-bottom: 5px"><?= $card_holder; ?></dl>
                <dl style="margin-bottom: 5px"><?= $card_number; ?></dl>
                <dl style="margin-bottom: 5px"><?= $bank_approve_code; ?></dl>
                <dl style="margin-bottom: 5px"><?= $bank_trans_id; ?></dl>
                <dl style="margin-bottom: 5px"><?= $timestamp; ?></dl>
                <dl style="margin-bottom: 5px"><?= $invoice_id; ?></dl>
            </div>
        </div>

        <div class="col-sm-1 col-md-1 col-sm-offset-9 col-md-offset-9" style="margin-top: 20px;">
            <input class="btn btn-success" type="button" value="Print">
        </div>

        <div class="col-sm-1 col-md-1">
            <form action="pay_confirm_controller.php" method="post">
                <input type="hidden" name="kijiji_payment_id" value="<?= $record_id ?>">
                <input class="btn-success" type="submit" name="download_receipt">Download Receipt</input>
            </form>
        </div>

    </div>
</body>

</html>
<?php
} elseif ($_POST['gateway'] == 'p') {
  $purchaser = $_POST['purchaser'];
  $amount = $_POST['amount'];
  $payment_method = $_POST['payment_method'];
  $timestamp = $_POST['timestamp'];
  $invoice_id = $_POST['invoice_id'];
  $p_id = $_POST['p_id'];
  $p_state = $_POST['p_state'];
  $record_id = $_POST['record_id'];
?>
<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin: 30px 30px">
        <div class="col-md-12 col-sm-12" style="margin:40px 30px">
            <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
                <img src="../images/payment_success.png" style="max-height: 150px; max-width: 150px;">
            </div>
            <div class="col-md-8 col-sm-8" style="vertical-align: center">
                <h2 style="color: #7CCD7C">Your payment has been approved !</h2>
            </div>
        </div>


        <div class="col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2"
            style="border: 1px solid #eeeeee;border-radius: 4px; padding: 10px 5px">
            <div class="col-sm-6 col-md-6" style="text-align: left;">
                <dl style="margin-bottom: 5px">Purchaser :</dl>
                <dl style="margin-bottom: 5px">Transaction Amount:</dl>
                <dl style="margin-bottom: 5px">Payment Method :</dl>
                <dl style="margin-bottom: 5px">Paypal transaction No :</dl>
                <dl style="margin-bottom: 5px">Paypal Transaction Status :</dl>
                <dl style="margin-bottom: 5px">Date/Time :</dl>
                <dl style="margin-bottom: 5px">Invoice No :</dl>
            </div>

            <div class="col-sm-6 col" style="text-align: right;">
                <dl style="margin-bottom: 5px"><?= $purchaser; ?></dl>
                <dl style="margin-bottom: 5px"><?= $amount; ?></dl>
                <dl style="margin-bottom: 5px"><?= $payment_method; ?></dl>
                <dl style="margin-bottom: 5px"><?= $p_id; ?></dl>
                <dl style="margin-bottom: 5px"><?= $p_state; ?></dl>
                <dl style="margin-bottom: 5px"><?= $timestamp; ?></dl>
                <dl style="margin-bottom: 5px"><?= $invoice_id; ?></dl>
            </div>
        </div>

        <!--    <div class="col-sm-1 col-md-1 col-sm-offset-9 col-md-offset-8" style="margin-top: 20px;">-->
        <!--      <input class="btn btn-success" type="button" value="Print">-->
        <!--    </div>-->
        <!--    <div class="col-sm-1 col-md-1">-->
        <!--      <form action="pay_confirm_controller.php" method="post">-->
        <!--        <input type="hidden" name="kijiji_payment_id" value="-->
        <? //= $record_id 
                                                                          ?>
        <!--">-->
        <!--        <input class="btn-success" type="submit" value="Download receipt"></input>-->
        <!--      </form>-->
        <!--    </div>-->
    </div>
</body>

</html>



<?php } ?>



<?php

include("../../../pdo/dbconfig.php");

$info = $DB_kijiji->get_payment_info_for_receipt($kijiji_payment_id);
$email = $info['email'];
$inovice_number = $info['inovice_number'];
$employee_name = $info['employee_name'];
$buy_slots_count = $info['buy_slots_count'];
$buy_slots_price = $info['buy_slots_price'];
$payment_time = $info['payment_time'];
$slots_due_time = $info['slots_due_time'];
$payment_amount = $info['payment_amount'];
$C_F_amount = $info['C_F_amount'];
$total_amount = $info['total_amount'];
$payment_status = $info['payment_status'];


// sent email -- receipt
require_once("../sendSMSEmail.php");
$email_sbj = "Receipt -- spgmanagement.com";
$email_content = get_receipt_template($employee_name);
SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $email, $employee_name, $email_sbj, $email_content);

?>


<?php
function get_receipt_template($to_name)
{
  global $kijiji_payment_id;
  global $buy_slots_price;
  global $buy_slots_count;
  global $slots_due_time;
  global $payment_time;
  global $payment_amount;
  global $C_F_amount;
  global $total_amount;
  global $inovice_number;


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
            <td align="center" style="text-align:center;padding:0; width: 600px;height: 234px;"><img src="http://www.beaveraittesting.site/admin/files/logot.png" height="70"></td>
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
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Invoice No</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $inovice_number . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Price($/slot/mon.)</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $buy_slots_price . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Slots Count</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $buy_slots_count . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Slots Due Time</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $slots_due_time . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Payment Time</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $payment_time . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $payment_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Convenience Fee</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $C_F_amount . '</td></tr>
                <tr><td style=" width: 5cm; border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center;">Total Amount</td><td style="border-bottom: 1px solid;border-left: 1px solid;padding: 10px;text-align: center; border-right: 1px solid;">' . $total_amount . '</td></tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr style="background-color:white;">
            <td style="padding:30px 20px 20px 20px;">
              <div align="center" style="text-align:center;margin:40px 0 20px 0;"><font face="Arial,sans-serif" size="2" color="#3DA9F5" style="font-family: Arial, sans-serif, serif, EmojiFont;"><span style="font-size:15px;"><a href="http://www.beaveraittesting.site/admin/custom/kijiji/invoice_receipt_controller.php?download_kijiji_receipt=' . $kijiji_payment_id . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;"><font color="white"><span style="background-color:#80C246;padding:15px 45px;"><b>Download Receipt</b></span></font></a> </span></font></div>
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
            <span style="font-size:13px;"><a href="http://www.beaveraittesting.site" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">Contact Us</font></a></span></div>
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
        <div><span style="font-size:14px; color: #363636;">@2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.</span></div>
      </td>
    </tr>
    </tbody>
  </table>
</div>

';
  return $content;
}

?>