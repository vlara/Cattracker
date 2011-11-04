var map;
var infoWindow;
var customIcons = {
    busstop: {
        icon: 'images/busstop.png'
    }
}
var nodesNearStart = [];
var nodesNearEnd = [];
var nodeArr =[];
var markersArr = [];
var allLocations = [];
var geocoder;
var queriedP;
var destMarker;
var directionDisplay;
var directionsService = new google.maps.DirectionsService();

function initialize() {
    //map
    //var marker = new google.maps.Marker();
    geocoder = new google.maps.Geocoder();
    directionsDisplay = new google.maps.DirectionsRenderer();
    var UCM = new google.maps.LatLng(37.366572, -120.424876);
    var myOptions = {
        zoom: 13,
        center: UCM,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        navigationControl: true
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    directionsDisplay.setMap(map);
    infoWindow = new google.maps.InfoWindow;

    var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(37.23439050241314, -120.65696218164061),
        new google.maps.LatLng(37.41130805899473, -120.34934499414061))
    var input = document.getElementById('addressSearch');
    var destlocation = document.getElementById('destSearch');
    var options = {
        bounds: defaultBounds
    };
    
    autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete2 = new google.maps.places.Autocomplete(destlocation, options);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        clearMarkers();
        var place = autocomplete.getPlace();
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(15);
        }
        showAddress(place.geometry.location);
    });
    
    google.maps.event.addListener(autocomplete2, 'place_changed', function() {
        //clearMarkers();
        var place = autocomplete2.getPlace();
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(15);
        }
        showDest(place.geometry.location);
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
    downloadUrl("/xml/Line-"+id + ".xml", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("Location");
        for (var i = 0; i < markers.length; i++) {
            
            var times = markers[i].getElementsByTagName("Arrival");
            var timeArr = new Array();
            for(var x = 0; x < times.length; x++) {
                timeArr[x] = new Object();
                timeArr[x].time = times[x].getAttribute("time");
                timeArr[x].line = times[x].getAttribute("line");
            }
            var id = markers[i].getAttribute("id");
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
            google.maps.event.addListener(marker, 'click', function(latLng) {
                calcRoute(this.position.Na, this.position.Oa);
            });
            nodeArr[id] = new Object();
            nodeArr[id].name = name;
            nodeArr[id].lat = parseFloat(markers[i].getAttribute("lat"));
            nodeArr[id].lng = parseFloat(markers[i].getAttribute("lng"));
            nodeArr[id].desc = desc;
            nodeArr[id].arrivals = timeArr;
            markersArr[id] = marker;
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
    getLocations(1);
}

function showDest(location){
    if (typeof destMarker != "undefined"){
        destMarker.setPosition(location);
    } else {
        destMarker = new google.maps.Marker({
            map: map,
            position: location
        });
    }
    getLocations(2);
}

function calculateDistance(type) {
    var glatlng1;
    var i = -1;
    if (type == 1)
        glatlng1  = new google.maps.LatLng(queriedP.getPosition().lat(), queriedP.getPosition().lng());
    else        
        glatlng1  = new google.maps.LatLng(destMarker.getPosition().lat(), destMarker.getPosition().lng());
    for(x = 0; x < allLocations.length; x++){
        var glatlng2 = new google.maps.LatLng(allLocations[x].getPosition().lat(), allLocations[x].getPosition().lng());
        var meterDistance = google.maps.geometry.spherical.computeDistanceBetween(glatlng1,glatlng2);
        var miledistance = meterDistance * 0.000621371192;
        if(miledistance <= 0.5){
            i++;
            allLocations[x].setVisible(true);
            if (type == 1){
                nodesNearStart[i] = new Object(nodeArr[x]);
            }
            else
                nodesNearEnd[i] = new Object(nodeArr[x]);
        }
    }
}

function getLocations(type) {
    if(allLocations.length == 0)
    {
        downloadUrl("/xml/Locations.xml", function(data) {
            var xml = data.responseXML;
            var markers = xml.documentElement.getElementsByTagName("Location");
            for (var i = 0; i < markers.length; i++) {
            
                var times = markers[i].getElementsByTagName("Arrival");
                var timeArr = new Array();
                for(var x = 0; x < times.length; x++) {
                    timeArr[x] = new Object();
                    timeArr[x].time = times[x].getAttribute("time");
                    timeArr[x].line = times[x].getAttribute("line");
                }
                var id = markers[i].getAttribute("id");
                var name = markers[i].getAttribute("name");
                var desc = markers[i].getAttribute("desc");
                var point = new google.maps.LatLng(
                    parseFloat(markers[i].getAttribute("lat")),
                    parseFloat(markers[i].getAttribute("lng")));
                var html = "<b>" + name + "</b> <br/>" + desc + "</b> <br/>"; //+timeArr.join(" , ");
                var icon = customIcons["busstop"] || {};
                var marker = new google.maps.Marker({
                    map: map,
                    position: point,
                    icon: icon.icon,
                    visible: false
                //shadow: icon.shadow
                });
                google.maps.event.addListener(marker, 'click', function(latLng) {
                    calcRoute(this.position.Na, this.position.Oa);
                });
                nodeArr[id] = new Object();
                nodeArr[id].name = name;
                nodeArr[id].lat = parseFloat(markers[i].getAttribute("lat"));
                nodeArr[id].lng = parseFloat(markers[i].getAttribute("lng"));
                nodeArr[id].desc = desc;
                nodeArr[id].arrivals = timeArr;
                nodeArr[id].id = id;
                allLocations.push(marker);
                bindInfoWindow(marker, map, infoWindow, html);
            }
            calculateDistance(type);
        });
        
    }else {
        calculateDistance(type);
    }
}

function calcRoute(lat, lng) {
    // your location
    if(typeof queriedP != "undefined"){
        var start  = new google.maps.LatLng(queriedP.getPosition().lat(), queriedP.getPosition().lng());
        var dest = new google.maps.LatLng(lat, lng);
        var request = {
            origin:start,
            destination:dest,
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsDisplay.setMap(map);
        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(map);
            }
            else {
                alert("no directions found status " + status);
            }
        });
    }
}
