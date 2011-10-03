var test;
var oTable;
var marker;
$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#locationsTable tbody").click(function(event) {
        $(oTable.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        setupMarker(event);
    });
	
    /* Add a click handler for the delete row */
    $('#delete').click( function() {
        var anSelected = fnGetSelected( oTable );
        oTable.fnDeleteRow( anSelected[0] );
    } );
	
    /* Init the table */
    oTable = $('#locationsTable').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bProcessing": true
    });
    
    /* Apply the jEditable handlers to the table */
    $('td', oTable.fnGetNodes()).editable( '/admin/crr', {
        "callback": function( sValue, y ) {
            var aPos = oTable.fnGetPosition( this );
            console.log("svalue = "+sValue);
            oTable.fnUpdate( sValue, aPos[0], aPos[1] );
        },
        "submitdata": function ( value, settings ) {
            var ret_arr = oTable.fnGetPosition( this );
            //oTable.fnSettings().aoColumns[ret_arr[1]].sTitle
            return {  
                "operation": 'rename' + oTable.fnSettings().aoColumns[ret_arr[1]].sTitle,
                "row_id": this.parentNode.getAttribute('id'),
                "column": oTable.fnGetPosition( this )[2]
            };
        },
        "event": "dblclick",
        "height": "14px"
    } );
} );

//create a marker
function setupMarker(event){
    var LatNode = event.target.parentNode.cells[2];
    var LngNode = event.target.parentNode.cells[3];
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

/* Get the rows which are currently selected */
function fnGetSelected( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();
	
    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            deleteLocation(aTrs[i].cells[0].id);
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteLocation(id){
    $.get("/admin/crr", {
        'operation' : 'removeLocation',
        'locationID' : id
    });
}