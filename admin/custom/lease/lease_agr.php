<? if (empty($pdf)) { ?>
<!DOCTYPE html>
<html lang="en">
<? } ?>
<?
include('../../../pdo/dbconfig.php');


if (!empty($_POST['hc'])) {
    $hc = $_POST['hc'];
} else {
    $hc = $_GET['hc'];
}

if (!empty($_POST['tid'])) {
    $tid = $_POST['tid'];
} elseif (!empty($_GET['tid'])) {
    $tid = $_GET['tid'];
}

if (!empty($_GET['prv'])) {
    $prv = $_GET['prv'];
} else {
    $prv = 0;
}


$query = "SELECT tenant_id, full_name, home_number as telephone, mobile, email, banking_name, banking_address, banking_accountno, banking_transit, banking_institution FROM tenant_infos WHERE FIND_IN_SET(tenant_id,(SELECT tenant_ids FROM lease_infos WHERE hash_code='$hc'))";
// die($query);
$stmt = $DB_con->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$row = $rows[0];
extract($row);


$query = "SELECT LI.id as lease_id, BI.building_name, AI.unit_number, BI.city, BI.address, PR.name AS province_name, BI.postal_code FROM lease_infos LI 
LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
LEFT JOIN apartment_infos AI ON LI.apartment_id=AI.apartment_id
LEFT JOIN provinces PR ON BI.province_id=PR.id
WHERE LI.hash_code='$hc'";
// die($query);
$stmt = $DB_con->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$row = $rows[0];
extract($row);

if (empty($tid) || $prv == 1) {
    $lessor_sign_only = "";
} else {
    $lessor_sign_only = "<img src='../../files/lease_signs/manager_sign_$lease_id.png' height='50'>";
}

if (empty($tid) || $prv == 1) {
    $tenant_sign = "";
} else {
    $tenant_sign = "<img src='../../files/lease_signs/tenant_sign_" . $tid . "_l" . $lease_id . ".png' height='50'>";
}



function fill($n, $what = "&nbsp;")
{
    return str_repeat($what, $n);
}

function underline($what, $n = "")
{
    if (empty($n)) {
        $n = strlen($what);
    }
    echo "<span class='toptext'>" . str_repeat('_', $n) . "&nbsp; <span class='bottomtextn'>$what" . str_repeat('&nbsp;', $n - strlen($what)) . "</span></span> &nbsp;";
}

$cb = "<span style='font-family: freeserif; font-size:10pt'>&#x2612;</span>";
$ecb = "<span style='font-family: freeserif; font-size:10pt'>&#x2610;</span>";
$datesign = "<span class='toptext'>&nbsp; &nbsp; " . date('m/d/Y') . fill(15) . "<span class='bottomtext'> Date (MM/DD/YY)</span></span>&nbsp;";


if (empty($pdf)) { ?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/snackbar.css">


    <?php } ?>
    <style>
    @page {
        size: LEGAL;
        /* margin: 0; */
    }

    body {
        font-family: Arial;
        font-size: 12pt;
        /* margin: 0px;
            padding: 0px; */
        width: 800px;
    }

    table {
        /* width: 100%; */
        border-spacing: 0px;
        /* table-layout: fixed; */
        margin: 0 auto;
    }

    .main_title {
        background-color: #777777;
    }

    .title {
        background-color: #aaaaaa;
    }

    td {
        padding: 10px 10px 5px 10px;
        font-size: 12pt;
        vertical-align: text-top;
    }

    td.footer {
        padding: 0px !important;
        font-size: 8pt;
        vertical-align: top;
    }

    td.small {
        font-size: smaller;
        padding: 0px;
        border-top: 1px;
    }

    td.td25 {
        width: 25%;
    }

    .tablecb td {
        padding: 3px;
    }

    .center {
        text-align: center;
    }

    .toptext {
        position: relative;
    }

    .bottomtext {
        position: absolute;
        left: 0;
        top: 13px;
        font-size: 14pt;
        text-decoration: overline;
        white-space: nowrap;

    }

    .bottomtextn {
        position: absolute;
        left: 0;
        top: 13px;
        font-size: 14pt;
        white-space: nowrap;
        /* text-decoration: overline; */
        /* white-space: pre; */

    }

    p {
        line-height: <?=($pdf==1 ? 14 : 16) ?>pt;
        padding: 0px;
        /*10px;*/
        font-size: 10pt;
    }

    p.footer {
        font-size: 7pt;
    }


    li {
        font-size: 12pt;
        padding-bottom: 12pt;
    }
    </style>
    <? if (empty($pdf)) { ?>
    <title>Lease</title>
</head>

<body>
    <? } ?>
    <page>
        <cert src="./cert.pem" privkey="./key.pem" name="SPG Management" location="Canada"
            reason="Lease Sign Validation" contactinfo="info@mgmgmt.ca"></cert>
        <hr>
        <h1 class="center">AGREEMENT</h1>
        <hr>
        <table>
            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    the parties are bound by a residential lease concerning a dwelling located<br>
                    at <?= $address ?> # <?= $unit_number ?> , in <?= $city ?> (<?= $province_name ?>)
                    <?= $postal_code ?>;
                </td>
            </tr>

            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    at the signing of the lease, the landlord advised the tenant(s) of its<br>
                    intentions to undertake major work and reconfiguration of the premises;
                </td>
            </tr>

            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    an agreement was reached between the parties that the tenant(s) shall<br>
                    leave the leased premises upon one (1) months’ notice given by the landlord;
                </td>
            </tr>


            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    the landlord will proceed with its project whenever the Rental Board gives<br>
                    its decision allowing the reconfiguration;
                </td>
            </tr>


            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    the parties agree to stipulate in writing the terms and conditions of the <br>
                    present agreement;
                </td>
            </tr>



            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    the present constitutes a transaction pursuant to article 2631 of the Civil<br> Code of Quebec;
                </td>
            </tr>


            <tr>
                <td>
                    <h3>WHEREAS</h3>
                </td>
                <td>
                    each party acknowledges that the present does not constitute an<br>
                    admission of liability by the other party and mainly serves the purpose of<br>
                    assisting both parties in reaching a mutual discharge agreement;
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3 style="font-weight:normal">WHEREFORE, THE PARTIES AGREE TO THE FOLLOWING:</h3>
                </td>
            </tr>

        </table>
        <ol>
            <li>The preamble shall constitute an integral part of the present agreement;</li>
            <li>Both parties hereto mutually agree that the lease under which they are boundshall be terminated 30 days
                after the reception of a written notice given by thelandlord terminating the lease;</li>
            <li>The tenant(s), along with any other occupant, commits to vacate the premiseswith respect to the above
                mentioned delay, bringing with him/them all personalbelongings and home furnishings, ensuring that the
                dwelling unit is left in soundcondition;</li>
            <li>The landlord is allowed to dispose of any goods or belongings left in the premisesafter the termination
                of the lease, without any further notice, delay orcompensation to the tenant(s);</li>
        </ol>

    </page>
    <page>
        <ol style="start:5;" start="5">
            <li>The tenant(s) shall comply with all his/their obligations and pay the rent until the termination of the
                lease;</li>
            <li>In consideration of the respect of the present agreement, both parties hereby
                agree to completely release and discharge the other of all past or present claims,
                actions or causes that could result or emerge from one or all of the arising facts
                set forth in the present litigation and/or with regards to the lease of the premises
                located at <?= $address ?> # <?= $unit_number ?> , in <?= $city ?> (<?= $province_name ?>)
                <?= $postal_code ?>;
            </li>
            <li>The parties declare that they have read and understood the present agreement,
                that it is the expression of their will, and that they sign it freely and without
                coercion;</li>
        </ol>
        <p></p>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <span class="toptext"><?= $lessor_sign_only ?><span class="bottomtext"><b>MG Real Estate Management
                                Inc<br> Landlord</b></span></span>
                </td>
                <td style="width: 50%;"><span class='toptext btn btn-warning'
                        onclick='toast(this)'><?= $tenant_sign ?><span class="bottomtext"><br>Tenant:
                            <?= $full_name ?><?= fill(30) ?> </span></span><br><br>
                </td>
            </tr>

        </table>

        <p>&nbsp;</p>

    </page>


    <? if (empty($pdf)) { ?>
    <form method="POST" action="lease_res2.php" onsubmit="return checkAllSigned()" id="signForm">
        <input type="hidden" name="hc" value="<?= $hc ?>">
        <input type="hidden" name="lid" value="<?= $lease_id ?>">
        <input type="hidden" name="tid" value="<?= $tid ?>">
        <input type="hidden" name="stid" value="3">
        <p class="center"><input type="submit" class="btn btn-primary center" value="Signed and Agreed"></p>
    </form>
    <!--toast-->
    <snackbar></snackbar>

    <!-- end of toast-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/snackbar.js"></script>
    <script>
    const snackbar = new SnackBar;

    function toast(signedObj) {
        signedObj.classList.remove("btn");;
        signedObj.classList.remove("btn-warning");
        snackbar.make("message", ["Signed", null, "bottom", "center"], 5000);
    }

    function checkAllSigned() {

        if ($(".btn-warning").length) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $(".btn-warning").offset().top
            }, 2000);
            alert("Please sign all signed boxes");
            return false;
        }
    }
    </script>
</body>

</html>
<? } ?>