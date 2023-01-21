function $el(element) {
    return document.getElementById(element);
}


var speedTest = {};



speedTest.pics = null;

speedTest.map = null;

speedTest.markerClusterer = null;

speedTest.markers = [];

speedTest.infoWindow = null;







//speedTest.init = function() {
function initMap() {
    var latlng = new google.maps.LatLng(45.520312,-73.590759);

    var options = {

        'zoom': 12,

        'center': latlng,

        'mapTypeId': google.maps.MapTypeId.ROADMAP,

        'streetViewControl': false,

        }








    speedTest.map = new google.maps.Map($el('map'), options);

    //speedTest.pics = data.photos;




    // var mylocation = document.getElementById('houzez-gmap-view-my');
    //
    // google.maps.event.addDomListener(mylocation, 'click', speedTest.locate);


    // var fullscreen = document.getElementById('houzez-gmap-full');
    //
    // google.maps.event.addDomListener(fullscreen, 'click', speedTest.fullscreen);

//houzez-gmap-full-my

    // var useGmm = document.getElementById('usegmm');

    //google.maps.event.addDomListener(useGmm, 'click', speedTest.change);



    // var numMarkers = document.getElementById('nummarkers');

    // google.maps.event.addDomListener(numMarkers, 'change', speedTest.change);



    speedTest.infoWindow = new google.maps.InfoWindow();



    speedTest.showMarkers();

};