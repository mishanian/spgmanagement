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
            include_once("pdo/dbconfig.php");
            $company_id = $_SESSION['company_id'];
            echo $DB_company->getWebTitle($company_id);
            ?></title>
    <!--    <base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once("links-for-html.php") ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>

    <?php
    include_once("header.php");
    include_once("search-bar.php");
    ?>

    <!--start section page body-->
    <section id="section-body">
        <div class="container">
            <div class="page-title breadcrumb-top">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                            <li class="active"><?php echo $DB_snapshot->echot("Contact Us"); ?></li>
                        </ol>
                        <div class="page-title-left">
                            <h2><?php echo $DB_snapshot->echot("Contact Us"); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id="content-area" class="contact-area">
                        <div class="white-block">
                            <div class="row">
                                <div class="col-sm-5 col-xs-12 contact-block-inner">
                                    <form id="contact_form" action="contact-us-email-sending.php" method="post">
                                        <div class="form-group">
                                            <label class="control-label"
                                                for="email"><?php echo $DB_snapshot->echot("Your Email"); ?></label>
                                            <input class="form-control" name="email" id="email">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"
                                                for="phone"><?php echo $DB_snapshot->echot("Your Phone"); ?></label>
                                            <input class="form-control" name="phone" id="phone">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"
                                                for="subject"><?php echo $DB_snapshot->echot("Subject"); ?></label>
                                            <input class="form-control" name="subject" id="subject">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"
                                                for="message"><?php echo $DB_snapshot->echot("Your Message"); ?></label>
                                            <textarea class="form-control" name="message" rows="4"
                                                id="message"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="g-recaptcha"
                                                data-sitekey="6LeSFDEUAAAAAPSBwXLuph-qK5d4uksBUyD6_3un"></div>
                                        </div>
                                        <input type="hidden" name="toEmail"
                                            value="<?php echo $DB_company->getEmail($company_id); ?>">
                                        <input type="hidden" name="toName"
                                            value="<?php echo $DB_company->getName($company_id); ?>">
                                        <button type="submit" class="btn btn-secondary btn-long">Send</button>
                                    </form>
                                </div>
                                <div class="col-sm-7 col-xs-12 contact-block-inner">
                                    <div id="houzez-gmap-main" class="contact_map">
                                        <!--<div class="mapPlaceholder">
                                              <div class="loader-ripple">
                                                <div></div>
                                                <div></div>
                                              </div>
                                            </div>-->
                                        <div id="map"></div>

                                    </div>
                                    <div class="col-sm-12 col-xs-12 contact-block-inner">
                                        <div class="contact-info-block alert alert-info">
                                            <p class="padding-top"><strong>HEAD OFFICE</strong><br>
                                            <p><i class="fa fa-map-marker"></i>
                                                <?php echo $DB_company->getAddress($company_id); ?></p>
                                            <p><i class="fa fa-phone"></i>
                                                <?php echo $DB_company->getPhone($company_id); ?> </p>
                                            <p><i class="fa fa-envelope"></i>
                                                <?php echo $DB_company->getEmail($company_id); ?> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end section page body-->

    <?php include_once("footer.php"); ?>

    <!--Start Scripts-->
    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyyF-y54uDrGJfNZKUmhnu1hGSbGKasqs"></script>
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
    <script type="text/javascript" src="js/infobox.js"></script>
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <!--<script type="text/javascript" src="js/custom.js"></script>-->
    <script type="text/javascript">
    (function($) {
        var theMap;

        function initMap() {
            /* Properties Array */
            var properties = [{
                id: 001,
                title: "Luxury family home",
                lat: "<?php echo $DB_company->getLatitude($company_id); ?>",
                lng: "<?php echo $DB_company->getLongitude($company_id); ?>",
                full_address: "<?php echo $DB_company->getAddress($company_id); ?>",
                phone: "<?php echo $DB_company->getPhone($company_id); ?>",
                email: "<?php echo $DB_company->getEmail($company_id); ?>",
                icon: "images/x2-apartment.png",
                id: 001,
                images_count: 7,
                prop_meta: " ",
                retinaIcon: "images/x2-apartment.png",
                //thumbnail:"<img src='http://placehold.it/385x258' alt='thumb'>",
                title: "<?php echo $DB_company->getName($company_id) ?>",
                type: "",
                url: "individual"
            }];

            var myLatLng = new google.maps.LatLng(properties[0].lat, properties[0].lng);

            var houzezMapOptions = {
                zoom: 20,
                maxZoom: 16,
                streetViewControl: false,
                center: myLatLng,
                disableDefaultUI: false,
                scrollwheel: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scroll: {
                    x: $(window).scrollLeft(),
                    y: $(window).scrollTop()
                }
            };
            var theMap = new google.maps.Map(document.getElementById("map"), houzezMapOptions);

            var markers = new Array();
            var current_marker = 0;
            var visible;

            var offset = $(theMap.getDiv()).offset();
            theMap.panBy(((houzezMapOptions.scroll.x - offset.left) / 3), ((houzezMapOptions.scroll.y - offset
                .top) / 3));
            google.maps.event.addDomListener(window, 'scroll', function() {
                var scrollY = $(window).scrollTop(),
                    scrollX = $(window).scrollLeft(),
                    scroll = theMap.get('scroll');
                if (scroll) {
                    theMap.panBy(-((scroll.x - scrollX) / 3), -((scroll.y - scrollY) / 3));
                }
                theMap.set('scroll', {
                    x: scrollX,
                    y: scrollY
                });
            });

            var mapBounds = new google.maps.LatLngBounds();

            for (i = 0; i < properties.length; i++) {
                var marker_url = properties[i].icon;
                var marker_size = new google.maps.Size(44, 56);
                if (window.devicePixelRatio > 1.5) {
                    if (properties[i].retinaIcon) {
                        marker_url = properties[i].retinaIcon;
                        marker_size = new google.maps.Size(84, 106);
                    }
                }

                var marker_icon = {
                    url: marker_url,
                    size: marker_size,
                    scaledSize: new google.maps.Size(44, 56),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(7, 27)
                };

                // Markers
                markers[i] = new google.maps.Marker({
                    map: theMap,
                    draggable: true,
                    position: new google.maps.LatLng(properties[0].lat, properties[0].lng),
                    //                            icon: marker_icon,
                    title: properties[i].title,
                    //                            animation: google.maps.Animation.DROP,
                    visible: true
                });

                mapBounds.extend(markers[i].getPosition());

                var infoBoxText = document.createElement("div");
                infoBoxText.className = 'property-item item-grid map-info-box';
                infoBoxText.innerHTML =
                    '<div class="figure-block">' +
                    '<figure class="item-thumb">' +
                    // properties[i].is_featured +
                    /*'<div class="price hide-on-list">'+
                     properties[i].price +
                     '</div>'+*/
                    '<a href="' + properties[i].url + '" tabindex="0">' +
                    properties[i].thumbnail +
                    '</a>' +
                    /*'<figcaption class="thumb-caption cap-actions clearfix">'+
                     '<div class="pull-right">'+
                     '<span title="" data-placement="top" data-toggle="tooltip" data-original-title="Photos">'+
                     '<i class="fa fa-camera"></i> <span class="count">('+ properties[i].images_count +')</span>'+
                     '</span>'+
                     '</div>'+
                     '</figcaption>'+*/
                    '</figure>' +
                    '</div>' +
                    '<div class="item-body contact_details">' +
                    '<div class="body-left">' +
                    '<div class="info-row">' +
                    '<h2 class="property-title"><a href="' + properties[i].url + '">' + properties[i].title +
                    '</a></h2>' +
                    '<h3>' + properties[i].full_address + '</h3>' +
                    '<p><i class="fa fa-phone"></i>' + properties[i].phone + '</p>' +
                    '<p><i class="fa fa-envelope"></i>' + properties[i].email + '</p>'
                '</div>' +
                '<div class="table-list full-width info-row">' +
                '<div class="cell">' +
                '<div class="info-row amenities">' +
                properties[i].prop_meta +
                    '<p>' + properties[i].type + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';


                var infoBoxOptions = {
                    content: infoBoxText,
                    disableAutoPan: true,
                    maxWidth: 0,
                    alignBottom: true,
                    pixelOffset: new google.maps.Size(-122, -48),
                    zIndex: null,
                    closeBoxMargin: "0 0 -16px -16px",
                    closeBoxURL: "images/close.png",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false
                };

                //                        var infobox = new InfoBox(infoBoxOptions);

                //                        attachInfoBoxToMarker(theMap, markers[i], infobox);
            }

            //                    if (document.getElementById('listing-mapzoomin')) {
            //                        google.maps.event.addDomListener(document.getElementById('listing-mapzoomin'), 'click', function () {
            //                            var current = parseInt(theMap.getZoom(), 10);
            //                            current++;
            //                            if (current > 20) {
            //                                current = 20;
            //                            }
            //                            theMap.setZoom(current);
            //                        });
            //                    }
            //
            //
            //                    if (document.getElementById('listing-mapzoomout')) {
            //                        google.maps.event.addDomListener(document.getElementById('listing-mapzoomout'), 'click', function () {
            //                            var current = parseInt(theMap.getZoom(), 10);
            //                            current--;
            //                            if (current < 0) {
            //                                current = 0;
            //                            }
            //                            theMap.setZoom(current);
            //                        });
            //                    }

            // Marker Clusters
            //if( googlemap_pin_cluster != 'no' ) {
            var markerClustererOptions = {
                ignoreHidden: true,
                maxZoom: parseInt(10),
                styles: [{
                    textColor: '#ffffff',
                    url: 'images/cluster-icon.png',
                    height: 48,
                    width: 48
                }]
            };

            //                    var markerClusterer = new MarkerClusterer(theMap, markers, markerClustererOptions);
            //}

            //                    theMap.fitBounds(mapBounds);

            function attachInfoBoxToMarker(map, marker, infoBox) {
                marker.addListener('click', function() {
                    var scale = Math.pow(2, map.getZoom());
                    var offsety = ((100 / scale) || 0);
                    var projection = map.getProjection();
                    var markerPosition = marker.getPosition();
                    var markerScreenPosition = projection.fromLatLngToPoint(markerPosition);
                    var pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x,
                        markerScreenPosition.y - offsety);
                    var aboveMarkerLatLng = projection.fromPointToLatLng(pointHalfScreenAbove);
                    map.setCenter(aboveMarkerLatLng);
                    infoBox.close();
                    infoBox.open(map, marker);
                });
            }

            jQuery('#houzez-gmap-next').click(function() {
                current_marker++;
                if (current_marker > markers.length) {
                    current_marker = 1;
                }
                while (markers[current_marker - 1].visible === false) {
                    current_marker++;
                    if (current_marker > markers.length) {
                        current_marker = 1;
                    }
                }
                if (theMap.getZoom() < 15) {
                    theMap.setZoom(15);
                }
                google.maps.event.trigger(markers[current_marker - 1], 'click');
            });

            jQuery('#houzez-gmap-prev').click(function() {
                current_marker--;
                if (current_marker < 1) {
                    current_marker = markers.length;
                }
                while (markers[current_marker - 1].visible === false) {
                    current_marker--;
                    if (current_marker > markers.length) {
                        current_marker = 1;
                    }
                }
                if (theMap.getZoom() < 15) {
                    theMap.setZoom(15);
                }
                google.maps.event.trigger(markers[current_marker - 1], 'click');
            });

            jQuery('#houzez-gmap-full').click(function() {
                //google.maps.event.trigger(theMap, 'resize');
                if ($(this).hasClass('active') == true) {
                    //alert('has');
                    google.maps.event.trigger(theMap, 'resize');
                    theMap.setOptions({
                        draggable: true,
                    });
                } else {
                    //alert('not has');
                    google.maps.event.trigger(theMap, 'resize');
                    theMap.setOptions({
                        draggable: false,
                    });
                }

            });


            fave_change_map_type = function(map_type) {

                if (map_type === 'roadmap') {
                    theMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                } else if (map_type === 'satellite') {
                    theMap.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                } else if (map_type === 'hybrid') {
                    theMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
                } else if (map_type === 'terrain') {
                    theMap.setMapTypeId(google.maps.MapTypeId.TERRAIN);
                }
                return false;
            };


            // Create the search box and link it to the UI element.
            //var input = document.getElementById('google-map-search');
            //var searchBox = new google.maps.places.SearchBox(input);
            //theMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            /*theMap.addListener('bounds_changed', function() {
             searchBox.setBounds(theMap.getBounds());
             });*/

            //var markers_location = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            /* searchBox.addListener('places_changed', function() {
             var places = searchBox.getPlaces();

             if (places.length == 0) {
             return;
             }

             // Clear out the old markers.
             markers_location.forEach(function(marker) {
             marker.setMap(null);
             });
             markers_location = [];

             // For each place, get the icon, name and location.
             var bounds = new google.maps.LatLngBounds();
             places.forEach(function(place) {
             var icon = {
             url: place.icon,
             size: new google.maps.Size(71, 71),
             origin: new google.maps.Point(0, 0),
             anchor: new google.maps.Point(17, 34),
             scaledSize: new google.maps.Size(25, 25)
             };

             // Create a marker for each place.
             markers_location.push(new google.maps.Marker({
             map: theMap,
             icon: icon,
             title: place.name,
             position: place.geometry.location
             }));

             if (place.geometry.viewport) {
             // Only geocodes have viewport.
             bounds.union(place.geometry.viewport);
             } else {
             bounds.extend(place.geometry.location);
             }
             });
             theMap.fitBounds(bounds);
             });*/

            google.maps.event.addListenerOnce(theMap, 'tilesloaded', function() {
                $('.mapPlaceholder').hide();
                $("div[title='<?php echo $DB_company->getName($company_id) ?>']").css("opacity", 100);
            });
        }

        initMap();

        google.maps.event.addDomListener(window, 'load', initMap);

    })(jQuery)
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