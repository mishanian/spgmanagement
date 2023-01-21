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
if(!empty($_GET['company_id'])){
    $company_id=$_GET['company_id'];
}else{ $company_id=9;} //SPG Canada
include('../../../pdo/dbconfig.php');

if (!empty($_POST['hc'])) {
    $hc = $_POST['hc'];
} else {
    $hc = $_GET['hc'];
}

$query = "select *, LI.id as lease_id, PPT.lease_name as period_name from lease_infos LI left join payment_period_types PPT on LI.payment_period=PPT.id where LI.hash_code='$hc'";
// die($query);
$stmt = $DB_con->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$row = $rows[0];
extract($row);
// die(var_dump($row));

$cid=$_SESSION['company_id'];
$query = "select name, short_name from company_infos where id=".$cid;
$stmt =$DB_con->prepare($query);
$stmt->execute();
$row=$stmt->fetch(\PDO::FETCH_ASSOC);
if(empty($row)){die("Signed or wrong ID");};
$company_name=$row['name'];
$company_abb=$row['short_name'];
list($first_name,$last_name)=explode(" ",$company_name,2);
$rid=$_SESSION['employee_id'];
$image_name = "../../files/lease_signs/tenant_sign_init_$rid" . "_l$lease_id" . ".png";
?>
    <div class="container">
        <form class="well form-horizontal" action="lease_res.php" method="post" id="sign_form"
            enctype="multipart/form-data">
            <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
            <input type="hidden" name="hc" value="<?= $hc ?>">
            <input type="hidden" name="rid" value="<?= $rid  ?>">
            <input type="hidden" name="who_sign" value="m">
            <div class="row">
                <div class="col-md-12">
                    Dear <b><?= $company_name ?></b><br>
                    <input type="radio" name="init_select" value="1" checked>
                    <img src="<?= "text2image.php?rid=$rid&lease_id=$lease_id&t=$r_sign_init" ?>">
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