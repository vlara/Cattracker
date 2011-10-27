var map;
var infoWindow;
var customIcons = {
    busstop: {
        icon: 'images/busstop.png'
    }
}
var markersArr = [];
var allLocations = [];
var geocoder;
var queriedP;

function initialize() {
    //map
    //var marker = new google.maps.Marker();
    geocoder = new google.maps.Geocoder();
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

    var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(37.23439050241314, -120.65696218164061),
        new google.maps.LatLng(37.41130805899473, -120.34934499414061))
    var input = document.getElementById('addressSearch');
    var options = {
        bounds: defaultBounds
    };
    
    autocomplete = new google.maps.places.Autocomplete(input, options);
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        clearMarkers();
        var place = autocomplete.getPlace();
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(15);
        }
        //        var image = new google.maps.MarkerImage(
        //            place.icon, new google.maps.Size(71, 71),
        //            new google.maps.Point(0, 0), new google.maps.Point(17, 34),
        //            new google.maps.Size(35, 35));
        //        marker.setIcon(image);
        //        marker.setPosition(place.geometry.location);
        showAddress(place.geometry.location);
    //infowindow.setContent(place.name);
    //infowindow.open(map, queriedP);
        
    });
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
            
            var times = markers[i].getElementsByTagName("Arrival");
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
        markersArr.length = 0;
    }
    if(allLocations) {
        for (i in allLocations) {
            allLocations[i].setVisible(false);
        }
    }
}

function showAddress(location){
    if (typeof queriedP != "undefined"){
        queriedP.setPosition(location);
    } else {
        queriedP = new google.maps.Marker({
            map: map,
            position: location
        });
    }
    getLocations();
}

function calculateDistance() {
    var glatlng1  = new google.maps.LatLng(queriedP.getPosition().lat(), queriedP.getPosition().lng());
    for(x = 0; x < allLocations.length; x++){
        var glatlng2 = new google.maps.LatLng(allLocations[x].getPosition().lat(), allLocations[x].getPosition().lng());
        var meterDistance = google.maps.geometry.spherical.computeDistanceBetween(glatlng1,glatlng2);
        var miledistance = meterDistance * 0.000621371192;
        if(miledistance <= 0.5){
            allLocations[x].setVisible(true);
        }
    }
}

function getLocations() {
    if(allLocations.length == 0)
    {
        downloadUrl("/api/getallmarkers", function(data) {
            var xml = data.responseXML;
            var markers = xml.documentElement.getElementsByTagName("Location");
            for (var i = 0; i < markers.length; i++) {
            
                var times = markers[i].getElementsByTagName("Arrival");
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
                    icon: icon.icon,
                    visible: false
                //shadow: icon.shadow
                });
                allLocations.push(marker);
                bindInfoWindow(marker, map, infoWindow, html);
            }
            calculateDistance();
        });
        
    }else {
        calculateDistance();
    }
}