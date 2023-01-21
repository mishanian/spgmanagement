<?php

include_once('../../../pdo/dbconfig.php');
include_once('../../../pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once("../../../pdo/Class.Apt.php");
$DB_apt  = new Apt($DB_con);
// include_once('../pdo/Class.Company.php');
// $DB_company = new Company($DB_con);
include_once('../../../pdo/Class.LeasePayment.php');
$DB_ls_payment = new LeasePayment($DB_con);

// require_once __DIR__ . '/mpdf-development/vendor/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();

if (!isset($_GET["fromPid"])) {
  echo "Failed to generate Deposit Slip PDF.";
  die();
}

$depositRecordId = $_GET["fromPid"];

// Fetch Record from deposit_infos table
$recordData = $DB_ls_payment->getAllDepositRecords($depositRecordId);
$depositData = $recordData["deposit_data"];

$depositDataArr = json_decode($depositData, true);

$date = $recordData["deposit_date"];
$accountNo = $depositDataArr["accountNo"];
$nameOfAccount = $depositDataArr["nameOfAccount"];
$branchNo = $depositDataArr["branchNo"];
$chequeSubtotal = $depositDataArr["chequeSubtotal"];
$cashSubtotal = $depositDataArr["cashSubtotal"];

$chequeData  = $depositDataArr["chequeData"];
$cashData  = $depositDataArr["cashData"];
$paidtotal = $depositDataArr["paidTotal"];
// die($depositDataArr["buildingId"]);
require_once('return_address.php');
$building_names = return_address($chequeData);


// if (!empty($depositDataArr["buildingId"])) {
//   $building_name = $DB_building->getBdName(intval($depositDataArr["buildingId"]));
// } else {
//   $building_name = "";
// }




$slipHtml = '<!DOCTYPE html>
<head>
<style>
#deposit,#depositCheque {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

    
.borderForStamp{
float:right;
border: 1px solid #8D8D8D;
height:120px;
margin-left:5px;
padding-left:2px;
}

.mt-25{
margin-top:25px;
}

.borderForSignature{
border:1px solid #8D8D8D;
border-top:0px;
border-right: 0px;
border-left: 0px;
}

#depositCash {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#deposit td, #deposit th {
    padding: 8px;
}
#depositCheque td, #depositCheque th {
    border:1px solid #ddd;
    padding: 8px;
}
#depositCash td, #depositCash th {
    border:1px solid #ddd;
    padding: 8px;
}

#deposit tr:nth-child(even){background-color: #ffffff;}

#deposit tr:hover {background-color: #ddd;}

#deposit th {
width:100%;
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
}
#depositCheque th {
width:100%;
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
}

#depositCash th {
    width:70%;
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
}
td, th {
vertical-align:top;
}
.boxes{
border:1px solid #D5D5D5;
margin:10px;
padding:10px;
}
</style>
</head>
<body>


<div>
<span><strong>Name of the Account : </strong>  ' . $nameOfAccount . '</span>
<div><strong>Date : </strong>' . $date . '</div>
<div><strong>Branch No : </strong> ' . $branchNo . '</div>
<div><strong>Account No : </strong> ' . $accountNo . '</div>
<div><strong>Address : </strong> ' . $building_names . '</div>
</div>

<br>
<div>
<table id="deposit">
<tbody>



<tr>
<td style="width:70%">
 <table id="depositCheque" class="">
    <thead>
      <tr>
      <th>Type</th>
        <th>Address</th>
        <th>Cheque ID</th>
        <th>Cheque/Cash Amount</th>
      </tr>
    </thead>
    <tbody>';
$chq_counter = 0;
foreach ($chequeData as $pid => $data) {
  $buildingName = "";
  $leaseInfo = $DB_ls_payment->get_lease_info_by_lease_payment_detail_id($pid);
  $buildingId = $leaseInfo["building_id"];
  $apartmentId = $leaseInfo["apartment_id"];
  $buildingName = $DB_building->getBdName($buildingId);
  $apartmentNo = $DB_apt->getAptInfo($apartmentId)["unit_number"];

  if ($data["ptype"] == "1") {
    if ($data["amt"] == 0) {
      continue;
    }
    $slipHtml .= '
    <tr>
    <td width="10%">Cash</td>
      <td width="50%">' .  $buildingName . ' - ' . $apartmentNo . '</td>
      <td width="20%">-</td>
      <td width="20%">$' . $data["amt"] . '</td>
    </tr>';
  } else {

    $chq_counter++;
    $slipHtml .= '
      <tr>
      <td width="10%">Chq</td>
        <td width="50%">' .  $buildingName . ' - ' . $apartmentNo . '</td>
        <td width="20%">' . $data["chequeid"] . '</td>
        <td width="20%">$' . $data["chequeamt"] . '</td>
      </tr>';
  }
}

$slipHtml .= '</tbody>
  </table>

</td>
<td style="width:30%">
 <table id="depositCash" class="table table-striped">
    <thead>
      <tr>
        <th>Cash Count</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>';

foreach ($cashData as $id => $cashDataSingle) {
  if (!$cashDataSingle["count"]) {
    $cashDataSingle["count"] = 0;
    $cashDataSingle["amount"] = 0;
  }
  if ($cashDataSingle["denom"] == 0) {
    $cashDataSingle["denom"] = "¢ ";
  }
  $slipHtml .= '<tr>
                        <td> ' . $cashDataSingle["count"] . ' <strong> X </strong> $' . $cashDataSingle["denom"] . ' </td>
                        <td> $' . $cashDataSingle["amount"] . '</td>
                    </tr>';
}

$slipHtml .= '</tbody>
  </table>

</td>
</tr>

<tr>
<td class="boxes" width="70%"> <div><strong>Cheque Subtotal : </strong> $' . $chequeSubtotal . ' </div>
</td><td width="30%">&nbsp;</td>

<tr>
<td class="boxes" width="70%"> <div><strong>Cash Subtotal : </strong> $' . $cashSubtotal . ' </div>
</td><td width="30%">&nbsp;</td>


</tr>

<tr>
<td class="boxes" width="70%"><div><strong>Total # of Cheques : </strong> ' . $chq_counter . '</div></td>
<td class="boxes" width="30%">   <div><strong>Total Deposit</strong> $' . $paidtotal . ' </div> </td>
</tr>

<tr>
  <td colspan="2"></td>
</tr>

<tr>
<td class="borderForSignature" style="width:50%;"> </td>
<td class="borderForStamp"></td>
</tr>

</tbody>
</table>
</div>


</body>
</html>
';
// $mpdf->keep_table_proportions = true;
// $mpdf->simpleTables = true;
// $mpdf->packTableData = true;
// $keep_table_proportions = TRUE;
// $mpdf->shrink_tables_to_fit = 1;
$mpdf->SetTitle('Deposit - ' . $date);
$mpdf->WriteHTML($slipHtml, 0);
$mpdf->Output();