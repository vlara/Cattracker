var map;
var clickMarker;
function initialize() {
    //map
    clickMarker = new google.maps.Marker();
    var UCM = new google.maps.LatLng(37.366572, -120.424876);
    var myOptions = {
        zoom: 13,
        center: UCM,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    google.maps.event.addListener(map, 'click', function(event) {
        clickMarker.setVisible(false);
        placeMarker(event.latLng);
    });
    google.maps.event.addListener(clickMarker, 'drag', function(latLng){
        $('#lat')[0].value = latLng.lat();
        $('#lng')[0].value = latLng.lng();
    });
			
    function placeMarker(location){
        $('#lat')[0].value = location.lat();
        $('#lng')[0].value = location.lng();
        clickMarker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: true
        });
        google.maps.event.addListener(clickMarker, 'drag', function(event){
            $('#lat')[0].value = event.latLng.lat();
            $('#lng')[0].value = event.latLng.lng();
        });
        clickMarker.setVisible(true);
         if (typeof marker != "undefined")
            marker.setVisible(false);
    }
}
$(document).ready(function() {
    initialize();
});