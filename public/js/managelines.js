$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#linesTable tbody").click(function(event) {
        $(oTable.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
    });
	
    /* Add a click handler for the delete row */
    $('#delete').click( function() {
        var anSelected = fnGetSelected( oTable );
        oTable.fnDeleteRow( anSelected[0] );
    } );
	
    /* Init the table */
    var oTable = $('#linesTable').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bProcessing": true
    });
    
    /* Apply the jEditable handlers to the table */
    $('td', oTable.fnGetNodes()).editable( '/admin/crr', {
        "callback": function( sValue, y ) {
            var aPos = oTable.fnGetPosition( this );
            oTable.fnUpdate( sValue, aPos[0], aPos[1] );
        },
        "submitdata": function ( value, settings ) {
            return {
                "operation": 'rename',
                "row_id": this.parentNode.getAttribute('id'),
                "column": oTable.fnGetPosition( this )[2]
            };
        },
        "event": "dblclick",
        "height": "14px"
    } );
} );


/* Get the rows which are currently selected */
function fnGetSelected( oTableLocal )
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