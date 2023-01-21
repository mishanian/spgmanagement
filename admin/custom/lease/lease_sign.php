<? if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);?>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>SPG Canada</title>
    <link rel="icon" type="image/PNG" href="images/logo_spg.png" />
    <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
    #signature {
        width: 300px;
        height: 200px;
        border: 1px solid black;
    }

    #signatureInit {
        width: 100px;
        height: 50px;
        border: 1px solid black;
        vertical-align: text-top;
    }

    #success_message {
        display: none;
    }
    </style>
</head>

<body>
    <br>
    <?
    $who_sign=$_GET['ws'];
if(!empty($_GET['company_id'])){
    $company_id=$_GET['company_id'];
}else{ $company_id=9;} //SPG Canada
include('../../../pdo/dbconfig.php');
// $lease_id=$_GET['lease_id'];
$hc=$_GET['hc'];
$query = "select id as lease_id from lease_infos where hash_code='$hc'";
// die($query);
$stmt = $DB_con->prepare($query);
$stmt->execute();
$lease_id = $stmt->fetchColumn();
// die($lease_id);

if($who_sign=="t"){
if(!empty($_GET['rid'])){
    $rid=$_GET['rid'];}else{$rid=$_GET['tid'];}

$query = "SELECT full_name FROM tenant_infos TI WHERE TI.tenant_id=$rid";
$stmt =$DB_con->prepare($query);
$stmt->execute();
$row=$stmt->fetch(\PDO::FETCH_ASSOC);
$full_name=$row['full_name'];
// $query = "select tenant_firstname, tenant_surname, tenant_address, tenant_apt, tenant_city, tenant_postalcode, tenant_email, tenant_tel, rent, total_cost, lease_amount from credit_check where creditcheck_id=".$id;
$query = "select is_signed, sign_DT, sign_IP, sign_user_info from tenant_signs where is_signed=1 and tenant_id=$rid and sign_type_id=1";
//echo $query;
$stmt =$DB_con->prepare($query);
$stmt->execute();
$row=$stmt->fetch(\PDO::FETCH_ASSOC);
if(!empty($row)){die("Signed or wrong ID");};
list($first_name,$last_name)=explode(" ",$full_name,2);
}

if($who_sign=="e"){
$eid=$_SESSION['employee_id'];
$query = "select full_name from employee_infos where employee_id=".$eid;
$stmt =$DB_con->prepare($query);
$stmt->execute();
$full_name=$stmt->fetchColumn();
if(empty($full_name)){die("Signed or wrong ID");};
// $company_name=$row['name'];
// $company_abb=$row['short_name'];
list($first_name,$last_name)=explode(" ",$full_name,2);
$rid=$_SESSION['employee_id'];
}
$r_sign_init=strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
$image_name = "../../files/lease_signs/".$who_sign."_sign_init_$rid" . "_l$lease_id" . ".png";
// $text=$t_sign_init;
// require_once("text2image.php");
?>
    <br><br>
    <div class="container">
        <form class="well form-horizontal" action="lease_res.php" method="post" id="sign_form"
            enctype="multipart/form-data">
            <input type="hidden" name="hc" value="<?= $hc ?>">
            <input type="hidden" name="rid" value="<?= $rid ?>">
            <input type="hidden" name="who_sign" value="<?= $who_sign ?>">
            <div class="row">
                <div class="col-md-12">
                    Dear <b><?= "$first_name $last_name" ?></b><br>
                    Please select your desire initial:<br>
                    <input type="radio" name="init_select" value="1" checked>
                    <img src="<?= "text2image.php?image_name=$image_name&text=$r_sign_init" ?>">
                    <br>
                    <input type="radio" name="init_select" value="2"> Draw Initial:
                    <div id="signatureInit">
                        <canvas id="signature-pad-init" class="signature-pad-init" width="100px" height="50px"></canvas>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div id="signature">

                        <canvas id="signature-pad" class="signature-pad" width="300px" height="200px"></canvas>

                    </div><br />
                    <input type="hidden" name="signatureData" id="signatureData">
                    <input type="hidden" name="signatureDataInit" id="signatureDataInit">
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit" />
                    <button class="btn btn-danger" type="button"
                        onclick="window.signaturePad.clear();window.signaturePadInit.clear();">Clear
                        Signature</button>
                </div>

                <div class="col align-top" style="margin:0 50px; color:red; font-size:18pt;">
                    Authenticate your document by sign in the left box<br>
                    <span style="font-size:50pt">&lArr;</span>
                </div>

            </div>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
    $(document).ready(function() {

        window.signaturePad = new SignaturePad(document.getElementById('signature-pad'));
        window.signaturePadInit = new SignaturePad(document.getElementById('signature-pad-init'));

        $('#sign_form').submit(function() {
            var data = window.signaturePad.toDataURL('image/png');
            var dataInit = window.signaturePadInit.toDataURL('image/png');
            $('#signatureData').val(data);
            $('#signatureDataInit').val(dataInit);
            if (window.signaturePad.isEmpty() || (window.signaturePadInit.isEmpty() && $("init_select")
                    .val() == 2)) {
                alert('Please Sign below of the page');
                return false;
            }


        });
    })
    </script>
</body>

</html>