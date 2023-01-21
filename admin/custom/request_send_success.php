<?php
session_start();
error_reporting(E_ALL);
include_once ('../../pdo/dbconfig.php');
$product = $_GET['product'];
$user_id = $_SESSION['UserID'];

if($user_id > 100000 && $user_id < 200000){
    $redirect_url = '../tenant_portal.php';
}
else if($product === 'kijiji'){
    $redirect_url = '../kijiji_listing.php';
}
else if($product === 'services'){
    $redirect_url = '../services.php';
}
?>

<html>
<head>
  <link rel="stylesheet" href="../bootstrap4/css/bootstrap.min.css">
</head>

<body>
<div class="container" style="margin: 30px 30px">
  <div class="col-md-12 col-sm-12" style="margin:40px 30px">
    <div class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1">
      <img src="images/send_success.png" style="max-height: 120px; max-width: 120px;">
    </div>
    <div class="col-md-8 col-sm-8" style="vertical-align: center; padding-top: 30px;">
      <h2 style="color: #7CCD7C">Your payment request has been send !</h2>
    </div>
  </div>

  <div class="col-sm-7 col-md-7 col-sm-offset-4 col-md-offset-4" style="margin-top: 20px;">
    <span>The page will be automatically redirected in&nbsp;&nbsp;<span id="countdown_timer" style="font-size: 16px; color: red;">8</span>&nbsp;&nbsp;seconds</span>
    <span>( <a href="<?php echo $redirect_url; ?>">Redirect Now !</a> )</span>
  </div>
</div>
</body>
<script src="../jquery/jquery.min.js"></script>
<script type="text/javascript">
    var timer = 8;
    var url = "<?php echo $redirect_url;?>";

    $(document).ready(function () {
        countdown();
    });

    console.log(url);

    function countdown() {
        if (timer === 0) {
            window.location.replace(url);
            return;
        }
        else {
            $('#countdown_timer').text(timer);
            timer--;
        }

        setTimeout(function () {
            countdown()
        }, 1000)
    }

</script>
</html>