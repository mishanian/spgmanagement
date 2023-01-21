<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (empty($_GET["pass_bd_id"])) {
    die("No building selected");
} else {
    $building_id = $_GET["pass_bd_id"];
}
include_once('pdo/dbconfig.php');
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('pdo/Class.Province.php');
$DB_province = new Province($DB_con);
include_once('pdo/Class.SizeType.php');
$DB_size = new SizeType($DB_con);
include_once('pdo/Class.Facility.php');
$DB_apt = new Facility($DB_con);
include_once('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con);

include "subdomain_check.php";
$company_id = $_SESSION['company_id'];

if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
}
// if (empty($_SESSION['company_id'])) {
//     // $_SESSION['company_id'] = 9;
//     die("No Company defined");
// } else {
//     $company_id = $_SESSION['company_id'];
// }

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
$isMobile = $detect->isMobile();
// $isMobile = false;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php
            echo $DB_company->getWebTitle($company_id); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache NO-STORE">
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />

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
    <?php include_once("links-for-html.php") ?>
    <!-- sortable table -->
    <script src="./js/sorttable.js"></script>
</head>

<body>
    <?php
    include_once("header.php");
    include_once("search-bar.php");
    $building_row = $DB_building->getBdInfo($building_id);
    $default = '0';
    // $company_id = $_SESSION['company_id'];
    $apt_rows = $DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id);
    ?>
    <!--start detail top-->
    <section class="detail-top detail-top-grid">
        <div class="<?= (!$isMobile) ? 'container' : 'container-fluid'; ?>">

            <div class="row">
                <div class="col-sm-12">
                    <div class="header-detail table-list">
                        <div class="header-left">
                            <ol class="breadcrumb">
                                <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                                <li><a href="map-listing.php"><?php echo $DB_snapshot->echot("Property"); ?></a></li>
                                <li class="active">
                                    <?php echo $building_row['building_name']; ?>
                                </li>
                            </ol>
                            <h1> <?php echo $building_row['building_name']; ?>
                                <span class="label-wrap hidden-sm hidden-xs">
                                    <span class="label label-primary">For Rent</span>
                                    <!--span class="label label-danger">Sold</span-->
                                </span>
                            </h1>
                            <address class="property-address">
                                <i class="fa fa-map-marker color_brown"></i>
                                <?php echo $DB_building->getFullAddress($building_id); ?>
                            </address>
                        </div>
                        <div class="header-right">

                            <span
                                class="item-price"><?php echo $DB_building->getPriceRange($building_id, $company_id); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <? if (!$isMobile) {
                include_once 'property-view_video.php';
                include_once 'property-view_map.php';
            } ?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="detail-media">
                        <div id="carousel-post-card-module" class="houzez-module carousel-post-card-module no-padding">
                            <div class="<?= (!$isMobile) ? 'container' : 'container-fluid'; ?>">
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
                                                            $pic = str_replace(" ", "%20", $pic);
                                                    ?>
                                                    <div class="item col-sm-12">
                                                        <div class="item-wrap">
                                                            <div class="post-card-item" style="display:none;">
                                                                <!-- <div class="figure-block" style="height: 0; width: 100%; padding-bottom: 60%; overflow: hidden"> -->
                                                                <div class="figure-block">
                                                                    <figure class="item-thumb" style="width: 100%">
                                                                        <a href="<?php echo "admin/files/building_pictures/$pic"; ?>"
                                                                            class="hover-effect"
                                                                            data-gal="prettyPhoto[gallery1]">
                                                                            <img alt="Building Pic"
                                                                                style="display:none;"
                                                                                src="<?php echo "admin/files/building_pictures/$pic" ?>">
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
                                                                    <figure class="item-thumb" style="height: 120px">
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
            <? if (!$isMobile) { ?>
            <div class="row HideOnMobile">
                <div class="col-sm-12">
                    <div class="<?= (!$isMobile) ? 'container' : 'container-fluid'; ?> bg-availablity ">
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
                                        <strong><?php echo $DB_snapshot->echot("Available"); ?></strong>
                                    </div>
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
                                        <?php echo $DB_snapshot->echot("RENT RANGE"); ?>
                                        <strong>
                                            <?php echo $DB_building->getPriceRange($building_id, $company_id); ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <? } //<? if (! $isMobile ) {
            ?>
        </div>
    </section>
    <!--end detail top-->

    <!--start section page body-->
    <div id="section-body">

        <!--start detail content-->
        <section class="section-detail-content">
            <div class="<?= (!$isMobile) ? 'container' : 'container-fluid'; ?>">
                <div class="row">
                    <?php
                    if ($DB_apt->isBuildingShowed($building_id)) { ?>

                    <? include('property-view-left.php'); ?>
                    <? include('property-view-right.php'); ?>
                    <?php
                    } else {
                    ?>
                    <div style="clear:both;">&nbsp;</div>
                    <div class="houzez-module module-title text-center">
                        <h2>Sorry. There is no units available at this
                            time. Please come back later.</h2>
                    </div>
                    <?php } ?>
                    <div style="clear:both;">&nbsp;</div>
                </div>
                <div class="row" id="hr_end">
                    <div class="col-*-12 text-center">
                        <hr>
                    </div>
                </div>
                <? if ($isMobile) { ?>

                <div class="row" id="hr_end">
                    <div class="col-*-12 text-center">

                        <? include_once 'property-view-local-logic.php'; ?>

                    </div>
                </div>
                <div class="row" id="hr_end">
                    <div class="col-*-12 text-center">
                        <?
                            include_once 'property-view_video.php';
                            include_once 'property-view_map.php';
                            ?>
                    </div>
                </div>
                <? } ?>


                <? include('property-view-next-previous.php'); ?>

            </div>
        </section>
        <!--end detail content-->

    </div>
    <!--end section page body-->

    <?php include_once("footer.php"); ?>

    <script>
    function initMap() {
        var myLatLng = {
            lat: <?= $building_row['latitude']; ?>,
            lng: <?= $building_row['longitude']; ?>
        };

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

    function initLocallogic() {
        var widget = new locallogic.LocalContent('local-content-widget', {
            lat: <?php echo $building_row['latitude'] ?>,
            lng: <?php echo $building_row['longitude'] ?>,
            locale: 'en',
            designId: 'll-2019'
        })
    }
    </script>
    <script async defer
        src="https://cdn.locallogic.co/sdk/?token=920f4541a6f531b16e3663e6183af0c8d9ec6f6f532ed1323c2de0cea67af3de38182ff03a963477&callback=initLocallogic">
    </script>
    </script>

    <!--Start Scripts-->
    <script src="js/jquery.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyyF-y54uDrGJfNZKUmhnu1hGSbGKasqs&callback=initMap&libraries=&v=weekly">
    </script>
    <script src="js/modernizr.custom.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.matchHeight-min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/vegas.min.js"></script>
    <script src="js/infobox.js"></script>
    // <script src="js/markerclusterer.js"></script>
    <script src="js/custom.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <script src="js/jquery.prettyPhoto.js"></script>
    <script>
    jQuery(function() {
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
    jQuery("document").ready(function() {
        jQuery(".figure-block").attr("style", "height: 0; width: 100%; padding-bottom: 60%; overflow: hidden");
        jQuery("img").show();
        jQuery(".post-card-item").show();
        jQuery(".next_row").click(
            function next_row() {
                var current_row = jQuery('#table_unit_list > tbody > tr.active');
                current_row.next().trigger("click");
                jQuery([document.documentElement, document.body]).animate({
                    scrollTop: jQuery("#tab_top").offset().top
                }, 2000);
                var container = jQuery('#table_unit_container');
                container.animate({
                    scrollTop: current_row.offset().top - container.offset().top + container
                        .scrollTop()
                });

            });
        jQuery(".prev_row").click(
            function prev_row() {
                var current_row = jQuery('#table_unit_list > tbody > tr.active');
                current_row.prev().trigger("click");
                jQuery([document.documentElement, document.body]).animate({
                    scrollTop: jQuery("#tab_top").offset().top
                }, 2000);
                var container = jQuery('#table_unit_container');
                container.animate({
                    scrollTop: current_row.offset().top - container.offset().top + container
                        .scrollTop()
                });
            });
        <?php if ($isMobile) { ?>
        jQuery(window).scroll(function() {
            var hT = jQuery('#hr_end').offset().top,
                hH = jQuery('#hr_end').outerHeight(),
                wH = jQuery(window).height(),
                wS = jQuery(this).scrollTop();
            if (wS > (hT + hH - wH)) {
                var current_row = jQuery('#table_unit_list > tbody > tr.active');
                var last_row = jQuery('#table_unit_list > tbody > tr:last');
                if (current_row.html() != last_row.html()) {
                    current_row.next().trigger("click");
                    jQuery([document.documentElement, document.body]).animate({
                        scrollTop: jQuery("#tab_top").offset().top
                    }, 0);
                    var container = jQuery('#table_unit_container');
                    container.animate({
                        scrollTop: current_row.offset().top - container.offset().top + container
                            .scrollTop()
                    });
                }
            }
        });
        <?php } ?>
    });
    </script>

    <!--javascript for addthis sharing button-->
    <!-- Go to www.addthis.com/dashboard to customize your tools -->
    <script src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59dcd4e8c39be78d"></script>

</body>

</html>