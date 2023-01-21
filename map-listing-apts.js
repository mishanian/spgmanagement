speedTest.getLatLng = function (address) {
  geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: address }, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      return results[0].geometry.location;
    }
  });
};

speedTest.select1 = 0;
speedTest.select2 = 0;

speedTest.showMarkers = function () {
  speedTest.markers = [];

  var type = 0;

  if (speedTest.markerClusterer) {
    speedTest.markerClusterer.clearMarkers();
  }

  var panel = $el('markerlist');

  panel.innerHTML = '';

  // insert a sort button and a dropdown menu

  panel.innerHTML =
    panel.innerHTML +
    '<div style="margin-left: 12px"> <button class="btn btn-primary" onclick="speedTest.mysort()">Sort by </button> ' +
    '<select id ="cSelect" style="margin-left: 12px; padding: 6px; font-size: 14px; width: 160px; background-color: #fff" >' +
    '<option value="date" selected ="selected" >Available Date</option>' +
    '<option value="price">Price</option>' +
    '<option value="bedrooms">Bedrooms</option>' +
    '<option value="surface">Surface</option>' +
    '<option value="address">Address</option>' +
    '</select>' +
    '<select id="oSelect" style="margin-left: 12px; padding: 6px; font-size: 14px; width: 100px; background-color: #fff" >' +
    '<option value="ascend"  selected = "selected">Ascend</option>' +
    '<option value="decend">Decend</option>' +
    '</select>';
  ('</div>');

  if (speedTest.pics == '') {
    panel.innerHTML =
      '<div style="padding-left: 30px"><h4 class="property-location">Sorry, no properties are found.</h4></div>';
  }

  var i = 0;

  var buildings = [];

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

    var unit_price = speedTest.pics[i].unit_price;

    var unit_number = speedTest.pics[i].unit_number;

    var unit_feature_pic = speedTest.pics[i].unit_feature_pic;

    var available_date = speedTest.pics[i].available_date;

    if (!available_date) {
      available_date = 'Now!';
    }

    if (titleText === '') {
      titleText = 'No title';
    }

    var item = document.createElement('DIV');

    item.className = 'item-wrap infobox_trigger item-luxury-family-home';

    title = document.createElement('DIV');

    //  title.href = '#';

    title.className = 'title';

    title.innerHTML =
      '<div class="property-item table-list">' +
      '<div class="table-cell">' +
      '<div class="figure-block">' +
      '<figure class="item-thumb">' +
      project_type +
      '<div class="label-wrap label-right hide-on-list">' +
      '<span class="label-status label label-default">For Rent</span>' +
      '</div>' +
      '<div class="price hide-on-list">' +
      '<h3>' +
      unit_price +
      '</h3>' +
      '</div>' +
      '<div style="height: 170px;"><a href="property-view.php?pass_bd_id=' +
      building_id +
      '" class="hover-effect"> <img src="' +
      unit_feature_pic +
      '" alt="thumb"></a></div>' +
      '</figure>' +
      '</div></div>' +
      '<div class="item-body table-cell">' +
      '<div class="body-left table-cell">' +
      '<div class="info-row">' +
      '<table class="unit-list" style="height: 170px; margin: 0px;">' +
      '<thead><tr><th style="border-right-style:none; border-top-style:none; border-left-style:none;"><h2 class="property-title" style="margin: 0px"><a style="color: #FFFFFF" href="property-view.php?pass_bd_id=' +
      building_id +
      '">' +
      unit_number +
      '</a></h2></th>' +
      '<th style="border-right-style:none; border-top-style:none; border-left-style:none;"><div class="label-wrap hide-on-grid" style="margin: 0px">' +
      '<div class="info-row price">' +
      '<h3 style="color: #FFFFFF">' +
      unit_price +
      '</h3>' +
      '</div>' +
      '</div></th></tr></thead>' +
      '<div class="info-row amenities hide-on-grid">' +
      '<tr><td>&nbsp;<i class="fa fa-bed"></i>&nbsp;&nbsp;<span>' +
      bedrooms +
      '</span> </td><td>&nbsp;<i class="fa fa-bath"></i>&nbsp;&nbsp;<span>' +
      bathrooms +
      '</span> </td></tr><tr><td>&nbsp;<i class="fa fa-arrows-alt"></i>&nbsp;&nbsp;<span>' +
      square_feet +
      '</span> </td><td>&nbsp;<i class="fa fa-building-o"></i>&nbsp;&nbsp;<span>' +
      type +
      '</span> </td></tr>' +
      '<tr><td colspan="2">&nbsp;<i class="fa fa-home"></i>&nbsp;&nbsp;<span>' +
      titleText +
      '</span></td></tr>' +
      '<tr><td colspan="2">&nbsp;&nbsp;<i class="fa fa-map-marker"></i>&nbsp;&nbsp;<span>' +
      address +
      '</span></td></tr>' +
      '<tr><td colspan="2">&nbsp;&nbsp;<i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>' +
      'Available: ' +
      available_date +
      '</span></td></tr>' +
      '</table>' +
      '</div></div>' +
      '</div>' +
      '</div></div>';

    item.appendChild(title);

    panel.appendChild(item);

    if (buildings.indexOf(building_id) == '-1') {
      var latLng = new google.maps.LatLng(
        speedTest.pics[i].latitude,
        speedTest.pics[i].longitude
      );

      var imageUrl = 'images/x1-single-family-home.png';

      var markerImage = new google.maps.MarkerImage(
        imageUrl,
        new google.maps.Size(42, 53)
      );

      var marker = new google.maps.Marker({
        position: latLng,

        icon: markerImage,
      });

      var fn = speedTest.markerClickFunction(speedTest.pics[i], latLng);

      google.maps.event.addListener(marker, 'click', fn);

      google.maps.event.addDomListener(title, 'mouseover', fn);

      speedTest.markers.push(marker);

      buildings.push(building_id);
    } else {
      //speedTest.markers[0]
      var latLng2 = new google.maps.LatLng(
        speedTest.pics[i].latitude,
        speedTest.pics[i].longitude
      );

      var fn2 = speedTest.markerClickFunction(speedTest.pics[i], latLng2);

      //google.maps.event.addListener(marker, 'click', fn);

      google.maps.event.addDomListener(title, 'mouseover', fn2);
    }

    i++;
  }
  window.setTimeout(speedTest.time, 0);
};

speedTest.markerClickFunction = function (pic, latlng) {
  return function (e) {
    e.cancelBubble = true;

    e.returnValue = false;

    if (e.stopPropagation) {
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

    var infoHtml =
      '<div class="infoBox">' +
      '<div class="property-item item-grid map-info-box">' +
      '<div class="figure-block">' +
      '<figure class="item-thumb" style="padding-left:40px;" >' +
      '<a href="property-view.php?pass_bd_id=' +
      building_id +
      '" tabindex="0">' +
      '<img src="' +
      fileurl +
      '" class="" style="height: 100px">' +
      // '<img src="'+ fileurl+'" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image" style="height: 200px">' +

      '</a>' +
      '</figure>' +
      '</div>' +
      '<div class="item-body">' +
      '<div class="body-left">' +
      '<div class="info-row">' +
      '<h2 class="property-title"><a href="property-view.php?pass_bd_id=' +
      building_id +
      '">' +
      title +
      '</a></h2>' +
      '<p class="map_list_spec"><span>' +
      '<b>Location:</b>' +
      '<br>' +
      address +
      '</span></p>' +
      '</div>' +
      '</div></div></div></div>';

    speedTest.infoWindow.setContent(infoHtml);

    speedTest.infoWindow.setPosition(latlng);

    speedTest.infoWindow.open(speedTest.map);
  };
};

speedTest.fullscreen = function () {
  google.maps.event.trigger(speedTest.map, 'resize');
};

speedTest.clear = function () {
  //$el('timetaken').innerHTML = 'cleaning...';

  for (var i = 0, marker; (marker = speedTest.markers[i]); i++) {
    marker.setMap(null);
  }
};

speedTest.change = function () {
  speedTest.clear();

  speedTest.showMarkers();
};

speedTest.locate = function () {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      var latLngs = new google.maps.LatLng(
        position.coords.latitude,
        position.coords.longitude
      );
      var infoHtmls = "<div class='current_location'>Current Location</div>";
      var options = {
        zoom: 12,
        center: latLngs,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };
      //speedTest.map = new google.maps.Map($el('map'), options);
      speedTest.infoWindow.setPosition(latLngs);
      speedTest.infoWindow.setContent(infoHtmls);
      speedTest.infoWindow.open(speedTest.map);
      speedTest.map.setCenter(latLngs);
      //alert(a);
      // x = "Latitude: " + a.coords.latitude +     "<br>Longitude: " + a.coords.longitude;
      //alert(x);
    });
  } else {
    x = 'Geolocation is not supported by this browser.';
  }
  // speedTest.clear();
  // speedTest.showMarkers();
};

speedTest.showPosition = function (a) {
  var latLngs = new google.maps.LatLng(a.coords.latitude, a.coords.longitude);

  var infoHtmls = "<div class='current_location'>Current Location</div>";

  var options = {
    zoom: 12,

    center: latLngs,

    mapTypeId: google.maps.MapTypeId.ROADMAP,
  };

  speedTest.infoWindow.setContent(infoHtmls);

  speedTest.infoWindow.setPosition(latLngs);

  speedTest.infoWindow.open(speedTest.map);

  speedTest.map.setCenter(latLngs);
};

speedTest.time = function () {
  var start = new Date();

  speedTest.markerClusterer = new MarkerClusterer(
    speedTest.map,
    speedTest.markers,
    { imagePath: 'images/m' }
  );

  var end = new Date();

  // $el('timetaken').innerHTML = end - start;
};

var sort_by = function (field, reverse, primer) {
  var key = primer
    ? function (x) {
        return primer(x[field]);
      }
    : function (x) {
        return x[field];
      };

  reverse = !reverse ? 1 : -1;

  return function (a, b) {
    return (a = key(a)), (b = key(b)), reverse * ((a > b) - (b > a));
  };
};

speedTest.mysort = function () {
  var select = document.getElementById('cSelect');
  var option = select.options[select.selectedIndex].value;

  var orderSelect = document.getElementById('oSelect');
  var order = orderSelect.options[orderSelect.selectedIndex].value;

  switch (option) {
    case 'date':
      //alert(option + "  " + order);
      if (order == 'ascend')
        speedTest.pics.sort(
          sort_by('available_date', false, function (a) {
            if (a == null) return '2016';
            return a.toUpperCase();
          })
        );
      else
        speedTest.pics.sort(
          sort_by('available_date', true, function (a) {
            if (a == null) return '2016';
            return a.toUpperCase();
          })
        );
      break;
    case 'price':
      if (order == 'ascend') {
        speedTest.pics.sort(
          sort_by('unit_price', false, function (a) {
            return parseFloat(a.match(/\d+/));
          })
        );
      } else {
        speedTest.pics.sort(
          sort_by('unit_price', true, function (a) {
            return parseFloat(a.match(/\d+/));
          })
        );
      }
      break;
    case 'bedrooms':
      //alert(option + "  " + order);
      if (order == 'ascend')
        speedTest.pics.sort(sort_by('bedrooms', false, parseInt));
      else speedTest.pics.sort(sort_by('bedrooms', true, parseInt));
      break;
    case 'address':
      //alert(option + "  " + order);
      if (order == 'ascend')
        speedTest.pics.sort(
          sort_by('address', false, function (a) {
            return a.toUpperCase();
          })
        );
      else
        speedTest.pics.sort(
          sort_by('address', true, function (a) {
            return a.toUpperCase();
          })
        );
      break;
    case 'surface':
      //alert(option + "  " + order);
      if (order == 'ascend')
        speedTest.pics.sort(
          sort_by('square_feet', false, function (a) {
            //console.log("price" + a + " "+ parseFloat(a.match(/\d+/)) +"\n");
            return parseFloat(a.match(/\d+/));
          })
        );
      else
        speedTest.pics.sort(
          sort_by('square_feet', true, function (a) {
            //console.log("price" + a + " "+ parseFloat(a.match(/\d+/)) +"\n");
            return parseFloat(a.match(/\d+/));
          })
        );
      break;
    default:
      alert('Default case' + option + '  ' + order);
  }

  speedTest.showMarkers();
};

speedTest.updateSortedList = function () {
  var i = 0;

  var buildings = [];

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

    var unit_price = speedTest.pics[i].unit_price;

    var unit_number = speedTest.pics[i].unit_number;

    var unit_feature_pic = speedTest.pics[i].unit_feature_pic;

    var available_date = speedTest.pics[i].available_date;

    if (!available_date) {
      available_date = 'Now!';
    }

    if (titleText === '') {
      titleText = 'No title';
    }

    var item = document.createElement('DIV');

    item.className = 'item-wrap infobox_trigger item-luxury-family-home';

    title = document.createElement('DIV');

    //  title.href = '#';

    title.className = 'title';

    title.innerHTML =
      '<div class="property-item table-list">' +
      '<div class="table-cell">' +
      '<div class="figure-block">' +
      '<figure class="item-thumb">' +
      project_type +
      '<div class="label-wrap label-right hide-on-list">' +
      '<span class="label-status label label-default">For Rent</span>' +
      '</div>' +
      '<div class="price hide-on-list">' +
      '<h3>' +
      unit_price +
      '</h3>' +
      '</div>' +
      '<div style="height: 170px;"><a href="property-view.php?pass_bd_id=' +
      building_id +
      '" class="hover-effect"> <img src="' +
      unit_feature_pic +
      '" alt="thumb"></a></div>' +
      '</figure>' +
      '</div></div>' +
      '<div class="item-body table-cell">' +
      '<div class="body-left table-cell">' +
      '<div class="info-row">' +
      '<table class="unit-list" style="height: 170px; margin: 0px;">' +
      '<thead><tr><th style="border-right-style:none; border-top-style:none; border-left-style:none;"><h2 class="property-title" style="margin: 0px"><a style="color: #FFFFFF" href="property-view.php?pass_bd_id=' +
      building_id +
      '">' +
      unit_number +
      '</a></h2></th>' +
      '<th style="border-right-style:none; border-top-style:none; border-left-style:none;"><div class="label-wrap hide-on-grid" style="margin: 0px">' +
      '<div class="info-row price">' +
      '<h3 style="color: #FFFFFF">' +
      unit_price +
      '</h3>' +
      '</div>' +
      '</div></th></tr></thead>' +
      '<div class="info-row amenities hide-on-grid">' +
      '<tr><td>&nbsp;<i class="fa fa-bed"></i>&nbsp;&nbsp;<span>' +
      bedrooms +
      '</span> </td><td>&nbsp;<i class="fa fa-bath"></i>&nbsp;&nbsp;<span>' +
      bathrooms +
      '</span> </td></tr><tr><td>&nbsp;<i class="fa fa-arrows-alt"></i>&nbsp;&nbsp;<span>' +
      square_feet +
      '</span> </td><td>&nbsp;<i class="fa fa-building-o"></i>&nbsp;&nbsp;<span>' +
      type +
      '</span> </td></tr>' +
      '<tr><td colspan="2">&nbsp;<i class="fa fa-home"></i>&nbsp;&nbsp;<span>' +
      titleText +
      '</span></td></tr>' +
      '<tr><td colspan="2">&nbsp;&nbsp;<i class="fa fa-map-marker"></i>&nbsp;&nbsp;<span>' +
      address +
      '</span></td></tr>' +
      '<tr><td colspan="2">&nbsp;&nbsp;<i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>' +
      'Available: ' +
      available_date +
      '</span></td></tr>' +
      '</table>' +
      '</div></div>' +
      '</div>' +
      '</div></div>';

    item.appendChild(title);

    panel.appendChild(item);

    if (buildings.indexOf(building_id) == '-1') {
      var latLng = new google.maps.LatLng(
        speedTest.pics[i].latitude,
        speedTest.pics[i].longitude
      );

      var imageUrl = 'images/x1-single-family-home.png';

      var markerImage = new google.maps.MarkerImage(
        imageUrl,
        new google.maps.Size(42, 53)
      );

      var marker = new google.maps.Marker({
        position: latLng,

        icon: markerImage,
      });

      var fn = speedTest.markerClickFunction(speedTest.pics[i], latLng);

      google.maps.event.addListener(marker, 'click', fn);

      google.maps.event.addDomListener(title, 'mouseover', fn);

      speedTest.markers.push(marker);

      buildings.push(building_id);
    } else {
      //speedTest.markers[0]
      var latLng2 = new google.maps.LatLng(
        speedTest.pics[i].latitude,
        speedTest.pics[i].longitude
      );

      var fn2 = speedTest.markerClickFunction(speedTest.pics[i], latLng2);

      //google.maps.event.addListener(marker, 'click', fn);

      google.maps.event.addDomListener(title, 'mouseover', fn2);
    }

    i++;
  }
};
