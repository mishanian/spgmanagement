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
// die(var_dump($row));

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
$datesign = "<span class='toptext'>&nbsp; &nbsp; " . date('m/d/Y') . fill(15)."<span class='bottomtext'> Date (MM/DD/YY)</span></span>&nbsp;";


if (empty($tid) || $prv==1) {
    $tenant_sign="";
} else {
    $tenant_sign="<img src='../../files/lease_signs/tenant_sign_".$tid."_l".$lease_id.".png' height='50'>";
}



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
            font-size: 10pt;
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
            font-size: 10pt;
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
            font-size: 9pt;
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
            line-height: <?=($pdf==1 ? 12 : 14) ?>pt;
            padding: 0px;
            /*10px;*/
            font-size: 10pt;
        }
    
        p.footer {
            font-size: 7pt;
        }
    
   
        li {
            font-size: 10pt;
            padding-top: 2pt;
            padding-bottom: 1pt;
        }
        
        </style>
        <? if (empty($pdf)) { ?>
        <title>Lease</title>
    </head>
    
    <body>
        <? } ?>
        
      
        
        <page>
                   
<h3 class=center style="margin-bottom: 0px;">Pre-Authorized Debit (PAD) Agreement</h3>
<h4 class=center style="margin-bottom: 0px;">MG Real Estate Management Inc.<br>
100-1650 Rene Levesque, Montreal, H3H 2S1 / 514-937-3529</h4>
<h4 class=center>Please complete the Pre-Authorized Debit (PAD) Plan agreement below.</h4>
<p>I/we authorize MG Real Estate Management Inc. and the financial institution designated (or any other financial institution I/We
may authorize at any time) to begin deductions as per my/our instructions for monthly regular recurring payments and/or onetime
payments from time to time, for payment of all charges arising. Regular monthly payments for the full amount of services
delivered will be debited to my/our specified account on the 1st day of each month. MG Real Estate Management Inc. will obtain
my/our authorization for any other one-time or sporadic debits.</p>
<p>This authority is to remain in effect until MG Real Estate Management Inc. has received written notification from me/us of its
change or termination. This notification must be received at least ten (10) business days before the next debit is scheduled at the
address provided below. I/We may obtain a sample cancellation form, or more information on my/our right to cancel a PAD
Agreement at my/our financial institution or by visiting www.payments.ca</p>
<p>MG Real Estate Management Inc. may not assign this authorization, whether directly or indirectly, by operation of law, change of
control or otherwise, without providing at least 10 days prior written notice to me/us.</p>
<p>I/we have certain recourse rights if any debit does not comply with this agreement. For example, I/we have the right to receive
reimbursement for any PAD that is not authorized or is not consistent with this PAD Agreement. To obtain a form for a
Reimbursement Claim, or for more information on my/our recourse rights, I/we may contact my/our financial institution or visit
www.payments.ca</p>

<table style="width: 100%;"><tr><td><b>PLEASE PRINT</b></td><td style="width:75%; text-align: right;"><?=$datesign?></td></tr></table>
<p>
Name(s): 
<span class="toptext"><b><?=$full_name?></b><?=fill(45)?><span class="bottomtext">(WRITE LEGIBLY) FIRSTLAST NAME </span></span>
Client-Acct Number:
<span class="toptext"><?=fill(45)?><span class="bottomtext">(WRITE LEGIBLY) </span></span>
</p>    
<p>
Type of Service: <?=$cb?> PERSONAL <?=$ecb?> BUSINESS</p>
<p>
Address: <span class="toptext"><b><?=$address." #".$unit_number?></b><?=fill(80)?><span class="bottomtext">(WRITE LEGIBLY) <?=fill(180)?></span></span>

</p>

<p>
City/Town: <span class="toptext"><b><?=$city?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Province: <span class="toptext"><b><?=$province_name?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Postal Code: <span class="toptext"><b><?=$postal_code?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
</p>

<p>
Telephone: <span class="toptext"><b><?=$telephone?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Cell: <span class="toptext"><b><?=$mobile?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Email: <span class="toptext"><b><?=$email?></b><span class="bottomtext"><?=fill(50)?> </span></span> <?=fill(30)?>
</p>



<p>
<b>Financial Institution (FI):</b> <span class="toptext"><b><?=$banking_name?></b><span class="bottomtext"><?=fill(180)?> </span></span>
</p>
<p>
FI Account Number: <span class="toptext"><b><?=$banking_accountno?></b><span class="bottomtext"><?=fill(20)?> </span></span> <?=fill(5)?>
FI Transit Number: <span class="toptext"><b><?=$banking_transit?></b><span class="bottomtext">(Branch -5 digits; <?=fill(20)?> </span></span> <?=fill(5)?> - 
<span class="toptext"><b><?=$banking_institution?></b><span class="bottomtext">FI – 3 digits)<?=fill(30)?> </span></span> 
</p>
<p>
FI Address: <span class="toptext"><b><?=$banking_address?></b><span class="bottomtext">(WRITE LEGIBLY)<?=fill(180)?> </span></span>
</p>
<p>
City/Town: <span class="toptext"><b><?=$city?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Province: <span class="toptext"><b><?=$province_name?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
Postal Code: <span class="toptext"><b><?=$postal_code?></b><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
</p>
<p>
Authorized Signature(s): 
<span class='toptext btn btn-warning' onclick='toast(this)'><?=$tenant_sign?><span class="bottomtext"><?=fill(30)?> </span></span> <?=fill(30)?>
</p>
        </page>
        <? if (empty($pdf)) { ?>
    <form method="POST" action="lease_res2.php" onsubmit="return checkAllSigned()" id="signForm">
        <input type="hidden" name="lid" value="<?= $lease_id ?>">
        <input type="hidden" name="hc" value="<?= $hc ?>">
        <input type="hidden" name="tid" value="<?= $tid ?>">
        <input type="hidden" name="stid" value="2">
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