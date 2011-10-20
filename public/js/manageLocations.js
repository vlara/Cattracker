var marker;
var event2;
$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#locationsTable tbody").click(function(event) {
        $(oTableLocation.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        //$(event.target.parentNode).addClass('row_selected');
        setupMarker(event);
        fillLocationForm(oTableLocation);
        
    });
	
    /* Add a click handler for the delete row */
    $('#deleteLoc').click( function() {
        var anSelected = fnGetSelectedLocation( oTableLocation );
        oTableLocation.fnDeleteRow( anSelected[0] );
    } );
	
    /* Init the table */
    var oTableLocation = $('#locationsTable').dataTable({
                        "bJQueryUI": true,
                        "bProcessing": true,
                        "sScrollY": "200px",
                        "bPaginate": false,
                        "aoColumnDefs": [
                            { "sWidth": "10%", "aTargets": [ -1 ] },
                        {
                        "bSearchable": false, 
                        "bVisible": false, 
                        "aTargets": [ 0 ]
                        }//Removing ID
                        ]
    });
    
    /* Apply the jEditable handlers to the table */
//    $('td', oTableLocation.fnGetNodes()).editable( '/admin/crr', {
//        "callback": function( sValue, y ) {
//            var aPos = oTableLocation.fnGetPosition( this );
//            console.log("svalue = "+sValue);
//            oTableLocation.fnUpdate( sValue, aPos[0], aPos[1] );
//        },
//        "submitdata": function ( value, settings ) {
//            var ret_arr = oTableLocation.fnGetPosition( this );
//            //oTable.fnSettings().aoColumns[ret_arr[1]].sTitle
//            return {  
//                "operation": 'rename' + oTableLocation.fnSettings().aoColumns[ret_arr[1]].sTitle,
//                "row_id": this.parentNode.getAttribute('id'),
//                "column": oTableLocation.fnGetPosition( this )[2]
//            };
//        },
//        "event": "dblclick",
//        "height": "10px"
//    } );
} );

//create a marker
function setupMarker(event){
    event2 = event;
    var LatNode = event.target.parentNode.cells[3];
    var LngNode = event.target.parentNode.cells[4];
    console.log("debug");
    console.log(LatNode);
    console.log(LngNode);
    var location =new google.maps.LatLng($(LatNode).html(),$(LngNode).html());
    if (typeof marker != "undefined")
        marker.setVisible(false);
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
        
        //make a ajax call to save the information
        map.panTo(event.latLng);
    });
    marker.setVisible(true);
    map.panTo(location);
    $(event.target.parentNode).addClass('row_selected');
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
            //deleteLocation(aTrs[i].cells[0].id);
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