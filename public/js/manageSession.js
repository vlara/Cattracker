$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#sessionsTable tbody").click(function(event) {
        $(oTableSession.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
        fillSessionForm(oTableSession);
    });
	
    /* Add a click handler for the delete row */
    $('#deleteSession').click( function() {
        var anSelected = fnGetSelectedSession( oTableSession );
        var row = anSelected[0];
        var data = oTableSession.fnGetData(row);
        oTableSession.fnDeleteRow( anSelected[0] );
        deleteSession(data[0]);
    } );
	
    /* Init the table */
    var oTableSession = $('#sessionsTable').dataTable({
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
//    $('td', oTableLine.fnGetNodes()).editable( '/admin/crr', {
//        "callback": function( sValue, y ) {
//            var aPos = oTableLine.fnGetPosition( this );
//            oTableLine.fnUpdate( sValue, aPos[0], aPos[1] );
//        },
//        "submitdata": function ( value, settings ) {
//             var ret_arr = oTableLine.fnGetPosition( this );
//            return {
//                "operation": 'edit' + oTableLine.fnSettings().aoColumns[ret_arr[1]].sTitle +'Session',
//                "row_id": this.parentNode.getAttribute('id'),
//                "column": oTableLine.fnGetPosition( this )[2]
//            };
//        },
//        "event": "dblclick",
//        "height": "14px"
//    } );
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
function fnGetSelectedSession( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();
	
    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            deleteSession(aTrs[i].cells[0].id);
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteSession(id){
    $('input:checkbox').removeAttr('checked');
    $('#SessionID').val("");
    $('#DescriptionForm').val("");
    $.get("/admin/crr", {
        'operation' : 'removeSession',
        'sessionID' : id
    });
}

function fillSessionForm(oTableLocal){
    $('input:checkbox').removeAttr('checked');
    var ret_arr = fnGetSelectedLine(oTableLocal);
    var row = ret_arr[0];
    var data = oTableLocal.fnGetData(row);
    $('#SessionID').val(data[0]);
    $('#DescriptionForm').val(data[1]);
    if (data[2] == 1)
        $('#Active').prop("checked", true);
    }
