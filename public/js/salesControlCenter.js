//js for Sales Control Center
PlexSales = {
    column_headers: [],
    table_width_percentage: 220,
    table_width_pixels: '',
    fixed_column_width: 0,
    saveNoteInterval: '',
    EngagementData: {}
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
    // here iColumn should be 5 not 6;
    $( 'td:eq('+iColumn+')', gH).each( function () {
        aColData.push( $(this).text().trim() );
    } );
    //console.log(aIndexData);
    //console.log(aColData);
 
    for (i in aIndexData) {
        // replace between { and } with the real value of your rows at first column
        if (aIndexData[i] == "Free" || aIndexData[i] == "Associate" )
            aColData[i] = aIndexData[i];
    }    
    return aColData;
}
 
$.fn.dataTableExt.oSort['custom-asc']  = function(x,y) {
    if (x == "{put row 1 col 1 here}") return -1; // keep this row at top
    if (y == "{put row 1 col 1 here}") return 1; // keep this row at top
 
    if (x == "{put row 2 col 1 here}") return -1; // keep this row next to top
    if (y == "{put row 2 col 1 here}") return 1; // keep this row next to top
 
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
$.fn.dataTableExt.oSort['custom-desc'] = function(x,y) {
    if (x == "{put row 1 col 1 here}") return 1; // keep this row at top
    if (y == "{put row 1 col 1 here}") return -1; // keep this row at top
 
    if (x == "{put row 2 col 1 here}") return 1; // keep this row next to top
    if (y == "{put row 2 col 1 here}") return -1; // keep this row next to top
 
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

$(document).ready( function () {

    $('table.dataTable.display tbody td').removeAttr('border-top');

    var salesDataTable = $('#sales_dataTable').DataTable({
        //search bar
    	searching: true,

        "bInfo": false,

        scrollY: "500px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,

        "columnDefs": [
            { "visible": false, "targets": 6 },
            {'targets': 5, 'sSortDataType': 'custom'}
        ],

    });//end of DataTable initialization
    new $.fn.dataTable.FixedColumns( salesDataTable, {
        'iLeftColumns': 4,
        'iLeftWidth': 350,
        "fnDrawCallback": function ( left, right ) {
            var _this = this;
            var oSettings = this.s.dt;

            if ( oSettings._iDisplayLength == 0 )
                return;

            var nGroup, nCell, iIndex, sGroup, rTotal, fBody, rTotalStart, rTotalEnd, cTotal;
            var sLastGroup = "", iCorrector=0;
            var nTrs = $('#sales_dataTable tbody tr');
            var fTable = $('.DTFC_LeftBodyWrapper tbody tr');
            var iColspan = nTrs[0].getElementsByTagName('td').length;

            var mainT, frozenT, customerType, last_logged_in,
                engagementScore, ove_act_score, daily_act_score, weekly_act_score, mon_act_score,
                inq, inqR, inqA, inqI, rec, acceptPending, total_pend, pend_appr, pend_appr_perc, search_rec, total_appr, appr_inq,
                appr_man_rec, appr_auto_rec, appr_search, prof_views, filtered_rec, nonFiltered_rec, days_chatted, daily_chat, chat_receive, chat_sent, t_mess, mess_receive, mess_sent,
                mess_rr, college_likes, exported, uploaded_ranks;

            var tData = [];

            cTotal = '';
            for(var k = 0; k < iColspan; k++){
                cTotal += '<td class="group">&nbsp;</td>';
            }

            for ( var i=0 ; i<nTrs.length ; i++ ){
                iIndex = oSettings._iDisplayStart + i;
                sGroup = oSettings.aoData[ oSettings.aiDisplay[iIndex] ]._aData[6];

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

            //calculate totals
            mainT = $('#sales_dataTable tr[data-groupname]');
            mainT.each(function(){
                var _self = $(this);
                var gName = _self.data('groupname');
                var nestedR = $('#sales_dataTable tr[data-nestedname="'+gName+'"]');

                customerType = '';
                last_logged_in = 0;

                engagementScore = 0;
                ove_act_score = 0;
                daily_act_score = 0;
                weekly_act_score = 0;
                mon_act_score = 0;

                // inq = 0;
                // inqR = 0;
                // inqA = 0;
                // inqI = 0;
                // rec = 0;
                // acceptPending = 0;
                // recR = 0;
                // recI = 0;
                // recAuto = 0;
                // total_pend = 0;
                // pend_appr = 0;
                // pend_appr_perc = 0;
                // search_rec = 0;
                // total_appr = 0;
                // appr_inq = 0;
                // appr_man_rec = 0;
                // appr_auto_rec = 0;
                // appr_search = 0;
                // prof_views = 0;
                // filtered_rec = 0;
                // nonFiltered_rec = 0;
                // days_chatted = 0;
                // daily_chat = 0;
                chat_receive = 0;
                chat_sent = 0;
                t_mess = 0;
                mess_receive = 0;
                mess_sent = 0;
                mess_rr = 0;
                // college_likes = 0;
                // exported = 0;
                // uploaded_ranks = 0;

                nestedR.each(function(){
                    var _this = $(this);
                    customerType = _this.find('td:eq(5)').text().trim();
                    // last_logged_in += parseFloat( _this.find('td:eq(9)').text().trim() );
                    // engagementScore = _this.find('td:eq(10)').text().trim();
                    // ove_act_score = _this.find('td:eq(11)').text().trim();
                    // daily_act_score = _this.find('td:eq(12)').text().trim();
                    // weekly_act_score = _this.find('td:eq(13)').text().trim();
                    // mon_act_score = _this.find('td:eq(14)').text().trim();
                    // inq += parseFloat( _this.find('td:eq(15)').text().trim() );
                    // inqR += parseFloat( _this.find('td:eq(16)').text().trim() );
                    // inqA += parseFloat( _this.find('td:eq(17)').text().trim() );
                    // inqI += parseFloat( _this.find('td:eq(18)').text().trim() );
                    // rec += parseFloat( _this.find('td:eq(19)').text().trim() );
                    // acceptPending += parseFloat( _this.find('td:eq(20)').text().trim() );
                    // recR += parseFloat( _this.find('td:eq(21)').text().trim() );
                    // recI += parseFloat( _this.find('td:eq(22)').text().trim() );
                    // recAuto += parseFloat( _this.find('td:eq(23)').text().trim() );
                    // total_pend += parseFloat( _this.find('td:eq(24)').text().trim() );
                    // pend_appr += parseFloat( _this.find('td:eq(25)').text().trim() );
                    // pend_appr_perc += parseFloat( _this.find('td:eq(26)').text().trim().substr(1) );
                    // search_rec += parseFloat( _this.find('td:eq(27)').text().trim() );
                    // total_appr += parseFloat( _this.find('td:eq(28)').text().trim() );
                    // appr_inq += parseFloat( _this.find('td:eq(29)').text().trim().substr(1) );
                    // appr_man_rec += parseFloat( _this.find('td:eq(30)').text().trim().substr(1) );
                    // appr_auto_rec += parseFloat( _this.find('td:eq(31)').text().trim().substr(1) );
                    // appr_search += parseFloat( _this.find('td:eq(32)').text().trim().substr(1) );
                    // prof_views += parseFloat( _this.find('td:eq(33)').text().trim() );
                    // filtered_rec += parseFloat( _this.find('td:eq(34)').text().trim() );
                    // nonFiltered_rec += parseFloat( _this.find('td:eq(35)').text().trim() );
                    // days_chatted += parseFloat( _this.find('td:eq(37)').text().trim() );
                    // daily_chat += parseFloat( _this.find('td:eq(38)').text().trim() );

                    // if(_this.find('td:eq(11)').text().trim() == 'Decreasing'){
                    //     var engagement = _this.find('td:eq(11)').find('a');
                    //     engagement.empty();
                    //     engagement.append('D');
                    // };
                    // var yesterdayScore = _this.find('td:eq(12)');
                    // if(yesterdayScore.text().trim()){
                    //     yesterdayScore.empty();
                    //     yesterdayScore.append('A');
                    // }
                    // var weeklyScore = _this.find('td:eq(13)');
                    // if(weeklyScore.text().trim()){
                    //     weeklyScore.empty();
                    //     weeklyScore.append('B');
                    // }
                    // var monthlyScore = _this.find('td:eq(14)');
                    // if(monthlyScore.text().trim()){
                    //     monthlyScore.empty();
                    //     monthlyScore.append('C');
                    // }

                    if (chat_receive < parseFloat( _this.find('td:eq(41)').text().trim() )) {
                        chat_receive = parseFloat( _this.find('td:eq(41)').text().trim() );
                    }
                    
                    chat_sent += parseFloat( _this.find('td:eq(42)').text().trim() );

                    if (t_mess < parseFloat( _this.find('td:eq(44)').text().trim() )) {
                        t_mess = parseFloat( _this.find('td:eq(44)').text().trim() );
                    }
                    
                    mess_receive += parseFloat( _this.find('td:eq(43)').text().trim() );
                    
                    // if (mess_sent < parseFloat( _this.find('td:eq(42)').text().trim() )) {
                    //     mess_sent = parseFloat( _this.find('td:eq(42)').text().trim() );
                    // }

                    mess_sent = mess_receive + t_mess + chat_sent + chat_receive;
                    
                    mess_rr += parseFloat( _this.find('td:eq(45)').text().trim().substr(1) );
                    // college_likes += parseFloat( _this.find('td:eq(45)').text().trim() );
                    // exported += parseFloat( _this.find('td:eq(46)').text().trim() );
                    // uploaded_ranks += parseFloat( _this.find('td:eq(47)').text().trim() );
                });

                _self.find('td:eq(5)').text(customerType);
                _self.find('td:eq(41)').text(chat_receive);
                _self.find('td:eq(42)').text(chat_sent);
                _self.find('td:eq(44)').text(t_mess);
                _self.find('td:eq(43)').text(mess_receive);
                _self.find('td:eq(45)').text(mess_sent);

                if( mess_rr > 0 )
                    mess_rr = mess_rr / nestedR.length;

                _self.find('td:eq(46)').text(mess_rr.toFixed(2) + '%');
            });
        }
    }); //end of fixedColumns

    //init data attr for rows that have role attributes
    // PlexSales.hideAllNestedRows();

    //on page load, make the date input fields empty
    $('.salesReport-from, .salesReport-to').val('');

    //adding placeholder text to dataTable's search bar since I hid the input label
    $('#sales_dataTable_filter input').attr('placeholder','Find...');

    //uncheck all column selection checkboxes on page load
    $('.col-select-chkbox, #show_clients_only').prop('checked', false);

    //column selection checkbox click event to hide/show table columns
    $('.col-select-chkbox').on('click', function(){
        setTableWidth(this);
        var _thisColumn = salesDataTable.column( $(this).val() );
        _thisColumn.visible( !_thisColumn.visible() );
    });

    //if uneven amount of columns, the set up in the 'select column pane' will be off, so add class 'end' to last column to fix
    $('.col-select-column:last-child').addClass('end');

    //horizontal scroll detecting to enable/disable fixed columns
    // var the_columns = $('.dataTables_scroll .dataTables_scrollBody tr td:nth-child(4)');
    // var widest = $('.dataTables_scroll .dataTables_scrollHead .dataTables_scrollHeadInner th:nth-child(4)');
    // PlexSales.fixed_column_width = widest.width();
    // $('.dataTables_scrollBody').scroll(function(){
    //     var current_left_val = $(this).scrollLeft();
        
    //     if( current_left_val > PlexSales.fixed_column_width ){
    //         $(the_columns).addClass('fixed_column').css({ 'left': 0, 'margin-top': -1, 'width': PlexSales.fixed_column_width });
    //     }else{
    //         $(the_columns).removeClass('fixed_column');
    //     }
    // });

    //make note textarea enabled on page load
    $('textarea.sales-messages-notes').prop('disabled', false);

    //on doc ready, make search bar focused
    $('.sales-dataSheet-container input[type="search"]').focus();

    // Order by the grouping
    $('#sales_dataTable tbody').on( 'click', 'tr[data-groupname] td', function () {
        var sort_by = $(this).index();
        var currentOrder = salesDataTable.order()[0];
        // console.log(currentOrder);
        // console.log(currentOrder[1]);
        // sort_by = sort_by > 6 ? ++sort_by : sort_by;
        // console.log(typeof sort_by);
        if ( currentOrder[0] === 6 && currentOrder[1] === 'asc' ) {
            salesDataTable.order( [ 6, 'desc' ], [ 0, 'desc' ] ).draw();
        }
        else {
            salesDataTable.order( [ 6, 'asc' ], [ 0, 'asc' ] ).draw();
        }
    } );  

    // $(document).on('click', '.engagement-link', function(e){
    $('.engagement-link').click(function(event) {
        /* Act on the event */
        var _this = $(this);
        var data = _this.data('engagement');
        //console.log(data);
        event.preventDefault();

        $('#leftSelectDateRange').data('school-id', data.college_id);
        $('#leftSelectDateRange').data('user-id', data.user_id);

        if( typeof data === 'object' ){
            PlexSales.buildEngagementModal(data);
        }
        else{
            return;
        }
    });

    var optionSet1 = {
        startDate: moment(),    
        endDate: moment(),
        minDate: '01/01/2010',
        maxDate: '12/31/2035',
        dateLimit: { days: 360 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
           '1 Day Before' : [moment().subtract('days', 2), moment().subtract('days', 2)],
           'Last 7 Days': [moment().subtract('days', 6), moment()],
           '7 Days Before' : [moment().subtract('days', 13), moment().subtract('days', 7)], 
           'Last 14 Days' : [moment().subtract('days', 13), moment()],
           '14 Days Before' : [moment().subtract('days', 29), moment().subtract('days', 14)],
           'Last 30 Days': [moment().subtract('days', 29), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
        },
        opens: 'right',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
     };

    $(".dash-cal").daterangepicker( 
       optionSet1,
        function(start, end, label) {

       }
    );

    $('select#leftSelectDateRange').val("0");

    $('#triggerModal #almArea .threshold').val("0");

    $('#triggerModal #almArea .threshold').keyup(function () { 
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });

    $('#triggerModal #almArea .threshold').blur(function(){
        // console.log($(this).val());
        // if($(this).val() == null || parseFloat($(this).val()) == 0.00 || $(this).val() == ""){
        //     if(!$('#triggerModal #almArea .button').hasClass('hide'))
        //         $('#triggerModal #almArea .button').addClass('hide');
        // }else{
        //     if($('#triggerModal #almArea .button').hasClass('hide'))
        //         $('#triggerModal #almArea .button').removeClass('hide');
        // }
    });    
});//end of document ready


//listen for key press '/' to focus on search bar
$(document).on('blur', '.sales-dataSheet-container input[type="search"]', function(){
    var self = $(this);
    $(document).on('keyup', function(e){
        if( e.which === 191 ){
            self.focus();
        }
    });
});


// -- hiding and showing of group's nested rows
$(document).on('click', 'tr[data-groupname]', function(){
    // $(this).toggleGroupRows();
});

PlexSales.hideAllNestedRows = function(){
    $('tr[data-nestedname]').hide();
}

$.fn.toggleGroupRows = function(){
    _this = $(this);
    var group = _this.data('groupname');
    var nested = $('tr[data-nestedname="'+group+'"]');

    nested.each(function(){
        self = $(this);
        if( self.hasClass('visible') ){
            self.removeClass().hide();
        }else{
            self.addClass('visible').show();
        }
    });

    return this;
}
// -- hiding and showing of group's nested rows


/*****************************************
*
* js for Sales Messages page - start
*
*****************************************/

//thread click event
$(document).on('click', '.convo-thread', function(){
    var thread_ID = $(this).data('thread-id');
    var msgLink = $(this).data('msg-link');

    $('.convo-thread').removeClass('active-thread');
    $(this).addClass('active-thread');

    PlexSales.openThreadConvo(thread_ID);
});


//make ajax call to retrieve new thread's convo
PlexSales.openThreadConvo = function(thread_id){
    var ajax_route = '/sales/getThreadMsgs/' + thread_id;
    $('.sales-msg-ajax-loader').show();

    $.ajax({
        url: ajax_route,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        var temp = $.parseJSON(data);
        var msg_data = $.parseJSON(temp.data);
        var msg_notes = $.parseJSON(temp.note_arr);

        $('.sales-msg-ajax-loader').hide();
        PlexSales.buildThreadMessageView( msg_data, msg_notes, thread_id );
        PlexSales.applyAlternateMsgTheme();
    }); 
}


//build thread message for ajax call
PlexSales.buildThreadMessageView = function(data, note_data, t_id){

    var msgView = '';
    var is_college = '';
    var msg_date = '';
    var new_date = '';
    var msg_time = '';
    var this_notepad = $('textarea.sales-messages-notes');

    $(data).each(function(index){

        is_college = '';
        msg_date = PlexSales.getMsgDate(this.date);
        msg_time = PlexSales.getMsgTime(this.date);

        if( new_date !== msg_date ){
            msgView += '<hr class="msg-divider">';
            msgView += '<div class="row msg-date">';
            msgView +=      '<div class="column small-12">';
            msgView +=          msg_date;
            msgView +=      '</div>';
            msgView += '</div>';
            new_date = msg_date;
        }

        if( this.is_org === 1 ){
            is_college = 'is-college';
        }

        msgView += '<div class="row msg-details-container ' + is_college + '">';     

        msgView +=      '<div class="column small-12 large-1 msg-time-sent">';
        msgView +=          msg_time;
        msgView +=      '</div>';

        msgView +=      '<div class="column small-12 medium-3 msg-name">';
        msgView +=          this.full_name;
        msgView +=      '</div>';

        msgView +=      '<div class="column small-12 medium-9 large-8 msg-content">'
        msgView +=          this.msg;
        msgView +=      '</div>';

        msgView += '</div>';
        
    });

    $('.msg-view-section').html(msgView);
    $(this_notepad).attr('data-thread-id-note', t_id);
    $(this_notepad).data('thread-id-note', t_id);
    PlexSales.buildNotesForThisThread(note_data, t_id);
}


//build current threads notes when thread is clicked
PlexSales.buildNotesForThisThread = function( notepad, t_id ){
    var note_pad = $('textarea.sales-messages-notes[data-thread-id-note="'+t_id+'"]');
    var this_notepads_saved_time = note_pad.closest('.notes-pane-section').find('.last-saved-updated-time');
    
    notepad.note = notepad.note === null ? '' : notepad.note;
    note_pad.val(notepad.note);
    this_notepads_saved_time.html(notepad.note_date);
}


//parses string to return just the date
PlexSales.getMsgDate = function(date){
    var parse_day = moment(date);
    return parse_day.format('dddd, MMMM DD, YYYY');
}


//parses string to return just the time
PlexSales.getMsgTime = function(time){
    var parse_time = moment(time);
    return parse_time.format('HH:mm');
}


//on load, force textarea height based on parent container size
$(window).on('load', function(){
    //on load, set the height of the note taking textarea
    $('.note-taking-pane textarea').height( $('.note-taking-pane').height() - 20 );
});


//dynamic textarea height on window resize
$(window).on('resize', function(){
    var _this = $('.note-taking-pane textarea');
    var this_container = $('.note-taking-pane');

    _this.height( this_container.height() - 20 );
});



//when textarea has focus, every ten seconds, save notes
$('textarea.sales-messages-notes').focus(function(){
    var _this = $(this);
    var this_thread_id = $(this).data('thread-id-note');

    if( this_thread_id !== '' ){
        //loop - every 10 seconds, while textarea is in focus, save the note
        PlexSales.saveNoteInterval = setInterval(function(){
            PlexSales.autoSaveSalesNotes( this_thread_id, _this );
        }, 10000);
    }
    
});


//when user clicks out of focused textarea, save notes
$('textarea.sales-messages-notes').blur(function(){
    var _this = $(this);
    var this_thread_id = $(this).data('thread-id-note');

    //when textarea is out of focus, stop the loop interval and call autoSave one last time to save notes
    clearInterval(PlexSales.saveNoteInterval);

    if( this_thread_id !== '' ){
        PlexSales.autoSaveSalesNotes( this_thread_id, _this );
    }
});

//auto save Sales notes
PlexSales.autoSaveSalesNotes = function( id, notepad ){
    var note_val = $(notepad).val();
    var last_saved_text = $(notepad).closest('.notes-pane-section').find('.last-saved-updated-time');

    $('.auto-save-ajax-loader').show();

    //post note and update 'last saved' time
    $.ajax({
        url: '/sales/setPlexussNote',
        type: 'POST',
        data: {thread_id: id, note: note_val},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(time){
        $('.auto-save-ajax-loader').hide();
        last_saved_text.html(time);
    });
}


//jquery ui datepicker initialization for Sales Messages page
$(document).on('focus', 'input.schoolsMsg-date:not(.hasDatepicker)', function(){

    //initialized jquery ui datepicker - for Messages
    $( ".schoolsMsg-from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( ".schoolsMsg-to" ).datepicker( "option", "minDate", selectedDate );
      }
    });

    $( ".schoolsMsg-to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( ".schoolsMsg-from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
});


//user presses ctrl + e + r, trigger message expansion
$(document).on('keydown', function(e){
    if(e.shiftKey === true && e.which === 37){
       if( $('.right-side-msgView').hasClass('expanded-view') ){
            $('.right-side-msgView').removeClass('expanded-view');
       }else{
            $('.right-side-msgView').addClass('expanded-view');
       }
    }
});


//user presses shift + left arrow, trigger alternate message view
$(document).on('keydown', function(e){
    if(e.shiftKey === true && e.which === 38){
        var msg_view_container = $('.msg-view-section');

        if( msg_view_container.hasClass('alternate-msg-view-theme') ){
            msg_view_container.removeClass('alternate-msg-view-theme');
        }else{
            msg_view_container.addClass('alternate-msg-view-theme');
        }

        PlexSales.applyAlternateMsgTheme();
    }
});

PlexSales.applyAlternateMsgTheme = function(){
    var msg_container = $('.msg-view-section');

    if( msg_container.hasClass('alternate-msg-view-theme') ){
        msg_container.find('.msg-details-container > .column').removeClass().addClass('alternate-theme-col');

        msg_container.find('.msg-details-container.is-college > .alternate-theme-col:nth-child(1)').addClass('column small-12 text-right msg-time-sent');
        msg_container.find('.msg-details-container.is-college > .alternate-theme-col:nth-child(2)').addClass('column small-12 text-right msg-name');
        msg_container.find('.msg-details-container.is-college > .alternate-theme-col:nth-child(3)').addClass('column small-8 large-7 small-offset-4 large-offset-5 text-right msg-content');

        msg_container.find('.msg-details-container:not(.is-college) > .alternate-theme-col:nth-child(1)').addClass('column small-12 text-left msg-time-sent');
        msg_container.find('.msg-details-container:not(.is-college) > .alternate-theme-col:nth-child(2)').addClass('column small-12 text-left msg-name');
        msg_container.find('.msg-details-container:not(.is-college) > .alternate-theme-col:nth-child(3)').addClass('column small-8 large-7 end text-left msg-content');
    }else{
        msg_container.find('.msg-details-container > div').removeClass().addClass('column');

        msg_container.find('.msg-details-container > .column:nth-child(1)').addClass('small-12 medium-2 large-1 msg-time-sent');
        msg_container.find('.msg-details-container > .column:nth-child(2)').addClass('small-12 medium-10 large-3 msg-name');
        msg_container.find('.msg-details-container > .column:nth-child(3)').addClass('small-12 large-8 msg-content');
    }
}
/*****************************************
*
* js for Sales Messages page - end
*
*****************************************/






/*****************************************
*
* js for Sales Client Report page - start
*
*****************************************/

//when column is fixed and user wants to sort another column, reset the fixed columns width
$(document).on('click', '.dataTables_scroll .dataTables_scrollHead .dataTables_scrollHeadInner tr th', function(){
    var the_fixed_col = $('.dataTables_scroll .dataTables_scrollBody tr td:nth-child(3)');

    if( the_fixed_col.hasClass('fixed_column') ){
        the_fixed_col.css({'width': PlexSales.fixed_column_width});
    }
});

//setting new table width when hiding columns
var setTableWidth = function(column){
    var col_index = $(column).val();
    var col_selector = '.dataTables_scroll .dataTables_scrollHeadInner table.dataTable th[data-column-index="'+ $(column).val() +'"]';
    var this_column = $(col_selector);
    var col_width = this_column.width();
    var is_checked = false;

    if( $(column).is(':checked') ){
        is_checked = true;
    }else{
        is_checked = false;
    }

    PlexSales.setColumnWidth(col_index, col_width, this_column, is_checked);
    PlexSales.calculateTableWidth(is_checked, col_index);
}

//instantiating column objects, if not already created
PlexSales.setColumnWidth = function(index, width, selector, checked){
    var already_exits = false;
    var new_column = {};

    //check if column has already been added to our array, if not, add it
    if( PlexSales.getCurrentColumn(index) === 0 ){
        new_column = {
            colIndex: index,
            colWidth: width,
            colSelector: selector,
            col_checkbox_checked: checked,
            widthPercent: 0
        };

        PlexSales.column_headers.push(new_column);
    }
}

//calculates table width based on which column has been hidden/shown
PlexSales.calculateTableWidth = function(it_is_checked, col_index){
    var new_width_attr = '';
    var this_table = $('#sales_dataTable'); 

    //get current column object
    var current_column = PlexSales.getCurrentColumn(col_index);

    //get table width in px
    PlexSales.table_width_pixels = this_table.width();

    //divide col width px by table width px to get percentage of column width of the entire table
    current_column.widthPercent = Math.floor( (current_column.colWidth / PlexSales.table_width_pixels) * 100 );
    current_column.col_checkbox_checked = it_is_checked;

    //if check box is checked to remove column, subtract column width from table width, otherwise add it back since column was added back
    if( current_column.col_checkbox_checked ){
        PlexSales.table_width_percentage -= current_column.widthPercent;
    }else{
        PlexSales.table_width_percentage += current_column.widthPercent;
    }

    //if table percentage is < 100%, then keep at 100%
    if( PlexSales.table_width_percentage < 100 ){
        new_width_attr = 'width: 100%;';
    }else{
        new_width_attr = 'width: ' + PlexSales.table_width_percentage + '%;';
    }

    $(this_table).attr('style', new_width_attr);
}

//returns column object from our array, if found, else returns 0
PlexSales.getCurrentColumn = function(col_index){
    var column_found = 0;
    for (var i = 0; i < PlexSales.column_headers.length; i++) {
        if( PlexSales.column_headers[i].colIndex === col_index){
            column_found = PlexSales.column_headers[i];
            break;
        }
    };
    return column_found;
}


//scroll event on Sales Report table to enable sticky fixed header
$(window).scroll(function(){
    var table_header = $('.dataTables_scrollHead');
    if( $(this).scrollTop() > 100 ){
        table_header.addClass('is_fixed');
    }else{
        table_header.removeClass('is_fixed');
    }
});


//jquery ui datepicker init for Sales Client Reporting page
$(document).on('focus', 'input.salesReport-date:not(.hasDatepicker)', function(){

    //initialized jquery ui datepicker - for Client Reporting
    $( ".salesReport-from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( ".salesReport-to" ).datepicker( "option", "minDate", selectedDate );
      }
    });

    $( ".salesReport-to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( ".salesReport-from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
});


//show clients only checkbox event
$('#show_clients_only').change(function(){
    $('.sales-dataSheet-container tr:not(tr.is-client):not(:first-child)').toggle();
});


//close 'choose column' pane on outside DOM click
$(document).on('click', function(e){
    var choose_col_btn = $('.choose-columns-btn');
    var choose_col_container = $('.column-selection-pane');

    /* as long as the target is not the 'choose columns to show' btn and the 'choose columns' pane
    or any children of the pane, then close the pane */
    if ((!choose_col_btn.is(e.target) && choose_col_btn.has(e.target).length === 0) && 
        (!choose_col_container.is(e.target) && choose_col_container.has(e.target).length === 0)){

        choose_col_container.fadeOut(250);
        choose_col_btn.removeClass('active');
    }
});


//date filter 'Go' btn click event to filter sales report between submitted dates
$('.submit-date-filter-btn').on('click', function(){
    // console.log('filter by date yo!');
    var from_date = $('.salesReport-from').val();
    var to_date = $('.salesReport-to').val();
    // console.log('from: ' + from_date);
    // console.log('to: ' + to_date);

    //add ajax here to get new sales report results!!!!!!!!
});


//reset button that restores all the hidden columns
$('.reset-col-btn').on('click', function(){
    $('.col-select-chkbox:checked').trigger('click');
});


//'Choose columns to show' button click event
$('.choose-columns-btn').on('click', function(){
    var _this = $(this);
    var selection_pane = $(this).closest('.sales-filter-container').find('.column-selection-pane');

    if( $(selection_pane).is(':visible') ){
        //hide selection pane and remove active class from btn
        $(selection_pane).fadeOut(250);
        _this.removeClass('active');

    }else{
        //add active class to btn and remove border radius from button when pane is open and position selection pane
        _this.addClass('active');
        $(selection_pane).css({
            top: _this.offset().top - 15,
            left: _this.offset().left - 1
        }).fadeIn(250);
    }
});
/*****************************************
*
* js for Sales Client Report page - end
*
*****************************************/

$(document).on('click', '#goalDataModal .clearfix', function() {
    var allTds = $('#goalDataModal table td[class^="inject-"]');
    var indicatorClass = '';
    var self;

    //loop through each property of data object
    allTds.each(function(){
        self = $(this);
        self.empty();
    }); 

    //close modal
    PlexSales.closeGoalDataModal();
});
$(document).on('click', '.goal-link', function(e){
    var _this = $(this);
    var data = _this.data('goals');
    // console.log(data);
    e.preventDefault();
    // inject data[prop] for each DOM
    if(typeof data === 'object'){
        var injectClass = 'inject-';
        for(var prop in data){
            if( data.hasOwnProperty(prop) ){
                $('td.' + injectClass + prop).html(data[prop]);
                if(data[prop] == 'positive'){
                    prop = prop.substring(0, prop.length - 6);
                    $('td.' + injectClass + prop).removeClass('negative');
                    $('td.' + injectClass + prop).addClass('positive');
                }else if(data[prop] == 'negative'){
                    prop = prop.substring(0, prop.length - 6);
                    $('td.' + injectClass + prop).removeClass('positive');
                    $('td.' + injectClass + prop).addClass('negative');
                }
            } 
        }
        PlexSales.openGoalDataModal();
    }else {
        return;
    }

});

$(document).on('click', '#monthly', function(){
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    }
    if(!$('#quarterly').hasClass('unselect')) {
        if($('#quarterly').hasClass('select')){
            $('#quarterly').removeClass('select');
        }
        $('#quarterly').addClass('unselect');
    }
    if(!$('#annually').hasClass('unselect')) {
        if($('#annually').hasClass('select')){
            $('#annually').removeClass('select');
        }
        $('#annually').addClass('unselect');
    }
    // show self
    $('#goalDataModal table#monthlyGoal').fadeIn(500);
    if($('#goalDataModal table#monthlyGoal').hasClass('hide')){
        $('#goalDataModal table#monthlyGoal').removeClass('hide');
    }
    // hide the other two tables  
    if(!$('#goalDataModal table#quarterlyGoal').hasClass('hide')) {
        $('#goalDataModal table#quarterlyGoal').addClass('hide');
    }
    if(!$('#goalDataModal table#annuallyGoal').hasClass('hide')) {
        $('#goalDataModal table#annuallyGoal').addClass('hide');
    }
    //console.log('I am here in monthly');
});

$(document).on('click', '#quarterly', function(){
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    }
    if(!$('#monthly').hasClass('unselect')) {
        if($('#monthly').hasClass('select')){
            $('#monthly').removeClass('select');
        }
        $('#monthly').addClass('unselect');
    }
    if(!$('#annually').hasClass('unselect')) {
        if($('#annually').hasClass('select')){
            $('#annually').removeClass('select');
        }
        $('#annually').addClass('unselect');
    }
    // show self
    $('#goalDataModal table#quarterlyGoal').fadeIn(500);
    if($('#goalDataModal table#quarterlyGoal').hasClass('hide')){
        $('#goalDataModal table#quarterlyGoal').removeClass('hide');
    }
    // hide the other two tables  
    if(!$('#goalDataModal table#monthlyGoal').hasClass('hide')) {
        $('#goalDataModal table#monthlyGoal').addClass('hide');
    }
    if(!$('#goalDataModal table#annuallyGoal').hasClass('hide')) {
        $('#goalDataModal table#annuallyGoal').addClass('hide');
    }
    //console.log('I am here in quarterly');
});

$(document).on('click', '#annually', function(){
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    }
    if(!$('#monthly').hasClass('unselect')) {
        if($('#monthly').hasClass('select')){
            $('#monthly').removeClass('select');
        }
        $('#monthly').addClass('unselect');
    }
    if(!$('#quarterly').hasClass('unselect')) {
        if($('#quarterly').hasClass('select')){
            $('#quarterly').removeClass('select');
        }
        $('#quarterly').addClass('unselect');
    }
    // show self
    $('#goalDataModal table#annuallyGoal').fadeIn(500);
    if($('#goalDataModal table#annuallyGoal').hasClass('hide')){
        $('#goalDataModal table#annuallyGoal').removeClass('hide');
    }
    // hide the other two tables  
    if(!$('#goalDataModal table#monthlyGoal').hasClass('hide')) {
        $('#goalDataModal table#monthlyGoal').addClass('hide');
    }
    if(!$('#goalDataModal table#quarterlyGoal').hasClass('hide')) {
        $('#goalDataModal table#quarterlyGoal').addClass('hide');
    }
    //console.log('I am here in annually');
});

// ----- engagement data table modal
$(document).on('click', '#engagementDataModal .clearfix', function(){
    PlexSales.removeIndicatorClasses();
});

//inject data into modal
PlexSales.buildEngagementModal = function(data){
    var injectClass = 'inject-';
    var colorIndicator = '';

    //loop through each property of data object
    for( var prop in data ){
        //only loop through this objects props, don't need any of the inherited props
        if( data.hasOwnProperty(prop) ){
            colorIndicator = PlexSales.colorIndicator(data[prop]);
            // console.log(prop);
            // console.log(data[prop]);
            $('td.' + injectClass + prop).addClass(colorIndicator).html(data[prop]);
        }
    } 

    //open modal
    PlexSales.openEngagementModal();
}

PlexSales.removeIndicatorClasses = function(){
    var className = '';
    var allTds = $('#engagementDataModal table td[class^="inject-"]');
    var indicatorClass = '';
    var self;

    //loop through each property of data object
    allTds.each(function(){
        self = $(this);
        indicatorClass = self.attr('class').split(' ').pop();
        self.removeClass(indicatorClass); 
        self.empty();
    }); 

    //remove data from comparison view
    $('#comparisonView table tbody').empty();
    $('select#leftSelectDateRange').val("0");

    if(!$('#comparisonView #rightSelect').hasClass('hide')){
        $('#comparisonView #rightSelect').addClass('hide');
    }
    //close modal
    PlexSales.closeEngagementModal();
}

PlexSales.colorIndicator = function(val){
    switch(val){
        case 'A': return 'Good';
        case 'B': return 'Average';
        case 'C': return 'BelowAvr';
        case 'D': return 'BelowAvr';
        case 'F': return 'BelowAvr';
        default :
            return 'NoResult';
    }

    return 'average';
}

PlexSales.openEngagementModal = function(){
    $('#engagementDataModal').foundation('reveal', 'open');
}

PlexSales.closeEngagementModal = function(){
    $('#engagementDataModal').foundation('reveal', 'close');
}

PlexSales.openGoalDataModal = function(){
    $('#goalDataModal').foundation('reveal', 'open');
}

PlexSales.closeGoalDataModal = function(){
    $('#goalDataModal').foundation('reveal', 'close');
}

$(document).on('click', '#default', function(){
    //self select
    if(!$(this).hasClass('select')) {
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }   
        $(this).addClass('select');
    }
    //unselect the comparison view
    if(!$('#comparison').hasClass('unselect')){
        if($('#comparison').hasClass('select')){
            $('#comparison').removeClass('select');
        }
        $('#comparison').addClass('unselect');
    }
    // show self
    $('#defaultView').fadeIn(500);
    if($('#defaultView').hasClass('hide')){
        $('#defaultView').removeClass('hide');
    }
    // hide comparisonView
    if(!$('#comparisonView').hasClass('hide')){
        $('#comparisonView').addClass('hide');
    }
});

$(document).on('click', '#comparison', function(){
    //self select
    if(!$(this).hasClass('select')) {
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }   
        $(this).addClass('select');
    }
    //unselect the comparison view
    if(!$('#default').hasClass('unselect')){
        if($('#default').hasClass('select')){
            $('#default').removeClass('select');
        }
        $('#default').addClass('unselect');
    }
    // show self
    $('#comparisonView').fadeIn(500);
    if($('#comparisonView').hasClass('hide')){
        $('#comparisonView').removeClass('hide');
    }
    // hide comparisonView
    if(!$('#defaultView').hasClass('hide')){
        $('#defaultView').addClass('hide');
    }
});

$(document).on('click', '#leftSelectDateRange', function(){
    //console.log($("#leftSelectDateRange option:selected").text());
    if($("#leftSelectDateRange option:selected").text() == 'Customed DateRange'){
        $('#rightSelect').removeClass('hide');
    }else{
        $('#rightSelect').addClass('hide');
    }
});


$(document).on('change', '#leftSelectDateRange', function(){

    var optionSet1 = {
        startDate: moment(),    
        endDate: moment(),
        minDate: '01/01/2010',
        maxDate: '12/31/2035',
        dateLimit: { days: 360 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
           '1 Day Before' : [moment().subtract('days', 2), moment().subtract('days', 2)],
           'Last 7 Days': [moment().subtract('days', 6), moment()],
           '7 Days Before' : [moment().subtract('days', 13), moment().subtract('days', 7)], 
           'Last 14 Days' : [moment().subtract('days', 13), moment()],
           '14 Days Before' : [moment().subtract('days', 27), moment().subtract('days', 14)],
           'Last 30 Days': [moment().subtract('days', 27), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
        },
        opens: 'right',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        autoUpdateInput: false,
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    };

    var _this = $(this), prepData, val;
    val = parseInt(_this.val());

    if( val > 0 && val < 5 ){
        //make ajax call with just value
        var left_start_date, left_end_date, right_start_date, right_end_date;
        prepData = PlexSales.prepComparisonData({option: val});
        if(val == 1) {
            left_start_date = moment().subtract('days', 1).format('YYYY-MM-DD');
            left_end_date = moment().subtract('days', 1).format('YYYY-MM-DD');
            right_start_date = moment().subtract('days', 2).format('YYYY-MM-DD');
            right_end_date = moment().subtract('days', 2).format('YYYY-MM-DD');

            prepData['l_start_date'] = left_start_date;
            prepData['l_end_date'] = left_end_date;
            prepData['r_start_date'] = right_start_date;
            prepData['r_end_date'] = right_end_date;
        }else if (val == 2){
            left_start_date = moment().subtract('days', 6).format('YYYY-MM-DD');
            left_end_date = moment().format('YYYY-MM-DD');
            right_start_date = moment().subtract('days', 13).format('YYYY-MM-DD');
            right_end_date = moment().subtract('days', 7).format('YYYY-MM-DD');

            prepData['l_start_date'] = left_start_date;
            prepData['l_end_date'] = left_end_date;
            prepData['r_start_date'] = right_start_date;
            prepData['r_end_date'] = right_end_date;
        }else if (val == 3){
            left_start_date = moment().subtract('days', 13).format('YYYY-MM-DD');
            left_end_date = moment().format('YYYY-MM-DD');
            right_start_date = moment().subtract('days', 27).format('YYYY-MM-DD');
            right_end_date = moment().subtract('days', 14).format('YYYY-MM-DD');

            prepData['l_start_date'] = left_start_date;
            prepData['l_end_date'] = left_end_date;
            prepData['r_start_date'] = right_start_date;
            prepData['r_end_date'] = right_end_date;
        }else if (val == 4){
            left_start_date = moment().startOf('month').format('YYYY-MM-DD');
            left_end_date = moment().endOf('month').format('YYYY-MM-DD');
            right_start_date = moment().subtract('month', 1).startOf('month').format('YYYY-MM-DD');
            right_end_date = moment().subtract('month', 1).endOf('month').format('YYYY-MM-DD');

            prepData['l_start_date'] = left_start_date;
            prepData['l_end_date'] = left_end_date;
            prepData['r_start_date'] = right_start_date;
            prepData['r_end_date'] = right_end_date;
        }
        PlexSales.setComparison(prepData);
    }else{
        //show text field
        //then make on another event handler the check when date range is changed and then send 
        //ajax call with date ranges
        // console.log('I am here');
        $('#rightSelectDateOpt1').val("");
        $('#rightSelectDateOpt2').val("");

        var prepData = {};
        prepData['option'] = val;
        prepData['school_id'] =  parseInt( $('#leftSelectDateRange').data('school-id') );
        prepData['user_id'] =  parseInt( $('#leftSelectDateRange').data('user-id') );
        var left_start_date, left_end_date, right_start_date, right_end_date;
        $("#rightSelectDateOpt1").daterangepicker( optionSet1, function(start, end, label) {});
        $('#rightSelectDateOpt1').on('apply.daterangepicker', function(ev, picker) {
            left_start_date = picker.startDate.format('YYYY-MM-DD');
            left_end_date = picker.endDate.format('YYYY-MM-DD');
            prepData['l_start_date'] = left_start_date;
            prepData['l_end_date'] = left_end_date;
            //if left date is not choose, show err
            if(!prepData['l_start_date'] || !prepData['l_end_date']){
                $('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().removeClass('hide');
            }else{ //if left date is choosen. hide err
                if(!$('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().hasClass('hide'))
                    $('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().addClass('hide');
            }
            // if left date is choosen, check right side
            if(prepData['l_start_date'] && prepData['l_end_date']){
                if(!prepData['r_start_date'] || !prepData['r_end_date']){
                    $('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().removeClass('hide');
                }
                if(prepData['r_start_date'] && prepData['r_end_date']){
                    PlexSales.setComparison(prepData);
                }
            }
        });

        $('#rightSelectDateOpt1').on('cancel.daterangepicker', function(ev, picker) {
            $('#rightSelectDateOpt1').val('');
            if($('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().hasClass('hide'))
                $('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().removeClass('hide');
            if(prepData['l_start_date'] || prepData['l_end_date']){
                prepData['l_start_date'] = null;
                prepData['l_end_date'] = null;
            }
        });

        $('#rightSelectDateOpt2').daterangepicker( optionSet1, function(start, end, label) {});
        $('#rightSelectDateOpt2').on('apply.daterangepicker', function(ev, picker) {
            right_start_date = picker.startDate.format('YYYY-MM-DD');
            right_end_date = picker.endDate.format('YYYY-MM-DD');  
            prepData['r_start_date'] = right_start_date;
            prepData['r_end_date'] = right_end_date;
            if(!prepData['r_start_date'] || !prepData['r_end_date']){
                $('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().removeClass('hide');
            }else{
                if(!$('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().hasClass('hide'))
                    $('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().addClass('hide');
            }
            if(prepData['r_start_date'] && prepData['r_end_date']){
                if(!prepData['l_start_date'] || !prepData['l_end_date']){
                    $('#rightSelectDateOpt1').parent('div').siblings('.errorMsg').first().removeClass('hide');
                }
                if(prepData['l_start_date'] && prepData['l_end_date']){
                    PlexSales.setComparison(prepData);
                }
            }
        });

        $('#rightSelectDateOpt2').on('cancel.daterangepicker', function(ev, picker) {
            $('#rightSelectDateOpt2').val('');
            if($('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().hasClass('hide'))
                $('#rightSelectDateOpt2').parent('div').siblings('.errorMsg').first().removeClass('hide');
            if(prepData['r_start_date'] || prepData['r_end_date']){
                prepData['r_start_date'] = null;
                prepData['r_end_date'] = null;
            }
        });
    }
});

PlexSales.prepComparisonData = function(data){
    var retObj = {
        school_id: parseInt( $('#leftSelectDateRange').data('school-id') ),
        user_id: parseInt( $('#leftSelectDateRange').data('user-id') ),
        option: 0,
        l_start_date: null,
        l_end_date: null,
        r_start_date: null,
        r_end_date: null
    };

    for( var prop in data ){
        if( data.hasOwnProperty(prop) ){
            retObj[prop] = data[prop];
        }
    }

    return retObj;
}

PlexSales.setComparison = function(postData){

    $.ajax({
        url: '/sales/setComparison',
        type: 'POST',
        data: postData,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        PlexSales.injectComparisonData(data);
    });
}

PlexSales.injectComparisonData = function(data){

    var html = "";
    var topics = ['# Student Profile View','# Inquiries', 'Inquiry Activity', 'Recommendation Activity', 
                  '# Handshakes', '# Adv. Search Recruited', '# Days Chatted','Chat Activity', 'Offline Message Activity', 'Filter Active', 'Last Logged In','Overall'];
    var ScoreSlug = ['num_profile_view','num_of_inquiries', 'inquiry_activity', 'recommendation_activity',
                  'num_of_total_approved', 'num_of_advance_search_approved', 'num_of_days_chatted','total_chat_sent', 'total_msg_sent', 'filter_active', 'last_logged_in', 'total'];
    var slugTooltipTitle = ['The last time this college representative logged in. A day is defined as 24 hours.', 
                            'Total number of times this college has viewed students\' profiles.', 
                            'Number of (student-initiated) inquiries this college has received.',
                            'The percentage of inquiries this college has responded to. [(Inquiries Accepted + Inquiries Rejected) / Total Inquiries]. Defaults to 0 if Total Inquiries is 0.',
                            'Number of recommendations this college has manually approved or rejected.',
                            'The number of handshakes/approved students this college has had.',
                            'Number of students this college chose to recruit through advanced search.',
                            'Number of chat messages this college representative has sent.',
                            'The number of offline messages this college representative has sent',
                            'The Overall Score/Grade using the criteria displayed above.']; 
    var gradeColorMapping = {'1' : 'higherGrade', '0' : 'equalGrade', '-1' : 'lowerGrade', 'N/A' : 'noGrade'};

    for (var i = 0; i <= topics.length - 1; i++) {
        //console.log(ScoreSlug[i]);
        html += "<tr>";   
        html += "<td><span data-tooltip aria-haspopup='true' class='has-tip tip-bottom row-tip' title='" + slugTooltipTitle[i] + "'>" + topics[i] + "</span></td>";
        var l_score = data['comparison_data']['left'][ScoreSlug[i]]['raw_value'];
        var l_grade = data['comparison_data']['left'][ScoreSlug[i]]['grade'];
        var r_score = data['comparison_data']['right'][ScoreSlug[i]]['raw_value'];
        var r_grade = data['comparison_data']['right'][ScoreSlug[i]]['grade'];

        if(l_grade > r_grade || parseFloat(l_score) < parseFloat(r_score)){
            l_compareGrade = gradeColorMapping['-1'];
            r_compareGrade = gradeColorMapping['1']; 
        }else if (l_grade == r_grade && l_score == r_score){
            l_compareGrade = gradeColorMapping['0'];
            r_compareGrade = gradeColorMapping['0'];
        }else if(l_grade < r_grade || parseFloat(l_score) > parseFloat(r_score)){
            l_compareGrade = gradeColorMapping['1'];
            r_compareGrade = gradeColorMapping['-1'];
        }

        html += "<td class='left-" + ScoreSlug[i] + "-score " + l_compareGrade + "'> " + l_score + " </td>" ;
        html += "<td class='left-" + ScoreSlug[i] + "-grade " + l_compareGrade + "'> " + l_grade + " </td>" ;
        html += "<td class='right-" + ScoreSlug[i] + "-score " + r_compareGrade+ "'> " + r_score + " </td>" ;
        html += "<td class='right-" + ScoreSlug[i] + "-grade " + r_compareGrade+ "'> " + r_grade + " </td>" ;
        html += "</tr>";
    };
    // console.log(html);
    $('#comparisonView table tbody').html(html);
}

$(document).on('click', '.save-trigger, .set-trigger-btn', function(){
    var school_id = $(this).closest('#triggerModal').data('school-id');
    var trigger_data = PlexSales.getTriggerData(school_id);

    if( $(this).hasClass('set-trigger-btn') ){
        if( trigger_data.emergency_trigger ){
            if( trigger_data.emergency_percentage > 0 & trigger_data.emergency_percentage <= 100 ){
                $('.err').slideUp(250); 
                PlexSales.saveTrigger(trigger_data);
            }else{
                $('.err').slideDown(250); 
            }
        }else{
            $('.err').slideUp(250); 
            PlexSales.saveTrigger(trigger_data);
        }
    }else{
        PlexSales.saveTrigger(trigger_data);
    }
});

// ----- trigger data modal
$(document).on('click', '.trigger-link', function() {
    var data = $(this).data('triggers');
    var school_id = $(this).data('school-id');

    PlexSales.buildTriggerModal(data, school_id);    
    $('#triggerModal').foundation('reveal', 'open');
});

PlexSales.buildTriggerModal = function(data, school_id){
    var modal = $('#triggerModal'), radioBtn, emails, item = '';

    radioBtn = data['triggers-frequency'] == 'weekly' ? modal.find('input.weekly') : modal.find('input.daily');
    radioBtn.prop('checked', true);
    modal.data('school-id', school_id);
    //console.log(data);
    if( data['triggers-emails'].length > 0 ){
        if( data['triggers-emails'].indexOf(',') === -1 ){
            emails = [data['triggers-emails']];
        }else{
            emails = data['triggers-emails'].split(',');
        }

        $.each(emails, function(index, val){
            item += '<div id="' + val.split('@')[0] + '"><a class="removeEmail" aria-label="Close">&#215;</a><small>' + val + '</small><br /></div>';
        });
    }
    
    $('#emailList').html(item);
    $('.threshold').val( data['triggers-emergency-perc'] );
}

PlexSales.getTriggerData = function(school){
    var emailList = $('#emailList div small').map(function(index, elem) {
        return elem['textContent'];  
    }).get();

    return {
        school_id: school,
        frequency: $('input[name="trigger"]:checked').val(),
        emergency_percentage: $('.threshold').val(),
        emergency_trigger: !!parseInt($('.threshold').val()) ? 1 : 0,
        emails: emailList
    };
}

PlexSales.saveTrigger = function(trigger_data){
    $.ajax({
        url: '/sales/setTrigger',
        type: 'POST',
        data: trigger_data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        $('#triggerModal').foundation('reveal', 'close');
    });

}

$(document).on('click', '#addEmail', function(){
    var validEmail = $('#validEmail').val();
    var validEmailId = validEmail.split('@')[0];
    // validate email
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(filter.test(validEmail)) {
        if(!$('.email-notify .errorMsg').hasClass('hide')){
            $('.email-notify .errorMsg').addClass('hide');
        }
        if(validEmail != null) {
            $('#emailList').prepend('<div id="' + validEmailId + '"><a class="removeEmail" aria-label="Close">&#215;</a><small>' + validEmail + '</small><br /></div>');
            $('#validEmail').val("");   
        }
    }else {
        //give a warning to notice pls input a valid email
        $('.email-notify .errorMsg').removeClass('hide');
    }
});

$(document).on('click', '.removeEmail', function(){
    var emailTobeRemoved = $(this).siblings('small').first().html().split('@')[0];
    $('#emailList').find('div#'+emailTobeRemoved).remove();
});

$(document).on('click', '#pastTwoWeeks', function(){
    //self select
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    } 

    //unselect the other views
    $('#pastMonth, #overAll').each(function(index, el) {
        if(!$(this).hasClass('unselect')){
            if($(this).hasClass('select')){
                $(this).removeClass('select');
            }
            $(this).addClass('unselect');
        }
    });

    //show self indicated table
    $('#pastTwoWeeksTable').fadeIn(500);
    if($('#pastTwoWeeksTable').hasClass('hide')){
        $('#pastTwoWeeksTable').removeClass('hide');
    }

    $('#pastMonthTable, #overAllTable').each(function(index, el) {
        if(!$(this).hasClass('hide')){
            $(this).addClass('hide');
        }
    });
});

$(document).on('click', '#pastMonth', function(){
    //self select
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    } 

    //unselect the other views
    $('#pastTwoWeeks, #overAll').each(function(index, el) {
        if(!$(this).hasClass('unselect')){
            if($(this).hasClass('select')){
                $(this).removeClass('select');
            }
            $(this).addClass('unselect');
        }
    });

    //show self indicated table
    $('#pastMonthTable').fadeIn(500);
    if($('#pastMonthTable').hasClass('hide')){
        $('#pastMonthTable').removeClass('hide');
    }

    $('#pastTwoWeeksTable, #overAllTable').each(function(index, el) {
        if(!$(this).hasClass('hide')){
            $(this).addClass('hide');
        }
    });
});

$(document).on('click', '#overAll', function(){
    //self select
    if(!$(this).hasClass('select')){
        if($(this).hasClass('unselect')){
            $(this).removeClass('unselect');
        }
        $(this).addClass('select');
    } 

    //unselect the other views
    $('#pastTwoWeeks, #pastMonth').each(function(index, el) {
        if(!$(this).hasClass('unselect')){
            if($(this).hasClass('select')){
                $(this).removeClass('select');
            }
            $(this).addClass('unselect');
        }
    });

    //show self indicated table
    $('#overAllTable').fadeIn(500);
    if($('#overAllTable').hasClass('hide')){
        $('#overAllTable').removeClass('hide');
    }

    $('#pastTwoWeeksTable, #pastMonthTable').each(function(index, el) {
        if(!$(this).hasClass('hide')){
            $(this).addClass('hide');
        }
    });
});

