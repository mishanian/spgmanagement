function $(element) {
  return document.getElementById(element);
}


var speedTest = {};



speedTest.pics = null;

speedTest.map = null;

speedTest.markerClusterer = null;

speedTest.markers = [];

speedTest.infoWindow = null;







speedTest.init = function() {

  var latlng = new google.maps.LatLng(39.91, 116.38);

  var options = {

    'zoom': 2,

    'center': latlng,

    'mapTypeId': google.maps.MapTypeId.ROADMAP

	

  };

  

  



  speedTest.map = new google.maps.Map($('map'), options);

  speedTest.pics = data.photos;

  

  var mylocation = document.getElementById('houzez-gmap-view-my');

  google.maps.event.addDomListener(mylocation, 'click', speedTest.locate);
  
  
  var fullscreen = document.getElementById('houzez-gmap-full');

  google.maps.event.addDomListener(fullscreen, 'click', speedTest.fullscreen);

//houzez-gmap-full-my

 // var useGmm = document.getElementById('usegmm');

  //google.maps.event.addDomListener(useGmm, 'click', speedTest.change);

  

 // var numMarkers = document.getElementById('nummarkers');

 // google.maps.event.addDomListener(numMarkers, 'change', speedTest.change);



  speedTest.infoWindow = new google.maps.InfoWindow();



  speedTest.showMarkers();

};



speedTest.showMarkers = function() {

  speedTest.markers = [];



  var type = 0;

/*  if ($('usegmm').checked) {

    type = 0;

  }*/



  if (speedTest.markerClusterer) {

    speedTest.markerClusterer.clearMarkers();

  }



  var panel = $('markerlist');

  panel.innerHTML = '';

 // var numMarkers = $('nummarkers').value;

numMarkers=3;

  for (var i = 0; i < numMarkers; i++) {

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

			

    if (titleText === '') {

      titleText = 'No title';

    }



    var item = document.createElement('DIV');

	item.className = 'item-wrap infobox_trigger item-luxury-family-home';

    var title = document.createElement('DIV');

  //  title.href = '#';

    title.className = 'title';

    title.innerHTML = '<div class="property-item table-list">'+

	'<div class="table-cell">'+

	'<div class="figure-block">'+

	'<figure class="item-thumb">'+project_type+

	'<div class="label-wrap label-right hide-on-list">'+status+'</div>'+	

    '<div class="price hide-on-list"><p class="price-start">Start from</p>'+

	'<h3>$350,000</h3>'+

	'<p class="rant">$21,000/mo</p>'+

	'</div>'+

	'<a href="#" class="hover-effect"> <img src="images/listings/properties_01.jpg" alt="thumb"></a>'+

	'<ul class="actions">'+

     '<li> <span title="" data-placement="top" data-toggle="tooltip" data-original-title="Favorite"> <i class="fa fa-heart"></i> </span> </li>'+

                              '<li> <span data-toggle="tooltip" data-placement="top" title="" data-original-title="Photos (12)"> <i class="fa fa-camera"></i> </span> </li>'+

                            '</ul></figure>'+

	'</div></div>'+

'<div class="item-body table-cell">'+

                       ' <div class="body-left table-cell">'+

                        '  <div class="info-row">'+

                         '   <div class="label-wrap hide-on-grid">'+status+'</div>'+

						 '<h2 class="property-title"><a href="property-view.html">'+titleText+'</a></h2>'+

                            '<h4 class="property-location">'+address+'</h4>'+

                          '</div>'+

                          '<div class="info-row amenities hide-on-grid">'+

                          '  <p> <span>Beds: '+bedrooms+'</span> <span>Baths: '+bathrooms+'</span><br> <span>Sqft: '+square_feet+'</span> </p>'+

                          '  <p>'+type+'</p>'+

                          '</div>'+

                        '</div>'+

                        '<div class="body-right table-cell hidden-gird-cell">'+

                        '  <div class="info-row price">'+

                        '    <p class="price-start">Start from</p>'+

                        '    <h3>'+price+'</h3>'+

                        '    <p class="rant">'+monthly_price+'</p>'+

                        '  </div>'+

                        '  <div class="info-row phone text-right"> <a href="#" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>'+

                        '    <p><a href="#">'+phone+'</a></p>'+

                        '  </div>'+

                        '</div>'+

                        '<div class="table-list full-width hide-on-list">'+

                        '  <div class="cell">'+

                        '    <div class="info-row amenities">'+

                        '      <p> <span>Beds: '+bedrooms+'</span> <span>Baths: '+bathrooms+'</span> <span>Sqft: 1,965</span> </p>'+

                        '      <p>'+type+'</p>'+

                        '    </div>'+

                        '  </div>'+

                        '  <div class="cell">'+

                         '   <div class="phone"> <a href="#" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>'+

                         '     <p><a href="#">'+phone+'</a></p>'+

                         '   </div>'+

                         ' </div>'+

                        '</div>'+

                      '</div></div>';

	

	//panel.addClass('item-wrap infobox_trigger item-luxury-family-home');

	

    item.appendChild(title);

    panel.appendChild(item);





    var latLng = new google.maps.LatLng(speedTest.pics[i].latitude,speedTest.pics[i].longitude);



    var imageUrl = 'images/x1-single-family-home.png';

    var markerImage = new google.maps.MarkerImage(imageUrl,new google.maps.Size(42, 53));



    var marker = new google.maps.Marker({

      'position': latLng,

      'icon': markerImage,

	  

    });

	

	



    var fn = speedTest.markerClickFunction(speedTest.pics[i], latLng);

    google.maps.event.addListener(marker, 'click', fn);

    google.maps.event.addDomListener(title, 'mouseover', fn);

    speedTest.markers.push(marker);

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







                           var infoHtml = '<div class="infoBox">'+

						   '<div class="property-item item-grid map-info-box">'+

						   '<div class="figure-block">'+

                            '<figure class="item-thumb">'+

                            '<a href="'+url+'" tabindex="0"><img src="'+ fileurl+'" width="385" height="158" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image"></a>'+

                            

                            '</figure>'+

                            '</div>' +

                            '<div class="item-body">' +

                            '<div class="body-left">' +

                            '<div class="info-row">' +

                            '<h2 class="property-title"><a href="'+url+'">'+title+'</a></h2>' +

                            //'<h4 class="property-location">'+properties[i].full_address+'</h4>' +

							'<p class="map_list_spec"><span>Bed: '+bedrooms+'</span>&nbsp;|&nbsp;<span>Baths: '+bathrooms+'</span>&nbsp;|&nbsp;<span>Sq Ft: '+square_feet+'</span></p>'+

                            '</div>' +

                            

                            '</div>' +

                            '</div>'+

							'</div>'+

							'</div>';

							

  /*  var infoHtml = '<div class="info"><h3>' + title +

      '</h3><div class="info-body">' +

      '<a href="' + url + '" target="_blank"><img src="' +

      fileurl + '" class="info-img"/></a></div>' +

      '<a href="http://www.panoramio.com/" target="_blank">' +

      '<img src="http://maps.google.com/intl/en_ALL/mapfiles/' +

      'iw_panoramio.png"/></a><br/>' +

      '<a href="' + pic.owner_url + '" target="_blank">' + pic.owner_name +

      '</a></div></div></div>';

*/

    speedTest.infoWindow.setContent(infoHtml);

    speedTest.infoWindow.setPosition(latlng);

    speedTest.infoWindow.open(speedTest.map);

  };

};



speedTest.fullscreen = function() {

 
//alert("rav");
google.maps.event.trigger(speedTest.map, "resize");

};


speedTest.clear = function() {

  //$('timetaken').innerHTML = 'cleaning...';

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

        navigator.geolocation.getCurrentPosition(speedTest.showPosition);

    } else {

        x = "Geolocation is not supported by this browser.";
		

    }

	

 // speedTest.clear();

 // speedTest.showMarkers();



};



speedTest.showPosition = function(a) {

	

	var latLngs = new google.maps.LatLng(a.coords.latitude,a.coords.longitude);

	var infoHtmls="<div class='current_location'>Current Location</div>";

	

	

	 var options = {

    'zoom': 10,

    'center': latLngs,

    'mapTypeId': google.maps.MapTypeId.ROADMAP

  };



  speedTest.map = new google.maps.Map($('map'), options);

  

	 

	

	speedTest.infoWindow.setContent(infoHtmls);

    speedTest.infoWindow.setPosition(latLngs);

    speedTest.infoWindow.open(speedTest.map);

	

	//alert(a);

	// x = "Latitude: " + a.coords.latitude +     "<br>Longitude: " + a.coords.longitude; 

	//alert(x);

	

	

};



speedTest.time = function() {

 // $('timetaken').innerHTML = 'timing...';

  var start = new Date();

 /* if ($('usegmm').checked) {

    speedTest.markerClusterer = new MarkerClusterer(speedTest.map, speedTest.markers, {imagePath: 'images/m'});

  } else {

    for (var i = 0, marker; marker = speedTest.markers[i]; i++) {

      marker.setMap(speedTest.map);

    }

  }*/

  speedTest.markerClusterer = new MarkerClusterer(speedTest.map, speedTest.markers, {imagePath: 'images/m'});



  var end = new Date();

 // $('timetaken').innerHTML = end - start;

};