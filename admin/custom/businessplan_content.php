<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}

include_once("$path/dbconfig.php");
include_once("$path/Class.Analyze.php");
if (empty($_SESSION)){session_start();}
$DB_analyze = new Analyze($DB_con);
$company_id = $_SESSION['company_id'];
$building_ids=0;
$apartment_id=0;
$payment_method_id=0;
$payment_type_id=0;
$month_due_date=0;
$province_id=0;
$size_type_id=0;
$TotalIncome=0;
$TotalExpence=0;
$NextYear=date("Y")+1;
$current_year=date("Y");

$BuildingsIncomes = $DB_analyze->getBuildingsMonthlyIncome($company_id, $building_ids);
$BuildingsExpences = $DB_analyze->getBuildingsMonthlyExpense($company_id, $building_ids);
//var_dump($BuildingsIncomes);
//die();
for ($j=2010;$j<=$current_year+2;$j++){
for ($i=1;$i<=12;$i++){$BuildingExpMonth[$j][$i]=0;
    $BuildingIncMonth[$j][$i]=0;
}}

foreach ($BuildingsIncomes as $BuildingsIncome){
    $TotalIncome +=  $BuildingsIncome['paid'];
    $BuildingIncMonth [$BuildingsIncome['year_due_date']] [$BuildingsIncome['month_due_date']]=$BuildingIncMonth [$BuildingsIncome['year_due_date']] [$BuildingsIncome['month_due_date']]+$BuildingsIncome['paid'];
}

foreach ($BuildingsExpences as $BuildingsExpence){
    // $TotalExpence +=  $BuildingsExpence['amount'];
    // $BuildingExpMonth [$BuildingsExpence['year_invoice_date']] [$BuildingsExpence['month_invoice_date']]=
    // $BuildingExpMonth [$BuildingsExpence['year_invoice_date']] [$BuildingsExpence['month_invoice_date']]
    // +$BuildingsExpence['amount'];
}
?>

<div class="container">

    <ul class="nav nav-pills card">
      <li class="nav-item"><a class="nav-link" href="#"><?=$current_year-1?></a></li>
      <li class="nav-item active"><a class="nav-link" href="#">2018</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><?=$current_year+1?></a></li>
    </ul>

<div class="card">
    <div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <h3>Incoming <?=date("Y")?>: <b>$<?=FormatNum($TotalIncome);?></b></h3>
            <h3>Expenses <?=date("Y")?>: <b>$<?=FormatNum($TotalExpence);?></b></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <hr>
        </div>
    </div>
<div class="row">
    <div class="col-sm-8">
        <p>Please enter your participate for following year:</p><br>
      <p>Incoming <?=$NextYear?>: <input type="text" class="form-control" size="10" value="<?=FormatNum($TotalIncome);?>" id="TotalIncome-<?=$NextYear?>" onchange="calc();"></p>
      <p>Expenses <?=$NextYear?>: <input type="text" class="form-control" size="10" value="<?=FormatNum($TotalExpence);?>" id="TotalExpence-<?=$NextYear?>" onchange="calc();"></p>
    </div>
    <div class="col-sm-2">
    </div>
  </div>
  <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="row" style="margin-bottom: 5px;">
                  <div class="col-sm-1"><strong>Month</strong></div>
                  <div class="col-sm-1 "><strong>Incoming</strong></div>
                    <div class="col-sm-4 "><strong>Statements</strong></div>
                  <div class="col-sm-1 "><strong>Expenses</strong></div>
                    <div class="col-sm-4 "><strong>Statements</strong></div>
                </div>
                <?for ($monthNum=1;$monthNum<=12;$monthNum++){?>
                <div class="row" style="margin-bottom: 5px;">
                    <div class="col-sm-1"><?=date("F", mktime(0, 0, 0, $monthNum, 10))?></div>
                    <div class="col-sm-1 "><input type="text" size="10" id='TotalIncome-<?="$NextYear-$monthNum"?>' value="<?=FormatNum($BuildingIncMonth[$current_year][$monthNum])?>"></div>
                    <div class="col-sm-4 "><a class="btn btn-info" href="rental_paymentslist.php?cmd=search&t=rental_payments&z_paidornot_status=LIKE&x_paidornot_status=1&z_month_due_date=%3D&x_month_due_date=<?=$monthNum?>&z_year_due_date=%3D&x_year_due_date=<?=$current_year?>">Business Income Statement</a></div>
                    <div class="col-sm-1 "><input type="text" size="10" id='TotalExpence-<?="$NextYear-$monthNum"?>' value="<?=FormatNum($BuildingExpMonth[$current_year][$monthNum])?>"></div>
                    <div class="col-sm-4 "><a class="btn btn-info" href="payment_infoslist.php?cmd=search&t=bill_payment&z_payment_month=%3D&x_payment_month=<?=$monthNum?>&z_payment_year=%3D&x_payment_year=<?=$current_year?>&z_is_void=%3D&x_is_void=0&z_is_signed=%3D&x_is_signed=1">Business Expences Statement</a></div>
                </div>
                <?}?>
            </div>
        </div>
    </div>

  </div>


</div>
<script>
    function calc(){
        TotalIncome=removeCommas($("#TotalIncome-<?=$NextYear?>").val());
        TotalExpence=removeCommas($("#TotalExpence-<?=$NextYear?>").val());
        MonthlyIncome=round(TotalIncome/12);
        MonthlyExpence=round(TotalExpence/12);
        //alert (TotalExpence);
        <?for ($monthNum=1;$monthNum<=12;$monthNum++){?>
        $("#TotalIncome-<?=$NextYear?>-<?=$monthNum?>").val(MonthlyIncome);
        $("#TotalExpence-<?=$NextYear?>-<?=$monthNum?>").val(MonthlyExpence);
        <?}?>
    }

    function removeCommas(str) {
        return(str.replace(/,/g,''));
    }

    function round(num){
      return Math.round(num * 100) / 100;
    }
</script>
<?
function FormatNum($number){
    return number_format($number,0,'.',',');
}
?>
