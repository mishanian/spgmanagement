<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('pdo/dbconfig.php');
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('pdo/Class.Province.php');
$DB_province = new Province($DB_con);
include_once('pdo/Class.SizeType.php');
$DB_size = new SizeType($DB_con);
include_once('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('pdo/Class.ListingData.php');
$listing_json_data = new ListingData($DB_con);
include_once('pdo/Class.Search.php');
$DB_search = new Search($DB_con);
include "subdomain_check.php";
if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
}
if (empty($_SESSION['company_id'])) {
    // $_SESSION['company_id'] = 9;
    die("No Company defined");
} else {
    $company_id = $_SESSION['company_id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        <?php
        echo $DB_company->getWebTitle($company_id); ?>
    </title>
    <!--    <base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="js/jquery.js"></script>
    <?php include_once("links-for-html.php"); ?>
</head>

<body>
    <?php
    include_once("header.php");
    include_once("search-bar.php");
    ?>
    <!--start section page body-->
    <section id="section-body" class="houzez-body-half">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6 col-sm-6 col-xs-12 " style="padding: 25px">
                    <!--no-padding-->
                    <div class="map-half fave-screen-fix">
                        <div id="houzez-gmap-main" class="fave-screen-fix">
                            <div id="map"></div>
                            <div class="map-next-prev-actions">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 " style="padding: 25px; padding-left: 0px">
                    <!--no-padding-->
                    <div class="module-half fave-screen-fix box" id="box">
                        <!--start latest listing module-->
                        <div class="white_bg houzez-module">
                            <!--start list tabs-->
                            <div class="list-tabs table-list">
                                <div class="tabs table-cell">
                                    <h2 class="text-uppercase">
                                        <?php echo $DB_snapshot->echot("Available Apartments"); ?></h2>
                                </div>
                                <div class="sort-tab table-cell text-right"> <span class="view-btn btn-list active"><i
                                            class="fa fa-th-list"></i></span> <span class="view-btn btn-grid"><i
                                            class="fa fa-th-large"></i></span> </div>
                            </div>
                            <!--end list tabs-->
                            <div class="property-listing list-view">
                                <div class="row">
                                    <div id="markerlist">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end latest listing module-->
                        <div class="clearfix">&nbsp;</div>
                        <!--start carousel module-->
                        <div class="white_bg houzez-module caption-above carousel-module"
                            style="padding-left:28px; padding-right:28px;">
                            <div class="row no-margin">
                                <div class="module-title-nav clearfix">
                                    <div>
                                        <h2 class="text-uppercase">
                                            <?php echo $DB_snapshot->echot("Featured Properties"); ?></h2>
                                    </div>
                                    <div class="module-nav">
                                        <button class="btn btn-sm btn-crl-2-prev"><i
                                                class="fa fa-arrow-left"></i></button>
                                        <button class="btn btn-sm btn-crl-2-next"><i
                                                class="fa fa-arrow-right"></i></button>
                                    </div>
                                </div>
                                <div id="properties-carousel-2" class="carousel slide-animated">
                                    <?php
                                    include_once("pdo/dbconfig.php");
                                    foreach ($building_rows = $DB_building->getAllBdRowsByCompany($company_id) as $building_row) {
                                        if ($building_row['featured'] == 1) {
                                            $building_id = $building_row['building_id'];
                                    ?>
                                    <div class="item">
                                        <div class="figure-block">
                                            <figure class="item-thumb">
                                                <div class="label-wrap label-left"> <span
                                                        class="label label-success">Featured</span>
                                                    <!--span class="label-status label label-default">For Sale</span-->
                                                </div>
                                                <a href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"
                                                    class="hover-effect">
                                                    <div style="height: 180px"><img
                                                            src="<?php echo $DB_building->getFeaturePic($building_id); ?>"
                                                            alt="Image"></div>
                                                </a>
                                                <figcaption class="detail-above detail">
                                                    <div class="fig-title clearfix">
                                                        <h3 class="pull-left">
                                                            <?php echo $DB_building->getBdName($building_id); ?></h3>
                                                    </div>
                                                    <ul class="list-inline">
                                                        <li class="cap-price">
                                                            <?php echo $DB_building->getPriceRange($building_id, $company_id); ?>
                                                        </li>
                                                    </ul>
                                                </figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <!--end carousel module-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end section page body-->
    <?php include_once "footer.php"; ?>
    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>



    <script type="text/javascript" src="js/modernizr.custom.js"></script>

    <script type="text/javascript" src="js/moment.js"></script>

    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="js/bootstrap.js"></script>

    <script type="text/javascript" src="js/owl.carousel.min.js"></script>

    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>

    <script type="text/javascript" src="js/bootstrap-select.js"></script>

    <script type="text/javascript" src="js/jquery-ui.js"></script>

    <!--<script type="text/javascript" src="js/masonry.pkgd.min.html"></script>-->

    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>

    <script type="text/javascript" src="js/markerclusterer.js"></script>

    <script type="text/javascript" src="js/custom.js"></script>


    <script src="data.json"></script>

    <script src="map-listing-apts-1.js"></script>
    <script>
    speedTest.pics =
        <?php
            $search = $_GET["search"];
            //search: 0 for not search; 1 for search from index; 2 for search from search bar; 3 for search based on sizes (from index bottom section)
            if ($search == 1) {
                echo $listing_json_data->jsonDataUnits($DB_search->searchAptFromIndex($_POST["province"], $_POST["size"], $_POST["price"], $company_id), $company_id);
            } else if ($search == 2) {
                echo $listing_json_data->jsonDataUnits($DB_search->searchAptFromSearchBar($_POST["province"], $_POST["size"], $_POST["price"], $_POST["bedroom"], $_POST["area"], $company_id), $company_id);
            } else if ($search == 3) {
                echo $listing_json_data->jsonDataUnits($DB_search->searchAptBySize($_GET["size"], $company_id), $company_id);
            }
            //    else if($search == 0){
            //        echo $listing_json_data->allJsonDataByCompany($company_id);
            //    }
            ?>.photos;
    </script>
    <script src="map-listing-apts.js"></script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyyF-y54uDrGJfNZKUmhnu1hGSbGKasqs&callback=initMap">
    </script>
    <script>
    //google.maps.event.addDomListener(window, 'load', speedTest.init);
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#box").niceScroll({
            autohidemode: true
        })
    });
    </script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
</body>

</html>