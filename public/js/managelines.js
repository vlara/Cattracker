$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#linesTable tbody").click(function(event) {
        $(oTableLine.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
    });
	
    /* Add a click handler for the delete row */
    $('#deleteLine').click( function() {
        var anSelected = fnGetSelectedLine( oTableLine );
        oTableLine.fnDeleteRow( anSelected[0] );
    } );
	
    /* Init the table */
    var oTableLine = $('#linesTable').dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "sScrollY": "200px",
        "bPaginate": false,
                        "aoColumnDefs": [
			{ "sWidth": "10%", "aTargets": [ -1 ] }
		]
    });
    
    /* Apply the jEditable handlers to the table */
    $('td', oTableLine.fnGetNodes()).editable( '/admin/crr', {
        "callback": function( sValue, y ) {
            var aPos = oTableLine.fnGetPosition( this );
            oTableLine.fnUpdate( sValue, aPos[0], aPos[1] );
        },
        "submitdata": function ( value, settings ) {
            return {
                "operation": 'rename',
                "row_id": this.parentNode.getAttribute('id'),
                "column": oTableLine.fnGetPosition( this )[2]
            };
        },
        "event": "dblclick",
        "height": "14px"
    } );
//    $(".toggle_container").hide(); 
//
//	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
//    $("h2.trigger").click(function(){
//            $(this).toggleClass("active").next().slideToggle("slow");
//            return false; //Prevent the browser jump to the link anchor
//    });
//$( "#accordion" ).accordion({active: false});
$( "#tabs" ).tabs({
    "show": function(event, ui) {
			var oTable = $('div.dataTables_scrollBody>table.display', ui.panel).dataTable();
			if ( oTable.length > 0 ) {
				oTable.fnAdjustColumnSizing();
			}
		}
});
} );


/* Get the rows which are currently selected */
function fnGetSelectedLine( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();
	
    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            deleteLine(aTrs[i].cells[0].id);
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteLine(id){
    $.get("/admin/crr", {
        'operation' : 'remove',
        'lineID' : id
    });
}