<?php
header("Location: attachment_infoslist.php");
//namespace PHPMaker2023\spgmanagement;
include_once("../pdo/dbconfig.php");
include_once '../pdo/Class.Snapshot.php';
$DB_snapshot = new Snapshot($DB_con);
?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<link href="custom/css/home_style_vendor.css" rel="stylesheet" type="text/css">

<div id="page-wrapper">
    <main>
        <section id="content">

            <div class="container">

                <div class="row">
                    <!-- left -->

                    <div class="col-xs-12 col-sm-6 col-md-6">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?php echo $DB_snapshot->echot("Quick Access"); ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>
                                    <!--<li><a href="vendor_infosedit.php?showdetail=&vendor_id=300002"><i class="far fa-address-book fa-3x" style="color: darkred"></i></i><p>Edit Your Profile</p></a></li>-->
                                    <!--                                <li><a href="projectinfoslist?cmd=resetall"><i class="fas fa-building fa-3x" style="color: red"></i><p>Projects</p></a></li>-->
                                    <!--                                <li><a href="proposal_infoslist.php?cmd=resetall"><i class="fas fa-file-signature fa-3x" style="color: blue"></i><p style="font-family:Verdana">Proposals</p></a></li>-->
                                    <li><a href="contract_infoslist.php?cmd=resetall"><i class="fas fa-file-contract fa-3x" style="color: orchid"></i>
                                            <p>Contracts</p>
                                        </a></li>

                                    <li><a href="invoice_infoslist.php?cmd=resetall"><i class="fas fa-file-invoice  fa-3x" style="color: green"></i>
                                            <p>Invoices</p>
                                        </a></li>

                                    <li><a href="payment_infoslist.php?cmd=resetall"><i class="far fa-money-bill-alt fa-3x"></i>
                                            <p>Payments</p>
                                        </a></li>
                                    <li><a href="invoice_infosadd.php"><i class="fas fa-file-invoice-dollar fa-3x" style="color: green"></i> <i class="fas fa-plus"></i>
                                            <p>Add an Invoice</p>
                                        </a></li>
                                    <li><a href="payment_infosadd.php"><i class="fas fa-money-check fa-3x"></i> <i class="fas fa-plus"></i>
                                            <p>Add Payment</p>
                                        </a></li>
                                    <li><a href="attachment_infoslist.php"><i class="fa fa-file-pdf-o fa-3x" style="color:maroon"></i>
                                            <p>Attachments (<?= $DB_snapshot->getNoOfUnReadAtt() ?>)</p>
                                        </a></li>
                                </ul>
                            </div>
                        </div>



                    </div>


                </div><!-- end row-->
            </div><!-- end container-->
        </section>
    </main>

</div>
<!--page-wrapper-->