<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
if (!empty($_GET['l'])){$Language_ID=$_GET['l'];}else{$Language_ID=1;}
include("../pdo/dbconfig.php");
$crud=new Crud($DB_con);
$crud->query("SELECT contact_us from settings where active_id=1 and language_id=$Language_ID");
$contact_us_content=$crud->resultField();
?>
<!-- <style>
    #contact_us {
        height: 600px;
        padding:10px;
        overflow: scroll;
    }

</style> -->
<!-- <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#contact_us">Contact Us</a></li>
</ul -->

<!-- <div class="tab-content">
    <div id="contact_us" class="tab-pane fade in active"> -->
        <?=$contact_us_content?>
    <!-- </div>
</div> -->