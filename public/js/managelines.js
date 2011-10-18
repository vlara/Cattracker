$(document).ready(function() {
    /* Add a click handler to the rows - this could be used as a callback */
    $("#linesTable tbody").click(function(event) {
        $(oTableLine.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
        fillLineForm(oTableLine);//Fills In Tthe Form
    });
    /* Add a click handler for the delete row */
    $('#deleteLine').click( function() {
        var anSelected = fnGetSelectedLine( oTableLine );
        var row = anSelected[0];
        var data = oTableLine.fnGetData(row);
        oTableLine.fnDeleteRow( anSelected[0] );
        deleteLine(data[0]);
    } );
	
    /* Init the table */
    var oTableLine = $('#linesTable').dataTable({
        "bJQueryUI": true,
        //"bProcessing": true,
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
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}

function deleteLine(id){
    $('input:checkbox').removeAttr('checked');
    $('#LineID').val("");
    $('#LineName').val("");
    $.get("/admin/crr", {
        'operation' : 'remove',
        'lineID' : id
    });
}
function fillLineForm(oTableLocal){
    $('input:checkbox').removeAttr('checked');
    var ret_arr = fnGetSelectedLine(oTableLocal);
    var row = ret_arr[0];
    var data = oTableLocal.fnGetData(row);
    $('#LineID').val(data[0]);
    $('#LineName').val(data[1]);
    var days = data[2].split(',');
    for(i = 0; i < days.length; i++){
        switch(days[i]){
            case "Mon":
                $('#M').prop("checked", true);
                break;
            case "Tue":
                $('#T').prop("checked", true);
                break;
            case "Wed":
                $('#W').prop("checked", true);
                break;
            case "Thu":
                $('#TH').prop("checked", true);
                break;
            case "Fri":
                $('#F').prop("checked", true);
                break;
            case "Sat":
                $('#S').prop("checked", true);
                break;
            case "Sun":
                $('#SU').prop("checked", true);
                break;
        }
    }
}