<?php if (empty($pdf)) { ?>
    <!DOCTYPE html>
    <html lang="en">
<?php } ?>
<?php
include('../../../pdo/dbconfig.php');
// die(print_r($_POST));



if (!empty($_POST['who_sign']) && $_POST['who_sign']) {
    $who_sign = $_POST['who_sign'];
} else {
    $who_sign = "t";
}


// die("who_sign=$who_sign --- tenant_toast=$t_toast  --- e_toast= $e_toast");

if (!empty($_POST['hc'])) {
    $hc = $_POST['hc'];
} else {
    $hc = $_GET['hc'];
}

if (!empty($_POST['rid'])) {
    $rid = $_POST['rid'];
} elseif (!empty($_GET['rid'])) {
    $rid = $_GET['rid'];
}

if (!empty($_GET['prv'])) {
    $prv = $_GET['prv'];
} else {
    $prv = 0;
}
if (!empty($rid) && $who_sign == "t") {
    $tenant_to_sign_id = $rid;
}


/**************** COMPANY *************************/

$query = "select CI.lease_name, CI.short_name from company_infos CI where CI.id=(select company_id from lease_infos LI where LI.hash_code='$hc')";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$row = $stmt->fetch(\PDO::FETCH_ASSOC);
if (empty($row)) {
    die("Signed or wrong ID " . $query);
};
$company_name = $row['lease_name'];
$company_abb = $row['short_name'];

if (empty($pdf) || $prv == 1) {
    $company_abb = " &nbsp; &nbsp; ";
}

$t_toast = $e_toast = $t_class = $e_class = "";

${$who_sign . '_toast'} = "onclick='toast(this)'";
${$who_sign . '_class'} = "btn btn-warning";






/****************** /COMPANY ***********************/


$query = "select *, LI.id as lease_id, LI.tenant_ids, PPT.lease_name as period_name from lease_infos LI left join payment_period_types PPT on LI.payment_period=PPT.id where LI.hash_code='$hc'";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$lrow = $stmt->fetch(\PDO::FETCH_ASSOC);
extract($lrow);
// die(var_dump($lrow));

/********************** Save Sign ********************/

if (!empty($_POST['signatureData'])) {
    if ($_POST['init_select'] == 2) {
        $signatureDataInit = $_POST['signatureDataInit'];
        ${$who_sign . '_signature_init'} = "../../files/lease_signs/" . $who_sign . "_sign_init_" . $rid . "_l" . $lease_id . ".png";
        $encoded_image_init = explode(",", $signatureDataInit)[1];
        $decoded_image_init = base64_decode($encoded_image_init);
        file_put_contents(${$who_sign . '_signature_init'}, $decoded_image_init);
    }
    $signatureData = $_POST['signatureData'];
    ${$who_sign . '_signature'} = "../../files/lease_signs/" . $who_sign . "_sign_" . $rid . "_l" . $lease_id . ".png";
    $encoded_image = explode(",", $signatureData)[1];
    $decoded_image = base64_decode($encoded_image);
    file_put_contents(${$who_sign . '_signature'}, $decoded_image);
}

/********************* /Save Sign **********************/


/***************** LESSOR ************************/



$lessor_sign_only = "";
$lessor_sign_init = "";
$lessor_sign = "";

$lessor_sign_date = date("M d,Y");
$lessor_name = $company_name;

// if (!empty($lrow['is_signed']) && $lrow['is_signed'] == 1) {

// die($sign_employee_id);
if (!empty($pdf)) {
    $rid = $sign_employee_id;
}
$lessor_sign_image_init_src = "../../files/lease_signs/e_sign_init_" . $rid . "_l" . $lease_id . ".png";
$lessor_sign_image_init = "<img src='$lessor_sign_image_init_src' height='30'>";
if (!is_file($lessor_sign_image_init_src)) {
    $lessor_sign_image_init = "";
}


$lessor_sign_image_src = "../../files/lease_signs/e_sign_" . $rid . "_l" . $lease_id . ".png";
// die("lessor_sign_image_src=$lessor_sign_image_src");
$lessor_sign_image = "<img src='$lessor_sign_image_src' height='70'>";
if (!is_file($lessor_sign_image_src)) {
    $lessor_sign_image = "";
}



$lessor_sign = "<span class='toptext $e_class' $e_toast>" . $lessor_sign_image  . "&nbsp; &nbsp; &nbsp;<span class='bottomtextn'>Signature of lessor</span></span>";
$lessor_sign_only = "<span class='toptext $e_class' $e_toast>" . $lessor_sign_image  . "&nbsp; &nbsp; &nbsp;</span>";
$lessor_sign_init = "<span class='toptext $e_class' $e_toast>" . $lessor_sign_image_init . "<span class='bottomtextn'>Intitial of lessor</span></span>";

$lessor_sign_date = date_format(date_create($lrow['signDT']), "Y-m-d");  //------------------- TODO : Comes from DB
$datesign = "<span class='toptext'>&nbsp; &nbsp; " . $lessor_sign_date . fill(15) . "<span class='bottomtext'> Date (MM/DD/YY)</span></span>&nbsp;";
// }


/****************** /LESSOR *****************/











/***************** Tenants ******************************/

if (empty($tenant_to_sign_id)) { // Not Only for one tenant
    $tenant_ids_array = explode(",", $tenant_ids);
} else {
    $tenant_ids_array = [$tenant_to_sign_id];
}

for ($i = 1; $i < 4; $i++) {
    ${"tenant_name" . $i} = fill(60);
    ${"tenant_apt" . $i} = fill(30);
    ${"tenant_name_abb" . $i} = fill(30);
    ${"tenant_address" . $i} = fill(70);
    ${"tenant_postalcode" . $i} = fill(30);
    ${"tenant_city" . $i} = fill(30);
    ${"tenant_mobile" . $i} = fill(30);
    ${"tenant_email" . $i} = fill(30);
    ${"tenant_capacity" . $i} = fill(30); //"another lessee".
    ${"tenant_sign_date" . $i} = fill(30);
    ${"tenant_sign_init" . $i} = "<span class='toptext' $t_toast>" . fill(25) . "<span class='bottomtext'>Intitial of lessee</span></span>";
    ${"tenant_sign" . $i} = fill(65);
    ${'t_sign_image_init_src' . $i} = "";
    ${'t_sign_image_src' . $i} = "";
    ${"tenant_sign_init_with_others" . $i} = "";
}



// die(print_r($tenant_ids_array));

$i = 0;
if (is_array($tenant_ids_array)) {

    foreach ($tenant_ids_array as $tenant_id) {
        $i++;

        //Get sign date if exist

        $query = "select sign_DT from tenant_signs where lease_id=$lease_id and tenant_id=$tenant_id and is_signed=1 and sign_type_id=1"; //RES Lease
        $stmt = $DB_con->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        //  if (!empty($row['sign_DT'])) {

        $query = "select full_name, home_address, city, email, mobile from tenant_infos where tenant_id=$tenant_id";
        $stmt = $DB_con->prepare($query);
        $stmt->execute();
        $trow = $stmt->fetch(\PDO::FETCH_ASSOC);
        ${"tenant_name" . $i} = $trow['full_name'];
        ${"tenant_address" . $i} = $trow['home_address'];
        ${"tenant_city" . $i} = $trow['city'];
        ${"tenant_email" . $i} = $trow['email'];
        ${"tenant_mobile" . $i} = $trow['mobile'];
        ${"tenant_address" . $i} = $trow['home_address'];
        $tenant_name = ${"tenant_name_abb" . $i} = $trow['full_name'];
        list($tenant_firstname, $tenant_surname) = explode(" ", $tenant_name, 2);
        $tenant_abb = strtoupper(substr($tenant_firstname, 0, 1) . substr($tenant_surname, 0, 1));
        // $lesseeini = '<span style="font-family:Courgette; font-size:12pt">' . $tenant_abb . '</span>';
        $t_sign_image_init_src = $t_sign_image_src = $t_sign_image_init = $t_sign_image = "";
        $t_sign_image_init_src = '../../files/lease_signs/t_sign_init_' . $tenant_id . '_l' . $lease_id . '.png';
        $t_sign_image_src = '../../files/lease_signs/t_sign_' . $tenant_id . '_l' . $lease_id . '.png';
        $t_sign_image_init = "<img src='$t_sign_image_init_src' height='30'>";
        $t_sign_image = "<img src='$t_sign_image_src' height='70'>";
        // die(print_r($t_sign_image_src));
        if (!is_file($t_sign_image_init_src)) {
            $t_sign_image_init = "";
        }
        if (!is_file($t_sign_image_src)) {
            $t_sign_image = "";
        }
        ${"tenant_sign_init" . $i} = "<span class='toptext $t_class' $t_toast>$t_sign_image_init" . fill(5) . "<span class='bottomtextinit'>Intitial of lessee</span></span>";
        ${"tenant_sign_init_with_others" . $i} = $t_sign_image_init;
        ${"tenant_sign" . $i} = "<span class='toptext $t_class' $t_toast>$t_sign_image" . fill(5) . "</span>";
        ${'tenant_sign_date' . $i} = date_format(date_create($row['sign_DT']), "Y-m-d");
        //  }
    }
}
$tenant_sign_inits = "<span class='toptext $t_class' $t_toast>" . $tenant_sign_init_with_others1 . " " . $tenant_sign_init_with_others2 . " " . $tenant_sign_init_with_others3 . "<span class='bottomtextinit'>Intitial of lessees</span></span>";
/***************** /Tenants ******************************/



$query = "select  BI.building_name, BI.address as building_address , BI.postal_code as building_postal_code, BI.city as building_city, AI.unit_number, ST.name as size_type_name
from apartment_infos AI
left join building_infos BI on BI.building_id=AI.building_id
left join size_types ST on AI.size_type_id=ST.id
where apartment_id=" . $apartment_id;
// die($query);
$stmt = $DB_con->prepare($query);
$stmt->execute();
$row = $stmt->fetch(\PDO::FETCH_ASSOC);
extract($row);


$cb = "<span style='font-family: freeserif; font-size:10pt'>&#x2612;</span>";
$ecb = "<span style='font-family: freeserif; font-size:10pt'>&#x2610;</span>";
$ecb_big = "<div style='font-family: freeserif; font-size:20pt; '>&#x2610;</div>";






$specify = "<span class='toptext'>______________________________&nbsp; &nbsp; &nbsp;<span class='bottomtextn'>Specify</span></span>";
$footer1 = "<table align='center' style='width:100%; padding:0px 0px'><tr><td style='width:15%'>Régie du logement</td><td class=center style='width:35%'>";
$footer2 = "</td><td style='width: 50%; text-align:right' nowrap>" . $lessor_sign_init . "&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;" . $tenant_sign_inits . " &nbsp; &nbsp; &nbsp; &nbsp;</td></tr></table>";
?>
<?php


if (empty($pdf)) {
    $footer2 .= "<p>&nbsp;</p>";
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

if ($lessor_sign_date == null) $lessor_sign_date = date("Y-m-d");
$datesign = "<span class='toptext'>&nbsp; &nbsp; " . $lessor_sign_date . fill(15) . "<span class='bottomtext'> Date (MM/DD/YY)</span></span>&nbsp;";

//----------------TODO SAVE AS PDF when they signed and use that PDF
?>
<?php if (empty($pdf)) { ?>

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="css/snackbar.css">


    <?php } ?>
    <style>
        @page {
            size: LEGAL;
            margin: 0;
        }

        body {
            font-family: Arial;
            font-size: 8pt;
            margin: 0px;
            padding: 0px;
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
            font-size: 8pt;
            vertical-align: top;
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
            font-size: 7pt;
            text-decoration: overline;
            white-space: nowrap;

        }

        .bottomtextinit {
            position: absolute;
            left: 0;
            top: 20px;
            font-size: 7pt;
            text-decoration: overline;
            white-space: nowrap;

        }

        .bottomtextn {
            position: absolute;
            left: 0;
            top: 13px;
            font-size: 7pt;
            white-space: nowrap;
            /* text-decoration: overline; */
            /* white-space: pre; */

        }

        p {
            line-height: <?= ($pdf == 1 ? 8 : 10) ?>pt;
            padding: 0px;
            /*10px;*/
            font-size: 8pt;
        }

        p.footer {
            font-size: 7pt;
        }

        h1 {
            font-size: 8pt;
            color: #336699;
            font-weight: bold;
            padding: 0px;
            margin: 0px;
        }

        table.voluntary {
            width: 100%;
            border-spacing: 0px;
            /* table-layout: fixed; */
            margin: 0 auto;
            padding: 10px;
            border-collapse: collapse;
        }

        table.voluntary td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }

        h2 {
            font-size: 8pt;
            font-weight: bold;
            text-decoration: underline;
            padding: 0px;
            margin: 0px;
            display: inline;
        }

        li {
            font-size: 8pt;
            padding-top: 2pt;
            padding-bottom: 1pt;
        }
    </style>
    <?php if (empty($pdf)) { ?>
        <title>Lease</title>
    </head>


    <body>
        <div class="alert alert-danger" id="danger-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>Alert! </strong> Please click and approve on all the Initials and Signatures of this page.
        </div>
    <?php } ?>
    <page>
        <table style="border:1px solid black; width:100% ">

            <tr>
                <td style="vertical-align: top;font-size: 12pt" colspan="2">Régie<br> du logement</td>
                <td style="text-align: right; padding-top:0px; margin-top:0px" colspan="2"><span style="font-size: 38pt">LEASE</span><br> <span style="font-size: 22pt">of a Dwelling</span></td>

            </tr>
            <tr class="main_title">
                <td colspan="2">RÉGIE DU LOGEMENT MANDATORY FORM</td>
                <td colspan="2" style="text-align: right;">TWO COPIES</td>

            </tr>
            <tr class="title">
                <td colspan="2">A | <b>BETWEEN THE LESSOR</b> (WRITE LEGIBLY)</td>
                <td colspan="2"><b>AND THE LESSEE</b> (solidarily liable for the lease )</td>

            </tr>

            <tr>
                <td style="width: 50%; border:#000000 1 solid" colspan="2">

                    <p style="padding-top: 0; margin-top: 0;">
                        <span class="toptext"><?= $company_name ?><span class="bottomtext"><b>Name (Lessor)*
                                    Represented by a Mandate</b></span></span>
                    </p>
                    <p> <span class="toptext">1650<span class="bottomtext">No.</span></span>&nbsp; &nbsp;
                        <span class="toptext">Rene Levesque West<span class="bottomtext">Street
                                <?= fill(50) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext">100<span class="bottomtext">Apt.</span></span>
                    </p>
                    <p> <span class="toptext">Montreal<span class="bottomtext">Municipality &nbsp;
                                <?= fill(25) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext">H3H2S1<span class="bottomtext">Postal Code</span></span>
                    </p>
                    <p> <span class="toptext">(514) 937-3529<span class="bottomtext">Telephone No.
                            </span> </span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Other Telephone No. (cell
                                phone)</span></span>
                    </p>
                    <p>
                        <span class="toptext">administration@spg-canada.com<span class="bottomtext">Email
                                address<?= fill(30) ?>
                            </span></span>
                    </p>


                </td>


                <td style="width: 50%;" colspan="2">
                    <p style="padding-top: 0; margin-top: 0;">
                        <span class="toptext"><?= $tenant_name1 ?>
                            <?= fill(50) ?><span class="bottomtext"><b>Name (Lessee) Solidarily liable for the
                                    lease</b></span></span>
                    </p>
                    <p>
                        <span class="toptext"><?= $tenant_address1 ?>
                            <?= fill(8) ?><span class="bottomtext">No.</span></span>&nbsp; &nbsp;
                        <span class="toptext">
                            <?= fill(40) ?><span class="bottomtext">Street
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_apt1 ?><span class="bottomtext">Apt.</span></span>
                    </p>


                    <p> <span class="toptext"><?= $tenant_city1 ?><span class="bottomtext">Municipality
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_postalcode1 ?><span class="bottomtext">Postal
                                Code</span></span>
                    </p>
                    <p> <span class="toptext"><?= $tenant_mobile1 ?><span class="bottomtext">Telephone No.
                            </span> </span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Other Telephone No. (cell
                                phone)</span></span>
                    </p>
                    <p>
                        <span class="toptext"><?= $tenant_email1 ?><span class="bottomtext">Email
                                address
                                <?= fill(30) ?>
                            </span></span>
                    </p>
                </td>



            </tr>
            <?php $lessor_td = <<<LESSORTTD
                <td style="width: 50%;" colspan="2">

                        <span class="toptext"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="bottomtext"><b>Name (Lessee) Solidarily liable for the
                                    lease</b></span></span><br><br>
                    <p> <span class="toptext"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="bottomtext">No.</span></span>&nbsp; &nbsp;
                        <span class="toptext"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="bottomtext">Street &nbsp;
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                &nbsp; &nbsp;</span></span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Apt.</span></span></p>
                    <p> <span class="toptext">Montreal<span class="bottomtext">Municipality &nbsp;
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                &nbsp; &nbsp;</span></span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Postal Code</span></span></p>
                    <p> <span class="toptext"><span class="bottomtext">Telephone No.
                            </span> </span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Other Telephone No. (cell
                                phone)</span></span></p>
                    <p>
                        <span class="toptext"><span class="bottomtext">Email
                                address
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                &nbsp; &nbsp; &nbsp;
                            </span></span></p><br>
                </td>
                LESSORTTD;
            ?>
            <tr>






                <td style="width: 50%;" colspan="2">
                    <p style="padding-top: 0; margin-top: 0;">
                        <span class="toptext"><?= $tenant_name2 ?>
                            <?= fill(50) ?><span class="bottomtext"><b>Name (Lessee) Solidarily liable for the
                                    lease</b></span></span>
                    </p>
                    <p>
                        <span class="toptext">
                            <?= fill(8) ?><span class="bottomtext">No.</span></span>&nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_address2 ?>
                            <?= fill(30) ?><span class="bottomtext">Street
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_apt2 ?><span class="bottomtext">Apt.</span></span>
                    </p>


                    <p> <span class="toptext">Montreal<span class="bottomtext">Municipality
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_postalcode2 ?><span class="bottomtext">Postal
                                Code</span></span>
                    </p>
                    <p> <span class="toptext"><?= $tenant_mobile2 ?><span class="bottomtext">Telephone No.
                            </span> </span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Other Telephone No. (cell
                                phone)</span></span>
                    </p>
                    <p>
                        <span class="toptext"><?= $tenant_email2 ?><span class="bottomtext">Email
                                address
                                <?= fill(30) ?>
                            </span></span>
                    </p>
                </td>








                <td style="width: 50%;" colspan="2">
                    <p style="padding-top: 0; margin-top: 0;">
                        <span class="toptext"><?= $tenant_name3 ?>
                            <?= fill(50) ?><span class="bottomtext"><b>Name (Lessee) Solidarily liable for the
                                    lease</b></span></span>
                    </p>
                    <p>
                        <span class="toptext">
                            <?= fill(8) ?><span class="bottomtext">No.</span></span>&nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_address3 ?>
                            <?= fill(30) ?><span class="bottomtext">Street
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_apt3 ?><span class="bottomtext">Apt.</span></span>
                    </p>


                    <p> <span class="toptext">Montreal<span class="bottomtext">Municipality
                                <?= fill(30) ?></span></span> &nbsp; &nbsp;
                        <span class="toptext"><?= $tenant_postalcode3 ?><span class="bottomtext">Postal
                                Code</span></span>
                    </p>
                    <p> <span class="toptext"><?= $tenant_mobile3 ?><span class="bottomtext">Telephone No.
                            </span> </span> &nbsp; &nbsp;
                        <span class="toptext"><span class="bottomtext">Other Telephone No. (cell
                                phone)</span></span>
                    </p>
                    <p>
                        <span class="toptext"><?= $tenant_email3 ?><span class="bottomtext">Email
                                address
                                <?= fill(30) ?>
                            </span></span>
                    </p>
                </td>





            </tr>

            <tr>
                <td class="center" colspan="4" style="font-size: smaller;">
                    Where applicable, the Lessor is represented by: ______________________
                </td>
            </tr>

            <tr>
                <td class="title center" colspan="4">
                    The names indicated in the lease must be those that the lessor and the lessee are legally authorized
                    to
                    use.<br>
                    The term “lessor” in the Civil Code of Québec generally refers to the owner of the immovable.
                </td>
            </tr>


            <tr>
                <td class="title" colspan="4">
                    <b>B | DESCRIPTION AND DESTINATION OF LEASED DWELLING, ACCESSORIES AND DEPENDENCIES</b> ( art. 1892
                    C.C.Q.)
                </td>
            </tr>

            <tr>
                <td style="padding: 0px!important; width: 50%;" colspan="2">

                    <table style="width: 100%" align="left">
                        <tr>

                            <td><b>Address</b>&nbsp; <span class="toptext"><?= $building_address ?>
                                    <?= fill(10) ?><span class="bottomtext">No.
                                    </span></span>&nbsp;&nbsp;
                                <span class="toptext">
                                    <?= fill(60) ?><span class="bottomtext">Street
                                        <?= fill(50) ?></span></span>&nbsp;&nbsp;
                                <span class="toptext"><?= $unit_number ?>
                                    <?= fill(20) ?><span class="bottomtext">Apt.</span></span><br><br><br>
                                <span class="toptext"><?= $building_city ?>
                                    <?= fill(20) ?><span class="bottomtext">Municipality
                                        <?= fill(20) ?></span></span>&nbsp;&nbsp;
                                <span class="toptext"><?= $building_postal_code ?>
                                    <?= fill(20) ?><span class="bottomtext">&nbsp;Postal Code</span></span>&nbsp;&nbsp;
                                <span class="toptext"><?= $size_type_name ?>
                                    <?= fill(20) ?><span class="bottomtext">&nbsp;Number of rooms</span></span>
                            </td>
                        </tr>


                    </table>


                </td>
                <td style="padding: 0px!important; width: 50%;" colspan="2"><br>
                    # of Smart Key __________<br><br>
                    SK1 ____________ &nbsp; &nbsp; SK2 _____________<br><br>
                    SK3 ____________ &nbsp; &nbsp; SK4 _____________
                </td>
            </tr>



            <tr>
                <td colspan="4">

                    The dwelling is leased for residential purposes only. <?= $ecb ?>Yes <?= $cb ?> No<br><br>
                    If the " No" box is checked off, the dwelling is leased for the combined purposes of housing and
                    <span class="toptext"><span class="bottomtext">Specify (e.g.professional activities, commercial
                            activities)</span></span><br><br>

                    but no more than one-third of the total floor area will be used for that second purpose
                    (art. 1892 C.C.Q.).<br><br>

                    The dwelling is located in a unit under divided co-ownership. <?= $ecb ?> Yes <?= $ecb ?> No
                    <br><br>


                    <?= $ecb ?>Outdoor parking

                    Number of places _____________________ Parking space(s)
                    _____________________________________________________<br><br>

                    <?= $ecb ?>Indoor parking

                    Number of places _____________________ Parking space(s)
                    ______________________________________________________<br><br>

                    Locker or storage space
                    <?= $specify ?><br><br>


                    <b>Other accessories and dependencies</b>
                    <?= $specify ?><br><br>



                    Furniture is leased and included in the rent. <?= $ecb ?> Yes <?= $cb ?> No


                </td>
            </tr>

            <tr>
                <td colspan="4" style="padding: 0px">
                    <table class="tablecb" width=100% style="padding: 0px">
                        <tr>
                            <td width=25% valign=top>
                                <b>Appliances</b><br><br>
                                <?= $cb ?> Stove*<br><br>
                                <?= $ecb ?> Microwave oven*<br><br>
                                <?= $ecb ?> Dishwasher*<br><br>
                                <?= $cb ?> Refrigerator*
                            </td>
                            <td width=25% valign=top>
                                <?= $ecb ?> Washer*<br><br>
                                <?= $ecb ?> Dryer*<br><br>
                                <?= $ecb ?> Furniture<br><br>
                                <?= $ecb ?> Table(s) <span class="toptext">____________<span class="bottomtextn">Number</span></span><br><br>
                                <?= $ecb ?> Chair(s) <span class="toptext">____________<span class="bottomtextn">Number</span></span>

                            </td>
                            <td width=25% valign=top>
                                <?= $ecb ?> Chest(s) of drawers <span class="toptext">____________<span class="bottomtextn">Number</span></span><br><br>
                                <?= $ecb ?> Couch(es) <span class="toptext">____________________<span class="bottomtextn">Number</span></span><br><br>
                                <?= $ecb ?> Armchair(s) <span class="toptext">___________________<span class="bottomtextn">Number</span></span><br><br>
                                <?= $ecb ?> Bed(s) <span class="toptext">_______________________<span class="bottomtextn">Number</span></span>
                            </td>
                            <td width=25% valign=top>
                                ______________________________________<br><br>
                                ______________________________________<br><br>
                                ______________________________________<br><br>
                                Lessee is responsible for 50% of the cost for <br>
                                repair of the appliance with *, if it is broken<br>
                                during the lease term.<br><br>

                                <?= $tenant_sign_inits ?>

                                <br><br>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="center" style="border:2px solid; margin: 50px;"><b>The lessor and the lessee
                        undertake,
                        in accordance with their respective responsibilities, to comply
                        with the regulations respecting the presence<br>
                        and proper working order of smoke detectors in the dwelling and the immovable. Lessee will
                        provide a
                        valid liability Insurance before lease starts.</b><br><br>


                    <?= $lessor_sign_init ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?= $datesign ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?= $tenant_sign_init1 ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?= $tenant_sign_init2 ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?= $tenant_sign_init3 ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?= $tenant_sign_date1 ?>&nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <?php if (empty($pdf)) {
                        echo "<br><br><br>";
                    } ?>
                </td>
            </tr>


            <tr>
                <td colspan="4" class="title">
                    C | TERM OF LEASE (art. 1851 C.C.Q. )
                </td>
            </tr>


            <tr>
                <td colspan="2">

                    <h1>FIXED TERM LEASE</h1><br>
                    The term of the lease is <span class="toptext"><?= $length_of_lease ?> <?= $period_name ?>
                        <?php fill(50) ?><span class="bottomtextn">
                            Specify number of weeks, months or years</span></span>&nbsp;
                    &nbsp; &nbsp; &nbsp;<br><br><br>
                    From <?= $start_date ?>&nbsp;
                    &nbsp; &nbsp; &nbsp; to <?= $end_date ?>

                </td>
                <td colspan="2">
                    <h1>INDETERMINATE TERM LEASE</h1><br>
                    The term of the lease is indeterminate,<br><br><br>
                    Beginning on <?= $datesign ?>

                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <b>Neither the lessor nor the lessee may terminate the lease unilaterally, except in the
                        cases provided for by law (particulars Nos. 5, 9, 23, 24, 45 and 51). However, they may
                        terminate the lease by mutual consent.</b>

                </td>
            </tr>
        </table>


        <page_footer>
            <?= $footer1 . " 1 of 3" . $footer2 ?>
        </page_footer>
    </page>
    <page>
        <table style="border:1px solid black; ">


            <tr>
                <td colspan="8" class="title" style="width: 100%;">

                    D | <b>RENT</b> (art. 1855, 1903 and 1904 C.C.Q. )

                </td>
            </tr>

            <tr>
                <td colspan="5">
                    The rent is $ <?= $lease_amount ?>_____________________________ <?= $cb ?>Per
                    month <?= $ecb ?>Per week<br>
                    The total cost of services is $ <?= $total_cost ?>________________ <?= $ecb ?>Per
                    month <?= $ecb ?>Per week<br>
                    The total rent is $ <?= $total_amount ?>_________________________ <?= $cb ?>Per
                    month <?= $ecb ?>Per week<br>


                    The lessee will be charged $20 each time for purchase room key <?= $tenant_sign_inits ?>
                    <br>1 key is granted free if PADs form is signed and accepted<br><br>

                    The lessee is a beneficiary of a rent subsidy program. <?= $ecb ?> Yes <?= $cb ?> No<br>
                    <?= $datesign ?><br><br><br>
                    <h1>DATE OF PAYMENT</h1><br><br>
                    - <b>FIRSTE PAYMENT PERIOD</b><br>
                    The rent will be paid on <?= $datesign ?><br><br><br>
                    - <b>OTHER PAYMENT PERIODS</b><br>
                    The rent will be paid on the 1st day <?= $cb ?> of the Month <?= $ecb ?> of the Week<br>
                    Or on <?= $specify ?><br><br><br>
                    <h1>METHOD OF PAYMENT</h1><br>
                    The rent is payable in accordance with the following method of payment:<br>
                    <?= $cb ?> Pre-authorized debits (PADs) <?= $ecb ?><u>Other <?= fill(50) ?></u><br><br>



                    The lessee agrees to give the lessor postdated cheques for the term of the lease.<br>
                    <?= $cb ?> Yes
                    <?= $ecb ?> No



                    <?= $tenant_sign_inits ?>
                    <br><br>
                    <h1>PLACE OF PAYMENT</h1>
                    Rent payment in cheque shall be dropped in the janitor mailbox of the dwelling or send by<br>
                    Registered-Mail to Landlord.<br>
                    Rent payment in cash shall be paid to the Landlord Authorized Person by Hand with a receipt for
                    the<br>
                    payment.<br>

                </td>
                <td style="border:1px solid" colspan="3">
                    Rent: The rent is payable in equal instalments not<br>
                    exceeding one month’s rent, except for the last<br>
                    instalment, which may be less.<br>
                    A lease with a term of more than 12 months may<br>
                    undergo only one adjustment of the rent during<br>
                    each 12-month period. No adjustment may be<br>
                    made within the first 12 months (art.1906 C.C.Q.).<br><br>

                    <b>The lessor may not exact any other amount of<br>
                        money from the lessee (e.g. deposit for the<br>
                        keys).</b><br><br>
                    Payment of rent for the first payment period: At<br>
                    the time of entering into the lease, the lessor may<br>
                    require advance payment of the rent for only the<br>
                    first payment period (e.g. the first month, the first<br>
                    week).The advance payment may not exceed one<br>
                    month’s rent.<br><br>
                    Payment of rent for the other payment periods:<br>
                    The rent is payable on the first day of each<br>
                    payment period (e.g. month, week), unless<br>
                    otherwise agreed.<br><br>
                    Method of payment: The lessor may not require<br>
                    payment by means of a postdated cheque or any<br>
                    other postdated instrument, unless otherwise<br>
                    agreed.<br><br>
                    Proof of payment: The lessee is entitled to a<br>
                    receipt for the payment of his or her rent in cash<br>
                    (arts. 1564 and 1568 C.C.Q.).<br><br>
                    Place of payment: The rent is payable at lessee’s<br>
                    domicile, unless otherwise agreed (art. 1566<br>
                    C.C.Q.).
                </td>

            </tr>
            <tr>
                <td colspan="8" class="title"> E | SERVICES AND CONDITIONS
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <b>BY-LAWS OF THE IMMOVABLE</b><br>
                    A copy of the by-laws of the immovable was given to the lessee before entering into the lease.<br>
                    Given on <?= $datesign ?>


                    <?= $tenant_sign_inits ?><br><br><br>
                    <b>DIVIDED CO-OWNERSHIP</b><br>
                    A copy of the by-laws of the immovable was given to the lessee.<br>
                    Given on <?= $tenant_sign_date1 ?>


                    <?= $tenant_sign_inits ?><br><br><br>
                    <h1>WORK AND REPAIRS</h1>
                    The work and repairs to be done by the lessor and the timetable for performing them are as
                    follows:<br>
                    - Before the delivery of the dwelling _____________________________________<br>
                    - During the lease ___________________________________________________<br>
                    - If Heating system changes to electricity for which the Tenant is responsible during the lease
                    term,<br>
                    Lessor will be responsible for<br>
                    &nbsp;&nbsp;$50/month of the remaining lease term as an adjustment.

                    <?= $tenant_sign_inits ?><br><br><br>


                    <b>Language:</b> The parties hereto have declared to have requested that the present document be
                    drawn<br>
                    up in the English language.<br><br>

                    <h1>JANITORIAL SERVICES</h1>
                    <?= $specify ?><br><br>

                    The contact information for the janitor or the person to contact if necessary is as follows:<br>
                    <b>For all the request, report "issue" in www.spgmanagement.com, No office hour will provide<br>
                        unless confirmed by an appointment. <u>Tenant agree to use "spgmanagement" or email as<br>
                            offical communication method.</u></b><br><br>

                    <h1>SERVICES, TAXES AND CONSUMPTION COSTS</h1>
                    <table class="tablecb" style="padding: 0px; margin: 0px;" cellpadding=0 cellspacing=0>
                        <?php
                        function LL($LL)
                        {
                            global $cb, $ecb;
                            if ($LL == 1) {
                                echo "<td>$cb</td><td>$ecb</td>";
                            } elseif ($LL == 2) {
                                echo "<td>$ecb</td><td>$cb</td>";
                            } else {
                                echo "<td>$ecb</td><td>$ecb</td>";
                            }
                        }

                        function YN($YN)
                        {
                            global $cb, $ecb;
                            if ($YN == 0) {
                                echo  "<td>$ecb Yes $cb No </td>";
                            } elseif ($YN == 1) {
                                echo  "<td>$cb Yes $ecb No </td>";
                            } else {
                                echo  "<td>$ecb Yes $ecb No </td>";
                            }
                        }

                        ?>

                        <tr>
                            <td><b>Will be borne by:</b></td>
                            <td><b>Lessor</b></td>
                            <td><b>Lessee</b></td>
                            <td>&nbsp;</td>
                            <td><b>Lessor</b></td>
                            <td><b>Lessee</b></td>
                        </tr>
                        <tr>
                            <td>Heating of dwelling</td>
                            <?php LL($heating) ?>

                            <td>Water consumption tax for dwelling</td>
                            <?php LL($water_consumption) ?>
                        </tr>
                        <tr>
                            <td>Gas</td>
                            <?php LL($gas) ?>
                            <td>Snow and ice removal</td>
                            <?php LL($snow_removal) ?>
                        </tr>
                        <tr>
                            <td>Fuel oil</td>
                            <?php LL($fuel_oil) ?>
                            <td>- Parking area</td>
                            <?php LL($snow_parking) ?>
                        </tr>
                        <tr>
                            <td>Electricity other than for heating</td>
                            <?php LL($electricity) ?>
                            <td>- Balcony</td>
                            <?php LL($snow_balcony) ?>
                        </tr>
                        <tr>
                            <td>Hot water heater (rental fees)</td>
                            <?php LL($hot_water_heater) ?>
                            <td>- Entrance,walkway,driveway</td>
                            <?php LL($snow_entrance) ?>
                        </tr>
                        <tr>
                            <td>Hot water (user fees)</td>
                            <?php LL($hot_water) ?>
                            <td>- Stairs</td>
                            <?php LL($snow_stairs) ?>
                        </tr>


                    </table>



                </td>
                <td colspan="3" style="border:1px solid">
                    By-laws of the immovable: The rules to be observed<br>
                    in the immovable are established by by-laws. The by-<br>
                    laws pertain to the enjoyment, use and maintenance<br>
                    of the dwelling and of the common premises.<br><br>
                    If such by-laws exist, the lessor must give a copy of<br>
                    them to the lessee before entering into the lease so<br>
                    that the by-laws form part of the lease (art. 1894<br>
                    C.C.Q.).<br><br>
                    If the dwelling is located in an immovable under<br>
                    divided co-ownership, the by-laws will apply as soon<br>
                    as a copy of them has been given to the lessee by<br>
                    the lessor or by the syndicate of the co-ownership<br>
                    (art. 1057 C.C.Q.).<br><br>
                    <b>The by-laws may not contradict the lease or<br>
                        violate the law.</b><br><br>
                    Work and repairs: On the date fixed for the delivery of<br>
                    the dwelling, the lessor must deliver it in a good state<br>
                    of repair in all respects. However, the lessor and the<br>
                    lessee may decide otherwise and agree on the work<br>
                    to be done and on a timetable for performing the<br>
                    work (art. 1854 1st par. and art. 1893 C.C.Q.).<br><br>

                    However, the lessor may not release himself or<br>
                    herself from the obligation to deliver the dwelling, its<br>
                    accessories and dependencies in clean condition and<br>
                    to deliver and maintain them in good habitable<br>
                    condition (arts. 1892, 1893, 1910 and 1911 C.C.Q.).<br><br>

                    Assessment of the condition of premises: In the<br>
                    absence of an assessment of the condition of the<br>
                    premises (descriptions, photographs, etc.), the lessee<br>
                    is presumed to have received the dwelling in good<br>
                    condition at the beginning of the lease, any damage<br>
                    will be responsible by tenant after move in.(art. 1890<br>
                    2nd par. C.C.Q.).
                </td>
            </tr>


            <tr>
                <td colspan=8>
                    <h1>CONDITIONS</h1><br>
                    The lessee has a right of access to the land.
                    The lessee has the right to keep one or more animals.
                </td>

            </tr>
            <tr>
                <td colspan=8>

                    <table class="tablecb">
                        <tr>
                            <td>The lessee has a right of access to the land.</td>
                            <? YN($lessee_access_land) ?>
                            <td> The lessee can consume cigarette/cannabis </td>
                            <? YN($lessee_cigarette) ?>
                        </tr>
                        <tr>
                            <td>The lessee has the right to keep one or more animals.</td>
                            <? YN($lessee_animals) ?>
                            <td> The lessee has the right to keep washer/dyer in dwelling </td>
                            <? YN($lessee_washer) ?>
                        </tr>
                    </table>
                    <h1>OTHER SERVICES, CONDITIONS AND RESTRICTIONS</h1> (**This lease shall be void if the lessee fail
                    to
                    pass the credit check process upon Lessor approval)<br>
                    As attached in Building Regulation, (Landlord is responsible for $50/month max on electricity if
                    applicable)<br>
                    ______________________________________________________________________________________________________________________<br><br>
                    ______________________________________________________________________________________________________________________<br><br>
                </td>
            </tr>
        </table>
        <page_footer>
            <?= $footer1 . " 2 of 3" . $footer2 ?>
        </page_footer>
    </page>

    <page>
        <table style="border:1px solid black; " align="center">
            <tr>
                <td class="title" colspan=6> F | RESTRICTIONS ON THE RIGHT TO HAVE THE RENT FIXED AND THE LEASE MODIFIED
                    (art.
                    1955 C.C.Q. )</td>
            </tr>

            <tr>
                <td colspan=3>

                    <b>The lessor and the lessee may not apply to the Régie du logement for the fixing of the rent
                        or<br>
                        for the modification of another condition of the lease if one of the following situations
                        applies:</b><br><br>
                    <?= $ecb ?>The dwelling is located in an immovable erected five years ago or less.<br><br>
                    The immovable became ready for habitation on <?= $datesign ?><br><br>
                    <b>OR</b><br>
                    <?= $ecb ?>The dwelling is located in an immovable whose use for residential purposes results from a
                    change<br>
                    of destination that was made five years ago or less.<br>
                    The immovable became ready for habitation on <?= $datesign ?><br><br><br>
                    However, the tribunal may rule on any other application concerning the lease (e.g. decrease in
                    rent).
                </td>


                <td colspan=3 style="border:1px solid">
                    If one of the two boxes opposite is checked off<br>
                    and if the five-year period has not yet expired, the<br>
                    lessee who refuses a modification in his or her<br>
                    lease requested by the lessor, such as an<br>
                    increase in the rent, must vacate the dwelling<br>
                    upon termination of the lease (particulars Nos. 39<br>
                    and 41).<br><br>
                    If neither of the two opposite is checked off and if<br>
                    the lessee refuses a modification in his or her<br>
                    lease requested by the lessor and wishes to<br>
                    continue to live in the dwelling, the lease is then<br>
                    renewed. The lessor may apply to the Regie du<br>
                    logement to have the conditions of the lease fixed<br>
                    for the purposes if its renewal (particulars Nos.41<br>
                    and 42).</td>
            </tr>
            <tr>
                <td class="title" colspan=6> G | <b>NOTICE TO A NEW LESSEE OR A SUBLESSEE</b> (arts. 1896 and 1950
                    C.C.Q. )</td>
            </tr>

            <tr>
                <td colspan=3>
                    <b>Mandatory notice to be given by the lessor at the time the lease or sublease is entered into,<br>
                        except when one of the two boxes in Section F is checked off.</b><br><br>
                    I hereby notify you that the lowest rent paid for your dwelling during the 12 months preceding
                    the<br>
                    beginning of your lease, or the rent fixed by the Régie du logement during that period, was<br>
                    $ <?= $lease_amount ?>__________________________________<br><br>
                    <?= $cb ?> <b>Per Month</b> <?= $ecb ?> <b>Per Week</b> <?= $ecb ?><b>Other</b>
                    _______________________________________________<br><br>
                    The property leased, the services offered by the lessor and the conditions of your lease are the
                    same.<br>
                    If the “No” box is checked off, the following changes have been made<br>
                    (e.g. addition of services of a personal nature, personal assistance services and nursing care,
                    parking,
                    heating):<br>
                    <?= fill(87, "_") ?><br>
                    <?= fill(87, "_") ?><br>
                    <?= fill(87, "_") ?><br><br>
                    <?= $lessor_sign ?>
                    <?= fill(20) ?><?= $datesign ?><br><br>
                </td>


                <td colspan=3 style="border:1px solid">
                    If the new lessee or the sublessee pays a rent<br>
                    higher than that declared in the notice, he or she<br>
                    may, within 10 days after the date the lease or<br>
                    sublease is entered into, apply to the Regie du<br>
                    logement to have the rent fixed.<br><br>
                    If the lessor did not give such notice at the time<br>
                    the lease or sublease was entered into, the new<br>
                    lessee or the sublessee may, within two months<br>
                    after the beginning of the lease, apply to the Regie<br>
                    du logement to have his or her rent fixed.<br><br>
                    The new lessee or the sublessee may also make<br>
                    such application within two months after the day<br>
                    he or she becomes aware of a false statement in<br>
                    the notice.

                </td>
            </tr>

            <tr>
                <td class="title" colspan=6> H | <b>SIGNATURES</b> The lessees undertake to be solidarily liable for the
                    lease <b>(particulars Nos.11 and 12)</b>.</td>
            </tr>
            <tr>
                <td colspan="6">
                    <span class="toptext"><?= $lessor_sign_only ?><?= fill(5) ?><span class="bottomtext">Signature of
                            lessor (or his or her mandatary)</span></span>

                    <?= $datesign ?>
                    <span class="toptext"><?= $tenant_sign1 ?><?= fill(5) ?><span class="bottomtext">Signature of lessee
                            (or
                            his or her mandatary)</span></span>
                    <?= $tenant_sign_date1 ?>
                    <br><br>
                    <span class="toptext"><?= $tenant_sign2 ?><?= fill(5) ?><span class="bottomtext">Signature of lessee
                            (or
                            his or her mandatary)</span></span>
                    <?= $tenant_sign_date2 ?>
                    <span class="toptext"><?= $tenant_sign3 ?><?= fill(5) ?><span class="bottomtext">Signature of lessee
                            (or
                            his or her mandatary)</span></span>
                    <?= $tenant_sign_date3 ?>
                    <br><br>The lessees undertake to be solidarily liable for the lease (particulars Nos. 11 and 12).

                    <?= $tenant_sign_inits ?>






                    <br><br>
                    <b>Any other person who signs the lease must clearly indicate in what capacity he or she is doing so
                        (e.g. another lessor, another lessee, surety).</b><br>
                    <b>by signing this lease, I agree to fully guarantee the responsibility and obligation of the
                        lessee(s)
                        for the current lease term and any renewal terms.</b><br><br>
                    <span class="toptext"><?= fill(45) ?><span class="bottomtext">Name (WRITE LEGIBLY)</span></span>
                    <span class="toptext"><?= fill(60) ?><span class="bottomtextn">Signature</span></span>
                    <span class="toptext"><?= fill(30) ?><span class="bottomtext">Telephone No.</span></span>
                    <span class="toptext"><?= fill(20) ?><span class="bottomtext">Capacity</span></span>
                    <br><br>
                    <?= underline('Address of signatory', 50) ?> &nbsp; <?= underline('Email Address', 40) ?>
                    &nbsp;<?= $datesign ?> <br><br><br><br>

                    <span class="toptext"><?= fill(45) ?><span class="bottomtext">Name (WRITE LEGIBLY)</span></span>
                    <span class="toptext"><?= fill(60) ?><span class="bottomtextn">Signature</span></span>
                    <span class="toptext"><?= fill(30) ?><span class="bottomtext">Telephone No.</span></span>
                    <span class="toptext"><?= fill(20) ?><span class="bottomtext">Capacity</span></span>
                    <br><br>
                    <?= underline('Address of signatory', 50) ?> &nbsp; <?= underline('Email Address', 40) ?>
                    &nbsp;<?= $datesign ?> <br><br><br><br>


                </td>
            </tr>

            <tr>
                <td colspan="6" class="title">
                    <p style='text-align:center'> lessor must give the lessee a copy of the lease within 10 days after
                        entering into the lease (art.
                        1895 C.C.Q.).</p>
                    <b>I | NOTICE OF FAMILY RESIDENCE</b> ( arts. 403 and 521.6 C.C.Q.)
                </td>
            </tr>

            <tr>
                <td colspan="6">
                    A lessee who is married or in a civil union may not, without the written consent of his or her
                    spouse,
                    sublease his or her dwelling, assihn the lease or<br>
                    terminate the lease where the lessor has been notified, by either of the spouses, that the dwelling
                    leased is used as the family residence.<br><br>
                    <b>Notice to lessor</b><br>
                    I hereby declare that I am married to or in a civil union with
                    <?= underline('Name of spouse', 50) ?><br><br><br><br>
                    I hereby notify you that the dwelling covered by the lease will be used as the family
                    residence.<br><br><br><br>
                    <?= underline("Signature of the lessee or lessee's spouse", 80) ?> &nbsp; &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?= underline("Day Month Year") ?>
                    <br><br>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="center" style="border:10px solid #CCCCCC; background-color:#888; color: #FFF">
                    If the lease includes services in addition to those indicated on this form, including services of a
                    personal nature,<br>
                    complete Schedule 6 to the lease: Services Offered to the Lessee by the Lessor.

                </td>
            </tr>
        </table>

        <page_footer>
            <?= $footer1 . " 3 of 3" . $footer2 ?>
        </page_footer>
    </page>
    <!--------------------------------------------------------------------------------------------------->
    <page>



        <div class="center" style="font-size: 12pt">Entend d’Annulation Volontaire d’un Bail de Logement<br>
            Agreement of Voluntary Cancelation of a Lease of Dwelling</div><br>
        <table style="border:1px solid black;" class="voluntary">

            <tr>
                <td>Locataire<br>Tenant</td>
                <td>
                    <?= $tenant_name1 ?>
                </td>
                <td>Propriétaire<br>Landlord</td>
                <td><?= $company_name ?></td>
            </tr>
            <tr>
                <td>Locataire<br>Tenant</td>
                <td>
                    <?= $tenant_name2 ?>
                </td>
                <td>Locataire<br>Tenant</td>
                <td><?= $tenant_name3 ?></td>
            </tr>
            <tr>
                <td colspan="3">Les parties conviennent d'annuler le bail de logement l'adresse en date
                    du<br>The parties agree to cancel the lease of accommodation address in the date of</td>
                <td>_____________________</td>
            </tr>
            <tr>
                <td colspan="4"><?= $building_address ?> Apt. <?= $unit_number ?>
                    <br>
                    Municipality <i>Montreal</i>Postal Code <?= $building_postal_code ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">Le locataire s'engage à quitter les lieux à ses frais avant le<br>The tenant
                    agrees to vacate the premises at his own expense before

                </td>
                <td>_____________________</td>
            </tr>
            <tr>
                <td colspan="4">
                    <p>Upon the signature of the present, the Tenant agrees to terminate the present Lease of the
                        dwelling in the Premise and vacant all personal<br>
                        belonging from the Premise no later than <u>17:00 o`clock on the of mentioned above;</u></p>
                    <p> From that date, at 17h00, the tenant allows the landlord to take possession of the apartment,
                        change the
                        lock on the door, to<br>
                        empty the apartment of its current size at the expense of the tenant and the owner of releases
                        any claim
                        against the owner of<br>
                        some nature whatsoever;</p>
                    <p>
                        The Tenant agrees to surrender the key and access card to the Landlord on or before the
                        termination
                        Date. The Tenant agrees to vacant the<br>
                        dwelling of the Premise in good condition, and broom clean the unit before the Landlord inspects
                        the
                        dwelling on or before the Termination<br>
                        Date;</p>
                    <p>This agreement is specifically and explicitly, a transaction within the meaning of Article 2631
                        and
                        following of the Civil Code of<br>
                        Quebec and rule of law for any purpose that all contractual and legal obligations arising from
                        currency
                        of this case;</p>
                    <p>The Lessee acknowledges having had the opportunity to seek independent counsel before signing the
                        agreement (Lease);Each<br>
                        party acknowledges having read and understood the content of this transaction, said it fully met
                        and
                        recognized as valid and
                        acceptable;</p>
                    <p>This agreement is by no means a waiver of rights and claims of the landlord against the tenant
                        for any
                        amount of unpaid rent,<br>
                        administration fee charges, or damage done to the apartment;</p>
                    <p>The parties declare that they have read and understood the present agreement that it is the
                        expression
                        of their will, and that they<br>
                        sign it freely and without coercion;</p>
                    <p>The parties hereto have declared to have requested that the present document be drawn up in the
                        English
                        language. <i>Les parties<br>
                            aux présentes déclarent avoir exigé que le présent document soit rédigé en langue
                            anglaise.</i></p>
                </td>
            </tr>
            <tr>
                <td>Signature : </td>
                <td><?= $tenant_sign1 ?></td>
                <td>Signature : </td>
                <td><?= $tenant_sign2 ?></td>
            </tr>
            <tr>
                <td>Name (Print) : </td>
                <td><?= $tenant_name1 ?></td>
                <td>Name (Print) : </td>
                <td><?= $tenant_name2 ?></td>
            </tr>
            <tr>
                <td> Date : </td>
                <td><?= $tenant_sign_date1 ?></td>
                <td> Date : </td>
                <td><?= $tenant_sign_date2 ?></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td>Signature : </td>
                <td><?= $tenant_sign3 ?></td>
                <td>Signature : </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Name (Print) : </td>
                <td><?= $tenant_name3 ?></td>
                <td>Name (Print) : </td>
                <td>
                    <? //=$lessor_name
                    ?>
                </td>
            </tr>
            <tr>
                <td> Date : </td>
                <td><?= $tenant_sign_date3 ?></td>
                <td> Date : </td>
                <td><?= $lessor_sign_date ?></td>
            </tr>


        </table>
        <p style="padding-left: 10px">I hereby confirmed and agreed the lease termination, and I have signed below, in
            Montreal</p>
        <p align=right>Signature : <?= $lessor_sign_only ?>
            <?= fill(35) ?><br><br>
            Landlord Authorized Agent (Approval Confirmation)<br><br>
            Date :
            <?= fill(36, '_') ?><br>
        </p>

        <page_footer>
            <?= $footer1 . " Annex A<br>Page 1 of 1" . $footer2 ?>
        </page_footer>
    </page>
    <page>


        <p class="center" style="font-size: 12pt"><b><?= $lessor_name ?><br><br>
                Building By-Law<br><br>
                Annex to the Lease</b></p>
        <ol>
            <li>
                <h2>Resident Requirement</h2>

                <ol style="list-style-type: lower-alpha;">
                    <li>The maximum number of people permitted to occupy an apartment is two (2) for a 1
                        1/2, three (3) for a 3 1/2, four<br>
                        (4) for a 4 1/2, five (5) for a 5 ½ and six (6) for a 6 ½; unless pre-declared and
                        consented to Lessor.</li>
                    <li>No noise is permitted between 10:00 p.m. (22:00 hrs) and 08:00. a.m. During all
                        other hours, excessive noise or<br>
                        disturbances (sound system, TV, musical Instruments, yelling or screaming, etc.) are
                        not permitted. 11. Playing or<br>
                        littering in hallways, corridors and staircases is prohibited. Roller blades,
                        bicycles and skate boards are strictly<br>
                        forbidden in the parking areas, corridor s and common areas.</li>
                    <li>Washers, dishwashers or washing machines are not permitted in the apartments unless
                        otherwise
                        specific</li>
                    <li>Should any damage be caused by <b>rain or frost penetrating through open windows,
                            shower water
                            through</b><br>
                        inappropriate use of shower curtain during taking shower, Tenant(s) will be
                        responsible for all
                        the
                        cost
                        to repair.<br>
                        Tenant will be billed directly for the cost of Labor and material used for execute
                        such repair.
                    </li>
                    <li>It is strictly prohibited to put nails or fixtures on any interior or exterior wall
                        without
                        the
                        consent from Lessor.</li>
                </ol>
                <div style="text-align: right">
                    <?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2>Dwelling Condition and Routine Maintenance Responsibility</h2>

                <ol style="list-style-type: lower-alpha;">
                    <li>In addition to any defects, Tenant(s) shall, in writing, provide written notice
                        to Lessor,
                        within
                        7
                        days of taking<br>
                        possession, of any other defects, which Tenant(s) discovers. Lessor shall address
                        these defects
                        within a<br>
                        reasonable length of time. If Tenant(s) does not provide written notice to Lessor of
                        any further
                        defects
                        within 7 days<br>
                        of taking possession, Tenant(s) shall be deemed to have accepted the Rental Premises
                        as being in
                        good
                        condition.<br>
                        Tenant(s declare having verified the premises, equipment, plumbing and electrical
                        appliances,
                        and
                        acknowledge<br>
                        finding the above mentioned to be in perfect order to be functioning properly and
                        acknowledge
                        that
                        they
                        know how<br>
                        to operate them.</li>
                    <li>Tenant(s) also acknowledges that there is no other promise or representation from
                        Lessor with respect to any <br>
                        alterations, remodeling or decorating, or any other matter other than that which
                        appears in this
                        Agreement.<br>
                        Tenant(s) shall not make any alterations, renovations or decorating changes to the
                        Rental Premises without prior<br>
                        written consent from Lessor.</li>


                    <li>No color paints unless agrees by the Lessor in writing.</li>
                    <li>Tenant(s) will be responsible for all the cost to restore the dwelling into its
                        original
                        condition.
                        Lessor shall have the<br>
                        right to claim such damages to Tenant(s) insurance if it deems necessary.</li>
                    <li><u>The Lessee is responsible for the telephone, cable, television and/or Internet
                            and their
                            installation, changing of light<br>
                            bulbs, fuses and smoke alarm batteries; unless specific otherwise in the
                            Lease.</u></li>
                    <li>In the event of any breakdown of the electrical, mechanical, heating or plumbing
                        systems, Lessor will not be liable or<br>
                        responsible for damages, personal discomfort or any illness arising therefrom, but
                        Lessor will carry out all necessary<br> repairs with reasonable diligence.</li>
                    <li>Tenant(s)shall be responsible for the cost of clearing all clogged drains and
                        toilets. No garbage, refuse, sanitary<br>
                        napkins, tampons, disposable diapers are to be flushed down the toilet or allowed to
                        enter the drainage system.<br>
                        NEVER POUR HOT GREASE OR FAT DOWN THE DRAIN. Pour it in a can, refrigerate it and
                        then put it into the<br>
                        garbage. <b>Providing Lessor delivers the dwelling in good condition, a fee of
                            $150.00
                            plus tax will be charged to the<br>
                            lessee to unblock any sink, toilet, bath tub or wash basin. If it is found that
                            the
                            blockage was caused by <br>the Tenant(s).</b></li>
                    <li>In case of loss or damage due to negligence on the part of the Lessee, the Lessee
                        will be held responsible and<br>
                        will have to pay to Lessor the total cost of repairs, Including parts, materials and
                        labor.</li>
                    <li>At the end of the lease, the Lessee is bound to leave the premises, equipment and
                        electrical appliances to the same<br>
                        condition as the first day that Tenant(s) moved in. Except where repairs or
                        replacements are required by normal<br>
                        wear and tear, Tenant(s)shall be responsible for all repairs and replacements that
                        are
                        required as a
                        result of<br>
                        Tenant(s)’s actions, including but not limited to broken glass, torn screens,
                        damaged light
                        fixtures,
                        plugged toilets<br>
                        and plugged sinks.</li>
                    <li>Tenant(s) shall collaborate and allow all repairs or pest control treatments,
                        deemed
                        necessary by
                        Lessor, to be<br>
                        done. Without pretending or laying claim to any reduction of rent as damages or
                        compensation.
                        <ol style="list-style-type:lower-roman">
                            <li>Tenant(s) shall be liable for damages caused to the leased premises and /or
                                its
                                equipment.</li>
                            <li>Lessor is authorized to enter Tenant(s) apartment at any time to make any
                                necessary
                                repairs or
                                verifications that<br>
                                Lessor may deem necessary.</li>
                            <li>Lessor shall not be held liable for any loss, theft or damages to any of
                                them.
                                Lessee's
                                belongings
                                or goods<br>
                                stored in any storage space or lockers provided by Lessor.
                            </li>
                        </ol>
                    </li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2>
                    Payments and Service Charges</h2>

                <ol style="list-style-type:lower-alpha;">
                    <li>Rent must be paid on or before the first day of each month.</li>
                    <li>Rent payment each delay of FIVE days will be charged $30.00 to the Tenant(s).
                    </li>
                    <li>Rent can be paid by cash, check or money order; 1st N.S.F check will be charged
                        $30.00 to the
                        Tenant
                        for whatever<br>
                        reason may be, and $100.00 for the 2nd time, after 2nd N.S.F, Rent must be paid by
                        cash/certify
                        check.</li>
                    <li>If the Lease is transferred to the third party, Tenant(s) must obtain consent to
                        from the Lessor,
                        and
                        $200<br>
                        administration fee must be paid before the lease transfer process</li>
                    <li>Tenant(s) shall pay $100.00 to the Lessor each time to open the dwelling door if
                        caused by the
                        room
                        card lost,<br>
                        damages or forget inside of the room.</li>
                    <li>Lessor should always have the access to the unit in case of emergency. Unauthorized
                        lock change
                        will
                        be charged<br>
                        $200.00 to the Tenant(s).</li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>



            <li>
                <h2>Vermin</h2>
                <ol style="list-style-type: lower-alpha">
                    <li>Tenant(s)shall keep the Rental Premises free from vermin. <u>If Tenant(s)causes the
                            existence of
                            vermin<br>
                            insects/rodents) whether brought into the building, Tenant(s)s possessions or
                            due to
                            unsanitary
                            conditions caused<br>
                            by Tenant(s), then Tenant(s)shall be held responsible for the fee of elimination
                            of
                            the vermin.</u>
                        Lessor
                        may, at<br>
                        Lessor’s discretion, enter the Rental Premises to exterminate the vermin, with
                        proper notice to
                        Tenant(s).</li>
                    <li>Pets are prohibited: violation of this code will cause immediate eviction of the
                        lessee and
                        pursuit
                        for damages</li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2>Garbage</h2>
                <ol style="list-style-type: lower-alpha; start:1;" start="1">
                    <li>All garbage is to be TIGHTLY WRAPPED and disposed in the basement as direct by
                        Lessor. Tenant(s)
                        shall<br>
                        properly dispose of garbage on a weekly basis. Garbage shall not be left in the
                        hallways or
                        emergency
                        stairs, as it is<br>
                        against the municipal by-law. Garbage shall store in the basement which is
                        designated by Lessor.</li>
                    <li>Tenant(s)s shall recycle waste materials in accordance with municipal guidelines.
                        Large items
                        such as
                        furnishings,<br>
                        appliances, tires and other items not allowed in bagged household garbage and
                        hazardous waste shall
                        be
                        disposed<br>
                        of by Tenant(s) or they shall be held liable for any costs.</li>
                    <li><b>Tenant(s) shall pay a $100.00 within Five (5) business days upon receiving for
                            each
                            failure to
                            put
                            garbage<br>
                            into the garbage room. Lessor will inspect any garbage bag not property disposed
                            to
                            identify the
                            apartment<br>
                            from which the garbage from! Tenant(s) will receive notice letter and proceed in
                            legal action if
                            necessary.</b>

                    </li>

                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>

        </ol>

        <page_footer>
            <?= "<p class='center footer'>Rules and Regulation<br>Page 1 of 2</p>" ?>
        </page_footer>
    </page>
    <page>

        <ol style="start:6;" start="6">

            <li>
                <h2>Insurance</h2>
                <ol style="list-style-type: lower-alpha; start:1;" start="1">
                    <li>Tenant(s) shall, at their sole cost and expense, obtain personal property damage
                        insurance and,
                        by
                        Lessor, civil<br>
                        <b>and liability insurance for the dwelling.</b> Lessor is not responsible for loss,
                        theft or damages
                        caused to
                        the<br>
                        Lessee's personal effects or belongings. (Insurance Liability shall be minimum
                        $2,000,000.00)
                    </li>
                    <li>Lessor will not be responsible of any damages, losses or injuries due to or caused
                        by the
                        operation
                        of the equipment<br>
                        or electrical appliances.</li>
                    <li>Tenant(s) is obligated to provide to Lessor, <b>before move-in</b>, proof that all
                        such insurance is in
                        full
                        force and effect,<br>
                        and Tenant(s) shall notify Lessor, in writing, in the event that such insurance is
                        cancelled or
                        otherwise terminated.</li>
                    <li>Tenant(s) expressly agrees to indemnify Lessor from and against any and all claims,
                        actions,
                        damages,
                        liability and<br>
                        expenses in connection with loss of life, personal injury and/or damage to property
                        arising from
                        any
                        occurrence in,<br>
                        on or about the Rental Premises, whether through Tenant(s)’s use of the Rental
                        Premises or by
                        anyone
                        permitted<br>
                        to be in or on the Rental Premises by Tenant(s), or by fire, smoke, theft, burglary,
                        conditions
                        due to
                        weather such as<br>
                        ice on the grounds, or for any cause whatsoever and in particular but without
                        limiting the
                        generality of
                        the foregoing.<br>
                        <u>IT IS THEREFORE IMPERATIVE THAT EACH TENANT CARRY ADEQUATE PERSONAL LIABILITY
                            AND<br>
                            PROPERTY INSURANCE during their lease term</u>
                    </li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2><u>Subletting</u> or Short-Term Rental</h2>
                <ol style="list-style-type: lower-alpha; start:1;" start="1">
                    <li>It is agree by both Lessor and Tenant(s) the signed rental premises will NOT be
                        sublet, lend,
                        yield,
                        secure or<br>
                        alienate the rented items and/or the rental premises.
                    </li>
                    <li>It is forbidden to use an apartment for commercial purposes, giving courses or
                        lessons or for any other use that may<br>
                        disturb the tranquility of the other Lessees, as this contravenes existing City
                        municipal regulations. Any offending Leasee<br>
                        will be taken to the Rental Board for eviction</li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2>Smoke Alarm & Heat Detector</h2>
                <img src="images/smoke_heat_detector.jpg" align=right width=200> Tenant(s) must not prevent the smoke
                alarm from working by any means.<br>
                <ol style="list-style-type: lower-alpha; start:1;" start="1">
                    <li>Tenant(s) must not by any means block, cover or damage the heat detector from<br>
                        working.</li>
                    <li><b>The person who make damages the heat detector will be charged $350.00<br>
                            minimum plus applicable taxes. Criminal Charges and Legal Issue can<br>
                            be rendered agansit the person who made damages or blocks to the<br>
                            heat detector.</b>


                    </li>

                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li>
                <h2>Change of Size or Destination of the Rental Premises</h2>
                <ol style="list-style-type: lower-alpha; start:1;" start="1">
                    <li>It is specifically understood and agreed by Tenant(s) when signing this lease that
                        upon issuance
                        of a
                        permit by the<br>
                        city of Montreal permitting enlargement, division, or change of destination of the
                        Rental
                        Premises, that
                        Lessor may,<br>
                        upon one (1) month’s Advance Notice, relocate or evict Tenant on the date set forth
                        in the
                        notice.
                        Tenant(s) hereby<br>
                        waives any right to seek compensation or indemnity from Lessor therefor, and agree
                        to vacate the
                        Rental
                        Premises<br>
                        and the cancelation of the Lease therefor by the date set forth in the notice.

                    </li>
                </ol>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li><b>No smoking of cannabis</b><br>
                It is strictly forbidden to use cannabis by inhalation (smoking cannabis). The definition of
                "smoking"
                also refers to the<br>
                use of a pipe, a bong, an electronic cigarette or any other device of this nature. This
                prohibition
                applies to the<br>
                interior and exterior areas of the property, including the dwelling, land, balconies,
                terraces and
                common areas.<br>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>
            <li><b>Pet Deposit</b><br>
                Tenant(s) voluntarily to pay the deposit of $500.00 for possessing a(1) cat with my while
                stay in the
                dwelling. I<br>
                personally responsible for the cat and warranty its cleanness and save the damages to the
                dwelling and
                building.<br>
                Should there be needs of extermination due to my animal Tenant will responsible for the
                whole cost and
                authorize<br>
                Landlord to deduct this deposit for such procedure. Tenant(s) understand If there is no
                damage and
                returning the<br>
                dwelling the same condition as they received upon departure. the Deposit will fully return.<br>
                <div style="text-align: right"><?= $tenant_sign_inits ?></div>
            </li>

        </ol>

        <p>By signing below, Tenant(s) acknowledged that he/she has read, understood and agreed to the terms
            mentioned
            above.<br>
            He/she signs it freely and without coercion. In case of the breach of any term, Lessor may choose to
            <u>undertake
                legal action<br>
                without further notice.</u>
        </p>



        <table style="border:1px solid black; width:100%" class="voluntary">
            <tr>
                <td>Signature : </td>
                <td><?= $tenant_sign1 ?></td>
                <td>Signature : </td>
                <td><?= $tenant_sign2 ?></td>
            </tr>
            <tr>
                <td>Name (Print) : </td>
                <td><?= $tenant_name1 ?></td>
                <td>Name (Print) : </td>
                <td><?= $tenant_name2 ?></td>
            </tr>
            <tr>
                <td> Date : </td>
                <td><?= $tenant_sign_date1 ?></td>
                <td> Date : </td>
                <td><?= $tenant_sign_date2 ?></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td>Signature : </td>
                <td><?= $tenant_sign3 ?></td>
                <td>Signature : </td>
                <td><?= $lessor_sign_only ?></td>
            </tr>
            <tr>
                <td>Name (Print) : </td>
                <td><?= $tenant_name3 ?></td>
                <td>Name (Print) : </td>
                <td>
                    <? //=$lessor_name
                    ?>
                </td>
            </tr>
            <tr>
                <td> Date : </td>
                <td><?= $tenant_sign_date3 ?></td>
                <td> Date : </td>
                <td><?= $lessor_sign_date ?></td>
            </tr>
        </table>
        <page_footer>
            <?= "<p class='center footer'>Rules and Regulation<br>Page 2 of 2</p>" ?><br>
        </page_footer>
    </page>


    <page>
        <p></p>
        <h3 class="center">Options of suretyship offered to the potential tenant<br>
            who had a rental application declined</h3>

        <table style="padding: 20px;">
            <tr>
                <td><br><?= $ecb_big ?></td>
                <td>
                    <h4>
                        JOINT TENANT : I propose to have a joint tenant sign the lease. This person agrees<br>
                        to undergo the same verification process.</h4>
                </td>
            </tr>
            <tr>
                <td><br><?= $ecb_big ?></td>
                <td>
                    <h4>SURETYSHIP : I propose a natural person who will sign the lease as a guarantor<br>
                        for its duration and renewals. This guarantor resides in Québec and agrees to<br>
                        undergo the same verification process.</h4>
                    <h5>Notice: A suretyship by a legal person (company) is not accepted. A suretyship or insurance,
                        resulting a<br>
                        disbursement and contracted by the lessee (tenant), imposing conditions and a contract of
                        adhesion<br>
                        on the lessor (landlord) is not accepted.</h5>
                </td>
            </tr>
            <tr>
                <td><br><?= $ecb_big ?></td>
                <td>
                    <h4>SECURITY DEPOSIT : I propose to pay a security deposit of $ <?= $lease_amount ?> upon the<br>
                        signing of the lease that will be kept by the lessor. At the end of the lease, this<br>
                        amount will be given back to me if I have respected the conditions of the lease.</h4>
                    <h5>Notice: under Article 1904 of the Civil Code of Québec, the lessor (landlord) may not demand a
                        sum of<br>
                        money other than the rent, in the form of a deposit or otherwise. However, the court recognized
                        the<br>
                        legality of a security deposit freely and voluntarily offered by the lessee (tenant). When
                        accepted by the<br>
                        lessor, the proposal becomes an agreement.</h5>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="padding:20; width:100%; border:1px" class="voluntary">
                        <tr>
                            <td colspan="4">
                                <h4>Proposed by us, the undersigned, potential tenant</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>Signature : </td>
                            <td><?= $tenant_sign1 ?></td>
                            <td>Signature : </td>
                            <td><?= $tenant_sign2 ?></td>
                        </tr>
                        <tr>
                            <td>Name (Print) : </td>
                            <td><?= $tenant_name1 ?></td>
                            <td>Name (Print) : </td>
                            <td><?= $tenant_name2 ?></td>
                        </tr>
                        <tr>
                            <td> Date : </td>
                            <td><?= $tenant_sign_date1 ?></td>
                            <td> Date : </td>
                            <td><?= $tenant_sign_date2 ?></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td>Signature : </td>
                            <td><?= $tenant_sign3 ?></td>
                            <td>Signature : </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Name (Print) : </td>
                            <td><?= $tenant_name3 ?></td>
                            <td>Name (Print) : </td>
                            <td>
                                <? //=$lessor_name
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td> Date : </td>
                            <td><?= $tenant_sign_date3 ?></td>
                            <td> Date : </td>
                            <td><?= $lessor_sign_date ?></td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">Accepted by <?= $lessor_name ?>, lessor</td>
                        </tr>
                        <tr>
                            <td>Signature </td>
                            <td><?= $lessor_sign_only ?></td>
                            <td>Date: </td>
                            <td><?= $lessor_sign_date ?></td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </page>




    <?php if (empty($pdf)) { ?>
        <form method="POST" action="lease_res2.php" onsubmit="return checkAllSigned()" id="signForm">
            <input type="hidden" name="lease_id" value="<?= $lease_id ?>">
            <input type="hidden" name="hc" value="<?= $hc ?>">
            <input type="hidden" name="rid" value="<?= $rid ?>">
            <input type="hidden" name="stid" value="1">
            <input type="hidden" name="who_sign" value="<?= $who_sign ?>">
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


            $(document).ready(function() {
                //   $("#danger-alert").hide();
                $("#danger-alert").fadeTo(2000, 500).slideUp(1000, function() {
                    $("#danger-alert").slideUp(500);
                });
            });
        </script>
    </body>

    </html>
<?php } ?>