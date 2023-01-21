<?
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
if (!empty($_GET['l'])){$Language_ID=$_GET['l'];}else{$Language_ID=1;}
include("../pdo/dbconfig.php");
$crud=new Crud($DB_con);
$crud->query("SELECT * from settings where active_id=1 and language_id=$Language_ID");
$row=$crud->resultSingle();
// die(var_dump($row));
?>
<style>
    #Terms, #Privacy , #Refunds  {
        height: 600px;
        padding:10px;
        overflow: scroll;
    }

</style>
<ul class="nav nav-tabs">
    <li class="nav-item" class="active"><a class="nav-link active" role="tab" data-toggle="tab" href="#Terms">Terms</a></li>
    <li class="nav-item"><a class="nav-link" role="tab" data-toggle="tab" href="#Privacy">Privacy</a></li>
    <li class="nav-item"><a class="nav-link" role="tab" data-toggle="tab" href="#Refunds">Refunds</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel"  id="Terms" class="tab-pane in active">
        <p><?=$row['terms']?></p>
    </div>
    <div role="tabpanel"  id="Privacy" class="tab-pane">
        <p><?=$row['privacy']?></p>
    </div>
    <div role="tabpanel"  id="Refunds" class="tab-pane">
        <p><?=$row['refunds']?></p>
    </div>
</div>
