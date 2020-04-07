/* js/jquery file for Plexuss Publisher */
Publisher = {
	menu_is_open: false,
};

$(document).ready(function(){

});

$(document).on('focus',".event_date", function(){
		var date = new Date();
		var yesterday = new Date(Date.now() - 86400000);

		$(this).daterangepicker({
			timePicker: true,
			timePickerIncrement: 1,
			timePicker12Hour: true,
			autoApply: true,
			minDate: yesterday,
			maxDate: '12/31/2035',
			dateLimit: { days: 360 },
			showDropdowns: true,
			showWeekNumbers: false,
			minYear: 1901,
			maxYear: parseInt(moment().format('YYYY'),10),
			opens: 'right',
			buttonClasses: ['btn btn-default'],
			applyClass: 'btn-small btn-primary',
			cancelClass: 'btn-small',
			format: 'MM/DD/YYYY hh:mm A',
			separator: ' to ',
			autoUpdateInput: false,
			locale: {
				applyLabel: 'Apply',
				cancelLabel: 'Clear',
				fromLabel: 'From',
				toLabel: 'To',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			}
		}, function(start, end, label) {
			console.log(start.toISOString(), end.toISOString(), label);
		});

		$('.event_date').on('apply.daterangepicker', function(ev, picker) {
           $(this).trigger('focus');
		});
	});

//dynamic textarea height on window resize
// $(window).on('resize', function(){
//     var _this = $('.article-form-container textarea');
//     var this_container = $('.article-form-container');
//     _this.height( this_container.height() - 160 );
// });

//publisher menu btn click event to open nav items
$(document).on('click', '.publisher-menu-btn', function(){
	Publisher.toggleMenu($(this));
});

Publisher.toggleMenu = function(btn){

	//if menu is already open, close it
	if( Publisher.menu_is_open ){
		$(btn).animate({
			bottom: '0.5%',
			left: '0.2%',
			width: '72px',
			borderRadius: '100px',
		}).removeClass('expanded').find('span').html('Menu');
		$('.publisher-menu-btn-bg').fadeOut(250);
		$('.publisher-menu-ul').slideUp(250);
		//wasnt open before, so now menu is open
		Publisher.menu_is_open = false;
	}else{
		
		$(btn).animate({
			bottom: '40%',
			left: '38%',
			width: '350px',
			borderRadius: '3px',
		}).addClass('expanded').find('span').html('Close');
		$('.publisher-menu-btn-bg').fadeIn(250);
		$('.publisher-menu-ul').slideDown(250);
		//now closed, so menu open is now false
		Publisher.menu_is_open = true;
	}

}

//when textarea has focus, every ten seconds, save notes
$('textarea.sales-messages-notes').focus(function(){
    // var these_notes = this;
    // var student_id = $(this).data('studentid');

    //loop - every 10 seconds, while textarea is in focus, save the note
    // Plex.inquiries.saveNotesInterval = setInterval(function(){
    //     Plex.inquiries.autoSaveSalesNotes( student_id, these_notes );
    // }, 10000);
});


//when user clicks out of focused textarea, save notes
$('textarea.sales-messages-notes').blur(function(){
    // var _this = this;
    // var this_student_id = $(this).data('studentid');

    // clearInterval(Plex.inquiries.saveNotesInterval);
    // Plex.inquiries.autoSaveSalesNotes( this_student_id, this );
});

//auto save Sales notes
Publisher.autoSaveArticle = function( id, notes ){
    // var note = $(notes).val();
    // var lastSaved = $(notes).parent().find('.last-saved-note-time');

    // $('.save-note-ajax-loader').show();

    //post note and update 'last saved' time
    // $.ajax({
    //     url: '/admin/inquiries/setNote',
    //     type: 'POST',
    //     data: {user_id: id, note: note_data}
    // })
    // .done(function(time){
    //     $('.save-note-ajax-loader').hide();
    //     $(last_saved).text('Last Saved: ' + time);
    // });
}
