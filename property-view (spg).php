<?php
session_start();
$locallist = array(
    '127.0.0.1',
    '::1',
    'localhost'
);
if (!in_array($_SERVER['REMOTE_ADDR'], $locallist)) {
    include "subdomain_check.php";
}

if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
}

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if(empty($_SESSION['company_id'])){
    $_SESSION['company_id']=9;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
    <title><?php
        include_once("pdo/dbconfig.php");
        include_once 'pdo/Class.Company.php';
        $DB_company = new Company($DB_con);
        $company_id = $_SESSION['company_id'];
        echo $DB_company->getWebTitle($company_id); ?></title>
    <!--<base href="/" />-->
    <!--Meta tags-->
    <style>
        .owl-height {
            min-height: 500px;
            height: auto !important;
            height: 500px;
        }

        .availablity .park_text strong {
            font-size: 15px !important;
        }

        @media screen and (max-width: 768px) {
            .HideOnMobile {
                visibility: hidden;
                display: none;
            }

            .topflex {
                display: -webkit-box;
                display: -moz-box;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                -webkit-flex-flow: row wrap;
                -moz-flex-flow: row wrap;
                -ms-flex-flow: row wrap;
                flex-flow: row wrap;
            }
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once("links-for-html.php") ?>
    <!-- Yuhong: sortable table -->
    <script src="./js/sorttable.js"></script>
</head>

<body>
<?php
include_once("header.php");
include_once("search-bar.php");
$building_id = $_GET["pass_bd_id"];
$building_row = $DB_building->getBdInfo($building_id);
?>


<!--start detail top-->
<section class="detail-top detail-top-grid">
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <div class="header-detail table-list">
                    <div class="header-left">
                        <ol class="breadcrumb">
                            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                            <li><a href="map-listing.php"><?php echo $DB_snapshot->echot("Property"); ?></a></li>
                            <li class="active"><?php echo $building_row['building_name']; ?></li>
                        </ol>
                        <h1> <?php echo $building_row['building_name']; ?> <span class="label-wrap hidden-sm hidden-xs"> <span
                                        class="label label-primary">For Rent</span>
                                <!--span class="label label-danger">Sold</span--> </span></h1>
                        <address class="property-address">
                            <i class="fa fa-map-marker color_brown"></i>
                            <?php echo $DB_building->getFullAddress($building_id); ?>
                        </address>
                    </div>
                    <div class="header-right" style="margin-top: 42px">

                        <span class="item-price"><?php echo $DB_building->getPriceRange($building_id); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <? if (!$detect->isMobile()) {
            include_once 'property-view_map.php';
        } ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="detail-media">
                    <div id="carousel-post-card-module" class="houzez-module carousel-post-card-module no-padding">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="module-title-nav clearfix">
                                        <div class="module-nav">
                                            <button class="btn btn-sm btn-crl-post-card-prev"><i
                                                        class="fa fa-arrow-left"></i></button>
                                            <button class="btn btn-sm btn-crl-post-card-next"><i
                                                        class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div id="showImagesAfterLoad">
                                        <div class="row grid-row">
                                            <div id="carousel-post-card" class="gallery carousel-post-card">
                                                <?php
                                                if ($building_row['pictures']) {
                                                    $pic_arr = explode("|", $building_row['pictures']);
                                                    foreach ($pic_arr as $pic) {
                                                        ?>
                                                        <div class="item col-sm-12">
                                                            <div class="item-wrap">
                                                                <div class="post-card-item" style="display:none;">
                                                                    <!-- <div class="figure-block" style="height: 0; width: 100%; padding-bottom: 60%; overflow: hidden"> -->
                                                                    <div class="figure-block">
                                                                        <figure class="item-thumb"
                                                                                style="width: 100%">
                                                                            <a href="<?php echo "admin/files/$pic"; ?>"
                                                                               class="hover-effect"
                                                                               data-gal="prettyPhoto[gallery1]">
                                                                                <img
                                                                                        style="display:none;"
                                                                                        src="<?php echo "admin/files/$pic" ?>">
                                                                            </a>

                                                                        </figure>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="item" style="height: 120px">
                                                        <div class="item-wrap" style="height: 120px">
                                                            <div class="post-card-item" style="height: 120px">
                                                                <div class="figure-block" style="height: 120px">
                                                                    <figure class="item-thumb"
                                                                            style="height: 120px">
                                                                        <a href="images/listings/sample_pic.jpg"
                                                                           class="hover-effect"
                                                                           data-gal="prettyPhoto[gallery1]">
                                                                            <img src="images/listings/sample_pic.jpg">
                                                                        </a>
                                                                    </figure>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--   </div>-->
        <? if (!$detect->isMobile()) { ?>
            <div class="row HideOnMobile">
                <div class="col-sm-12">
                    <div class="container bg-availablity ">
                        <div class="col-sm-6 col-md-6 col-lg-3 bg1 availablity">
                            <div class="row">
                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                    <div class="park_icon"><i class="fa fa-dollar"></i></div>
                                </div>
                                <div class="col-sm-9 col-xs-9 col-md-9 col-lg-9">
                                    <div class="park_text"><?php echo $DB_snapshot->echot("Parking"); ?>
                                        <strong>
                                            <?php if ($DB_building->parkingAvailability($building_id)) {
                                                echo $DB_snapshot->echot("Available");
                                            } else {
                                                echo $DB_snapshot->echot("Unavailable");
                                            } ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 bg2 availablity">
                            <div class="row">
                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                    <div class="park_icon"><i class="fa fa-dollar"></i></div>
                                </div>
                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                    <div class="park_text"> <?php echo $DB_snapshot->echot("Storage"); ?>
                                        <strong>
                                            <?php if ($DB_building->storageAvailability($building_id)) {
                                                echo $DB_snapshot->echot("Available");
                                            } else {
                                                echo $DB_snapshot->echot("Unavailable");
                                            } ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 bg3 availablity">
                            <div class="row">
                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                    <div class="park_icon"><i class="fa fa-dollar"></i></div>
                                </div>
                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                    <div class="park_text"><?php echo $DB_snapshot->echot("Online Payment"); ?>
                                        <strong><?php echo $DB_snapshot->echot("Available"); ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 bg4 availablity">
                            <div class="row">
                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                    <div class="park_icon"><i class="fa fa-dollar"></i></div>
                                </div>
                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                    <div class="park_text">
                                        <strong>
                                            <?php echo $DB_snapshot->echot("RENT RANGE"); ?>
                                            <?php echo $DB_building->getPriceRange($building_id); ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } //<? if (! $detect->isMobile() ) {?>
    </div>
</section>
<!--end detail top-->

<!--start section page body-->
<div id="section-body">

    <!--start detail content-->
    <section class="section-detail-content">
        <div class="container">
            <div class="row">

                <?php
                if ($DB_apt->isBuildingShowed($building_id)) { ?>

                    <!-- Left side section start-->
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 table_tab">
                        <div id="facilities" class="detail-features detail-block target-block">
                            <div class="detail-title">
                                <h2 class="title-left"><?php echo $DB_snapshot->echot("Available Units List"); ?></h2>
                            </div>
                            <div style="overflow-y: auto;max-height: 400px;">
                                <table class="sortable">
                                    <thead>
                                    <tr>
                                        <th><?php echo $DB_snapshot->echot("Unit"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Rent"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Bed"); ?></th>
                                        <th class="availableOnTh"><?php echo $DB_snapshot->echot("Available On"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("Size"); ?></th>
                                        <th><?php echo $DB_snapshot->echot("WC"); ?></th>

                                        <!--                          <th>Available until</th>-->
                                    </tr>
                                    </thead>
                                    <tbody class="houzez-tabs">
                                    <?php
                                    $default = '0';
                                    $company_id = $_SESSION['company_id'];
                                    $apt_rows = $DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id);
                                   // die(print_r($apt_rows));
                                    // Iterate over all the apartments in the building and check if they have to be shown to user
                                    // If the Force Listing is Set to true, show the apartment
                                    //echo(var_dump($DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id)));
                                    foreach ($apt_rows as $apt_row) {
                                        //  echo $apt_row['apartment_id']."<br>";
                                        if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
//echo $apt_row['apartment_id']."<br>";
                                            $gapBetweenLeases = false;

                                            $furnishedDislayText = array(
                                                0 => "Not Furnished",
                                                1 => "Fully Furnished",
                                                2 => "Partially Furnished"
                                            );

                                            $is_apt_furnished = $apt_row["furnished"];
                                            $furnishedDisplay = "";
                                            if ($is_apt_furnished != 0) {
                                                $furnishedDisplay = '<i  data-toggle="tooltip" title="' . $furnishedDislayText[$is_apt_furnished] . '" style="color:black;" class="fas fa-couch"></i>';
                                            }
                                            //       echo "<br>".$apt_row['apartment_id']."<br>";
                                            // Checking if the apartment is under repair
                                            if (!$apt_row["front_force_list"]) {
                                                if (in_array($apt_row['renovation_status'], array(2, 3))) {
                                                    continue;
                                                }
                                            }

                                            //$apartmentAvailability = "N/A";
                                            if ($apt_row['apartment_status'] == '5') {
                                                $apartmentAvailability = $DB_snapshot->echot("Available Now!");
                                            } else {
                                                $apartmentAvailability = $apt_row['available_date'];
                                            }
                                            /*
                                            if ($apt_row['apartment_status'] == '5') {
                                                $apartmentAvailability = $DB_snapshot->echot("Available Now!");
                                            } else {
                                                $apartmentAvailability = $apt_row['available_date'];

                                                $leaseInfos = $DB_lease->getLeaseInfoByAptId($apt_row['apartment_id']);
                                                if (count($leaseInfos) > 1) {
                                                    foreach ($leaseInfos as $aptLease) {
                                                        if (!in_array(intval($aptLease["lease_status_id"]), array(1, 7, 8, 9, 10))) {
                                                            continue;
                                                        }
                                                        $apartmentAvailabilityDate = new DateTime($aptLease["move_out_date"]);
                                                        $apartmentAvailabilityDate = $apartmentAvailabilityDate->modify('+1 day');
                                                        $apartmentAvailability = $apartmentAvailabilityDate->format('Y-m-d');
                                                    }
                                                }

                                                if (count($leaseInfos) == 1) {
                                                    if (in_array(intval($leaseInfos["lease_status_id"]), array(1, 7, 8, 9, 10))) {
                                                        $apartmentAvailabilityDate = new DateTime($leaseInfos["move_out_date"]);
                                                        $apartmentAvailabilityDate = $apartmentAvailabilityDate->modify('+1 day');
                                                        $apartmentAvailability = $apartmentAvailabilityDate->format('Y-m-d');
                                                    }
                                                }

                                     };

*/
                                            if (!$apt_row["front_force_list"]) {
                                                if ($DB_apt->isUnitShowed($apt_row['apartment_id']) || $gapBetweenLeases) { ?>
                                                    <tr id="<?php echo $apt_row['apartment_id']; ?>" <?php if ($default == '0') {
                                                        echo 'class="active"';
                                                        $default = '1';
                                                    } ?> ><!--unit_number-->
                                                        <td><?php echo $apt_row['unit_number']; ?><?php echo $furnishedDisplay; ?> </td>
                                                        <td><?php echo number_format($apt_row['monthly_price']); ?></td>
                                                        <td><?php echo $apt_row['bedrooms']; ?></td>
                                                        <td><?php echo $apartmentAvailability; ?></td>
                                                        <td><?php echo number_format($apt_row['area']); ?></td>
                                                        <td><?php echo $apt_row['bath_rooms']; ?></td>

                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr id="<?php echo $apt_row['apartment_id']; ?>" <?php if ($default == '0') { ///unit_number
                                                    echo 'class="active"';
                                                    $default = '1';
                                                } ?>>
                                                    <td><?php echo $apt_row['unit_number']; ?></td>
                                                    <td><?php echo number_format($apt_row['monthly_price']); ?></td>
                                                    <td><?php echo $apt_row['bedrooms']; ?></td>
                                                    <td><?php echo $apartmentAvailability; ?></td>
                                                    <td><?php echo number_format($apt_row['area']); ?></td>
                                                    <td><?php echo $apt_row['bath_rooms']; ?></td>

                                                </tr>
                                                <?php
                                            }
                                        }// if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <? if (!$detect->isMobile()) {
                                include_once 'property-view_facility.php';
                            } ?>



                            <? if (!$detect->isMobile()) { ?>
                                <div class="detail-title HideOnMobile">
                                    <h2 class="title-left"><?php echo $DB_snapshot->echot("Building Description"); ?></h2>
                                </div>
                                <div class="HideOnMobile">
                                    <p><?php echo $DB_snapshot->echot($DB_building->getBdInfo($building_id)['public_comments']); ?></p>
                                </div>
                            <? } //if (! $detect->isMobile() ) {?>
                        </div>

                    </div>
                    <!-- Left side section end-->

                    <!-- Right side section start-->
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <div class="houzez-taber-body no_marpad_top">
                            <div class="tab-content">
                                <?php
                                $default = '0';
                                //foreach ($apt_rows = $DB_apt->getAptInfoInBuilding($building_id) as $apt_row) {
                                foreach ($apt_rows as $apt_row) {
                                    if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
                                        $apt_id = $apt_row['apartment_id'];

                                        // Checking if the apartment is under repair
                                        if (!$apt_row["front_force_list"]) {
                                            if (in_array($apt_row['renovation_status'], array(2, 3))) {
                                                continue;
                                            }
                                        }


                                        ?>
                                        <div id="<?php echo "tabcontent_" . $apt_row["apartment_id"] ?>"
                                             class="tab-pane fade <?php if ($default == '0') {
                                                 echo "active in";
                                                 $default = '1';
                                             } ?>">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="detail-bar">

                                                        <!--Unit Details section start-->
                                                        <div class="detail-list detail-block target-block">
                                                            <div class="detail-title">
                                                                <h2 class="title-left"><?php echo $DB_snapshot->echot("Unit Details"); ?></h2>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="prop_details">
                                                                    <div class="col-lg-6">
                                                                        <strong><?php echo $DB_snapshot->echot("Unit Number"); ?></strong>
                                                                        <div class="col-lg-12">
                                                                            <span><?php echo $DB_apt->getUnitNumber($apt_id); ?></span>

                                                                        </div>
                                                                    </div>
                                                                    <? if ($detect->isMobile()) {
                                                                        include 'property-view_unit_pics.php';
                                                                    } ?>
                                                                    <div class="col-lg-6">
                              <span>
                                <div class="btn"
                                     style="padding-left: 0px; margin-bottom: 10px; width: 100%; text-align: center">
                                  <a href="book-event.php?building_id=<?php echo $building_id; ?>&apt_id=<?php echo $apt_id; ?>">
                                    <button type="submit"
                                            class="btn btn-primary"><?php echo $DB_snapshot->echot("Visit Appointment"); ?></button>
                                  </a>
                                    <? if ($detect->isMobile()) {
                                        include 'property-view_question.php';
                                        echo "<br><br>";
                                    } ?>
                                    <? if ($detect->isMobile()) {
                                        include 'property-view_floorplan.php';
                                    } ?>
                                </div>
                              </span>
                                                                    </div>

                                                                    <div class="col-lg-6">
                                                                        <strong><?php echo $DB_snapshot->echot("Monthly rent"); ?></strong>
                                                                        <span><?php echo $DB_apt->getMonthlyPrice($apt_id, true) . "/" . $DB_snapshot->echot("Month"); ?>
                                                                            <!--em>(include water & heat)</em--> </span>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <strong><?php echo $DB_snapshot->echot("Size") ?></strong>
                                                                        <span> <?php echo $DB_apt->getSize($apt_id); ?></span>
                                                                    </div>

                                                                    <div class="col-lg-6">
                                                                        <strong><?php echo $DB_snapshot->echot("Floor"); ?></strong>
                                                                        <span>
                             <?php
                             $floorName = $DB_apt->getFloorInfo($apt_id);
                             echo str_replace("Floor", $DB_snapshot->echot("Floor"), $floorName);
                             ?> </span>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <strong><?php echo $DB_snapshot->echot("Available Date"); ?></strong>
                                                                        <span><?php

                                                                            if ($apt_row['apartment_status'] == '5') {
                                                                                echo $DB_snapshot->echot("Available Now!");
                                                                            } else {
                                                                                echo $apt_row['available_date'];
                                                                            } ?></span>
                                                                    </div>

                                                                    <?php if ($amenities = $DB_apt->getAmenities($apt_id)) { ?>
                                                                        <div class="col-lg-12 facilities_content">
                                                                            <strong><?php echo $DB_snapshot->echot("Amenities"); ?></strong>
                                                                            <p>
                                                                                <?php
                                                                                foreach ($amenities as $amenity) {
                                                                                    ?>
                                                                                    <em><?php echo $DB_snapshot->echot($amenity); ?></em>
                                                                                <?php } ?>
                                                                            </p>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <!--                      </div>-->
                                                            <!--Unit Details section end-->

                                                            <!--Unit Pics section start-->
                                                            <!--                      <div class="additional_info detail-list detail-block target-block">-->

                                                            <? if (!$detect->isMobile()) {
                                                                include 'property-view_unit_pics.php';
                                                            } ?>

                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                <?php
                                                                if ($apt_row['floor_plan']) {
                                                                    ?>
                                                                    <div class="btn"
                                                                         style="text-align: center; width: 100%">
                                                                        <? if (!$detect->isMobile()) {
                                                                            include 'property-view_floorplan.php';
                                                                        } ?>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                          <span>
                            <div class="btn" style="text-align: center;width: 100%;">
<? if (!$detect->isMobile()) {
    include 'property-view_question.php';
} ?>
                            </div>
                          </span>
                                                            </div>

                                                            <div style="clear:both;">&nbsp;</div>


                                                            <div class="detail-title">
                                                                <h2 class="title-left"><?php echo $DB_snapshot->echot("Unit Description"); ?></h2>
                                                            </div>
                                                            <div>
                                                                <p><?php echo $apt_row['public_comments']; ?></p>
                                                            </div>


                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Right side section end-->


                    <?php
                } else {
                    ?>
                    <div style="clear:both;">&nbsp;</div>
                    <div class="houzez-module module-title text-center"><h2>Sorry. There is no units available at this
                            time. Please come back later.</h2></div>
                <?php } ?>
                <div style="clear:both;">&nbsp;</div>
            </div>
            <? if ($detect->isMobile()) {
                include_once 'property-view_facility.php';
            } ?>
            <? if ($detect->isMobile()) {
                include_once 'property-view_map.php';
            } ?>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                <div class="next-prev-block clearfix">
                    <div class="prev-box pull-left">
                        <div class="media">
                            <div class="media-left"><a
                                        href="property-view.php?pass_bd_id=<?php echo $previous_building_id = $DB_building->getPreviousBdId($building_id, $company_id); ?>">
                                    <img src="<?php echo $DB_building->getFeaturePic($previous_building_id) ?>"
                                         class="media-object next-prev-pic" alt="image"> </a></div>
                            <div class="media-body media-middle">
                                <h3 class="media-heading"><a
                                            href="property-view.php?pass_bd_id=<?php echo $previous_building_id; ?>"><i
                                                class="fa fa-angle-left"></i> <?php echo $DB_snapshot->echot("PREVIOUS PROPERTY"); ?>
                                    </a></h3>
                                <h4><?php echo $DB_building->getPreviousBdName($building_id, $company_id); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="next-box pull-right">
                        <div class="media">
                            <div class="media-body media-middle text-right">
                                <h3 class="media-heading">
                                    <a href="property-view.php?pass_bd_id=<?php echo $next_building_id = $DB_building->getNextBdId($building_id, $company_id); ?>">
                                        <?php echo $DB_snapshot->echot("NEXT PROPERTY"); ?> <i
                                                class="fa fa-angle-right"></i></a></h3>
                                <h4><?php echo $DB_building->getNextBdName($building_id, $company_id); ?></h4>
                            </div>
                            <div class="media-right"><a
                                        href="property-view.php?pass_bd_id=<?php echo $next_building_id; ?>"> <img
                                            src="<?php echo $DB_building->getFeaturePic($next_building_id); ?>"
                                            class="media-object next-prev-pic" alt="image"> </a></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


</div>
</section>
<!--end detail content-->

</div>
<!--end section page body-->

<?php include_once("footer.php"); ?>

<script>
    function initMap() {
        var myLatLng = {lat: <?php echo $building_row['latitude']; ?>, lng: <?php echo $building_row['longitude']; ?>};

        var icon = {
            url: 'images/x2-apartment.png',
            scaledSize: new google.maps.Size(50, 70), // scaled size
            origin: new google.maps.Point(0, 0), // origin
            anchor: new google.maps.Point(0, 0) // anchor
        };

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: myLatLng
        });

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: icon
        });
    }
</script>

<!--Start Scripts-->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM1RTWPcXs6sMZEj3hsjVAE6hd3MBX8Ug&callback=initMap"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
<script type="text/javascript" src="js/bootstrap-select.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="js/vegas.min.js"></script>
<script type="text/javascript" src="js/infobox.js"></script>
<!-- <script type="text/javascript" src="js/markerclusterer.js"></script> -->
<script type="text/javascript" src="js/custom.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
      integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

<script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
<script type="text/javascript">
    jQuery(function () {
        jQuery("area[data-gal^='prettyPhoto']").prettyPhoto();
        jQuery(".gallery:first a[data-gal^='prettyPhoto']").prettyPhoto({
            animation_speed: 'normal',
            theme: 'pp_default',
            slideshow: 5000,
            autoplay_slideshow: true
        });
        jQuery(".gallery:gt(0) a[data-gal^='prettyPhoto']").prettyPhoto({
            animation_speed: 'fast',
            slideshow: 10000,
            hideflash: true
        });
    });
</script>

<script>
    jQuery("document").ready(function () {
        jQuery(".figure-block").attr("style", "height: 0; width: 100%; padding-bottom: 60%; overflow: hidden");
        jQuery("img").show();
        jQuery(".post-card-item").show();
    });
</script>

<!--javascript for addthis sharing button-->
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59dcd4e8c39be78d"></script>

</body>
</html>
