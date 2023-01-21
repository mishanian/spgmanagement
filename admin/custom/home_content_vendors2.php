<?php
include_once("../pdo/dbconfig.php");
?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<link href="custom/css/home_style_vendor.css" rel="stylesheet" type="text/css">

<div id="page-wrapper">
    <main>
        <section id="content">

            <div class="container">

                <div class="row">
                    <!-- left -->

                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "My File" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>
                                    <li>
                                        <a href="vendor_infosedit.php?showdetail=&vendor_id=<?= $_SESSION['vendor_id'] ?>"><i class="far fa-address-book fa-3x" style="color: darkred"></i></i>
                                            <p>
                                                My Profile</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="vendor_licenseslist.php?showmaster=vendor_infos&fk_vendor_id=<?= $_SESSION['vendor_id'] ?>"><i class="fas fa-file-alt fa-3x" style="color: darkred"></i>
                                            <p>My Licenses</p>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>


                    </div>


                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "My Clients" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>
                                    <li>
                                        <a href="company_infoslist.php?&vendor_id=<?= $_SESSION['vendor_id'] ?>"><i class="far fa-address-book fa-3x" style="color: darkred"></i></i>
                                            <p>
                                                Property Managers</p>
                                        </a>
                                    </li>
                                    <li>

                                </ul>
                            </div>
                        </div>


                    </div>


                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "My Projects" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>

                                    <li><a href="projectinfoslist?cmd=resetall"><i class="fas fa-building fa-3x" style="color: red"></i>
                                            <p>Projects</p>
                                        </a></li>
                                    <li><a href="proposal_infoslist.php?cmd=resetall"><i class="fas fa-file-signature fa-3x" style="color: blue"></i>
                                            <p style="font-family:Verdana">Proposals</p>
                                        </a></li>
                                    <li><a href="contract_infoslist.php?cmd=resetall"><i class="fas fa-file-contract fa-3x" style="color: orchid"></i>
                                            <p>Contracts</p>
                                        </a></li>
                                    <li><a href="invoice_infoslist.php?cmd=resetall"><i class="fas fa-file-invoice  fa-3x" style="color: green"></i>
                                            <p>Invoices</p>
                                        </a></li>


                                </ul>
                            </div>
                        </div>


                    </div>


                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "My Finance" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>


                                    <li><a href="payment_infoslist.php?cmd=resetall"><i class="far fa-money-bill-alt fa-3x"></i>
                                            <p>Payments</p>
                                        </a></li>
                                    <li><a href="view_shared_payment_infoslist.php?cmd=resetall"><i class="far fa-money-bill-alt fa-3x"></i>
                                            <p>Shared Payments</p>
                                        </a></li>
                                    <hr>

                                    <!--                                <li><a href="project_infosadd.php"><i class="fas fa-building fa-3x" style="color: red"></i> <i class="fas fa-plus"></i><p>Add a Project</p></a></li>-->
                                    <!--                                <li><a href="proposal_infosadd.php"><i class="fas fa-building fa-3x" style="color: blue"></i> <i class="fas fa-plus"></i><p>Add a Proposal</p></a></li>-->
                                    <!--                                <li><a href="contract_infosadd.php"><i class="fas fa-file-invoice-dollar fa-3x" style="color: orchid"></i> <i class="fas fa-plus"></i><p>Add a Contract</p></a></li>-->

                                    <li><a href="invoice_infosadd.php"><i class="fas fa-file-invoice  fa-3x" style="color: green"></i> <i class="fas fa-plus"></i>
                                            <p>Add an Invoice</p>
                                        </a></li>
                                    <li><a href="payment_infosadd.php"><i class="fas fa-money-check fa-3x"></i> <i class="fas fa-plus"></i>
                                            <p>Request for Payment</p>
                                        </a></li>
                                </ul>
                            </div>
                        </div>


                    </div>


                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "Files Pool" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>


                                    <li><a href="attachment_infoslist.php"><i class="fa fa-file-pdf-o fa-2x" style="color:maroon"></i>
                                            <p>File Center</p>
                                        </a></li>

                                </ul>
                            </div>
                        </div>


                    </div>

                    <div class="col-xs-12 col-sm-3 col-md-3">

                        <div class="profile white-bkg">
                            <div class="headings_box">
                                <ul>
                                    <li class="pull-left"><?= "My Supplier" ?></li>
                                    <li class="pull-right"><span><?php //echo $building_num;
                                                                    ?></span></li>
                                    <div class="clearfix"></div>
                                </ul>
                            </div>

                            <div class="points">
                                <ul>


                                    <li><a href="#"><i class="fa fa-file-pdf-o fa-2x" style="color:maroon"></i>
                                            <p>Suppliers</p>
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

<?php

function timediff($begin_time, $end_time)
{
    $timediff = $end_time - $begin_time;

    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}

?>