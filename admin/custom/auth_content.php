<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//die(print_r($_SESSION));
//die($_SESSION['UserID']."=".$_SESSION['auth_trust']);
if (empty($_SESSION['UserID'])) {
    unset($_SESSION['auth_trust']);
    header("Location: logout.php");
}
if (!empty($_SESSION['UserID']) && !empty($_SESSION['auth_trust'])) {
    header("Location: home");
}
?>
<div class="container">
    <?
    //namespace PHPMaker2023\spgmanagement;
    // var_dump($_SESSION['auth_trust']);
    // die("auth page");
    $root = dirname(__DIR__);
    include_once($root . '/../pdo/dbconfig.php');
    include_once($root . '/../pdo/Class.Crud.php');
    $Crud = new CRUD($DB_con);
    include_once 'sendSMSEmail.php';
    //die("session auth=".$_SESSION['auth_trust']." user session=".$_SESSION['UserID']." submit=".$_POST['submit']." script name=".basename($_SERVER['SCRIPT_FILENAME'])." ".basename($_SERVER['SCRIPT_FILENAME']));
    //echo $_SESSION['auth_trust'];


    if (!empty($_POST['submit']) && $_POST['submit'] == "Login" && !empty($_POST['otp_code']) && empty($_SESSION['auth_trust'])) {

        $otp_code = trim($_POST['otp_code']);
        $otpcheck = "select count(*) as count from otp where user_id=" . $_SESSION['UserID'] . " and mobile='" . $_SESSION['UserMobile'] . "' and optcode='" . $otp_code . "' and optstatus=1";
        //   echo "otpcheck=$otpcheck";
        $Crud->query($otpcheck);
        $count = $Crud->resultField();
        //   echo "count=$count";
        // $count=ExecuteScalar($otpcheck);
        if ($count == 1) {
            // die($count);
            $_SESSION['auth_trust'] = 1;
            $otpupdate = "update otp set optstatus=0 where user_id=" . $_SESSION['UserID'] . " and mobile='" . $_SESSION['UserMobile'] . "' and optcode='" . $otp_code . "' and optstatus=2";
            //    echo "otpupdate=$otpupdate";
            $Crud->query($otpupdate);
            $Crud->execute();
            if (!empty($_POST['auth_this_pc']) && $_POST['auth_this_pc'] == 1) {
                setcookie("auth_trust", 1, time() + (86400 * 7), "/"); //Expire after 7 days
            }
            //echo "<p style='color:green'>Now you can login</p>";
            header("Location: login");
        } else {
            echo "<p style='color:red'>OTP Code is wrong</p>";
        }
    }
    if (empty($_SESSION['auth_trust'])) {
        //    die("no auth_trust session");
        Send_OTP();
    ?>
    <form action="auth.php" method="post">
        <p>Enter the OTP Code here: <input type="text" name="otp_code" id="otp_code" size="7"> <input type="checkbox"
                name="auth_this_pc" value="1"> Trust this computer for 7 days. Not recommended for public PCs.</p>
        <p><input type="submit" name="submit" value="Login" class="btn btn-primary"></p>
    </form>
    <?php
    }
    ?>
    <?php
    function GenerateRandomString($length = 5)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length); //$x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    }

    function Send_OTP()
    {
        if (empty($_SESSION['UserID'])) {
            die("Wrong UserID");
            /*
            error_log("There is no Sesion UserID in Send_OTP");
            require_once("custom/GetClientData.php");
            $data = new GetDataPlugin();

            $client_data = array(
                $data->ip(), $data->os(), $data->browser(), $data->geo('country'),
                $data->geo('region'), $data->geo('continent'), $data->geo('city'), $data->agent(), $data->referer(),
                $data->height(), $data->width(), $data->javaenabled(), $data->cookieenabled(), $data->language(), $data->architecture(), $data->geo('logitude'), $data->geo('latitude'), $data->provetor(), $data->getdate()
            );
            $client_data = json_encode($client_data);
            error_log("client data=" . $client_data);
            $server_data = json_encode($_SERVER);
            error_log("server_data=" . $server_data);
            $session_data = json_encode($_SESSION);
            error_log("session_data=" . $session_data);
            */
        }
        global $Crud;
        $isSMSActive = false;
        $otp = GenerateRandomString();
        $otpupdate = "update otp set optstatus=0 where user_id=" . $_SESSION['UserID'] . " and mobile='" . $_SESSION['UserMobile'] . "'";
        //    echo "otpupdate=$otpupdate";
        $Crud->query($otpupdate);
        $Crud->execute();
        $otpinsert = "insert otp (user_id, mobile, optcode, optstatus) values (" . $_SESSION['UserID'] . ",'" . $_SESSION['UserMobile'] . "','" . $otp . "', 1)";
        $Crud->query($otpinsert);
        $Crud->execute();
        $otptxt = "Your OTP is <b>" . $otp . "</b> . Use this code to verify your account. Thank You.";
        $otpsms = "Your OTP is " . $otp . " . Use this code to verify your account. Thank You.";
        if ($isSMSActive) {
            if (!empty($_SESSION['UserMobile'])) {
                SendSMS($_SESSION['UserMobile'], $otpsms);
                echo "<p>We have sent you an authentication secure code. Please enter it below to safe login:</p>";
                echo "<p>SMS Send to " . $_SESSION['UserMobile'] . " as :" . $otpsms . "</p>";
            } else {
                echo ("<p>You should have mobile for authentication. Please ask admin to add your mobile</p>");
            }
        } else {
            echo $otptxt;
        }
    }


    ?>
</div>