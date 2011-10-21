var data;
$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#arrivalsTable tbody").click(function(event) {
        $(oTableArrival.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
        fillArrivalForm(oTableArrival);
    //setupMarker(event);
    });
	
    /* Add a click handler for the delete row */
    $('#deleteArrival').click( function() {
        var anSelected = fnGetSelectedArrival( oTableArrival );
        var row = anSelected[0];
        var data = oTableArrival.fnGetData(row);
        oTableArrival.fnDeleteRow( anSelected[0] );
        deleteArrival(data[0]);
    } );
	
    /* Init the table */
    var oTableArrival = $('#arrivalsTable').dataTable({
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
    
/* Apply the jEditable handlers to the table */
//    $('td', oTableArrival.fnGetNodes()).editable( '/admin/crr', {
//        "callback": function( sValue, y ) {
//            var aPos = oTableArrival.fnGetPosition( this );
//            console.log("svalue = "+sValue);
//            oTableArrival.fnUpdate( sValue, aPos[0], aPos[1] );
//        },
//        "submitdata": function ( value, settings ) {
//            var ret_arr = oTableArrival.fnGetPosition( this );
//            //oTable.fnSettings().aoColumns[ret_arr[1]].sTitle
//            return {  
//                "operation": 'arrivalRename' + oTableArrival.fnSettings().aoColumns[ret_arr[1]].sTitle,
//                "row_id": this.parentNode.getAttribute('id'),
//                "column": oTableArrival.fnGetPosition( this )[2]
//            };
//        },
//        "event": "dblclick",
//        "height": "10px"
//    } );
} );

//create a marker
//function setupMarker(event){
//    var LatNode = event.target.parentNode.cells[2];
//    var LngNode = event.target.parentNode.cells[3];
//    var location =new google.maps.LatLng($(LatNode).html(),$(LngNode).html());
//    if (typeof marker != "undefined")
//        marker.setVisible(false);
//    marker = new google.maps.Marker({
//        position: location, 
//        map: map,
//        draggable: true
//    });
//    google.maps.event.addListener(marker, 'drag', function(event){
//        $(LatNode).html(event.latLng.lat());
//        $(LngNode).html(event.latLng.lng());
//    });
//    google.maps.event.addListener(marker, 'dragend', function(event){
//        
//        //make a ajax call to save the information
//        map.panTo(event.latLng);
//    });
//    marker.setVisible(true);
//    map.panTo(location);
//    $(event.target.parentNode).addClass('row_selected');
//}

/* Get the rows which are currently selected */
function fnGetSelectedArrival( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();
	
    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            //deleteArrival(aTrs[i].cells[0].id);
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteArrival(id){
    $('#ArrivalID').val("");
    $('#time').val("");
    $('#line').val(0);
    $('#location').val(0);
    $('#sessionID').val(0);
    $.get("/admin/crr", {
        'operation' : 'removeArrival',
        'arrivalID' : id
    });
}
function fillArrivalForm(oTableLocal){
    var ret_arr =fnGetSelectedArrival(oTableLocal);
    var row = ret_arr[0];
    data = oTableLocal.fnGetData(row);
    $('#ArrivalID').val(data[0]);
    $("#location > option").each(function() {
        if(this.text == data[1])
            $('#location').val(this.value);
    });
    $('#time').val(data[2]);
    $('#line > option').each(function(){
        if(this.text == data[3])
            $('#line').val(this.value);
    });
    $('#sessionID > option').each(function(){
        if(this.text == data[4])
            $('#sessionID').val(this.value);
    });
}
