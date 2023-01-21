speedTest.getLatLng = function (address) {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': address }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            return results[0].geometry.location;
        }
    });
};


speedTest.showMarkers = function() {
    speedTest.markers = [];
    var type = 0;

    if (speedTest.markerClusterer) {
        speedTest.markerClusterer.clearMarkers();
    }

    var panel = $el('markerlist');
    panel.innerHTML = '';
    if (speedTest.pics == ''){
        panel.innerHTML = '<div style="padding-left: 30px"><h4 class="property-location">Sorry, no properties are found.</h4></div>';
    }

    var i = 0;
    var latitudeTop = 90;
    var latitudeBottom = -90;
    var longitudeLeft = -180;
    var longitudeRight = 180;

    for (var each_one in speedTest.pics) {

        var titleText = speedTest.pics[i].photo_title;
        var address = speedTest.pics[i].address;
        var images_count = speedTest.pics[i].images_count;
        var bedrooms = speedTest.pics[i].bedrooms;
        var bathrooms = speedTest.pics[i].bathrooms;
        var square_feet = speedTest.pics[i].square_feet;

        var phone = speedTest.pics[i].phone;
        var type = speedTest.pics[i].type;
        var price = speedTest.pics[i].price;
        var monthly_price = speedTest.pics[i].monthly_price;
        var status = speedTest.pics[i].status;
        var project_type = speedTest.pics[i].project_type;
        var feature_pic = speedTest.pics[i].photo_file_url;
        var building_id = speedTest.pics[i].building_id;
        var facilities = speedTest.pics[i].facilities;

        if (titleText === '') {
            titleText = 'No title';
        }

        var item = document.createElement('DIV');
        item.className = 'item-wrap infobox_trigger item-luxury-family-home';
        title = document.createElement('DIV');

        title.className = 'title';
        title.innerHTML = '<div class="property-item table-list">' +

            '<div class="table-cell">' +

            '<div class="figure-block">' +

            '<figure class="item-thumb">' + project_type +

            '<div class="label-wrap label-right hide-on-list">' + '<span class="label-status label label-default">For Rent</span>' + '</div>' +

            '<div class="price hide-on-list">' +

            '<h3>' + price + '</h3>' +

            '</div>' +

            '<div style="height: 170px;"><a href="property-view.php?pass_bd_id=' + building_id + '" class="hover-effect"> <img src="' + feature_pic + '" alt="thumb"></a></div>' +

            '</figure>' +

            '</div></div>' +

            '<div class="item-body table-cell">' +

            ' <div class="body-left table-cell">' +

            '  <div class="info-row">' +

            '   <div class="label-wrap hide-on-grid">' + '<span class="label-status label label-default">For Rent</span>' +

            '  <div class="info-row price">' +

            '    <h3>' + price + '</h3>' +

            '  </div>' +

            '</div>' +

            '<h2 class="property-title"><a href="property-view.php?pass_bd_id=' + building_id + '">' + titleText + '</a></h2>' +

            '<h4 class="property-location">' + address + '</h4>' +

            '</div>' +

            '<div class="info-row amenities hide-on-grid">' +

            '  <p> <span> ' + facilities +'</span> </p>'+

            '</div>' +

            '  <div class="info-row phone text-right"> <a href="property-view.php?pass_bd_id=' + building_id + '" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>' +

            '    <p><a href="#">' + phone + '</a></p>' +

            '  </div>' +

            '</div>' +

            '<div class="table-list full-width hide-on-list">' +

            '  <div class="cell">' +

            '    <div class="info-row amenities">' +

            '    </div>' +

            '  </div>' +

            '  <div class="cell">' +

            '   <div class="phone"> <a href="property-view.php?pass_bd_id=' + building_id + '" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>' +

            '     <p><a href="#">' + phone + '</a></p>' +

            '   </div>' +

            ' </div>' +

            '</div>' +

            '</div></div>';

        item.appendChild(title);
        panel.appendChild(item);
        var latLng = new google.maps.LatLng(speedTest.pics[i].latitude,speedTest.pics[i].longitude);
        var imageUrl = 'images/x1-single-family-home.png';
        var markerImage = new google.maps.MarkerImage(imageUrl,new google.maps.Size(30, 53));
        var marker = new google.maps.Marker({
            'position': latLng,
            'icon': markerImage
        });

        if (speedTest.pics[i].latitude < latitudeTop){
            latitudeTop = Number(speedTest.pics[i].latitude);
        }
        if (speedTest.pics[i].latitude > latitudeBottom){
            latitudeBottom = Number(speedTest.pics[i].latitude);
        }
        if (speedTest.pics[i].longitude > longitudeLeft){
            longitudeLeft = Number(speedTest.pics[i].longitude);
        }
        if (speedTest.pics[i].longitude < longitudeRight){
            longitudeRight = Number(speedTest.pics[i].longitude);
        }

        var fn = speedTest.markerClickFunction(speedTest.pics[i], latLng);
        google.maps.event.addListener(marker, 'click', fn);
        google.maps.event.addDomListener(title, 'mouseover', fn);
        speedTest.markers.push(marker);
        i++;
    }

    if (i!=0) {
        var latitudeCenter = (latitudeTop + latitudeBottom )/2;
        var longitudeCenter = (longitudeRight + longitudeLeft)/2;
        speedTest.map.setCenter(new google.maps.LatLng(latitudeCenter, longitudeCenter));
    }
    window.setTimeout(speedTest.time, 0);
};

speedTest.markerClickFunction = function(pic, latlng) {
    return function(e) {
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
        }

        var title = pic.photo_title;
        var url = pic.photo_url;
        var fileurl = pic.photo_file_url;
        var address = pic.address;
        var bedrooms = pic.bedrooms;
        var bathrooms = pic.bathrooms;
        var images_count = pic.images_count;
        var prop_meta = '';
        var type = '';
        var square_feet = '';
        var building_id = pic.building_id;

        var infoHtml = '<div class="infoBox">'+

            '<div class="property-item item-grid map-info-box">'+

            '<div class="d">'+

            '<figure class="item-thumb">'+

            // '<a href="property-view.php?pass_bd_id='+ building_id +'" tabindex="0"><img src="'+ fileurl+'" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image" width="150px"><!--style="height: 70px"--></a>'+

            '<a href="property-view.php?pass_bd_id='+ building_id +'" tabindex="0"><img src="'+ fileurl+'" width="150px"><!--style="height: 70px"--></a>'+

            '</figure>'+

            '</div>' +

            '<div class="item-body">' +

            '<div class="body-left">' +

            '<div class="info-row">' +

            '<h2 class="property-title"><a href="property-view.php?pass_bd_id='+ building_id +'">'+title+'</a></h2>' +

            '<p class="map_list_spec"><span>'+ '<b>Address:</b>'+'<br>'+address+'</span></p>'+

            '</div>' +

            '</div></div></div></div>';

        speedTest.infoWindow.setContent(infoHtml);

        speedTest.infoWindow.setPosition(latlng);

        speedTest.infoWindow.open(speedTest.map);

    };

};

speedTest.fullscreen = function() {
    google.maps.event.trigger(speedTest.map, "resize");
};

speedTest.clear = function() {

    //$el('timetaken').innerHTML = 'cleaning...';

    for (var i = 0, marker; marker = speedTest.markers[i]; i++) {

        marker.setMap(null);

    }

};

speedTest.change = function() {
    speedTest.clear();
    speedTest.showMarkers();
};

speedTest.locate = function() {
    if (navigator.geolocation) {

        navigator.geolocation.getCurrentPosition(function(position) {
            var latLngs = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
            var infoHtmls="<div class='current_location'>Current Location</div>";
            var options = {
                'zoom': 12,
                'center': latLngs,
                'mapTypeId': google.maps.MapTypeId.ROADMAP
            };
                //speedTest.map = new google.maps.Map($el('map'), options);
            speedTest.infoWindow.setPosition(latLngs);
            speedTest.infoWindow.setContent(infoHtmls);
            speedTest.infoWindow.open(speedTest.map);
            speedTest.map.setCenter(latLngs);
                //alert(a);
                // x = "Latitude: " + a.coords.latitude +     "<br>Longitude: " + a.coords.longitude;
                //alert(x);
            }
        );
    } else {
        x = "Geolocation is not supported by this browser.";
    }
};

speedTest.showPosition = function(a) {
    var latLngs = new google.maps.LatLng(a.coords.latitude,a.coords.longitude);
    var infoHtmls="<div class='current_location'>Current Location</div>";

    var options = {
        'zoom': 12,
        'center': latLngs,
        'mapTypeId': google.maps.MapTypeId.ROADMAP
    };
    //speedTest.map = new google.maps.Map($el('map'), options);
    speedTest.infoWindow.setContent(infoHtmls);
    speedTest.infoWindow.setPosition(latLngs);
    speedTest.infoWindow.open(speedTest.map);
    speedTest.map.setCenter(latLngs);
};

speedTest.time = function() {
    var start = new Date();
    speedTest.markerClusterer = new MarkerClusterer(speedTest.map, speedTest.markers, {imagePath: 'images/m'});
    var end = new Date();
};
