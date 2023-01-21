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
include_once('pdo/Class.Snapshot.php');
$DB_snapshot = new Snapshot($DB_con);
include_once('pdo/Class.Province.php');
$DB_province = new Province($DB_con);
include_once('pdo/Class.SizeType.php');
$DB_size = new SizeType($DB_con);
include_once('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con);
include_once('pdo/Class.Search.php');
$DB_search = new Search($DB_con);

?>
<?php include "subdomain_check.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php
            if (isset($_SESSION['company_id'])) {
                $company_id = $_SESSION['company_id'];
            } else {
                $company_id = 9;
            } //SPG default

            include_once("pdo/dbconfig.php");
            echo $DB_company->getWebTitle($company_id);
            ?>
    </title>
    <!--<base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once("links-for-html.php") ?>

</head>

<body>

    <?php
    include_once("header.php");
    ?>

    <div class="header-media">
        <div id="houzez-gmap-main">
            <div id="map"></div>
            <div class="map-next-prev-actions">
                <div id="markerlist" style="display:none;"></div>
            </div>
        </div>
        <div class="search_box_bg">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="search-inner-wrap">
                            <h2 class="text-uppercase"><?php echo $DB_snapshot->echot("Find your ideal home"); ?></h2>
                            <div>
                                <form method="post" action="map-listing-apts.php?search=1">
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <div class="search map-marker input-icon">
                                                    <select class="selectpicker" data-live-search="false"
                                                        title="<?php echo $DB_snapshot->echot("All Provinces"); ?>"
                                                        name="province">
                                                        <?php
                                                        $province_rows = $DB_province->getAllProvinces();
                                                        foreach ($province_rows as $province_row) {
                                                        ?>
                                                        <option value="<?php echo $province_row['id'] ?>">
                                                            <?php echo $DB_snapshot->echot($province_row['name']); ?>
                                                        </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="form-group">
                                                <select class="selectpicker" data-live-search="false"
                                                    title="<?php echo $DB_snapshot->echot("Any Size"); ?>" name="size">
                                                    <?php
                                                    $size_type_rows = $DB_size->getAllSizeType();
                                                    foreach ($size_type_rows as $size_type_row) {
                                                    ?>
                                                    <option value="<?php echo $size_type_row['id'] ?>">
                                                        <?php echo $DB_snapshot->echot($size_type_row['name']); ?>
                                                    </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="form-group">
                                                <select class="selectpicker" data-live-search="false"
                                                    title="<?php echo $DB_snapshot->echot("Any Price"); ?>"
                                                    name="price">
                                                    <option value="500">
                                                        <?php echo $DB_snapshot->echot("$500 or less"); ?> </option>
                                                    <option value="1000">
                                                        <?php echo $DB_snapshot->echot("$501 to $1000"); ?></option>
                                                    <option value="1500">
                                                        <?php echo $DB_snapshot->echot("$1001 to $1500"); ?></option>
                                                    <option value="2000">
                                                        <?php echo $DB_snapshot->echot("$1501 to $2000"); ?></option>
                                                    <option value="2500">
                                                        <?php echo $DB_snapshot->echot("$2001 or above"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="form-group">
                                                <select class="selectpicker" data-live-search="false" title="Options">
                                                    <option><?php echo $DB_snapshot->echot("Pets Allowed"); ?></option>
                                                    <option><?php echo $DB_snapshot->echot("Furnished"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-block"><i
                                                        class="fa fa-search"></i><?php echo $DB_snapshot->echot("Search"); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        var featuredBuildingsForMap = [];
        var infoWindowContentArray = [];
        </script>

        <!--start section page body-->
        <section id="section-body">

            <!--start carousel module-->
            <?php
            $show = 0;
            foreach ($building_rows = $DB_building->getAllBdRowsByCompany($company_id) as $building_row) {
                if ($building_row['featured'] == 1) {
                    $show++;
                }
            }
            if ($show != 0) {
            ?>
            <div class="houzez-module-main">
                <div class="houzez-module carousel-module">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="module-title-nav clearfix">
                                    <div>
                                        <h2 class="text-uppercase">
                                            <?php echo $DB_snapshot->echot("Featured Properties"); ?> </h2>
                                    </div>
                                    <div class="module-nav">
                                        <button class="btn btn-sm btn-crl-pprt-1-prev"><i
                                                class="fa fa-arrow-left"></i></button>
                                        <button class="btn btn-sm btn-crl-pprt-1-next"><i
                                                class="fa fa-arrow-right"></i></button>
                                        <a href="map-listing.php?search=0" class="btn btn-carousel btn-sm">All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row grid-row">
                                    <div class="carousel properties-carousel-grid-1 slide-animated">
                                        <?php
                                            foreach ($building_rows = $DB_building->getAllBdRowsByCompany($company_id) as $building_row) {
                                                if ($building_row['featured'] == 1) {
                                                    $building_id = $building_row['building_id'];
                                            ?>
                                        <div class="item">
                                            <div class="item-wrap">
                                                <div class="property-item item-grid">
                                                    <div class="figure-block">
                                                        <figure class="item-thumb">
                                                            <div class="label-wrap hide-on-list">
                                                                <div class="label-status label label-default">For Rent
                                                                </div>
                                                            </div>
                                                            <span
                                                                class="label-featured label label-success">Featured</span>
                                                            <div class="price hide-on-list">
                                                                <h3><?php echo $DB_building->getPriceRange($building_id, $company_id); ?>
                                                                </h3>
                                                            </div>
                                                            <a href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"
                                                                class="hover-effect">
                                                                <div style="height: 235px"><img
                                                                        src="<?php echo $DB_building->getFeaturePic($building_id); ?>"
                                                                        alt="No Pictures"></div>
                                                            </a>
                                                        </figure>
                                                    </div>
                                                    <div class="item-body">
                                                        <div class="body-left">
                                                            <div class="info-row">
                                                                <h2 class="property-title"><a
                                                                        href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"><?php echo $building_row['building_name']; ?></a>
                                                                </h2>
                                                                <h4 class="property-location">
                                                                    <?php echo $DB_building->getFullAddress($building_id); ?>
                                                                </h4>
                                                                <!-- <div class="rating"> <span class="bottom-ratings"><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span style="width: 70%" class="top-ratings"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span></span> <span class="star-text-right">15 Ratings</span> </div>-->
                                                            </div>
                                                            <div class="table-list full-width info-row">
                                                                <div class="cell">
                                                                    <div class="phone"><a
                                                                            href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"
                                                                            class="btn btn-primary"><?php echo $DB_snapshot->echot("Details "); ?>
                                                                            <i
                                                                                class="fa fa-angle-right fa-right"></i></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                                }
                                            }

                                            // For Loop to get the Map information - if the script code is placed inside the above for loop - it's behaving weird and rendering extra HTML
                                            foreach ($building_rows = $DB_building->getAllBdRowsByCompany($company_id) as $building_row) {
                                                if ($building_row['featured'] == 1) {
                                                ?>
                                        <script>
                                        var buildingArray = ['<?php echo $building_row['building_name']; ?>',
                                            '<?php echo $building_row['latitude']; ?>',
                                            '<?php echo $building_row['longitude']; ?>'
                                        ];
                                        featuredBuildingsForMap.push(buildingArray);

                                        var infoWindowForBuilding = ['<div class="info_content">' +
                                            '<h3> <?php echo $building_row['building_name']; ?> </h3>' +
                                            '<span> <?php echo $building_row['address']; ?> </span>' +
                                            '<p> <a href="property-view.php?pass_bd_id=<?php echo $building_row['building_id']; ?>">View Building </a></p>' +
                                            '</div>'
                                        ];
                                        infoWindowContentArray.push(infoWindowForBuilding);
                                        </script>

                                        <?php
                                                }
                                            }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!--end carousel module-->

            <!--start location module-->
            <div class="houzez-module-main module-white-bg">
                <div class="houzez-module module-title text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <h2 class="text-uppercase"><?php echo $DB_snapshot->echot("Quick Search by Sizes"); ?>
                                </h2>
                                <h3 class="sub-heading">
                                    <?php echo $DB_snapshot->echot("Find apartments with your ideal size"); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="location-modul" class="houzez-module location-module grid">
                    <div class="container">
                        <div class="row">
                            <!-- col-sm-3 changed to col-sm-2 -->
                            <div class="col-sm-2">
                                <div class="location-block">
                                    <a href="map-listing-apts.php?search=3&size=2">
                                        <figure> <img src="images/studio.jpg" alt="size1">
                                            <figcaption class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size2 = $DB_size->getSizeType('1')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('1', $company_id); ?></p>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="location-block">
                                    <a href="map-listing-apts.php?search=3&size=2">
                                        <figure> <img src="images/pic_02.jpg" alt="size2">
                                            <figcaption class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size2 = $DB_size->getSizeType('2')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('2', $company_id); ?></p>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="location-block"> <a href="map-listing-apts.php?search=3&size=3">
                                        <figure> <img src="images/pic_01.jpg" alt="size3">
                                            <div class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size3 = $DB_size->getSizeType('3')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('3', $company_id); ?></p>
                                            </div>
                                        </figure>
                                    </a> </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="location-block"> <a href="map-listing-apts.php?search=3&size=4">
                                        <figure> <img src="images/pic_04.jpg" alt="size4">
                                            <div class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size4 = $DB_size->getSizeType('4')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('4', $company_id); ?></p>
                                            </div>
                                        </figure>
                                    </a> </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="location-block"> <a href="map-listing-apts.php?search=3&size=5">
                                        <figure> <img src="images/pic_03.jpg" alt="size5">
                                            <div class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size5 = $DB_size->getSizeType('5')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('5', $company_id); ?></p>
                                            </div>
                                        </figure>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="location-block"> <a href="map-listing-apts.php?search=3&size=5">
                                        <figure> <img src="images/bedroom6.jpg" alt="sizep">
                                            <div class="location-fig-caption">
                                                <h3 class="heading">
                                                    <?php echo $DB_snapshot->echot($size6 = $DB_size->getSizeType('6')); ?>
                                                </h3>
                                                <p class="sub-heading">
                                                    <?php echo $DB_search->countAptsBySize('6', $company_id); ?></p>
                                            </div>
                                        </figure>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--end location module-->

        </section>
        <!--end section page body-->

        <?php include_once("footer.php"); ?>

        <script>
        function initMap() {
            var map;
            var bounds = new google.maps.LatLngBounds();
            var mapOptions = {
                mapTypeId: 'roadmap'
            };

            // Display a map on the page
            map = new google.maps.Map(document.getElementById("map"), mapOptions);
            map.setTilt(45);

            // Multiple Markers
            var markers = featuredBuildingsForMap;

            // Info Window Content
            var infoWindowContent = infoWindowContentArray;

            // Display multiple markers on a map
            var infoWindow = new google.maps.InfoWindow(),
                marker, i;

            var icon = {
                url: 'images/x2-apartment.png',
                scaledSize: new google.maps.Size(50, 70), // scaled size
                origin: new google.maps.Point(0, 0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            };

            // Loop through our array of markers & place each one on the map
            for (i = 0; i < markers.length; i++) {
                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                bounds.extend(position);
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: markers[i][0],
                    icon: icon
                });

                // Allow each marker to have an info window
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infoWindow.setContent(infoWindowContent[i][0]);
                        infoWindow.open(map, marker);
                    }
                })(marker, i));

                // Automatically center the map fitting all markers on the screen
                map.fitBounds(bounds);
            }

            // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
                this.setZoom(14);
                google.maps.event.removeListener(boundsListener);
            });

        }
        </script>
        <!--Start Scripts-->
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM1RTWPcXs6sMZEj3hsjVAE6hd3MBX8Ug&callback=initMap">
        </script>
        <script type="text/javascript" src="js/modernizr.custom.js"></script>
        <script type="text/javascript" src="js/moment.js"></script>
        <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/owl.carousel.min.js"></script>
        <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
        <script type="text/javascript" src="js/bootstrap-select.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
        <!--<script type="text/javascript" src="js/markerclusterer.js"></script>-->
        <script type="text/javascript" src="js/custom.js"></script>
        <script src="data.json"></script>
        <script src="speed_test_1.js"></script>
        <script src="speed_test.js"></script>
        <script>
        //                 google.maps.event.addDomListener(window, 'load', speedTest.init);
        </script>
        <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery("#box").niceScroll({
                autohidemode: true
            })
        });
        </script>
        <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
</body>

</html>