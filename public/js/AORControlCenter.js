// for aor control center
PlexAOR = {
    column_headers : [],
    table_width_percentage: 220,
    table_with_pixels: '',
    fixed_column_width: 0
}

$.fn.dataTableExt.afnSortData['custom'] = function (oSettings, iColumn){
    // console.log(this.api().rows(oSettings).nodes());
    var aIndexData = [], aColData = [];
    var gH = $('tr[data-groupname]');

    $( 'td:eq(0)', gH).each( function () {
        if($(this).text().trim()!="") {
            aIndexData.push( $(this).text().trim() );
        }
    } );
    
    $( 'td:eq('+iColumn+')', gH).each( function () {
        aColData.push( $(this).text().trim() );
    } );

    for( i in aIndexData ){
        if(aIndexData[i] == 'Free' || aIndexData == 'Associate')
            aColData[i] = aIndexData[i];
    }

    return aColData;
}
 
$.fn.dataTableExt.oSort['custom-asc']  = function(x,y) {
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
$.fn.dataTableExt.oSort['custom-desc'] = function(x,y) {
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

$(document).ready(function() {

    $('table.dataTable.display tbody td').removeAttr('border-top');

    //initilize DataTable
    var aorDataTable = $('#aor_dataTable').DataTable({
        'searching': true,
        'bInfo': false,
        'scrollY': false,
        'scrollX': true,
        'scrollCollapse': true,
        'paging': false,
        'columnDefs': [
            {'visible': false, 'targets': [3] },            // defines which column will hide
            {'width'  : '4%' , 'targets': [0,1] },        // defines width ratio
            {'width'  : '12%' , 'targets': [2] },
            {'width'  : '9%', 'targets': [3,4,5]},
            {'width'  : '10%', 'targets': [6,7,8,9,10] }, 
            {'sSortDataType': 'custom', 'targets' : [3]}    // defines custom sorting order
        ],
        
    });

    // build frozen columns on the left
    new $.fn.dataTable.FixedColumns( aorDataTable, {
        'iLeftColumns': 3,
        'iLeftWidth': 350,
        "fnDrawCallback": function ( left, right ) {
            var _this = this;
            var oSettings = this.s.dt;

            if ( oSettings._iDisplayLength == 0 )
                return;

            var nGroup, iIndex, sGroup, cTotal, mainT, customerType;
            var sLastGroup = "", iCorrector=0;
            var nTrs = $('#aor_dataTable tbody tr');
            var fTable = $('.DTFC_LeftBodyWrapper tbody tr');
            var iColspan = nTrs[0].getElementsByTagName('td').length;

            var tData = [];

            cTotal = '';
            for(var k = 0; k < iColspan; k++){
                cTotal += '<td class="group">&nbsp;</td>';
            }

            for ( var i=0 ; i<nTrs.length ; i++ ){
                iIndex = oSettings._iDisplayStart + i;
                sGroup = oSettings.aoData[ oSettings.aiDisplay[iIndex] ]._aData[3];

                //if group name contains &amp; replace with just &
                if( sGroup.indexOf('&') !== -1 )
                    sGroup = sGroup.replace('&amp;', '&');

                //add custom data attr to original table rows and fixed table rows
                nTrs[i].dataset.nestedname = sGroup;
                fTable[i].dataset.nestedname = sGroup;
                
                if ( sGroup != sLastGroup )
                {
                    /* Cell to insert into main table - group row */
                    nGroup = $('<tr data-groupname="'+sGroup+'">'+cTotal+'</tr>')[0];
                    nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );

                    /* Cell to insert into the frozen columns - group row */
                    nGroup = $('<tr data-groupname="'+sGroup+'"><td colspan="'+iColspan+'" class="group">'+sGroup+'</td></tr>')[0];
                    $(nGroup).insertBefore( $('tbody tr:eq('+(i+iCorrector)+')', left.body)[0] );

                    iCorrector++;
                    sLastGroup = sGroup;
                }
            }

            /* calculate totals
            mainT = $('#aor_dataTable tr[data-groupname]');
            mainT.each(function(){
                var _self = $(this);
                var gName = _self.data('groupname');
                var nestedR = $('#aor_dataTable tr[data-nestedname="'+gName+'"]');

                customerType = '';

                nestedR.each(function(){
                    var _this = $(this);
                    customerType = _this.find('td:eq(3)').text().trim();
                });

                _self.find('td:eq(6)').text(customerType);

            });
            */
        }
    });

});

// end of ready

$(document).on('click', 'tr[data-groupname]', function(){
    $(this).toggleGroupRows();
});

$.fn.toggleGroupRows = function() {
    var _this = $(this);

    var group = _this.data('groupname');
    var nested = $('tr[data-nestedname="' + group + '"]');

    nested.each(function() {
        self = $(this);
        if( self.hasClass('visible') ) {
            self.removeClass('visible').hide(400);
        } else {
            self.addClass('visible').show(400);
        }
    });

    return _this;
}