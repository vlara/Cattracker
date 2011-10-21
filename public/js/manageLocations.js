var marker;
$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#locationsTable tbody").click(function(event) {
        $(oTableLocation.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        //$(event.target.parentNode).addClass('row_selected');
        setupMarker(event);
        $(event.target.parentNode).addClass('row_selected');
        fillLocationForm(oTableLocation);
        
    });
	
    /* Add a click handler for the delete row */
    $('#deleteLoc').click( function() {
        var anSelected = fnGetSelectedLocation( oTableLocation );
        var row = anSelected[0];
        var data = oTableLocation.fnGetData(row);
        oTableLocation.fnDeleteRow( anSelected[0] );
        deleteLocation(data[0]);
    } );
	
    /* Init the table */
    var oTableLocation = $('#locationsTable').dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "sScrollY": "200px",
        "bPaginate": false,
        "aoColumnDefs": [
        {
            "sWidth": "10%", 
            "aTargets": [ -1 ]
        },
        {
            "bSearchable": false, 
            "bVisible": false, 
            "aTargets": [ 0 ]
        }//Removing ID
        ]
    });
} );

//create a marker
function setupMarker(event){
    var LatNode = event.target.parentNode.cells[2];
    var LngNode = event.target.parentNode.cells[3];
    var LatNodeHtml = $(LatNode).html();
    var LngNodeHtml = $(LngNode).html();
    var LatTrimmed = $.trim(LatNodeHtml);
    var LngTrimmed = $.trim(LngNodeHtml);
    if(LatTrimmed.length > 0 || LngTrimmed.length > 0){
        var location =new google.maps.LatLng($(LatNode).html(),$(LngNode).html());
        if (typeof marker != "undefined")
            marker.setVisible(false);
        if (typeof clickMarker != "undefined")
            clickMarker.setVisible(false);
        marker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: true
        });
        google.maps.event.addListener(marker, 'drag', function(event){
            $(LatNode).html(event.latLng.lat());
            $(LngNode).html(event.latLng.lng());
        });
        google.maps.event.addListener(marker, 'dragend', function(event){
          map.panTo(event.latLng);
        });
        marker.setVisible(true);
        clickMarker.setVisible(false);
        map.panTo(location);
    }
}

function fillLocationForm(oTableLocal){
    var ret_arr = fnGetSelectedLocation(oTableLocal);
    var row = ret_arr[0];
    var data = oTableLocal.fnGetData(row);
    $('#LocationID').val(data[0]);
    $('#LocationName').val(data[1]);
    $('#LocationDescription').val(data[2]);
    $('#lat').val(data[3]);
    $('#lng').val(data[4]);
}

/* Get the rows which are currently selected */
function fnGetSelectedLocation( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();
	
    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteLocation(id){
    $('#LocationID').val("");
    $('#LocationName').val("");
    $('#LocationDescription').val("");
    $('#lat').val("");
    $('#lng').val("");
    $.get("/admin/crr", {
        'operation' : 'removeLocation',
        'locationID' : id
    });
}