var map;
var infoWindow;
var customIcons = {
      busstop: {
        icon: 'images/busstop.png'
      }
}
var markersArr = [];

function initialize() {
    //map
    //var marker = new google.maps.Marker();
    var UCM = new google.maps.LatLng(37.366572, -120.424876);
    var myOptions = {
        zoom: 13,
        center: UCM,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    infoWindow = new google.maps.InfoWindow;
//    google.maps.event.addListener(map, 'click', function(event) {
//        marker.setVisible(false);
//        placeMarker(event.latLng);
//    });
//    google.maps.event.addListener(marker, 'drag', function(latLng){
//        $('#lat')[0].value = latLng.lat();
//        $('#lng')[0].value = latLng.lng();
//    });
//			
//    function placeMarker(location){
//        $('#lat')[0].value = location.lat();
//        $('#lng')[0].value = location.lng();
//        marker = new google.maps.Marker({
//            position: location, 
//            map: map,
//            draggable: true
//        });
//        google.maps.event.addListener(marker, 'drag', function(event){
//            $('#lat')[0].value = event.latLng.lat();
//            $('#lng')[0].value = event.latLng.lng();
//        });
//        marker.setVisible(true);
//    }
}
$(document).ready(function() {
    initialize();
    
    $('#lineSelector').change(function() {
        clearMarkers();
        placeMarkerFromXML($('#lineSelector').val());
    });
});

function placeMarkerFromXML(id){
    downloadUrl("/api/getallmarkersforline/id/"+id, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("Location");
        for (var i = 0; i < markers.length; i++) {
            
            var times = markers[i].getElementsByTagName("Time");
            var timeArr = new Array();
            for(var x = 0; x < times.length; x++) {
                timeArr[x] = times[x].getAttribute("time")
            }
            var name = markers[i].getAttribute("name");
            var desc = markers[i].getAttribute("desc");
            var point = new google.maps.LatLng(
                parseFloat(markers[i].getAttribute("lat")),
                parseFloat(markers[i].getAttribute("lng")));
            var html = "<b>" + name + "</b> <br/>" + desc + "</b> <br/>" +timeArr.join(" , ");
            var icon = customIcons["busstop"] || {};
            var marker = new google.maps.Marker({
                map: map,
                position: point,
                icon: icon.icon
            //shadow: icon.shadow
            });
            markersArr.push(marker);
            bindInfoWindow(marker, map, infoWindow, html);
        }
    });
}

function downloadUrl(url,callback) {
    var request = window.ActiveXObject ?
    new ActiveXObject('Microsoft.XMLHTTP') :
    new XMLHttpRequest;

    request.onreadystatechange = function() {
        if (request.readyState == 4) {
            request.onreadystatechange = doNothing;
            callback(request, request.status);
        }
    };

    request.open('GET', url, true);
    request.send(null);
}

function doNothing() {}

function bindInfoWindow(marker, map, infoWindow, html) {
    google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
    });
}

function clearMarkers() {
    if(markersArr) {
        for (i in markersArr) {
            markersArr[i].setMap(null);
        }
    }
}