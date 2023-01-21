<?

namespace PHPMaker2023\spgmanagement;
//2. Create Function to Generate Random String (OTP CODE)

function GenerateRandomString($length = 5)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

//3. Create a function to sms OTP Code (modify to suit your provider)

function Send_OTP($uname)
{
    $otp = GenerateRandomString();
    $uphonesql = "select user_id,user_phone from app_users where user_name = '" . $uname . "'";
    $uphone = ExecuteRow($uphonesql);
    $otpinsert = "insert into otp_ver(u_code,s_code,o_status)
values(" . $uphone["user_id"] . ",'" . $otp . "','N')";
    ExecuteStatement($otpinsert);
    $otptxt = "Thank you for Registering on Shopman.
Your Username is " . $userinfo["user_name"] . ".
Your OTP is " . $otp . " . Login to verify your code.
Thank You.";
    $sendto = $uphone["user_phone"];
    SendSMS($sendto, $otptxt);
}

//4. Create function to verify OTP status when logging in. It will redirect user to verification page if code is not verified

function Check_OTP($uname)
{
    $ucodesql = "select user_id from app_users where user_name = '" . $uname . "'";
    $ucode = ExecuteScalar($ucodesql);
    $otpsql = "select o_status from otp_ver where u_code = " . $ucode . "";
    $tstatus = ExecuteScalar($otpsql);
    if ($tstatus == 'Y') {
        return TRUE;
    } elseif ($tstatus == 'N') {

        $_SESSION[SESSION_STATUS] = "";
        ob_end_clean();
        // NOTE: Modify the target page
        header("Location:otp/otp_verify.php");
        exit();
        return FALSE;
        //setFailureMessage("Sorry! User Not verified.");
    } else {
        $_SESSION = [];
    }
}